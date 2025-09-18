<div class="contenedor crear">
    
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu Cuenta</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/crear" class="formulario" method="POST">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Tu Nombre" value="<?php echo $usuario->nombre; ?>">
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu Email" value="<?php echo $usuario->email; ?>">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Tu Contraseña">
            </div>
            <div class="campo">
                <label for="password2">Repetir Contraseña</label>
                <input type="password" name="password2" id="password2" placeholder="Repite tu Contraseña">
            </div>

            <input type="submit" value="Crear Cuenta" class="boton">
        </form>

        <div class="acciones">
            <a href="/">Ya Tienes Una Cuenta? Inicia Sesion</a>
            <a href="/olvide">Olvidaste Tu Contraseña?</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div> <!-- .contenedor -->