<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/registro', 'Registro@registro');
Route::get('/registro/entidades', 'Registro@entidadesMunicipios');
Route::post('/login', 'Users\UsersController@login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/logout', 'Users\UsersController@logout');
    Route::get('/entidades', 'Busquedas\BusquedasCandidatos@entidadesFederativas');
    Route::get('/colonias/{cp}', 'Busquedas\BusquedasCandidatos@getColonias');
    Route::get('/secciones/{cp}/{colonia}', 'Busquedas\BusquedasCandidatos@getColoniasSecciones');
    Route::post('/registro/poblacion', 'Simpatizantes@registroPoblacion');
    Route::post('/registro/simpatizante', 'Simpatizantes@registroSimpatizante');

    Route::get('/candidato/{id}/entidades', 'Busquedas\BusquedasCandidatos@candidatoEntidades');
    Route::get('/candidato/{id}/{entidad}/municipios', 'Busquedas\BusquedasCandidatos@candidatoMunicipios');
    Route::get('/candidato/{id}/{entidad}/{municipio_id}/secciones', 'Busquedas\BusquedasCandidatos@candidatoSecciones');
    Route::get('/candidato/{id}/{entidad}/{municipio_id}/{seccion_id}/poblacion', 'Busquedas\BusquedasCandidatos@candidatoPoblacion');
    
    //GRAFICAS
    Route::get('/candidato/{id}/{entidad}/grafica/municipios/{filter}', 'Graficas@candidatoMunicipios');

    //TEST AUTH
    Route::get('/user/{id}', function (Request $request, $id) {
        if ($request->user()->tokenCan('user.view') || $request->user()->id == $id) return $request->user();
        return response()->json(["data" => "No tiene Permisos"], 401);
    });
});
