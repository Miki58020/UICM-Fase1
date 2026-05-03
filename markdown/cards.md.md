> SERVER_SIDE
>
> h1
>
> Tarjetas

Con toda la información recopilada en el _backend_ , envía un **POST** con los atributos requeridos al endpoint [/v1/payments](/developers/es/reference/online-payments/checkout-api-payments/create-payment/post) y ejecuta la solicitud o, si lo prefieres, envía la información utilizando nuestros SDKs.

> NOTE
>
> Importante
> 
> Además, deberás enviar obligatoriamente el atributo `X-Idempotency-Key` para asegurar la ejecución y reejecución de las solicitudes sin el riesgo de realizar la misma acción más de una vez por error. Para hacerlo, actualiza [nuestra biblioteca de SDKs](/developers/es/docs/sdks-library/landing), o bien genera un UUID V4 y envíalo en los _header_ de tus llamados.

[[[
```php
<?php
  use MercadoPago\Client\Payment\PaymentClient;
  use MercadoPago\Client\Common\RequestOptions;
  use MercadoPago\MercadoPagoConfig;

  MercadoPagoConfig::setAccessToken("YOUR_ACCESS_TOKEN");

  $client = new PaymentClient();
  $request_options = new RequestOptions();
  $request_options->setCustomHeaders(["X-Idempotency-Key: <SOME_UNIQUE_VALUE>"]);

  $payment = $client->create([
  "transaction_amount" => (float) $_POST['<TRANSACTION_AMOUNT>'],
  "token" => $_POST['<TOKEN>'],
  "description" => $_POST['<DESCRIPTION>'],
  "installments" => $_POST['<INSTALLMENTS>'],
  "payment_method_id" => $_POST['<PAYMENT_METHOD_ID'],
  "issuer_id" => $_POST['<ISSUER>'],
  "payer" => [
  "email" => $_POST['<EMAIL>'],
  "identification" => [
  "type" => $_POST['<IDENTIFICATION_TYPE'],
  "number" => $_POST['<NUMBER>']
  ]
  ]
  ], $request_options);
  echo implode($payment);
?>
```
```node
const mercadopago = require('mercadopago');
import { MercadoPagoConfig, Payment } from '@src/index';

const client = new MercadoPagoConfig({ accessToken: '<ACCESS_TOKEN>', options: { timeout: 5000 } });

const payment = new Payment(client);

payment
  .create({
  body: {
  transaction_amount: 100,
  token: '<TOKEN>',
  description: '<DESCRIPTION>',
  installments: 1,
  payment_method_id: '<PAYMENT_METHOD_ID>',
  issuer_id: 310,
  payer: {
  email: '<EMAIL>',
  identification: {
  number: '12345678909',
  type: 'CPF',
  },
  },
  },
  }).then(console.log).catch(console.log);
```

```java
Map<String, String> customHeaders = new HashMap<>();
  customHeaders.put("x-idempotency-key", <SOME_UNIQUE_VALUE>);
 
MPRequestOptions requestOptions = MPRequestOptions.builder()
  .customHeaders(customHeaders)
  .build();

MercadoPagoConfig.setAccessToken("YOUR_ACCESS_TOKEN");

PaymentClient client = new PaymentClient();

PaymentCreateRequest paymentCreateRequest =
  PaymentCreateRequest.builder()
  .transactionAmount(request.getTransactionAmount())
  .token(request.getToken())
  .description(request.getDescription())
  .installments(request.getInstallments())
  .paymentMethodId(request.getPaymentMethodId())
  .payer(
  PaymentPayerRequest.builder()
  .email(request.getPayer().getEmail())
  .firstName(request.getPayer().getFirstName())
  .identification(
  IdentificationRequest.builder()
  .number(request.getPayer().getIdentification().getNumber())
  .build())
  .build())
  .build();

client.create(paymentCreateRequest, requestOptions);
```

```ruby
require 'mercadopago'
sdk = Mercadopago::SDK.new('YOUR_ACCESS_TOKEN')

custom_headers = {
 'x-idempotency-key': '<SOME_UNIQUE_VALUE>'
}

custom_request_options = Mercadopago::RequestOptions.new(custom_headers: custom_headers)

payment_data = {
  transaction_amount: params[:transactionAmount].to_f,
  token: params[:token],
  description: params[:description],
  installments: params[:installments].to_i,
  payment_method_id: params[:paymentMethodId],
  payer: {
  email: params[:cardholderEmail],
  identification: {
  number: params[:identificationNumber]
  },
  first_name: params[:cardholderName]
  }
}

payment_response = sdk.payment.create(payment_data, custom_request_options)
payment = payment_response[:response]

puts payment
```
```csharp
using System;
using MercadoPago.Client.Common;
using MercadoPago.Client.Payment;
using MercadoPago.Config;
using MercadoPago.Resource.Payment;

MercadoPagoConfig.AccessToken = "YOUR_ACCESS_TOKEN";

var requestOptions = new RequestOptions();
requestOptions.CustomHeaders.Add("x-idempotency-key", "<SOME_UNIQUE_VALUE>");

var paymentRequest = new PaymentCreateRequest
{
  TransactionAmount = decimal.Parse(Request["transactionAmount"]),
  Token = Request["token"],
  Description = Request["description"],
  Installments = int.Parse(Request["installments"]),
  PaymentMethodId = Request["paymentMethodId"],
  Payer = new PaymentPayerRequest
  {
  Email = Request["cardholderEmail"],
  Identification = new IdentificationRequest
  {
  Number = Request["identificationNumber"],
  },
  FirstName = Request["cardholderName"]
  },
};

var client = new PaymentClient();
Payment payment = await client.CreateAsync(paymentRequest, requestOptions);

Console.WriteLine(payment.Status);
```
```python
import mercadopago
sdk = mercadopago.SDK("ACCESS_TOKEN")

request_options = mercadopago.config.RequestOptions()
request_options.custom_headers = {
  'x-idempotency-key': '<SOME_UNIQUE_VALUE>'
}

payment_data = {
  "transaction_amount": float(request.POST.get("transaction_amount")),
  "token": request.POST.get("token"),
  "description": request.POST.get("description"),
  "installments": int(request.POST.get("installments")),
  "payment_method_id": request.POST.get("payment_method_id"),
  "payer": {
  "email": request.POST.get("cardholderEmail"),
  "identification": {
  "number": request.POST.get("identificationNumber")
  }
  "first_name": request.POST.get("cardholderName")
  }
}

payment_response = sdk.payment().create(payment_data, request_options)
payment = payment_response["response"]

print(payment)
```

```curl
curl -X POST \
  -H 'accept: application/json' \
  -H 'content-type: application/json' \
  -H 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  -H 'X-Idempotency-Key: SOME_UNIQUE_VALUE' \
  'https://api.mercadopago.com/v1/payments' \
  -d '{
  "transaction_amount": 100,
  "token": "ff8080814c11e237014c1ff593b57b4d",
  "description": "Blue shirt",
  "installments": 1,
  "payment_method_id": "visa",
  "issuer_id": 310,
  "payer": {
  "email": "PAYER_EMAIL_HERE",
  "identification": {
  "number": 19119119100
  }
  }
  }'

```

]]]

