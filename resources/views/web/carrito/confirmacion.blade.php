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
            @foreach($entradas as $entrada)
            <div class="card-dark p-4 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h3 style="font-size: 1rem; font-weight: 600;" class="mb-1">
                            {{ $entrada->evento->nombre }}
                        </h3>
                        <div class="text-muted-custom small mb-2">
                            <i class="bi bi-calendar me-1"></i>{{ $entrada->evento->fecha->format('d/m/Y') }}
                        </div>
                        <span class="badge-purple">
                            {{ $entrada->asiento->sector->nombre }} · Fila {{ $entrada->asiento->fila }} · Nº {{ $entrada->asiento->numero }}
                        </span>
                    </div>
                    <div class="col-md-5 text-center mt-3 mt-md-0">
                        <div style="background: #ffffff; padding: 8px; border-radius: 8px; display: inline-block;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $entrada->codigo_qr }}"
                                 alt="QR" style="width: 100px; height: 100px;">
                        </div>
                        <div class="text-muted-custom small mt-1">{{ $entrada->codigo_qr }}</div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Acciones --}}
            <div class="d-flex gap-3 justify-content-center mt-4">
                <a href="{{ route('entradas.index') }}" class="btn-primary-custom">
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