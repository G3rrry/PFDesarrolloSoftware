<?php
session_start();
$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
    // Si no se cumple la sesión o el rol, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];
try {
    $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hacer la consulta y almacenar los resultados en una variable
    $stmt = $pdo->query("SELECT claveproveedor, proveedor FROM proveedores");
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (isset($_GET['claveEquipo'])) {
        $claveEquipo = $_GET['claveEquipo'];
        $stmt = $pdo->prepare("SELECT * FROM EquipoLaboratorio WHERE claveEquipo = ?");
        $stmt->execute([$claveEquipo]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>



<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar equipos</title>
    <link rel="stylesheet" href="../CSS/header&footer.css">
    <link rel="stylesheet" href="../CSS/RegistroEquipos.css">

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
  
      <main>
        <div class="container">
            <h1>Actualizar de equipos</h1>
            <form class="form-alta-equipo" action="ActlzExitosoLab.php" method="post">
                <!-- Sección izquierda del formulario -->
                <div class="form-section">
                    <!-- Dentro de tu formulario -->
                    <input type="hidden" name="claveEquipo" value="<?php echo htmlspecialchars($claveEquipo); ?>">

                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($equipo['marca']); ?>" required>

        
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($equipo['modelo']); ?>" required>
        
                    <label for="serie">Serie:</label>
                    <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($equipo['serie']); ?>" required>
        
                    <label for="descripcion-corta">Descripción corta:</label>
                    <input type="text" id="descripcion-corta" name="descripcion-corta" value="<?php echo htmlspecialchars($equipo['descripcioncorta']); ?>" required>
        
                    <label for="descripcion-larga">Descripción larga:</label>
                    <textarea id="descripcion-larga" name="descripcion-larga" required><?php echo htmlspecialchars($equipo['descripcionlarga']); ?></textarea>
                </div>
        
                <!-- Sección derecha del formulario -->
                <div class="form-section">
                    <label for="clave-proveedor">Clave Proveedor:</label>
                    <select id="clave-proveedor" name="clave-proveedor" required>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?php echo htmlspecialchars($proveedor['claveproveedor']); ?>" <?php echo ($proveedor['claveproveedor'] == $equipo['claveproveedor']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($proveedor['proveedor']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

        
                    <label for="fecha-adquisicion">Fecha de adquisición:</label>
                    <input type="date" id="fecha-adquisicion" name="fecha-adquisicion" value="<?php echo htmlspecialchars($equipo['fechaadquisicion']); ?>" required>
        
                    <label for="garantia">Garantía:</label>
                    <input type="text" id="garantia" name="garantia" value="<?php echo htmlspecialchars($equipo['garantia']); ?>" required>

                    <label for="tipo_garantia">Tipo Garantía</label>
                    <select id="tipo_garantia" name="tipo_garantia" required>
                        <option value="garantia_completa" <?php echo ($equipo['tipogarantia'] == 'garantia_completa') ? 'selected' : ''; ?>>Completa</option>
                        <option value="garantia_parcial" <?php echo ($equipo['tipogarantia'] == 'garantia_parcial') ? 'selected' : ''; ?>>Parcial</option>
                    </select>
        
                    <label for="vigencia-garantia">Vigencia garantía:</label>
                    <input type="date" id="vigencia-garantia" name="vigencia-garantia" value="<?php echo htmlspecialchars($equipo['vigenciagarantia']); ?>" required>
        
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($equipo['ubicacion']); ?>" required>
        
                    <label for="responsable">Responsable:</label>
                    <input type="text" id="responsable" name="responsable" value="<?php echo htmlspecialchars($equipo['responsable']); ?>" required>
        
                    <label for="estado_equipo">Estado:</label>
                    <select id="estado_equipo" name="estado_equipo" required>
                        <option value="activo" <?php echo ($equipo['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($equipo['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>

                    <label for="equipo">Selecciona un equipo:</label>
                    <select id="equipo" name="equipo" required>
                        <option value="1">Farinógrafo</option>
                        <option value="0">Alveógrafo</option>
                    </select>
                    
                </div>
        
                <button type="submit" class="submit-button">Actualizar</button>
            </form>
        </div>
        <div class="footer-buttons">
            <button onclick="location.href='ConsultaEquipoLab.php'" type="submit">Volver a Consultas de Equipos</button>
        </div>
</html>
