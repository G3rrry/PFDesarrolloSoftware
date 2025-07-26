document.addEventListener("DOMContentLoaded", function() {
  var requiereCertificadoCheckbox = document.getElementById("requiere_certificado");
  var parametrosReferenciaCheckbox = document.getElementById("parametros_referencia");
  var parametrosReferenciaContainer = document.getElementById("parametros_referencia_container");
  var alveografoContainer = document.getElementById("parametros_alveografo");
  var farinografoContainer = document.getElementById("parametros_farinografo");
  var parametrosInternacionalesCheckbox = document.getElementById("parametros_internacional");
  var parametrosInternacionalesContainer = document.getElementById("parametros_internacional_container");

  function toggleOptions() {
    var isParametrosChecked = parametrosReferenciaCheckbox.checked;
    alveografoContainer.style.display = isParametrosChecked ? "" : "none";
    farinografoContainer.style.display = isParametrosChecked ? "" : "none";
  }

  function toggleCertificado() {
    var isCertificadoChecked = requiereCertificadoCheckbox.checked;
    parametrosReferenciaContainer.style.display = isCertificadoChecked ? "" : "none";
    parametrosInternacionalesContainer.style.display = isCertificadoChecked ? "" : "none";
    if (!isCertificadoChecked) {
      alveografoContainer.style.display = "none";
      farinografoContainer.style.display = "none";
      parametrosReferenciaCheckbox.checked = false;
      parametrosInternacionalesCheckbox.checked = false;
      // Limpia los campos si se desactiva "Requiere Certificado"
      clearInternationalParameters();
    }
  }

  function toggleInternacional() {
    var isInternacionalChecked = parametrosInternacionalesCheckbox.checked;
    setInternationalParameters(isInternacionalChecked);
  }

  function setInternationalParameters(isSet) {
    //
    //Alveografo
    //
    // Establece o limpia los valores de los parámetros internacionales
    // Valor puesto y valor cuando se desactiva lo borra y lo deja null
    document.getElementById("tenacidad_sup").value = isSet ? "55" : "";
    document.getElementById("tenacidad_inf").value = isSet ? "50" : "";
    document.getElementById("extensibilidad_sup").value = isSet ? "120" : "";
    document.getElementById("extensibilidad_inf").value = isSet ? "110" : "";
    document.getElementById("fuerza_panadera").value = isSet ? "220" : "";
    //No se puede este valor default pq es calculo matematico de integrar a menos que se integre un subproceso que lo haga
    document.getElementById("area_curva").value = isSet ? "525" : "";
    //Relacion curva eliminado a CP por que no se si dejarlo o quitarlo

    //document.getElementById("relacion_curva").value = isSet ? "0.50" : "";
    //Farinografo
    document.getElementById("absorcion_agua_sup").value = isSet ? "0.55" : "";
    document.getElementById("absorcion_agua_inf").value = isSet ? "0.65" : "";
    document.getElementById("tiempo_desarrollo_masa_sup").value = isSet ? "180" : "";
    document.getElementById("tiempo_desarrollo_masa_inf").value = isSet ? "300" : "";
    document.getElementById("estabilidad_sup").value = isSet ? "300" : "";
    document.getElementById("estabilidad_inf").value = isSet ? "600" : "";
    //No se puede este valor default pq es calculo matematico de integrar a menos que se integre un subproceso que lo haga
    document.getElementById("grado_reblandecimiento_sup").value = isSet ? "40" : "";
    document.getElementById("grado_reblandecimiento_inf").value = isSet ? "90" : "";
  
  }

  function clearInternationalParameters() {
    // Limpia todos los campos de parámetros internacionales
    setInternationalParameters(false);
  }

  // Event listeners
  requiereCertificadoCheckbox.addEventListener("change", toggleCertificado);
  parametrosReferenciaCheckbox.addEventListener("change", toggleOptions);
  parametrosInternacionalesCheckbox.addEventListener("change", toggleInternacional);

  // Inicialización
  toggleCertificado();
  toggleOptions();
});

//verificar si estan todos los datos minimos necesarios
