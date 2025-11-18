// JSON BASE A MOSTRAR EN FORMULARIO
var baseJSON = {
  precio: 0.0,
  unidades: 1,
  modelo: "XX-000",
  marca: "NA",
  detalles: "NA",
};

// FUNCIÓN CALLBACK DE BOTÓN "Buscar"
function buscarID(e) {
  /**
   * Revisar la siguiente información para entender porqué usar event.preventDefault();
   * http://qbit.com.mx/blog/2013/01/07/la-diferencia-entre-return-false-preventdefault-y-stoppropagation-en-jquery/#:~:text=PreventDefault()%20se%20utiliza%20para,escuche%20a%20trav%C3%A9s%20del%20DOM
   * https://www.geeksforgeeks.org/when-to-use-preventdefault-vs-return-false-in-javascript/
   */
  e.preventDefault();

  // SE OBTIENE EL VALOR A BUSCAR (id o nombre)
  var searchValue = document.getElementById("search").value.trim();
  if (!searchValue) return;

  // Determinar si es id (número) o nombre (texto)
  var params = "";
  if (!isNaN(searchValue)) {
    params = "id=" + encodeURIComponent(searchValue);
  } else {
    params = "nombre=" + encodeURIComponent(searchValue);
  }

  // SE CREA EL OBJETO DE CONEXIÓN ASÍNCRONA AL SERVIDOR
  var client = getXMLHttpRequest();
  client.open("POST", "./backend/read.php", true);
  client.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  client.onreadystatechange = function () {
    // SE VERIFICA SI LA RESPUESTA ESTÁ LISTA Y FUE SATISFACTORIA
    if (client.readyState == 4 && client.status == 200) {
      let productos = {};
      try {
        productos = JSON.parse(client.responseText);
      } catch (e) {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se pudo leer la respuesta del servidor.</td></tr>";
        return;
      }
      if (Object.keys(productos).length > 0) {
        let descripcion = "";
        descripcion +=
          "<li>precio: " +
          (productos.descripcion ? productos.descripcion.precio : "") +
          "</li>";
        descripcion +=
          "<li>unidades: " +
          (productos.descripcion ? productos.descripcion.unidades : "") +
          "</li>";
        descripcion +=
          "<li>modelo: " +
          (productos.descripcion ? productos.descripcion.modelo : "") +
          "</li>";
        descripcion +=
          "<li>marca: " +
          (productos.descripcion ? productos.descripcion.marca : "") +
          "</li>";
        descripcion +=
          "<li>detalles: " +
          (productos.descripcion ? productos.descripcion.detalles : "") +
          "</li>";
        let template = `
                        <tr>
                            <td>${productos.id}</td>
                            <td>${productos.nombre}</td>
                            <td><ul>${descripcion}</ul></td>
                        </tr>
                    `;
        document.getElementById("productos").innerHTML = template;
      } else {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se encontró el producto.</td></tr>";
      }
    }
  };
  client.send(params);
}

// BÚSQUEDA EXACTA POR ID
function buscarPorId() {
  var id = document.getElementById("searchId").value.trim();
  if (!id) return;
  var client = getXMLHttpRequest();
  client.open("POST", "./backend/read.php", true);
  client.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  client.onreadystatechange = function () {
    if (client.readyState == 4 && client.status == 200) {
      let productos = [];
      try {
        productos = JSON.parse(client.responseText);
      } catch (e) {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se pudo leer la respuesta del servidor.</td></tr>";
        return;
      }
      if (productos.length > 0) {
        let template = "";
        productos.forEach(function (prod) {
          let descripcion = "";
          descripcion +=
            "<li>precio: " +
            (prod.descripcion ? prod.descripcion.precio : "") +
            "</li>";
          descripcion +=
            "<li>unidades: " +
            (prod.descripcion ? prod.descripcion.unidades : "") +
            "</li>";
          descripcion +=
            "<li>modelo: " +
            (prod.descripcion ? prod.descripcion.modelo : "") +
            "</li>";
          descripcion +=
            "<li>marca: " +
            (prod.descripcion ? prod.descripcion.marca : "") +
            "</li>";
          descripcion +=
            "<li>detalles: " +
            (prod.descripcion ? prod.descripcion.detalles : "") +
            "</li>";
          template += `
            <tr>
              <td>${prod.id}</td>
              <td>${prod.nombre}</td>
              <td><ul>${descripcion}</ul></td>
            </tr>
          `;
        });
        document.getElementById("productos").innerHTML = template;
      } else {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se encontró el producto.</td></tr>";
      }
    }
  };
  client.send("id=" + encodeURIComponent(id));
}

