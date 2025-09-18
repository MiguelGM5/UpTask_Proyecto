<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $contraseña_actual;
    public $contraseña_nueva;
    public $token;
    public $confirmado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->contraseña_actual = $args['contraseña_actual'] ?? '';
        $this->contraseña_nueva = $args['contraseña_nueva'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //validar login de usuarios
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Ingresa un Email valido';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña es obligatoria';
        }

        return self::$alertas;
    }

    //Validacion para cuentas nuevas
    public function validarCuentaNueva(){

        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }

        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Las contraseñas deben coincidir';
        }

        return self::$alertas;

    }

    //Valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Ingresa un Email valido';
        }
        
        return self::$alertas;
    }

    //Valida password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        
        return self::$alertas;
    }

    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'el nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'el email es obligatorio';
        }
        return self::$alertas;
    }

    public function nuevo_password() : array{
        if(!$this->contraseña_actual){
            self::$alertas['error'][] = 'La Contraseña Actual no puede ir vacia';
        }

        if(!$this->contraseña_nueva){
            self::$alertas['error'][] = 'La Contraseña Nueva no puede ir vacia';
        }
        
        if(strlen($this->contraseña_nueva) < 6){
            self::$alertas['error'][] = 'La Contraseña Nueva debe al menos tener 6 caracteres';
        }

        return self::$alertas;
    }

    public function comprobar_password() : bool{
        return password_verify($this->contraseña_actual, $this->password);
    }

    //hashea password
    public function hashearPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //generar token
    public function crearToken() : void{
        $this->token = uniqid();
    }

    
}