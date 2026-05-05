# Comandos del proyecto UICM

## Servidor local
```bash
php artisan serve          # Levanta el servidor en localhost:8000
npm run dev                # Compila assets en modo desarrollo (Vite)
npm run build              # Compila assets para producción o ngrok
```

## Base de datos
```bash
php artisan migrate        # Ejecuta las migraciones pendientes
php artisan migrate:fresh  # Borra todo y vuelve a migrar (cuidado en producción)
php artisan db:seed        # Ejecuta los seeders
```

## Cache y configuración
```bash
php artisan config:clear   # Limpia el cache de configuración (.env)
php artisan cache:clear    # Limpia el cache general de la app
php artisan route:clear    # Limpia el cache de rutas
php artisan view:clear     # Limpia el cache de vistas compiladas
php artisan optimize:clear # Limpia todo de una vez
```

## Scheduler (tareas automáticas)
```bash
php artisan schedule:work              # Activa el scheduler en local (dejar corriendo)
php artisan periodos:sincronizar       # Abre/cierra periodos según sus fechas
php artisan uicm:resumen-diario        # Envía correo de resumen a todos los usuarios del sistema
```

## ngrok
```bash
ngrok http 8000            # Expone el servidor local con URL pública https://
```
> Después de cambiar `APP_URL` en `.env` correr `php artisan config:clear`
