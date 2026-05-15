<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evento;

class EventoWebController extends Controller
{
    public function index()
    {
        $eventos = Evento::futuros()
            ->with(['precios.sector'])
            ->get();

        return view('web.eventos.index', compact('eventos'));
    }

    public function show($id)
    {
        $evento = Evento::with(['precios.sector'])
            ->findOrFail($id);
    
        $sectoresDisponibles = $evento->sectoresDisponibles();
    
        // Calcular disponibilidad por sector
        $disponibilidadSectores = [];
        foreach ($sectoresDisponibles as $sector) {
            $totalAsientos = $sector->asientos()->count();
            $asientosOcupados = \App\Models\EstadoAsiento::where('evento_id', $id)
                ->whereHas('asiento', fn($q) => $q->where('sector_id', $sector->id))
                ->count();
            
            $disponibilidadSectores[$sector->id] = [
                'total' => $totalAsientos,
                'ocupados' => $asientosOcupados,
                'disponibles' => $totalAsientos - $asientosOcupados,
                'porcentaje' => $totalAsientos > 0 
                    ? round((($totalAsientos - $asientosOcupados) / $totalAsientos) * 100) 
                    : 0,
            ];
        }
    
        return view('web.eventos.show', compact('evento', 'sectoresDisponibles', 'disponibilidadSectores'));
    }
}