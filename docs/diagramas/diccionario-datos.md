# Diccionario de Datos — UICM

**Índice visual:** [`diccionario-datos.svg`](./diccionario-datos.svg)

## 1. Propósito

Este documento describe, columna por columna, las 23 tablas de negocio del sistema tal como quedan en la base de datos de producción (**MariaDB 11.8**, InnoDB) después de aplicar todas las migraciones (`database/migrations/`). Es la referencia de texto que complementa a `diagrama-base-datos.svg` (visual) y `diagrama-entidad-relacion.svg` (conceptual).

**Convenciones de esta tabla:**
- **Null** — `Sí` si la columna acepta `NULL`.
- **Notas** — `PK` llave primaria · `FK→tabla.columna` llave foránea real (con su acción `ON DELETE`) · `fk→tabla.columna (sin constraint)` relación usada por la aplicación pero sin restricción en la base de datos · `UQ` columna única (se indica si es simple o compuesta).
- Todas las tablas tienen `created_at`/`updated_at` (`timestamp`, gestionados automáticamente por Eloquent); se omiten de las tablas de abajo por brevedad salvo que tengan un comportamiento especial.
- No se incluyen las tablas de infraestructura de Laravel (`sessions`, `cache`, `jobs`, `migrations`, `failed_jobs`, `password_reset_tokens`).

---

## 2. Núcleo / Usuarios

### 2.1 `users`
Cuenta de acceso al sistema. Un solo modelo de usuario para los cinco roles autenticados (`admin`, `coordinacion`, `control_escolar`, `finanzas`, `profesor`, `alumno`) — no hay tablas de usuario separadas por rol.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| name | string | No | — | Nombre(s) del usuario |
| apellido_paterno | string(100) | Sí | — | |
| apellido_materno | string(100) | Sí | — | |
| email | string | No | — | UQ · usado como usuario de login |
| rol | string | No | `alumno` | Valores de aplicación: admin/coordinacion/control_escolar/finanzas/profesor/alumno — no es un enum de BD |
| foto | string | Sí | — | Ruta del avatar |
| email_verified_at | timestamp | Sí | — | No se usa verificación de correo activamente en el flujo actual |
| password | string | No | — | Hash (cast `hashed`) |
| remember_token | string(100) | Sí | — | Token de "recordarme" |

### 2.2 `profesores`
Perfil de un profesor, independiente de si tiene o no cuenta de acceso.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| user_id | bigint | Sí | — | FK→users.id (`ON DELETE SET NULL`) — un profesor puede existir sin cuenta todavía |
| nombre | string | No | — | |
| correo | string | No | — | UQ |
| telefono | string(20) | Sí | — | |
| especialidad | string | Sí | — | |
| activo | boolean | No | `true` | Si es falso, no debería asignársele nueva carga académica |

### 2.3 `solicitudes_contrasena`
Cola de solicitudes de restablecimiento de contraseña, atendidas manualmente por el rol correspondiente.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| user_id | bigint | No | — | FK→users.id (`ON DELETE CASCADE`) |
| estado | enum(pendiente, atendida) | No | `pendiente` | |

---

## 3. Admisión

### 3.1 `aspirantes`
Persona que llena el formulario público de registro, antes de convertirse en alumno.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| folio | string(20) | No | — | UQ · identificador público que el aspirante usa para dar seguimiento |
| nombre | string | No | — | |
| apellido_paterno | string | No | — | |
| apellido_materno | string | Sí | — | |
| email | string | No | — | UQ compuesta con `programa_id` (permite doble carrera con el mismo correo) |
| telefono | string(20) | Sí | — | |
| curp | string(18) | Sí | — | UQ compuesta con `programa_id` |
| fecha_nacimiento | date | Sí | — | |
| programa_id | bigint | No | — | FK→programas.id (`RESTRICT`) |
| generacion | string(10) | Sí | — | |
| acta_nacimiento_url | string | Sí | — | Documento cargado |
| certificado_url | string | Sí | — | Documento cargado |
| identificacion_url | string | Sí | — | Documento cargado |
| comprobante_domicilio_url | string | Sí | — | Documento cargado |
| foto_url | string | Sí | — | Documento cargado |
| curp_url | string | Sí | — | Documento cargado |
| titulo_url | string | Sí | — | Documento cargado (solo aplica a posgrado) |
| estado | enum(pendiente, aprobado, rechazado) | No | `pendiente` | Controla si ya se generó el alumno |
| observaciones | text | Sí | — | Notas internas de Control Escolar |

