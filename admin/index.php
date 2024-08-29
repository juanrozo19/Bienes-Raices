<?php
require '../includes/funciones.php';
$auth= estaAutenticado();

if(!$auth){
    header('Location: /Bienes_Raices/login.php');
}

/**TRAER DATOS DE BASE DE DATOS */

// Importar la conexión
require '../includes/config/database.php';
$db = conectarDB();

// Escribir el Query
$query = "SELECT * FROM propiedades";

// Consultar la Base De Datos
$resultadoConsulta = mysqli_query($db, $query);

// Muestra mensaje condicional
$resultado = $_GET['resultado'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id) {

        // Elimina El Archivo
        $query = "SELECT imagen FROM propiedades WHERE id = ${id}";
        $resultado = mysqli_query($db, $query);
        $propiedad = mysqli_fetch_assoc($resultado);

        // Eliminar la imagen del servidor
        $rutaImagen = '../imagenes/' . $propiedad['imagen'];
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }

        // Eliminar la propiedad de la base de datos
        $query = "DELETE FROM propiedades WHERE id = ${id}";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {

            // Redireccionar usuarios después de eliminar datos
            header('Location: /Bienes_Raices/admin/index.php?resultado=3');
            exit;
        }
    }
}

// Incluye un template

incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Administrador De Bienes Raices</h1>

    <?php if (intval($resultado) === 1) : ?>
        <p class="alerta exito">Anuncio Creado Correctamente</p>
    <?php elseif (intval($resultado) === 2) : ?>
        <p class="alerta exito">Anuncio Actualizado Correctamente</p>
    <?php elseif (intval($resultado) === 3) : ?>
        <p class="alerta exito">Anuncio Eliminado Correctamente</p>
    <?php endif; ?>

    <a href="/Bienes_Raices/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>

    <table class="propiedades">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody> <!-- Mostrar los Datos De la BD -->
            <?php while ($propiedad = mysqli_fetch_assoc($resultadoConsulta)) : ?>
                <tr>
                    <td><?php echo $propiedad['id']; ?></td>
                    <td><?php echo $propiedad['titulo']; ?></td>
                    <td><img src="/Bienes_Raices/imagenes/<?php echo $propiedad['imagen']; ?>" class="imagen-tabla"></td>
                    <td><?php echo "$ " . $propiedad['precio']; ?></td>
                    <td>
                        <form method="post" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                        <a href="/Bienes_Raices/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php
// Cerrar la conexión (Opcional)
mysqli_close($db);

incluirTemplate('footer');
?>
