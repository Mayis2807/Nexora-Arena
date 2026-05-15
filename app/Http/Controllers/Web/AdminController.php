<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\User;
use App\Models\Entrada;
use App\Models\EstadoAsiento;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'eventos' => Evento::count(),
            'sectores' => Sector::count(),
            'usuarios' => User::count(),
            'entradas' => Entrada::count(),
            'reservas' => EstadoAsiento::where('estado', 'bloqueado')->count(),
            'ingresos' => Entrada::sum('precio_pagado'),
        ];

        $eventosRecientes = Evento::futuros()->take(5)->get();
        $entradasRecientes = Entrada::with(['user', 'evento'])->latest()->take(5)->get();

        return view('web.admin.index', compact('stats', 'eventosRecientes', 'entradasRecientes'));
    }

    public function eventos()
    {
        $eventos = Evento::withCount('entradas')->orderBy('fecha')->get();
        return view('web.admin.eventos', compact('eventos'));
    }

    public function crearEvento()
    {
        return view('web.admin.eventos-crear');
    }

    public function storeEvento(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion_corta' => 'required|string|max:255',
            'descripcion_larga' => 'required|string',
            'fecha' => 'required|date|unique:eventos,fecha',
            'hora' => 'required',
            'poster_url' => 'nullable|string',
        ]);

        Evento::create($request->all());

        return redirect()->route('admin.eventos')
            ->with('success', 'Evento creado correctamente.');
    }

    public function editarEvento($id)
    {
        $evento = Evento::findOrFail($id);
        return view('web.admin.eventos-editar', compact('evento'));
    }

    public function updateEvento(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion_corta' => 'required|string|max:255',
            'descripcion_larga' => 'required|string',
            'fecha' => 'required|date|unique:eventos,fecha,' . $id,
            'hora' => 'required',
            'poster_url' => 'nullable|string',
        ]);

        $evento->update($request->all());

        return redirect()->route('admin.eventos')
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroyEvento($id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->entradas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un evento con entradas vendidas.');
        }

        $evento->delete();
        return redirect()->route('admin.eventos')
            ->with('success', 'Evento eliminado correctamente.');
    }

    public function sectores()
    {
        $sectores = Sector::withCount('asientos')->get();
        return view('web.admin.sectores', compact('sectores'));
    }

    public function storeSector(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:sectores',
            'descripcion' => 'nullable|string',
        ]);

        Sector::create($request->all());

        return redirect()->route('admin.sectores')
            ->with('success', 'Sector creado correctamente.');
    }

    public function updateSector(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:sectores,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $sector->update($request->all());

        return redirect()->route('admin.sectores')
            ->with('success', 'Sector actualizado correctamente.');
    }

    public function destroySector($id)
    {
        $sector = Sector::findOrFail($id);

        if ($sector->totalAsientos() > 0) {
            return back()->with('error', 'No se puede eliminar un sector con asientos.');
        }

        $sector->delete();
        return redirect()->route('admin.sectores')
            ->with('success', 'Sector eliminado correctamente.');
    }

    public function usuarios()
    {
        $usuarios = User::withCount('entradas')->latest()->get();
        return view('web.admin.usuarios', compact('usuarios'));
    }
}