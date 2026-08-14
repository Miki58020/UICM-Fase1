# Diagrama de Flujo — Ciclo de una Petición

**Archivo:** [`diagrama-flujo.svg`](./diagrama-flujo.svg)

## 1. Propósito

A diferencia de `diagrama-bloques.svg` (que muestra las **capas estáticas** del sistema: frontend, backend, servicios externos, datos), este diagrama muestra el **camino dinámico** que sigue cualquier petición dentro de esas capas: qué se decide, en qué orden, y qué pasa si algo falla.

Es un flujo **genérico**: no describe un módulo en particular (para eso existe `flujo-admision.html`, específico del proceso de admisión), sino el patrón que siguen *todas* las peticiones del sistema — desde que un aspirante llena un formulario público hasta que un profesor captura una calificación o Finanzas valida un pago.

## 2. Cómo leer el diagrama

El flujo va de arriba hacia abajo. Los rombos azules son decisiones; las flechas etiquetadas "Sí"/"No" indican el camino que se toma. Las cajas naranjas son salidas anticipadas (la petición termina ahí, normalmente mostrando un error o pidiendo algo al usuario). La caja dorada es la única etapa que sale del sistema hacia un tercero.

### 2.1 Entrada y autenticación

1. **Request HTTP** — el navegador envía la petición a `routes/web.php`.
2. **¿Hay sesión iniciada?** — si la ruta requiere autenticación y no hay sesión válida, se corta el flujo con una redirección a `/login`. Las rutas públicas (landing, registro de aspirantes, oferta educativa, contacto) pasan de largo esta pregunta.
3. **¿El rol tiene permiso?** — el middleware `rol:*` filtra por rol (`admin`, `coordinacion`, `control_escolar`, `finanzas`, `profesor`, `alumno`). Si el rol no coincide con lo que la ruta permite, responde `403` y termina ahí.

### 2.2 Validación

4. El **Controller** del módulo correspondiente recibe la petición.
5. Si la petición trae datos (`POST`/`PUT`), un **Form Request** valida las reglas del módulo (formatos, longitudes, unicidad, etc.). Si algo no es válido, responde `422` y regresa al formulario con los errores — el usuario nunca llega a la lógica de negocio con datos incorrectos.

### 2.3 Lógica de negocio y servicios externos

6. El Controller ejecuta la lógica propia del módulo: cálculos, reglas de negocio, permisos más finos que el rol (ej. "un profesor solo captura calificaciones de sus propios grupos").
7. **Solo dos flujos del sistema llaman a un servicio externo** en este punto: el cobro en línea (MercadoPago Checkout Pro) y el envío de correo (SMTP). Si el servicio falla, el sistema está diseñado para **no detener el flujo** — registra el error o marca el intento como pendiente (ej. un correo que no se pudo enviar no bloquea el alta del alumno; un pago rechazado simplemente no se marca como aprobado).

### 2.4 Persistencia y respuesta

8. **Eloquent ORM** hace la lectura o escritura sobre MariaDB (SQLite en el entorno de desarrollo). La integridad de las operaciones que tocan varias tablas se apoya en las restricciones de llave foránea declaradas en el esquema, que el motor InnoDB hace cumplir en cada escritura. El proyecto **no** envuelve estas operaciones en transacciones explícitas: no hay usos de `DB::transaction` ni de `beginTransaction` en `app/`.
9. El Controller prepara la respuesta: si la petición vino de una interacción con Alpine.js (`fetch`/AJAX), responde JSON; si no, renderiza la vista Blade completa.

## 3. Por qué existen las tres salidas de error

Las tres cajas naranjas (`login`, `403`, `422`) no son casos raros — son el mecanismo central de seguridad y de UX del sistema:

- **Redirección a login / 403** son las dos capas de control de acceso: primero "¿quién eres?", después "¿qué puedes hacer?". Ningún dato de negocio se toca antes de pasar ambas.
- **422** es lo que hace posible que los formularios del sistema (registro de aspirantes, alta masiva, captura de calificaciones, etc.) le devuelvan al usuario mensajes de error específicos por campo en vez de una pantalla en blanco.

## 4. Notas de alcance

- Este diagrama describe el **patrón de control de flujo**, no la lista de rutas o controllers — para eso ver `diagrama-bloques.svg` (módulos) y `diagrama-base-datos.svg` (persistencia).
- La tolerancia a fallas de servicios externos (paso 7) es una decisión de diseño explícita: el sistema prioriza que el flujo del usuario no se rompa por una caída temporal de MercadoPago o del servidor de correo.
