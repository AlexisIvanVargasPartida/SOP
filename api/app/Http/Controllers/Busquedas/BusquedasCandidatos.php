<?php

namespace App\Http\Controllers\Busquedas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PadronElectoral;
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
        $coordinador = true;
        if($coordinador){
            $seccion = 435;
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
                ->where('seccion', $seccion)
                ->get(['id', 'seccion', 'tipo'])
                ->toArray();
            $seccionid = DB::table("secciones")->where('clave_entidad_federal', $entidad)
                ->where('clave_municipio', $municipio_id)->where("seccion",$seccion)->first();
            return response()->json(["data" => $data,"seccion"=>$seccionid->id, "coordinador"=>true], 200);
        }else{
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

            return response()->json(["data" => $data,"coordinador"=>false], 200);
        }
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
            ->where("seccion", $seccion->seccion);
            if($request->busqueda!="null"){
                $data->where("cve_elector","LIKE","%$request->busqueda%");
            }
            $data=$data->select("padronelectoral.id", "nombre", "paterno", "materno", "calle", "num_ext", "colonia", "cp", "seccion", "sc.simpatiza","cve_elector")->paginate(15);

        return response()->json(["data" => $data], 200);
    }


}
