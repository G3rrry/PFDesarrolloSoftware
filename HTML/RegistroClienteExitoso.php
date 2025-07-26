<?php
session_start();
$rol = $_SESSION['rol'];
try {
    session_start();
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
        header("Location: login.php");
        exit();
    }
    $rol = $_SESSION['rol'];
   

    // Conexión a la base de datos
    $conn = pg_connect("host=localhost dbname=ds user=root password=root");
    if (!$conn) {
        throw new Exception('No se pudo conectar: ' . pg_last_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $requiere_certificado = isset($_POST['requiere_certificado']) && $_POST['requiere_certificado'] == '1' ? true : false;
        if ($_POST['requiere_certificado'] == false) {
            $requiere_certificado = '0';
        }
        $sqlCliente = "INSERT INTO Clientes (ID_SAP, RFC, nombreCliente, domicilioFiscal, requiereCertificado, estadoCliente, correo, telefono, nombreContacto, correoContacto, telefonoContacto)
                      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)";
        $sqlValoresReferencia = "INSERT INTO ValoresDeReferencia (ID_SAP, IdParametro, min, max)
                                VALUES ($1, $2, $3, $4)";
        $sqlDomicilioEntrega = "INSERT INTO domicilio (ID_SAP, domicilio) VALUES ($1, $2)";

        $params = array(
            (int)$_POST['ID_SAP_CLIENTE'],
            $_POST['rfc'],
            $_POST['nombre_cliente'],
            $_POST['razon_factura'] . ". " . $_POST['Domicilio'] . ", " . $_POST['NumExt_Factura'] . ", " . $_POST['NumInt_Factura'] . ". " . $_POST['CP_factura'] . ", " . $_POST['Colonia_factura'] . ".",
            $requiere_certificado,
            true,
            $_POST['correo_cliente'],
            $_POST['telefono_cliente'],
            $_POST['nombre_contacto'],
            $_POST['correo_contacto'],
            $_POST['telefono_contacto']
        );

        $valref = array(
            (float)$_POST['tenacidad_inf'],
            (float)$_POST['extensibilidad_inf'],
            (float)$_POST['fuerza_panadera_inf'],
            (float)$_POST['relacion_curva_inf'],
            (float)$_POST['absorcion_agua_inf'],
            (float)$_POST['tiempo_desarrollo_masa_inf'],
            (float)$_POST['estabilidad_inf'],
            (float)$_POST['grado_reblandecimiento_inf'],
            (float)$_POST['tenacidad_sup'],
            (float)$_POST['extensibilidad_sup'],
            (float)$_POST['fuerza_panadera_sup'],
            (float)$_POST['relacion_curva_sup'],
            (float)$_POST['absorcion_agua_sup'],
            (float)$_POST['tiempo_desarrollo_masa_sup'],
            (float)$_POST['estabilidad_sup'],
            (float)$_POST['grado_reblandecimiento_sup']
        );

       

        $domic = array(
            $params[0],
            $_POST['domicilio'] . ", " . $_POST['NumExt_Entrega'] . ", " . $_POST['NumInt_Entrega'] . ". " . $_POST['CP_entrega'] . ", " . $_POST['colonia_cliente'] . ".",
        );

        // Inicia la transacción
        pg_query($conn, 'BEGIN');

        // Insertar cliente
        $insertCliente = pg_query_params($conn, $sqlCliente, $params);
        if (!$insertCliente) {
            throw new Exception('Error al insertar cliente: ' . pg_last_error($conn));
        }

        // Insertar valores de referencia
        $insertValores = true;
        for ($i = 1; $i < 9; $i++) {
            if (!empty($valref[$i - 1]) && !empty($valref[$i + 7])) {
                $valrefer = array($params[0], $i, $valref[$i - 1], $valref[$i + 7]);
                $insertValores = $insertValores && pg_query_params($conn, $sqlValoresReferencia, $valrefer);
            }
        }

        // Insertar domicilio de entrega
        $insertDomicilio = pg_query_params($conn, $sqlDomicilioEntrega, $domic);
        if (!$insertDomicilio) {
            throw new Exception('Error al insertar domicilio de entrega: ' . pg_last_error($conn));
        }

        // Commit de la transacción
        pg_query($conn, 'COMMIT');
        echo "<h1>Cliente registrado correctamente</h1>";
    } else {
        throw new Exception('Error with POST');
    }
} catch (Exception $e) {
    echo "<h1>Error en Registro de Cliente</h1>";
    echo $e->getMessage();
}

// Cerrar conexión
pg_close($conn);
?>
</body>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso</title>
    <link rel="stylesheet" href="../CSS/header&footer.css">
    <link rel="stylesheet" href="../CSS/Login.css">

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
<p></p>
<p></p>
<div class="container">
    <div class="box-and-logo">
      <div class="login-box">
        <H2>Registro de Cliente Subido Correctamente</H2>
      </div>

    </div>
  </div>


</body>
