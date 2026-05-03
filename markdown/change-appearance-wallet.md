> CLIENT_SIDE
>
> h1
>
> Cambiar de aspecto

Wallet Brick permite algunas personalizaciones visuales listadas en la tabla abajo, todas opcionales y del tipo `string`.

Si la propiedad enviada está vacía, la pantalla mostrará el diseño definido por el [*layout* predeterminado](/developers/es/docs/checkout-bricks/wallet-brick/default-rendering#bookmark_renderizar_el_brick). Por otro lado, al enviar un valor alternativo, este reemplazará el valor predeterminado.

| Clave | Opciones disponibles | Predeterminado |
|--- |--- | --- |
| theme | default ou black | default |
| customStyle.valuePropColor | Para el tema **default**, `valuePropColor` puede ser **blue ou white**, mientras que para el tema **dark**, `valuePropColor` puede ser **black**. | Para el tema **default**, el **predeterminado es blue**, mientras que para el tema **dark**, el **predeterminado es black**. |
| customStyle.buttonHeight | Mínimo: 48px. <br> Máximo: libre elección. | 48px |
| customStyle.borderRadius | Mínimo: livre escolha. <br> Máximo: libre elección. | 6px |
| customStyle.verticalPadding | Mínimo: 8px. <br> Máximo: libre elección. | 8px |
| customStyle.horizontalPadding | Mínimo: 0px. <br> Máximo: libre elección. | 0px |

[[[
```javascript
const settings = {
  ...,
  customization: {
  theme:'dark',
  customStyle: {
  valueProp: 'practicality',
  valuePropColor: 'black',
  borderRadius: '10px',
  verticalPadding: '10px',
  horizontalPadding: '10px',
  }
  }
}
```
```react-jsx
const customization = {
  theme:'dark',
  customStyle: {
  valueProp: 'practicality',
  valuePropColor: 'black',
  borderRadius: '10px',
  verticalPadding: '10px',
  horizontalPadding: '10px',
  }
};
```
]]]

## Ocultar texto de propuesta de valor (valueProp)

También es posible ocultar el texto de la propuesta de valor pasando el valor `boolean true` a la propiedad `customStyle.hideValueProp`. El **valor predeterminado** es `false`.

[[[
```javascript
const settings = {
  ...,
  customization: {
	 theme: 'default',
  customStyle: {
  hideValueProp: true,
  }
  }
}
```
```react-jsx
const customization = {
  theme: 'default',
  customStyle: {
  hideValueProp: true,
  }
};
```
]]]