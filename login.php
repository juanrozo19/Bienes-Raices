<?php
//Importar la conexion 
require 'includes/config/database.php';
$db = conectarDB();

//Autenticar al usuario
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if(!$email){
        $errores[] = "El email es oblogatorio o no es valido";
    }

    if(!$password){
        $errores[] = "El password es obligatorio";
    }

    if(empty($errores)){
        //Revisar si el usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '${email}'";
        $resultado = mysqli_query($db , $query);

        if($resultado -> num_rows){
            //Revisar si el password es correcto
            $usuario = mysqli_fetch_assoc($resultado);

            //Verificar si el password es correcto o no
            $auth = password_verify($password, $usuario['password']);
            
            if($auth){
                //El usuario es autenticado
                session_start();

                //Llenar el arreglo de la sesion
                $_SESSION['usuario'] = $usuario['email'];   
                $_SESSION['login'] = true;   

                header('Location: /Bienes_Raices/admin/index.php');
            } else{
                $errores[] = "El password es incorrecto";
            }

        } else{
            $errores[] = "El usuario no existe";
        }
    }
}

//Incluye el header
require 'includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion contenido-centrado">
    <h1>Iniciar Sesion</h1>

    <?php foreach($errores as $error): ?>

        <div class="alerta error">
            <?php echo $error; ?>
        </div>

    <?php endforeach; ?>

    <form method="post" class="formulario" novalidate>
        <fieldset>
            <legend>Email & Password</legend>

            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="Tu Email" id="email">

            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Tu Password" id="password">

        </fieldset>

        <input type="submit" value="Iniciar Sesion" class="boton boton-verde-center">
    </form>
</main>

<?php
incluirTemplate('footer');
?>