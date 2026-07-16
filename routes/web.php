<?php

use App\Http\Controllers\Admin\ConfiguracionApisController;
use App\Http\Controllers\Admin\ConfiguracionCorreoController;
use App\Http\Controllers\Admin\ConfiguracionMercadopagoController;
use App\Http\Controllers\AltaMasivaAlumnosController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AlumnoFinanzasController;
use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\AclaracionCalificacionController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\DocumentoAlumnoController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PaginaPrincipalController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\PeriodoProgramaController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\ReinscripcionController;
use App\Http\Controllers\SolicitudContrasenaController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Terminar sesión vía AJAX (cuando se detecta restauración de pestaña cerrada)
Route::post('/session/terminate', function (Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['status' => 'ok']);
})->name('session.terminate');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/oferta-educativa', [HomeController::class, 'ofertaEducativa'])->name('oferta-educativa');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Módulo de aspirantes (público)
Route::get('/registro', [AspiranteController::class, 'create'])->name('aspirantes.registro');
Route::post('/registro', [AspiranteController::class, 'store'])->name('aspirantes.store');

Route::view('/confirmacion', 'aspirantes.confirmacion')->name('aspirantes.confirmacion');
Route::view('/seguimiento', 'aspirantes.seguimiento')->name('aspirantes.seguimiento');
Route::get('/resultado', [AspiranteController::class, 'resultado'])->name('aspirantes.resultado');

// Módulo pago de inscripción (público)
Route::get('/aspirante/pago', [PagoController::class, 'create'])->name('aspirantes.pago');
Route::post('/aspirante/pago/procesar', [PagoController::class, 'procesar'])->name('aspirantes.pago.procesar');
Route::get('/aspirante/pago/retorno', [PagoController::class, 'retorno'])->name('aspirantes.pago.retorno');
Route::get('/aspirante/pago-confirmacion', [PagoController::class, 'confirmacion'])->name('aspirantes.pago.confirmacion');

// Webhook de Mercado Pago (excluido de CSRF en bootstrap/app.php)
Route::post('/mp/webhook', [PagoController::class, 'webhook'])->name('mp.webhook');

// Portal del profesor — captura de calificaciones
Route::middleware(['auth', 'rol:profesor'])->group(function () {
    Route::get('/profesor/calificaciones', [CalificacionController::class, 'indexProfesor'])->name('profesor.calificaciones.index');
    Route::get('/profesor/calificaciones/{carga}/capturar', [CalificacionController::class, 'capturar'])->name('profesor.calificaciones.capturar');
    Route::post('/profesor/calificaciones/{carga}/guardar', [CalificacionController::class, 'guardar'])->name('profesor.calificaciones.guardar');
    Route::post('/profesor/cambiar-password', [CalificacionController::class, 'cambiarPassword'])->name('profesor.cambiar-password');
    Route::get('/profesor/aclaraciones', [AclaracionCalificacionController::class, 'misAclaraciones'])->name('profesor.aclaraciones.index');
    Route::post('/profesor/calificaciones/{carga}/aclaracion/{alumno}', [AclaracionCalificacionController::class, 'solicitar'])->name('profesor.aclaraciones.solicitar');
    Route::get('/profesor/horario', [HorarioController::class, 'profesor'])->name('profesor.horario.index');
    Route::get('/profesor/grupos', [GrupoController::class, 'profesor'])->name('profesor.grupos.index');
    Route::get('/profesor/alumnos', [AlumnoController::class, 'profesor'])->name('profesor.alumnos.index');
});

