<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuadrilla extends Model
{
    protected $table = 'cuadrilla';
    protected $primaryKey = 'id_cuadrilla';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'responsable',
        'telefono',
    ];

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(
            OrdenTrabajo::class,
            'cuadrilla_id_cuadrilla',
            'id_cuadrilla'
        );
    }
}
