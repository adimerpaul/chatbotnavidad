<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $start->addMinutes(2);
        $end = $start->copy()->addMinutes($request->duracion);
        $end->subMinutes(2);

        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where(function ($query) use ($start, $end) {
                $query->where('fecha_inicio', '<', $end)
                    ->where('fecha_fin', '>', $start);
            })
            ->exists();
        if ($existingAppointment) {
            $citas = Appointment::where('doctor_id', $request->doctor_id)
                ->whereDate('fecha_inicio', $request->fecha)
                ->orderBy('fecha_inicio')
                ->get(['fecha_inicio', 'fecha_fin']);

            return response()->json([
                'message' => 'El horario seleccionado ya está ocupado. Por favor, elija otro horario disponible.',
                'ocupados' => $citas
            ], 409);
        }

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
    public function reporteDiario(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'fecha' => 'required|date',
        ]);

        $doctor = \App\Models\Doctor::findOrFail($request->doctor_id);
        $citas = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('fecha_inicio', $request->fecha)
            ->orderBy('fecha_inicio')
            ->get();
//        return $citas;
        $user = auth()->user();

        $pdf = Pdf::loadView('reports.reporte_citas', [
            'doctor' => $doctor,
            'citas' => $citas,
            'fecha' => $request->fecha,
            'user' => $user
        ]);



        return $pdf->stream('reporte_citas_'.$doctor->id.'_'.$request->fecha.'.pdf', [
            'Attachment' => false // Para mostrar en el navegador
        ]);
    }
}
