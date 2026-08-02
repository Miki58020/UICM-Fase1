# Diagrama de Casos de Uso — UICM Sistema Integral de Gestión

**Archivo:** [`diagrama-casos-de-uso.svg`](./diagrama-casos-de-uso.svg)

## 1. Propósito

Mientras `diagrama-bloques.svg` describe la arquitectura (capas técnicas) y `diagrama-flujo.svg` describe el ciclo genérico de una petición, este diagrama responde una pregunta distinta: **¿qué puede hacer cada actor dentro del sistema?**

Es el diagrama UML de casos de uso clásico: un actor (muñeco) por cada rol, conectado a las funcionalidades (óvalos) a las que tiene acceso, todas dentro del límite del sistema (rectángulo grande). Está construido directamente a partir de los grupos de rutas protegidas por rol en `routes/web.php` y del middleware `CheckRol.php`, agrupando cada bloque de rutas en un caso de uso legible (no hay una elipse por cada ruta individual — serían más de 90).

## 2. Actores

El sistema define **7 actores**: uno público (sin cuenta) y seis roles autenticados (columna `rol` en la tabla `users`).

| Actor | Rol técnico | Carril en el diagrama |
|---|---|---|
| Aspirante | *(sin cuenta)* | Naranja |
| Alumno | `alumno` | Azul |
| Profesor | `profesor` | Dorado |
| Finanzas | `finanzas` | Naranja |
| Control Escolar | `control_escolar` | Azul |
| Coordinación | `coordinacion` | Dorado |
| Admin | `admin` | Verde (destacado) |

## 3. El caso especial de Admin

Revisando `CheckRol.php` se confirma algo que no era obvio a simple vista: la condición `if ($userRol !== 'admin' && $userRol !== $rol)` no solo le da a Admin sus propias rutas — hace que **cualquier ruta protegida por `rol:*` deje pasar también a un usuario con rol `admin`**, sin importar cuál sea `$rol`. En la práctica, Admin es un superusuario que puede entrar a las rutas de Alumno, Profesor, Finanzas, Control Escolar y Coordinación, además de tener sus propias rutas exclusivas (usuarios del sistema, configuración de APIs, página principal, contactos, notificaciones).

Esto se representa en el diagrama con dos recursos combinados, no solo una nota:

1. **Generalización UML** (línea con triángulo hueco `►`) desde Admin hacia cada uno de los otros 5 roles autenticados — la notación estándar para "este actor hereda los casos de uso de aquel".
2. Dentro del propio carril de Admin, una lista explícita "**+ TODOS LOS CASOS DE USO DE:**" con los 5 roles heredados, para que quede legible incluso sin conocer la notación UML.

Aspirante queda fuera de esa herencia a propósito: no es un rol autenticado, así que no hay nada que "heredar" en términos de middleware — sus casos de uso ya están cubiertos indirectamente por Control Escolar (que revisa y aprueba aspirantes) y por Admin (que hereda de Control Escolar).

## 4. Casos de uso por actor

Cada elipse agrupa una o varias rutas relacionadas de `routes/web.php`:

- **Aspirante (4):** registro público, seguimiento de solicitud, pago de ficha de inscripción, resultado de admisión.
- **Alumno (7):** dashboard, materias y horario, kárdex, documentos, finanzas/colegiatura, comprobante de pago, perfil (foto/contraseña).
- **Profesor (5):** grupos y alumnos asignados, horario, captura de calificaciones, aclaraciones, perfil.
- **Finanzas (5):** validación de pagos, exportación, estadísticas, alumnos al corriente, tarifas.
- **Control Escolar (6):** aspirantes, inscripciones, alumnos, expedientes, solicitudes de acceso, reinscripciones.
- **Coordinación (8):** programas/materias, profesores, calificaciones, aclaraciones, solicitudes de acceso (profesores), carga académica/horarios, alta masiva de alumnos, periodos/grupos.
- **Admin (5 propios + herencia):** usuarios del sistema, APIs externas, página principal, mensajes de contacto, notificar a departamentos.

## 5. Notas de alcance

- Las elipses agrupan rutas relacionadas (ej. `store`/`update`/`toggle` de un mismo recurso caen en un solo caso de uso "Gestionar X"), no son una traducción 1:1 de `routes/web.php`.
- Coordinación, Control Escolar y Admin comparten visualmente el prefijo `/admin/*` en las rutas reales, pero son tres roles distintos con permisos distintos a nivel de middleware — igual que se aclara en `diagrama-bloques.md`.
- Para el detalle de flujos transaccionales (qué pasa paso a paso al ejecutar cada caso de uso) ver `mapa-sistema.html`; para la arquitectura técnica, `diagrama-bloques.svg`.
