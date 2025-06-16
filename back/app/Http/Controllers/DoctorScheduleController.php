<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller{
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
