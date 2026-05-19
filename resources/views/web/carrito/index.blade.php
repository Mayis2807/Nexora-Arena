@extends('layouts.app')

@section('title', 'Carrito - Nexora Arena')

@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <span class="badge-purple mb-2 d-inline-block">Mi carrito</span>
        <h1 style="font-size: 2.5rem; font-weight: 700;">Carrito de reservas</h1>
        <p class="text-muted-custom">Tienes 10 minutos para completar tu compra</p>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
    <div style="background: #00ff0020; border: 1px solid #00ff0040; border-radius: 8px; padding: 12px 16px;" class="mb-4">
        <p class="mb-0 small" style="color: #00ff88;">
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

    @forelse($reservas as $reserva)
    <div class="card-dark p-4 mb-3">
        <div class="row align-items-center">

            {{-- Info --}}
            <div class="col-md-5 mb-3 mb-md-0">
                <h3 style="font-size: 1rem; font-weight: 600;" class="mb-1">
                    {{ $reserva->evento->nombre }}
                </h3>
                <div class="text-muted-custom small">
                    <i class="bi bi-calendar me-1"></i>{{ $reserva->evento->fecha->format('d/m/Y') }}
                    @if($reserva->evento->hora)
                        · <i class="bi bi-clock me-1"></i>{{ $reserva->evento->hora->format('H:i') }}
                    @endif
                </div>
                <div class="mt-2">
                    <span class="badge-purple">
                        {{ $reserva->asiento->sector->nombre }} · Fila {{ $reserva->asiento->fila }} · Nº {{ $reserva->asiento->numero }}
                    </span>
                </div>
            </div>

            {{-- Precio --}}
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="text-muted-custom small mb-1">Precio</div>
                <div class="text-orange" style="font-weight: 600; font-size: 1.2rem;">
                    {{ number_format($reserva->evento->precioDelSector($reserva->asiento->sector_id)?->precio ?? 0, 2, ',', '.') }} €
                </div>
            </div>

            {{-- Temporizador --}}
            <div class="col-md-2 mb-3 mb-md-0 text-center">
                <div class="text-muted-custom small mb-1">Expira en</div>
                <div class="text-gold" style="font-weight: 600; font-size: 1.1rem;"
                     data-expira="{{ $reserva->reservado_hasta->toISOString() }}"
                     id="timer-{{ $reserva->id }}">
                    --:--
                </div>
            </div>

            {{-- Eliminar --}}
            <div class="col-md-2 text-md-end">
                <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #ff000020; border: 1px solid #ff000040; color: #ff6b6b; padding: 6px 14px; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>

        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size: 4rem;" class="mb-3">🛒</div>
        <h3 style="color: var(--text-muted-custom);">Tu carrito está vacío</h3>
        <p class="text-muted-custom small mb-4">Selecciona un evento y elige tus asientos</p>
        <a href="{{ route('eventos.index') }}" class="btn-orange">
            <i class="bi bi-ticket-fill me-2"></i>Ver eventos
        </a>
    </div>
    @endforelse

    {{-- Total y comprar --}}
    @if($reservas->count() > 0)
    <div class="card-dark p-4 mt-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="text-muted-custom">Total a pagar</span>
                <div class="text-orange" style="font-size: 2rem; font-weight: 700;">
                    {{ number_format($reservas->sum(fn($r) => $r->evento->precioDelSector($r->asiento->sector_id)?->precio ?? 0), 2, ',', '.') }} €
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('compra.store') }}" method="POST">
                    @csrf
                    @foreach($reservas as $reserva)
                        <input type="hidden" name="reservas[]" value="{{ $reserva->id }}">
                    @endforeach
                    <button type="submit" class="btn-orange" style="font-size: 1rem; padding: 12px 32px;">
                        <i class="bi bi-credit-card me-2"></i>Confirmar compra
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection

@section('scripts')
<script>
function actualizarTimers() {
    document.querySelectorAll('[data-expira]').forEach(el => {
        const expira = new Date(el.dataset.expira);
        const ahora = new Date();
        const diff = Math.floor((expira - ahora) / 1000);

        if (diff <= 0) {
            el.textContent = 'Expirado';
            el.style.color = '#ff6b6b';
            setTimeout(() => location.reload(), 2000);
        } else {
            const min = Math.floor(diff / 60).toString().padStart(2, '0');
            const seg = (diff % 60).toString().padStart(2, '0');
            el.textContent = `${min}:${seg}`;
            if (diff < 120) el.style.color = '#ff6b6b';
        }
    });
}

actualizarTimers();
setInterval(actualizarTimers, 1000);
</script>
@endsection