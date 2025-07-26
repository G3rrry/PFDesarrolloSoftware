<?php
session_start();
$rol=$_SESSION['rol'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica la sesión y el rol del usuario
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
    header("Location: login.php");
    exit();
}

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara la sentencia INSERT incluyendo el nuevo campo 'tipogarantia'
        $stmt = $pdo->prepare("INSERT INTO EquipoLaboratorio (marca, modelo, serie, descripcionLarga, descripcionCorta, claveProveedor, fechaAdquisicion, garantia, vigenciaGarantia, ubicacion, responsable, estado, tipogarantia, farinografo) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Vincula los parámetros y ejecuta la sentencia
        $stmt->execute([
            $_POST['marca'],
            $_POST['modelo'],
            $_POST['serie'],
            $_POST['descripcion-larga'],
            $_POST['descripcion-corta'],
            $_POST['clave-proveedor'],
            $_POST['fecha-adquisicion'],
            $_POST['garantia'],
            $_POST['vigencia-garantia'],
            $_POST['ubicacion'],
            $_POST['responsable'],
            $_POST['estado_equipo'],
            $_POST['tipo_garantia'],
            $_POST['equipo'], // Asegúrate de que este valor se esté enviando correctamente desde el formulario
        ]);

        // Redirecciona o maneja el caso de éxito
        $_SESSION['mensaje'] = 'Equipo insertado con éxito';
        header('Location: ConsultaEquipoLab.php'); // Cambia esto por tu página de éxito
        exit();
    } catch (PDOException $e) {
        // Manejar el error y mostrarlo o registrar en un log
        $_SESSION['error'] = "Error al insertar el equipo: " . $e->getMessage();
        // Muestra el error directamente en la página en lugar de redirigir
        echo "<p>Error al insertar el equipo: " . $e->getMessage() . "</p>";
        // Aquí podrías incluir la salida del error detallado
    }
} else {
    // Redirige al formulario si el método no es POST
    header("Location: formularioEquipo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso</title>
    <link rel="stylesheet" href="../CSS/header&footer.css">
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
    <!-- Mostrar el mensaje de éxito si existe -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <p><?php echo $_SESSION['mensaje']; ?></p>
    <?php endif; ?>

    <!-- Mostrar el mensaje de error si existe -->
    <?php if (isset($_SESSION['error'])): ?>
        <p><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>

    <!-- Aquí el resto de tu HTML -->
    <!--<div class="countdown" id="countdown">5</div>-->

</body>
<!--
<script src="../JS/kaboomRegistro.js"></script>
    -->
</html>

