(function(){
    //Obtener Tareas
    ObtenerTareas();
    let tareas = [];
    let filtradas = [];

    //Boton para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', () => {
        mostrarformulario();
    });

    //Filtros
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');

    filtros.forEach(radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e){
        const filtro = e.target.value;
        
        if(filtro !== ''){
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        }else{
            filtradas = [];
        }

        mostrarTareas();
    }

    async function ObtenerTareas() {
        try {
            const id = obtenerProyecto();
            const url = `api/tareas?id=${id}`

            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            tareas = resultado.tareas;
            mostrarTareas();
            
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas() {
        limpiarTareas();
        totalPendientes();
        totalCompletas();


        const arrayTareas = filtradas.length ? filtradas : tareas;

        if(arrayTareas.length == 0){
            const contenedorTareas = document.querySelector('#listado-tareas');

            const noTareas = document.createElement('LI');
            noTareas.textContent = 'No hay tareas';
            noTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(noTareas);
            return;
        }

        const estados = {
            0: 'Pendiente', 
            1: 'Completa'
        }

        arrayTareas.forEach(tarea => {

            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');

            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;

            nombreTarea.ondblclick = () =>{
                mostrarformulario(true, {...tarea});
            };

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //botones
            const btnStatus = document.createElement('BUTTON');
            btnStatus.classList.add('estado-tarea');
            btnStatus.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnStatus.textContent = estados[tarea.estado];
            btnStatus.dataset.estadoTarea = tarea.estado;

            btnStatus.ondblclick = function(){
                cambiarEstadoTarea({...tarea});
            };

            const btnDelete = document.createElement('BUTTON');
            btnDelete.classList.add('eliminar-tarea');
            btnDelete.dataset.idTarea = tarea.id;
            btnDelete.textContent = 'Eliminar';

            btnDelete.ondblclick = function(){
                confirmarEliminarTarea({...tarea});
            };

            opcionesDiv.appendChild(btnStatus);
            opcionesDiv.appendChild(btnDelete);
            
            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);

        });
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        const pendientesRadio = document.querySelector('#pendientes');

        if(totalPendientes.length === 0){
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletas(){
        const totalCompletas = tareas.filter(tarea => tarea.estado === "1");
        const completasRadio = document.querySelector('#completadas');

        if(totalCompletas.length === 0){
            completasRadio.disabled = true;
        }else{
            completasRadio.disabled = false;
        }
    }

    function mostrarformulario(editar = false, tarea = {}){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>${editar ? 'Editar Tarea' : 'Añade una Nueva Tarea'}</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input 
                    type="text" 
                    name="tarea" 
                    placeholder="${tarea.nombre ? 'Edita la Tarea' : 'Añadir Tarea al Proyecto Actual'}" 
                    id="tarea" 
                    value="${tarea.nombre ? tarea.nombre : ''}" 
                />
                </div>
                <div class="opciones">
                    <button type="button" class="cerrar-modal"> Cancelar </button>
                    
                    <input 
                        type="submit" 
                        class="submit-nueva-tarea" 
                        value="${tarea.nombre ? 'Guardar Cambios' : 'Añadir tarea'}" 
                    />
                </div>
            </form>
        `;

        setTimeout(()=>{
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
            
        }, 100);
        
        modal.addEventListener('click', e => {
            e.preventDefault();

            if (e.target.classList.contains('cerrar-modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');

                setTimeout(() => {
                    modal.remove();
                },500);

            }

            if(e.target.classList.contains('submit-nueva-tarea')){
                const nombreTarea = document.querySelector('#tarea').value.trim();

                if (nombreTarea === '') {
                    //Mostrar alerta de error
                    mostrarAlerta('El Nombre de la Tarea es Obligatorio', 'error', document.querySelector('.formulario legend'));
                    return;
                }

                if(editar){
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                }else{
                    agregarTarea(nombreTarea);
                }

            }
        });
        
        document.querySelector('.dashboard').appendChild(modal);
    }

    //Muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia){
        //Previene la creacion de multiples alertas
        const alertaPrevia = document.querySelector('.alerta');

        if(alertaPrevia){
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        //Inserta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        //Eliminar la alerta despues de 5 segundos
        setTimeout(()=>{
            alerta.remove();
        }, 5000);
    }

    //consultar el server para añadir una nueva tarea 
    async function agregarTarea(tarea){
        //Construir la peticion
        const datos = new FormData();

        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = "http://localhost:3000/api/tareas";
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos,
            });

            const resultado = await respuesta.json();

            console.log(resultado);
            
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                },3000);

                //Agregar el objeto de tarea al global de tarea
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: 0,
                    proyectoId: resultado.proyectoId
                };

                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function cambiarEstadoTarea(tarea) {
        const nuevoEstado = tarea.estado === '1' ? '0' : '1';
        tarea.estado = nuevoEstado;
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea){
        const {estado, id, nombre} = tarea;
        
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = 'http://localhost:3000/api/tareas/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.respuesta.tipo == 'exito') {
                Swal.fire({
                    title: resultado.respuesta.mensaje,
                    icon: "success"
                });

                const modal = document.querySelector('.modal');
                if(modal){
                    modal.remove();
                }

                tareas = tareas.map(tareaMemoria => {
                    if(tareaMemoria.id === id){
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }

                    return tareaMemoria;
                });

                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
        
    }

    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: "Estas seguro?",
            text: "No se puede revertir!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {

                eliminarTarea(tarea);

                Swal.fire({
                  title: "Eliminado!",
                  text: "Se ha eliminado la tarea.",
                  icon: "success"
                });
            }
        });  
    }

    async function eliminarTarea(tarea) {

        const {estado, id, nombre} = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = 'http://localhost:3000/api/tareas/eliminar';
            
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if(resultado.resultado){
                //mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.contenedor-nueva-tarea'));

                Swal.fire({
                    title: "Eliminado!",
                    text: resultado.mensaje,
                    icon: "success"
                });

                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== tarea.id);
                
                mostrarTareas();
            }

        } catch (error) {
            
        }
    }

    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas() {
        const listadoTareas = document.querySelector('#listado-tareas');
        
        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }

})(); //IIFE


