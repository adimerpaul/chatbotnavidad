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
        ];

        $rangos = [
            'LUNES' => 1,
            'MARTES' => 2,
            'MIERCOLES' => 3,
            'MIÉRCOLES' => 3,
            'JUEVES' => 4,
            'VIERNES' => 5,
            'SABADO' => 6,
            'SÁBADO' => 6,
            'DOMINGO' => 0,
        ];

        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctor_id)->get();
        $eventos = [];

        foreach ($schedules as $horario) {
            $textoDias = strtoupper($horario->days);
//            $textoDias = str_replace([' A ', ' Y '], [' a ', ' y '], $textoDias); // uniformizar
            $textoDias = strtoupper($horario->days);

// Separar conectores comunes
            $textoDias = str_replace([' A ', ' Y ', ' Y', 'Y ', ' A', 'A '], [',', ',', ',', ',', ',', ','], $textoDias);

// Agregar coma entre días consecutivos sin separador, ej. "MIERCOLES JUEVES"
            $textoDias = preg_replace('/(LUNES|MARTES|MIERCOLES|MIÉRCOLES|JUEVES|VIERNES|SABADO|SÁBADO|DOMINGO)\s+(?=LUNES|MARTES|MIERCOLES|MIÉRCOLES|JUEVES|VIERNES|SABADO|SÁBADO|DOMINGO)/', '$1, ', $textoDias);
            $dias = [];

            if (in_array($textoDias, ['A LLAMADO', 'A REQUERIMIENTO'])) {
                continue; // ignorar esos casos
            }

            if (str_contains($textoDias, ' a ')) {
                // Rango tipo "LUNES a VIERNES"
                [$inicio, $fin] = explode(' a ', $textoDias);
                $inicio = trim($inicio);
                $fin = trim($fin);
                if (isset($rangos[$inicio]) && isset($rangos[$fin])) {
                    $i = $rangos[$inicio];
                    $f = $rangos[$fin];
                    if ($i <= $f) {
                        $dias = range($i, $f);
                    } else {
                        // si es un rango inverso (ej. VIERNES a MARTES)
                        $dias = array_merge(range($i, 6), range(0, $f));
                    }
                }
            } elseif (str_contains($textoDias, ',')) {
                // Lista tipo "LUNES, MIERCOLES Y VIERNES"
                $partes = preg_split('/,| y /', $textoDias);
                foreach ($partes as $diaTexto) {
                    $diaTexto = trim($diaTexto);
                    if (isset($diasMap[$diaTexto])) {
                        $dias[] = $diasMap[$diaTexto];
                    }
                }
            } elseif (str_contains($textoDias, ' Y ')) {
                // Caso tipo "LUNES Y JUEVES"
                $partes = explode(' Y ', $textoDias);
                foreach ($partes as $diaTexto) {
                    $diaTexto = trim($diaTexto);
                    if (isset($diasMap[$diaTexto])) {
                        $dias[] = $diasMap[$diaTexto];
                    }
                }
            } else {
                // Caso simple "LUNES"
                $diaTexto = trim($textoDias);
                if (isset($diasMap[$diaTexto])) {
                    $dias[] = $diasMap[$diaTexto];
                }
            }

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
