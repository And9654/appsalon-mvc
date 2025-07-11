<h1 class="nombre-pagina">Confirmar cuenta</h1>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<div class="acciones">
    <?php if(array_key_exists('errores', $alertas)){ ?>
        <a href="/crear-cuenta">¿Aún no cuentas con una cuenta? Crea una</a>
    <?php } else { ?>
        <a href="/">Iniciar sesión</a>
    <?php } ?>
</div>