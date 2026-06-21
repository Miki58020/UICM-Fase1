<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Duración del bloqueo (segundos) según cuántas veces se ha bloqueado antes
     * este email/IP. Cada bloqueo nuevo escala al siguiente valor de la lista.
     */
    private const DECAY_PROGRESIVO = [60, 300, 900, 1800, 3600]; // 1min, 5min, 15min, 30min, 1h

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->hitProgresivo();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Cache::forget($this->nivelBloqueoKey());
    }

    /**
     * Registra el intento fallido. Si es el primer intento de una ventana nueva,
     * el tiempo de bloqueo se calcula según cuántas veces este email/IP ya fue
     * bloqueado antes (bloqueos progresivos: 1min, 5min, 15min, 30min, 1h).
     */
    private function hitProgresivo(): void
    {
        $key = $this->throttleKey();
        $esVentanaNueva = RateLimiter::attempts($key) === 0;

        if ($esVentanaNueva) {
            $nivel = Cache::get($this->nivelBloqueoKey(), 0);
            $decay = self::DECAY_PROGRESIVO[min($nivel, count(self::DECAY_PROGRESIVO) - 1)];
            RateLimiter::hit($key, $decay);
        } else {
            RateLimiter::hit($key);
        }

        if (RateLimiter::attempts($key) === 5) {
            $nivel = Cache::get($this->nivelBloqueoKey(), 0);
            Cache::put($this->nivelBloqueoKey(), $nivel + 1, now()->addDay());
        }
    }

    private function nivelBloqueoKey(): string
    {
        return 'login_nivel_bloqueo:' . $this->throttleKey();
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
