# Matriz de trazabilidad — wireframes

Mapa entre cada wireframe (`docs/wireframes/*.svg`) y la(s) ruta(s) real(es) del sistema que representa.
34 pantallas consolidadas (patrones repetidos = 1 wireframe); ver el pie de nota de cada SVG para el detalle.

| # | Wireframe | Ruta(s) real(es) |
|---|-----------|-------------------|
| 1 | [`publico-landing.svg`](./publico-landing.svg) | `GET /` |
| 2 | [`publico-oferta.svg`](./publico-oferta.svg) | `GET /oferta-educativa` |
| 3 | [`login.svg`](./login.svg) | `GET /login` |
| 4 | [`aspirante-registro.svg`](./aspirante-registro.svg) | `GET/POST /registro` |
| 5 | [`aspirante-confirmacion.svg`](./aspirante-confirmacion.svg) | `GET /confirmacion` |
| 6 | [`aspirante-pago.svg`](./aspirante-pago.svg) | `GET /aspirante/pago` |
| 7 | [`aspirante-pago-confirmacion.svg`](./aspirante-pago-confirmacion.svg) | `GET /aspirante/pago-confirmacion` |
| 8 | [`aspirante-seguimiento.svg`](./aspirante-seguimiento.svg) | `GET /seguimiento` |
| 9 | [`aspirante-resultado.svg`](./aspirante-resultado.svg) | `GET /resultado` |
| 10 | [`dashboard.svg`](./dashboard.svg) | `GET /alumno`<br>`GET /dashboard` |
| 11 | [`alumno-materias.svg`](./alumno-materias.svg) | `GET /alumno/materias` |
| 12 | [`alumno-horario.svg`](./alumno-horario.svg) | `GET /alumno/horario` |
| 13 | [`alumno-kardex.svg`](./alumno-kardex.svg) | `GET /alumno/kardex`<br>`GET /alumno/kardex/imprimir` |
| 14 | [`alumno-documentos.svg`](./alumno-documentos.svg) | `GET /alumno/documentos` |
| 15 | [`alumno-finanzas.svg`](./alumno-finanzas.svg) | `GET /alumno/finanzas` |
| 16 | [`alumno-pagar.svg`](./alumno-pagar.svg) | `GET /alumno/pagos/{pago}/pagar`<br>`GET /alumno/comprobante/{pago}` |
| 17 | [`profesor-grupos.svg`](./profesor-grupos.svg) | `GET /profesor/grupos` |
| 18 | [`profesor-alumnos.svg`](./profesor-alumnos.svg) | `GET /profesor/alumnos` |
| 19 | [`profesor-calificaciones.svg`](./profesor-calificaciones.svg) | `GET /profesor/calificaciones` |
| 20 | [`profesor-calificaciones-capturar.svg`](./profesor-calificaciones-capturar.svg) | `GET /profesor/calificaciones/{carga}/capturar` |
| 21 | [`profesor-aclaraciones.svg`](./profesor-aclaraciones.svg) | `GET /profesor/aclaraciones` |
| 22 | [`profesor-horario.svg`](./profesor-horario.svg) | `GET /profesor/horario` |
| 23 | [`admin-crud-estandar.svg`](./admin-crud-estandar.svg) | `GET /admin/usuarios`<br>`GET /admin/alumnos`<br>`GET /admin/profesores`<br>`GET /admin/programas`<br>`GET /admin/materias`<br>`GET /admin/grupos`<br>`GET /admin/periodos`<br>`GET /admin/contactos` |
| 24 | [`admin-aspirante-revision.svg`](./admin-aspirante-revision.svg) | `GET /admin/aspirantes/{aspirante}` |
| 25 | [`admin-inscripcion-generar.svg`](./admin-inscripcion-generar.svg) | `POST /admin/inscripciones/{alumno}/inscribir` |
| 26 | [`admin-reinscripciones.svg`](./admin-reinscripciones.svg) | `GET /admin/reinscripciones` |
| 27 | [`admin-carga-academica.svg`](./admin-carga-academica.svg) | `GET /admin/carga-academica` |
| 28 | [`admin-alta-masiva.svg`](./admin-alta-masiva.svg) | `GET /admin/alta-masiva-alumnos` |
| 29 | [`admin-expediente.svg`](./admin-expediente.svg) | `GET /admin/expedientes/{alumno}` |
| 30 | [`admin-apis.svg`](./admin-apis.svg) | `GET /admin/apis` |
| 31 | [`admin-solicitudes-contrasena.svg`](./admin-solicitudes-contrasena.svg) | `GET /admin/solicitudes-contrasena`<br>`GET /admin/contrasenas-profesores` |
| 32 | [`finanzas-pagos.svg`](./finanzas-pagos.svg) | `GET /finanzas/pagos` |
| 33 | [`finanzas-estadisticas.svg`](./finanzas-estadisticas.svg) | `GET /finanzas/estadisticas` |
| 34 | [`finanzas-tarifas.svg`](./finanzas-tarifas.svg) | `GET /finanzas/tarifas` |
