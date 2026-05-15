<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use Illuminate\Http\Request;

class EntradaWebController extends Controller
{
    public function index(Request $request)
    {
        $entradas = $request->user()
            ->entradas()
            ->with(['evento', 'asiento.sector'])
            ->latest()
            ->get();

        return view('web.entradas.index', compact('entradas'));
    }

    public function show(Request $request, $id)
    {
        $entrada = Entrada::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['evento', 'asiento.sector'])
            ->firstOrFail();

        return view('web.entradas.show', compact('entrada'));
    }
}