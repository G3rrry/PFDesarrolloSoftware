  // Esta función se llama cada vez que se cambia el estado del combobox.
  function toggleCausaBaja() {
    // Obtiene el combobox de estado y el campo de la causa de baja.
    var estado = document.getElementById('estado');
    var causaBaja = document.getElementById('causa-baja');

    // Verifica si el estado seleccionado es 'Inactivo'
    if (estado.value === 'Inactivo') {
      causaBaja.style.display = 'block'; // Muestra la causa de baja
    } else {
      causaBaja.style.display = 'none';  // Oculta la causa de baja
    }
  }

  // Agrega un escucha de eventos al combobox de estado para que llame a la función toggleCausaBaja cada vez que se cambie su valor.
  document.getElementById('estado').addEventListener('change', toggleCausaBaja);

  // Llama a la función al cargar la página por si el combobox ya está establecido en 'Inactivo'.
  window.onload = toggleCausaBaja;
