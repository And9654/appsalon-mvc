<?php

namespace Model;

class Usuario extends ActiveRecord{

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['errores'][] = "El nombre es obligatorio";
        }
        if(!$this->apellido){
            self::$alertas['errores'][] = "El apellido es obligatorio";
        }
        if(!$this->email){
            self::$alertas['errores'][] = "El email es obligatorio";
        }
        if(!$this->password){
            self::$alertas['errores'][] = "La contraseña es obligatoria";
        }
        if(strlen($this->password) < 6){
            self::$alertas['errores'][] = "La contraseña debe contener al menos 6 caracteres";
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['errores'][] = "El email es obligatorio";
        }
        if(!$this->password){
            self::$alertas['errores'][] = "La contraseña es obligatoria";
        }
        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['errores'][] = "El email es obligatorio";
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['errores'][] = "La contraseña es obligatoria";
        }
        if(strlen($this->password) < 6){
            self::$alertas['errores'][] = "La contraseña debe contener al menos 6 caracteres";
        }
        return self::$alertas;
    }

    public function existeUsuario(){
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "'";
        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['errores'][] = "El usuario ya se encuentra registrado";
        }

        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordAndVerificado($password){
        $resultado = password_verify($password, $this->password);

        if(!$this->confirmado || !$resultado){
            self::$alertas['errores'][] = "La contraseña es incorrecta o la cuenta no ha sido confirmada";
        } else {
            return true;
        }
    }

}