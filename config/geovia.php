<?php

return [
    // Centro inicial del mapa al crear un reporte (Puerto Barrios, Izabal).
    'mapa' => [
        'latitud' => (float) env('GEOVIA_MAPA_LAT', 15.7277),
        'longitud' => (float) env('GEOVIA_MAPA_LNG', -88.5944),
        'zoom' => (int) env('GEOVIA_MAPA_ZOOM', 14),
    ],

    // Límites para las fotografías de un reporte.
    'fotos' => [
        'maximo' => 5,
        'max_kb' => 4096,
    ],
];