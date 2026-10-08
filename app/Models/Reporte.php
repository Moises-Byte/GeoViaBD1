<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reporte extends Model
{
    protected $table = 'reporte';
    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id_usuario',
        'via_id_via',
        'tipo_dano_id_tipo_dano',
        'descripcion',
        'latitud',
        'longitud',
        'fecha_reporte',
        'estado',
        'prioridad',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'fecha_reporte' => 'datetime',
            'prioridad' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id_usuario', 'id_usuario');
    }

    public function via(): BelongsTo
    {
        return $this->belongsTo(Via::class, 'via_id_via', 'id_via');
    }

    public function tipoDano(): BelongsTo
    {
        return $this->belongsTo(TipoDano::class, 'tipo_dano_id_tipo_dano', 'id_tipo_dano');
    }

    public function fotografias(): HasMany
    {
        return $this->hasMany(Fotografia::class, 'reporte_id_reporte', 'id_reporte');
    }

    public function inspecciones(): HasMany
    {
        return $this->hasMany(Inspeccion::class, 'reporte_id_reporte', 'id_reporte');
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class, 'reporte_id_reporte', 'id_reporte');
    }
}
