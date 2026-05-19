@extends('layouts.app')

@section('title', 'Mi Experiencia - Nexora Arena')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Header --}}
            <div class="text-center mb-5">
                <span class="badge-purple mb-3 d-inline-block">Tu opinión importa</span>
                <h1 style="font-size: 2.5rem; font-weight: 700;">¿Cómo fue tu experiencia?</h1>
                <p class="text-muted-custom">Cuéntanos cómo viviste la navegación y tus visitas al Nexora Arena</p>
            </div>

            {{-- Mensajes --}}
            @if(session('success'))
            <div style="background: #00cc6620; border: 1px solid #00cc6640; border-radius: 12px; padding: 20px;" class="mb-4 text-center">
                <div style="font-size: 3rem;">🎉</div>
                <h3 style="color: #00cc66; font-weight: 600;" class="mt-2">{{ session('success') }}</h3>
                <p class="text-muted-custom small mt-2">Tu opinión nos ayuda a mejorar cada día.</p>
                <a href="{{ route('home') }}" class="btn-orange mt-3 d-inline-block">
                    <i class="bi bi-house me-2"></i>Volver al inicio
                </a>
            </div>
            @endif

            @if(session('error'))
            <div style="background: #ff000020; border: 1px solid #ff000040; border-radius: 12px; padding: 16px;" class="mb-4">
                <p class="mb-0 small" style="color: #ff6b6b;">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                </p>
            </div>
            @endif

            {{-- Aviso general si hay errores --}}
            @if($errors->any())
            <div style="background: #ff000015; border: 1px solid #ff000040; border-radius: 12px; padding: 16px;" class="mb-4">
                <p class="mb-0 small" style="color: #ff6b6b;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Por favor completa los campos marcados en rojo antes de enviar.
                </p>
            </div>
            @endif

            @if($yaRespondio)
            {{-- Ya respondió --}}
            <div class="card-dark p-5 text-center">
                <div style="font-size: 4rem;" class="mb-3">✅</div>
                <h2 style="font-weight: 700;" class="mb-3">¡Ya compartiste tu experiencia!</h2>
                <p class="text-muted-custom mb-4">Gracias por tomarte el tiempo de contarnos tu opinión. Tu feedback es muy valioso para nosotros.</p>
                <a href="{{ route('home') }}" class="btn-orange">
                    <i class="bi bi-house me-2"></i>Volver al inicio
                </a>
            </div>

            @else
            {{-- Formulario --}}
            <form action="{{ route('experiencia.store') }}" method="POST">
                @csrf

                {{-- 1. Valoración web (estrellas) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('valoracion_web') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">01.</span> ¿Cómo valorarías tu experiencia navegando la web?
                        @if($errors->has('valoracion_web'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Selecciona del 1 al 5</p>

                    <div class="d-flex gap-3 justify-content-center">
                        @for($i = 1; $i <= 5; $i++)
                        <label style="cursor: pointer; text-align: center;">
                            <input type="radio" name="valoracion_web" value="{{ $i }}"
                                   class="d-none estrella-input" {{ old('valoracion_web') == $i ? 'checked' : '' }}>
                            <div class="estrella-btn" data-valor="{{ $i }}"
                                 style="width: 55px; height: 55px; border-radius: 12px;
                                        background: {{ old('valoracion_web') >= $i ? '#F5C40030' : '#ffffff10' }};
                                        border: 1px solid {{ old('valoracion_web') >= $i ? '#F5C40060' : '#ffffff20' }};
                                        display: flex; align-items: center; justify-content: center; font-size: 1.5rem; transition: all 0.2s; cursor: pointer;">
                                ⭐
                            </div>
                            <div class="small text-muted-custom mt-1">{{ $i }}</div>
                        </label>
                        @endfor
                    </div>
                    @error('valoracion_web')
                    <p class="text-center mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Secciones visitadas (checkboxes) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('secciones_visitadas') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">02.</span> ¿Qué secciones de la web visitaste?
                        @if($errors->has('secciones_visitadas'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Selecciona todas las que apliquen</p>

                    <div class="row g-2">
                        @foreach(['Página de inicio', 'Lista de eventos', 'Detalle de evento', 'Selector de asientos', 'Carrito de compra', 'Mis entradas', 'Panel de administración', 'Registro/Login'] as $seccion)
                        <div class="col-md-6">
                            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; border: 1px solid #ffffff15; cursor: pointer; transition: all 0.2s;"
                                   onmouseover="this.style.borderColor='#9D50FF50'; this.style.background='#9D50FF10';"
                                   onmouseout="this.style.borderColor='#ffffff15'; this.style.background='transparent';">
                                <input type="checkbox" name="secciones_visitadas[]" value="{{ $seccion }}"
                                       style="accent-color: #9D50FF; width: 16px; height: 16px;"
                                       {{ in_array($seccion, old('secciones_visitadas', [])) ? 'checked' : '' }}>
                                <span class="small">{{ $seccion }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('secciones_visitadas')
                    <p class="mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Eventos de interés (select múltiple) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('eventos_interes') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">03.</span> ¿Qué tipo de eventos te interesan más?
                        @if($errors->has('eventos_interes'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Puedes seleccionar varios</p>

                    <select name="eventos_interes[]" multiple
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('eventos_interes') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 10px; padding: 10px 14px; color: #ffffff; outline: none; min-height: 140px;">
                        @foreach(['Conciertos de rock', 'Música electrónica', 'Bachata y salsa', 'Baloncesto', 'Fútbol', 'Boxeo y MMA', 'Teatro y ópera', 'Comedia y shows', 'Eventos familiares', 'Experiencias inmersivas'] as $evento)
                        <option value="{{ $evento }}"
                                style="padding: 6px; background: #1A1A28;"
                                {{ in_array($evento, old('eventos_interes', [])) ? 'selected' : '' }}>
                            {{ $evento }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-muted-custom mt-1" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i>Mantén Ctrl (Windows) o Cmd (Mac) para seleccionar varios
                    </p>
                    @error('eventos_interes')
                    <p class="mt-1 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4. Cómo nos encontraste (select simple) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('como_nos_encontraste') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">04.</span> ¿Cómo nos encontraste?
                        @if($errors->has('como_nos_encontraste'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Selecciona una opción</p>

                    <select name="como_nos_encontraste"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('como_nos_encontraste') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 10px; padding: 10px 14px; color: #ffffff; outline: none;">
                        <option value="" disabled {{ old('como_nos_encontraste') ? '' : 'selected' }} style="background: #1A1A28;">-- Selecciona una opción --</option>
                        @foreach(['Redes sociales', 'Google / Buscador', 'Recomendación de un amigo', 'Publicidad online', 'Radio o televisión', 'Cartel o publicidad física', 'Ya conocía el arena', 'Otro'] as $opcion)
                        <option value="{{ $opcion }}" style="background: #1A1A28;"
                                {{ old('como_nos_encontraste') == $opcion ? 'selected' : '' }}>
                            {{ $opcion }}
                        </option>
                        @endforeach
                    </select>
                    @error('como_nos_encontraste')
                    <p class="mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 5. ¿Recomendarías? (radio) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('recomendaria') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">05.</span> ¿Recomendarías Nexora Arena a un amigo?
                        @if($errors->has('recomendaria'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Sé honesto/a</p>

                    <div class="d-flex gap-3">
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="recomendaria" value="1" class="d-none"
                                   {{ old('recomendaria') == '1' ? 'checked' : '' }}>
                            <div class="opcion-radio text-center p-3" style="border-radius: 12px; border: 1px solid {{ old('recomendaria') == '1' ? '#9D50FF' : '#ffffff20' }}; background: {{ old('recomendaria') == '1' ? '#9D50FF15' : 'transparent' }}; transition: all 0.2s;">
                                <div style="font-size: 2rem;">👍</div>
                                <div class="small mt-1">Sí, lo recomendaría</div>
                            </div>
                        </label>
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="recomendaria" value="0" class="d-none"
                                   {{ old('recomendaria') == '0' ? 'checked' : '' }}>
                            <div class="opcion-radio text-center p-3" style="border-radius: 12px; border: 1px solid {{ old('recomendaria') == '0' ? '#9D50FF' : '#ffffff20' }}; background: {{ old('recomendaria') == '0' ? '#9D50FF15' : 'transparent' }}; transition: all 0.2s;">
                                <div style="font-size: 2rem;">👎</div>
                                <div class="small mt-1">No lo recomendaría</div>
                            </div>
                        </label>
                    </div>
                    @error('recomendaria')
                    <p class="mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 6. ¿Qué mejorarías? (select múltiple checkboxes) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('mejoras') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">06.</span> ¿Qué mejorarías de la web?
                        @if($errors->has('mejoras'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Selecciona todas las que apliquen</p>

                    <div class="row g-2">
                        @foreach(['Velocidad de carga', 'Diseño visual', 'Facilidad de compra', 'Información de eventos', 'Selector de asientos', 'Proceso de pago', 'Atención al cliente', 'Variedad de eventos', 'Precios', 'Nada, todo perfecto'] as $mejora)
                        <div class="col-md-6">
                            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; border: 1px solid #ffffff15; cursor: pointer; transition: all 0.2s;"
                                   onmouseover="this.style.borderColor='#FF7B0050'; this.style.background='#FF7B0010';"
                                   onmouseout="this.style.borderColor='#ffffff15'; this.style.background='transparent';">
                                <input type="checkbox" name="mejoras[]" value="{{ $mejora }}"
                                       style="accent-color: #FF7B00; width: 16px; height: 16px;"
                                       {{ in_array($mejora, old('mejoras', [])) ? 'checked' : '' }}>
                                <span class="small">{{ $mejora }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('mejoras')
                    <p class="mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- 7. Comentario (textarea) — opcional, no lleva error --}}
                <div class="card-dark p-4 mb-4">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">07.</span> Cuéntanos tu experiencia en el arena
                        <span class="text-muted-custom" style="font-size: 0.8rem; font-weight: 400;"> — Opcional</span>
                    </h3>
                    <p class="text-muted-custom small mb-3">Comparte anécdotas, momentos especiales o cualquier comentario</p>

                    <textarea name="comentario" rows="5"
                              style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 10px; padding: 12px 14px; color: #ffffff; outline: none; resize: vertical;"
                              placeholder="Ej: Fui al concierto de Aventura y fue una experiencia increíble..."
                              maxlength="1000">{{ old('comentario') }}</textarea>
                    <div class="text-end">
                        <span class="text-muted-custom" style="font-size: 0.75rem;">Máximo 1000 caracteres</span>
                    </div>
                </div>

                {{-- 8. ¿Volverías a comprar? (radio) --}}
                <div class="card-dark p-4 mb-4" style="{{ $errors->has('volveria_comprar') ? 'border: 1px solid #ff6b6b;' : '' }}">
                    <h3 style="font-size: 1.1rem; font-weight: 600;" class="mb-1">
                        <span class="text-purple">08.</span> ¿Volverías a comprar entradas en nuestra web?
                        @if($errors->has('volveria_comprar'))
                            <span style="color: #ff6b6b; font-size: 0.8rem; font-weight: 400;"> — Obligatorio</span>
                        @endif
                    </h3>
                    <p class="text-muted-custom small mb-3">Tu respuesta es importante para nosotros</p>

                    <div class="d-flex gap-3">
                        @foreach(['1' => ['emoji' => '🚀', 'texto' => 'Definitivamente sí'], '0' => ['emoji' => '🤔', 'texto' => 'Probablemente no']] as $valor => $opcion)
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="volveria_comprar" value="{{ $valor }}" class="d-none"
                                   {{ old('volveria_comprar') == $valor ? 'checked' : '' }}>
                            <div class="opcion-radio text-center p-3" style="border-radius: 12px; border: 1px solid {{ old('volveria_comprar') == $valor ? '#9D50FF' : '#ffffff20' }}; background: {{ old('volveria_comprar') == $valor ? '#9D50FF15' : 'transparent' }}; transition: all 0.2s;">
                                <div style="font-size: 2rem;">{{ $opcion['emoji'] }}</div>
                                <div class="small mt-1">{{ $opcion['texto'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('volveria_comprar')
                    <p class="mt-2 small" style="color: #ff6b6b;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button type="submit" class="btn-orange" style="font-size: 1rem; padding: 14px 40px;">
                        <i class="bi bi-send-fill me-2"></i>Enviar mi experiencia
                    </button>
                </div>

            </form>
            @endif

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Estrellas interactivas
document.querySelectorAll('.estrella-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const valor = parseInt(this.dataset.valor);
        
        document.querySelectorAll('.estrella-input').forEach((input, i) => {
            input.checked = (i + 1) === valor;
        });

        document.querySelectorAll('.estrella-btn').forEach((b, i) => {
            if (i < valor) {
                b.style.background = '#F5C40030';
                b.style.borderColor = '#F5C40060';
                b.style.transform = 'scale(1.1)';
            } else {
                b.style.background = '#ffffff10';
                b.style.borderColor = '#ffffff20';
                b.style.transform = 'scale(1)';
            }
        });
    });
});

// Radio buttons personalizados
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const name = this.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            const div = r.nextElementSibling;
            if (div && div.classList.contains('opcion-radio')) {
                div.style.borderColor = '#ffffff20';
                div.style.background = 'transparent';
            }
        });

        const div = this.nextElementSibling;
        if (div && div.classList.contains('opcion-radio')) {
            div.style.borderColor = '#9D50FF';
            div.style.background = '#9D50FF15';
        }
    });
});
</script>
@endsection