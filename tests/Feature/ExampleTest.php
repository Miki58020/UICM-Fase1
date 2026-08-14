<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Comprobacion minima de que el sitio publico responde.
 *
 * La pagina principal consulta periodos, carrusel y configuracion del sitio, de
 * modo que sirve para detectar de forma temprana una ruptura en cualquiera de
 * esas consultas. Se ejecuta sobre una base vacia a proposito: verifica que la
 * portada se construye aunque todavia no haya contenido cargado.
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_principal_responde(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_la_oferta_educativa_responde(): void
    {
        $this->get('/oferta-educativa')->assertStatus(200);
    }
}
