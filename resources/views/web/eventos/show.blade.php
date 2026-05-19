@extends('layouts.app')

@section('title', $evento->nombre . ' - Nexora Arena')

@section('content')

<div class="container py-5">

    {{-- Header evento --}}
    <div class="row mb-5">
        <div class="col-lg-8">
            <a href="{{ route('eventos.index') }}" class="text-muted-custom small mb-3 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Volver a eventos
            </a>
            <span class="badge-orange mb-2 d-inline-block">
                {{ $evento->fecha->format('d M Y') }}
                @if($evento->hora) · {{ $evento->hora->format('H:i') }} @endif
            </span>
            {{-- Imagen del evento --}}
            <div class="mb-4">
                <img 
                    src="{{ asset($evento->poster_url) }}"
                    alt="{{ $evento->nombre }}"
                    class="img-fluid rounded-4 shadow-lg"
                    style="
                        width: 50%;
                        max-height: 220px;
                        object-fit: cover;
                    "
                >
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 700;" class="mb-3">
                {{ $evento->nombre }}
            </h1>
            <p class="text-muted-custom mb-4" style="font-size: 1.05rem; line-height: 1.7;">
                {{ $evento->descripcion_larga }}
            </p>
        </div>
        <div class="col-lg-4">
            <div class="card-dark p-4">
                <h3 style="font-size: 1rem; font-weight: 600;" class="mb-3">
                    <i class="bi bi-info-circle me-2 text-purple"></i>Detalles del evento
                </h3>
                <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #ffffff10;">
                    <span class="text-muted-custom small">Fecha</span>
                    <span class="small">{{ $evento->fecha->format('d/m/Y') }}</span>
                </div>
                @if($evento->hora)
                <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #ffffff10;">
                    <span class="text-muted-custom small">Hora</span>
                    <span class="small">{{ $evento->hora->format('H:i') }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #ffffff10;">
                    <span class="text-muted-custom small">Sectores</span>
                    <span class="small">{{ $sectoresDisponibles->count() }} disponibles</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted-custom small">Precio desde</span>
                    <span class="text-orange" style="font-weight: 600;">
                        {{ number_format($evento->precios->min('precio'), 2, ',', '.') }} €
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sectores --}}
    <div class="mb-5">
        <h2 style="font-size: 1.5rem; font-weight: 600;" class="mb-4">
            <i class="bi bi-grid me-2 text-purple"></i>Elige tu sector
        </h2>
        <div class="row g-3">
            @foreach($sectoresDisponibles as $sector)
            <div class="col-md-4 col-lg-3">
                <div class="card-dark p-3 text-center sector-card" 
                     style="cursor: pointer; transition: all 0.2s;"
                     data-sector-id="{{ $sector->id }}"
                     data-evento-id="{{ $evento->id }}"
                     data-total="{{ $disponibilidadSectores[$sector->id]['total'] }}"
                     data-disponibles="{{ $disponibilidadSectores[$sector->id]['disponibles'] }}"
                     data-porcentaje="{{ $disponibilidadSectores[$sector->id]['porcentaje'] }}">
                    <div class="badge-purple mb-2 d-inline-block">
                        {{ $sector->nombre }}
                    </div>
                    <div class="text-orange" style="font-size: 1.3rem; font-weight: 700;">
                        {{ number_format($sector->pivot->precio, 2, ',', '.') }} €
                    </div>
                    <div class="text-muted-custom" style="font-size: 0.75rem;">
                        por asiento
                    </div>
                    <div class="mt-2 disponibilidad-bar" style="height: 4px; border-radius: 2px; background: #ffffff10;">
                        <div style="height: 100%; border-radius: 2px; width: {{ $disponibilidadSectores[$sector->id]['porcentaje'] }}%;"></div>
                    </div>
                    <div class="mt-1 disponibilidad-texto" style="font-size: 0.7rem;">
                        {{ $disponibilidadSectores[$sector->id]['disponibles'] }} / {{ $disponibilidadSectores[$sector->id]['total'] }} disponibles
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Mapa de asientos (se carga al seleccionar sector) --}}
    <div id="mapa-asientos" style="display: none;">
        <h2 style="font-size: 1.5rem; font-weight: 600;" class="mb-4">
            <i class="bi bi-grid-3x3 me-2 text-purple"></i>Selecciona tu asiento
        </h2>
        <div class="card-dark p-4">
            <div class="text-center mb-4">
                <div style="background: #8B6BF530; border: 1px solid #8B6BF5; border-radius: 8px; padding: 8px 40px; display: inline-block;">
                    <span class="text-purple small">ESCENARIO / CAMPO</span>
                </div>
            </div>
            <div id="asientos-container" class="text-center"></div>
            <div class="d-flex justify-content-center gap-4 mt-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 16px; height: 16px; background: #1A3A6B; border-radius: 3px;"></div>
                    <span class="text-muted-custom small">Libre</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 16px; height: 16px; background: #8B6BF5; border-radius: 3px;"></div>
                    <span class="text-muted-custom small">Seleccionado</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 16px; height: 16px; background: #3A3A4A; border-radius: 3px;"></div>
                    <span class="text-muted-custom small">Ocupado</span>
                </div>
            </div>
            {{-- Contador y botón reservar --}}
            <div class="text-center mt-4">
                <p id="mensaje-asientos" style="display:none; font-size: 0.85rem;" class="mb-2"></p>
                <span id="contador-seleccionados" class="text-muted-custom small d-block mb-3"></span>
                <button id="btn-reservar" onclick="reservarAsientos()" 
                        class="btn-orange" style="display:none;">
                    <i class="bi bi-cart-plus me-2"></i>Añadir al carrito
                </button>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
