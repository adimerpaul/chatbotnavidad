<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller{
    public function index(Request $request)
    {
        return Appointment::with('doctor')->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'cliente' => 'required|string',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:1',
            'observacion' => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse("{$request->fecha} {$request->hora}");
        $end = $start->copy()->addMinutes($request->duracion);

        return Appointment::create([
            'doctor_id' => $request->doctor_id,
            'cliente' => $request->cliente,
            'fecha_inicio' => $start,
            'fecha_fin' => $end,
            'observacion' => $request->observacion,
            'estado' => 'ACTIVA'
        ]);
    }
    function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'cliente' => 'required|string',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:1',
            'observacion' => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse("{$request->fecha} {$request->hora}");
        $end = $start->copy()->addMinutes($request->duracion);

        $appointment->update([
            'doctor_id' => $request->doctor_id,
            'cliente' => $request->cliente,
            'fecha_inicio' => $start,
            'fecha_fin' => $end,
            'observacion' => $request->observacion,
        ]);

        return response()->json(['message' => 'Cita actualizada', 'data' => $appointment]);
    }

    public function cancelar($id)
    {
        $appointment = Appointment::findOrFail($id);
//        $appointment->estado = 'CANCELADA';
//        $appointment->save();
        $appointment->delete();

        return response()->json(['message' => 'Cita cancelada']);
    }
}