// Portal del alumno
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno', [AlumnoController::class, 'dashboard'])->name('alumno.dashboard');
    Route::get('/alumno/materias', [AlumnoController::class, 'materias'])->name('alumno.materias.index');
    Route::get('/alumno/kardex', [AlumnoController::class, 'kardex'])->name('alumno.kardex');
    Route::get('/alumno/kardex/imprimir', [AlumnoController::class, 'kardexImprimir'])->name('alumno.kardex.imprimir');
    Route::get('/alumno/comprobante/{pago}', [AlumnoController::class, 'comprobante'])->name('alumno.comprobante');
    Route::post('/alumno/cambiar-password', [AlumnoController::class, 'cambiarPassword'])->name('alumno.cambiar-password');
    Route::get('/alumno/documentos', [DocumentoAlumnoController::class, 'index'])->name('alumno.documentos.index');
    Route::post('/alumno/documentos/{tipo}', [DocumentoAlumnoController::class, 'subir'])->name('alumno.documentos.subir');
    Route::get('/alumno/finanzas', [AlumnoFinanzasController::class, 'index'])->name('alumno.finanzas.index');
    Route::get('/alumno/pagos/{pago}/pagar', [PagoController::class, 'pagarAlumno'])->name('alumno.pagos.pagar');
    Route::get('/alumno/pagos/{pago}/retorno', [PagoController::class, 'retornoAlumno'])->name('alumno.pagos.retorno');
    Route::get('/alumno/horario', [HorarioController::class, 'alumno'])->name('alumno.horario.index');
});

// Polling de estado del alumno — solo requiere auth (sin CheckRol para no entrar en bucle de kick)
Route::get('/alumno/check-estado', [AlumnoController::class, 'checkEstado'])
    ->middleware('auth')
    ->name('alumno.check-estado');