let sectorSeleccionado = null;
let eventoId = null;
let asientosSeleccionados = [];
const MAX_ASIENTOS = 5;
let isDragging = false;
let startAsientoId = null;

document.querySelectorAll('.sector-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.sector-card').forEach(c => {
            c.style.borderColor = '';
            c.style.boxShadow = '';
        });
        this.style.borderColor = '#9D50FF';
        this.style.boxShadow = '0 0 15px rgba(157,80,255,0.3)';

        eventoId = this.dataset.eventoId;
        sectorSeleccionado = this.dataset.sectorId;
        asientosSeleccionados = [];

        fetch(`/api/eventos/${eventoId}/sectores/${sectorSeleccionado}/asientos`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('mapa-asientos').style.display = 'block';
            document.getElementById('mapa-asientos').scrollIntoView({ behavior: 'smooth' });
            renderAsientos(data.data.asientos);
        });
    });
});

function renderAsientos(asientos) {
    const container = document.getElementById('asientos-container');
    const filas = {};

    asientos.forEach(a => {
        if (!filas[a.fila]) filas[a.fila] = [];
        filas[a.fila].push(a);
    });

    container.innerHTML = Object.entries(filas).map(([fila, seats]) => `
        <div style="display: flex; justify-content: center; gap: 4px; margin-bottom: 4px; align-items: center;">
            <span style="width: 20px; font-size: 11px; color: #7A7A9A;">${fila}</span>
            ${seats.map(s => `
                <div class="asiento-btn"
                     data-id="${s.id}"
                     data-disponible="${s.disponible}"
                     data-fila="${s.fila}"
                     data-numero="${s.numero}"
                     style="width: 22px; height: 18px; border-radius: 3px;
                            cursor: ${s.disponible ? 'pointer' : 'not-allowed'};
                            background: ${s.disponible ? '#1A3A6B' : '#3A3A4A'};
                            transition: background 0.15s; user-select: none;"
                     title="Fila ${s.fila} - Asiento ${s.numero}">
                </div>
            `).join('')}
        </div>
    `).join('');

    inicializarEventosAsientos();
    actualizarBotonReservar();
}

function inicializarEventosAsientos() {
    const asientos = document.querySelectorAll('.asiento-btn');

    asientos.forEach(btn => {
        btn.addEventListener('mousedown', (e) => {
            if (btn.dataset.disponible !== 'true') return;
            isDragging = true;
            startAsientoId = btn.dataset.id;
            toggleAsiento(btn);
            e.preventDefault();
        });

        btn.addEventListener('mouseover', () => {
            if (!isDragging || btn.dataset.disponible !== 'true') return;
            if (!asientosSeleccionados.includes(btn.dataset.id)) {
                if (asientosSeleccionados.length < MAX_ASIENTOS) {
                    toggleAsiento(btn);
                }
            }
        });
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
    });
}

