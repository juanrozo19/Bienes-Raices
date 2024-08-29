<?php
require '../../includes/funciones.php';
$auth= estaAutenticado();

if(!$auth){
    header('Location: /Bienes_Raices/login.php');
}

//Base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//Consultar para obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//Arreglo con mensajes de errores
$errores = [];

$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedores_id = '';

$imagen = '';

//Ejecutar el codigo despues de que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    // echo "<pre>";
    // var_dump($_FILES);
    // echo "</pre>";

    $titulo = mysqli_real_escape_string($db,  $_POST['titulo']);
    $precio = mysqli_real_escape_string($db,  $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db,  $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db,  $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db,  $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db,  $_POST['estacionamiento']);
    $vendedores_id = mysqli_real_escape_string($db,  $_POST['vendedores']);
    $creado = date('Y/m/d');

    //Asignar files hacia una variable
    $imagen = $_FILES['imagen'];

    //Validaciones
    if (!$titulo) {
        $errores[] = "Debes añadir un titulo";
    }

    if (!$precio) {
        $errores[] = "Debes añadir un Precio";
    }

    if (strlen($descripcion) < 50) {
        $errores[] = "Debes añadir una descripcion de al menos 50 caracteres";
    }

    if (!$habitaciones) {
        $errores[] = "Debes añadir el numero de habitaciones";
    }

    if (!$wc) {
        $errores[] = "Debes añadir el numero de baños";
    }

    if (!$estacionamiento) {
        $errores[] = "Debes añadir el numero de estacionamientos";
    }

    if (!$vendedores_id) {
        $errores[] = "Debes añadir el vendedor de la propiedad";
    }

    if (!$imagen['name'] || $imagen['error']) {
        $errores[] = 'La Imagen Es Obligatoria';
    }

    // Validar por tamaño 1 mb Maximo

    $medida = 2000 * 2000;

    if ($imagen['size'] > $medida) {
        $errores[] = 'La imagen es muy pesada';
    }

    //Revisar que el array de errores este vacio

    if (empty($errores)) {

        /** SUBIDA DE ARCHIVOS */

        //Crear Carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        //Generar un nombre unico
        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

        //Subir la imagen
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

        //Insertar en la base de datos
        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedores_id) 
                VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedores_id')";

        $resultado = mysqli_query($db, $query);

        if ($resultado) {

            //REDIRECCIONAR USUARIOS DESPUES DE REGISTRO DE DATOS
            header('location: /Bienes_Raices/admin/index.php?resultado=1');
        }
    }
}

//Templates
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear</h1>

    <a href="/Bienes_Raices/admin/index.php" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" action="/Bienes_Raices/admin/propiedades/crear.php" enctype="multipart/form-data">
        <fieldset>
            <legend>Informacion De La Propiedad</legend>

            <label for="titulo:">Titulo:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio:">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" min="1" value="<?php echo $precio; ?>">

            <label for="titulo:">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">

            <label for="descripcion:">Descripcion:</label>
            <textarea id="descripcion" name="descripcion"> <?php echo $descripcion; ?> </textarea>
        </fieldset>

        <fieldset>
            <legend>Informacion Propiedad</legend>

            <label for="habitaciones:">Habitaciones:</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" , max="9" value="<?php echo $habitaciones; ?>">

            <label for="wc:">Baños:</label>
            <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" , max="9" value="<?php echo $wc; ?>">

            <label for="estacionamiento:">Estacionamiento:</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" , max="9" value="<?php echo $estacionamiento; ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>
            <select name="vendedores">
                <!-- CODIGO PARA AUTOMATIZAR OPCIONES DE UN SELECT-->
                <option value="">-- Seleccione --</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado)) : ?>
                    <option <?php echo $vendedores_id === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>">
                        <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?></option>
                <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Crear Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>