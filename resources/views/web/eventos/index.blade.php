@extends('layouts.app')

@section('title', 'Eventos - Nexora Arena')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <span class="badge-purple mb-2 d-inline-block">Cartelera</span>
        <h1 style="font-size: 2.5rem; font-weight: 700;">Próximos eventos</h1>
        <p class="text-muted-custom">Elige tu evento y selecciona el mejor asiento</p>
    </div>

    {{-- Lista eventos --}}
    <div class="row g-4">
        @forelse($eventos as $evento)
        <div class="col-12">
            <div class="card-dark p-4">
                <div class="row align-items-center">

                    {{-- Fecha --}}
                    <div class="col-md-2 text-center mb-3 mb-md-0">
                        <div style="background: #8B6BF520; border: 1px solid #8B6BF540; border-radius: 12px; padding: 16px;">
                            <div class="text-purple" style="font-size: 2rem; font-weight: 700; line-height: 1;">
                                {{ $evento->fecha->format('d') }}
                            </div>
                            <div class="text-muted-custom small text-uppercase">
                                {{ $evento->fecha->format('M Y') }}
                            </div>
                            @if($evento->hora)
                            <div class="text-muted-custom" style="font-size: 0.75rem; margin-top: 4px;">
                                <i class="bi bi-clock me-1"></i>{{ $evento->hora->format('H:i') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h2 style="font-size: 1.3rem; font-weight: 600; color: #fff;" class="mb-2">
                            {{ $evento->nombre }}
                        </h2>
                        <p class="text-muted-custom small mb-3">
                            {{ $evento->descripcion_corta }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($evento->precios->take(3) as $precio)
                            <span class="badge-purple">
                                {{ $precio->sector->nombre }}: {{ number_format($precio->precio, 2, ',', '.') }} €
                            </span>
                            @endforeach
                            @if($evento->precios->count() > 3)
                            <span class="text-muted-custom small">+{{ $evento->precios->count() - 3 }} sectores más</span>
                            @endif
                        </div>
                    </div>

                    {{-- Precio y CTA --}}
                    <div class="col-md-4 text-md-end">
                        <div class="mb-3">
                            <span class="text-muted-custom small">Precio desde</span>
                            <div class="text-orange" style="font-size: 1.8rem; font-weight: 700;">
                                {{ number_format($evento->precios->min('precio'), 2, ',', '.') }} €
                            </div>
                        </div>
                        <a href="{{ route('eventos.show', $evento->id) }}" class="btn-orange">
                            <i class="bi bi-ticket-fill me-2"></i>Comprar entradas
                        </a>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div style="font-size: 4rem;" class="mb-3">🎭</div>
            <h3 class="text-muted-custom">No hay eventos próximos</h3>
            <p class="text-muted-custom small">Vuelve pronto para ver nuevos eventos</p>
        </div>
        @endforelse
    </div>

</div>

@endsection