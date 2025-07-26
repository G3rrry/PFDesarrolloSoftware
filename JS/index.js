function toggleSubmenu(submenuId) {
  var submenu = document.getElementById(submenuId);
  var isDisplaying = submenu.style.display !== 'none';

  // Cierra todos los submenús primero
  closeAllSubmenus();

  // Si el submenú no estaba mostrándose, ábrelo
  if (!isDisplaying) {
    submenu.style.display = 'block';
  } else {
    submenu.style.display = 'none';
  }
}

// Cierra todos los submenús
function closeAllSubmenus() {
  var submenus = document.querySelectorAll('.submenu');
  submenus.forEach(function(submenu) {
    submenu.style.display = 'none';
  });
}

// Cierra los submenús si se hace clic en cualquier otro lugar de la página
window.onclick = function(event) {
  if (!event.target.matches('.module, .module *')) {
    closeAllSubmenus();
  }
}
