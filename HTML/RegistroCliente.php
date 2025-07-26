<?php
session_start();
$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de clientes</title>
  <link rel="stylesheet" href="../CSS/Login.css">
  <link rel="stylesheet" href="../CSS/RNU.css">
  <link rel="stylesheet" href="../CSS/header&footer.css">
  <link rel="stylesheet" href="../CSS/RegistroBoton.css">
</head>
<body>

<header class="header">
    <div class="nav-container">
      <a href="index.php" class="logo">
        <img src="../Imagenes/LogoHeader.png" alt="Logo">
      </a>
      <nav class="navbar">
        <ul class="nav-links">
          <!-- Todos los usuarios autenticados pueden ver estos enlaces -->
          <li class="nav-item"><a href="index.php">Inicio</a></li>
          <li class="nav-item"><a href="ContactUs.html">Contacto Sistemas</a></li>
          
          <?php if ($rol == 1 || $rol == 2): ?>
          <!-- Registros -->
          <li class="dropdown">
            <a href="javascript:void(0)">Registros</a>
            <div class="dropdown-content">
              <a href="RegistroCliente.php">Registro Cliente</a>
              <a href="RegistroEquipoLab.php">Registro Equipo Laboratorio</a>
              <a href="RegistroLotes.php">Registro de Lotes</a>
              <a href="RegistroResultados.php">Registro de Resultados de Lotes</a>
              <a href="RegistroResultadosPedidos.php">Registro de Resultados de Pedidos</a>
            </div>
          </li>
          <?php endif; ?>

          <!-- Consultas -->
          <li class="dropdown">
            <a href="javascript:void(0)">Consultas</a>
            <div class="dropdown-content">
              <a href="ConsultaClientes.php">Consulta Clientes</a>
              <a href="ConsultaEquipoLab.php">Consulta Equipo Laboratorio</a>
              <a href="ConsultaLotes.php">Consulta de Lotes</a>
              <a href="estadisticas.php">Consulta de Estadísticas</a>

            </div>
          </li>

          <?php if ($rol == 1 || $rol == 2): ?>
          <!-- Emisión de certificados y consulta de historial -->
          <li class="nav-item"><a href="EmisionCertificado.php">Emisión Certificado</a></li>
          <?php endif; ?>

          <li class="nav-item"><a href="ConsultaHistorialCertif.php">Consulta Historial Certificados</a></li>

          <?php if ($rol == 1): ?>
          <!-- Administrar usuarios -->
          <li class="nav-item"><a href="admin.php">Administrar Usuarios</a></li>
          <?php endif; ?>
          
          <!-- Opción de cerrar sesión -->
          <li class="nav-item"><a href="login.php">Cerrar Sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

