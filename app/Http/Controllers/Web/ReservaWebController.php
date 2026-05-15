<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EstadoAsiento;
use App\Services\ReservaService;
use Illuminate\Http\Request;

class ReservaWebController extends Controller
{
    public function index(Request $request)
    {
        $reservas = $request->user()
            ->reservas()
            ->where('estado', 'bloqueado')
            ->where('reservado_hasta', '>', now())
            ->with(['evento', 'asiento.sector'])
            ->get();

        return view('web.carrito.index', compact('reservas'));
    }

    public function store(Request $request, ReservaService $service)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'asiento_id' => 'required|exists:asientos,id',
        ]);

        try {
            $service->reservarAsiento(
                $request->evento_id,
                $request->asiento_id,
                auth()->id()
            );

            return redirect()->route('carrito.index')
                ->with('success', 'Asiento reservado por 15 minutos.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id, ReservaService $service)
    {
        try {
            $service->cancelarReserva($id, auth()->id());
            return redirect()->route('carrito.index')
                ->with('success', 'Reserva cancelada.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeApi(Request $request, ReservaService $service)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'asiento_id' => 'required|exists:asientos,id',
        ]);

        try {
            $service->reservarAsiento(
                $request->evento_id,
                $request->asiento_id,
                auth()->id()
            );

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}