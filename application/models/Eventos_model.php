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
   
    public function obtener_todos_los_eventos(){
        $eventos=$this->db->query("SET sql_mode = '' ");
    	
        $eventos=$this->db->query("SELECT eventos.id, eventos.nombre, eventos.descripcion, eventos.imagen, eventos.latitud, eventos.longitud, eventos.calle, eventos.codigo_postal,
            eventos.colonia, eventos.alcaldia,eventos.costo,json_object('evento', eventos.fecha, 'registro', eventos.fecha_registro) AS 'fecha',tipos_eventos.nombre AS 'tipo_evento',JSON_ARRAYAGG(publico_dirigido.nombre) AS 'dirigido_a',
            foros.nombre AS 'foro', json_object('id', usuarios.id, 'nombre', usuarios.nombre, 'apellido_paterno', usuarios.apellido_paterno, 'apellido_materno', usuarios.apellido_materno, 'correo', usuarios.correo,
            'area_inscrita', usuarios.area_inscrita) AS 'usuario'
              FROM eventos
                INNER JOIN tipos_eventos ON eventos.id_tipo_evento = tipos_eventos.id
                INNER JOIN publico_del_evento ON publico_del_evento.id_evento = eventos.id
                INNER JOIN publico_dirigido on publico_dirigido.id = publico_del_evento.id_publico_dirigido
                INNER JOIN usuarios ON eventos.id_usuario = usuarios.id
                INNER JOIN foros ON eventos.id_foro = foros.id
             group by eventos.id;" )->result_array();
            for ($i=0; $i < sizeof($eventos); $i++) { 
                $eventos[$i]['fecha']=json_decode($eventos[$i]['fecha']);
                $eventos[$i]['dirigido_a']=json_decode($eventos[$i]['dirigido_a']);
                $eventos[$i]['usuario']=json_decode($eventos[$i]['usuario']);
            }
        return $eventos;
    }
}