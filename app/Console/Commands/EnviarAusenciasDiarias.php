<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PDFController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnviarAusenciasDiarias extends Command
{
    // Nombre del comando que usará Artisan
    protected $signature = 'EnviarAusenciasDiarias';

    // Descripción del comando
    protected $description = 'Envía las ausencias diarias';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Genera la fecha actual para pasarla como parámetro
        $selectedDate = Carbon::now()->format('Y-m-d');

        // Dirección de correo a la que se enviará el informe
        $recipientEmail = 'luis06031994@gmail.com';

        // Crea una instancia del PDFController
        $pdfController = new PDFController();

        // Crea una solicitud simulada para pasar la fecha seleccionada
        $request = Request::create('/generate-pdf', 'GET', ['date' => $selectedDate]);

        // Llama al método generatePDF del PDFController y pasa la dirección de correo
        $response = $pdfController->generatePDF($request, $recipientEmail);

        // Mensaje de confirmación en la consola
        $this->info($response->getData()->message);
    }
}
