<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class EntidadesFederales extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->nombre,
            'children' => DB::table('municipios')->where('clave_entidad_federal', $this->id)->get([DB::raw('CONCAT (clave_entidad_federal,"-",clave_municipio) as id'), 'nombre as label'])
        ];
    }
}