<div class="container">
    <h2>Registro de clientes</h2>
    <div class="logo">
        <img src="../Imagenes/Logo-Harinas-Elizondo.png" alt="Logo">
    </div>
    <div class="form-container">
      <form action="RegistroClienteExitoso.php" method="post">
        <div class="form-group">
          <label for="NumExt_Factura">ID SAP:</label>
          <input type="text" id="ID_SAP_CLIENTE" name="ID_SAP_CLIENTE"  maxlength="6" minlength="6" required pattern="[0-9]{6}">
        </div>
        <div class="form-row">
              <div class="form-group">
                  <label for="nombre_cliente">Nombre del cliente:</label>
                  <input type="text" id="nombre_cliente" name="nombre_cliente" required maxlength="50">
                  <?php $nombreCliente="nombre_cliente"?>
              </div>
              <div class="form-group">
                  <label for="nombre_contacto">Nombre del contacto:</label>
                  <input type="text" id="nombre_contacto" name="nombre_contacto" required maxlength="50">
              </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email_cliente">Correo electrónico:</label>
              <input type="email" id="correo_cliente" name="correo_cliente" required maxlength="30">
            </div>
            <div class="form-group">
              <label for="correo_contacto">Correo del contacto:</label>
            <input type="email" id="correo_contacto" name="correo_contacto" required maxlength="50">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
            <label for="telefono_cliente">Número telefónico:</label>
            <input type="text" id="telefono_cliente" name="telefono_cliente" required pattern="[0-9]{10}" maxlength="10">
            </div>
            <div class="form-group">
            <label for="telefono_contacto">Número telefónico contacto:</label>
            <input type="text" id="telefono_contacto" name="telefono_contacto" required pattern="[0-9]{10}" maxlength="10">
            </div>
          </div>


        <div class="form-group">
            <label for="CP_factura">Código postal para entrega:</label>
            <input type="text" id="CP_entrega" name="CP_entrega" required pattern="[0-9]{5}" maxlength="5">
        </div>

          <div class="form-row">
              <div class="form-group">
                  <label for="domicilio">Calle de entrega:</label>
                  <input type="text" id="domicilio" name="domicilio" required maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
              </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email_cliente">Colonia Entrega:</label>
 <input type="text" id="colonia_cliente" name="colonia_cliente" required maxlength="30" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="NumExt_Factura">Número exterior Entrega:</label>
  <input type="text" id="NumExt_Entrega" name="NumExt_Entrega" required maxlength="3" minlength="1" pattern="[0-9]{1,2,3}">
            </div>
            <div class="form-group">
              <label for="NumExt_Factura">Número Interior Entrega:</label>
   <input type="text" id="NumInt_Entrega" name="NumInt_Entrega" required maxlength="3" minlength="1" pattern="[0-9]{1,2,3}">
            </div>
          </div>

          <p><strong>Datos para facturación</strong></p>
          <div class="form-group">
              <label for="rfc">RFC persona Física:</label>
              <input type="text" id="rfc" name="rfc" required maxlength="13"  pattern="[A-Z]{3,4}[0-9]{6}[A-Z0-9]{3}">
          </div>
          <div class="form-group">
            <label for="CP_factura">Razón Social:</label>
            <input type="text" id="razon_factura" name="razon_factura" required>
          </div>
          <div class="form-group">
            <label for="CP_factura">Código postal Factura:</label>
            <input type="text" id="CP_factura" name="CP_factura" required pattern="[0-9]{4,5}" maxlength="5">
        </div>
          <div class="form-group">
            <label for="Localidad_factura">Calle Registrada en Factura:</label>
            <input type="text" id="Domicilio" name="Domicilio" required maxlength="50">
          </div>
          <div class="form-group">
            <label for="Localidad_factura">Colonia Factura:</label>
            <input type="text" id="Colonia_factura" name="Colonia_factura" required maxlength="50">
         </div>

          <div class="form-row">
            <div class="form-group">
              <label for="NumExt_Factura">Número exterior Factura:</label>
 <input type="text" id="NumExt_Factura" name="NumExt_Factura" required maxlength="3" minlength="1" pattern="[0-9]{1,2,3}">
            </div>
            <div class="form-group">
              <label for="NumExt_Factura">Número Interior Factura:</label>
  <input type="text" id="NumInt_Factura" name="NumInt_Factura" required maxlength="3" minlength="1" pattern="[0-9]{1,2,3}">
           </div>
          </div>

      <!-- Boton subir -->

      <button type="submit" class="submit-button">Enviar</button>
               <!-- Checkbox para por si el cliente quiere parametros de referencia -->
               <div class="form-group">
          <label>
            <input type="hidden" name="requiere_certificado" value="0">
            <input type="checkbox" id="requiere_certificado" name="requiere_certificado" value="1">
            Requiere Certificado
          </label>
        </div>

  </div>






        <!-- Checkbox para Parámetros de referencia propios -->
        <div class="form-group" id="parametros_referencia_container" style="display: none;">
          <label>
            <input type="checkbox" id="parametros_referencia" name="parametros_referencia">
            Parámetros de referencia específicos
          </label>
        </div>

        <!-- Campos para Alveógrafo -->
        <div id="parametros_alveografo" style="display: none;">
          <h3>Parámetros Alveógrafo</h3>

          <div class="form-group">
            <label for="tenacidad">Tenacidad:</label>
            <input type="text" id="tenacidad_sup" name="tenacidad_sup" placeholder="Límite Superior" maxlength="2" pattern="[0-9]{1,2}">
            <input type="text" id="tenacidad_inf" name="tenacidad_inf" placeholder="Límite Inferior" maxlength="2" pattern="[0-9]{1,2}">
          </div>
          <div class="form-group">
            <label for="extensibilidad">Extensibilidad:</label>
            <input type="text" id="extensibilidad_sup" name="extensibilidad_sup" placeholder="Límite Superior" maxlength="3" pattern="[0-9]{1,2,3}">
            <input type="text" id="extensibilidad_inf" name="extensibilidad_inf" placeholder="Límite Inferior" maxlength="3" pattern="[0-9]{1,2,3}">
          </div>
          <div class="form-group">
            <label for="fuerza_panadera">Fuerza panadera:</label>
            <input type="text" id="fuerza_panadera_sup" name="fuerza_panadera_sup" placeholder="Límite Superior" maxlength="3" pattern="[0-9]{1,2,3}">
            <input type="text" id="fuerza_panadera_inf" name="fuerza_panadera_inf" placeholder="Límite Inferior" maxlength="3" pattern="[0-9]{1,2,3}">
          </div>
          <div class="form-group">
            <label for="relacion_curva">Relación de la curva:</label>
            <input type="text" id="relacion_curva_sup" name="relacion_curva_sup" placeholder="Límite Superior" maxlength="3" pattern="[0-9]{1,2,3}">
            <input type="text" id="relacion_curva_inf" name="relacion_curva_inf" placeholder="Límite Inferior" maxlength="3" pattern="[0-9]{1,2,3}">
          </div>

          <!-- ... -->

        <!-- Campos para Farinógrafo -->
        <div id="parametros_farinografo" style="display: none;">
          <h3>Parámetros Farinógrafo</h3>
          <!-- Parámetros -->
          <div class="form-group">
            <label for="absorcion_agua">Absorción del agua:</label>
            <input type="text" id="absorcion_agua_sup" name="absorcion_agua_sup" placeholder="Límite Superior" maxlength="4" pattern="[0-9]{1,2,3,4}">
            <input type="text" id="absorcion_agua_inf" name="absorcion_agua_inf" placeholder="Límite Inferior" maxlength="4" pattern="[0-9]{1,2,3,4}">
          </div>
          <div class="form-group">
            <label for="tiempo_desarrollo_masa">Tiempo de desarrollo de la masa (En Segundos):</label>
            <input type="text" id="tiempo_desarrollo_masa_sup" name="tiempo_desarrollo_masa_sup" placeholder="Límite Superior" maxlength="3" pattern="[0-9]{1,2,3}">
            <input type="text" id="tiempo_desarrollo_masa_inf" name="tiempo_desarrollo_masa_inf" placeholder="Límite Inferior" maxlength="3" pattern="[0-9]{1,2,3}">
          </div>
          <div class="form-group">
            <label for="estabilidad">Estabilidad (En Segundos):</label>
            <input type="text" id="estabilidad_sup" name="estabilidad_sup" placeholder="Límite Superior" maxlength="3" pattern="[0-9]{2,3}">
            <input type="text" id="estabilidad_inf" name="estabilidad_inf" placeholder="Límite Inferior" maxlength="3" pattern="[0-9]{2,3}">
          </div>
          <div class="form-group">
            <label for="grado_reblandecimiento">Grado de reblandecimiento:</label>
            <input type="text" id="grado_reblandecimiento_sup" name="grado_reblandecimiento_sup" placeholder="Límite Superior" maxlength="2" pattern="[0-9]{1.2}">
            <input type="text" id="grado_reblandecimiento_inf" name="grado_reblandecimiento_inf" placeholder="Límite Inferior" maxlength="2" pattern="[0-9]{1,2}">
          </div>


          <!-- ... -->
        </div>
</form>
    </div>

  </div>

  <script src="../JS/RegistroCliente.js" defer>  </script>
  <script src="../JS/Header&Footer.js"></script>\
</body>
</html>
