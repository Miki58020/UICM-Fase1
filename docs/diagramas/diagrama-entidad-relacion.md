# Diagrama Entidad-Relación — UICM

**Archivo:** [`diagrama-entidad-relacion.svg`](./diagrama-entidad-relacion.svg)

## 1. Propósito

Muestra las **23 entidades de negocio** del sistema (todas las tablas excepto la infraestructura propia de Laravel: `sessions`, `cache`, `jobs`, `migrations`, etc.), agrupadas por área funcional, con sus atributos clave y las relaciones entre ellas.

Es la vista **conceptual**: solo incluye la llave primaria (`PK`), las llaves foráneas (`FK`) y los atributos que identifican o distinguen a cada entidad de cara al negocio (claves, nombres, estados, montos). El listado exhaustivo de columnas — incluyendo tipos exactos, longitudes, valores por default y nulabilidad — vive en `diccionario-datos.md`; el esquema físico completo en `diagrama-base-datos.svg`.

## 2. Agrupación de entidades

| Grupo | Entidades |
|---|---|
| **Núcleo / Usuarios** | `users`, `profesores`, `solicitudes_contrasena` |
| **Admisión** | `aspirantes`, `contactos`, `contacto_intereses` |
| **Académico (catálogos)** | `programas`, `periodos`, `periodo_programa`, `grupos`, `materias` |
| **Académico (alumno)** | `alumnos`, `carga_academica`, `calificaciones`, `aclaraciones_calificaciones`, `documentos_alumno` |
| **Finanzas** | `pagos`, `tarifas_inscripcion` |
| **Configuración & Marketing** | `configuracion_sitio`, `configuracion_correo`, `configuracion_mercadopago`, `carrusel_imagenes`, `oferta_programas` |

Esta agrupación es la misma usada en `diagrama-base-datos.svg`, para que ambos diagramas se puedan leer lado a lado.

## 3. Cómo leer las relaciones

- Cada línea conecta una `FK` con la `PK` que referencia. La etiqueta `1—N` se lee desde la tabla con la llave foránea hacia la tabla referenciada: *"muchos registros de la tabla A pueden apuntar a un mismo registro de la tabla B"*.
- Línea **sólida gris** = la relación tiene una restricción de llave foránea real en la base de datos (SQLite la hace cumplir).
- Línea **punteada dorada** (`fk` en minúscula) = la relación existe y se usa en el código (Eloquent la declara y la aplicación la respeta), pero **no tiene un constraint de base de datos detrás**. Ver la sección 5.

## 4. Relaciones centrales del modelo

- **`aspirantes` → `alumnos` (1—1 lógico):** un aspirante aprobado genera un registro de alumno. No es una FK formal (se relaciona por `aspirante_id` en `alumnos`), sino el punto de conversión entre el flujo de admisión y la vida académica.
- **`programas` ⟷ `periodos` vía `periodo_programa`:** es la única relación N—M del modelo. La tabla intermedia no es un simple pivote — carga `numero_carrera` y `numero_generacion`, que es como el sistema calcula la matrícula de un alumno nuevo.
- **`carga_academica`** es el nodo central del área académica: conecta `grupo`, `materia`, `profesor` y `periodo` en una sola fila, y de ahí cuelgan `calificaciones` y `aclaraciones_calificaciones`.
- **`pagos`** se relaciona con **dos** entidades del flujo de admisión/académico (`aspirantes` y `alumnos`) porque cubre dos momentos distintos: el pago de inscripción (antes de que exista el alumno) y los pagos posteriores de reinscripción/colegiatura (ya con el alumno dado de alta).
- **`tarifas_inscripcion`** y **`contacto_intereses`** son catálogos administrables sin relación de base de datos formal con el resto — se enlazan por coincidencia de valor (`nivel`, `interes`), no por FK.

## 5. Aviso importante: relaciones sin FK real en la tabla `alumnos`

Una reconstrucción de la tabla `alumnos` (migración `2026_04_06_000001_fix_alumnos_estado_enum.php`, para poder cambiar el enum de `estado` en SQLite) recreó la tabla sin volver a declarar las llaves foráneas originales. En el estado actual de la base de datos:

- `alumnos.user_id`, `alumnos.aspirante_id`, `alumnos.programa_id` y `alumnos.grupo_id` **no tienen constraint de FK a nivel de motor** — son columnas numéricas sueltas.
- Sólo `alumnos.periodo_id` (agregada en una migración posterior) sí quedó como FK real con `ON DELETE SET NULL`.
- Los modelos Eloquent (`Alumno::user()`, `::aspirante()`, `::programa()`, `::grupo()`) siguen declarando estas relaciones con normalidad, y la aplicación las respeta — el riesgo es únicamente que **la base de datos no rechazaría, por sí sola, un `user_id` o `programa_id` inexistente** insertado fuera de la aplicación (por ejemplo, en una consulta manual o un script de migración de datos).

En el diagrama, estas cuatro relaciones aparecen con línea punteada dorada y la etiqueta `fk` en minúscula para distinguirlas de las relaciones con integridad referencial real.

## 6. Notas de alcance

- No se muestran los `timestamps` (`created_at`/`updated_at`) ni columnas puramente descriptivas sin valor de identificación — están todas en el diccionario de datos.
- Los tres roles administrativos comparten la tabla `users` (columna `rol`), no hay una tabla separada por rol.
- Para el detalle de qué controller y qué vista usa cada entidad, ver `diagrama-bloques.svg` y `mapa-sistema.html`.
