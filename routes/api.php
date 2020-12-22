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
use App\RangoHora;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
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
//http://127.0.0.1:8000/api/login
Route::post('/login', function (Request $request) {
    
    $dni = $request->input('dni');
    $maxDig = strlen($dni);
	$pass = $request->input('pass');
	$tipo = $request->input('tipo');
    
    if($maxDig ==8){
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
    
    	if ($consulta) {
    		if($pass == $consulta->password){
    	        $response["dato"]=array('estado' => 200,'dato' => $consulta);
    	        echo json_encode($response["dato"]);
    	    }else{
    	        //header('Status: 400', TRUE, 400);
    	        $response["dato"]=array('estado' => 400,'message' =>'Contraseña no coincide' );
    	        echo json_encode($response["dato"]);
    	    }	
    	}else{
    	    //header('Status: 400', TRUE, 400);
    	    $response["dato"]=array('estado' => 400,'message' =>'Usuario no encontrado' );
    	    echo json_encode($response["dato"]);
    	}
    }else{
        $response["dato"]=array('estado' => 400,'message' =>'Dni debe contener 8 digitos' );
    	echo json_encode($response["dato"]);
    }
    
		
	
});

/*==========================
 * REGISTRO
 ===========================*/
//http://127.0.0.1:8000/api/register
Route::post('/register', function (Request $request) {
    
    $names = $request->input('names');
    $lastnames = $request->input('lastnames');
    $dni = $request->input('dni');
	$pass = $request->input('pass');
	$tipo = $request->input('tipo');
	$tel = $request->input('tel');
    $maxDigDni = strlen($dni);
    $maxDigTel = strlen($tel);
    
    if( $maxDigDni == 8 && $maxDigTel == 9){
        switch ($tipo) {
		case 1:
		    $consulta = Paciente::where("dni",$dni)->get();
		    
		    if(count($consulta)>0){
		        
		        $response["dato"]=array('estado' => 400,'message' =>'Paciente ya registrado');
	            echo json_encode($response["dato"]);
		    }else{
		        $obj = new Paciente();
    			$obj->nombres = $names;
    			$obj->apellidos = $lastnames;
    			$obj->dni = $dni;
    			$obj->telefono = $tel;
    			$obj->password = $pass; 
    			$obj->usuario_id = intval($tipo);
    			
    			if($obj->save()){
    			    return $response["dato"]=array('estado' => 200,'dato' => $obj);
    	            echo json_encode($response["dato"]);
    			}else{
    			    $response["dato"]=array('estado' => 400,'message' =>'No se ha guardado el usuario' );
    	            echo json_encode($response["dato"]);
    			}
		    }
			
			break;
		case 2:
			$consulta = Medico::where("dni",$dni)->get();
		    
		    if(count($consulta)>0){
		        
		        $response["dato"]=array('estado' => 400,'message' =>'Medico ya registrado');
	            echo json_encode($response["dato"]);
		    }else{
		        $obj = new Medico();
    			$obj->nombres = $names;
    			$obj->apellidos = $lastnames;
    			$obj->dni = $dni;
    			$obj->telefono = $tel;
    			$obj->password = $pass; 
    			$obj->usuario_id = intval($tipo);
    			
    			if($obj->save()){
    			    return $response["dato"]=array('estado' => 200,'dato' => $obj);
    	            echo json_encode($response["dato"]);
    			}else{
    			    $response["dato"]=array('estado' => 400,'message' =>'No se ha guardado el usuario' );
    	            echo json_encode($response["dato"]);
    			}
		    }
			break;
		
	    }
    }else{
        $response["dato"]=array('estado' => 400,'message' =>'Dni debe contener 8 digitos y número de teléfono debe contener 9 digitos' );
    	echo json_encode($response["dato"]);
    }
	

});

/*==================================
 * LISTADO DE CITAS, POR PACIENTE
 ===================================*/
