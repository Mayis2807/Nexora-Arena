<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EstadoAsiento;
use App\Services\CompraService;
use Illuminate\Http\Request;

class CompraWebController extends Controller
{
    public function store(Request $request, CompraService $service)
    {
        $request->validate([
            'reservas' => 'required|array|min:1',
            'reservas.*' => 'exists:estado_asientos,id',
        ]);

        try {
            $entradas = $service->procesarCompra(
                $request->reservas,
                auth()->id()
            );

            return redirect()->route('compra.confirmacion')
                ->with('entradas', $entradas->pluck('id')->toArray());

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmacion(Request $request)
    {
        $entradasIds = session('entradas', []);
        
        $entradas = \App\Models\Entrada::whereIn('id', $entradasIds)
            ->with(['evento', 'asiento.sector'])
            ->get();

        return view('web.compra.confirmacion', compact('entradas'));
    }
}