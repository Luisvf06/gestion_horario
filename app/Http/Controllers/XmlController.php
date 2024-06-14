<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\FranjaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PeriodoController;

class XmlController extends Controller
{
    public function uploadXML(Request $request)
    {
        set_time_limit(120);
        try {
            if (!$request->hasFile('file')) {
                throw new \Exception('No XML file uploaded.');
            }

            $file = $request->file('file');
            $xmlString = file_get_contents($file->getPathname());
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xml) {
                throw new \Exception('Failed to load XML.');
            }

            Log::info('XML Structure:', ['xml' => $xml->asXML()]);

            $asignaturas = $this->extractTableData($xml, 'asignaturas');
            $grupos = $this->extractTableData($xml, 'grupos');
            $profesores = $this->extractTableData($xml, 'profesores');
            $aulas = $this->extractTableData($xml, 'aulas');
            $franjas = $this->extractTableData($xml, 'franjas');
            $horarios = $this->extractTableData($xml, 'horarios');
            $periodos = $this->extractTableData($xml, 'periodos');

            Log::info('Asignaturas Data:', ['data' => $asignaturas]);
            Log::info('Grupos Data:', ['data' => $grupos]);
            Log::info('Profesores Data:', ['data' => $profesores]);
            Log::info('Aulas Data:', ['data' => $aulas]);
            Log::info('Franjas Data:', ['data' => $franjas]);
            Log::info('Horarios Data:', ['data' => $horarios]);
            Log::info('Periodos Data:', ['data' => $periodos]);

            $userController = new UserController();
            $userController->insertUsers($profesores);

            $asignaturaController = new AsignaturaController();
            $asignaturaController->insertAsignaturas($asignaturas);

            $aulaController = new AulaController();
            $aulaController->insertAulas($aulas);

            $franjaController = new FranjaController();
            $franjaController->insertFranjas($franjas);

            $grupoController = new GrupoController();
            $grupoController->insertGrupos($grupos);

            $periodoController = new PeriodoController();
            $periodoController->insertPeriodos($periodos);
            
            $horarioController = new HorarioController();
            $horarioController->insertHorarios($horarios);

            return response()->json([
                'success' => 'XML parsed successfully',
                'asignaturas' => $asignaturas,
                'grupos' => $grupos,
                'profesores' => $profesores,
                'aulas' => $aulas,
                'franjas' => $franjas,
                'periodos' => $periodos,
                'horarios' => $horarios
                
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error parsing XML: ' . $e->getMessage());
            return response()->json(['error' => 'Error parsing XML: ' . $e->getMessage()], 400);
        }
    }

    private function extractTableData($xml, $tableName)
    {
        $result = [];
        foreach ($xml->database->table as $table) {
            if ((string)$table['name'] === $tableName) {
                $columns = [];
                foreach ($table->column as $column) {
                    $columns[] = (string)$column;
                }
                $result[] = $columns;
            }
        }
        return $result;
    }
}
