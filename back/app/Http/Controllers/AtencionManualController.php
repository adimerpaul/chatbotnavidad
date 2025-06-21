<?php

namespace App\Http\Controllers;

use App\Models\AtencionManual;
use App\Models\History;
use Illuminate\Http\Request;

class AtencionManualController extends Controller
{
    /**
     * Obtener teléfonos únicos del historial con su estado de atención.
     */
    public function number()
    {
        $phones = History::whereNotNull('phone')
            ->select('phone')
            ->groupBy('phone')
            ->get();

        $data = $phones->map(function ($item) {
            return [
                'phone' => $item->phone,
                'en_atencion' => AtencionManual::where('phone', $item->phone)->exists(),
            ];
        });

        return response()->json($data);
    }

    /**
     * Agregar un número a la tabla de atención manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'hora_atencion' => 'nullable|date',
        ]);

        $atencion = AtencionManual::updateOrCreate(
            ['phone' => $request->phone],
            ['hora_atencion' => $request->hora_atencion ?? now()]
        );

        return response()->json([
            'message' => 'Número agregado a atención manual',
            'data' => $atencion
        ]);
    }

    /**
     * Quitar un número de la tabla de atención manual.
     */
    public function destroy($phone)
    {
        $deleted = AtencionManual::where('phone', $phone)->delete();

        return response()->json([
            'message' => $deleted ? 'Número quitado de atención manual' : 'Número no encontrado',
        ]);
    }

    /**
     * (Opcional) Verificar si un número está siendo atendido.
     */
    public function show($phone)
    {
        $atencion = AtencionManual::where('phone', $phone)->first();

        return response()->json([
            'phone' => $phone,
            'en_atencion' => !!$atencion,
            'datos' => $atencion
        ]);
    }
}
