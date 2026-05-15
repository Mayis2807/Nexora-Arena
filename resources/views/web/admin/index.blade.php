@extends('layouts.app')

@section('title', 'Panel Admin - Nexora Arena')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <span class="badge-orange mb-2 d-inline-block">Panel de control</span>
        <h1 style="font-size: 2.5rem; font-weight: 700;">Dashboard</h1>
        <p class="text-muted-custom">Resumen general del sistema</p>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div class="text-purple" style="font-size: 1.8rem; font-weight: 700;">{{ $stats['eventos'] }}</div>
                <div class="text-muted-custom small">Eventos</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div class="text-orange" style="font-size: 1.8rem; font-weight: 700;">{{ $stats['sectores'] }}</div>
                <div class="text-muted-custom small">Sectores</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div class="text-gold" style="font-size: 1.8rem; font-weight: 700;">{{ $stats['usuarios'] }}</div>
                <div class="text-muted-custom small">Usuarios</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div style="color: #00cc66; font-size: 1.8rem; font-weight: 700;">{{ $stats['entradas'] }}</div>
                <div class="text-muted-custom small">Entradas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div style="color: #ff6b6b; font-size: 1.8rem; font-weight: 700;">{{ $stats['reservas'] }}</div>
                <div class="text-muted-custom small">Reservas activas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card-dark p-3 text-center">
                <div class="text-orange" style="font-size: 1.4rem; font-weight: 700;">{{ number_format($stats['ingresos'], 2, ',', '.') }} €</div>
                <div class="text-muted-custom small">Ingresos</div>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <a href="{{ route('admin.eventos') }}" class="card-dark p-4 d-block text-decoration-none">
                <i class="bi bi-calendar-event text-purple" style="font-size: 2rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 600;" class="mt-3 mb-1">Gestionar eventos</h3>
                <p class="text-muted-custom small mb-0">Crear, editar y eliminar eventos</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.sectores') }}" class="card-dark p-4 d-block text-decoration-none">
                <i class="bi bi-grid text-orange" style="font-size: 2rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 600;" class="mt-3 mb-1">Gestionar sectores</h3>
                <p class="text-muted-custom small mb-0">Activar y desactivar sectores</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.usuarios') }}" class="card-dark p-4 d-block text-decoration-none">
                <i class="bi bi-people text-gold" style="font-size: 2rem;"></i>
                <h3 style="font-size: 1rem; font-weight: 600;" class="mt-3 mb-1">Gestionar usuarios</h3>
                <p class="text-muted-custom small mb-0">Ver todos los usuarios registrados</p>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Próximos eventos --}}
        <div class="col-md-6">
            <div class="card-dark p-4">
                <h2 style="font-size: 1.1rem; font-weight: 600;" class="mb-3">
                    <i class="bi bi-calendar me-2 text-purple"></i>Próximos eventos
                </h2>
                @forelse($eventosRecientes as $evento)
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #ffffff10;">
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 500;">{{ $evento->nombre }}</div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;">{{ $evento->fecha->format('d/m/Y') }}</div>
                    </div>
                    <a href="{{ route('admin.eventos') }}" class="btn-primary-custom" style="font-size: 0.75rem; padding: 4px 10px;">
                        Ver
                    </a>
                </div>
                @empty
                <p class="text-muted-custom small">No hay eventos próximos</p>
                @endforelse
            </div>
        </div>

        {{-- Últimas entradas --}}
        <div class="col-md-6">
            <div class="card-dark p-4">
                <h2 style="font-size: 1.1rem; font-weight: 600;" class="mb-3">
                    <i class="bi bi-ticket me-2 text-orange"></i>Últimas entradas
                </h2>
                @forelse($entradasRecientes as $entrada)
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #ffffff10;">
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 500;">{{ $entrada->user->nombre }} {{ $entrada->user->apellido }}</div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;">{{ $entrada->evento->nombre }}</div>
                    </div>
                    <span class="text-orange" style="font-size: 0.85rem; font-weight: 600;">
                        {{ number_format($entrada->precio_pagado, 2, ',', '.') }} €
                    </span>
                </div>
                @empty
                <p class="text-muted-custom small">No hay entradas vendidas</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection