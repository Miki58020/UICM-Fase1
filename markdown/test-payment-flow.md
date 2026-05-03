# Hacer compra de prueba

La etapa de pruebas permite verificar si la integración es correcta y si los pagos se están procesando sin errores antes de disponibilizarlo para los compradores.

> WARNING
>
> Atención
>
> No use el correo electrónico de usuario de prueba en el campo de correo electrónico del Brick (si corresponde). Además, cada medio de pago requiere credenciales específicas para la prueba, descritas en cada sección a continuación.

## Tarjetas

Para probar este medio de pago, utiliza las **credenciales de prueba** de tu cuenta real. Para obtenerlas, accede a [Tus integraciones](/developers/panel/app) y ve a **Detalles de la aplicación > Credenciales** o en tu cuenta de Mercado Pago, accediendo a [Tu negocio > Configuraciones > Gestión y administración > Credenciales](https://www.mercadopago[FAKER][URL][DOMAIN]/settings/account/credentials).

1. Inicia la integración de tu proyecto con las **credenciales de prueba**.
2. Ingresa cualquier correo electrónico como usuario pagador (**recuerda que debe ser diferente al correo electrónico que utilizas en Mercado Pago**).
3. Ingresa los datos de una de nuestras [tarjetas de prueba](/developers/es/guides/additional-content/your-integrations/test-cards).
4. Prueba diferentes resultados de pago utilizando la tabla disponible en las [tarjetas de prueba](/developers/es/guides/additional-content/your-integrations/test-cards) y completando el estatus deseado en el nombre del titular de la tarjeta (campo `card_holder_name`).
5. Confirma la compra. Se generará un pago con el **status indicado para prueba**.

## Medios de pago offline

Al igual que en las pruebas con Tarjetas, utiliza las **credenciales de prueba** de tu cuenta real. Para obtenerlas, accede a [Tus integraciones](/developers/panel/app) y ve a **Detalles de la aplicación > Credenciales** o en tu cuenta de Mercado Pago, accediendo a [Tu negocio > Configuraciones > Gestión y administración > Credenciales](https://www.mercadopago[FAKER][URL][DOMAIN]/settings/account/credentials).

1. Inicia la integración de tu proyecto con las **credenciales de prueba**.
2. Ingresa cualquier correo electrónico como usuario pagador (**recuerda que debe ser diferente al correo electrónico que utilizas en Mercado Pago**).
3. Ingresa los datos requeridos en el formulario.
4. Confirma la compra. Se generará un pago con **status pendiente**.

## Pago con redirección a Mercado Pago

Para probar este medio de pago, necesitarás dos [cuentas de prueba](/developers/es/docs/checkout-bricks/additional-content/your-integrations/test/accounts) creadas desde [Tus integraciones](/developers/panel/app): una **vendedora** y una **compradora**. Utiliza las **credenciales de producción** de la cuenta vendedora para inicializar el brick.

1. Crea las dos [cuentas de prueba](/developers/es/docs/checkout-bricks/additional-content/your-integrations/test/accounts) en [Tus integraciones](/developers/panel/app)
2. En la cuenta vendedora, crea una aplicación y utiliza sus **credenciales de producción** para [crear una preferencia](/developers/es/reference/online-payments/checkout-pro/preferences/create-preference/post) e inicializar el brick.
3. Dirígete a Mercado Pago (a través de [Payment Brick](/developers/es/docs/checkout-bricks/payment-brick/payment-submission/wallet-credits) o [Wallet Brick](/developers/es/docs/checkout-bricks/wallet-brick/default-rendering)).
4. Ingresa a Mercado Pago con la **cuenta de prueba compradora**.
5. Confirma la compra.

> NOTE
>
> Nota
>
> Si posteriormente quieres probar pagos con tarjeta, será necesario cambiar las credenciales de producción de la cuenta de prueba vendedora por las **credenciales de prueba de tu cuenta real**.

Una vez que se completen estos pasos, la integración estará completa y podrás usar tus credenciales de producción para usar el Checkout Bricks.