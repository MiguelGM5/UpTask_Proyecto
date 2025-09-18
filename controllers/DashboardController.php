<?php 

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController{

    public static function index(Router $router){
        session_start();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);

            //Validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //Generar URL unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                //almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id']; 
                
                //Guardar proyecto
                $proyecto->guardar();

                //redireccionar
                header('Location: /proyecto?id='.$proyecto->url);
            }

        }

        $router->render('dashboard/crear-proyecto', [
            'alertas' => $alertas,
            'titulo' => 'Crear Proyecto'
            

        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();
        
        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);

        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){

                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    //Mostrar mensaje de error
                    Usuario::setAlerta('error', 'Email no valido');
                    $alertas = $usuario->getAlertas();
                }else{
                    //Guardar el Registro
                    //Guarda el usuario
                    $usuario->guardar();
    
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
    
                    //Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }
        
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();
            
            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();

                if ($resultado) {
                    //Asignar la neuva contraseña
                    $usuario->password = $usuario->contraseña_nueva;

                    //eliminar propiedades no necesarias
                    unset($usuario->contraseña_actual);
                    unset($usuario->contraseña_nueva);

                    //Hashear la nueva contraseña
                    $usuario->hashearPassword();
                    
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Contraseña Actualizada Correctamente');
                        $alertas = Usuario::getAlertas();
                    }

                }else{
                    Usuario::setAlerta('error', 'Contraseña Incorrecta');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]);
    }

}