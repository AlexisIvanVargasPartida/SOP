<?php

namespace App\Http\Controllers;

use App\Models\PadronElectoral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Graficas extends Controller
{
    public function candidatoMunicipios(Request $request, $id, $entidad, $filter)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad, "filtro" => $filter]), [
            'id' => 'required|integer|exists:candidato,id',
            'entidad' => 'required|integer',
            'filtro' => 'required|string|in:all,simpatizantes,nosimpatizantes,noconocen,nodeciden'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        $municipiosData = DB::table('municipios')
            ->where('clave_entidad_federal', $entidad)
            ->whereIn('clave_municipio', $municipios)
            ->get(['id', 'clave_municipio', 'nombre'])
            ->toArray();

        $data = [];
        $tipoFiltro = ["simpatizantes" => "SI", "nosimpatizantes" => "NO", "noconocen" => "NO LO CONOZCO", "nodeciden" => "NO DECIDE",];
        $userString = $request->user()->candidato_id;
        foreach ($municipiosData as $value) {
            $data["Municipios"][] = $value->id;
            $data["idMunicipios"][$value->id] = $value->nombre;
            if ($filter == "all") {
                $data["Simpatizantes"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "SI");
                $data["NoSimpatiza"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "NO");
                $data["NoNosConoce"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "NO LO CONOZCO");
                $data["NoDecide"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "NO DECIDE");
            } else {
                $data["filter"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, $tipoFiltro[$filter]);
            }
        }
        if ($filter != "all") {
            $data["poblacion"][] = $this->consultaSimpatizantesData($entidad, $municipios, $userString, $tipoFiltro[$filter]);
        }
        return response()->json(["data" => $data], 200);
    }

    public function consultaSimpatizantes($entidad, $clave_municipio, $user, $simpatiza)
    {
        $count = PadronElectoral::join("simpatizantes_candidatos as sc", function ($join) use ($user) {
            $join->on("sc.padronelectoral_id", "padronelectoral.id")
                ->where("sc.candidato_id", $user);
        })
            ->where("entidad", $entidad)
            ->where("municipio", $clave_municipio)
            ->where("sc.simpatiza", $simpatiza)
            ->count();
        return $count;
    }

    public function consultaSimpatizantesData($entidad, $municipios, $candidato_id, $simpatiza)
    {
        $data = PadronElectoral::leftjoin("simpatizantes_candidatos as sc", function ($join) use ($candidato_id) {
            $join->on("sc.padronelectoral_id", "padronelectoral.id")
                ->where("sc.candidato_id", $candidato_id);
        })
            ->where("entidad", $entidad)
            ->whereIn('municipio', $municipios)
            ->where("sc.simpatiza", $simpatiza)
            ->select("padronelectoral.id", "nombre", "paterno", "materno", "calle", "num_ext", "colonia", "cp", "seccion", "sc.data", "sc.created_at as fechacaptura")
            ->paginate(15);
        return $data;
    }
}
