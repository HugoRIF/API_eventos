<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Login extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Login_model');
        $this->load->helper('url');

    }

    private function crear_respuesta($codigo, $mensaje, $data){
        $respuesta = array('codigo' => $codigo,
                            'mensaje' => $mensaje,
                            'data' => $data);
        return $respuesta;
    }
    

/*************************** GET usuarios ********************** */

    private function validar_parametros_get($Nombre,$Contrasenia){
        if(!empty($Nombre) && !empty($Contrasenia)){
            return  1;
            #es 1 cuando Nombre y Contrasenia no son vacios
        }
        else{
            return 0;
            #es 0 cuando alguno o ambos son vacios
        }
    }   
    private function ver_tipo_usuario($usuario){
        $id_tipo=$usuario['id_tipo'];
        if($id_tipo == 2){
            return 2;
            #es 2 cuando es tipo usuario
        }
        else{
            return 1;
            #es 1 cuando es tipo administrador
        }
    }
    private function checar_parametros_definidos_get($Nombre, $Contrasenia, $Dia, $Mes, $Anio){
        if (isset($Nombre, $Contrasenia,$Dia, $Mes, $Anio)) {
            return 1;
            #es 1 cuando Nombre y Contrasenia estan definidos
        }elseif(is_null($Nombre) && is_null($Contrasenia)){
            return 2;
            #es 2 cuando Nombre y Contrasenia no estan definidos
        }else{
            return 0;
            #es 0 cuando solo uno esta definido
        }
    }

    public function usuarios_get() {
        $Nombre=$this->get('Nombre');
        $Contrasenia=$this->get('Contrasenia');
        $Dia=$this->get('Dia');
        $Mes=$this->get('Mes');
        $Anio=$this->get('Anio');
        #Revisamos si estan definidos.
        $opcion=$this->checar_parametros_definidos_get($Nombre, $Contrasenia, $Dia, $Mes, $Anio);
        switch($opcion){
            #Validamos si no estan vacios
            case 1:
                if($this->validar_parametros_get($Nombre,$Contrasenia)==1){
                    #(parametros validos) los parametros estan definidos y contienen algo
                    #Hacemos select a la tabla usuario
                    $usuario=$this->Usuarios_model->login($Nombre,$Contrasenia,$Dia, $Mes, $Anio);
                    #checamos el exito del query
                    if($usuario != 0){
                        #exito del query
                                $respuesta=$this->crear_respuesta(200,"Encontrado",$usuario);
                                              }
                    else {#falla query
                        $respuesta=$this->crear_respuesta(404,"error en la consulta del usuario login",[]);
                    }
                }
                else { #falla de los parametros
                        $respuesta=$this->crear_respuesta(400,"error en los parametros",[]);
                }
                break;

            case 2:
                $usuarios=$this->Usuarios_model->usuarios();
                //$usuarios=$this->Usuarios_model->ver_playlists("3");
                if (!is_null($usuarios)) {
                    #exito query
                    $respuesta=$this->crear_respuesta(200,"Usuarios Encontrados", $usuarios);
                } else{
                    #query falla
                    $respuesta=$this->crear_respuesta(404,"error en la consulta de los usuarios get solo", []);
                }
                break;

            default:
                 $respuesta=$this->crear_respuesta(200, "No implementado", []); 
        }

    $this->response($respuesta);
               
    }

    /* ******************** POST ********************** */

    private function checar_parametros_definidos_post($Nombre, $Contrasenia, $id_tipo){
        if (isset($Nombre, $Contrasenia, $id_tipo)) {
            return 1;
            #es 1 cuando los parametros estan definidos
        }else{
            return 0;
            #es 0 cuando no estan definidos
        }
    }
    private function validar_parametros_post($Nombre, $Contrasenia, $id_tipo){
        if(!empty($Nombre) && !empty($Contrasenia) && !empty($id_tipo) && $id_tipo>0){
            return  1;
            #es 1 cuando no son vacios y el id es >0
        }
        else{
            return 0;
            #es 0 cuando son vacios o el id no es >0
        }
    }

    public function usuarios_post(){
        $Nombre = $this->post('Nombre');
        $Contrasenia = $this->post('Contrasenia');
        $id_tipo = $this->post('id_tipo');
        $opcion = $this->checar_parametros_definidos_post($Nombre, $Contrasenia, $id_tipo);

        switch ($opcion){
            case 1:
            #parametros definidos    
                if($this->validar_parametros_post($Nombre,$Contrasenia,$id_tipo)==1){
                    #parametros validos
                    $existe_Nombre = $this->Usuarios_model->existe_Nombre($Nombre);
                    
                    if ($existe_Nombre != 0){
                        #exito del query
                        if ($existe_Nombre == 1){
                            #existe usuario con ese Nombre
                            $respuesta = $this->crear_respuesta(200, 'Nombre de usuario ya existe', []);
                        } else {
                            #no existe usuario con ese Nombre
                            $insert = $this->Usuarios_model->insertar_usuario($Nombre, $Contrasenia, $id_tipo);
                            #se hace el insert
                            if ($insert == 1) {
                                #exito query
                                $respuesta = $this->crear_respuesta(201, 'usuario agregado', []);
                            } else {
                                #exito falla
                                $respuesta = $this->crear_respuesta(202, 'no se pudo agregar el usuario', []);
                            }
                        }
                    } else {
                        #query falla
                        $respuesta = $this->crear_respuesta(404, 'error en la consulta', []);
                    }

                } else {
                    #parametros no validos
                    $respuesta = $this->crear_respuesta(400, 'error en los parametros', []);
                }
                break;

            default:
                #parametros no definidos
                //$existe_Nombre = $this->Usuarios_model->existe_Nombre('David');
                $respuesta = $this->crear_respuesta(400, 'no implementado', []);
        }

        $this->response($respuesta);
    }

    /* ******************** DELETE ********************** */

    private function checar_parametros_definidos_delete($id){
        if (isset($id)) {
            return 1;
            #es 1 cuando el id esta definido
        }else{
            return 0;
            #es 0 cuando no esta definido
        }
    }
    private function validar_parametros_delete($id){
        if(!empty($id) && $id>0){
            return  1;
            #es 1 cuando no es vacio y es >0
        }
        else{
            return 0;
            #es 0 cuando es vacio o no es >0
        }
    }

    public function usuarios_delete(){
        $id = $_GET['id'];
        $opcion=$this->checar_parametros_definidos_delete($id);
        #se valida que el id este definido
        switch ($opcion) {
            case 1:
            #id definido
                if($this->validar_parametros_delete($id)==1){
                    #se valida que el id sea >0
                    $delete_usuario = $this->Usuarios_model->delete_usuario($id);
                    #se realiza el delete (update) que es borrado logico
                        if ($delete_usuario == 1) {
                            #query exitoso, se afecto un usuario
                            $respuesta = $this->crear_respuesta(200, 'usuario eliminado (actualizado)', []);
                        } else {
                            #no se afecto a ningun usuario
                            $respuesta = $this->crear_respuesta(400, 'error en la consulta o ya esta eliminado', []);
                        }
                }else{
                    #el id no es valido
                    $respuesta = $this->crear_respuesta(400, 'error en los parametros', []);
                }
                break;
            
            default:
                //$prueba = $this->Usuarios_model->delete_usuario(21);
                #el id no esta definido
                $respuesta = $this->crear_respuesta(404, 'no implementado', []);
                break;
        }
        $this->response($respuesta);
    }

    /* ******************** PUT ********************** */

    private function checar_parametros_definidos_put($id,$Nombre,$Contrasenia,$id_tipo){
        if(isset($id, $Nombre, $Contrasenia, $id_tipo) && $id>0){
            #todos estan bien
            return 1;
        }
        else{
            return 0;
        }
    }    
    private function validar_parametros_put($id,$Nombre,$Contrasenia,$id_tipo){
        
        if($id >0 ){
            #el id  valido
            if(!empty($Nombre) && !empty($Contrasenia) && !empty($id_tipo) && $id_tipo>0){
                #se va a modificar todo
                return 1;
            }
            else{
                if(!empty($Nombre) && empty($Contrasenia) && empty($id_tipo)){
                    #solo modificar Nombre
                    return 2;   
                }
                elseif (empty($Nombre) && !empty($Contrasenia)  && empty($id_tipo)) {
                    # solo modifico contraseña
                    return 3; 
                }
                elseif(empty($Nombre) && empty($Contrasenia) && !empty($id_tipo) && $id_tipo>0){
                    #modifico el tipo de usuario 
                    return 4;
                }
                elseif(!empty($Nombre) && !empty($Contrasenia) && empty($id_tipo)){
                    #modifico Nombre y contraseña
                    return 5;
                }
                elseif(!empty($Nombre) && empty($Contrasenia) && !empty($id_tipo) && $id_tipo>0){
                    #modifico Nombre y tipo de usuario 
                    return 6;
                }
                elseif(empty($Nombre) && !empty($Contrasenia) && !empty($id_tipo) && $id_tipo>0){
                    #modifico Contrasenia y tipo de usuario
                    return 7;
                }
            }
        }
        else{
           #el id invalido
           #no se hara mada, error en los parametros
           return 0;
        }

    }
    public function actualizar_post(){
            $id=$this->put('id');
            $Nombre=$this->put('Nombre');
            $Contrasenia=$this->put('Contrasenia');
            $id_tipo=$this->put('id_tipo');
            $opcion = $this->checar_parametros_definidos_put($id,$Nombre,$Contrasenia,$id_tipo);
            switch ($opcion) {
                case 1:
                    #parametros definidos
                    $parametros_a_modificar=$this->validar_parametros_put($id,$Nombre,$Contrasenia,$id_tipo);
                    switch ($parametros_a_modificar) {
                        case 1:#se modifica todo
                            $modificar_usuario=$this->Usuarios_model->modificar_todo($id,$Nombre,$Contrasenia,$id_tipo);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                        case 2:# se modifica solo el Nombre
                            $modificar_usuario=$this->Usuarios_model->modificar_Nombre($id,$Nombre);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado solo Nombre",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                         case 3: #se modifica solo la contraseña
                            $modificar_usuario=$this->Usuarios_model->modificar_Contrasenia($id,$Contrasenia);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado solo Contrasenia",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                         case 4:# se modifica el tipo usuario
                            $modificar_usuario=$this->Usuarios_model->modificar_tipo_usuario($id,$id_tipo);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado solo tipo",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                         case 5:# se modifica el Nombre y la contraseña
                            $modificar_usuario=$this->Usuarios_model->modificar_Nombre_Contrasenia($id,$Nombre,$Contrasenia);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado Nombre y Contrasenia",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                         case 6:# se modifica en Nombre y el tipo usuario
                            $modificar_usuario=$this->Usuarios_model->modificar_Nombre_tipo($id,$Nombre,$id_tipo);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado Nombre y tipo",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                         case 7: # se modifica la contraseña y el tipo usuario
                            $modificar_usuario=$this->Usuarios_model->modificar_contasenia_tipo($id,$Contrasenia,$id_tipo);
                            #checo si se modifico
                            if($modificar_usuario==1){
                                $respuesta=$this->crear_respuesta(200,"usuario actualizado Contrasenia y tipo",[]);
                            }
                            else{
                                $respuesta=$this->crear_respuesta(400,"no se pudo actualizar",[]);
                            }
                            break;
                        default:
                            $respuesta=$this->crear_respuesta(200,"no se modifico nada",[]);
                            break;
                    }
                    break;
                /*case 0:
                    $respuesta=$this->crear_respuesta(400,"error en los parametros",[]);
                    break;*/
                
                default:
                    #parametros no definidos
                    $respuesta=$this->crear_respuesta(400,"no implementado",[]);
                    break;
            }
            $this->response($respuesta);
    }
}
