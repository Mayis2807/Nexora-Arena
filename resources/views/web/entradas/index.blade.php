@extends('layouts.app')

@section('title', 'Mis Entradas - Roig Arena')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <span class="badge-purple mb-2 d-inline-block">Mi cuenta</span>
        <h1 style="font-size: 2.5rem; font-weight: 700;">Mis entradas</h1>
        <p class="text-muted-custom">Gestiona todas tus entradas compradas</p>
    </div>

    {{-- Lista entradas --}}
    @forelse($entradas as $entrada)
    <div class="card-dark p-4 mb-3">
        <div class="row align-items-center">

            {{-- Info evento --}}
            <div class="col-md-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 50px; height: 50px; background: #9D50FF20; border: 1px solid #9D50FF40; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        🎭
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600;" class="mb-1">
                            {{ $entrada->evento->nombre }}
                        </h3>
                        <div class="text-muted-custom small">
                            <i class="bi bi-calendar me-1"></i>{{ $entrada->evento->fecha->format('d/m/Y') }}
                            @if($entrada->evento->hora)
                                · <i class="bi bi-clock me-1"></i>{{ $entrada->evento->hora->format('H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Asiento --}}
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Asiento</div>
                <span class="badge-purple">
                    {{ $entrada->asiento->sector->nombre }} · Fila {{ $entrada->asiento->fila }} · Nº {{ $entrada->asiento->numero }}
                </span>
            </div>

            {{-- Precio --}}
            <div class="col-md-2 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Precio pagado</div>
                <div class="text-orange" style="font-weight: 600;">
                    {{ number_format($entrada->precio_pagado, 2, ',', '.') }} €
                </div>
            </div>

            {{-- Estado y acción --}}
            <div class="col-md-2 text-md-end">
                @if($entrada->esValida())
                    <span class="badge-gold mb-2 d-inline-block">
                        <i class="bi bi-check-circle me-1"></i>Válida
                    </span>
                @else
                    <span style="background: #ff000020; color: #ff6b6b; border: 1px solid #ff000040; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem;" class="mb-2 d-inline-block">
                        <i class="bi bi-x-circle me-1"></i>Expirada
                    </span>
                @endif
                <br>
                <a href="{{ route('entradas.show', $entrada->id) }}" class="btn-primary-custom mt-2" style="font-size: 0.8rem; padding: 5px 14px;">
                    Ver QR
                </a>
            </div>

        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size: 4rem;" class="mb-3">🎫</div>
        <h3 style="color: var(--text-muted-custom);">No tienes entradas todavía</h3>
        <p class="text-muted-custom small mb-4">Compra tus primeras entradas y aparecerán aquí</p>
        <a href="{{ route('eventos.index') }}" class="btn-orange">
            <i class="bi bi-ticket-fill me-2"></i>Ver eventos
        </a>
    </div>
    @endforelse

</div>

@endsection