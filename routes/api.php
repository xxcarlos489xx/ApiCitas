<?php

use Illuminate\Http\Request;
use App\Cita;
use App\Consultorio;
use App\Empleado;
use App\Especialidad;
use App\Estado;
use App\Medico;
use App\Paciente;
use App\Usuario;
use Illuminate\Support\Facades\DB;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*==========================
 * LOGIN
 ===========================*/
//http://127.0.0.1:8000/api/users?dni=45589149&password=123456&tipo=1
/*Route::get('users',function (Request $request){

	$dni = $request->input('dni');
	$pass = $request->input('password');
	$tipo = $request->input('tipo');

	switch ($tipo) {
		case 1:
			$consulta = Paciente::where('dni',$dni)->first();
			break;
		case 2:
			$consulta = Medico::where('dni',$dni)->first();
			break;
		case 3:
			$consulta = Empleado::where('dni',$dni)->first();
			break;
	}
	$response["dato"]=array('dato' => $consulta);
	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});*/

Route::post('/login', function (Request $request) {
	return json_encode($request->getContent(), true);
	/*$dni = $request->input('dni');
	$pass = $request->input('password');
	$tipo = $request->input('tipo');

	switch ($tipo) {
		case 1:
			$consulta = Paciente::where('dni',$dni)->first();
			break;
		case 2:
			$consulta = Medico::where('dni',$dni)->first();
			break;
		case 3:
			$consulta = Empleado::where('dni',$dni)->first();
			break;
	}
	$response["dato"]=array('dato' => $consulta);
	if ($consulta) {
			echo json_encode($response["dato"]);
	}	*/
	
});

/*==================================
 * LISTADO DE CITAS, POR PACIENTE
 ===================================*/
// http://127.0.0.1:8000/api/citas/1
Route::get('/citas/{id}',function ($id, Request $request){

	$consulta = DB::table('cita')
                   	->select('especialidad_id','fecha','hora')
                   	->where('paciente_id', $id)
                    ->get();

	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {

			echo json_encode($response["dato"]);
	}	
	
});

/*==================================
 * DETALLE DE CITAS, POR PACIENTE
 ===================================*/
//http://127.0.0.1:8000/api/cita/1
Route::get('/cita/{id}',function ($id, Request $request){

	$consulta = Cita::where('id',$id)->first();
	$response["dato"]=array('dato' => $consulta);
	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==================================
 * SPINNER LISTA ESPECIALIDAD
 ===================================*/
//http://127.0.0.1:8000/api/especialidades
Route::get('/especialidades',function (){

	$consulta = Especialidad::select('id','nombre')->first();
	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==============================================
 * SPINNER LISTA MEDICOS POR ID DE ESPECIALIDAD
 ==============================================*/
//http://127.0.0.1:8000/api/medicos/1
Route::get('/medicos/{id}',function ($id){

	$consulta = Medico::select('id','nombres','apellidos','consultorio_id','especialidad_id')->where('especialidad_id',$id)->get();

	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==============================================
 * SPINNER LISTA CONSULTORIOS
 ==============================================*/
//http://127.0.0.1:8000/api/consultorios/1/1
Route::get('/consultorios/{numero}/{id}',function ($numero,$id){

	$consulta = Consultorio::select('id','letra','descripcion')
							->where('especialidad_id',$numero)
							->where('medico_id',$id)
							->get();
	
	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==============================================
 * GENERAR CITA
 ==============================================*/

Route::post('/reserva',function (Request $request){

	$id_usuario = $request->input("id"); 	
	$id_medico = $request->input("medico");
	$id_consultorio = $request->input("consultorio");
	$id_especialidad = $request->input("especialidad");
	$fecha = $request->input("fecha");
	$hora = $request->input("hora");

	$object = new Cita();
	$object->paciente_id = $id_usuario;
	$object->medico_id = $id_medico;
	$object->estado = 1;
	$object->consultorio_id = $id_consultorio;
	$object->especialidad_id = $id_especialidad;
	$object->fecha = $fecha;
	$object->hora = $hora;
	
	if ($object->save()) {
		echo json_encode("true");
	}else{
		echo json_encode("false");
	}
});

/*==============================================
 * ACTUALIZAR CITA
 ==============================================*/

Route::post('/reserva/{id}/{estado}',function ($id,$estado,Request $request){

	$update = Cita::where("id",$id)->first();
	$estado = 1;
	switch ($estado) {
		case 2:
			$estado = 2;
			break;
		case 3:
			$estado = 3;
			break;
		case 4:
			$estado = 4;
			break;
		/*case 5:
			$fechaActual = date('Y-m-d');
			$fechaReserva = $update->fecha;
			
			$cantDias = date_diff($fechaActual,$fechaReserva);


			if ($cantDias < 3) {
				$estado = 5;
			}else{
				echo json_encode("fecha");
				return;
			}

			$estado = 5;
			break;*/
	}
	if ($update) {
		
		$update->estado = $estado;
		
		if ($update->save()) {
			echo json_encode("true");
		}else{
			echo json_encode("false");
		}
	}else{
		echo json_encode("cita no encontrada");
	}
});