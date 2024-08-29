<?php
require 'includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion">

    <h2>Casas y Depas en Venta</h2>

    <?php
    $limite = 1000000000;
    include 'includes/templates/anuncios.php';
    ?>

</main>

<?php
incluirTemplate('footer');
?>

<script src="build/js/bundle.min.js"></script>
</body>

</html>