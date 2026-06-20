<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');

        $this->seedMaterias();
        $this->seedGrupos();
        $this->seedProfesores();
        $this->seedCargaAcademica();
        $aspiranteIds = $this->seedAspirantes();
        $this->seedAlumnos($aspiranteIds);
        $this->seedPagos();
        $this->seedContactos();
        $this->seedCalificaciones();
        $this->seedSolicitudesContrasena();

        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info('✓ Datos demo sembrados correctamente.');
    }

    // ─────────────────────────────────────────
    private function seedMaterias(): void
    {
        $now = now();
        $materias = [
            // Psicopedagogía (programa_id=1)
            ['clave' => 'PSI-101', 'nombre' => 'Fundamentos de Psicopedagogía',    'creditos' => 8, 'cuatrimestre' => 1, 'programa_id' => 1],
            ['clave' => 'PSI-102', 'nombre' => 'Desarrollo Humano',                 'creditos' => 8, 'cuatrimestre' => 1, 'programa_id' => 1],
            ['clave' => 'PSI-103', 'nombre' => 'Estadística Básica',                'creditos' => 6, 'cuatrimestre' => 1, 'programa_id' => 1],
            ['clave' => 'PSI-201', 'nombre' => 'Psicología del Aprendizaje',        'creditos' => 8, 'cuatrimestre' => 2, 'programa_id' => 1],
            ['clave' => 'PSI-202', 'nombre' => 'Didáctica General',                 'creditos' => 8, 'cuatrimestre' => 2, 'programa_id' => 1],
            ['clave' => 'PSI-203', 'nombre' => 'Orientación Educativa',             'creditos' => 6, 'cuatrimestre' => 2, 'programa_id' => 1],
            ['clave' => 'PSI-301', 'nombre' => 'Neurociencias Aplicadas',           'creditos' => 8, 'cuatrimestre' => 3, 'programa_id' => 1],
            ['clave' => 'PSI-302', 'nombre' => 'Evaluación Psicopedagógica',        'creditos' => 8, 'cuatrimestre' => 3, 'programa_id' => 1],
            ['clave' => 'PSI-303', 'nombre' => 'Intervención Temprana',             'creditos' => 6, 'cuatrimestre' => 3, 'programa_id' => 1],
            // Administración (programa_id=3)
            ['clave' => 'ADM-101', 'nombre' => 'Fundamentos de Administración',     'creditos' => 8, 'cuatrimestre' => 1, 'programa_id' => 3],
            ['clave' => 'ADM-102', 'nombre' => 'Contabilidad I',                    'creditos' => 8, 'cuatrimestre' => 1, 'programa_id' => 3],
            ['clave' => 'ADM-103', 'nombre' => 'Matemáticas Financieras',           'creditos' => 6, 'cuatrimestre' => 1, 'programa_id' => 3],
            ['clave' => 'ADM-201', 'nombre' => 'Mercadotecnia',                     'creditos' => 8, 'cuatrimestre' => 2, 'programa_id' => 3],
            ['clave' => 'ADM-202', 'nombre' => 'Contabilidad II',                   'creditos' => 8, 'cuatrimestre' => 2, 'programa_id' => 3],
            ['clave' => 'ADM-203', 'nombre' => 'Derecho Mercantil',                 'creditos' => 6, 'cuatrimestre' => 2, 'programa_id' => 3],
            ['clave' => 'ADM-301', 'nombre' => 'Finanzas Corporativas',             'creditos' => 8, 'cuatrimestre' => 3, 'programa_id' => 3],
            ['clave' => 'ADM-302', 'nombre' => 'Recursos Humanos',                  'creditos' => 8, 'cuatrimestre' => 3, 'programa_id' => 3],
            ['clave' => 'ADM-303', 'nombre' => 'Producción y Operaciones',          'creditos' => 6, 'cuatrimestre' => 3, 'programa_id' => 3],
        ];

        foreach ($materias as $m) {
            DB::table('materias')->insert(array_merge($m, ['activo' => 1, 'created_at' => $now, 'updated_at' => $now]));
        }

        $this->command->line('  Materias: ' . count($materias));
    }

    // ─────────────────────────────────────────
    private function seedGrupos(): void
    {
        $now  = now();
        $pid  = 8; // periodo 2026-1

        $grupos = [
            ['clave' => 'PSI-2026-1-A', 'programa_id' => 1, 'periodo_id' => $pid, 'cuatrimestre' => 1, 'capacidad' => 30],
            ['clave' => 'PSI-2026-2-A', 'programa_id' => 1, 'periodo_id' => $pid, 'cuatrimestre' => 2, 'capacidad' => 30],
            ['clave' => 'PSI-2026-3-A', 'programa_id' => 1, 'periodo_id' => $pid, 'cuatrimestre' => 3, 'capacidad' => 30],
            ['clave' => 'ADM-2026-1-A', 'programa_id' => 3, 'periodo_id' => $pid, 'cuatrimestre' => 1, 'capacidad' => 30],
            ['clave' => 'ADM-2026-2-A', 'programa_id' => 3, 'periodo_id' => $pid, 'cuatrimestre' => 2, 'capacidad' => 30],
        ];

        foreach ($grupos as $g) {
            DB::table('grupos')->insert(array_merge($g, ['created_at' => $now, 'updated_at' => $now]));
        }

        $this->command->line('  Grupos: ' . count($grupos));
    }

    // ─────────────────────────────────────────
    private function seedProfesores(): void
    {
        $now = now();

        $profesores = [
            ['nombre' => 'Alejandro Fuentes Mora',    'correo' => 'a.fuentes@uicm.edu.mx',   'telefono' => '55-21-43-65-87', 'especialidad' => 'Psicología Educativa'],
            ['nombre' => 'Carmen López Hernández',    'correo' => 'c.lopez@uicm.edu.mx',     'telefono' => '55-34-56-78-90', 'especialidad' => 'Administración de Empresas'],
            ['nombre' => 'Roberto Sánchez García',    'correo' => 'r.sanchez@uicm.edu.mx',   'telefono' => '55-56-78-90-12', 'especialidad' => 'Matemáticas y Estadística'],
            ['nombre' => 'Patricia Morales Ruiz',     'correo' => 'p.morales@uicm.edu.mx',   'telefono' => '55-67-89-01-23', 'especialidad' => 'Ciencias de la Educación'],
            ['nombre' => 'Carlos Mendoza Jiménez',   'correo' => 'c.mendoza@uicm.edu.mx',   'telefono' => '55-78-90-12-34', 'especialidad' => 'Finanzas y Negocios'],
        ];

        foreach ($profesores as $prof) {
            $user = DB::table('users')->insertGetId([
                'name'       => $prof['nombre'],
                'email'      => $prof['correo'],
                'password'   => Hash::make('profesor123'),
                'rol'        => 'profesor',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('profesores')->insert([
                'nombre'      => $prof['nombre'],
                'correo'      => $prof['correo'],
                'telefono'    => $prof['telefono'],
                'especialidad'=> $prof['especialidad'],
                'activo'      => 1,
                'user_id'     => $user,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $this->command->line('  Profesores: ' . count($profesores));
    }

    // ─────────────────────────────────────────
    private function seedCargaAcademica(): void
    {
        $now = now();
        $pid = 8;

        $grupos   = DB::table('grupos')->pluck('id', 'clave');
        $materias = DB::table('materias')->pluck('id', 'clave');
        $profs    = DB::table('profesores')->pluck('id', 'nombre');

        $pAlejandro = $profs['Alejandro Fuentes Mora'];
        $pCarmen    = $profs['Carmen López Hernández'];
        $pRoberto   = $profs['Roberto Sánchez García'];
        $pPatricia  = $profs['Patricia Morales Ruiz'];
        $pCarlos    = $profs['Carlos Mendoza Jiménez'];

        $cargas = [
            // PSI cuatrimestre 1
            ['grupo_id' => $grupos['PSI-2026-1-A'], 'materia_id' => $materias['PSI-101'], 'profesor_id' => $pAlejandro, 'horario' => 'Lun-Mié 08:00-10:00', 'aula' => 'A-101'],
            ['grupo_id' => $grupos['PSI-2026-1-A'], 'materia_id' => $materias['PSI-102'], 'profesor_id' => $pPatricia,  'horario' => 'Mar-Jue 10:00-12:00', 'aula' => 'A-102'],
            ['grupo_id' => $grupos['PSI-2026-1-A'], 'materia_id' => $materias['PSI-103'], 'profesor_id' => $pRoberto,   'horario' => 'Vie 08:00-11:00',     'aula' => 'A-103'],
            // PSI cuatrimestre 2
            ['grupo_id' => $grupos['PSI-2026-2-A'], 'materia_id' => $materias['PSI-201'], 'profesor_id' => $pAlejandro, 'horario' => 'Lun-Mié 08:00-10:00', 'aula' => 'B-101'],
            ['grupo_id' => $grupos['PSI-2026-2-A'], 'materia_id' => $materias['PSI-202'], 'profesor_id' => $pPatricia,  'horario' => 'Mar-Jue 10:00-12:00', 'aula' => 'B-102'],
            ['grupo_id' => $grupos['PSI-2026-2-A'], 'materia_id' => $materias['PSI-203'], 'profesor_id' => $pPatricia,  'horario' => 'Vie 08:00-11:00',     'aula' => 'B-103'],
            // PSI cuatrimestre 3
            ['grupo_id' => $grupos['PSI-2026-3-A'], 'materia_id' => $materias['PSI-301'], 'profesor_id' => $pAlejandro, 'horario' => 'Lun-Mié 10:00-12:00', 'aula' => 'C-101'],
            ['grupo_id' => $grupos['PSI-2026-3-A'], 'materia_id' => $materias['PSI-302'], 'profesor_id' => $pPatricia,  'horario' => 'Mar-Jue 08:00-10:00', 'aula' => 'C-102'],
            ['grupo_id' => $grupos['PSI-2026-3-A'], 'materia_id' => $materias['PSI-303'], 'profesor_id' => $pAlejandro, 'horario' => 'Vie 11:00-14:00',     'aula' => 'C-103'],
            // ADM cuatrimestre 1
            ['grupo_id' => $grupos['ADM-2026-1-A'], 'materia_id' => $materias['ADM-101'], 'profesor_id' => $pCarmen,    'horario' => 'Lun-Mié 14:00-16:00', 'aula' => 'D-101'],
            ['grupo_id' => $grupos['ADM-2026-1-A'], 'materia_id' => $materias['ADM-102'], 'profesor_id' => $pCarlos,    'horario' => 'Mar-Jue 14:00-16:00', 'aula' => 'D-102'],
            ['grupo_id' => $grupos['ADM-2026-1-A'], 'materia_id' => $materias['ADM-103'], 'profesor_id' => $pRoberto,   'horario' => 'Vie 14:00-17:00',     'aula' => 'D-103'],
            // ADM cuatrimestre 2
            ['grupo_id' => $grupos['ADM-2026-2-A'], 'materia_id' => $materias['ADM-201'], 'profesor_id' => $pCarmen,    'horario' => 'Lun-Mié 16:00-18:00', 'aula' => 'E-101'],
            ['grupo_id' => $grupos['ADM-2026-2-A'], 'materia_id' => $materias['ADM-202'], 'profesor_id' => $pCarlos,    'horario' => 'Mar-Jue 16:00-18:00', 'aula' => 'E-102'],
            ['grupo_id' => $grupos['ADM-2026-2-A'], 'materia_id' => $materias['ADM-203'], 'profesor_id' => $pCarmen,    'horario' => 'Vie 11:00-14:00',     'aula' => 'E-103'],
        ];

        foreach ($cargas as $c) {
            DB::table('carga_academica')->insert(array_merge($c, ['periodo_id' => $pid, 'created_at' => $now, 'updated_at' => $now]));
        }

        $this->command->line('  Carga académica: ' . count($cargas));
    }

    // ─────────────────────────────────────────
    private function seedAspirantes(): array
    {
        $now = now();

        $aspirantes = [
            // Pendientes
            ['nombre'=>'Valeria',    'ap'=>'Ramos',    'am'=>'Cisneros',  'email'=>'valeria.ramos@gmail.com',    'tel'=>'55-11-22-33-44', 'curp'=>'RACV020315MMCMSRA5', 'fn'=>'2002-03-15', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'pendiente'],
            ['nombre'=>'Diego',      'ap'=>'Herrera',  'am'=>'Vázquez',   'email'=>'diego.herrera@gmail.com',    'tel'=>'55-22-33-44-55', 'curp'=>'HEDV030820HMCRZGA3', 'fn'=>'2003-08-20', 'prog'=>3, 'gen'=>'2026-1', 'estado'=>'pendiente'],
            ['nombre'=>'Sofía',      'ap'=>'Castellanos','am'=>'Pérez',   'email'=>'sofia.castellanos@gmail.com','tel'=>'55-33-44-55-66', 'curp'=>'CAPS040112MDFSTFA9', 'fn'=>'2004-01-12', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'pendiente'],
            // Aprobados (esperando inscripción)
            ['nombre'=>'Andrés',     'ap'=>'Gutiérrez','am'=>'Torres',    'email'=>'andres.gutierrez@gmail.com', 'tel'=>'55-44-55-66-77', 'curp'=>'GUTA020505HDFTNA7', 'fn'=>'2002-05-05', 'prog'=>3, 'gen'=>'2026-1', 'estado'=>'aprobado'],
            ['nombre'=>'Fernanda',   'ap'=>'Mendoza',  'am'=>'Salinas',   'email'=>'fernanda.mendoza@gmail.com', 'tel'=>'55-55-66-77-88', 'curp'=>'MESF030914MDFNLA2', 'fn'=>'2003-09-14', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'aprobado'],
            // Rechazados
            ['nombre'=>'Rodrigo',    'ap'=>'Vargas',   'am'=>'Ortiz',     'email'=>'rodrigo.vargas@gmail.com',   'tel'=>'55-66-77-88-99', 'curp'=>'VAOR010225HDFRGA8', 'fn'=>'2001-02-25', 'prog'=>3, 'gen'=>'2026-1', 'estado'=>'rechazado', 'obs'=>'Documentación incompleta.'],
            ['nombre'=>'Daniela',    'ap'=>'Flores',   'am'=>'Cruz',      'email'=>'daniela.flores@gmail.com',   'tel'=>'55-77-88-99-00', 'curp'=>'FLCD020718MDFCRNA4', 'fn'=>'2002-07-18', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'rechazado', 'obs'=>'No cumple con los requisitos de bachillerato.'],
            // Aprobados ya inscritos (tienen alumno vinculado)
            ['nombre'=>'Mariana',    'ap'=>'Jiménez',  'am'=>'Rueda',     'email'=>'mariana.jimenez@gmail.com',  'tel'=>'55-88-99-00-11', 'curp'=>'JIRM010330MDFMNA6', 'fn'=>'2001-03-30', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'aprobado', 'tag'=>'inscrito'],
            ['nombre'=>'Luis',       'ap'=>'Palomino', 'am'=>'Reyes',     'email'=>'luis.palomino@gmail.com',    'tel'=>'55-99-00-11-22', 'curp'=>'PARL020614HDFMLS5', 'fn'=>'2002-06-14', 'prog'=>3, 'gen'=>'2026-1', 'estado'=>'aprobado', 'tag'=>'inscrito'],
            ['nombre'=>'Alejandra',  'ap'=>'Soto',     'am'=>'Miranda',   'email'=>'alejandra.soto@gmail.com',   'tel'=>'55-10-20-30-40', 'curp'=>'SOMA030907MDFTRA1', 'fn'=>'2003-09-07', 'prog'=>1, 'gen'=>'2026-1', 'estado'=>'aprobado', 'tag'=>'inscrito'],
        ];

        $folioBase  = 'UICM-2026-';
        $n          = 1;
        $insertados = [];

        foreach ($aspirantes as $a) {
            $id = DB::table('aspirantes')->insertGetId([
                'folio'            => $folioBase . str_pad($n++, 4, '0', STR_PAD_LEFT),
                'nombre'           => $a['nombre'],
                'apellido_paterno' => $a['ap'],
                'apellido_materno' => $a['am'],
                'email'            => $a['email'],
                'telefono'         => $a['tel'],
                'curp'             => $a['curp'],
                'fecha_nacimiento' => $a['fn'],
                'programa_id'      => $a['prog'],
                'generacion'       => $a['gen'],
                'estado'           => $a['estado'],
                'observaciones'    => $a['obs'] ?? null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $insertados[] = ['id' => $id, 'tag' => $a['tag'] ?? null, 'prog' => $a['prog']];
        }

        $this->command->line('  Aspirantes: ' . count($aspirantes));

        return $insertados;
    }

    // ─────────────────────────────────────────
    private function seedAlumnos(array $aspiranteData): void
    {
        $now    = now();
        $grupos = DB::table('grupos')->pluck('id', 'clave');

        // Filtrar los que tienen tag='inscrito' y agrupar por programa
        $inscritos = collect($aspiranteData)->filter(fn($a) => ($a['tag'] ?? null) === 'inscrito')->values();
        // inscritos[0]=Mariana(prog1), inscritos[1]=Luis(prog3), inscritos[2]=Alejandra(prog1)
        $idMariana   = $inscritos[0]['id'];
        $idLuis      = $inscritos[1]['id'];
        $idAlejandra = $inscritos[2]['id'];

        $alumnos = [
            // Cuatrimestre 1 PSI (aspirantes inscritos)
            ['matricula'=>'2611010001','nombre'=>'Mariana',   'ap'=>'Jiménez',  'am'=>'Rueda',   'email'=>'mariana.jimenez@gmail.com',  'prog'=>1,'grupo'=>'PSI-2026-1-A','cuatri'=>1,'cred'=>0,  'aspirante_id'=>$idMariana],
            ['matricula'=>'2611010003','nombre'=>'Alejandra', 'ap'=>'Soto',     'am'=>'Miranda', 'email'=>'alejandra.soto@gmail.com',   'prog'=>1,'grupo'=>'PSI-2026-1-A','cuatri'=>1,'cred'=>0,  'aspirante_id'=>$idAlejandra],
            // Cuatrimestre 1 ADM (aspirante inscrito)
            ['matricula'=>'2611030002','nombre'=>'Luis',      'ap'=>'Palomino', 'am'=>'Reyes',   'email'=>'luis.palomino@gmail.com',    'prog'=>3,'grupo'=>'ADM-2026-1-A','cuatri'=>1,'cred'=>0,  'aspirante_id'=>$idLuis],
            // Cuatrimestre 2 PSI
            ['matricula'=>'2511010004','nombre'=>'Isabel',    'ap'=>'Reyes',    'am'=>'Moreno',  'email'=>'isabel.reyes@gmail.com',     'prog'=>1,'grupo'=>'PSI-2026-2-A','cuatri'=>2,'cred'=>22,'aspirante_id'=>null],
            ['matricula'=>'2511010005','nombre'=>'Eduardo',   'ap'=>'Nava',     'am'=>'Blanco',  'email'=>'eduardo.nava@gmail.com',     'prog'=>1,'grupo'=>'PSI-2026-2-A','cuatri'=>2,'cred'=>22,'aspirante_id'=>null],
            // Cuatrimestre 2 ADM
            ['matricula'=>'2511030006','nombre'=>'Gabriela',  'ap'=>'Estrada',  'am'=>'Campos',  'email'=>'gabriela.estrada@gmail.com', 'prog'=>3,'grupo'=>'ADM-2026-2-A','cuatri'=>2,'cred'=>22,'aspirante_id'=>null],
            ['matricula'=>'2511030007','nombre'=>'Héctor',    'ap'=>'Ibarra',   'am'=>'Luna',    'email'=>'hector.ibarra@gmail.com',    'prog'=>3,'grupo'=>'ADM-2026-2-A','cuatri'=>2,'cred'=>22,'aspirante_id'=>null],
            // Cuatrimestre 3 PSI
            ['matricula'=>'2411010008','nombre'=>'Natalia',   'ap'=>'Cruz',     'am'=>'Espinosa','email'=>'natalia.cruz@gmail.com',     'prog'=>1,'grupo'=>'PSI-2026-3-A','cuatri'=>3,'cred'=>44,'aspirante_id'=>null],
        ];

        foreach ($alumnos as $al) {
            $user = DB::table('users')->insertGetId([
                'name'       => $al['nombre'] . ' ' . $al['ap'] . ' ' . $al['am'],
                'email'      => strtolower($al['matricula']) . '@uicm.edu.mx',
                'password'   => Hash::make('alumno123'),
                'rol'        => 'alumno',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('alumnos')->insert([
                'matricula'          => $al['matricula'],
                'user_id'            => $user,
                'aspirante_id'       => $al['aspirante_id'],
                'programa_id'        => $al['prog'],
                'grupo_id'           => $grupos[$al['grupo']],
                'nombre'             => $al['nombre'],
                'apellido_paterno'   => $al['ap'],
                'apellido_materno'   => $al['am'],
                'email'              => $al['email'],
                'cuatrimestre_actual'=> $al['cuatri'],
                'creditos_acumulados'=> $al['cred'],
                'estado'             => 'activo',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        $this->command->line('  Alumnos: ' . count($alumnos));
    }

    // ─────────────────────────────────────────
    private function seedPagos(): void
    {
        $now = now();

        // Pagos de inscripción para aspirantes aprobados e inscritos
        $aspirantes = DB::table('aspirantes')
            ->whereIn('estado', ['aprobado', 'inscrito'])
            ->get();

        foreach ($aspirantes as $asp) {
            DB::table('pagos')->insert([
                'aspirante_id' => $asp->id,
                'alumno_id'    => null,
                'concepto'     => 'inscripcion',
                'periodo'      => '2026-1',
                'monto'        => 2250,
                'estado'       => 'aprobado',
                'fecha_pago'   => Carbon::now()->subDays(rand(5, 30))->toDateString(),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // Pagos de reinscripción para alumnos de cuatrimestre 2 y 3
        $alumnos = DB::table('alumnos')->where('cuatrimestre_actual', '>', 1)->get();

        foreach ($alumnos as $al) {
            DB::table('pagos')->insert([
                'aspirante_id' => null,
                'alumno_id'    => $al->id,
                'concepto'     => 'reinscripcion',
                'periodo'      => '2026-1',
                'monto'        => 2250,
                'estado'       => 'aprobado',
                'fecha_pago'   => Carbon::now()->subDays(rand(5, 20))->toDateString(),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // Un pago pendiente (aspirante aprobado aún sin pagar)
        $aprobado = DB::table('aspirantes')->where('estado', 'aprobado')->first();
        if ($aprobado) {
            DB::table('pagos')->insert([
                'aspirante_id' => $aprobado->id,
                'alumno_id'    => null,
                'concepto'     => 'inscripcion',
                'periodo'      => '2026-1',
                'monto'        => 2250,
                'estado'       => 'pendiente',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $this->command->line('  Pagos: OK');
    }

    // ─────────────────────────────────────────
    private function seedContactos(): void
    {
        $now = now();

        $contactos = [
            ['nombre'=>'Jorge Pedraza Ruiz',    'correo'=>'jorge.pedraza@gmail.com',   'telefono'=>'55-11-11-22-22', 'interes'=>'Licenciatura en Psicopedagogía',     'mensaje'=>'Quisiera saber más sobre los requisitos de admisión para la carrera de Psicopedagogía.',        'atendido'=>0],
            ['nombre'=>'Claudia Reyes Soto',    'correo'=>'claudia.reyes@hotmail.com', 'telefono'=>'55-22-22-33-33', 'interes'=>'Licenciatura en Administración',      'mensaje'=>'Me interesa conocer las modalidades de pago y si hay becas disponibles para administración.',   'atendido'=>0],
            ['nombre'=>'Marco Ávila Torres',    'correo'=>'marco.avila@gmail.com',     'telefono'=>'55-33-33-44-44', 'interes'=>'Maestría en Educación',               'mensaje'=>'Soy licenciado en pedagogía y quiero continuar con posgrado. ¿Cuál es el proceso de admisión?','atendido'=>1],
            ['nombre'=>'Paola Guzmán Ríos',     'correo'=>'paola.guzman@gmail.com',    'telefono'=>'55-44-44-55-55', 'interes'=>'Licenciatura en Lengua Inglesa',      'mensaje'=>'Quiero saber si el programa de Lengua Inglesa tiene enfoque en traducción o solo en docencia.',  'atendido'=>1],
            ['nombre'=>'Tomás Carrillo Vargas', 'correo'=>'tomas.carrillo@yahoo.com',  'telefono'=>'55-55-55-66-66', 'interes'=>'Maestría en Negocios y Logística',    'mensaje'=>'Trabajo en importaciones y me interesa la maestría para avanzar en mi carrera profesional.',   'atendido'=>0],
        ];

        foreach ($contactos as $c) {
            DB::table('contactos')->insert(array_merge($c, ['created_at' => $now, 'updated_at' => $now]));
        }

        $this->command->line('  Contactos: ' . count($contactos));
    }

    // ─────────────────────────────────────────
    private function seedCalificaciones(): void
    {
        $now = now();

        // Calificaciones para alumnos de cuatrimestre 2 y 3
        $alumnos = DB::table('alumnos')->where('cuatrimestre_actual', '>', 1)->get();

        foreach ($alumnos as $al) {
            $grupo = DB::table('grupos')->where('id', $al->grupo_id)->first();
            if (!$grupo) continue;

            $cargas = DB::table('carga_academica')->where('grupo_id', $grupo->id)->get();

            foreach ($cargas as $carga) {
                $final = round(rand(55, 100) / 10, 1);

                DB::table('calificaciones')->insert([
                    'alumno_id'          => $al->id,
                    'carga_academica_id' => $carga->id,
                    'tipo'               => 'final',
                    'calificacion'       => $final,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                // Extraordinario solo si el final fue reprobatorio
                if ($final < 7.0) {
                    DB::table('calificaciones')->insert([
                        'alumno_id'          => $al->id,
                        'carga_academica_id' => $carga->id,
                        'tipo'               => 'extraordinario',
                        'calificacion'       => round(rand(70, 90) / 10, 1),
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ]);
                }

                DB::table('carga_academica')->where('id', $carga->id)->update([
                    'estado_revision' => 'aprobado',
                    'revisado_at'     => $now,
                ]);
            }
        }

        $total = DB::table('calificaciones')->count();
        $this->command->line('  Calificaciones: ' . $total);
    }

    // ─────────────────────────────────────────
    private function seedSolicitudesContrasena(): void
    {
        $now = now();

        $alumnos = DB::table('alumnos')->limit(2)->get();
        foreach ($alumnos as $al) {
            DB::table('solicitudes_contrasena')->insert([
                'user_id'    => $al->user_id,
                'estado'     => 'pendiente',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->line('  Solicitudes contraseña: 2');
    }
}
