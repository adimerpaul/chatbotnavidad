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
        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctor_id)->get();

        $diasMap = [
            'LUNES' => 1, 'MARTES' => 2, 'MIÉRCOLES' => 3, 'JUEVES' => 4,
            'VIERNES' => 5, 'SÁBADO' => 6, 'DOMINGO' => 0
        ];

        $eventos = [];

        foreach ($schedules as $horario) {
            // Separar por coma o rango
            $dias = explode(',', str_replace([' Y ', ' A '], [',', ','], strtoupper($horario->days)));
            foreach ($dias as $diaTexto) {
                $diaTexto = trim($diaTexto);
                if (isset($diasMap[$diaTexto])) {
                    $eventos[] = [
                        'title' => 'Disponible',
                        'daysOfWeek' => [$diasMap[$diaTexto]],
                        'startTime' => $horario->available_from,
                        'endTime' => $horario->available_to,
                        'display' => 'background',
                        'color' => '#d3d3d3',
                    ];
                }
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
