<?php if(! defined('BASEPATH')) exit ('No direct script access allowed');

class Usuarios_model extends CI_Model{
	function __construct(){
		parent :: __construct();
		$this->load->database();
	}

	
    private function validar_select($query){
        if($query->conn_id->sqlstate == "00000"){
            return 1;
            #es 1 cuando el query se realizo con exito, codigo 00000
        }
        else{
            return 0;
            #es 0 cuando el query falla
        }
    }

    /* ******************** GET ********************** */
   
    public function usuarios(){
    	$USUARIOS=$this->db->query('SELECT id,Nombre, idTipoUsuario
                                FROM USUARIOS' );
    	
    	if($this->validar_select($USUARIOS)==1){
            return $USUARIOS->result_array();
        }
        else{
            return NULL;
	   }
    }
    private function validar_select_usuario($query_usuario){
    	if($query_usuario->conn_id->sqlstate == "00000"){
    		#el query fue exitoso, el codigo de exito es 00000
    		$query = $query_usuario->result_array();
	    	if (count($query) == 1) {
	    		if ($query[0]['id'] > 0 && $query[0]['idTipoUsuario'] > 0 && !empty($query[0]['Nombre'])) {
	    			return 1;
	    			#es 1 cuando el query es exitoso y los valores regresados son validos
	    		}else{
	    			return 0;
	    			#es 0 cuando los valores regresados no son validos
	    		}
	    	}else{
	    		return 0;
	    		#es 0 cuando manda mas de un resultado o ningun resultado
	    	}
	    }else{
	    	return 0;
	    	#es 0 cuando el query falla
	    }
    }

	public function login($Nombre,$Contrasenia,$d,$m,$a){
	    $query_usuario=$this->db->query('SELECT id,Nombre,idTipoUsuario
                                  FROM USUARIOS 
                                  WHERE Nombre="'.$Nombre.'"
                                  AND Contrasenia="'.$Contrasenia.'"');
		$query_ganancias=$this->db->query('SELECT Ganancias, Total_Gastos
											FROM GANANCIAS 
											WHERE idVenta=(SELECT id FROM VENTAS WHERE Dia="'.$d.'" AND Mes="'.$m.'"AND Anio="'.$a.'")');
		$query_gastos=$this->db->query('SELECT GASTOS.idTipoGasto,GASTOS.Nombre,GASTOS_DIARIOS.Costo
										FROM GASTOS_DIARIOS,GASTOS 
										WHERE GASTOS_DIARIOS.idVenta=(SELECT id FROM VENTAS WHERE Dia="'.$d.'" AND Mes="'.$m.'"AND Anio="'.$a.'")
												AND GASTOS_DIARIOS.idGasto=GASTOS.id');

		if($this->validar_select_usuario($query_usuario)==1){
            $usuario=array(
                'id' => $query_usuario->result_array()[0]['id'],
				'Nombre' => $query_usuario->result_array()[0]['Nombre'],
				'Tipo' => $query_usuario->result_array()[0]['idTipoUsuario'],
				'Ganancias' => $query_ganancias->result_array()[0]['Ganancias'],
				'Total_Gastos' => $query_ganancias->result_array()[0]['Total_Gastos'],
				'Gastos' => $query_gastos->result_array()
				
				
			
			);
            return $usuario;
        }
        else{
            return 0;
            #es 0 cuando el query falla
	   }
	}
	public function ver_playlists($usuario, $info_playlists){
	    
		/*$query = 'SELECT playlists.id, playlists.Nombre,  COUNT(*) AS numero_de_canciones, JSON_ARRAYAGG(JSON_OBJECT("id",canciones.id,
				"Nombre",canciones.Nombre,"artista",artistas.Nombre,"album",albums.Nombre,"direccion",canciones.direccion)) AS "canciones"
				  FROM playlist_con_canciones
				    INNER JOIN playlists ON playlists.id = playlist_con_canciones.id_playlist
				    INNER JOIN canciones ON canciones.id = playlist_con_canciones.id_cancion
				    INNER JOIN artistas ON artistas.id = canciones.id_artista
				    INNER JOIN albums ON albums.id = canciones.id_album
				  WHERE
				    playlists.id_usuario = "'.$id.'"
				  GROUP BY playlists.id;';
	    $playlists = $this->db->query($query);

	    if ($this->validar_select($playlists)==1) {
	    	return $playlists->result_array();
	    }else{
	    	return 0;
	    	#es 0 cuando el query falla
	    }*/
	    $aux = [];
	    $playlists = [];
	    foreach ($info_playlists as $playlist) {

	    	$canciones_de_playlist = $this->db->query('SELECT canciones.id, canciones.Nombre, artistas.Nombre AS "artista", albums.Nombre AS "album", canciones.direccion FROM playlists JOIN playlist_con_canciones ON playlists.id = playlist_con_canciones.id_playlist JOIN canciones ON canciones.id = playlist_con_canciones.id_cancion JOIN artistas ON canciones.id_artista = artistas.id JOIN albums ON canciones.id_album = albums.id WHERE playlists.id_usuario ="'.$usuario['id'].'" AND playlists.id = "'.$playlist['id'].'"');

	    	if ($this->validar_select($canciones_de_playlist)==1) {
	    		$aux['id'] = $playlist['id'];
	    		$aux['Nombre'] = $playlist['Nombre'];
	    		$aux['canciones'] = $canciones_de_playlist->result_array();
	    		array_push($playlists, $aux);
	    	}else{
	    		$playlists = 0;
	    		break;
	    	}
	    }
	    return $playlists;
	}
	public function info_playlists($id){
	    $info_playlists=$this->db->query('SELECT id,Nombre FROM playlists WHERE id_usuario = '.$id );
	    #campo de existo o no, sin result
	    if($this->validar_select($info_playlists)==1){
            return $info_playlists->result_array();
        }
        else{
            return 0;
            #es 0 cuando el query falla
	    }
	   
	}

	/* ******************** POST ********************** */

	private function validar_select_Nombre($query){
    	if($query->conn_id->sqlstate == "00000"){
    		#el query fue exitoso, el codigo de exito es 00000
    		$query = $query->result_array();
	    	if (count($query) > 0) {
	    		if ($query[0]['id'] > 0) {
	    			return 1;
	    			#es 1 cuando existe el Nombre y el id es >0
	    		}else{
	    			return 0;
	    			#es 0 cuando el id no es >0
	    		}
	    	}else{
	    		return 2;
	    		#es 2 cuando no existe el Nombre
	    	}
	    }else{
	    	return 0;
	    	#es 0 cuando el query falla
	    }
    }
    private function validar_insert($query){
    	if ($query == TRUE) {
    		return 1;
    		#es 1 cuando el query se realizo con exito
    	}else{
    		return 0;
    		#es 0 cuando el query falla
    	}
    }

	public function existe_Nombre($Nombre){
		$existe_Nombre = $this->db->query("SELECT id FROM USUARIOS WHERE Nombre = '$Nombre'");
		$exito_query = $this->validar_select_Nombre($existe_Nombre);
		return $exito_query;
	}

	public function insertar_usuario($Nombre, $Contrasenia, $idTipoUsuario){
		$insert = $this->db->query("INSERT INTO USUARIOS (Nombre, Contrasenia, idTipoUsuario) VALUES ('$Nombre', '$Contrasenia', '$idTipoUsuario')");
		return ($this->validar_insert($insert));
	}


	/* ******************** DELETE ********************** */

	private function validar_update(){
		    if($this->db->affected_rows() == 1){
				#el update realizo algun cambio
		        return 1;
		    }else{
				#el update fallo o no se actualizo nada
		        return 0;
		    }
    }

	public function delete_usuario($id){
		$delete_usuario = $this->db->query("UPDATE USUARIOS SET activo = 0 WHERE id = ".$id);
		//return $this->db->affected_rows();
		if($this->validar_update()==1){
			#tiene exito, se elimino logicamente el usuario
			return 1;
		}
		else{
			#el query falla y no se elimina el usuario
			return 0;
		}
	}

	/* ******************** PUT ********************** */

	public function modificar_todo($id,$Nombre,$Contrasenia,$id_tipo){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Nombre="'.$Nombre.'", 
												Contrasenia="'.$Contrasenia.'", 
												idTipoUsuario="'.$id_tipo.'"
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_Nombre($id,$Nombre){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Nombre="'.$Nombre.'" 	
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_Contrasenia($id,$Contrasenia){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Contrasenia="'.$Contrasenia.'" 	
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_tipo_usuario($id,$id_tipo){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET idTipoUsuario="'.$id_tipo.'"
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_Nombre_Contrasenia($id,$Nombre,$Contrasenia){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Nombre="'.$Nombre.'", 
												Contrasenia="'.$Contrasenia.'" 
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_Nombre_tipo($id,$Nombre,$id_tipo){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Nombre="'.$Nombre.'", 
												idTipoUsuario="'.$id_tipo.'"
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}
	public function modificar_contasenia_tipo($id,$Contrasenia,$id_tipo){
		$modificar_usuario=$this->db->query('UPDATE USUARIOS 
											SET Contrasenia="'.$Contrasenia.'", 
												idTipoUsuario="'.$id_tipo.'"
											WHERE id='.$id);
		#validamos que su halla modificado algo
		if($this->validar_update()==1){
			#se modifico el usuario
			return 1;
		}
		else{
			#no se pudo modificar
			return 0;
		}
	}

}