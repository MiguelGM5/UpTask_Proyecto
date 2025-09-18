<div class="contenedor login">
    
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesion</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/" class="formulario" method="POST">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu Email">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Tu Contraseña">
            </div>

            <input type="submit" value="Iniciar Sesion" class="boton">
        </form>

        <div class="acciones">
            <a href="/crear">Aun no tienes una cuenta? Obtener Una</a>
            <a href="/olvide">Olvidaste tu Contraseña?</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div> <!-- .contenedor -->