### 3.2 `contactos`
Mensajes del formulario público de contacto (landing).

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| nombre | string | No | — | |
| correo | string | No | — | |
| telefono | string(10) | No | — | |
| interes | string | No | — | Texto libre; coincide semánticamente con `contacto_intereses.etiqueta` pero **no es una FK** |
| mensaje | text | Sí | — | |
| atendido | boolean | No | `false` | Marca de seguimiento interno |

### 3.3 `contacto_intereses`
Catálogo administrable que llena el dropdown "interés" del formulario público de contacto.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| etiqueta | string(100) | No | — | Texto mostrado en el formulario |
| orden | smallint | No | `0` | Orden de aparición |
| activo | boolean | No | `true` | |

---

## 4. Académico

### 4.1 `programas`
Catálogo de programas/carreras que ofrece la institución.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| clave | string(10) | No | — | UQ |
| nombre | string | No | — | |
| nivel | enum(licenciatura, maestria, doctorado) | No | `licenciatura` | |
| duracion_cuatrimestres | tinyint | No | `12` | |
| total_creditos | smallint | Sí | — | |
| activo | boolean | No | `true` | |
| numero_carrera | tinyint | Sí | — | Usado para construir la matrícula del alumno |

### 4.2 `periodos`
Cuatrimestres/ciclos escolares.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| nombre | string(10) | No | — | UQ · ej. `2026-1` |
| label | string(80) | No | — | Nombre para mostrar |
| fecha_inicio_registro | date | Sí | — | |
| fecha_fin_registro | date | Sí | — | |
| fecha_inicio_clases | date | Sí | — | |
| fecha_fin_clases | date | Sí | — | |
| estado | string | No | `proximo` | Valores de aplicación: proximo/activo/cerrado/inactivo. **Ya no tiene `CHECK` a nivel de BD** (se eliminó en una reconstrucción posterior de la tabla); la validación es solo de la aplicación |
| auto | boolean | No | `true` | Si el periodo se abre/cierra automáticamente por fecha |

### 4.3 `periodo_programa`
Tabla pivote entre `periodos` y `programas`, con atributos propios — no es un pivote simple.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| periodo_id | bigint | No | — | FK→periodos.id (`CASCADE`) · UQ compuesta con `programa_id` |
| programa_id | bigint | No | — | FK→programas.id (`RESTRICT`) |
| numero_carrera | smallint | No | — | Se usa para calcular la matrícula del alumno |
| numero_generacion | smallint | No | — | Se usa para calcular la matrícula del alumno |
| activo | boolean | No | `true` | |

### 4.4 `grupos`
Grupo de alumnos de un programa en un periodo específico.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| clave | string(20) | No | — | UQ |
| programa_id | bigint | No | — | FK→programas.id (`RESTRICT`) |
| periodo_id | bigint | No | — | FK→periodos.id (`RESTRICT`) |
| cuatrimestre | tinyint | No | — | |
| capacidad | smallint | No | `30` | |

### 4.5 `materias`
Catálogo de materias de un plan de estudios.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| clave | string(20) | No | — | UQ |
| nombre | string | No | — | |
| creditos | tinyint | No | `6` | |
| cuatrimestre | tinyint | No | — | Cuatrimestre del plan de estudios al que pertenece |
| programa_id | bigint | No | — | FK→programas.id (`RESTRICT`) |
| activo | boolean | No | `true` | |

