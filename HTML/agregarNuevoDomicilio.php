<?php
session_start();
$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || 
    ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2 && $_SESSION['rol'] != 3)) {
    // Si no hay una sesión activa o si el rol no es 1, 2 o 3, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

if (isset($_GET['ID_SAP_CLIENTE']) || isset($_POST['ID_SAP_CLIENTE'])) {
    $idSap = isset($_GET['ID_SAP_CLIENTE']) ? $_GET['ID_SAP_CLIENTE'] : $_POST['ID_SAP_CLIENTE'];

    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $codigoPostal = $_POST['CP_entrega'];
            $calleEntrega = $_POST['domicilio'];
            $coloniaEntrega = $_POST['colonia_cliente'];
            $numeroExterior = $_POST['NumExt_Entrega'];
            $numeroInterior = $_POST['NumInt_Entrega'];

            // Concatenar los valores en el formato requerido
            $domicilio = $calleEntrega . ", " . $numeroExterior . ", " . $numeroInterior . ". " . $codigoPostal . ", " . $coloniaEntrega;

            // Insertar el nuevo domicilio en la base de datos
            $stmt = $pdo->prepare("INSERT INTO Domicilio (ID_SAP, domicilio) VALUES (:idSap, :domicilio)");
            $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
            $stmt->bindParam(':domicilio', $domicilio);
            $stmt->execute();

            echo "<p>Domicilio registrado correctamente.</p>";
        }

        $stmt = $pdo->prepare("SELECT * FROM Clientes WHERE ID_SAP = :idSap");
        $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            echo "<p>Cliente no encontrado.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error de conexión: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ID SAP no proporcionado.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Nuevo Domicilio - Harinas Elizondo</title>
    <link rel="stylesheet" type="text/css" href="../CSS/header&footer.css">
    <link rel="stylesheet" type="text/css" href="../CSS/ContactUs.css">
    <link rel="stylesheet" href="../CSS/RNU.css">
    <link rel="stylesheet" href="../CSS/BarraNavResultados.css">
    <link rel="stylesheet" href="../CSS/cosaGris.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    <div class="contact-container">
        <!-- Form box -->
        <div class="form-container">
            <form action="" method="post">
                <h2>Registro de Nuevo Domicilio</h2>

                <div class="search-container">
                    <label for="ID_SAP_CLIENTE">ID SAP:</label>
                    <input type="text" id="ID_SAP_CLIENTE" name="ID_SAP_CLIENTE" value="<?php echo htmlspecialchars($idSap); ?>" readonly onfocus="this.blur()"> 
                </div>
                <div class="form-group">
                    <label for="CP_entrega">Código postal para entrega:</label>
                    <input type="text" id="CP_entrega" name="CP_entrega" required pattern="[0-9]{5}" maxlength="5">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="domicilio">Calle de entrega:</label>
                        <input type="text" id="domicilio" name="domicilio" required maxlength="50">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="colonia_cliente">Colonia Entrega:</label>
                        <input type="text" id="colonia_cliente" name="colonia_cliente" required maxlength="30">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="NumExt_Entrega">Número exterior Entrega:</label>
                        <input type="text" id="NumExt_Entrega" name="NumExt_Entrega" required maxlength="3" minlength="1">
                    </div>
                    <div class="form-group">
                        <label for="NumInt_Entrega">Número Interior Entrega:</label>
                        <input type="text" id="NumInt_Entrega" name="NumInt_Entrega" required maxlength="3" minlength="1">
                    </div>
                </div>

                <!-- Submit y Exit Buttons -->
                <button type="submit" class="submit-button">Registrar Nuevo Domicilio</button>
                <button type="button" class="submit-button" onclick="location.href='index.php'">Volver a la página principal</button>
            </form>
        </div>
    </div>
</body>
</html>
