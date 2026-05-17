<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Roig Arena')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>

<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <!-- Clase personalizada para control de tamaño -->
            <img src="{{ asset('imagenes/nexora-logo.png') }}" alt="Nexora Arena" class="nav-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('eventos.*') ? 'active' : '' }}" href="{{ route('eventos.index') }}">Eventos</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" href="{{ route('entradas.index') }}">Mis entradas</a>
                </li>
    
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('carrito.*') ? 'active' : '' }}" 
                       href="{{ route('carrito.index') }}">
                        <i class="bi bi-bag me-1"></i>Carrito
                    </a>
                </li>
    
                @if(auth()->user()->is_admin)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" 
                       href="{{ route('admin.index') }}">
                        <i class="bi bi-gear me-1"></i>Admin
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('experiencia.*') ? 'active' : '' }}"
                       href="{{ route('experiencia.index') }}">
                        <i class="bi bi-star me-1"></i>Mi experiencia
                    </a>
                </li>
                @endauth
            </ul>

            <div class="d-flex gap-2 align-items-center">
                @auth
                    <span class="text-muted-custom">{{ auth()->user()->nombre }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-outline-custom">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline-custom">Entrar</a>
                    <a href="{{ route('register') }}" class="btn-primary-custom">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0 small">© 2026 Roig Arena · Valencia, España</p>
    </div>
</footer>

@auth
<a href="{{ route('carrito.index') }}" 
   style="position: fixed; bottom: 30px; right: 30px; background: var(--color-orange); color: #0A0A0F; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; text-decoration: none; box-shadow: 0 4px 20px rgba(255,123,0,0.4); z-index: 999; transition: transform 0.2s;"
   onmouseover="this.style.transform='scale(1.1)'"
   onmouseout="this.style.transform='scale(1)'">
    <i class="bi bi-bag-fill"></i>
</a>
@endauth

@yield('scripts')
</body>
</html>