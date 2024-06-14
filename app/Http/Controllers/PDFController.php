<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use PDF;
use App\Models\Ausencia;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Horario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AusenciaMail;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        $selectedDate = $request->query('date');
        $selectedWeek = Carbon::parse($selectedDate)->dayOfWeek;
    
        // No incluyo domingos y sábados porque no tienen sentido
        if ($selectedWeek == 0 || $selectedWeek == 6) {
            return response()->json(['message' => 'No se generan reportes de los fines de semana.'], 400);
        }
    
        $weekDays = [
            1 => 'L',
            2 => 'M',
            3 => 'X',
            4 => 'J',
            5 => 'V',
        ];
    
        $selectedWeekLetter = $weekDays[$selectedWeek] ?? 'No laboral';
    
        $ausencias = Ausencia::with([
                'user',
                'user.horarios.aula', 
                'user.horarios.grupo'
            ])
            ->whereDate('fecha', $selectedDate)
            ->get();
    
        $data = [
            'title' => 'Lista de ausencias del día, ' . $selectedDate,
            'date' => $selectedDate,
            'user_name' => Auth::user()->name, 
            'ausencias' => [],
        ];
    
        foreach ($ausencias as $ausencia) {
            $horario = $ausencia->user->horarios->firstWhere('dia', $selectedWeekLetter);
            $data['ausencias'][] = [
                'ausencia' => $ausencia,
                'horario' => $horario,
                'aula' => $horario ? $horario->aula : null,
                'grupo' => $horario ? $horario->grupo : null,
            ];
        }
    
        $pdf = PDF::loadView('pdf.myPDF', $data);
        $pdfPath = storage_path('app/public/ausencias_hoy.pdf');
        $pdf->save($pdfPath);
    
        // Enviar el correo
        Mail::to(Auth::user()->email)->send(new AusenciaMail($data, $pdfPath));
    
        return response()->json(['message' => 'PDF generado y enviado al correo electrónico del usuario logueado.']);
    }
    
}
