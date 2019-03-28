<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Eventos extends REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Eventos_model');
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

    public function eventos_get() {
       $opcion=1;
        #Revisamos si estan definidos.
       // $opcion=$this->checar_parametros_definidos_get($Nombre, $Contrasenia, $Dia, $Mes, $Anio);
        switch($opcion){
            #Validamos si no estan vacios
            case 1:
                /*if($this->validar_parametros_get($Nombre,$Contrasenia)==1){
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
                */
                $eventos=$this->Eventos_model->obtener_eventos_todos();
                if(!is_null($eventos) ){
                    $respuesta=$this->crear_respuesta(200,"Eventos encontrados",$eventos);
                }else{
                    $respuesta=$this->crear_respuesta(400,"NO se encontraron los eventos",[]);
                
                }
                   
                
                break;

            
            default:
                 $respuesta=$this->crear_respuesta(200, "No implementado", []); 
        }

    $this->response($respuesta);
               
    }
}
