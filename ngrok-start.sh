#!/bin/bash

# Inicia ngrok en segundo plano
ngrok http --request-header-add "ngrok-skip-browser-warning:true" 8000 &
NGROK_PID=$!

echo "Esperando que ngrok inicie..."
sleep 3

# Obtiene la URL pública de la API local de ngrok
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

if [ -z "$NGROK_URL" ]; then
    echo "Error: No se pudo obtener la URL de ngrok. Asegúrate de que ngrok esté instalado."
    kill $NGROK_PID 2>/dev/null
    exit 1
fi

echo "URL de ngrok: $NGROK_URL"

# Actualiza APP_URL y APP_ENV en el .env
if grep -q "^APP_URL=" .env; then
    sed -i "s|^APP_URL=.*|APP_URL=$NGROK_URL|" .env
else
    echo "APP_URL=$NGROK_URL" >> .env
fi

sed -i "s|^APP_ENV=.*|APP_ENV=staging|" .env
echo "APP_ENV cambiado a 'staging' (activa back_urls y webhook de MP)"

echo ""
echo "=================================="
echo " Comparte esta URL:"
echo " $NGROK_URL"
echo "=================================="
echo ""
echo "Presiona Ctrl+C para detener ngrok y restaurar APP_URL=http://localhost"

# Al salir, restaura el APP_URL original
trap "echo ''; echo 'Restaurando .env...'; sed -i 's|^APP_URL=.*|APP_URL=http://localhost|' .env; sed -i 's|^APP_ENV=.*|APP_ENV=local|' .env; kill $NGROK_PID 2>/dev/null; echo 'Listo.'" EXIT

# Espera a que ngrok termine
wait $NGROK_PID
