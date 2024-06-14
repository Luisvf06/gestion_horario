<? php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class XmlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
    * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'xml' => 'required|file|mimes:xml|max:10240', // 10MB max file size
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'xml.required' => 'El archivo XML es requerido.',
            'xml.file' => 'El archivo debe ser un archivo XML.',
            'xml.mimes' => 'El archivo debe ser un archivo XML.',
            'xml.max' => 'El archivo es demasiado grande. El tamaño máximo permitido es 10MB.',
        ];
    }

    /**
     * Validate the XML file contents.
     *
     * @return void
     */
    public function validateXmlContents()
    {
        $xml = file_get_contents($this->file('xml')->getRealPath());
        $reader = XmlReader::fromString($xml);

        // Valida la estructura del xml
        if (!$reader->xpathValue('//profesores')) {
            return redirect()->back()->withErrors('El archivo XML no contiene la estructura esperada.');
        }

        // Revisa si existe el rol profesorado
        $roleProfesorado = Role::where('name', 'profesorado')->first();
        if (!$roleProfesorado) {
            return redirect()->back()->withErrors('Rol de "profesorado" no encontrado.');
        }

        // valida asignatura_cod
        $asignaturas = $reader->xpathValue('//asignaturas/asignatura');
        $asignaturaCodes = [];
        foreach ($asignaturas as $asignatura) {
            $asignaturaCode = $asignatura->xpathValue('asignatura_cod');
            if (!preg_match('/^[a-zA-Z]{3}\d$/', $asignaturaCode)) {
                return redirect()->back()->withErrors("Código de asignatura inválido: {$asignaturaCode}");
            }
            if (in_array($asignaturaCode, $asignaturaCodes)) {
                return redirect()->back()->withErrors("Código de asignatura repetido: {$asignaturaCode}");
            }
            $asignaturaCodes[] = $asignaturaCode;
        }

        // Validate aula_cod
        $aulas = $reader->xpathValue('//aulas/aula');
        $aulaCodes = [];
        foreach ($aulas as $aula) {
            $aulaCode = $aula->xpathValue('aula_cod');
            if (!preg_match('/^[0-9]{3}$/', $aulaCode) && !preg_match('/^[a-zA-Z][0-9]{1}$/', $aulaCode)) {
                return redirect()->back()->withErrors("Código de aula inválido: {$aulaCode}");
            }
            if (in_array($aulaCode, $aulaCodes)) {
                return redirect()->back()->withErrors("Código de aula duplicado: {$aulaCode}");
            }
            $aulaCodes[] = $aulaCode;
        }

        // Validate franja_cod
        $franjas = $reader->xpathValue('//franjas/franja');
        $franjaCodes = [];
        foreach ($franjas as $franja) {
            $franjaCode = $franja->xpathValue('franja_cod');
            if (!is_numeric($franjaCode)) {
                return redirect()->back()->withErrors("Código de franja inválido: {$franjaCode}");
            }
            if (in_array($franjaCode, $franjaCodes)){
                return redirect()->back()->withErrors("Código de franja duplicado: {$franjaCode}");
            }
            $franjaCodes[] = $franjaCode;
        }

        // Validate grupo_cod
        $grupos = $reader->xpathValue('//grupos/grupo');
        $grupoCodes = [];
        foreach ($grupos as $grupo) {
            $grupoCode = $grupo->xpathValue('grupo_cod');
            if (in_array($grupoCode, $grupoCodes)) {
                return redirect()->back()->withErrors("Código de grupo duplicado: {$grupoCode}");
            }
            $grupoCodes[] = $grupoCode;
        }

        // Validate horario_cod
        $horarios = $reader->xpathValue('//horarios/horario');
        $horarioCodes = [];
        foreach ($horarios as $horario) {
            $horarioCode = $horario->xpathValue('horario_cod');
            if (!is_numeric($horarioCode)) {
                return redirect()->back()->withErrors("Código de horario inválido: {$horarioCode}");
            }
            if (in_array($horarioCode, $horarioCodes)) {
                return redirect()->back()->withErrors("Código de horario duplicado: {$horarioCode}");
            }
            $horarioCodes[] = $horarioCode;
        }

        // Validate periodo_cod
        $periodos = $reader->xpathValue('//periodos/periodo');
        $periodoCodes = [];
        foreach ($periodos as $periodo) {
            $periodoCode = $periodo->xpathValue('periodo_cod');
            if (!is_numeric($periodoCode)) {
                return redirect()->back()->withErrors("Código de periodo inválido: {$periodoCode}");
            }
            if (in_array($periodoCode, $periodoCodes)) {
                return redirect()->back()->withErrors("Código de periodo duplicado: {$periodoCode}");
            }
            $periodoCodes[] = $periodoCode;
        }

        // Validate fecha_desde and fecha_hasta
        $fechas = $reader->xpathValue('//periodos/periodo');
        foreach ($fechas as $fecha) {
            $fechaDesde = $fecha->xpathValue('fecha_desde');
            $fechaHasta = $fecha->xpathValue('fecha_hasta');
            if (!Carbon::parse($fechaDesde)) {
                return redirect()->back()->withErrors("Fecha de inicio inválida: {$fechaDesde}");
            }
            if (!Carbon::parse($fechaHasta)) {
                return redirect()->back()->withErrors("Fecha de fin inválida: {$fechaHasta}");
            }
            if (Carbon::parse($fechaDesde) > Carbon::parse($fechaHasta)) {
                return redirect()->back()->withErrors("Fecha de inicio debe ser anterior a la fecha de fin");
            }
        }

        // Validate profesor_cod
        $profesores = $reader->xpathValue('//profesores/profesor');
        $profesorCodes = [];
        foreach ($profesores as $profesor) {
            $profesorCode = $profesor->xpathValue('profesor_cod');
            if (strlen($profesorCode) != 3) {
                return redirect()->back()->withErrors("Código de profesor inválido: {$profesorCode}");
            }
            if (in_array($profesorCode, $profesorCodes)) {
                return redirect()->back()->withErrors("Código de profesor duplicado: {$profesorCode}");
            }
            $profesorCodes[] = $profesorCode;
        }
    }
}