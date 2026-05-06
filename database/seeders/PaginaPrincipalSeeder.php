<?php

namespace Database\Seeders;

use App\Models\ConfiguracionSitio;
use App\Models\OfertaPrograma;
use Illuminate\Database\Seeder;

class PaginaPrincipalSeeder extends Seeder
{
    public function run(): void
    {
        // Contacto
        $contacto = [
            'correo'  => 'contacto@uicm.edu.mx',
            'telefono' => '(55) 0000 0000',
            'horario'  => 'Lunes a viernes de 9:00 a 18:00 hrs.',
        ];
        foreach ($contacto as $clave => $valor) {
            ConfiguracionSitio::set($clave, $valor);
        }

        // Oferta educativa
        $programas = [
            [
                'nombre'      => 'Licenciatura en Psicopedagogía',
                'nivel'       => 'licenciatura',
                'descripcion' => 'Programa enfocado en el acompañamiento de procesos de aprendizaje, orientación educativa y el diseño de estrategias de intervención psicopedagógica.',
                'puntos_clave' => [
                    'Intervención educativa en distintos contextos.',
                    'Evaluación y seguimiento del desarrollo académico.',
                    'Diseño de programas de apoyo a estudiantes.',
                ],
                'orden' => 1,
            ],
            [
                'nombre'      => 'Licenciatura en Lengua Inglesa',
                'nivel'       => 'licenciatura',
                'descripcion' => 'Formación en el dominio del idioma inglés y en la enseñanza de la lengua, con fuerte énfasis en competencias comunicativas en inglés avanzado.',
                'puntos_clave' => [
                    'Competencia comunicativa en inglés avanzado.',
                    'Enfoques y técnicas para la enseñanza de idiomas.',
                    'Vinculación con distintas educativas y culturales.',
                ],
                'orden' => 2,
            ],
            [
                'nombre'      => 'Licenciatura en Administración',
                'nivel'       => 'licenciatura',
                'descripcion' => 'Programa dirigido a la gestión de organizaciones, recursos y procesos administrativos en distintos sectores productivos.',
                'puntos_clave' => [
                    'Planeación y dirección de proyectos organizacionales.',
                    'Gestión de recursos humanos, financieros y materiales.',
                    'Desarrollo de habilidades directivas y de liderazgo.',
                ],
                'orden' => 3,
            ],
            [
                'nombre'      => 'Licenciatura en Lengua y Literatura',
                'nivel'       => 'licenciatura',
                'descripcion' => 'Formación centrada en el análisis, producción y difusión de textos, así como en el estudio de la literatura y sus manifestaciones.',
                'puntos_clave' => [
                    'Análisis crítico de obras literarias.',
                    'Creación y corrección de textos académicos y literarios.',
                    'Participación en proyectos editoriales y culturales.',
                ],
                'orden' => 4,
            ],
            [
                'nombre'      => 'Maestría en Administración y Negocios',
                'nivel'       => 'maestria',
                'descripcion' => 'Enfocada en la dirección estratégica, la gestión de recursos y la toma de decisiones en organizaciones públicas y privadas.',
                'puntos_clave' => [
                    'Planeación y evaluación de proyectos de negocio.',
                    'Gestión financiera y análisis de indicadores.',
                    'Liderazgo y gestión del talento humano.',
                ],
                'orden' => 5,
            ],
            [
                'nombre'      => 'Maestría en Educación',
                'nivel'       => 'maestria',
                'descripcion' => 'Orientada al análisis, diseño y mejora de procesos educativos en distintos niveles y modalidades de enseñanza.',
                'puntos_clave' => [
                    'Diseño curricular y planeación didáctica.',
                    'Evaluación de programas y prácticas educativas.',
                    'Investigación aplicada al contexto escolar.',
                ],
                'orden' => 6,
            ],
            [
                'nombre'      => 'Maestría en Negocios y Logística Internacional',
                'nivel'       => 'maestria',
                'descripcion' => 'Programa que integra comercio exterior, logística y gestión de cadenas de suministro en entornos globalizados.',
                'puntos_clave' => [
                    'Operación de comercio internacional y aduanas.',
                    'Diseño y optimización de cadenas logísticas.',
                    'Negociación y gestión de acuerdos comerciales.',
                ],
                'orden' => 7,
            ],
            [
                'nombre'      => 'Doctorado en Educación',
                'nivel'       => 'doctorado',
                'descripcion' => 'Programa de formación avanzada para profesionales interesados en la investigación educativa, el análisis de políticas y la innovación en procesos de enseñanza-aprendizaje.',
                'puntos_clave' => [
                    'Investigación en procesos de enseñanza y aprendizaje.',
                    'Evaluación y mejora de instituciones y sistemas educativos.',
                    'Diseño de propuestas innovadoras para la formación docente.',
                    'Análisis de políticas públicas en el ámbito educativo.',
                ],
                'orden' => 8,
            ],
        ];

        foreach ($programas as $data) {
            OfertaPrograma::updateOrCreate(
                ['nombre' => $data['nombre'], 'nivel' => $data['nivel']],
                $data
            );
        }
    }
}