// BÚSQUEDA FLEXIBLE POR TEXTO
function buscarProducto() {
  var q = document.getElementById("searchText").value.trim();
  if (!q) return;
  var client = getXMLHttpRequest();
  client.open("POST", "./backend/read.php", true);
  client.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  client.onreadystatechange = function () {
    if (client.readyState == 4 && client.status == 200) {
      let productos = [];
      try {
        productos = JSON.parse(client.responseText);
      } catch (e) {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se pudo leer la respuesta del servidor.</td></tr>";
        return;
      }
      if (productos.length > 0) {
        let template = "";
        productos.forEach(function (prod) {
          let descripcion = "";
          descripcion +=
            "<li>precio: " +
            (prod.descripcion ? prod.descripcion.precio : "") +
            "</li>";
          descripcion +=
            "<li>unidades: " +
            (prod.descripcion ? prod.descripcion.unidades : "") +
            "</li>";
          descripcion +=
            "<li>modelo: " +
            (prod.descripcion ? prod.descripcion.modelo : "") +
            "</li>";
          descripcion +=
            "<li>marca: " +
            (prod.descripcion ? prod.descripcion.marca : "") +
            "</li>";
          descripcion +=
            "<li>detalles: " +
            (prod.descripcion ? prod.descripcion.detalles : "") +
            "</li>";
          template += `
            <tr>
              <td>${prod.id}</td>
              <td>${prod.nombre}</td>
              <td><ul>${descripcion}</ul></td>
            </tr>
          `;
        });
        document.getElementById("productos").innerHTML = template;
      } else {
        document.getElementById("productos").innerHTML =
          "<tr><td colspan='3'>No se encontraron productos.</td></tr>";
      }
    }
  };
  client.send("q=" + encodeURIComponent(q));
}

// FUNCIÓN CALLBACK DE BOTÓN "Agregar Producto"
function agregarProducto(e) {
  e.preventDefault();

  // SE OBTIENE DESDE EL FORMULARIO EL JSON A ENVIAR
  var productoJsonString = document.getElementById("description").value;
  // SE CONVIERTE EL JSON DE STRING A OBJETO
  var finalJSON = JSON.parse(productoJsonString);
  // SE AGREGA AL JSON EL NOMBRE DEL PRODUCTO
  finalJSON["nombre"] = document.getElementById("name").value;

  // SE OBTIENE EL STRING DEL JSON FINAL
  productoJsonString = JSON.stringify(finalJSON, null, 2);

  // SE CREA EL OBJETO DE CONEXIÓN ASÍNCRONA AL SERVIDOR
  var client = getXMLHttpRequest();
  client.open("POST", "./backend/create.php", true);
  client.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  client.onreadystatechange = function () {
    // SE VERIFICA SI LA RESPUESTA ESTÁ LISTA Y FUE SATISFACTORIA
    if (client.readyState == 4 && client.status == 200) {
      try {
        var resp = JSON.parse(client.responseText);
        window.alert(resp.message);
      } catch (e) {
        window.alert("Error inesperado en la respuesta del servidor.");
      }
    }
  };
  client.send(productoJsonString);
}

// SE CREA EL OBJETO DE CONEXIÓN COMPATIBLE CON EL NAVEGADOR
function getXMLHttpRequest() {
  var objetoAjax;

  try {
    objetoAjax = new XMLHttpRequest();
  } catch (err1) {
    /**
     * NOTA: Las siguientes formas de crear el objeto ya son obsoletas
     *       pero se comparten por motivos historico-académicos.
     */
    try {
      // IE7 y IE8
      objetoAjax = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (err2) {
      try {
        // IE5 y IE6
        objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (err3) {
        objetoAjax = false;
      }
    }
  }
  return objetoAjax;
}

function init() {
  /**
   * Convierte el JSON a string para poder mostrarlo
   * ver: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/JSON
   */
  var JsonString = JSON.stringify(baseJSON, null, 2);
  document.getElementById("description").value = JsonString;
}
