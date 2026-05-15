<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    // RELACIONES

    public function reservas()
    {
        return $this->hasMany(EstadoAsiento::class);
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    // MÉTODOS ÚTILES

    public function reservasActivas()
    {
        return $this->reservas()
            ->where('estado', 'bloqueado')
            ->where('reservado_hasta', '>', now());
            //->get();
    }

    public function entradasValidas()
    {
        return $this->entradas()
            ->whereHas('evento', function ($q) {
                $q->where('fecha', '>=', now()->toDateString());
            })
            ->get();
    }

    public function tieneReservaEnEvento($eventoId): bool
    {
        return $this->reservas()
            ->where('evento_id', $eventoId)
            ->exists();
    }

    public function tieneEntradaEnEvento($eventoId): bool
    {
        return $this->entradas()
            ->where('evento_id', $eventoId)
            ->exists();
    }

}