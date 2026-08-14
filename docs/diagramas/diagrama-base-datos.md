# Diagrama de la Base de Datos — UICM

**Archivo:** [`diagrama-base-datos.svg`](./diagrama-base-datos.svg)

## 1. Propósito

Es el esquema **físico** completo: las 23 tablas de negocio con **todas** sus columnas, tipo de dato, llave primaria, llaves foráneas y restricciones `unique`, tal como quedan en la base de datos de producción —**MariaDB 11.8**, motor InnoDB, cotejamiento `utf8mb4_unicode_ci`— después de aplicar todas las migraciones en orden. En el entorno de desarrollo local el mismo esquema puede reconstruirse sobre SQLite, con las salvedades por motor que se indican más abajo.

A diferencia de `diagrama-entidad-relacion.svg` (conceptual, solo atributos clave), aquí está cada columna — es la referencia a usar cuando se va a escribir una consulta, una migración nueva o revisar qué tipo exacto tiene un campo.

## 2. Cómo leer una tabla

Cada caja es una tabla real. El color del encabezado indica el módulo al que pertenece (mismos colores que `diagrama-bloques.svg`). Cada fila es una columna, con tres partes:

- **Izquierda:** `PK` (llave primaria, dorado), `FK` (llave foránea con constraint real, azul) o `fk` en minúscula (relación lógica sin constraint en BD, naranja).
- **Centro:** nombre de la columna. Un `?` después del nombre indica que acepta `NULL`. `UQ` marca una restricción `unique` (simple o compuesta con otra columna).
- **Derecha:** tipo de dato SQL (`string`, `text`, `decimal(10,2)`, `enum(...)`, etc.), en gris.

Las líneas entre tablas son llaves foráneas: la flecha apunta de la tabla que **tiene** la columna FK hacia la tabla que **referencia** (el lado "uno" de la relación). Línea punteada = sin constraint real (ver `diagrama-entidad-relacion.md`, sección 5, para el caso de `alumnos`).

## 3. Cosas que no son obvias con solo ver el esquema actual

Estas notas vienen de revisar el historial completo de migraciones, no solo el estado final — sirven para no interpretar mal una columna:

- **`alumnos.estado`** hoy solo acepta `activo | inactivo | baja`. Originalmente existían cuatro valores (`activo`, `baja_temporal`, `baja_definitiva`, `egresado`); una migración los redujo a tres (`baja_temporal`→`inactivo`, `baja_definitiva` y `egresado`→`baja`). Si algún reporte o vista viejo menciona "egresado" como estado de un alumno, ya no es un valor válido en la base de datos.
- **`carga_academica.estado_revision`** puede ser `NULL`, y eso **no es lo mismo** que `'pendiente'`: `NULL` significa que el profesor todavía no ha enviado sus calificaciones a revisión; `'pendiente'` significa que ya las envió y Coordinación aún no responde.
- **`carga_academica`** ya no tiene una columna `horario` de texto libre — se reestructuró en `dia_semana` + `hora_inicio` + `hora_fin`, columnas estructuradas.
- **`calificaciones`** tuvo un esquema anterior con `tipo` (`parcial`/`extraordinario`) y un campo `numero` (primer/segundo parcial). Se reconstruyó por completo al esquema actual (`final`/`extraordinario`, sin `numero`); cualquier calificación parcial capturada antes de ese cambio ya no existe.
- **`periodos.estado`** ya no tiene una restricción `CHECK` a nivel de motor (se quitó en una reconstrucción posterior de la tabla) — los cuatro valores (`proximo`, `activo`, `cerrado`, `inactivo`) siguen siendo los válidos, pero ahora la validación es responsabilidad exclusiva de la aplicación.
- **`aspirantes`** y **`alumnos`** ya no tienen `email` único por sí solo — es único en combinación con `programa_id`, para permitir que la misma persona se registre en más de un programa ("doble carrera"). Lo mismo aplica al `curp` de `aspirantes`.
- **`configuracion_mercadopago.access_token`** y **`configuracion_correo.password`** están tipadas como `text` (no `string`) y llevan cast `encrypted` en el modelo — el valor se guarda cifrado en la base de datos, aunque el diagrama solo muestra el nombre y tipo de columna, no si el valor está cifrado.

## 4. Relación con los otros documentos

| Documento | Para qué sirve |
|---|---|
| `diagrama-bloques.svg` | Arquitectura de capas y módulos — dónde vive cada Controller. |
| `diagrama-flujo.svg` | Cómo se procesa una petición paso a paso (decisiones). |
| `diagrama-entidad-relacion.svg` | Vista conceptual de entidades y relaciones (sin el detalle de cada columna). |
| **`diagrama-base-datos.svg`** | Esquema físico completo — todas las columnas, tipos y constraints. |
| `diccionario-datos.md` | Descripción en texto de cada columna, tabla por tabla (el detalle que no cabe en el SVG). |

## 5. Notas de alcance

- No incluye las tablas de infraestructura de Laravel (`sessions`, `cache`, `jobs`, `migrations`, `failed_jobs`, `password_reset_tokens`) — no son parte del dominio de negocio.
- El diagrama refleja el estado **final** del esquema (después de las ~58 migraciones aplicadas), no el historial de cambios — ese historial está resumido en la sección 3 de este documento y, con más detalle, en `diccionario-datos.md`.
