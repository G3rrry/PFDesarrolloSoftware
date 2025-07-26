<?php
$rol = $_SESSION['rol'];
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
    // Si no se cumple la sesión o el rol, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}


if (isset($_GET['ID_SAP_CLIENTE']) || isset($_POST['ID_SAP_CLIENTE'])) {
    $idSap = isset($_GET['ID_SAP_CLIENTE']) ? $_GET['ID_SAP_CLIENTE'] : $_POST['ID_SAP_CLIENTE'];

    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Concatenar los campos de domicilio de factura
            $domicilioFiscal = $_POST['razon_factura'] . ". " . $_POST['Domicilio'] . ", " . $_POST['NumExt_Factura'] . ", " . $_POST['NumInt_Factura'] . ". " . $_POST['CP_factura'] . ", " . $_POST['Colonia_factura'];

            // Convertir los valores de estado y requiereCertificado a booleanos
            $estadoCliente = ($_POST['estado'] === 'Activo') ? true : false;
            $requiereCertificado = ($_POST['requiereCertificado'] == '1') ? true : false;

            // Actualizar los datos del cliente en la base de datos
            $stmt = $pdo->prepare("
                UPDATE Clientes SET
                    nombreCliente = :nombre_cliente,
                    nombreContacto = :nombre_contacto,
                    correo = :email_cliente,
                    correoContacto = :correo_contacto,
                    telefono = :telefono_cliente,
                    telefonoContacto = :telefono_contacto,
                    domicilioFiscal = :domicilioFiscal,
                    RFC = :rfc,
                    estadoCliente = :estado,
                    requiereCertificado = :requiereCertificado
                WHERE ID_SAP = :idSap
            ");

            $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_cliente', $_POST['nombre_cliente']);
            $stmt->bindParam(':nombre_contacto', $_POST['nombre_contacto']);
            $stmt->bindParam(':email_cliente', $_POST['email_cliente']);
            $stmt->bindParam(':correo_contacto', $_POST['correo_contacto']);
            $stmt->bindParam(':telefono_cliente', $_POST['telefono_cliente']);
            $stmt->bindParam(':telefono_contacto', $_POST['telefono_contacto']);
            $stmt->bindParam(':domicilioFiscal', $domicilioFiscal);
            $stmt->bindParam(':rfc', $_POST['rfc']);
            $stmt->bindParam(':estado', $estadoCliente, PDO::PARAM_BOOL);
            $stmt->bindParam(':requiereCertificado', $requiereCertificado, PDO::PARAM_BOOL);

            $stmt->execute();

            echo "<p>Datos actualizados correctamente.</p>";
        }

        $stmt = $pdo->prepare("SELECT * FROM Clientes WHERE ID_SAP = :idSap");
        $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            echo "<p>Cliente no encontrado.</p>";
            exit();
        }

        // Desconcatenar domicilioFiscal y asignar valores a cada campo
        $domicilioParts = explode(". ", $cliente['domiciliofiscal']);
        $razonSocial = trim($domicilioParts[0]);
        $domicilioRest = explode(", ", $domicilioParts[1]);
        $domicilioRegistrado = trim($domicilioRest[0]);
        $numeroExt = trim($domicilioRest[1]);
        $numeroInt = trim($domicilioRest[2]);
        $codigoPostalColonia = explode(", ", $domicilioParts[2]);
        $codigoPostal = trim($codigoPostalColonia[0]);
        $coloniaFactura = trim($codigoPostalColonia[1]);
    } catch (PDOException $e) {
        echo "<p>Error de conexión: " . $e->getMessage() . "</p>";
        exit();
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
    <title>Actualizar Clientes</title>
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
        <h2>Actualizar Información Clientes</h2>
        <div class="logo">
            <img src="../Imagenes/Logo-Harinas-Elizondo.png" alt="Logo">
        </div>
        <div class="form-container">
            <form action="ActualizarCliente.php" method="post"> 
                <div style="display: flex; justify-content: space-between;">
                    <div class="columna-cliente">
                        <p><strong>Datos del cliente</strong></p>
                        <div class="form-group">
                            <label for="ID_SAP_CLIENTE">ID SAP:</label>
                            <input type="text" id="ID_SAP_CLIENTE" name="ID_SAP_CLIENTE" required maxlength="6" value="<?php echo htmlspecialchars($cliente['id_sap']); ?>" readonly>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre_cliente">Nombre del cliente:</label>
                                <input type="text" id="nombre_cliente" name="nombre_cliente" required maxlength="50" value="<?php echo htmlspecialchars($cliente['nombrecliente']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="nombre_contacto">Nombre del contacto:</label>
                                <input type="text" id="nombre_contacto" name="nombre_contacto" required maxlength="50" value="<?php echo htmlspecialchars($cliente['nombrecontacto']); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email_cliente">Correo electrónico:</label>
                                <input type="email" id="email_cliente" name="email_cliente" required maxlength="30" value="<?php echo htmlspecialchars($cliente['correo']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="correo_contacto">Correo del contacto:</label>
                                <input type="email" id="correo_contacto" name="correo_contacto" required maxlength="50" value="<?php echo htmlspecialchars($cliente['correocontacto']); ?>">
                            </div>
                        </div>
        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono_cliente">Número telefónico:</label>
                                <input type="text" id="telefono_cliente" name="telefono_cliente" required pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="telefono_contacto">Número telefónico contacto:</label>
                                <input type="text" id="telefono_contacto" name="telefono_contacto" required pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($cliente['telefonocontacto']); ?>">
                            </div>
                        </div>        
                    </div>

                    <div class="columna-facturacion">
                        <p><strong>Datos para facturación</strong></p>
                        <div class="form-group">
                            <label for="rfc">RFC:</label>
                            <input type="text" id="rfc" name="rfc"  maxlength="13" value="<?php echo htmlspecialchars($cliente['rfc']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="CP_factura">Código postal Factura:</label>
                            <input type="text" id="CP_factura" name="CP_factura"  pattern="[0-9]{5}" maxlength="5" value="<?php echo htmlspecialchars($codigoPostal); ?>">
                        </div>
                        <div class="form-group">
                            <label for="razon_factura">Razón Social:</label>
                            <input type="text" id="razon_factura" name="razon_factura"  value="<?php echo htmlspecialchars($razonSocial); ?>">
                        </div>
                        <div class="form-group">
                            <label for="Domicilio">Domicilio Registrado Factura:</label>
                            <input type="text" id="Domicilio" name="Domicilio" maxlength="50" value="<?php echo htmlspecialchars($domicilioRegistrado); ?>">
                        </div>
                        <div class="form-group">
                            <label for="Colonia_factura">Colonia/Delegacion Factura:</label>
                            <input type="text" id="Colonia_factura" name="Colonia_factura"  maxlength="50" value="<?php echo htmlspecialchars($coloniaFactura); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="NumExt_Factura">Número exterior Factura:</label>
                                <input type="text" id="NumExt_Factura" name="NumExt_Factura"  maxlength="3" value="<?php echo htmlspecialchars($numeroExt); ?>">
                            </div>
                            <div class="form-group">
                                <label for="NumInt_Factura">Número Interior Factura:</label>
                                <input type="text" id="NumInt_Factura" name="NumInt_Factura"  maxlength="3" value="<?php echo htmlspecialchars($numeroInt); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="columna-estado">
                        <div class="estado-cliente">
                            <p><strong>Estado del cliente</strong></p>
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select id="estado" name="estado" required>
                                    <option value="Activo" <?php echo $cliente['estadocliente'] ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Inactivo" <?php echo !$cliente['estadocliente'] ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group" id="causa-baja" style="display: <?php echo !$cliente['estadocliente'] ? 'block' : 'none'; ?>;"> <!-- Añade el id y el style inline -->
                                <label for="causa">Causa de baja:</label>
                                <input type="text" id="causa" name="causa" value="<?php echo htmlspecialchars($cliente['causabaja']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="requiereCertificado">¿Requiere Certificado?</label>
                    <select id="requiereCertificado" name="requiereCertificado" required>
                        <option value="1" <?php echo $cliente['requierecertificado'] ? 'selected' : ''; ?>>Sí</option>
                        <option value="0" <?php echo !$cliente['requierecertificado'] ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
                <!-- Botón de enviar -->
                <button type="submit" class="submit-button">Enviar</button>
            </form>
        </div>
    </div>
    <script src="../JS/ActualizarCliente.js"></script>
</body>
</html>
