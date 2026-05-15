<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\Request;
use  App\Http\Resources\EntradaResource;

class EntradaController extends Controller
{
    /* Listar mis entradas */
    public function index(Request $request)
    {
        $entradas = $request->user()
            ->entradas()
            ->with(['evento', 'asiento.sector'])
            ->latest()
            ->get();
    
        return EntradaResource::collection($entradas);
    }
    
    public function show($id)
    {
        $entrada = Entrada::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['evento', 'asiento.sector', 'user'])
            ->firstOrFail();
    
        return new EntradaResource($entrada);
    }
}