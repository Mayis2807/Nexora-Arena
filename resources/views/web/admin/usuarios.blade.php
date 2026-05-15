@extends('layouts.app')

@section('title', 'Usuarios - Admin')

@section('content')

<div class="container py-5">

    <div class="mb-5">
        <a href="{{ route('admin.index') }}" class="text-muted-custom small mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Volver al panel
        </a>
        <h1 style="font-size: 2rem; font-weight: 700;">Gestión de usuarios</h1>
        <p class="text-muted-custom">{{ $usuarios->count() }} usuarios registrados</p>
    </div>

    @foreach($usuarios as $usuario)
    <div class="card-dark p-4 mb-3">
        <div class="row align-items-center">
            <div class="col-md-1 mb-3 mb-md-0">
                <div style="width: 44px; height: 44px; background: #9D50FF20; border: 1px solid #9D50FF40; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--color-purple);">
                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div style="font-weight: 600;">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                <div class="text-muted-custom small">{{ $usuario->email }}</div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Registrado</div>
                <div class="small">{{ $usuario->created_at->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-2 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Entradas</div>
                <div class="text-orange" style="font-weight: 600;">{{ $usuario->entradas_count }}</div>
            </div>
            <div class="col-md-2 text-md-end">
                @if($usuario->is_admin)
                    <span class="badge-purple">Admin</span>
                @else
                    <span class="badge-orange">Usuario</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach

</div>

@endsection