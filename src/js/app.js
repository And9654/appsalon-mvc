let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const citas = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

// ? llama a las funciones a ejecutar
function iniciarApp(){
    mostrarPaginacion();
    mostrarSeccion();
    tabs();
    paginaAnterior();
    paginaSiguiente();
    consultarAPI();
    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();
    mostrarResumen();
}

// ? muestra la sección actual en la barra de secciones
function mostrarSeccion(){

    const seccionAnterior = document.querySelector('.mostrar');

    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');

    const tabAnterior = document.querySelector('.actual');
    
    if(tabAnterior){
        tabAnterior.classList.remove('actual'); 
    }

    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

// ? se encarga de obtener la sección la cual se quiere mostrar
function tabs(){
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();
            mostrarPaginacion();
        });
    });

}

// ? se encarga de mostrar u ocultar los botones de anterior y siguiente
function mostrarPaginacion(){
    const tabAnterior = document.querySelector('#anterior');
    const tabSiguiente = document.querySelector('#siguiente');

    if(paso == 1){
        tabAnterior.classList.add('ocultar');
        tabSiguiente.classList.remove('ocultar');
    } else if(paso == 3){
        tabAnterior.classList.remove('ocultar');
        tabSiguiente.classList.add('ocultar');
        mostrarResumen();
    } else{
        tabAnterior.classList.remove('ocultar');
        tabSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

// ? se encarga de moverse una sección atras
function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');

    paginaAnterior.addEventListener('click', function(){
        if(paso <= pasoInicial) return;
        paso--;
        mostrarPaginacion();
    });
}

// ? se encarga de moverse una sección adelante
function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');

    paginaSiguiente.addEventListener('click', function(){
        if(paso >= pasoFinal) return;
        paso++;
        mostrarPaginacion();
    });
}

// ? se encarga de obtener los servicios obtenidos de PHP
async function consultarAPI(){
    
    const url = `${location.origin}/api/servicios`;
    
    try{

        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    }catch(error){
        console.log(error);
    }

}

// ? se encarga de crear y mostrar los servicios que se obtuvieron de la API
function mostrarServicios(servicios){
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio); 
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
        
    });
}


function seleccionarServicio(servicio){
    const { id } = servicio;
    const { servicios } = citas;
    
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
    
    if(servicios.some( agregado => agregado.id === id)){
        citas.servicios = servicios.filter(agregado => agregado.id != id );
        divServicio.classList.remove('seleccionado');
    } else {
        citas.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
}

// ? obtiene el id del cliente
function idCliente(){
    citas.id = document.querySelector('#id').value;
}

// ? obtiene el nombre del cliente
function nombreCliente(){
    citas.nombre = document.querySelector('#nombre').value;
}

// ? obtiene la fecha que eligió el usuario y verifica que no sea un día en fin de semana
function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia = new Date(e.target.value).getUTCDay();
        
        if([6,0].includes(dia)){
            e.target.value = ''; // ? limpia el elemento de fecha si se seleccionó un fin de semana
            mostrarAlerta('Fines de semana no permitidos', 'errores', '.formulario');
        } else {
            citas.fecha = e.target.value; // ? almacena la fecha en el objeto de citas si es que todo está bien
        }
    });
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {

        const horaCita = e.target.value;
        const hora = horaCita.split(':')[0];

        if(hora < 8 || hora > 20){
            e.target.value = '';
            mostrarAlerta('Hora no válida', 'errores', '.formulario');
        } else {
            citas.hora = e.target.value;
        }

    });
}

// ? se encarga de mostrar las alertas
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){

    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }

}

// ? muestra el resumen si todos los datos se han seleccionado
function mostrarResumen(){
    const resumen = document.querySelector('.seccion-resumen');

    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }
    
    if(Object.values(citas).includes('') || citas.servicios.length == 0){
        mostrarAlerta('Faltan datos o no se han seleccionado servicios', 'errores', '.seccion-resumen', false);
        return;
    }

    const { nombre, fecha, hora, servicios } = citas;

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // ? Formatear fecha en español
    const fechaObj = new Date(fecha);
    const dia = fechaObj.getDate() + 2;
    const mes = fechaObj.getMonth();
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};

    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} horas`;

    const headingServicios = document.createElement('H3');
    headingServicios.textContent = "Resumen de Servicios";
    resumen.appendChild(headingServicios);

    // ? muestra los servicios del resumen
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;
        
        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);
        
        resumen.appendChild(contenedorServicio);

    });

    const citaServicios = document.createElement('H3');
    citaServicios.textContent = "Resumen de Cita";
    resumen.appendChild(citaServicios);

    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);

}

// ? contiene el código de la API que manda los datos a PHP
async function reservarCita(){

    
    const { id, nombre, fecha, hora, servicios } = citas;
    const idServicios = servicios.map( servicio => servicio.id );
    
    const datos = new FormData();
    datos.append('usuarioId', id);
    datos.append('nombre', nombre);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('servicios', idServicios);

    const url = `${location.origin}/api/citas`;
    
    try {
        
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
    
        const resultado = await respuesta.json();
    
        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Cita creada",
                text: "Tu cita ha sido creada con éxito",
                button: "Ok",
                customClass: {
                    popup: 'ventana-modal' // ? agrega la clase css a la ventana
                }
            }).then( () => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        }

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ha ocurrido un error al intentar crear tu cita",
            button: "Ok",
            customClass: {
                popup: 'ventana-modal'
            }
        });
    }

    // console.log([...datos]);
}