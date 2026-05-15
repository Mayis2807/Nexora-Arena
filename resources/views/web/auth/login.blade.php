@extends('layouts.app')

@section('title', 'Iniciar sesión - Nexora Arena')

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

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="tu@email.com" required>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="small text-muted-custom mb-1">Contraseña</label>
                        <input type="password" name="password"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="••••••••" required>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-orange w-100 text-center" style="width: 100%;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                    </button>
                </form>
            </div>

            {{-- Link registro --}}
            <div class="text-center mt-4">
                <span class="text-muted-custom small">¿No tienes cuenta?</span>
                <a href="{{ route('register') }}" class="text-purple ms-1 small" style="text-decoration: none;">
                    Regístrate gratis
                </a>
            </div>

        </div>
    </div>
</div>
</div>


@endsection