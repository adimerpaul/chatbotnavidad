<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller{
    public function update(Request $request, $id)
    {
        $horario = \App\Models\DoctorSchedule::findOrFail($id);
        $request->validate([
            'price' => 'required|numeric'
        ]);
        $horario->price = $request->price;
        $horario->save();

        return response()->json(['message' => 'Precio actualizado']);
    }
    public function horariosPorDoctor($doctor_id)
    {
        $diasMap = [
            'DOMINGO' => 0,
            'LUNES' => 1,
            'MARTES' => 2,
            'MIERCOLES' => 3,
            'MIÉRCOLES' => 3,
            'JUEVES' => 4,
            'VIERNES' => 5,
            'SABADO' => 6,
            'SÁBADO' => 6,
            'SABADOS' => 6,
        ];

        $ordenDias = ['LUNES', 'MARTES', 'MIERCOLES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'SÁBADO', 'SABADOS', 'DOMINGO'];

        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctor_id)->get();
        $eventos = [];

        foreach ($schedules as $horario) {
            $textoDias = strtoupper(trim($horario->days));

            if (in_array($textoDias, ['A LLAMADO', 'A REQUERIMIENTO'])) {
                continue;
            }

            $dias = [];

            // --- Detectar si es un rango tipo "LUNES A JUEVES"
            if (preg_match('/^(LUNES|MARTES|MIERCOLES|MIÉRCOLES|JUEVES|VIERNES|SABADO|SÁBADO|SABADOS|DOMINGO)\s+A\s+(LUNES|MARTES|MIERCOLES|MIÉRCOLES|JUEVES|VIERNES|SABADO|SÁBADO|SABADOS|DOMINGO)$/', $textoDias, $matches)) {
                $inicio = $matches[1];
                $fin = $matches[2];

                $startIndex = array_search($inicio, $ordenDias);
                $endIndex = array_search($fin, $ordenDias);

                if ($startIndex !== false && $endIndex !== false) {
                    $rango = $startIndex <= $endIndex
                        ? array_slice($ordenDias, $startIndex, $endIndex - $startIndex + 1)
                        : array_merge(array_slice($ordenDias, $startIndex), array_slice($ordenDias, 0, $endIndex + 1));

                    foreach ($rango as $diaTexto) {
                        if (isset($diasMap[$diaTexto])) {
                            $dias[] = $diasMap[$diaTexto];
                        }
                    }
                }
            } else {
                // Convertir conectores como "Y", "," y otros a coma
                $textoDias = str_replace([' Y ', ' Y', 'Y ', ' A ', ' A'], ',', $textoDias);
                $partes = explode(',', $textoDias);
                foreach ($partes as $diaTexto) {
                    $diaTexto = trim($diaTexto);
                    if (isset($diasMap[$diaTexto])) {
                        $dias[] = $diasMap[$diaTexto];
                    }
                }
            }

            // Agregar eventos al calendario
            foreach ($dias as $dia) {
                $eventos[] = [
                    'title' => 'Disponible',
                    'daysOfWeek' => [$dia],
                    'startTime' => $horario->available_from,
                    'endTime' => $horario->available_to,
                    'display' => 'background',
                    'color' => '#d3d3d3',
                ];
            }
        }

        return response()->json($eventos);
    }

    public function store(Request $request, Doctor $doctor)
    {
        $request->validate([
            'available_from' => 'required|date_format:H:i',
            'available_to' => 'required|date_format:H:i',
            'days' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $schedule = $doctor->schedules()->create([
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
            'days' => $request->days,
            'price' => $request->price,
        ]);

        return response()->json($schedule, 201);
    }

    public function destroy($id)
    {
        $schedule = DoctorSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Horario eliminado']);
    }
}
