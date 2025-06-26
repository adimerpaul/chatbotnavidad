<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 0; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 32px 40px; border-radius: 10px; box-shadow: 0 2px 8px #ccc; }
        .logo { width: 40px; margin-bottom: 10px; }
        h2, h3, h4 { text-align: center; margin: 0 0 10px 0; }
        .info { margin-bottom: 20px; }
        .info p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background: #e9ecef; }
        .footer { margin-top: 30px; font-size: 12px; text-align: right; color: #888; }
    </style>
</head>
<body>
<div class="container">
    <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo">
    <h2>Reporte de Citas</h2>
    <div class="info">
        <p><strong>Doctor:</strong> {{ $doctor->name }}</p>
        <p><strong>Especialidad:</strong> {{ $doctor->specialty ?? 'N/A' }}</p>
        <p><strong>Fecha de atención:</strong> {{ $fecha }}</p>
    </div>
    <table>
        <thead>
        <tr>
            <th>Hora</th>
            <th>Paciente</th>
            <th>Duración</th>
            <th>Observación</th>
        </tr>
        </thead>
        <tbody>
        @foreach($citas as $cita)
            <tr>
                <td>{{ \Carbon\Carbon::parse($cita->fecha_inicio)->format('H:i') }}</td>
                <td>{{ $cita->cliente }}</td>
                <td>{{ \Carbon\Carbon::parse($cita->fecha_inicio)->diffInMinutes(\Carbon\Carbon::parse($cita->fecha_fin)) }} min</td>
                <td>{{ $cita->observacion }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="footer">
        Impreso por: {{ $user->name ?? 'Sistema' }}<br>
        Fecha y hora de impresión: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>
