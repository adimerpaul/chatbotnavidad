<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller{
    public function select()
    {
        return Doctor::select('id', 'name')->orderBy('name')->get();
    }
    public function index()
    {
        return Doctor::with('schedules')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'specialty' => 'required|string',
        ]);

        $doctor = Doctor::create([
            'name' => $request->name,
            'specialty' => $request->specialty,
            'phone' => $request->phone ?? null,
        ]);

        return response()->json($doctor->load('schedules'), 201);
    }

    public function show($id)
    {
        return Doctor::with('schedules')->findOrFail($id);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string',
            'specialty' => 'required|string',
        ]);

        $doctor->update([
            'name' => $request->name,
            'specialty' => $request->specialty,
            'phone' => $request->phone ?? null,
        ]);

        return response()->json($doctor->load('schedules'));
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return response()->json(['message' => 'Doctor eliminado']);
    }
}
