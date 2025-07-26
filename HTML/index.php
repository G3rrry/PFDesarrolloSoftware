<?php
session_start();
$rol = $_SESSION['rol'];

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio - Harinas Elizondo</title>
  <link rel="stylesheet" href="../CSS/header&footer.css">
  <link rel="stylesheet" href="../CSS/index.css">
  <link rel="stylesheet" href="../CSS/Login.css">
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

  <div class="index-container">
    <!-- Contenido principal de la página -->
    <h1>Bienvenid@, <?php echo htmlspecialchars($_SESSION['nombres']); ?></h1>
    <!-- Aquí iría el contenido extraído de index.php -->
  </div>

  <body>
  <div class="index-container">
        <?php if ($rol == 1 || $rol == 2): ?>
        <div class="dropdown">
            <a href="javascript:void(0)" class="module-link dropbtn">Registros</a>
            <div class="dropdown-content">
                <a href="RegistroCliente.php">Registro Cliente</a>
                <a href="RegistroEquipoLab.php">Registro Equipo Laboratorio</a>
                <a href="RegistroLotes.php">Registro de Nuevos Lotes</a>
                <a href="RegistroResultados.php">Registro de Resultados de Lotes</a>
                <a href="RegistroResultadosPedidos.php">Registro de Resultados de Pedidos</a>
                <!-- Agrega más enlaces de registro según sea necesario -->
            </div>
        </div>
        <?php endif; ?>


        <div class="dropdown">
            <a href="javascript:void(0)" class="module-link dropbtn">Consultas</a>
            <div class="dropdown-content">
                <a href="ConsultaEquipoLab.php">Consulta Equipo Laboratorio</a>
                <a href="ConsultaClientes.php">Consulta Clientes</a>
                <a href="ConsultaLotes.php">Consulta de Lotes</a>
                <a href="estadisticas.php">Consulta de Estadísticas</a>


                <!-- Agrega más enlaces de consulta según sea necesario -->
            </div>
        </div>


        <?php if ($rol == 1 || $rol == 2): ?>
        <div class="dropdown">
          <a href="javascript:void(0)" class="module-link dropbtn">Análisis</a>
          <div class="dropdown-content">
            <a href="estadisticas.php">Ver Estadísticas</a>
            <a href="EmisionCertificado.php">Emision de Certifiado</a>
              <!-- Agrega más enlaces de consulta según sea necesario -->
          </div>
        <?php endif; ?>

    </body>
  <!-- Aquí iría el footer extraído de index.php -->

  <script src="../JS/index.js"></script>
</body>
</html>