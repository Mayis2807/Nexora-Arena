@extends('layouts.app')

@section('title', 'Crear evento - Admin')

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <a href="{{ route('admin.eventos') }}" class="text-muted-custom small mb-3 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i>Volver a eventos
            </a>
            <h1 style="font-size: 2rem; font-weight: 700;" class="mb-4">Crear nuevo evento</h1>

            <div class="card-dark p-4">

                @if($errors->any())
                <div style="background: #ff000020; border: 1px solid #ff000040; border-radius: 8px; padding: 12px 16px;" class="mb-4">
                    @foreach($errors->all() as $error)
                    <p class="mb-0 small" style="color: #ff6b6b;">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $error }}
                    </p>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('admin.eventos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Nombre del evento</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="Ej: Concierto Rock 2027" required>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Descripción corta</label>
                        <input type="text" name="descripcion_corta" value="{{ old('descripcion_corta') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="Máximo 255 caracteres" required>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted-custom mb-1">Descripción larga</label>
                        <textarea name="descripcion_larga" rows="4"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none; resize: vertical;"
                            placeholder="Descripción completa del evento" required>{{ old('descripcion_larga') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small text-muted-custom mb-1">Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha') }}"
                                style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted-custom mb-1">Hora</label>
                            <input type="time" name="hora" value="{{ old('hora') }}"
                                style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                                required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small text-muted-custom mb-1">Imagen (ruta o URL)</label>
                        <input type="text" name="poster_url" value="{{ old('poster_url') }}"
                            style="width: 100%; background: #0A0A0F; border: 1px solid #ffffff20; border-radius: 8px; padding: 10px 14px; color: #ffffff; outline: none;"
                            placeholder="imagenes/mi-evento.jpg">
                    </div>

                    <button type="submit" class="btn-orange w-100">
                        <i class="bi bi-plus-lg me-2"></i>Crear evento
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection