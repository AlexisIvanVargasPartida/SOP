<?php

namespace App\Http\Controllers;

use App\Models\PadronElectoral;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getPoblacion($idCandidato){
        $candidato =  DB::table('candidato')->find($idCandidato);
        $municipios = json_decode($candidato->configuacion, true)['registros'];
        $data = DB::table('municipios')
            ->whereIn('clave_municipio', $municipios)
            ->get(['id', 'clave_municipio', 'nombre'])
            ->toArray();
        $p = 0;
        foreach ($data as $item){
            $municipio = $item->clave_municipio;
            $poblacion = PadronElectoral::where("municipio",$municipio)->count();
            $p = $p+$poblacion;
        }
        return $p;
    }
    public function getSimpatizantes($idCandidato){
        $candidato =  DB::table('simpatizantes_candidatos')
            ->where("candidato_id",$idCandidato)
            ->where("simpatiza","SI")
            ->count();
        return $candidato;
    }

    //obtienes lista de municipios
    public function getMunicipios($idEntidad,$idCandidato){
        $candidato = DB::table("candidato")->find($idCandidato);
        $muns = json_decode($candidato->configuacion, true)['registros'];
        $data = DB::table('municipios')
            ->whereIn('clave_municipio', $muns)
            ->get(['id', 'clave_municipio', 'nombre'])
            ->toArray();
        $municipios = DB::table("municipios")->where("clave_entidad_federal",$idEntidad)->get();
        return ["municipios"=>$municipios,"municipio"=>$data];
    }
    //obtienes lista de secciones por municipio
    public function getSecciones($entidad,$claveMunicipio,$candidato){
        $coordinador = true;
        if($coordinador){
            $sec = 435;
            $secciones = [];
            $nd = [];
            $nnc = [];
            $ns = [];
            $s = [];
            array_push($secciones,"Seccion $sec");
            array_push($nd,$this->consultaSimpatizantes($entidad, $claveMunicipio,$sec, $candidato, "NO DECIDE"));
            array_push($nnc,$this->consultaSimpatizantes($entidad, $claveMunicipio,$sec, $candidato, "NO LO CONOZCO"));
            array_push($ns,$this->consultaSimpatizantes($entidad, $claveMunicipio,$sec, $candidato, "NO"));
            array_push($s,$this->consultaSimpatizantes($entidad, $claveMunicipio,$sec, $candidato, "SI"));
            return ["secciones"=>$secciones, "nd"=>$nd, "nnc"=>$nnc, "ns"=>$ns, "s"=>$s,"pages"=>0,"coordinador"=>true];
        }else{
            $sec = DB::table("secciones")->where("clave_municipio",$claveMunicipio)->paginate(10);
            $seccions = DB::table("secciones")->where("clave_municipio",$claveMunicipio)->count();
            $pages = round($seccions/10);
            $secciones = [];
            $nd = [];
            $nnc = [];
            $ns = [];
            $s = [];
            foreach ($sec as $seccion){
                array_push($secciones,"Seccion $seccion->seccion");
                array_push($nd,$this->consultaSimpatizantes($entidad, $claveMunicipio,$seccion->seccion, $candidato, "NO DECIDE"));
                array_push($nnc,$this->consultaSimpatizantes($entidad, $claveMunicipio,$seccion->seccion, $candidato, "NO LO CONOZCO"));
                array_push($ns,$this->consultaSimpatizantes($entidad, $claveMunicipio,$seccion->seccion, $candidato, "NO"));
                array_push($s,$this->consultaSimpatizantes($entidad, $claveMunicipio,$seccion->seccion, $candidato, "SI"));
            }
            return ["secciones"=>$secciones, "nd"=>$nd, "nnc"=>$nnc, "ns"=>$ns, "s"=>$s,"pages"=>$pages,"coordinador"=>false];

        }
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

}
