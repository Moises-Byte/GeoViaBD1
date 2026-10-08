<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Via extends Model
{
    protected $table = 'via';
    protected $primaryKey = 'id_via';

    public $timestamps = false;

    protected $fillable = [
        'nombre_via',
        'nivel_trafico',
        'fecha_ultimo_mantenimiento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ultimo_mantenimiento' => 'date',
        ];
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'via_id_via', 'id_via');
    }
}
