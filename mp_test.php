<?php
// Script temporal de diagnóstico: tokeniza la tarjeta de prueba APRO y crea un pago.
// Uso (PowerShell):
//   $env:MP_PUBLIC_KEY="TU_PUBLIC_KEY"; $env:MP_ACCESS_TOKEN="TU_ACCESS_TOKEN"; php mp_test.php

$publicKey   = getenv('MP_PUBLIC_KEY');
$accessToken = getenv('MP_ACCESS_TOKEN');

if (!$publicKey || !$accessToken) {
    fwrite(STDERR, "Faltan MP_PUBLIC_KEY y/o MP_ACCESS_TOKEN como variables de entorno.\n");
    exit(1);
}

function post(string $url, array $body, array $headers): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json'], $headers),
    ]);
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, json_decode($response, true)];
}

function get(string $url, array $headers): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, json_decode($response, true)];
}

// 0) Inspeccionar la cuenta dueña del Access Token
echo "== Paso 0: /users/me ==\n";
[$status, $meResp] = get(
    "https://api.mercadopago.com/users/me",
    ['Authorization: Bearer ' . $accessToken]
);
echo "HTTP {$status}\n";
echo json_encode($meResp, JSON_PRETTY_PRINT) . "\n\n";

// 1) Tokenizar tarjeta de prueba APRO (Mastercard)
echo "== Paso 1: Tokenizar tarjeta APRO ==\n";
[$status, $tokenResp] = post(
    "https://api.mercadopago.com/v1/card_tokens?public_key={$publicKey}",
    [
        'card_number'      => '5474925432670366',
        'expiration_month' => 11,
        'expiration_year'  => 2030,
        'security_code'    => '123',
        'cardholder'       => [
            'name' => 'APRO',
            'identification' => ['type' => 'CPF', 'number' => '12345678909'],
        ],
    ],
    []
);
echo "HTTP {$status}\n";
echo json_encode($tokenResp, JSON_PRETTY_PRINT) . "\n\n";

if ($status !== 201 && $status !== 200) {
    fwrite(STDERR, "Fallo la tokenizacion, no se continua.\n");
    exit(1);
}

$cardToken = $tokenResp['id'];

// 2) Crear pago con el token
echo "== Paso 2: Crear pago ==\n";
[$status, $payResp] = post(
    "https://api.mercadopago.com/v1/payments",
    [
        'transaction_amount' => 100,
        'token'              => $cardToken,
        'description'        => 'Prueba diagnostico UICM',
        'installments'       => 1,
        'payment_method_id'  => 'master',
        'payer'              => ['email' => 'test@testuser.com'],
    ],
    [
        'Authorization: Bearer ' . $accessToken,
        'X-Idempotency-Key: ' . bin2hex(random_bytes(16)),
    ]
);
echo "HTTP {$status}\n";
echo json_encode($payResp, JSON_PRETTY_PRINT) . "\n";