### 4.6 `alumnos`
Alumno inscrito, generado a partir de un aspirante aprobado. **Tabla con avisos importantes — ver sección 4.6.1.**

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| matricula | string(20) | No | — | UQ |
| user_id | bigint | Sí | — | fk→users.id **(sin constraint en BD, ver 4.6.1)** |
| aspirante_id | bigint | Sí | — | fk→aspirantes.id **(sin constraint en BD)** |
| programa_id | bigint | No | — | fk→programas.id **(sin constraint en BD)** |
| periodo_id | bigint | Sí | — | FK→periodos.id (`SET NULL`) — única FK real de esta tabla hacia sus catálogos |
| grupo_id | bigint | Sí | — | fk→grupos.id **(sin constraint en BD)** |
| nombre | string | No | — | |
| apellido_paterno | string | No | — | |
| apellido_materno | string | Sí | — | |
| email | string | No | — | UQ compuesta con `programa_id` |
| cuatrimestre_actual | tinyint | No | `1` | |
| creditos_acumulados | smallint | No | `0` | |
| estado | string | No | — | Valores de aplicación: `activo` / `inactivo` / `baja` (ver 4.6.1) |
| migrado | boolean | No | `false` | Marca a los alumnos dados de alta por el importador de migración desde el sistema anterior |
| curp | string(18) | Sí | — | |
| telefono | string(20) | Sí | — | |
| fecha_nacimiento | date | Sí | — | |

#### 4.6.1 Avisos sobre `alumnos`

1. **Llaves foráneas dependientes del motor:** la migración de reparación del enum de `estado` (`2026_04_06_000001_fix_alumnos_estado_enum.php`) reconstruye la tabla completa **sólo cuando el motor es SQLite** y, al recrearla, declara `user_id`, `aspirante_id`, `programa_id` y `grupo_id` como columnas numéricas simples en vez de llaves foráneas con `constrained()`; sobre ese motor sólo `periodo_id` es una FK real. **En MariaDB la migración toma la rama `ALTER TABLE ... MODIFY` y las cinco llaves foráneas se conservan como restricciones reales.** El esquema documentado aquí es el de producción, sobre MariaDB.
2. **Cambio de valores de `estado`:** el esquema original tenía cuatro valores (`activo`, `baja_temporal`, `baja_definitiva`, `egresado`). Se migraron a tres (`activo`, `inactivo`, `baja`): `baja_temporal`→`inactivo`, y tanto `baja_definitiva` como `egresado`→`baja`. **`egresado` ya no existe como valor distinguible** — un alumno que egresó y uno que causó baja definitiva hoy tienen el mismo valor de `estado`.
3. **Relación de pagos no estándar:** el método `pagos()` del modelo trae los pagos por `aspirante_id` (el pago de inscripción original), no por `alumno_id`. Los pagos posteriores (reinscripción/colegiatura) se acceden con `reinscripciones()` (por `alumno_id`). Existe `todosLosPagos()` para traer ambos conjuntos juntos.

### 4.7 `carga_academica`
Asignación de una materia a un grupo, con su profesor y horario, en un periodo específico. Es el nodo central del área académica.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| grupo_id | bigint | No | — | FK→grupos.id (`RESTRICT`) · UQ compuesta con `materia_id` + `periodo_id` |
| materia_id | bigint | No | — | FK→materias.id (`RESTRICT`) |
| profesor_id | bigint | Sí | — | FK→profesores.id (`SET NULL`) |
| dia_semana | string(20) | Sí | — | Valores de aplicación: lunes…domingo (constante `CargaAcademica::DIAS_SEMANA`) |
| hora_inicio | string(5) | Sí | — | Formato `HH:MM` |
| hora_fin | string(5) | Sí | — | Formato `HH:MM` |
| aula | string(100) | Sí | — | Ensanchada de 20 a 100 caracteres |
| periodo_id | bigint | No | — | FK→periodos.id (`RESTRICT`) |
| fecha_inicio | date | Sí | — | |
| fecha_fin | date | Sí | — | |
| estado_revision | string(20) | Sí | `NULL` | Ver aviso abajo — `NULL` ≠ `'pendiente'` |
| motivo_rechazo | text | Sí | — | |
| revisado_por | bigint | Sí | — | FK→users.id (`SET NULL`) |
| revisado_at | timestamp | Sí | — | |

**Aviso:** `dia_semana`/`hora_inicio`/`hora_fin` reemplazaron a una columna `horario` de texto libre que ya no existe. Y `estado_revision` cambió de enum `NOT NULL` (default `'pendiente'`) a string nullable sin `CHECK`: hoy **`NULL` significa "el profesor aún no ha enviado calificaciones a revisión"**, mientras que `'pendiente'` significa "enviadas, esperando revisión de Coordinación" — son dos estados distintos, no equivalentes.

