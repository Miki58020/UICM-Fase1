<?php

namespace Database\Seeders;

use App\Models\Materia;
use App\Models\Programa;
use Illuminate\Database\Seeder;

class MateriasRealesSeeder extends Seeder
{
    /**
     * Plan de estudios oficial por cuatrimestre. Créditos asignados de forma
     * uniforme (8 por materia) al no contar con el valor real por materia.
     */
    private function planes(): array
    {
        return [
            'psicopedagogia' => [
                'prefijo' => 'PSI',
                'duracion' => 10,
                'cuatrimestres' => [
                    ['Introducción a la Psicopedagogía', 'Psicología General', 'Desarrollo Humano y Educación', 'Historia de la Educación'],
                    ['Comunicación Educativa', 'Historia de la Psicología', 'Metodología de la Intervención Psicopedagógica', 'Teoría del Conocimiento'],
                    ['Lectura, Expresión Oral y Escrita', 'Educación en Valores', 'Desarrollo de Habilidades Interpersonales', 'Sociología de la Educación', 'Teoría Pedagógica'],
                    ['Personalidad y Educación', 'Psicología del Desarrollo de la Edad Infantil', 'Psicología del Desarrollo del Adolescente y el Adulto Joven', 'Didáctica General', 'Neurociencias y Educación'],
                    ['Psicología del Adulto y del Adulto Mayor', 'Entornos Virtuales de Aprendizaje', 'Didáctica del Proceso Educativo', 'Psicología del Aprendizaje'],
                    ['Diagnóstico Psicopedagógico', 'Psicología Educativa', 'Estrategias y Técnicas Didácticas Centradas en el Aprendizaje', 'Métodos y Técnicas de Investigación Psicopedagógica'],
                    ['Psicología de Grupo', 'Administración y Gestión de Instituciones Educativas', 'Evaluación del Aprendizaje', 'Legislación y Política Educativa', 'Pedagogía Institucional'],
                    ['Métodos de Evaluación Psicopedagógica', 'Orientación Educativa', 'Problemas del Aprendizaje', 'Orientación Familiar', 'Evaluación de la Sexualidad'],
                    ['Educación Especial', 'Higiene Mental del Proceso Pedagógico', 'Diseño Curricular', 'Educación para la Vida y el Bienestar Personal'],
                    ['Proyectos Psicopedagógicos Innovadores', 'Intervención Psicopedagógica', 'Planeación Didáctica', 'Orientación Personal', 'Seminario de Investigación Psicopedagógica'],
                ],
            ],
            'lengua_inglesa' => [
                'prefijo' => 'LEI',
                'duracion' => 9,
                'cuatrimestres' => [
                    ['Inglés Principiantes', 'Expresión Oral y Escrita', 'Fundamentos de la Cultura Contemporánea', 'Estrategias para el Aprendizaje de Lengua Extranjera', 'Sistemas de Información'],
                    ['Inglés Elemental', 'Iniciación a la Lectura y Redacción de Inglés', 'Liderazgo de Equipo de Alto Desempeño', 'Introducción a la Investigación', 'Desarrollo Humano y Valores'],
                    ['Inglés Pre-Intermedio', 'Fundamentos de la Cultura Lingüística General', 'Habilidades de Lectura y Redacción en Inglés', 'Composición de Textos en Español', 'Cultura Británica'],
                    ['Inglés Intermedio', 'Cultura Estadounidense', 'Sistemas Lingüísticos', 'Iniciación a la Traducción', 'Inteligencia Emocional y Manejo de Conflictos'],
                    ['Inglés Intermedio Alto', 'Estudios de la Traducción', 'Iniciación a la Literatura en Lengua Inglesa', 'Lectura y Redacción de Textos Académicos en Inglés', 'Habilidades Cognitivas y Creatividad'],
                    ['Inglés Avanzado', 'Literatura en Lengua Inglesa', 'Enseñanza Aprendizaje del Inglés', 'Métodos y Enfoques Actuales en la Enseñanza del Inglés', 'Ética Profesional'],
                    ['Planeación de la Práctica Docente', 'Taller de Traducción', 'Literatura Comparada Inglés-Español', 'Gramática Contrastiva', 'Desarrollo Sustentable'],
                    ['Práctica Docente', 'Tecnología para la Enseñanza Aprendizaje de Lengua Extranjera', 'Traducción de Textos Científico Técnicos', 'Cultura de los Pueblos Anglófonos', 'Traductología'],
                    ['Seminario de Investigación', 'Auxiliares Informáticos para la Traducción', 'Didáctica del Inglés a través de la Traducción', 'La Enseñanza Aprendizaje del Inglés a Niños', 'Traducción de Textos Humanístico Literarios'],
                ],
            ],
            'administracion' => [
                'prefijo' => 'ADM',
                'duracion' => 9,
                'cuatrimestres' => [
                    ['Introducción a la Administración', 'Matemática Aplicada a la Administración', 'Expresión Oral y Escrita', 'Aspectos Legales de la Administración', 'Introducción a la Contabilidad'],
                    ['Metodología de la Investigación Científica', 'Estadística Descriptiva', 'Proceso Administrativo', 'Microeconomía', 'Sistemas de Información en las Organizaciones'],
                    ['Liderazgo de Equipo de Alto Desempeño', 'Macroeconomía', 'Fundamentos de Mercadotecnia', 'Desarrollo Sustentable', 'Métodos y Técnicas de Investigación Científica'],
                    ['Matemáticas Financieras', 'Comportamiento Organizacional', 'Contabilidad de Costos', 'Investigación de Mercados', 'Administración y Gestión del Talento Humano'],
                    ['Planeación Estratégica', 'Investigación de Operaciones', 'Administración de la Producción', 'Administración de la Calidad', 'Derecho Laboral'],
                    ['Desarrollo de Habilidades Directivas', 'Administración Financiera', 'Innovación y Emprendimiento', 'Comercio Internacional'],
                    ['Auditoría Administrativa', 'Consultoría Empresarial', 'Derecho Fiscal', 'Tecnologías de la Información Aplicada a la Administración', 'Negociación y Toma de Decisiones Efectivas'],
                    ['Comercio Electrónico', 'Desarrollo Organizacional', 'Formulación y Evaluación de Proyectos de Inversión', 'Gestión de Marca', 'Logística Administrativa'],
                    ['Plan de Negocios', 'Ética Profesional', 'Derecho Mercantil', 'Responsabilidad Social Empresarial', 'Seminario de Investigación'],
                ],
            ],
            'mba' => [
                'prefijo' => 'MBA',
                'duracion' => 5,
                'cuatrimestres' => [
                    ['Administración de las Organizaciones', 'Teoría Económica', 'Metodología de la Investigación Científica', 'Calidad y Gestión por Procesos'],
                    ['Globalización y Modernización', 'Métodos y Técnicas de Investigación Científica', 'Tecnologías de la Información Aplicadas a los Negocios', 'Planeación Estratégica'],
                    ['Administración Financiera', 'Gestión y Dirección del Capital Humano', 'Desarrollo Económico', 'Investigación de Operaciones para la Toma de Decisiones'],
                    ['Mercadotecnia Internacional', 'Gestión Logística y Operaciones', 'Negociación y Toma de Decisiones', 'Comercialización Internacional'],
                    ['Emprendimiento y Diseño de Modelos de Negocios', 'Planificación y Control de la Producción', 'Responsabilidad Social y Sustentabilidad', 'Administración de Redes Empresariales'],
                ],
            ],
            'maestria_educacion' => [
                'prefijo' => 'MAE',
                'duracion' => 5,
                'cuatrimestres' => [
                    ['Filosofía de la Educación', 'Enfoques Sociales de la Educación', 'Investigación y Análisis de Caso'],
                    ['Teoría General de Sistemas en Educación', 'Política y Educación en México', 'Administración y Gestión Educativa'],
                    ['Teoría de la Organización en la Administración Escolar', 'Enfoques Actuales de la Educación en el Mundo Contemporáneo', 'Modelos de Evaluación Institucional'],
                    ['Modelos de Desarrollo Curricular', 'Sociología de la Educación', 'Métodos y Técnicas de Investigación en la Administración Educativa'],
                    ['Ética en la Práctica Docente', 'Innovación Educativa', 'Seminario de Investigación en la Docencia'],
                ],
            ],
            'doctorado_educacion' => [
                'prefijo' => 'DOE',
                'duracion' => 6,
                'cuatrimestres' => [
                    ['Didáctica Aplicada', 'Fundamentos Teóricos y Metodológicos de la Investigación Científica'],
                    ['Metodología Avanzada de la Investigación Científica', 'Principios Filosóficos y Sociológicos de la Educación'],
                    ['Diseño de la Investigación Científica Educativa', 'Problemas Actuales de la Educación'],
                    ['Diseño Curricular', 'Organización y Gestión de Centros Educativos'],
                    ['Las Tecnologías de la Información en la Educación y la Sociedad del Conocimiento', 'Planeación Didáctica Innovadora'],
                    ['Elaboración del Documento Recepcional de Grado Doctoral', 'Seminario de Publicaciones Científicas'],
                ],
            ],

            // PROVISIONAL: no se cuenta con el plan de estudios oficial de este
            // programa. Reemplazar en cuanto la coordinación lo proporcione.
            'lengua_literatura' => [
                'prefijo' => 'LYL',
                'duracion' => 12,
                'cuatrimestres' => [
                    ['Introducción a la Lengua y la Literatura', 'Comunicación Oral y Escrita', 'Historia de la Lengua Española', 'Métodos de Investigación Literaria'],
                    ['Lingüística General', 'Literatura Universal I', 'Teoría Literaria', 'Redacción Académica'],
                    ['Literatura Española', 'Gramática Española Avanzada', 'Semiótica y Análisis del Discurso', 'Literatura Universal II'],
                    ['Literatura Mexicana', 'Literatura Latinoamericana I', 'Sociolingüística', 'Didáctica de la Lengua'],
                    ['Literatura Latinoamericana II', 'Estilística y Retórica', 'Crítica Literaria', 'Literatura Comparada'],
                    ['Literatura Contemporánea', 'Taller de Creación Literaria I', 'Análisis del Discurso Periodístico', 'Pragmática del Lenguaje'],
                    ['Literatura Infantil y Juvenil', 'Taller de Creación Literaria II', 'Traducción Literaria', 'Historia de la Lingüística'],
                    ['Literatura y Cine', 'Edición y Corrección de Estilo', 'Metodología de la Investigación Lingüística', 'Literatura de Vanguardia'],
                    ['Literatura Digital y Nuevos Medios', 'Planeación Didáctica de la Lengua', 'Seminario de Tesis I', 'Lectura y Análisis de Textos Académicos'],
                    ['Promoción y Animación a la Lectura', 'Gestión Editorial', 'Seminario de Tesis II', 'Ética y Deontología Profesional'],
                    ['Práctica Docente en Lengua y Literatura', 'Literatura y Sociedad', 'Seminario de Investigación Aplicada', 'Optativa de Especialización I'],
                    ['Proyecto Terminal de Lengua y Literatura', 'Seminario de Titulación', 'Optativa de Especialización II', 'Desarrollo Profesional Docente'],
                ],
            ],

            // PROVISIONAL: no se cuenta con el plan de estudios oficial de este
            // programa. Reemplazar en cuanto la coordinación lo proporcione.
            'negocios_logistica' => [
                'prefijo' => 'NLI',
                'duracion' => 6,
                'cuatrimestres' => [
                    ['Fundamentos de los Negocios Internacionales', 'Economía Global', 'Metodología de la Investigación Científica', 'Comercio Internacional'],
                    ['Cadena de Suministro Global', 'Logística Internacional', 'Finanzas Internacionales', 'Negociación Internacional'],
                    ['Gestión de Operaciones Portuarias y Aduaneras', 'Transporte Multimodal', 'Marketing Internacional', 'Derecho del Comercio Internacional'],
                    ['Gestión de Inventarios y Almacenamiento', 'Tecnologías de la Información en Logística', 'Mercados Financieros Internacionales', 'Responsabilidad Social y Sustentabilidad'],
                    ['Planeación Estratégica de Negocios Internacionales', 'Gestión de Riesgos en el Comercio Global', 'Comercio Electrónico Internacional', 'Seminario de Investigación'],
                    ['Proyecto de Negocios Internacionales', 'Análisis de Casos de Logística Global', 'Seminario de Titulación', 'Liderazgo y Negociación Intercultural'],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $creditosPorMateria = 8;

        foreach ($this->planes() as $claveProgramaPrefijo => $plan) {
            $programa = Programa::where('clave', $claveProgramaPrefijo)->first();
            if (!$programa) {
                $this->command?->warn("Programa '{$claveProgramaPrefijo}' no encontrado, se omite.");
                continue;
            }

            // No se reemplazan materias que ya tengan carga académica asignada.
            $materiaIdsConCarga = Materia::where('programa_id', $programa->id)
                ->whereHas('cargaAcademica')
                ->pluck('id');

            if ($materiaIdsConCarga->isNotEmpty()) {
                $this->command?->warn("'{$programa->nombre}' tiene materias con carga académica registrada, se omite para no romper relaciones existentes.");
                continue;
            }

            Materia::where('programa_id', $programa->id)->delete();

            $prefijoClave = $plan['prefijo'];
            $totalCreditos = 0;

            foreach ($plan['cuatrimestres'] as $numCuatri => $materias) {
                $cuatrimestre = $numCuatri + 1;
                foreach ($materias as $i => $nombre) {
                    $secuencia = $i + 1;
                    Materia::create([
                        'clave'        => sprintf('%s-%d%02d', $prefijoClave, $cuatrimestre, $secuencia),
                        'nombre'       => $nombre,
                        'creditos'     => $creditosPorMateria,
                        'cuatrimestre' => $cuatrimestre,
                        'programa_id'  => $programa->id,
                        'activo'       => true,
                    ]);
                    $totalCreditos += $creditosPorMateria;
                }
            }

            $programa->update([
                'duracion_cuatrimestres' => $plan['duracion'],
                'total_creditos'         => $totalCreditos,
            ]);

            $this->command?->line("  {$programa->nombre}: " . array_sum(array_map('count', $plan['cuatrimestres'])) . ' materias, ' . $plan['duracion'] . ' cuatrimestres, ' . $totalCreditos . ' créditos.');
        }
    }
}