// Módulo finanzas — Validación de pagos
Route::middleware(['auth', 'rol:finanzas'])->group(function () {
    Route::get('/finanzas/pagos', [PagoController::class, 'index'])->name('finanzas.pagos.index');
    Route::get('/finanzas/pagos/exportar', [PagoController::class, 'exportar'])->name('finanzas.pagos.exportar');
    Route::get('/finanzas/pagos/{pago}', [PagoController::class, 'show'])->name('finanzas.pagos.show');
    Route::patch('/finanzas/pagos/{pago}/aprobar', [PagoController::class, 'aprobar'])->name('finanzas.pagos.aprobar');
    Route::patch('/finanzas/pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])->name('finanzas.pagos.rechazar');
    Route::get('/finanzas/estadisticas', [PagoController::class, 'estadisticas'])->name('finanzas.estadisticas');
    Route::get('/finanzas/alumnos', [PagoController::class, 'alumnosEstadoPago'])->name('finanzas.alumnos');

    Route::get('/finanzas/tarifas', [TarifaController::class, 'index'])->name('finanzas.tarifas.index');
    Route::patch('/finanzas/tarifas/{tarifa}', [TarifaController::class, 'update'])->name('finanzas.tarifas.update');
});

// Módulo Control Escolar — Aspirantes e inscripciones
Route::middleware(['auth', 'rol:control_escolar'])->group(function () {
    Route::get('/admin/alumnos', [AlumnoController::class, 'listado'])->name('admin.alumnos.index');
    Route::patch('/admin/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('admin.alumnos.update');

    Route::get('/admin/expedientes', [ExpedienteController::class, 'index'])->name('admin.expedientes.index');
    Route::get('/admin/expedientes/{alumno}', [ExpedienteController::class, 'show'])->name('admin.expedientes.show');

    Route::get('/admin/aspirantes', [AspiranteController::class, 'index'])->name('admin.aspirantes.index');
    Route::get('/admin/aspirantes/{aspirante}', [AspiranteController::class, 'show'])->name('admin.aspirantes.show');
    Route::patch('/admin/aspirantes/{aspirante}/aprobar', [AspiranteController::class, 'aprobar'])->name('admin.aspirantes.aprobar');
    Route::patch('/admin/aspirantes/{aspirante}/rechazar', [AspiranteController::class, 'rechazar'])->name('admin.aspirantes.rechazar');

    Route::get('/admin/inscripciones', [InscripcionController::class, 'index'])->name('admin.inscripciones.index');
    Route::post('/admin/inscripciones/{alumno}/inscribir', [InscripcionController::class, 'inscribir'])->name('admin.inscripciones.inscribir');
    Route::get('/admin/inscripciones/{alumno}/resultado', [InscripcionController::class, 'resultado'])->name('admin.inscripciones.resultado');
    Route::post('/admin/inscripciones/{alumno}/reenviar', [InscripcionController::class, 'reenviar'])->name('admin.inscripciones.reenviar');

    Route::get('/admin/solicitudes-contrasena', [SolicitudContrasenaController::class, 'index'])->name('admin.solicitudes-contrasena.index');
    Route::post('/admin/solicitudes-contrasena/{solicitud}/atender', [SolicitudContrasenaController::class, 'atender'])->name('admin.solicitudes-contrasena.atender');

    Route::get('/admin/reinscripciones', [ReinscripcionController::class, 'index'])->name('admin.reinscripciones.index');
    Route::post('/admin/reinscripciones/{alumno}/generar-pago', [ReinscripcionController::class, 'generarPago'])->name('admin.reinscripciones.generar-pago');
    Route::post('/admin/reinscripciones/{alumno}/completar', [ReinscripcionController::class, 'completar'])->name('admin.reinscripciones.completar');
});

// Módulo admin — Gestión de usuarios del sistema
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/admin/usuarios', [UsuarioController::class, 'store'])->name('admin.usuarios.store');
    Route::patch('/admin/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');

    // APIs externas (MercadoPago, correo)
    Route::get('/admin/apis', [ConfiguracionApisController::class, 'index'])->name('admin.apis.index');
    Route::patch('/admin/apis/mercadopago/{configuracion}/credenciales', [ConfiguracionMercadopagoController::class, 'updateCredenciales'])->name('admin.apis.mercadopago.credenciales');
    Route::patch('/admin/apis/mercadopago/{configuracion}/urls', [ConfiguracionMercadopagoController::class, 'updateUrls'])->name('admin.apis.mercadopago.urls');
    Route::patch('/admin/apis/correo/{configuracion}/conexion', [ConfiguracionCorreoController::class, 'updateConexion'])->name('admin.apis.correo.conexion');
    Route::patch('/admin/apis/correo/{configuracion}/remitente', [ConfiguracionCorreoController::class, 'updateRemitente'])->name('admin.apis.correo.remitente');
    Route::patch('/admin/apis/correo/{configuracion}/dominio', [ConfiguracionCorreoController::class, 'updateDominio'])->name('admin.apis.correo.dominio');

    Route::get('/admin/pagina-principal', [PaginaPrincipalController::class, 'index'])->name('admin.pagina-principal.index');
    Route::post('/admin/pagina-principal/contacto', [PaginaPrincipalController::class, 'updateContacto'])->name('admin.pagina-principal.contacto');
    Route::post('/admin/pagina-principal/carrusel', [PaginaPrincipalController::class, 'storeImagen'])->name('admin.pagina-principal.carrusel.store');
    Route::delete('/admin/pagina-principal/carrusel/{imagen}', [PaginaPrincipalController::class, 'destroyImagen'])->name('admin.pagina-principal.carrusel.destroy');
    Route::patch('/admin/pagina-principal/carrusel/{imagen}/toggle', [PaginaPrincipalController::class, 'toggleImagen'])->name('admin.pagina-principal.carrusel.toggle');
    Route::post('/admin/pagina-principal/hero', [PaginaPrincipalController::class, 'storeHeroImagen'])->name('admin.pagina-principal.hero.store');
    Route::post('/admin/pagina-principal/oferta', [PaginaPrincipalController::class, 'storePrograma'])->name('admin.pagina-principal.oferta.store');
    Route::put('/admin/pagina-principal/oferta/{programa}', [PaginaPrincipalController::class, 'updatePrograma'])->name('admin.pagina-principal.oferta.update');
    Route::delete('/admin/pagina-principal/oferta/{programa}', [PaginaPrincipalController::class, 'destroyPrograma'])->name('admin.pagina-principal.oferta.destroy');
    Route::patch('/admin/pagina-principal/oferta/{programa}/toggle', [PaginaPrincipalController::class, 'togglePrograma'])->name('admin.pagina-principal.oferta.toggle');

    Route::post('/admin/pagina-principal/intereses', [PaginaPrincipalController::class, 'storeInteres'])->name('admin.pagina-principal.intereses.store');
    Route::put('/admin/pagina-principal/intereses/{interes}', [PaginaPrincipalController::class, 'updateInteres'])->name('admin.pagina-principal.intereses.update');
    Route::delete('/admin/pagina-principal/intereses/{interes}', [PaginaPrincipalController::class, 'destroyInteres'])->name('admin.pagina-principal.intereses.destroy');
    Route::patch('/admin/pagina-principal/intereses/{interes}/toggle', [PaginaPrincipalController::class, 'toggleInteres'])->name('admin.pagina-principal.intereses.toggle');

    Route::get('/admin/contactos', [ContactoController::class, 'index'])->name('admin.contactos.index');
    Route::post('/admin/contactos/{contacto}/responder', [ContactoController::class, 'responder'])->name('admin.contactos.responder');
});

