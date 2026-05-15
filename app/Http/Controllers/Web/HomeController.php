<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evento;

class HomeController extends Controller
{
    public function index()
    {
        $eventos = Evento::futuros()
            ->with(['precios.sector'])
            ->take(3)
            ->get();

        return view('web.home', compact('eventos'));
    }
}