# Diagrama de Bloques — UICM Sistema Integral de Gestión

**Archivo:** [`diagrama-bloques.svg`](./diagrama-bloques.svg)

## 1. Propósito

Este diagrama describe la arquitectura del Sistema Integral de Gestión de la UICM combinando dos perspectivas complementarias:

- **Arquitectura técnica:** las capas por las que atraviesa una solicitud, desde el actor que la origina hasta el dato que finalmente se lee o se escribe.
- **Módulos funcionales:** cómo se organiza la lógica de negocio dentro de la capa de aplicación, agrupada por área (admisión, académico, finanzas, etc.).

Sirve como referencia de alto nivel para entender cómo se relacionan el frontend, el backend, la base de datos y los servicios externos, sin entrar al detalle de rutas o clases individuales (para eso existe `mapa-sistema.html`, enfocado en flujos, entidades, roles y estados).

## 2. Capas del sistema

El diagrama se lee de arriba hacia abajo, siguiendo el recorrido de una solicitud típica.

### 2.1 Actores y roles del sistema

Representan a quién usa el sistema y bajo qué rol accede. El sistema define seis actores, cinco de ellos ligados a un rol de usuario autenticado (`rol` en la tabla `users`) y uno público:

| Actor | Rol técnico | Descripción |
|---|---|---|
| Aspirante | *(sin cuenta)* | Persona externa que llena el formulario de registro antes de ser alumno. |
| Alumno | `alumno` | Alumno inscrito con acceso al portal. |
| Profesor | `profesor` | Titular de grupos y materias. |
| Personal Administrativo | `admin`, `coordinacion`, `control_escolar` | Tres roles administrativos que comparten el mismo panel (`/admin`). |
| Finanzas | `finanzas` | Encargado de validar pagos y revisar estadísticas de ingresos. |

### 2.2 Capa de presentación (Frontend)

Construida con **Blade Templates + Tailwind CSS + Alpine.js** (sin SPA ni framework de JS pesado). Cada actor tiene una superficie de interacción dedicada:

- **Sitio Público** — landing, registro de aspirantes, oferta educativa.
- **Portal Alumno** — dashboard, materias, kárdex, finanzas, documentos.
- **Portal Profesor** — calificaciones, aclaraciones, horario, grupos.
- **Panel Administrativo** — aspirantes, alumnos, catálogos, usuarios, configuración. Compartido por los tres roles administrativos.
- **Panel Finanzas** — validación de pagos, estadísticas.

### 2.3 Capa de aplicación (Backend — Laravel)

Recibe las peticiones HTTP a través de `routes/web.php`, filtradas por middleware de autenticación y de rol (`rol:*`). La lógica de negocio se organiza en seis módulos funcionales, agrupando los *Controllers* del sistema:

| Módulo | Controllers principales | Responsabilidad |
|---|---|---|
| **Admisión** | `AspiranteController`, `InscripcionController`, `ContactoController` | Captura y revisión de solicitudes de ingreso. |
| **Académico** | `MateriaController`, `GrupoController`, `CalificacionController`, `AclaracionCalificacionController`, `CargaAcademicaController`, `HorarioController`, `PeriodoController`, `PeriodoProgramaController`, `ProgramaController`, `AltaMasivaAlumnosController`, `ReinscripcionController` | Estructura académica: programas, periodos, materias, grupos, calificaciones y reinscripción. |
| **Finanzas** | `PagoController`, `TarifaController`, `AlumnoFinanzasController` | Cobro de inscripciones y colegiaturas, tarifas, estado de cuenta. |
| **Usuarios & Accesos** | `UsuarioController`, `PerfilController`, `SolicitudContrasenaController`, controllers en `Auth/` | Autenticación, perfiles y recuperación de contraseña. |
| **Documentos & Expediente** | `DocumentoAlumnoController`, `ExpedienteController` | Carga y resguardo de documentos del alumno. |
| **Configuración & Avisos** | Controllers en `Admin/` (APIs de MercadoPago/Correo), `PaginaPrincipalController`, `NotificacionDepartamentoController` | Configuración administrable del sitio y de las integraciones externas, avisos internos. |

### 2.4 Servicios externos

La capa de aplicación se integra con dos servicios de terceros, con credenciales administrables desde el panel de Configuración & Avisos:

- **MercadoPago (Bricks)** — cobro en línea de inscripciones y colegiaturas. Actualmente en modo *sandbox* (credenciales de prueba); no hay dominio de producción todavía.
- **SMTP / Correo** — envío de notificaciones automáticas (folios, accesos, avisos), con dominio institucional configurable.

### 2.5 Capa de datos

Persistencia sobre **SQLite** (`database/database.sqlite`), accedida vía Eloquent ORM. Las entidades principales del modelo de datos son:

`Alumno`, `Aspirante`, `Profesor`, `Programa`, `Periodo`, `Materia`, `Grupo`, `CargaAcademica`, `Calificacion`, `AclaracionCalificacion`, `Pago`, `TarifaInscripcion`, `DocumentoAlumno`, `Contacto`, `User`, `Configuracion` (sitio, correo y MercadoPago).

## 3. Flujo general de una solicitud

```
Actor → Portal correspondiente (Blade) → routes/web.php + middleware rol:*
      → Controller del módulo → Eloquent ORM → SQLite
      → (según el módulo) MercadoPago o SMTP como servicios externos
```

## 4. Notas de alcance

- El diagrama describe **capas y módulos**, no la totalidad de rutas, controllers o tablas del sistema.
- Los tres roles administrativos (`admin`, `coordinacion`, `control_escolar`) comparten un solo panel visual, pero cada uno tiene permisos distintos a nivel de middleware — el diagrama los agrupa por simplicidad visual, no porque sean el mismo rol.
- Para el detalle de flujos transaccionales (admisión, pagos, calificaciones, aclaraciones, alta masiva), ver `mapa-sistema.html` en la raíz del proyecto.
