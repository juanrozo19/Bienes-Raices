<?php
require '../../includes/funciones.php';
$auth= estaAutenticado();

if(!$auth){
    header('Location: /Bienes_Raices/login.php');
}
//Validar ID de actualizacion
//Validar URL por ID valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
    header('location: /Bienes_Raices/admin/index.php');
}

//Base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//Consulta para obtener los datos (de la propiedad)
$consulta = "SELECT * FROM propiedades WHERE id = ${id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);

//Consultar para obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//Arreglo con mensajes de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedores_id = $propiedad['vendedores_id'];

$imagen = $propiedad['imagen'];

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

    // Validar por tamaño 1 mb Maximo

    $medida = 1000 * 1000;

    if ($imagen['size'] > $medida) {
        $errores[] = 'La imagen es muy pesada';
    }

    //Revisar que el array de errores este vacio

    if (empty($errores)) {

        // /** SUBIDA DE ARCHIVOS */

        //Crear Carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        if ($imagen['name']) {
            //Eliminar la imagen previa
            unlink($carpetaImagenes . $propiedad['imagen']);

            //Generar un nombre unico
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //Subir la imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
        }
        else{
            $nombreImagen = $propiedad['imagen'];
        }

        //Insertar en la base de datos
        $query = " UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen  = '${nombreImagen}', descripcion = '${descripcion}',
        habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedores_id = ${vendedores_id} WHERE id = ${id}";

        $resultado = mysqli_query($db, $query);

        if ($resultado) {

            //REDIRECCIONAR USUARIOS DESPUES DE REGISTRO DE DATOS
            header('location: /Bienes_Raices/admin/index.php?resultado=2');
        }
    }
}

//Templates
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Actualizar Propiedad</h1>

    <a href="/Bienes_Raices/admin/index.php" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Informacion De La Propiedad</legend>

            <label for="titulo:">Titulo:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio:">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" min="1" value="<?php echo $precio; ?>">

            <label for="titulo:">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png" name="imagen">

            <img src="/Bienes_Raices/imagenes/<?php echo $imagen; ?>" class="imagen-small">

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

        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>