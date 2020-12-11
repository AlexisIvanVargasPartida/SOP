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

    public function candidatoMunicipiosFiltro(Request $request, $id, $entidad, $municipio, $filter)
    {
        if ($request->user()->candidato_id != $id) return response()->json(["error" => "No tiene Permisos"], 401);
        Validator::make(array_merge($request->all(), ["id" => $id, "entidad" => $entidad, "municipio" => $municipio, "filtro" => $filter]), [
            'id' => 'required|integer|exists:candidato,id',
            'municipio' => 'required|integer',
            'entidad' => 'required|integer',
            'filtro' => 'required|string|in:all,simpatizantes,nosimpatizantes,noconocen,nodeciden'
        ])->validate();

        $candidato =  DB::table('candidato')->find($id);
        $municipios = json_decode($candidato->configuacion, true)['registros'][$entidad];

        if (!in_array($municipio, $municipios)) return response()->json(["error" => "No tiene Permisos del municipio"], 401);

        $municipiosData = DB::table('municipios')
            ->where('clave_entidad_federal', $entidad)
            ->where('clave_municipio', $municipio)
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
        if ($filter == "all") {
            $data["poblacion"][] = $this->consultaSimpatizantesData($entidad, [$municipio], $userString, null, false);
        } else {
            $data["poblacion"][] = $this->consultaSimpatizantesData($entidad, [$municipio], $userString, $tipoFiltro[$filter]);
        }
        return response()->json(["data" => $data], 200);
    }

    public function consultaSimpatizantes($entidad, $clave_municipio,$seccion, $user, $simpatiza)
    {
        $count = PadronElectoral::join("simpatizantes_candidatos as sc", function ($join) use ($user) {
            $join->on("sc.padronelectoral_id", "padronelectoral.id")
                ->where("sc.candidato_id", $user);
        })
            ->where("entidad", $entidad)
            ->where("municipio", $clave_municipio)
            ->where("seccion", $seccion)
            ->where("sc.simpatiza", $simpatiza)
            ->count();
        return $count;
    }

    public function consultaSimpatizantesData($entidad, $municipios, $candidato_id, $simpatiza, $filter = true)
    {
        $data = PadronElectoral::leftjoin("simpatizantes_candidatos as sc", function ($join) use ($candidato_id) {
            $join->on("sc.padronelectoral_id", "padronelectoral.id")
                ->where("sc.candidato_id", $candidato_id);
        })
            ->where("entidad", $entidad)
            ->whereIn('municipio', $municipios)
            ->when($filter == true, function ($q) use ($simpatiza) {
                return $q->where("sc.simpatiza", $simpatiza);
            })
            ->when($filter == false, function ($q) use ($simpatiza) {
                return $q->where("sc.simpatiza", "<>", $simpatiza);
            })
            ->select("padronelectoral.id", "nombre", "paterno", "materno", "calle", "num_ext", "colonia", "cp", "seccion", "sc.data", "sc.created_at as fechacaptura")
            ->paginate(15);
        return $data;
    }

    public function consultaSimpatizantesDataSeccion(Request $request, $id, $entidad, $municipio_id, $seccion_id, $filter)
    {
        $seccion =  DB::table('secciones')->find($seccion_id);
        if (!$seccion) return response()->json(["data" => "Error de datos!"], 401);
        $data = PadronElectoral::leftjoin("simpatizantes_candidatos as sc", function ($join) use ($id) {
            $join->on("sc.padronelectoral_id", "padronelectoral.id")
                ->where("sc.candidato_id", $id);
        })
            ->where("entidad", $entidad)
            ->where('municipio', $municipio_id)
            ->where('seccion', $seccion->seccion)
            ->when($filter != "all", function ($q) use ($filter) {
                return $q->where("sc.simpatiza", $filter);
            })
            ->when($filter == "all", function ($q) {
                return $q->where("sc.simpatiza", "<>", null);
            })
            ->select("padronelectoral.id", "nombre", "paterno", "materno", "calle", "num_ext", "colonia", "cp", "seccion", "sc.data", "sc.created_at as fechacaptura")
            ->paginate(15);
        return $data;
    }
}
