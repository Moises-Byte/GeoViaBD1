<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fotografia extends Model
{
    protected $table = 'fotografia';
    protected $primaryKey = 'id_foto';

    public $timestamps = false;

    protected $fillable = [
        'reporte_id_reporte',
        'ruta_foto',
    ];

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(Reporte::class, 'reporte_id_reporte', 'id_reporte');
    }
}