## Respuesta

```json
{
  "status": "approved",
  "status_detail": "accredited",
  "id": 3055677,
  "date_approved": "2019-02-23T00:01:10.000-04:00",
  "payer": {
  ...
  },
  "payment_method_id": "visa",
  "payment_type_id": "credit_card",
  "refunds": [],
  ...
}
```

El _callback_ _onSubmit_ de Brick contiene todos los datos necesarios para crear un pago; sin embargo, si lo deseas, puedes incluir detalles adicionales que pueden facilitar el reconocimiento de la compra por parte del comprador y aumentar la tasa de aprobación del pago.

Para hacer esto, agrega campos relevantes para el objeto enviado, que viene en la respuesta del _callback_ _onSubmit_ de Brick. Algunos de estos campos son: `description` (este campo se puede mostrar en los tickets emitidos) y `external_reference` (ID de compra en tu sitio web, lo que permite un reconocimiento de compra más fácil). También es posible añadir datos adicionales sobre el comprador.

> NOTE
>
> Importante
>
> Recomendamos adherirse al protocolo 3DS 2.0 para aumentar la probabilidad de aprobación de sus pagos, lo cual se puede hacer como se describe [aquí.](/developers/es/docs/checkout-bricks/how-tos/integrate-3ds)

Conoce todos los campos disponibles para realizar un pago completo en las [Referencias de API](/developers/es/reference/online-payments/checkout-api-payments/create-payment/post).

## Prueba tu integración

Con la integración completada, podrás probar la recepción de pagos. Para obtener más información, accede a la sección [Hacer compra de prueba](/developers/es/docs/checkout-bricks/integration-test/test-payment-flow).