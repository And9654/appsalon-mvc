<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginControllers{

    public static function login(Router $router){
    
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();
            
            if(empty($alertas)){

                $usuario = Usuario::where('email', $auth->email);
                
                // ? si el usuario esta en la BD
                if($usuario){

                    // ? revisa la contraseña y que esté autenticado
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){

                        if(session_status() == PHP_SESSION_DISABLED) session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        if($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                        
                    }
                    
                } else{
                    Usuario::setAlerta('errores', 'Usuario no encontrado');
                }

            }
            
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(Router $router){
        if(session_status() === PHP_SESSION_DISABLED) session_start();

        $_SESSION = [];
        
        header('Location: /');
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if(empty($alertas)){

                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){

                    $usuario->crearToken();
                    $usuario->guardar();

                    // ? enviar el email de recuperación
                    $mail = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $mail->enviarInstrucciones();

                    // ? alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');

                } else {
                    Usuario::setAlerta('errores', 'El usuario no existe o no ha confirmado su cuenta');
                }
                
            } 
            
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router){

        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);
    
        $error = false;

        if(empty($usuario)){
            $alertas = Usuario::setAlerta('errores', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();

            if(empty($alertas)){

                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();

                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);

    }

    public static function crear(Router $router){

        $usuario = new Usuario;

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // ? pasó la validación de formulario
            if(empty($alertas)){

                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){

                    $alertas = Usuario::getAlertas();

                // ? Acciones si el usuario aún no está registrado
                }else{

                    $usuario->hashPassword();

                    $usuario->crearToken();

                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();

                    $resultado = $usuario->guardar();

                    if($resultado){
                        header('Location: /mensaje');
                    }
                }

            }

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function confirmar(Router $router){

        $alertas = [];

        if(empty($_GET['token'])) header('Location: /404');

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        // ? si el token no existe en la BD
        if(empty($usuario)){
            Usuario::setAlerta('errores', 'Token no válido');
        
        // ? si el token existe
        }else{

            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

}