@extends('layouts.app')

@section('title', 'Compra confirmada - Nexora Arena')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            {{-- Éxito --}}
            <div class="text-center mb-5">
                <div style="font-size: 4rem;" class="mb-3">🎉</div>
                <span class="badge-gold mb-3 d-inline-block">Compra completada</span>
                <h1 style="font-size: 2rem; font-weight: 700;">¡Entradas confirmadas!</h1>
                <p class="text-muted-custom">Guarda tus códigos QR para acceder al evento</p>
            </div>

            {{-- Entradas --}}
            @forelse($entradas as $entrada)
            <div class="card-dark p-4 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h3 style="font-size: 1rem; font-weight: 600;" class="mb-1">
                            {{ $entrada->evento->nombre }}
                        </h3>
                        <div class="text-muted-custom small mb-2">
                            <i class="bi bi-calendar me-1"></i>{{ $entrada->evento->fecha->format('d/m/Y') }}
                            @if($entrada->evento->hora)
                                · <i class="bi bi-clock me-1"></i>{{ $entrada->evento->hora->format('H:i') }}
                            @endif
                        </div>
                        <span class="badge-purple">
                            {{ $entrada->asiento->sector->nombre }} · Fila {{ $entrada->asiento->fila }} · Nº {{ $entrada->asiento->numero }}
                        </span>
                        <div class="mt-3">
                            <span class="text-muted-custom small">Precio pagado: </span>
                            <span class="text-orange" style="font-weight: 600;">
                                {{ number_format($entrada->precio_pagado, 2, ',', '.') }} €
                            </span>
                        </div>
                    </div>
                    <div class="col-md-5 text-center mt-3 mt-md-0">
                        <div style="background: #ffffff; padding: 12px; border-radius: 12px; display: inline-block;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $entrada->codigo_qr }}"
                                 alt="QR" style="width: 120px; height: 120px;">
                        </div>
                        <div class="text-muted-custom small mt-2" style="font-size: 0.7rem;">
                            {{ $entrada->codigo_qr }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-3">
                <p class="text-muted-custom">No se encontraron entradas.</p>
            </div>
            @endforelse

            {{-- Acciones --}}
            <div class="d-flex gap-3 justify-content-center mt-5">
                <a href="{{ route('entradas.index') }}" class="btn-orange">
                    <i class="bi bi-ticket-fill me-2"></i>Ver mis entradas
                </a>
                <a href="{{ route('eventos.index') }}" class="btn-outline-custom">
                    Ver más eventos
                </a>
            </div>

        </div>
    </div>
</div>

@endsection