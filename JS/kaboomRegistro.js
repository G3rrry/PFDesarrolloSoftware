// Contador
function countdown() {
  var count = 15; // segundos
  var countdownElement = document.getElementById('countdown');
  var countdownInterval = setInterval(function() {
    count--;
    countdownElement.textContent = count;
    if (count <= 0) {
      clearInterval(countdownInterval);
      redirectTo('index.php'); // Cambia 'https://tupagina.com' por la URL a la que quieres redirigir
    }
  }, 100); // cada segundo
}

// Función para redirigir después de contar hasta 0
function redirectTo(url) {
  window.location.href = url;
}

// Iniciar el contador cuando la página se carga
window.onload = function() {
  countdown();
};