// Módulo coordinación — Materias, profesores y carga académica
Route::middleware(['auth', 'rol:coordinacion'])->group(function () {
    Route::get('/admin/programas', [ProgramaController::class, 'index'])->name('admin.programas.index');
    Route::post('/admin/programas', [ProgramaController::class, 'store'])->name('admin.programas.store');
    Route::patch('/admin/programas/{programa}', [ProgramaController::class, 'update'])->name('admin.programas.update');
    Route::patch('/admin/programas/{programa}/toggle', [ProgramaController::class, 'toggle'])->name('admin.programas.toggle');

    Route::get('/admin/materias', [MateriaController::class, 'index'])->name('admin.materias.index');
    Route::post('/admin/materias', [MateriaController::class, 'store'])->name('admin.materias.store');
    Route::patch('/admin/materias/{materia}', [MateriaController::class, 'update'])->name('admin.materias.update');
    Route::patch('/admin/materias/{materia}/toggle', [MateriaController::class, 'toggle'])->name('admin.materias.toggle');

    Route::get('/admin/profesores', [ProfesorController::class, 'index'])->name('admin.profesores.index');
    Route::post('/admin/profesores', [ProfesorController::class, 'store'])->name('admin.profesores.store');
    Route::patch('/admin/profesores/{profesor}', [ProfesorController::class, 'update'])->name('admin.profesores.update');
    Route::patch('/admin/profesores/{profesor}/toggle', [ProfesorController::class, 'toggle'])->name('admin.profesores.toggle');
    Route::post('/admin/profesores/{profesor}/acceso', [ProfesorController::class, 'generarAcceso'])->name('admin.profesores.acceso');

    // Calificaciones — vista de coordinación
    Route::get('/admin/calificaciones', [CalificacionController::class, 'indexCoordinacion'])->name('admin.calificaciones.index');
    Route::post('/admin/calificaciones/{carga}/aprobar', [CalificacionController::class, 'aprobar'])->name('admin.calificaciones.aprobar');
    Route::post('/admin/calificaciones/{carga}/rechazar', [CalificacionController::class, 'rechazar'])->name('admin.calificaciones.rechazar');

    // Aclaraciones de calificación
    Route::get('/admin/aclaraciones', [AclaracionCalificacionController::class, 'index'])->name('admin.aclaraciones.index');
    Route::post('/admin/aclaraciones/{aclaracion}/aprobar', [AclaracionCalificacionController::class, 'aprobar'])->name('admin.aclaraciones.aprobar');
    Route::post('/admin/aclaraciones/{aclaracion}/rechazar', [AclaracionCalificacionController::class, 'rechazar'])->name('admin.aclaraciones.rechazar');

    // Contraseñas de profesores — coordinación
    Route::get('/admin/contrasenas-profesores', [SolicitudContrasenaController::class, 'index'])->name('admin.contrasenas-profesores.index');
    Route::post('/admin/contrasenas-profesores/{solicitud}/atender', [SolicitudContrasenaController::class, 'atender'])->name('admin.contrasenas-profesores.atender');

    Route::get('/admin/carga-academica', [CargaAcademicaController::class, 'index'])->name('admin.carga-academica.index');
    Route::post('/admin/carga-academica/{grupo}/generar', [CargaAcademicaController::class, 'generar'])->name('admin.carga-academica.generar');
    Route::patch('/admin/carga-academica/{carga}/actualizar', [CargaAcademicaController::class, 'actualizar'])->name('admin.carga-academica.actualizar');
    Route::get('/admin/carga-academica/horarios/plantilla', [CargaAcademicaController::class, 'plantillaHorarios'])->name('admin.carga-academica.plantilla-horarios');
    Route::post('/admin/carga-academica/horarios/importar', [CargaAcademicaController::class, 'importarHorarios'])->name('admin.carga-academica.importar-horarios');

    Route::get('/admin/alta-masiva-alumnos', [AltaMasivaAlumnosController::class, 'index'])->name('admin.alta-masiva-alumnos.index');
    Route::get('/admin/alta-masiva-alumnos/plantilla', [AltaMasivaAlumnosController::class, 'plantillaMigracion'])->name('admin.alta-masiva-alumnos.plantilla');
    Route::post('/admin/alta-masiva-alumnos/importar', [AltaMasivaAlumnosController::class, 'importarMigracion'])->name('admin.alta-masiva-alumnos.importar');

    Route::get('/admin/periodos', [PeriodoController::class, 'index'])->name('admin.periodos.index');
    Route::post('/admin/periodos', [PeriodoController::class, 'store'])->name('admin.periodos.store');
    Route::patch('/admin/periodos/{periodo}', [PeriodoController::class, 'update'])->name('admin.periodos.update');
    Route::patch('/admin/periodos/{periodo}/inscripcion', [PeriodoController::class, 'configurarInscripcion'])->name('admin.periodos.inscripcion');
    Route::patch('/admin/periodos/{periodo}/activar', [PeriodoController::class, 'activar'])->name('admin.periodos.activar');
    Route::patch('/admin/periodos/{periodo}/cerrar', [PeriodoController::class, 'cerrar'])->name('admin.periodos.cerrar');
    Route::patch('/admin/periodos/{periodo}/toggle-auto', [PeriodoController::class, 'toggleAuto'])->name('admin.periodos.toggleAuto');
    Route::delete('/admin/periodos/{periodo}', [PeriodoController::class, 'destroy'])->name('admin.periodos.destroy');

    Route::post('/admin/periodos/{periodo}/programas', [PeriodoProgramaController::class, 'store'])->name('admin.periodos.programas.store');
    Route::patch('/admin/periodos/{periodo}/programas/{programa}/toggle', [PeriodoProgramaController::class, 'toggle'])->name('admin.periodos.programas.toggle');
    Route::delete('/admin/periodos/{periodo}/programas/{programa}', [PeriodoProgramaController::class, 'destroy'])->name('admin.periodos.programas.destroy');

    Route::get('/admin/grupos', [GrupoController::class, 'index'])->name('admin.grupos.index');
    Route::post('/admin/grupos', [GrupoController::class, 'store'])->name('admin.grupos.store');
    Route::patch('/admin/grupos/{grupo}', [GrupoController::class, 'update'])->name('admin.grupos.update');
    Route::delete('/admin/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('admin.grupos.destroy');
});

// Servir archivos privados — solo usuarios autenticados del sistema
Route::get('/admin/archivo/{path}', function (string $path) {
    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return Storage::disk('local')->response($path);
})->middleware('auth')->where('path', '.*')->name('admin.archivo');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::post('/admin/notificar/{rol}', [App\Http\Controllers\NotificacionDepartamentoController::class, 'enviar'])
    ->middleware(['auth', 'rol:admin'])
    ->name('admin.notificar.departamento')
    ->where('rol', 'control_escolar|finanzas|coordinacion|admin');

Route::middleware('auth')->group(function () {
    Route::post('/perfil/foto', [\App\Http\Controllers\PerfilController::class, 'updateFoto'])->name('perfil.foto');
    Route::post('/perfil/cambiar-password', [\App\Http\Controllers\PerfilController::class, 'cambiarPassword'])->name('perfil.cambiar-password');
});

require __DIR__ . '/auth.php';
