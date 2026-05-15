@extends('layouts.app')

@section('title', 'Sectores - Admin')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <a href="{{ route('admin.index') }}" class="text-muted-custom small mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Volver al panel
            </a>
            <h1 style="font-size: 2rem; font-weight: 700;">Gestión de sectores</h1>
        </div>
    </div>

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

    <div class="row g-3">
        @foreach($sectores as $sector)
        <div class="col-md-4">
            <div class="card-dark p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h3 style="font-size: 0.95rem; font-weight: 600;" class="mb-0">
                        {{ $sector->nombre }}
                    </h3>
                    @if($sector->activo)
                        <span style="background: #00cc6620; color: #00cc66; border: 1px solid #00cc6640; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;">
                            Activo
                        </span>
                    @else
                        <span style="background: #ff000020; color: #ff6b6b; border: 1px solid #ff000040; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem;">
                            Inactivo
                        </span>
                    @endif
                </div>
                <div class="text-muted-custom small mb-3">
                    <i class="bi bi-grid me-1"></i>{{ $sector->asientos_count }} asientos
                </div>
                <form action="{{ route('admin.sectores.update', $sector->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="nombre" value="{{ $sector->nombre }}">
                    <input type="hidden" name="activo" value="{{ $sector->activo ? 0 : 1 }}">
                    <button type="submit"
                        style="width: 100%; background: {{ $sector->activo ? '#ff000020' : '#00cc6620' }}; border: 1px solid {{ $sector->activo ? '#ff000040' : '#00cc6640' }}; color: {{ $sector->activo ? '#ff6b6b' : '#00cc66' }}; padding: 6px; border-radius: 8px; cursor: pointer; font-size: 0.8rem;">
                        {{ $sector->activo ? 'Desactivar sector' : 'Activar sector' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection