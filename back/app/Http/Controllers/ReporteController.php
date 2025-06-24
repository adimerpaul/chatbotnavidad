<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\History;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller{
    public function resumen()
    {
        $totalHistorial = History::groupBy('phone')
            ->select('phone', DB::raw('count(*) as total'))
            ->get()
            ->count();
        $totalRespuestas = History::whereNotNull('answer')->count();
        $totalPreguntas = Pregunta::count();
        $totalDoctores = Doctor::count();

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();
        $respuestasPorFecha = History::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as cantidad')
        )
            ->whereNotNull('answer')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->whereBetween('created_at', [$inicioMes, $finMes])
            ->get();

        return response()->json([
            'total_historial' => $totalHistorial,
            'total_respuestas' => $totalRespuestas,
            'total_preguntas' => $totalPreguntas,
            'total_doctores' => $totalDoctores,
            'respuestas_por_fecha' => $respuestasPorFecha,
        ]);
    }
}
