<?php

namespace App\Http\Controllers\RH;

use App\EvaluacionRiesgos;
use App\Http\Controllers\Auditoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\RHModels\FactorRiesgo;
use Barryvdh\DomPDF\Facade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FactoresRiesgoController extends Controller
{

    private $PATH = "RH/FactoresRiesgo/";

    /**
     * Registrar
     */
    public function GuardarFactorRiesgo(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $datos = $request->all();
            if ($request->id == null)
            {
                $factorriesgo = new FactorRiesgo($datos);
                $factorriesgo->empleado_registra_id = Auth::user()->empleado_id;
                $factorriesgo->save();
                Auditoria::AuditarCambios($factorriesgo);
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el factor de riesgo");
        }
    }

    /**
     * Obtener todos los registros de factor de riesgo
     */
    public function ObtenerFactorRiesgo()
    {
        try
        {
            $factorRiesgo = DB::table("rh_factores_riesgo as rfr")
                ->join("empleados as e", "e.id", "rfr.empleado_id")
                ->join("puestos as p", "p.id", "rfr.puesto_id")
                ->select(
                    "rfr.id",
                    "rfr.fecha",
                    "rfr.documento",
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as nombre"),
                    "p.nombre as puesto"
                )
                ->get();
            return Status::Success("factorriesgo", $factorRiesgo);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los factores de riesgo");
        }
    }

    /**
     * Guardar cuestionario en pdf
     */
    public function GuardarEvidencia(Request $request)
    {
        try
        {
            if (!$request->ajax()) return;
            $factorRiesgo = FactorRiesgo::find($request->id);
            $uid = uniqid() . ".pdf";
            Storage::disk("local")->put(
                $this->PATH . $uid,
                fopen($request->file("evidencia"), 'r+')
            );

            $factorRiesgo->documento = $uid; // nombre del documento guardado
            Auditoria::AuditarCambios($factorRiesgo);
            $factorRiesgo->update();

            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar la evidencia");
        }
    }

    /**
     * 
     */
    public function DescargarCuestionario($id)
    {
        try
        {
            // Buscar evaluación de factores de riesgo
            $conceptos = [
                [
                    "concepto" => "Para responder las preguntas siguientes considere las condiciones ambientales de su centro de trabajo.",
                    "preguntas" => [
                        "1. El espacio donde trabajo me permite realizar mis actividades de manera segura e higiénica",
                        "2. Mi trabajo me exige hacer mucho esfuerzo físico",
                        "3. Me preocupa sufrir un accidente en mi trabajo",
                        "4. Considero que en mi trabajo se aplican las normas de seguridad y salud en el trabajo",
                        "5.Considero que las actividades que realizo son peligrosas"
                    ]
                ],
                [
                    "concepto" => "Para responder a las preguntas siguientes piense en la cantidad y ritmo de trabajo que tiene.",
                    "preguntas" => [
                        "6. Por la cantidad de trabajo que tengo debo quedarme tiempo adicional a mi turno",
                        "7. Por la cantidad de trabajo que tengo debo trabajar sin parar",
                        "8. Considero que es necesario mantener un ritmo de trabajo acelerado",
                    ]
                ],
                [
                    "concepto" => "Las preguntas siguientes están relacionadas con el esfuerzo mental que le exige su trabajo.",
                    "preguntas" => [
                        "9. Mi trabajo exige que esté muy concentrado",
                        "10. Mi trabajo requiere que memorice mucha información",
                        "11. En mi trabajo tengo que tomar decisiones difíciles muy rápido",
                        "12. Mi trabajo exige que atienda varios asuntos al mismo tiempo",
                    ]
                ],
                [
                    "concepto" => "Las preguntas siguientes están relacionadas con las actividades que realiza en su trabajo y las 
                responsabilidades que tiene.",
                    "preguntas" => [
                        "13. En mi trabajo soy responsable de cosas de mucho valor",
                        "14. Respondo ante mi jefe por los resultados de toda mi área de trabajo",
                        "15. En el trabajo me dan órdenes contradictorias",
                        "16. Considero que en mi trabajo me piden hacer cosas innecesarias"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con su jornada de trabajo.",
                    "preguntas" => [
                        "17. Trabajo horas extras más de tres veces a la semana",
                        "18. Mi trabajo me exige laborar en días de descanso, festivos o fines de semana",
                        "19. Considero que el tiempo en el trabajo es mucho y perjudica mis actividades familiares o personales",
                        "20. Debo atender asuntos de trabajo cuando estoy en casa",
                        "21. Pienso en las actividades familiares o personales cuando estoy en mi trabajo",
                        "22. Pienso que mis responsabilidades familiares afectan mi trabajo "
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con las decisiones que puede tomar en su trabajo.",
                    "preguntas" => [
                        "23. Mi trabajo permite que desarrolle nuevas habilidades ",
                        "24. En mi trabajo puedo aspirar a un mejor puesto",
                        "25. Durante mi jornada de trabajo puedo tomar pausas cuando las necesito",
                        "26. Puedo decidir cuánto trabajo realizo durante la jornada laboral",
                        "27. Puedo decidir la velocidad a la que realizo mis actividades en mi trabajo",
                        "28. Puedo cambiar el orden de las actividades que realizo en mi trabajo"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con cualquier tipo de cambio que ocurra en su trabajo 
                (considere los últimos cambios realizados).",
                    "preguntas" => [
                        "29. Los cambios que se presentan en mi trabajo dificultan mi labor ",
                        "30. Cuando se presentan cambios en mi trabajo se tienen en cuenta mis ideas o aportaciones	"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con la capacitación e información que se le proporciona 
                sobre su trabajo.",
                    "preguntas" => [
                        "32. Me explican claramente los resultados que debo obtener en mi trabajo",
                        "33. Me explican claramente los objetivos de mi trabajo",
                        "34. Me informan con quién puedo resolver problemas o asuntos de trabajo",
                        "35. Me permiten asistir a capacitaciones relacionadas con mi trabajo"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con el o los jefes con quien tiene contacto.",
                    "preguntas" => [
                        "36. Recibo capacitación útil para hacer mi trabajo",
                        "37. Mi jefe ayuda a organizar mejor el trabajo",
                        "38. Mi jefe tiene en cuenta mis puntos de vista y opiniones",
                        "39. Mi jefe me comunica a tiempo la información relacionada con el trabajo",
                        "40. La orientación que me da mi jefe me ayuda a realizar mejor mi trabajo",
                        "41. Mi jefe ayuda a solucionar los problemas que se presentan en el trabajo"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes se refieren a las relaciones con sus compañeros.",
                    "preguntas" => [
                        "42. Puedo confiar en mis compañeros de trabajo",
                        "43. Entre compañeros solucionamos los problemas de trabajo de forma respetuosa",
                        "44. En mi trabajo me hacen sentir parte del grupo",
                        "45. Cuando tenemos que realizar trabajo de equipo los compañeros colaboran",
                        "46. Mis compañeros de trabajo me ayudan cuando tengo dificultades"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con la información que recibe sobre su rendimiento 
                en el trabajo, el reconocimiento, el sentido de pertenencia y la estabilidad que le ofrece su trabajo.",
                    "preguntas" => [
                        "47. Me informan sobre lo que hago bien en mi trabajo",
                        "48. La forma como evalúan mi trabajo en mi centro de trabajo me ayuda a mejorar mi desempeño",
                        "49. En mi centro de trabajo me pagan a tiempo mi salario",
                        "50. El pago que recibo es el que merezco por el trabajo que realizo",
                        "51. Si obtengo los resultados esperados en mi trabajo me recompensan o reconocen",
                        "52. Las personas que hacen bien el trabajo pueden crecer laboralmente",
                        "53. Considero que mi trabajo es estable",
                        "54. En mi trabajo existe continua rotación de personal",
                        "55. Siento orgullo de laborar en este centro de trabajo",
                        "56. Me siento comprometido con mi trabajo"
                    ]
                ],

                [
                    "concepto" => "Las preguntas siguientes están relacionadas con actos de violencia laboral (malos tratos, acoso, 
                hostigamiento, acoso psicológico).",
                    "preguntas" => [
                        "57.En mi trabajo puedo expresarme libremente sin interrupciones",
                        "58. Recibo críticas constantes a mi persona y/o trabajo",
                        "59. Recibo burlas, calumnias, difamaciones, humillaciones o ridiculizaciones",
                        "60. Se ignora mi presencia o se me excluye de las reuniones de trabajo y en la toma de decisiones",
                        "61. Se manipulan las situaciones de trabajo para hacerme parecer un mal trabajador",
                        "62. Se ignoran mis éxitos laborales y se atribuyen a otros trabajadores",
                        "63. Me bloquean o impiden las oportunidades que tengo para obtener ascenso o mejora en mi trabajo",
                        "64. He presenciado actos de violencia en mi centro de trabajo"
                    ]
                ]
            ];

            // 2
            $conceptos2 = [
                [
                    "concepto" => "Las preguntas siguientes están relacionadas con la atención a clientes y usuarios.",
                    "preguntas" =>
                    [
                        "65. Atiendo clientes o usuarios muy enojados",
                        "66. Mi trabajo me exige atender personas muy necesitadas de ayuda o enfermas",
                        "67. Para hacer mi trabajo debo demostrar sentimientos distintos a los míos",
                        "68. Mi trabajo me exige atender situaciones de violencia"
                    ]
                ]
            ];

            // 3
            $conceptos3 = [
                [
                    "concepto" => "Las preguntas siguientes están relacionadas con las actitudes de las personas que supervisa.",
                    "preguntas" =>
                    [
                        "69. Comunican tarde los asuntos de trabajo",
                        "70. Dificultan el logro de los resultados del trabajo",
                        "71. Cooperan poco cuando se necesita",
                        "72. Ignoran las sugerencias para mejorar su trabajo"
                    ]
                ]
            ];

            $evaluacion = DB::table("rh_factores_riesgo as rfr")
                ->join("empleados as e", "e.id", "rfr.empleado_id")
                ->join("puestos as p", "p.id", "rfr.puesto_id")
                ->select(
                    DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as empleado"),
                    "p.nombre as puesto",
                    "rfr.fecha"
                )
                ->where("rfr.id", $id)->first();
            // dd($evaluacion);
            $pdf = Facade::loadView("pdf.rh.factoresderiesgo", compact("evaluacion", "conceptos", "conceptos2", "conceptos3"));
            $pdf->setPaper("letter", "portrait");
            $pdf->getDomPDF()->set_option("enable_php", true);
            return $pdf->stream("CUESTIONARIO DE IDENTIFICIÓN DE FACTORES DE RIESGO PSICOSOCIAL Y  ENTORNO ORGANIZACIONAL");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }

    /**
     * Descargar evidencia
     */
    public function DescargarEvidencia($id)
    {
        try
        {
            // buscar platica
            $evaluacion = FactorRiesgo::find($id);
            $nombre = $evaluacion->documento;
            $path = "app/" . $this->PATH . $nombre;
            $doc = storage_path($path);
            if (file_exists($doc))
            {
                return response()->download($doc, $nombre, [
                    'Content-Type' => "application/pdf",
                    'Content-Disposition' => 'inline; filename="' . $nombre . '"'
                ]);
            }
            return view("errors.404");
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            return view("errors.500");
        }
    }
}
