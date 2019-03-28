<?php if(! defined('BASEPATH')) exit ('No direct script access allowed');

class Eventos_model extends CI_Model{
	function __construct(){
		parent :: __construct();
		$this->load->database();
	}

	
    private function validar_select($query){
        if($query->conn_id->sqlstate == '00000'){
            return 1;
            #es 1 cuando el query se realizo con exito, codigo 00000
        }
        else{
            return 0;
            #es 0 cuando el query falla
        }
    }

    /* ******************** GET ********************** */
   
   
 public function obtener_eventos_todos(){
        $eventos=$this->db->query("SET sql_mode = '' ");
    	$eventos=$this->db->query("SELECT eventos.id, eventos.nombre, eventos.descripcion, eventos.imagen, eventos.latitud, eventos.longitud, eventos.calle, eventos.codigo_postal,
        eventos.colonia, eventos.alcaldia,eventos.costo,eventos.fecha,eventos.fecha_registro,tipos_eventos.nombre AS 'tipo_evento',JSON_ARRAYAGG(publico_dirigido.nombre) AS 'dirigido_a',
        usuarios.nombre AS 'usuario que lo registro', foros.nombre AS 'foro'
          FROM eventos
            INNER JOIN tipos_eventos ON eventos.id_tipo_evento = tipos_eventos.id
            INNER JOIN publico_del_evento ON publico_del_evento.id_evento = eventos.id
            INNER JOIN publico_dirigido on publico_dirigido.id = publico_del_evento.id_publico_dirigido
            INNER JOIN usuarios ON eventos.id_usuario = usuarios.id
            INNER JOIN foros ON eventos.id_foro = foros.id
         group by eventos.id;" );
        
    	if($this->validar_select($eventos)==1){
          
            return $eventos->result_array();
        }
        else{
            return NULL;
	   }
    }
}
/*
 public function obtener_eventos_todos(){
        $eventos=$this->db->query("SET sql_mode = '' ");
    	$eventos=$this->db->query("
        SELECT eventos.id, eventos.nombre, eventos.descripcion, eventos.imagen, eventos.latitud, eventos.longitud, eventos.calle, eventos.codigo_postal,
        eventos.colonia, eventos.alcaldia,eventos.costo,eventos.imagen,json_object('evento', eventos.fecha, 'registro', eventos.fecha_registro) AS 'fecha',tipos_eventos.nombre,json_arrayagg(publico_dirigido.nombre) AS 'dirigido_a:',
        foros.nombre AS 'foro', json_object('id', usuarios.id, 'nombre', usuarios.nombre, 'apellido_paterno', usuarios.apellido_paterno, 'apellido_materno', usuarios.apellido_materno, 'correo', usuarios.correo,
        'area_inscrita', usuarios.area_inscrita) AS 'usuario'
          FROM eventos
            INNER JOIN tipos_eventos ON eventos.id_tipo_evento = tipos_eventos.id
            INNER JOIN publico_del_evento ON publico_del_evento.id_evento = eventos.id
            INNER JOIN publico_dirigido ON publico_dirigido.id = publico_del_evento.id_publico_dirigido
            INNER JOIN usuarios ON eventos.id_usuario = usuarios.id
            INNER JOIN foros ON eventos.id_foro = foros.id
         group by eventos.id;" );
        $respuesta_query=$eventos->result_array();
        $arr_eventos=[];
        for ($i=0; $i < sizeof($respuesta_query); $i++) { 
            $arr_eventos[$i]['id']=$respuesta_query[$i]['id'];
            $arr_eventos[$i]['descripcion']=$respuesta_query[$i]['descripcion'];
            $arr_eventos[$i]['imagen']=$respuesta_query[$i]['imagen'];
            $arr_eventos[$i]['latitud']=$respuesta_query[$i]['latitud'];
            $arr_eventos[$i]['longitud']=$respuesta_query[$i]['longitud'];
            $arr_eventos[$i]['calle']=$respuesta_query[$i]['calle'];
            $arr_eventos[$i]['codigo_postal']=$respuesta_query[$i]['codigo_postal'];
            $arr_eventos[$i]['colonia']=$respuesta_query[$i]['colonia'];
            $arr_eventos[$i]['alcaldia']=$respuesta_query[$i]['alcaldia'];
            $arr_eventos[$i]['costo']=$respuesta_query[$i]['costo'];
            $arr_eventos[$i]['foro']=$respuesta_query[$i]['foro'];
            $arr_eventos[$i]['codigo_postal']=$respuesta_query[$i]['codigo_postal'];
            $arr_eventos[$i]['fecha']=json_decode($respuesta_query[$i]['fecha']);
            $arr_eventos[$i]['dirigido_a:']=json_decode($respuesta_query[$i]['dirigido_a:']);
            $arr_eventos[$i]['usuario']=json_decode($respuesta_query[$i]['usuario']);
                
        }
    	if($this->validar_select($eventos)==1){
            return $arr_eventos;
        }
        else{
            return NULL;
	   }
    }

*/