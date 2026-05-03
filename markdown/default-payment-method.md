# Método de pago por defecto

Es posible inicializar Payment Brick con una opción de pago ya abierta. Para configurar un método de pago predeterminado, utiliza la configuración a continuación.

[[[
```Javascript
settings = {
 ...,
 customization: {
  ...,
  visual: {
  ...,
  defaultPaymentOption: {
  walletForm: true,
  // creditCardForm: true,
  // debitCardForm: true,
  // savedCardForm: 'card id sent in the initialization',
  // ticketForm: true,
  },
  },
 }
}
```
```react-jsx
const customization = {
 visual: {
  defaultPaymentOption: {
  walletForm: true,
  // creditCardForm: true,
  // debitCardForm: true,
  // savedCardForm: 'card id sent in the initialization',
  // ticketForm: true,
  },
 }
};
```
]]]

> WARNING
> 
> Atención
> 
> No es posible habilitar más de un método de pago predeterminado, así que usa solo una propiedad dentro del objeto `defaultPaymentOption`.

![default-payment-option-mlm](checkout-bricks/default-payment-option-mlm-es-v1.png)