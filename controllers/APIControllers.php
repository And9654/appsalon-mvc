<?php 

namespace Controllers;

use Model\Cita;
use Model\Citasservicios;
use Model\Servicio;

class APIControllers{
    
    public static function index() {
    
        $servicios = Servicio::all();

        echo json_encode($servicios);
    }

    public static function guardar(){

        // ? almacena el registro en la tabla de citas
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        // ? obtenemos el id de la cita de la variable resultado que se almacenará en citasservicios 
        $id = $resultado['id'];
        
        // ? obtenemos los servicios que se han mandado desde JavaScript y los separamos en un arreglo
        $idServicios = explode(',', $_POST['servicios']);

        // ? iteramos sobre cada servicio que se seleccionó y lo almacenamos en la tabla uno por uno
        foreach($idServicios as $idServicio){
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new Citasservicios($args);
            $citaServicio->guardar();
        }
        
        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar(){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();

            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

    }

}