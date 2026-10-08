<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'contrasena',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    public function getEmailForVerification(): string
    {
        return $this->correo;
    }

    public function routeNotificationForMail($notification): string
    {
        return $this->correo;
    }

    public function getNameAttribute(): ?string
    {
        return $this->nombre;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->correo;
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(
            Reporte::class,
            'usuario_id_usuario',
            'id_usuario'
        );
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(
            Inspeccion::class,
            'usuario_id_usuario',
            'id_usuario'
        );
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(
            OrdenTrabajo::class,
            'usuario_id_usuario',
            'id_usuario'
        );
    }
}
