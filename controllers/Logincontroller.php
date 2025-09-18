<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class Logincontroller{

    public static function login(Router $router){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                //verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }else{
                    //El usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        //Iniciar sesion del usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                    }else{
                        Usuario::setAlerta('error', 'Credenciales Incorrectas');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION =[];
        header('Location: /');
    }

    public static function crear(Router $router){

        $usuario = new Usuario();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear el password
                    $usuario->hashearPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //generar el token
                    $usuario->crearToken();

                    //Crear un nuevo usuario
                    $resultado = $usuario->guardar();
                    
                    //enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->sendConfirmacion();

                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }
            
        $router->render('auth/crear', [
            'titulo' => 'Crea tu Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    
                    //Actualizar el usuario
                    $usuario->guardar();
                    
                    //enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->sendInstrucciones();

                    //Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide', [
            'titulo' => 'Olvidaste Tu Contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){
        
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        //Identificar al usuario con token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //añadir nuevo pass
            $usuario->sincronizar($_POST);
            
            //Validar el password
            $alertas = $usuario->validarPassword();
            
            if(empty($alertas)){
                //Hashear nuevo password
                $usuario->hashearPassword();

                //Eliminar el token
                $usuario->token = '';
                //unset($usuario->password2);
                
                //Actualiza el usuario
                $resultado = $usuario->guardar();

                //Redirecciona a index
                if($resultado){

                    header('Location: /');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada'
        ]);
    }

    public static function confirmar(Router $router){

        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        //encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            //No se encontro un usuario con ese token
            Usuario::setAlerta('error', 'Token no valido');
        }else{
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);

            Usuario::setAlerta('exito', 'Cuenta Confirmada Correctamente');
            $usuario->guardar();
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta',
            'alertas' => $alertas
        ]);
    }
}   