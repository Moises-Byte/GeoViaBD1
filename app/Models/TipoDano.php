<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDano extends Model
{
    protected $table = 'tipo_dano';
    protected $primaryKey = 'id_tipo_dano';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    public function reportes(): HasMany
    {
        return $this->hasMany(
            Reporte::class,
            'tipo_dano_id_tipo_dano',
            'id_tipo_dano'
        );
    }
}
