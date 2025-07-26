  function hoverDropdown(dropdownId) {
    document.getElementById(dropdownId).style.display = 'block';
  }

  function hideDropdown() {
    var dropdowns = document.getElementsByClassName('dropdown');
    for (var i = 0; i < dropdowns.length; i++) {
      dropdowns[i].style.display = 'none';
    }
  }