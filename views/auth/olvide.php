<div class="contenedor olvide">
    
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Olvidaste Tu Contraseña</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/olvide" class="formulario" method="POST" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu Email">
            </div>
            <input type="submit" value="Enviar Correo" class="boton">
        </form>

        <div class="acciones">
            <a href="/">Ya Tienes Una Cuenta? Inicia Sesion</a>
            <a href="/crear">Aun No Tienes Una Cuenta? Crea  Una</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div> <!-- .contenedor -->