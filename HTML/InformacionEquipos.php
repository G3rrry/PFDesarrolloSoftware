<?php
session_start();
$rol = $_SESSION['rol'];

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$claveEquipo = isset($_GET['claveEquipo']) ? intval($_GET['claveEquipo']) : 0;

try {
    $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM EquipoLaboratorio WHERE claveEquipo = :claveEquipo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':claveEquipo', $claveEquipo, PDO::PARAM_INT);
    $stmt->execute();
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

if (!$equipo) {
    echo "<script>alert('No se encontró el equipo solicitado.'); location.href='ConsultaEquipoLab.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Clientes - Harinas Elizondo</title>
    <link rel="stylesheet" type="text/css" href="../CSS/header&footer.css">
    <link rel="stylesheet" type="text/css" href="../CSS/ContactUs.css">
    <link rel="stylesheet" href="../CSS/ConsultaClientes.css">
    <link rel="stylesheet" href="../CSS/ConsultaEquipoLab.css">
    <link rel="stylesheet" href="../CSS/InformacionEquipos.css">

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener los detalles del equipo desde PHP y convertirlo en un objeto JavaScript
        const details = <?php echo json_encode($equipo); ?>;

        // Si hay detalles disponibles, actualizar el contenido de los elementos HTML
        if (details) {
            document.getElementById('detail-marca').textContent = details.marca || 'No especificado';
            document.getElementById('detail-modelo').textContent = details.modelo || 'No especificado';
            document.getElementById('detail-serie').textContent = details.serie || 'No especificado';
            document.getElementById('detail-descripcion-corta').textContent = details.descripcioncorta || 'No especificado';
            document.getElementById('detail-descripcion-larga').textContent = details.descripcionlarga || 'No especificado';
            document.getElementById('detail-clave-proveedor').textContent = details.claveproveedor || 'No especificado';
            document.getElementById('detail-fecha-adquisicion').textContent = details.fechaadquisicion || 'No especificado';
            document.getElementById('detail-garantia').textContent = details.garantia || 'No especificado';
            document.getElementById('detail-tipo-garantia').textContent = details.vigenciagarantia || 'No especificado'; // Asumiendo que vigencia de la garantía es el tipo
            document.getElementById('detail-vigencia-garantia').textContent = details.vigenciagarantia || 'No especificado';
            document.getElementById('detail-ubicacion').textContent = details.ubicacion || 'No especificado';
            document.getElementById('detail-responsable').textContent = details.responsable || 'No especificado';
            document.getElementById('detail-estado-equipo').textContent = details.estado || 'No especificado';
        }
    });
    </script>


</head>
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
  
<body>

<div class="results-container">
    <div class="equipment-details">
        <h2>Detalles del Equipo</h2>
        <p><strong>Marca:</strong> <span id="detail-marca"></span></p>
        <p><strong>Modelo:</strong> <span id="detail-modelo"></span></p>
        <p><strong>Serie:</strong> <span id="detail-serie"></span></p>
        <p><strong>Descripción Corta:</strong> <span id="detail-descripcion-corta"></span></p>
        <p><strong>Descripción Larga:</strong> <span id="detail-descripcion-larga"></span></p>
        <p><strong>Clave Proveedor:</strong> <span id="detail-clave-proveedor"></span></p>
        <p><strong>Fecha de Adquisición:</strong> <span id="detail-fecha-adquisicion"></span></p>
        <p><strong>Garantía:</strong> <span id="detail-garantia"></span></p>
        <p><strong>Tipo Garantía:</strong> <span id="detail-tipo-garantia"></span></p>
        <p><strong>Vigencia Garantía:</strong> <span id="detail-vigencia-garantia"></span></p>
        <p><strong>Ubicación:</strong> <span id="detail-ubicacion"></span></p>
        <p><strong>Responsable:</strong> <span id="detail-responsable"></span></p>
        <p><strong>Estado:</strong> <span id="detail-estado-equipo"></span></p>
    </div>

    <button onclick="location.href='ConsultaEquipoLab.php'">Regresar</button>
   
</div>

</body>

</html>
