<?php
session_start();
$rol=$_SESSION['rol'];

$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
	    header("Location: login.php");
	        exit();
}



$idSap = isset($_GET['ID_SAP_CLIENTE']) && trim($_GET['ID_SAP_CLIENTE']) !== '' ? $_GET['ID_SAP_CLIENTE'] : null;


if ($idSap === null) {
	    echo "<p>ID SAP es requerido y no puede estar vacío.</p>";
} else {
	    try {
		            $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
			            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			            $stmt = $pdo->prepare("SELECT * FROM Clientes WHERE ID_SAP = :idSap");
				            $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
				            $stmt->execute();
					            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
					            if (!$cliente) {
							                echo "<p>Cliente no encontrado. Por favor, verifique el ID SAP proporcionado.</p>";
									        }else{
												$stmtDomic = $pdo->prepare("SELECT iddomicilio, domicilio FROM domicilio WHERE ID_SAP = :idSap");
												            $stmtDomic->bindParam(':idSap', $idSap, PDO::PARAM_INT);
												            $stmtDomic->execute();
													                $domic = $stmtDomic->fetchAll(PDO::FETCH_ASSOC);
													    	}

						        } catch (PDOException $e) {
								        die("Error de conexión: " . $e->getMessage());
									    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta Ordenes</title>
    <link rel="stylesheet" type="text/css" href="../CSS/header&footer.css">
    <link rel="stylesheet" type="text/css" href="../CSS/ContactUs.css">
    <link rel="stylesheet" href="../CSS/ConsultaClientes.css">

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

  <body>
    <div class="container">
    <h2>Alta de Ordenes</h2>
        <form action="PedidoExitoso.php" method="post">
            <p>ID SAP: <strong><?php echo htmlspecialchars($idSap); ?></strong></p>
            <input type="hidden" id="idSap" name="ID_SAP_CLIENTE" value="<?php echo $idSap; ?>">
	<select id="iddomicilio" name="iddomicilio" required>
 	<?php
          if (isset($domic)) {
            foreach ($domic as $row) {
              echo '<option value="' . $row['iddomicilio'] . '">' . $row['domicilio'] . '</option>';
            }
          }
          ?>
    </select>
            <div class="form-group">
                <label for="cantidad-total">Cantidad Total (toneladas):</label>
                <input type="number" id="cantidad-total" name="cantidad-total" step="0.001" required>
            </div>

            <div class="form-group">
                <label for="fecha-envio">Fecha de Envío:</label>
                <input type="date" id="fecha-envio" name="fecha-envio" required>
            </div>
            <div class="form-group">
                <label for="numerofactura">Numero de Factura:</label>
                <input type="text" id="numerofactura" name="numerofactura" required>
            </div>

            <button type="submit">Subir Pedido</button>
            <button type="button" onclick="window.location.href='ConsultaClientes.php'">Regresar al Menú</button>
        </form>
    </div>
</body>

</html>
