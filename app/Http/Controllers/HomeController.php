<?php

namespace App\Http\Controllers;

use App\Models\Periodo;

class HomeController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('estado', 'activo')->first();
        return view('home.index', compact('periodoActivo'));
    }
}
