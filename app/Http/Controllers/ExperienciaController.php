<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Experiencia;
use Illuminate\Http\Request;

class ExperienciaController extends Controller
{
    public function index()
    {
        $yaRespondio = Experiencia::where('user_id', auth()->id())->exists();
        return view('web.experiencia.index', compact('yaRespondio'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'valoracion_web' => 'required|integer|min:1|max:5',
            'secciones_visitadas' => 'required|array|min:1',
            'eventos_interes' => 'required|array|min:1',
            'como_nos_encontraste' => 'required|string',
            'recomendaria' => 'required|boolean',
            'mejoras' => 'required|array|min:1',
            'comentario' => 'nullable|string|max:1000',
            'volveria_comprar' => 'required|boolean',
        ]);

        // Verificar que no haya respondido antes
        if (Experiencia::where('user_id', auth()->id())->exists()) {
            return redirect()->route('experiencia.index')
                ->with('error', 'Ya has enviado tu experiencia anteriormente.');
        }

        Experiencia::create([
            'user_id' => auth()->id(),
            'valoracion_web' => $request->valoracion_web,
            'secciones_visitadas' => $request->secciones_visitadas,
            'eventos_interes' => $request->eventos_interes,
            'como_nos_encontraste' => $request->como_nos_encontraste,
            'recomendaria' => $request->recomendaria,
            'mejoras' => $request->mejoras,
            'comentario' => $request->comentario,
            'volveria_comprar' => $request->volveria_comprar,
        ]);

        return redirect()->route('experiencia.index')
            ->with('success', '¡Gracias por compartir tu experiencia!');
    }
}