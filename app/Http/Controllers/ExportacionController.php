<?php

namespace App\Http\Controllers;

use App\Services\EstadisticaService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportacionController extends Controller
{
    public function excel(Request $request, EstadisticaService $estadisticas): StreamedResponse
    {
        abort_unless(in_array($request->user()?->rol, ['AUTORIDAD', 'SUPERVISOR'], true), 403);

        $datos = $estadisticas->resumen();
        $libro = new Spreadsheet();
        $libro->getProperties()->setCreator('GeoVía')->setTitle('Estadísticas de gestión');

        $resumen = $libro->getActiveSheet()->setTitle('Resumen');
        $this->fila($resumen, 1, ['Indicador', 'Valor']);
        $this->fila($resumen, 2, ['Reportes registrados', $datos['totalReportes']]);
        $this->fila($resumen, 3, ['Reportes finalizados', $datos['porEstado']['FINALIZADO']]);
        $this->fila($resumen, 4, ['Tiempo promedio de resolución (días)', $datos['tiempoPromedio'] ?? 'Sin cierres válidos']);
        $this->fila($resumen, 5, ['Cuadrilla más activa', $datos['cuadrillaMasActiva']?->nombre ?? 'Sin asignaciones']);
        $this->fila($resumen, 6, ['Órdenes de la cuadrilla más activa', (int) ($datos['cuadrillaMasActiva']?->ordenes_trabajo_count ?? 0)]);
        $this->fila($resumen, 7, ['Fecha de generación', now()->format('d/m/Y H:i:s')]);
        $this->fila($resumen, 8, ['Cálculo del promedio', 'Desde la fecha del reporte hasta el cierre de la reparación.']);
        $this->fila($resumen, 9, ['Cuadrilla más activa', 'Mayor cantidad total de órdenes asignadas.']);

        $estados = $libro->createSheet()->setTitle('Reportes por estado');
        $this->fila($estados, 1, ['Estado', 'Reportes', 'Porcentaje del total']);
        $fila = 2;
        foreach ($datos['porEstado'] as $estado => $cantidad) {
            $this->fila($estados, $fila++, [$estado, $cantidad, $datos['totalReportes'] > 0 ? $cantidad / $datos['totalReportes'] : 0]);
        }
        $estados->getStyle('C2:C6')->getNumberFormat()->setFormatCode('0.00%');

        $vias = $libro->createSheet()->setTitle('Reportes por vía');
        $this->fila($vias, 1, ['Vía', 'Reportes']);
        $fila = 2;
        foreach ($datos['porVia'] as $via) {
            $this->fila($vias, $fila++, [$via->nombre_via, (int) $via->reportes_count]);
        }

        foreach ($libro->getAllSheets() as $hoja) {
            $ultimaColumna = $hoja->getHighestColumn();
            $hoja->getStyle('A1:'.$ultimaColumna.'1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $hoja->getStyle('A1:'.$ultimaColumna.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF0F766E');
            $hoja->freezePane('A2');
            foreach ($hoja->getColumnIterator() as $columna) {
                $hoja->getColumnDimension($columna->getColumnIndex())->setAutoSize(true);
            }
        }
        $libro->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($libro) {
            try {
                (new Xlsx($libro))->save('php://output');
            } finally {
                $libro->disconnectWorksheets();
            }
        }, 'geovia-estadisticas-'.now()->format('Y-m-d-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function fila(Worksheet $hoja, int $fila, array $valores): void
    {
        foreach ($valores as $indice => $valor) {
            // Los nombres se guardan como texto, aunque comiencen con un signo de fórmula.
            $hoja->setCellValueExplicit([$indice + 1, $fila], $valor,
                is_int($valor) || is_float($valor) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING);
        }
    }
}
