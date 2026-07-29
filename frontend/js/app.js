const URL = "../backend/controller/PersonaController.php";

let personaSeleccionada = null;

document.addEventListener("DOMContentLoaded", () => {

    cargarPersonas();

    document
        .getElementById("continuar")
        .addEventListener("click", continuar);

});


//========================================
// CARGAR PERSONAS
//========================================

async function cargarPersonas() {

    try {

        const respuesta = await fetch(`${URL}?fun=obtenerTodos`);
        const json = await respuesta.json();
        const contenedor = document.getElementById("listaPersonas");
        
        contenedor.innerHTML = "";

        json.data.forEach(persona => {

            const boton = document.createElement("button");

            boton.className = "persona";

            boton.innerHTML = "🙂 " + persona.nombre;

            boton.onclick = () => seleccionarPersona(persona.id, boton);

            contenedor.appendChild(boton);

        });

    } catch (e) {

        console.log("Error cargando personas.", e);

    }

}


//========================================
// SELECCIONAR PERSONA
//========================================

function seleccionarPersona(id, boton) {

    document.querySelectorAll(".persona").forEach(b => {

        b.classList.remove("activa");

    });

    boton.classList.add("activa");

    personaSeleccionada = id;

    document.getElementById("continuar").disabled = false;

}


//========================================
// CONTINUAR
//========================================

async function continuar() {

    if (personaSeleccionada == null)
        return;

    try {

        const datos = new FormData();

        datos.append("idPersona", personaSeleccionada);

        const respuesta = await fetch(

            `${URL}?fun=obtenerColorPersona`,

            {
                method: "POST",
                body: datos
            }

        );

        const json = await respuesta.json();
        
        if (!json.success) {

            alert(json.msj);

            return;

        }

        // Ya tenía color

        if (json.data.color != null) {
            
            mostrarResultado(json.data);

            return;

        }

        // No tenía color
        const respuestaColor = await fetch(
 
            `${URL}?fun=asignarColor`,

            {
                method: "POST",
                body: datos
            }

        );

        const jsonColor = await respuestaColor.json();
        if(!jsonColor.success){
            console.log(respuestaColor)
            alert("Todavía no estas en un grupo")
            return
        }

        mostrarRuleta();

        await new Promise(r => setTimeout(r, 4000));

        


        if (!jsonColor.success) {

            console.log(jsonColor.msj);

            return;

        }

        mostrarResultado(jsonColor.data);

    }

    catch (e) {

        console.error(e);

        console.log("Ocurrió un error.", e);

    }

}


//========================================
// PANTALLAS
//========================================

function ocultarPantallas() {

    document.getElementById("inicio")
        .classList.add("d-none");

    document.getElementById("ruleta")
        .classList.add("d-none");

    document.getElementById("resultado")
        .classList.add("d-none");

}

function mostrarInicio() {

    ocultarPantallas();

    document.getElementById("inicio")
        .classList.remove("d-none");

}

function mostrarRuleta() {

    ocultarPantallas();

    document.getElementById("ruleta")
        .classList.remove("d-none");

}

function mostrarResultado(data) {
    console.log(data)
    ocultarPantallas();

    document.getElementById("resultado")
        .classList.remove("d-none");

    document.getElementById("nombreColor")
        .innerText = data.color.toUpperCase();

    const circulo = document.getElementById("circuloColor");

    circulo.style.background = obtenerColorCSS(data.color);

}


//========================================
// COLORES CSS
//========================================

function obtenerColorCSS(color) {

    switch (color.toLowerCase()) {

        case "rojo":
            return "#e74c3c";

        case "verde":
            return "#2ecc71";

        case "azul":
            return "#3498db";

        case "amarillo":
            return "#f1c40f";

        case "naranja":
            return "#e67e22";

        case "violeta":
            return "#9b59b6";

        case "rosa":
            return "#ff66cc";

        case "blanco":
            return "#ecf0f1";

        case "negro":
            return "#2d3436";

        default:
            return "#95a5a6";

    }

}