<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTrabajo extends Model
{
    protected $table = 'orden_trabajo';
    protected $primaryKey = 'id_orden';

    public $timestamps = false;

    protected $fillable = [
        'reporte_id_reporte',
        'cuadrilla_id_cuadrilla',
        'usuario_id_usuario',
        'fecha_asignacion',
        'fecha_finalizacion',
        'estado',
        'avance',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_finalizacion' => 'datetime',
            'avance' => 'integer',
        ];
    }

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(
            Reporte::class,
            'reporte_id_reporte',
            'id_reporte'
        );
    }

    public function cuadrilla(): BelongsTo
    {
        return $this->belongsTo(
            Cuadrilla::class,
            'cuadrilla_id_cuadrilla',
            'id_cuadrilla'
        );
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id_usuario',
            'id_usuario'
        );
    }
}
