@extends('layouts.app')

@section('title', 'Crear cuenta - Nexora Arena')

@section('content')

<div style="
    min-height: 100vh;
    background: linear-gradient(rgba(10,10,15,0.75), rgba(10,10,15,0.85)),
                url('{{ asset('imagenes/nexora-arena1.png') }}') center center / cover no-repeat;
    display: flex;
    align-items: center;
">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            {{-- Card formulario --}}
            <div class="card-dark p-4">

                {{-- Errores --}}
                @if($errors->any())
                <div style="background: #ff000020; border: 1px solid #ff000040; border-radius: 8px; padding: 12px 16px;" class="mb-4">
                    @foreach($errors->all() as $error)
                    <p class="mb-0 small" style="color: #ff6b6b;">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $error }}
                    </p>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- Nombre --}}
                    <div class="col-6">
                        <label class="small text-muted-custom mb-1">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('nombre') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="Juan" required>
                        @error('nombre')
                        <p class="mt-1 small" style="color: #ff6b6b;">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Apellido --}}
                    <div class="col-6">
                        <label class="small text-muted-custom mb-1">Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('apellido') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="García" required>
                        @error('apellido')
                        <p class="mt-1 small" style="color: #ff6b6b;">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('email') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="tu@email.com" required>
                        @error('email')
                        <p class="mt-1 small" style="color: #ff6b6b;">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Contraseña</label>
                        <input type="password" name="password"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('password') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="Mínimo 8 caracteres" required>
                        @error('password')
                        <p class="mt-1 small" style="color: #ff6b6b;">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Confirmar Password --}}
                    <div class="mb-4">
                        <label class="small text-muted-custom mb-1">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation"
                            style="width: 100%; background: #0A0A0F; border: 1px solid {{ $errors->has('password_confirmation') ? '#ff6b6b' : '#ffffff20' }}; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="Repite la contraseña" required>
                        @error('password_confirmation')
                        <p class="mt-1 small" style="color: #ff6b6b;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pregunta anti-bot --}}
                    <div class="mb-4" style="background: #9D50FF10; border: 1px solid #9D50FF30; border-radius: 12px; padding: 16px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-shield-check text-purple"></i>
                            <span class="small text-muted-custom">Verificación humana</span>
                        </div>
                        <p class="small mb-3" style="color: #ffffff; font-weight: 500;">{{ $pregunta }}</p>
                        <div class="d-flex flex-column gap-2">
                            @foreach($opciones as $opcion)
                            <label style="display: flex; align-items: center; gap-10px; cursor: pointer; padding: 8px 12px; border-radius: 8px; border: 1px solid #ffffff15; transition: all 0.2s;"
                                   onmouseover="this.style.borderColor='#9D50FF50'; this.style.background='#9D50FF10';"
                                   onmouseout="this.style.borderColor='#ffffff15'; this.style.background='transparent';">
                                <input type="radio" name="respuesta" value="{{ $opcion }}"
                                       {{ old('respuesta') == $opcion ? 'checked' : '' }}
                                       style="accent-color: #9D50FF; margin-right: 10px;"
                                       required>
                                <span class="small" style="color: #ffffff;">{{ $opcion }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('respuesta')
                        <p class="mt-2 small" style="color: #ff6b6b;">
                            <i class="bi bi-x-circle me-1"></i>{{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-orange w-100 text-center">
                        <i class="bi bi-person-plus-fill me-2"></i>Crear cuenta
                    </button>
                </form>
            </div>

            {{-- Link login --}}
            <div class="text-center mt-4">
                <span class="text-muted-custom small">¿Ya tienes cuenta?</span>
                <a href="{{ route('login') }}" class="text-purple ms-1 small" style="text-decoration: none;">
                    Inicia sesión
                </a>
            </div>

        </div>
    </div>
</div>
</div>

@endsection