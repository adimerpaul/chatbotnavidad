<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index()
    {
        return Pregunta::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:255',
            'respuesta' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $pregunta = Pregunta::create($request->all());
        return response()->json($pregunta, 201);
    }

    public function show(Pregunta $pregunta)
    {
        return $pregunta;
    }

    public function update(Request $request, Pregunta $pregunta)
    {
        $request->validate([
            'pregunta' => 'required|string|max:255',
            'respuesta' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $pregunta->update($request->all());
        return response()->json($pregunta);
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();
        return response()->json(['message' => 'Pregunta eliminada']);
    }
}