// http://127.0.0.1:8000/api/citas/1
Route::get('/citas/{paciente_id}',function ($paciente_id, Request $request){

	$consulta = DB::table('cita')
                    ->join('especialidad', 'cita.especialidad_id', '=', 'especialidad.id')
                    ->join('medico', 'cita.medico_id', '=', 'medico.id')
                    ->join('consultorio', 'cita.consultorio_id', '=', 'consultorio.id')
                    ->join('rango_horas', 'cita.rangohora_id', '=', 'rango_horas.id')
                    ->join('estado', 'cita.estado', '=', 'estado.id')
                    ->select(   'cita.id','medico.nombres as nombreMedico','medico.apellidos as apellidoMedico',
                                'especialidad.nombre as especialidad','consultorio.descripcion as consultorio',
                                'estado.tipo as estado','cita.fecha','rango_horas.rango as rangoHora')
                    ->where('cita.paciente_id','=',$paciente_id)
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

	$consulta = Especialidad::select('id','nombre')->get();
	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==============================================
 * SPINNER LISTA MEDICOS POR ID DE ESPECIALIDAD
 ==============================================*/
//https://citas.dmqvirucida.com.pe/api/medicos/1
Route::get('/medicos/{id}',function ($id){

	
    $consulta = DB::table('medico')
                    ->join('consultorio', 'medico.id', '=', 'consultorio.medico_id')
                    ->select('medico.id','medico.nombres','medico.apellidos','medico.especialidad_id', 'consultorio.descripcion as consultorio','consultorio.id as id_consultorio')
                    ->where('medico.especialidad_id','=',$id)
                    ->get();
                
	$response["dato"]=array('dato' => $consulta);

	if ($consulta) {
			echo json_encode($response["dato"]);
	}	
	
});
/*==============================================
 * SPINNER LISTA CONSULTORIOS
 ==============================================*/
//https://citas.dmqvirucida.com.pe/api/consultorios/1/1
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
//https://citas.dmqvirucida.com.pe/reserva
Route::post('/reserva',function (Request $request){    
	$id_usuario = $request->input("paciente"); 	
	$id_medico = $request->input("medico");
	$id_consultorio = $request->input("consultorio");
	$id_especialidad = $request->input("especialidad");
	$fecha = $request->input("fecha");
	$hora = $request->input("hora");

    $consultaFecha = Cita::where('fecha',$fecha)
                            ->where('medico_id',$id_medico)
                            ->where('especialidad_id',$id_especialidad)
                            ->where('consultorio_id', $id_consultorio)
                            ->get();
    
    if(count($consultaFecha)>0){
        if(count($consultaFecha)==8){
        	$response["dato"]=array('estado' => 400,'message' =>'No existe horario disponible para esta fecha' );
    		echo json_encode($response["dato"]);
    		return;
        }

        for ($i=0; $i < count($consultaFecha); $i++) { 
        	if ($consultaFecha[$i]->paciente_id == intval($id_usuario)) {
        	    
        		$rangoHora = RangoHora::where('id',intval($consultaFecha[$i]->rangohora_id))->first();
        		$rangoHora = $rangoHora->rango;
        		$message = "Ya tiene una cita reservada para la fecha ".$consultaFecha[$i]->fecha." en el horario de ".$rangoHora;
        		$response["dato"]=array('estado' => 400,'message' =>$message);
    			echo json_encode($response["dato"]);
    			return;
        	}
        }

        for ($i=0; $i < count($consultaFecha); $i++) { 
        	if ($consultaFecha[$i]->rangohora_id == intval($hora)) {
        		$response["dato"]=array('estado' => 400,'message' =>'Este horario ya se encuentra reservado, seleccione otro horario');
    			echo json_encode($response["dato"]);
    			return;
        	}
        }        
    }    
    
	$object = new Cita();
	$object->paciente_id = intval($id_usuario);
	$object->medico_id = intval($id_medico);
	$object->estado = 1;
	$object->consultorio_id = intval($id_consultorio);
	$object->especialidad_id = intval($id_especialidad);
	$object->fecha = $fecha;
	$object->rangohora_id = intval($hora);	
	
	if ($object->save()) {
		$response["dato"]=array('estado' => 200,'message' =>'Su reserva ha sido generada con éxito');
    	echo json_encode($response["dato"]);
    	return;
	}else{
		$response["dato"]=array('estado' => 400,'message' =>'Ocurrio un error, intente nuevamente...');
    	echo json_encode($response["dato"]);
    	return;
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