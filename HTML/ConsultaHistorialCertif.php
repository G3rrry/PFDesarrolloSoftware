<?php
session_start();
$rol=$_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 )) {
  header("Location: login.php");
  exit();
}
try {
  $rol = $_SESSION['rol'];
  $pdo = new PDO('pgsql:host=localhost; dbname=ds', 'root', 'root');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $mensaje = '';

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (empty($_POST['nombres']) || empty($_POST['apellidos']) || empty($_POST['correo']) || empty($_POST['contrasena']) || empty($_POST['rol'])) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
      // Get user input and sanitize data
      $nombres = $_POST['nombres'];
      $apellidos = $_POST['apellidos'];
      $correo = $_POST['correo'];
      $contrasena = $_POST['contrasena'];
      $rol_user = intval($_POST['rol']);

      // Prepare and execute the SQL statement to insert the user
      $sql = "INSERT INTO usuarios (nombres, apellidos, correo, contrasena, rol) VALUES (?, ?, ?, ?, ?) RETURNING id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(1, $nombres, PDO::PARAM_STR);
      $stmt->bindParam(2, $apellidos, PDO::PARAM_STR);
      $stmt->bindParam(3, $correo, PDO::PARAM_STR);
      $stmt->bindParam(4, $contrasena, PDO::PARAM_STR);
      $stmt->bindParam(5, $rol_user, PDO::PARAM_INT);

      if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $newUserId = $row['id'];
        $mensaje = "Registro exitoso! ID: $newUserId";
      } else {
        $mensaje = "Error al registrar: " . $stmt->errorInfo()[2];
      }
    }
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
  <title>Historial de Certificados</title>
  <link rel="stylesheet" href="../CSS/header&footer.css">
  <link rel="stylesheet" href="../CSS/index.css">
  <link rel="stylesheet" href="../CSS/Login.css">
  <link rel="stylesheet" href="../CSS/estadistico.css">
  <link rel="stylesheet" href="../CSS/administrar.css">
  <link rel="stylesheet" href="../CSS/ConsultaEquipoLab.css">
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
    <div class="content-container">

        <h2>Historial de Certificados</h2>

        <table class="ordenes">
          <?php
              try {
                // Create a PDO connection
                $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
            
                // Set PDO to throw exceptions on errors
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
                // Your SQL query
                $sql = "SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, c.numerocertificado, cl.id_sap, nombrecliente, o.cantidadlote, o.path  FROM orden AS o, lotes AS l, certificado AS c, fechadeanalisis AS f, pedido as p, domicilio as d, clientes as cl WHERE l.numerolote = c.numerolote AND c.numerocertificado = o.numerocertificado AND f.numerocertificado = c.numerocertificado and o.numeroorden=p.numeroorden and p.iddomicilio=d.iddomicilio and d.id_sap=cl.id_sap and path is not NULL";
                # $sql = "WITH q1 AS (SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, c.numerocertificado, cl.id_sap, nombrecliente, o.cantidadlote, o.path  FROM orden AS o, lotes AS l, certificado AS c, fechadeanalisis AS f, pedido as p, domicilio as d, clientes as cl WHERE l.numerolote = c.numerolote AND c.numerocertificado = o.numerocertificado AND f.numerocertificado = c.numerocertificado and o.numeroorden=p.numeroorden and p.iddomicilio=d.iddomicilio and d.id_sap=cl.id_sap and path is not NULL), q2 as (select idparametro, min as min, max as max from valoresdereferencia v, q1 where v.id_sap=q1.id_sap union select idparametro, minint as min, maxint as max from parametros where idparametro not in (select idparametro from valoresdereferencia v, q1 where v.id_sap=q1.id_sap)) select distinct secuenciainspeccion, nombrecliente, cantidadlote, numeroorden, q1.numerolote, fechaanalisis, q1.numerocertificado, q1.path, resultado>=min and resultado<=max as cumple from resultados r, certificado c, q1, q2 where r.idparametro=q2.idparametro and r.numerocertificado=q1.numerocertificado order by fechaanalisis DESC";      
                // Prepare the SQL statement
                $stmt = $pdo->prepare($sql);
            
                // Execute the statement
                $stmt->execute();
            
                // Fetch all rows as an associative array
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Handle database connection errors
                echo "Connection failed: " . $e->getMessage();
            }
            ?>
            
            <table class="equipment-table">
                <thead>
                    <tr>
                        <th>Secuencia de Inspeccion</th>
                        <th>Cliente Solicitante</th>
                        <th>Cantidad Entregada de la Orden</th>
                        <th>Numero de orden</th>
                        <th>Numero de Lote</th>
                        <th>Fecha de Analisis</th>
                        <th>Numero de Certificado</th>
                        <th>Descargar PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Iterate through the query results
                    foreach ($results as $row) {
                      echo "<tr>";
                      echo "<td>" . $row['secuenciainspeccion'] . "</td>";
                      echo "<td>" . $row['nombrecliente'] . "</td>";
                      echo "<td>" . $row['cantidadlote'] . "</td>";
                      echo "<td>" . $row['numeroorden'] . "</td>";
                      echo "<td>" . $row['numerolote'] . "</td>";
                      echo "<td>" . $row['fechaanalisis'] . "</td>";
                      echo "<td>" . $row['numerocertificado'] . "</td>";
          
                      echo "<td><button onclick=\"openPdf('CERT{$row['numerocertificado']}ORD{$row['numeroorden']}{$row['secuenciainspeccion']}')\">Abrir PDF</button></td>";          
                      echo "</tr>";
                    }
                    ?>
                </tbody>
              </table>
          </tbody>
        </table>
        <div class="footer-buttons">
            <button onclick="location.href='index.php'">Volver a la página principal</button>
        </div>
    </div>
</body>
  <script>
  function openPdf(certOrder) {
      window.open(`../certificados/${certOrder}.pdf`, '_blank');
  }
  </script>
</html>
