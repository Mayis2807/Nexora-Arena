@extends('layouts.app')

@section('title', 'Nexora Arena - Inicio')

@section('content')

{{-- HERO --}}
<section style="background: linear-gradient(135deg, #0D0D1A 0%, #1E1040 50%, #0D0D1A 100%); min-height: 85vh; display: flex; align-items: center; position: relative; overflow: hidden;">
    
    {{-- Círculos decorativos --}}
    <div style="position: absolute; top: -100px; right: -100px; width: 500px; height: 500px; background: radial-gradient(circle, #8B6BF520 0%, transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -50px; left: -50px; width: 300px; height: 300px; background: radial-gradient(circle, #FF7B0015 0%, transparent 70%); pointer-events: none;"></div>

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge-orange mb-3 d-inline-block">
                    <i class="bi bi-lightning-fill me-1"></i> Valencia, España
                </span>
                <h1 style="font-size: 3.5rem; font-weight: 700; line-height: 1.1; color: #b6b6ec;" class="mb-4">
                    Tu lugar de coneXión<br>
                    <span class="text-purple">en directo</span>
                </h1>
                <p class="text-muted-custom mb-4" style="font-size: 1.1rem; max-width: 480px; line-height: 1.7;">
                    Conciertos, deportes y espectáculos únicos en el corazón de Valencia. 
                    Elige tu asiento y vive la experiencia.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('eventos.index') }}" class="btn-orange">
                        <i class="bi bi-ticket-fill me-2"></i>Ver eventos
                    </a>
                    <a href="#eventos" class="btn-outline-custom">
                        Próximos eventos
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div style="width: 320px; height: 320px; background: #1A1A28; border-radius: 50%; border: 2px solid #8B6BF530; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="imagenes/nexora-arena1.png" alt="Nexora Arena" style="width: 100%; height: 100%; object-fit: cover; ">
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row mt-5 pt-4" style="border-top: 1px solid #ffffff10;">
            <div class="col-6 col-md-3 mb-3">
                <div class="text-purple" style="font-size: 2rem; font-weight: 700;">14.896</div>
                <div class="text-muted-custom small">Asientos disponibles</div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="text-orange" style="font-size: 2rem; font-weight: 700;">71</div>
                <div class="text-muted-custom small">Sectores</div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="text-gold" style="font-size: 2rem; font-weight: 700;">{{ $eventos->count() }}</div>
                <div class="text-muted-custom small">Próximos eventos</div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div style="font-size: 2rem; font-weight: 700; color: #fff;">100%</div>
                <div class="text-muted-custom small">Entradas seguras</div>
            </div>
        </div>
    </div>
</section>

{{-- EVENTOS --}}
<section id="eventos" class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge-purple mb-2 d-inline-block">Próximamente</span>
                <h2 style="font-size: 1.8rem; font-weight: 700;">Eventos destacados</h2>
            </div>
            <a href="{{ route('eventos.index') }}" class="btn-outline-custom">
                Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($eventos as $evento)
            <div class="col-md-4">
                <div class="card-dark h-100">
                    {{-- Imagen/Banner --}}
                    <div style="height: 180px; background: linear-gradient(135deg, #1A1228, #12121A); display: flex; align-items: center; justify-content: center; font-size: 4rem; position: relative;">
                        @if($evento->poster_url)
                            <img src="{{ asset($evento->poster_url) }}" 
                                 alt="{{ $evento->nombre }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="font-size: 4rem;">🎭</div>
                        @endif
                        <div style="position: absolute; top: 12px; left: 12px;">
                            <span class="badge-orange">
                                {{ $evento->fecha->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    {{-- Contenido --}}
                    <div class="p-4">
                        <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-2">
                            {{ $evento->nombre }}
                        </h3>
                        <p class="text-muted-custom small mb-3">
                            {{ $evento->descripcion_corta }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted-custom small">Desde</span>
                                <span class="text-purple ms-1" style="font-size: 1.1rem; font-weight: 600;">
                                    {{ number_format($evento->precios->min('precio'), 2, ',', '.') }} €
                                </span>
                            </div>
                            <a href="{{ route('eventos.show', $evento->id) }}" class="btn-primary-custom" style="padding: 6px 16px; font-size: 0.85rem;">
                                Comprar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted-custom">No hay eventos próximos disponibles.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- SALA INFINITO --}}
<section class="py-5" style="background: var(--bg-primary); overflow: hidden;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div style="position: relative; border-radius: 20px; overflow: hidden;">
                    <img src="{{ asset('imagenes/inf.png') }}" 
                         alt="Sala Infinito" 
                         style="width: 100%; height: 400px; object-fit: cover; border-radius: 20px;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, transparent 60%, var(--bg-primary)); border-radius: 20px;"></div>
                    <div style="position: absolute; top: 16px; left: 16px;">
                        <span style="background: #9D50FF; color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            ✦ NUEVO
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="badge-purple mb-3 d-inline-block">Nexora Arena</span>
                <h2 style="font-size: 2rem; font-weight: 700; line-height: 1.2; color: #b6b6ec;" class="mb-3">
                    Sala <span class="text-purple">Infinito</span>
                </h2>
                <p style="color: #B8C6E0; line-height: 1.8; font-size: 1rem;" class="mb-4">
                    Adéntrate en una experiencia sin límites. La nueva <strong style="color: #ffffff;">Sala Infinito</strong> de Nexora Arena 
                    te rodea de proyecciones inmersivas a 360° que te transportan al cosmos. 
                    Galaxias, nebulosas y constelaciones cobran vida a tu alrededor en una 
                    experiencia audiovisual única que desafía los límites de la percepción.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div style="background: #9D50FF15; border: 1px solid #9D50FF30; border-radius: 12px; padding: 12px 16px; text-align: center;">
                        <div class="text-purple" style="font-size: 1.3rem; font-weight: 700;">360°</div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;">Proyección</div>
                    </div>
                    <div style="background: #FF7B0015; border: 1px solid #FF7B0030; border-radius: 12px; padding: 12px 16px; text-align: center;">
                        <div class="text-orange" style="font-size: 1.3rem; font-weight: 700;">8K</div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;">Resolución</div>
                    </div>
                    <div style="background: #D4AF3715; border: 1px solid #D4AF3730; border-radius: 12px; padding: 12px 16px; text-align: center;">
                        <div class="text-gold" style="font-size: 1.3rem; font-weight: 700;">∞</div>
                        <div class="text-muted-custom" style="font-size: 0.75rem;">Experiencia</div>
                    </div>
                </div>
                <a href="{{ route('eventos.index') }}" class="btn-primary-custom" style="padding: 12px 28px; font-size: 0.95rem;">
                    <i class="bi bi-stars me-2"></i>Descubrir experiencias
                </a>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-5" style="background: linear-gradient(135deg, #1A1228 0%, #2D1B69 50%, #1A1228 100%);">
    <div class="container text-center py-4">
        <span class="badge-purple mb-3 d-inline-block">Únete ahora</span>
        <h2 style="font-size: 2.2rem; font-weight: 700; color: #ffffff;" class="mb-3">
            ¿Listo para vivir la experiencia?
        </h2>
        <p style="color: #B8C6E0;" class="mb-4">Regístrate y consigue tus entradas en segundos.</p>
        <a href="{{ route('register') }}" class="btn-orange" style="font-size: 1rem; padding: 12px 32px;">
            <i class="bi bi-person-plus-fill me-2"></i>Crear cuenta gratis
        </a>
    </div>
</section>

@endsection