function toggleAsiento(btn) {
    const id = btn.dataset.id;
    const idx = asientosSeleccionados.indexOf(id);

    if (idx === -1) {
        if (asientosSeleccionados.length >= MAX_ASIENTOS) {
            mostrarMensaje(`Máximo ${MAX_ASIENTOS} asientos por compra`, 'warning');
            return;
        }
        asientosSeleccionados.push(id);
        btn.style.background = '#9D50FF';
        btn.style.transform = 'scale(1.2)';
    } else {
        asientosSeleccionados.splice(idx, 1);
        btn.style.background = '#1A3A6B';
        btn.style.transform = 'scale(1)';
    }

    actualizarBotonReservar();
}

function actualizarBotonReservar() {
    const btn = document.getElementById('btn-reservar');
    const contador = document.getElementById('contador-seleccionados');

    if (!btn) return;

    if (asientosSeleccionados.length > 0) {
        btn.style.display = 'inline-block';
        contador.textContent = `${asientosSeleccionados.length} asiento${asientosSeleccionados.length > 1 ? 's' : ''} seleccionado${asientosSeleccionados.length > 1 ? 's' : ''}`;
    } else {
        btn.style.display = 'none';
        contador.textContent = '';
    }
}

function mostrarMensaje(texto, tipo) {
    const msg = document.getElementById('mensaje-asientos');
    msg.textContent = texto;
    msg.style.display = 'block';
    msg.style.color = tipo === 'warning' ? '#FF7B00' : '#ff6b6b';
    setTimeout(() => msg.style.display = 'none', 3000);
}

async function reservarAsientos() {
    @auth
    if (asientosSeleccionados.length === 0) return;

    if (!confirm(`¿Reservar ${asientosSeleccionados.length} asiento(s)? Tendrás 10 minutos para completar la compra.`)) return;

    const btnReservar = document.getElementById('btn-reservar');
    btnReservar.textContent = 'Reservando...';
    btnReservar.disabled = true;

    for (const asientoId of asientosSeleccionados) {
        try {
            const response = await fetch('/api/web/reservas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    evento_id: eventoId,
                    asiento_id: asientoId
                })
            });

            // await hace que cada reserva espere a que la anterior termine antes de empezar la siguiente
            const data = await response.json();

            if (data.error) {
                mostrarMensaje(data.error, 'error');
                btnReservar.textContent = 'Añadir al carrito';
                btnReservar.disabled = false;
                return;
            }
        } catch (e) {
            mostrarMensaje('Error al reservar. Inténtalo de nuevo.', 'error');
            btnReservar.textContent = 'Añadir al carrito';
            btnReservar.disabled = false;
            return;
        }
    }

    window.location.href = '/carrito';
    @else
    window.location.href = '/login';
    @endauth
}

// Colorear sectores según disponibilidad
document.querySelectorAll('.sector-card').forEach(card => {
    const porcentaje = parseInt(card.dataset.porcentaje);
    const bar = card.querySelector('.disponibilidad-bar div');
    const texto = card.querySelector('.disponibilidad-texto');

    let color, label;

    if (porcentaje === 0) {
        color = '#3A3A4A';
        label = 'Agotado';
        card.style.opacity = '0.6';
        card.style.cursor = 'not-allowed';
    } else if (porcentaje <= 20) {
        color = '#ff4444';
        label = `¡Últimas entradas! ${card.dataset.disponibles} disponibles`;
    } else if (porcentaje <= 50) {
        color = '#FF7B00';
        label = `${card.dataset.disponibles} / ${card.dataset.total} disponibles`;
    } else {
        color = '#00cc66';
        label = `${card.dataset.disponibles} / ${card.dataset.total} disponibles`;
    }

    if (bar) bar.style.background = color;
    if (texto) {
        texto.style.color = color;
        texto.textContent = label;
    }

    if (porcentaje > 0 && porcentaje <= 20) {
        card.style.borderColor = '#ff444440';
    } else if (porcentaje > 20 && porcentaje <= 50) {
        card.style.borderColor = '#FF7B0040';
    }
});
</script>
@endsection