### 4.8 `calificaciones`
Calificación final (u extraordinaria) de un alumno en una `carga_academica`.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| alumno_id | bigint | No | — | FK→alumnos.id (`CASCADE`) · UQ compuesta con `carga_academica_id` + `tipo` |
| carga_academica_id | bigint | No | — | FK→carga_academica.id (`CASCADE`) |
| tipo | enum(final, extraordinario) | No | `final` | |
| calificacion | decimal(4,1) | No | — | Aprobatoria desde 7.0 |

**Aviso (con pérdida de datos histórica):** el esquema original tenía `tipo` (`parcial`/`extraordinario`) y una columna `numero` (1/2, para primer/segundo parcial). Una migración posterior **eliminó la tabla y la recreó** con el esquema actual — cualquier calificación parcial capturada antes de ese cambio se perdió; hoy solo existe una calificación "final" por materia-alumno, más una "extraordinario" opcional que la sustituye.

### 4.9 `aclaraciones_calificaciones`
Solicitud de un alumno para revisar una calificación ya capturada.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| alumno_id | bigint | No | — | FK→alumnos.id (`CASCADE`) |
| carga_academica_id | bigint | No | — | FK→carga_academica.id (`CASCADE`) |
| tipo | enum(final, extraordinario) | No | `final` | A qué calificación se refiere la aclaración |
| profesor_id | bigint | No | — | FK→profesores.id (`CASCADE`) |
| calificacion_propuesta | decimal(4,1) | No | — | Nueva calificación que propone el profesor |
| motivo | text | No | — | |
| estado | enum(pendiente, aprobada, rechazada) | No | `pendiente` | |
| revisado_por | bigint | Sí | — | FK→users.id (`SET NULL`) |
| revisado_at | timestamp | Sí | — | |
| motivo_rechazo | text | Sí | — | |

No tiene restricción `unique` en BD — la regla de "solo una aclaración activa a la vez por calificación" la aplica la lógica de negocio, no la base de datos.

### 4.10 `documentos_alumno`
Documentos del expediente de un alumno ya inscrito (distinto de los documentos que carga el aspirante).

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| alumno_id | bigint | No | — | FK→alumnos.id (`CASCADE`) · UQ compuesta con `tipo` |
| tipo | string(40) | No | — | Catálogo de aplicación: acta_nacimiento, curp, identificacion, comprobante_domicilio, foto, certificado, titulo (solo doctorado) |
| archivo_path | string | No | — | |
| fecha_subida | timestamp | No | — | |
| fecha_vigencia | date | Sí | — | Usada para marcar documentos por vencer/vencidos |

---

## 5. Finanzas

### 5.1 `pagos`
Registro de un pago (inscripción, reinscripción, colegiatura u otro), en línea o presencial.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| aspirante_id | bigint | Sí | — | FK→aspirantes.id (`SET NULL`) — pago de inscripción, antes de existir el alumno |
| alumno_id | bigint | Sí | — | FK→alumnos.id (`SET NULL`) — pagos posteriores (reinscripción/colegiatura) |
| concepto | enum(inscripcion, reinscripcion, cuatrimestre, colegiatura, otro) | No | `inscripcion` | Ampliado de un catálogo más corto |
| periodo | string(10) | No | — | Periodo al que corresponde el pago |
| mes | string(7) | Sí | — | Formato `2026-07`; solo aplica a `colegiatura` |
| monto | decimal(10,2) | No | — | |
| descuento | decimal(5,2) | No | `0` | |
| monto_original | decimal(10,2) | Sí | — | Monto antes del descuento aplicado |
| comprobante | string | Sí | — | Ruta del comprobante subido (pago presencial) |
| mp_preference_id | string | Sí | — | Referencia de MercadoPago |
| mp_payment_id | string | Sí | — | Referencia de MercadoPago |
| fecha_vencimiento | date | Sí | — | |
| fecha_pago | date | Sí | — | |
| estado | enum(pendiente, aprobado, rechazado) | No | `pendiente` | |
| observaciones | text | Sí | — | |

