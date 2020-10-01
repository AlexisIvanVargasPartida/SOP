<?php

namespace App\Http\Controllers;

use App\Candidato;
use App\Http\Resources\EntidadesFederales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Registro extends Controller
{
    public function treeEntidades()
    {
        $entidades = DB::table('entidades_federales')->select('id', 'nombre as text', DB::raw('(select "#") as parent'))->get()->toArray();
        $municipios = DB::table('municipios')->select(DB::raw('CONCAT (clave_entidad_federal,"-",clave_municipio) as id'), 'nombre as text', 'clave_entidad_federal as parent')->get()->toArray();
        $data = array_merge($entidades, $municipios);
        return response()->json($data);
    }

    public function registro(Request $request)
    {   
        Validator::make($request->all(), [
            'email'         => 'string|email',
            'nombre'      => 'required|string',
            'partido'      => 'required|string',
            'ce'      => 'required|string',
            'password'      => 'required|string'
        ])->validate();

        $jsonData = [
            "partido" => $request->partido,
            "ce" => $request->ce,
            "registros" => $this->generaArrayRegistros($request->values)
        ];

        $candidato = Candidato::create(['nombre' => $request->nombre, 'configuacion' => json_encode($jsonData)]);
        User::create(['name' => $request->nombre, 'email' => $request->email, 'username' => null, 'password' => bcrypt($request->password), 'candidato_id' => $candidato->id, "role_id" => 1]);

        return response()->json("Ok", 200);
    }

    public function entidadesMunicipios()
    {
        $entidades = DB::table('entidades_federales')->get();
        return EntidadesFederales::collection($entidades);
    }

    public function generaArrayRegistros($values)
    {
        $valuesReturn = [];
        //$datas = explode(",", $values);
        for ($i = 0; $i < count($values); $i++) {
            if (strpos($values[$i], "-")) {
                $key = explode("-", $values[$i]);
                $valuesReturn[$key[0]][] = $key[1];
            } else {
                $valuesReturn[$values[$i]] = [];
            }
        }
        return $valuesReturn;
    }
}
