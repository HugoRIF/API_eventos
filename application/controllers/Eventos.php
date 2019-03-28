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

    private function validar_parametros_get($x){
        if(!empty($x)){
            return  1;
           
        }
        else{
            return 0;
            #es 0 cuando alguno o ambos son vacios
        }
    }   
    
    private function checar_parametros_definidos_get($x){
        if (isset($x)) {
            return 1;
            
        }elseif(is_null($x) ){
            return 2;
        }else{
            return 0;
            
        }
    }

    public function eventos_get() {
       $opcion=1;
        switch($opcion){
            
            case 1:
                $eventos=$this->Eventos_model->obtener_todos_los_eventos();
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
