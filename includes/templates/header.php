<?php
    if(isset($_SESSION)){
        session_start();
    }

    $auth = $_SESSION['login'] ?? false;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raices</title>
    <link rel="stylesheet" href="/Bienes_Raices/build/css/app.css">
    <link rel="icon" href="/Bienes_Raices/src/img/SVG/asset 3.svg" type="image/x-icon">
</head>
<body>
    
    <header class="header <?php echo $inicio ? 'inicio' : '' ; ?>">
        <div class="contenedor contenido-header">
            <div class="barra">
                <a href="/Bienes_Raices/index.php">
                    <img src="/Bienes_Raices/build/img/logo.svg" alt="Logotipo de Bienes Raices">
                </a>

                <div class="mobile-menu">
                    <img src="/Bienes_Raices/build/img/barras.svg" alt="icono menu responsive">
                </div>

                <div class="derecha">
                    <img class="dark-mode-boton" src="/Bienes_Raices/build/img/dark-mode.svg">
                    <nav class="navegacion">
                        <a href="/Bienes_Raices/nosotros.php">Nosotros</a>
                        <a href="/Bienes_Raices/anuncios.php">Anuncios</a>
                        <a href="/Bienes_Raices/blog.php">Blog</a>
                        <a href="/Bienes_Raices/contacto.php">Contacto</a>
                        <?php if($auth):?>
                            <a href="/Bienes_Raices/cerrar-sesion.php">Cerrar Sesión</a>

                        <?php endif; ?> 
                    </nav>
                </div>
   
                
            </div> <!--.barra-->

            <?php if ($inicio) {  ?>
            <h1>Venta de Casas y Apartamentos Exclusivos de Lujo</h1>
            <?php  } ?>

            
        </div>
    </header>