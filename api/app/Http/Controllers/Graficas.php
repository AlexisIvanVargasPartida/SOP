<?php

namespace App\Http\Controllers;

use App\Models\PadronElectoral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Graficas extends Controller
{
    public function candidatoMunicipios(Request $request, $id, $entidad)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["data" => "No tiene Permisos"], 401);

        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad]), [
            'id' => 'required|integer|exists:candidato,id',
            'entidad' => 'required|integer'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        $municipiosData = DB::table('municipios')
            ->where('clave_entidad_federal', $entidad)
            ->whereIn('clave_municipio', $municipios)
            ->get(['id', 'clave_municipio', 'nombre'])
            ->toArray();

        $data = [];
        $userString = $request->user()->candidato_id;
        foreach ($municipiosData as $value) {
            $data["Municipios"][] = $value->nombre;
            $data["Claves"][] = $value->id;
            $data["Simpatizantes"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "SI");
            $data["NoSimpatiza"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "NO");
            $data["NoNosConoce"][] = $this->consultaSimpatizantes($entidad, $value->clave_municipio, $userString, "NO LO CONOZCO");
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
}
