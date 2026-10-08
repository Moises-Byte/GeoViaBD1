<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspeccion extends Model
{
    protected $table = 'inspeccion';
    protected $primaryKey = 'id_inspeccion';

    public $timestamps = false;

    protected $fillable = [
        'reporte_id_reporte',
        'usuario_id_usuario',
        'fecha_inspeccion',
        'resultado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inspeccion' => 'datetime',
        ];
    }

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(Reporte::class, 'reporte_id_reporte', 'id_reporte');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id_usuario', 'id_usuario');
    }
}
