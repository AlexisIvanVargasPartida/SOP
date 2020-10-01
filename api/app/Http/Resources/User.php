<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class User extends JsonResource
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
            'nombre' => $this->name,
            'rol' => DB::table('roles')->where('id', $this->role_id)->first()->nombre,
            'idcandidato' => $this->candidato_id,
            'candidato' => DB::table('candidato')->where('id', $this->candidato_id)->first()->nombre,
        ];
    }
}
