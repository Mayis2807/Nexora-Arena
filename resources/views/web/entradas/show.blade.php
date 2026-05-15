@extends('layouts.app')

@section('title', 'Entrada - Roig Arena')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <a href="{{ route('entradas.index') }}" class="text-muted-custom small mb-4 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Volver a mis entradas
            </a>

            <div class="card-dark p-4">

                {{-- Header entrada --}}
                <div class="text-center mb-4 pb-4" style="border-bottom: 1px solid #ffffff10;">
                    <span class="badge-orange mb-2 d-inline-block">Entrada oficial</span>
                    <h2 style="font-size: 1.5rem; font-weight: 700;">{{ $entrada->evento->nombre }}</h2>
                    <p class="text-muted-custom small">
                        <i class="bi bi-calendar me-1"></i>{{ $entrada->evento->fecha->format('d/m/Y') }}
                        @if($entrada->evento->hora)
                            · <i class="bi bi-clock me-1"></i>{{ $entrada->evento->hora->format('H:i') }}
                        @endif
                    </p>
                </div>

                {{-- QR Code --}}
                <div class="text-center mb-4">
                    <div style="background: #ffffff; padding: 16px; border-radius: 12px; display: inline-block;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ $entrada->codigo_qr }}"
                             alt="QR Code" style="width: 180px; height: 180px;">
                    </div>
                    <p class="text-muted-custom small mt-2">{{ $entrada->codigo_qr }}</p>
                </div>

                {{-- Detalles --}}
                <div style="border-top: 1px solid #ffffff10; padding-top: 1rem;">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted-custom small mb-1">Sector</div>
                            <div style="font-weight: 600;">{{ $entrada->asiento->sector->nombre }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted-custom small mb-1">Asiento</div>
                            <div style="font-weight: 600;">Fila {{ $entrada->asiento->fila }} · Nº {{ $entrada->asiento->numero }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted-custom small mb-1">Titular</div>
                            <div style="font-weight: 600;">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted-custom small mb-1">Precio pagado</div>
                            <div class="text-orange" style="font-weight: 600;">{{ number_format($entrada->precio_pagado, 2, ',', '.') }} €</div>
                        </div>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="text-center mt-4">
                    @if($entrada->esValida())
                        <span class="badge-gold" style="font-size: 0.85rem; padding: 8px 20px;">
                            <i class="bi bi-check-circle-fill me-1"></i>Entrada válida
                        </span>
                    @else
                        <span style="background: #ff000020; color: #ff6b6b; border: 1px solid #ff000040; padding: 8px 20px; border-radius: 20px; font-size: 0.85rem;">
                            <i class="bi bi-x-circle-fill me-1"></i>Entrada expirada
                        </span>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@endsection