### 5.2 `tarifas_inscripcion`
Catálogo administrable de tarifas por nivel académico y tipo de cobro.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| nivel | string | No | — | UQ compuesta con `tipo`. Coincide en valor con `programas.nivel`, no es FK |
| tipo | string(20) | No | `inscripcion` | Valores de aplicación: inscripcion/colegiatura/cuatrimestre |
| monto | decimal(10,2) | No | — | |
| descuento | decimal(5,2) | No | `0.00` | |
| descuento_fecha_inicio | date | Sí | — | Ventana de pronto pago |
| descuento_fecha_fin | date | Sí | — | Ventana de pronto pago |
| dia_limite_pago | tinyint | Sí | — | |
| dias_descuento_pronto_pago | tinyint | Sí | — | |
| dias_anticipacion_cobro | tinyint | Sí | — | |
| dias_para_pagar | tinyint | Sí | — | |

---

## 6. Configuración & Marketing

### 6.1 `configuracion_sitio`
Almacén clave-valor genérico para configuración del sitio público.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| clave | string | No | — | UQ |
| valor | text | Sí | — | |

### 6.2 `configuracion_correo`
Configuración administrable del servidor SMTP (una fila activa a la vez, patrón singleton).

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| mailer | string | No | `smtp` | |
| host | string | Sí | — | |
| port | int | Sí | — | |
| username | string | Sí | — | |
| password | text | Sí | — | Cast `encrypted` — se guarda cifrado |
| from_address | string | No | — | |
| from_name | string | No | — | |
| dominio_institucional | string | No | `'uicm.edu.mx'` | Agregada después; usada para construir correos institucionales |
| activo | boolean | No | `true` | Marca cuál configuración está en uso |

### 6.3 `configuracion_mercadopago`
Configuración administrable de credenciales de MercadoPago (patrón singleton, igual que correo).

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| public_key | string | No | — | |
| access_token | text | No | — | Cast `encrypted` — se guarda cifrado; ensanchada de `string` a `text` |
| back_url_success | string | No | — | |
| back_url_pending | string | No | — | |
| back_url_failure | string | No | — | |
| notification_url | string | Sí | — | Webhook de MercadoPago |
| activo | boolean | No | `true` | |

### 6.4 `carrusel_imagenes`
Imágenes del carrusel de la landing pública.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| imagen_path | string | No | — | |
| orden | tinyint | No | `0` | |
| activo | boolean | No | `true` | |

### 6.5 `oferta_programas`
Contenido de marketing de "oferta educativa" en el sitio público — **independiente** de la tabla `programas` (no tiene FK hacia ella), pensada para poder describir programas antes de que existan formalmente en el catálogo académico.

| Columna | Tipo | Null | Default | Notas |
|---|---|---|---|---|
| id | bigint | No | — | PK |
| nombre | string | No | — | |
| nivel | enum(licenciatura, maestria, doctorado) | No | — | |
| descripcion | text | Sí | — | |
| puntos_clave | json | Sí | — | Cast `array` — lista de bullets mostrados en la tarjeta pública |
| orden | tinyint | No | `0` | |
| activo | boolean | No | `true` | |

---

## 7. Resumen de llaves foráneas sin constraint real en BD

Para referencia rápida — todas están en la tabla `alumnos` (ver 4.6.1 para el porqué):

| Columna | Referencia lógica | Constraint en BD |
|---|---|---|
| `alumnos.user_id` | `users.id` | No |
| `alumnos.aspirante_id` | `aspirantes.id` | No |
| `alumnos.programa_id` | `programas.id` | No |
| `alumnos.grupo_id` | `grupos.id` | No |
| `alumnos.periodo_id` | `periodos.id` | **Sí** (`SET NULL`) |

## 8. Documentos relacionados

| Documento | Contenido |
|---|---|
| `diagrama-bloques.md` / `.svg` | Arquitectura de capas y módulos |
| `diagrama-flujo.md` / `.svg` | Ciclo de decisiones de una petición |
| `diagrama-entidad-relacion.md` / `.svg` | Entidades, atributos clave y relaciones (vista conceptual) |
| `diagrama-base-datos.md` / `.svg` | Esquema físico completo (vista visual) |
| **`diccionario-datos.md`** | Este documento — detalle de cada columna en texto |
