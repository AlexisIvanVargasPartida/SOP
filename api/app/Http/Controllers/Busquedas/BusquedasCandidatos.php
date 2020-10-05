<?php

namespace App\Http\Controllers\Busquedas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PadronElectoral;
use App\Models\SimpatizantesCandidato;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusquedasCandidatos extends Controller
{
    public function entidadesFederativas()
    {
        $data = DB::table('entidades_federales')
            ->get(['id', 'nombre'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }

    public function getColonias($cp)
    {
        $data = DB::table('secciones_colonias')
            ->where('cp', $cp)
            ->groupBy('nombre')
            ->get(['nombre'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }

    public function getColoniasSecciones($cp, $colonia)
    {
        $data = DB::table('secciones_colonias')
            ->where('cp', $cp)
            ->where('nombre', $colonia)
            ->get(['id', 'seccion'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }

    public function candidatoEntidades(Request $request, $id)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id]), [
            'id' => 'required|integer|exists:candidato,id'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $configCandidato = json_decode($candidato->configuacion, true)['registros'];
        $entidades = array_keys($configCandidato);

        $data = DB::table('entidades_federales')
            ->whereIn('id', $entidades)
            ->get(['id', 'nombre'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }


    public function candidatoMunicipios(Request $request, $id, $entidad)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad]), [
            'id' => 'required|integer|exists:candidato,id',
            'entidad' => 'required|integer'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        $data = DB::table('municipios')
            ->where('clave_entidad_federal', $entidad)
            ->whereIn('clave_municipio', $municipios)
            ->get(['id', 'clave_municipio', 'nombre'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }

    public function candidatoSecciones(Request $request, $id, $entidad, $municipio_id)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad, "municipio_id" => $municipio_id]), [
            'id' => 'required|integer|exists:candidato,id',
            'entidad' => 'required|integer',
            'municipio_id' => 'required|integer'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $municipio =  DB::table('municipios')->find($municipio_id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        if (!in_array($municipio->clave_municipio, $municipios)) return response()->json(["data" => "No tiene Permisos del municipio"], 401);

        $data = DB::table('secciones')
            ->where('clave_entidad_federal', $entidad)
            ->where('clave_municipio', $municipio_id)
            ->get(['id', 'seccion', 'tipo'])
            ->toArray();

        return response()->json(["data" => $data], 200);
    }

    public function candidatoPoblacion(Request $request, $id, $entidad, $municipio_id, $seccion_id)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad, "municipio_id" => $municipio_id]), [
            'id' => 'required|integer|exists:candidato,id',
            'entidad' => 'required|integer',
            'municipio_id' => 'required|integer'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $seccion =  DB::table('secciones')->find($seccion_id);
        $municipio =  DB::table('municipios')->find($municipio_id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        if (!in_array($municipio->clave_municipio, $municipios)) return response()->json(["data" => "No tiene Permisos del municipio"], 401);

        $userString = $request->user()->candidato_id;
        $data = PadronElectoral::leftjoin("simpatizantes_candidatos as sc", function ($join) use ($userString) {
                $join->on("sc.padronelectoral_id", "padronelectoral.id")
                    ->where("sc.candidato_id", $userString);
            })
            ->where("entidad", $entidad)
            ->where("municipio", $municipio->clave_municipio)
            ->where("seccion", $seccion->seccion)
            ->select("padronelectoral.id", "nombre", "paterno", "materno", "calle", "num_ext", "colonia", "cp", "seccion", "sc.simpatiza")
            ->paginate(15);

        return response()->json(["data" => $data], 200);
    }

    public function registroPoblacion(Request $request)
    {

        try {
            $getDatos = DB::table('secciones')->where("id", $request->seccion)->first();
            $localidad = DB::table('localidades_secciones')
                ->where("clave_entidad_federal", $getDatos->clave_entidad_federal)
                ->where("clave_municipio", $getDatos->clave_municipio)
                ->where("seccion", $getDatos->seccion)
                ->first();

            $data = $request->except('seccion', 'cve_elector', 'simpatiza', 'year', 'month', 'day', 'data');
            $dataCandidato = $request->only('simpatiza', 'data');
            $dataCandidato["created_by"] = $request->user()->id;
            $dataCandidato["seccion_id"] = $getDatos->id;
            $data["seccion"] = $getDatos->seccion;
            $data["entidad"] = $getDatos->clave_entidad_federal;
            $data["municipio"] = $getDatos->clave_municipio;
            $data["localidad"] = $localidad->localidad;
            $data["nacimiento"] = $request->year . $request->month . $request->day;
            $data["created_by"] = $request->user()->id;

            $claveElector = $request->cve_elector;
            $exist = PadronElectoral::where("cve_elector", "like", $claveElector . "%")->get();
            $claveElector = $claveElector . "000";
            if ($exist) $claveElector = $exist[0]["cve_elector"];

            $row = PadronElectoral::updateOrCreate(['cve_elector' => $claveElector], $data);
            SimpatizantesCandidato::updateOrCreate(
                [
                    'candidato_id' => $request->user()->candidato_id,
                    'padronelectoral_id' => $row->id,
                ],
                $dataCandidato
            );
            return response()->json("Ok", 200);
        } catch (\Throwable $th) {
            return response()->json("Error", 404);
        }
    }
}
