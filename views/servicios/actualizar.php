<h1 class="nombre-pagina">Actualizar servicios</h1>
<p class="descripcion-pagina">Modifica los valores del formulario</p>

<?php 
    include_once __dir__ . '/../templates/barra.php';
    include_once __dir__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST">
    <?php 
        include_once __DIR__ . '/formulario.php';
    ?>
    <input type="submit" class="boton" value="Actualizar">
</form>