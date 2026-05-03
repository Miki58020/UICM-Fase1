> CLIENT_SIDE
>
> h1
>
> Cambiar textos

Wallet Brick está compuesto por el botón y la **propuesta de valor** (`valueProp`), que puede ser personalizada de acuerdo con las opciones disponibles en Mercado Pago. Es importante destacar que la elección del tema impacta directamente en el color de fondo del botón, la propuesta de valor y las imágenes dentro del botón. Para más información, accedé a [Cambiar de aspecto](/developers/es/docs/checkout-bricks/wallet-brick/visual-customizations/change-appearance).

![wallet-brick-actioncomplement](checkout-bricks/wallet-brick-actioncomplement-es-all-v1.png)

A continuación, revisa todos los textos posibles para el contenido de la propuesta de valor:

| Opción | Texto |
|--- | --- |
|`practicality` | "**Usa tarjetas guardadas o dinero en cuenta**" |
|`convenience_all` | "**Meses con tarjeta o Meses sin Tarjeta de Mercado Pago**" |
|`security_details` | "**Todos tus datos protegidos**" |
|`security_safety` (por defecto) | "**Paga de forma segura**" |
|`convenience_credits` | "**Hasta 12 Meses sin Tarjeta**" <br><br> Para utilizar la _value prop_ de `convenience_credits`, es necesario que el Brick se [inicialice con una preferencia](/developers/es/docs/checkout-bricks/wallet-brick/default-rendering) y que la preferencia tenga el propósito de [onboarding_credits](/developers/es/docs/checkout-bricks/wallet-brick/advanced-features/preferences). |
|`payment_methods_logos` | Se mostrarán los logotipos de los métodos de pago disponibles. Para configurar los métodos de pago, utilice la _preference_. <br><br> Se recomienda la [inicialización con una preferencia](/developers/es/docs/checkout-bricks/wallet-brick/default-rendering) en el uso de la _value prop_ `payment_methods_logos`. En caso de que la preferencia tenga solo un método de pago válido, dejará de mostrar imágenes y mostrará el texto: "**Con saldo disponible o a meses sin tarjeta**". |

> WARNING
> 
> Importante
>
> Si no especificás la propuesta de valor, por defecto se elegirá la opción `security_safety`. Además, al eliminar de la preferencia un método de pago en _ticket_ ("paycash", por ejemplo) o un _ATM_ ("banamex", por ejemplo), no se mostrarán los íconos de los puntos de pago asociados a estos métodos.

[[[
```javascript
const settings = {
  ...,
  customization: {
  theme: 'default',
  customStyle: {
  valueProp: 'practicality',
  }
  }
}
```
```react-jsx
const customization = {
  theme: 'default',
  customStyle: {
  valueProp: 'practicality',
  }
};
```
]]]