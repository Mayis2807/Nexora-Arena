@extends('layouts.app')

@section('title', 'Eventos - Admin')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <a href="{{ route('admin.index') }}" class="text-muted-custom small mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Volver al panel
            </a>
            <h1 style="font-size: 2rem; font-weight: 700;">Gestión de eventos</h1>
        </div>
        <a href="{{ route('admin.eventos.crear') }}" class="btn-orange">
            <i class="bi bi-plus-lg me-2"></i>Nuevo evento
        </a>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div style="background: #00ff0020; border: 1px solid #00ff0040; border-radius: 8px; padding: 12px 16px;" class="mb-4">
        <p class="mb-0 small" style="color: #00cc66;">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        </p>
    </div>
    @endif

    @if(session('error'))
    <div style="background: #ff000020; border: 1px solid #ff000040; border-radius: 8px; padding: 12px 16px;" class="mb-4">
        <p class="mb-0 small" style="color: #ff6b6b;">
            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
        </p>
    </div>
    @endif

    {{-- Lista eventos --}}
    @forelse($eventos as $evento)
    <div class="card-dark p-4 mb-3">
        <div class="row align-items-center">
            <div class="col-md-5 mb-3 mb-md-0">
                <h3 style="font-size: 1rem; font-weight: 600;" class="mb-1">{{ $evento->nombre }}</h3>
                <div class="text-muted-custom small mb-2">{{ $evento->descripcion_corta }}</div>
                <span class="badge-orange">{{ $evento->fecha->format('d/m/Y') }}
                    @if($evento->hora) · {{ $evento->hora->format('H:i') }} @endif
                </span>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Entradas vendidas</div>
                <div style="font-size: 1.3rem; font-weight: 700; color: #00cc66;">
                    {{ $evento->entradas_count }}
                </div>
            </div>
            <div class="col-md-4 text-md-end d-flex gap-2 justify-content-md-end">
                <a href="{{ route('admin.eventos.editar', $evento->id) }}" 
                   class="btn-primary-custom" style="font-size: 0.85rem; padding: 6px 14px;">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <form action="{{ route('admin.eventos.destroy', $evento->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este evento?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            style="background: #ff000020; border: 1px solid #ff000040; color: #ff6b6b; padding: 6px 14px; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <p class="text-muted-custom">No hay eventos creados</p>
        <a href="{{ route('admin.eventos.crear') }}" class="btn-orange mt-3">
            <i class="bi bi-plus-lg me-2"></i>Crear primer evento
        </a>
    </div>
    @endforelse

</div>

@endsection