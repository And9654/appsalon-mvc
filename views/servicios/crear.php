<h1 class="nombre-pagina">Nuevo servicio</h1>
<p class="descripcion-pagina">LLena todos los campos para añadir un nuevo servicio</p>

<?php 
    include_once __dir__ . '/../templates/barra.php';
?>

<?php 
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form action="/servicios/crear" class="formulario" method="POST">

    <?php 
        include_once __DIR__ . '/formulario.php';
    ?>

    <input type="submit" class="boton" value="Guardar">
</form>