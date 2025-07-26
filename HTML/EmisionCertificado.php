<?php
session_start();
$rol=$_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 )) {
  header("Location: login.php");
  exit();
}

try {
  $pdo = new PDO('pgsql:host=localhost; dbname=ds', 'root', 'root');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rowData = json_decode($_POST['generate'], true);
    $certPATH = "/var/www/html/ds/certificados/CERT{$rowData['numerocertificado']}ORD{$rowData['numeroorden']}{$rowData['secuenciainspeccion']}.pdf";
    $updatepath = "UPDATE orden SET path='{$certPATH}' WHERE numerocertificado=:numerocertificado";
    $stmt = $pdo->prepare($updatepath);
    $stmt->bindParam(':numerocertificado', $rowData["numerocertificado"], PDO::PARAM_INT);
    $stmt->execute();


    $sqlResultados = "SELECT resultado, idparametro FROM resultados WHERE numerocertificado=:numerocertificado";
    $stmt = $pdo->prepare($sqlResultados);
    $stmt->bindParam(':numerocertificado', $rowData["numerocertificado"], PDO::PARAM_INT);
    $stmt->execute();
    $ResultadosSimples = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sqlValoresReferencia = "WITH q1 as (SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, c.numerocertificado, id_sap FROM orden AS o, lotes AS l, certificado AS c, fechadeanalisis AS f, pedido as p, domicilio as d WHERE l.numerolote = c.numerolote AND c.numerocertificado = o.numerocertificado AND f.numerocertificado = c.numerocertificado and o.numeroorden=p.numeroorden and p.iddomicilio=d.iddomicilio and p.numeroorden=:numeroorden) select idparametro, min as min, max as max from
    valoresdereferencia v, q1 where v.id_sap=q1.id_sap union select idparametro, minint as min, maxint as max from parametros where
    idparametro not in (select idparametro from valoresdereferencia v, q1 where v.id_sap=q1.id_sap) order by idparametro";
    $stmt = $pdo->prepare($sqlValoresReferencia);
    $stmt->bindParam(':numeroorden', $rowData["numeroorden"], PDO::PARAM_INT);
    $stmt->execute();
    $ValoresReferencia = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $resultsArray = array();

    for ($id=1; $id<9; $id++) {
        $Resultado = "SELECT idparametro, minint, maxint FROM parametros WHERE idparametro=:idparametro";
        $stmt = $pdo->prepare($Resultado);
        $stmt->bindParam(':idparametro', $id, PDO::PARAM_INT);
        $stmt->execute();
        $parametroInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parametroInfo) {
            $sqlResultados = "SELECT resultado FROM resultados WHERE numerocertificado=:numerocertificado AND idparametro=:idparametro order by idparametro";
            $stmt = $pdo->prepare($sqlResultados);
            $stmt->bindParam(':numerocertificado', $rowData["numerocertificado"], PDO::PARAM_INT);
            $stmt->bindParam(':idparametro', $parametroInfo["idparametro"], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
              $min = isset($ValoresReferencia[$id-1]["min"]) ? $ValoresReferencia[$id-1]["min"] : $parametroInfo["minint"];
              $max = isset($ValoresReferencia[$id-1]["max"]) ? $ValoresReferencia[$id-1]["max"] : $parametroInfo["maxint"];
              
              $paramArray = array(
                  "r_" . $parametroInfo["idparametro"] => $result["resultado"],
                  "rmin_" . $parametroInfo["idparametro"] => $min,
                  "rmax_" . $parametroInfo["idparametro"] => $max,
              );
          
              if ($result["resultado"] < $min || $result["resultado"] > $max) {
                $paramArray["dr_" . $parametroInfo["idparametro"]] = "FUERA DE RANGO";
              } else{
                $paramArray["dr_" . $parametroInfo["idparametro"]] = "EN RANGO";
              }
            } else {
                $paramArray = array(
                    "r_" . $parametroInfo["idparametro"] => "NO APLICA",
                    "rmin_" . $parametroInfo["idparametro"] => "",
                    "rmax_" . $parametroInfo["idparametro"] => "",
                    "dr_" . $parametroInfo["idparametro"] => "NO APLICA"
                );
            }
        } else {
            $paramArray = array(
                "r_" . $parametroInfo["idparametro"] => "NO APLICA",
                "rmin_" . $parametroInfo["idparametro"] => "",
                "rmax_" . $parametroInfo["idparametro"] => "",
                "dr_" . $parametroInfo["idparametro"] => "NO APLICA"
            );
        }

        foreach ($paramArray as $key => $value) {
            $resultsArray[$key] = $value;
        }
      }

    $sqlDatosGenerales = "SELECT DISTINCT nombrecliente, fechacaducidad, fechaproduccion, domiciliofiscal, domicilio, o.cantidadlote AS cantidadentregada, cantidadtotal AS cantidadsolicitada, numerofactura, l.numerolote
    FROM clientes AS c, domicilio AS d, orden AS o, pedido AS p, lotes AS l, certificado AS cert
    WHERE d.id_sap = c.id_sap
    AND o.numeroorden = p.numeroorden
    AND p.iddomicilio = d.iddomicilio
    AND cert.numerocertificado = o.numerocertificado
    AND l.numerolote = cert.numerolote
    AND cert.numerocertificado = :numerocertificado";


    $resultsArray["producto"]="Harina Para Pan Precocido";
    $resultsArray["numerocertificado"] = $rowData["numerocertificado"];
    $resultsArray["numeroorden"] = $rowData["numeroorden"];
    
    $stmt = $pdo->prepare($sqlDatosGenerales);
    $stmt->bindParam(':numerocertificado', $rowData["numerocertificado"], PDO::PARAM_INT);
    $stmt->execute();
    $DatosGenerales = $stmt->fetch(PDO::FETCH_ASSOC);

    foreach ($DatosGenerales as $column => $value) {
      $resultsArray[$column] = $value;
    }


    $jsonResults = json_encode($resultsArray);

    $output = "";
    $return_var = 0;

    exec("/usr/bin/python3 /var/www/html/ds/certificados/emitcertificado.py {$certPATH} '{$jsonResults}' formato-certificado.html /var/www/html/ds/Imagenes/Logo-Harinas-Elizondo.png 2>&1", $output, $return_var);
    if ($return_var !== 0) {
      echo "Error executing Python script. Output: " . implode("\n", $output);
    }
    echo "<script>window.open('../certificados/CERT{$rowData['numerocertificado']}ORD{$rowData['numeroorden']}{$rowData['secuenciainspeccion']}.pdf', '_blank');</script>";
  }
} 
catch (PDOException $e) {
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
        <h2>Emitir certificado</h2>
        
        <form id="orderForm" method="POST" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> >
        <table class="ordenes">
          <?php
              try {
                // Create a PDO connection
                $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
            
                // Set PDO to throw exceptions on errors
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
                // Your SQL query
                $sql = "SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, c.numerocertificado, cl.id_sap, nombrecliente, o.cantidadlote  FROM orden AS o, lotes AS l, certificado AS c, fechadeanalisis AS f, pedido as p, domicilio as d, clientes as cl WHERE l.numerolote = c.numerolote AND c.numerocertificado = o.numerocertificado AND f.numerocertificado = c.numerocertificado and o.numeroorden=p.numeroorden and p.iddomicilio=d.iddomicilio and d.id_sap=cl.id_sap and path is NULL";
                # $sql = "WITH q1 AS (SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, c.numerocertificado, cl.id_sap, nombrecliente, o.cantidadlote  FROM orden AS o, lotes AS l, certificado AS c, fechadeanalisis AS f, pedido as p, domicilio as d, clientes as cl WHERE l.numerolote = c.numerolote AND c.numerocertificado = o.numerocertificado AND f.numerocertificado = c.numerocertificado and o.numeroorden=p.numeroorden and p.iddomicilio=d.iddomicilio and d.id_sap=cl.id_sap and path is NULL), q2 as (select idparametro, min as min, max as max from valoresdereferencia v, q1 where v.id_sap=q1.id_sap union select idparametro, minint as min, maxint as max from parametros where idparametro not in (select idparametro from valoresdereferencia v, q1 where v.id_sap=q1.id_sap)) select distinct secuenciainspeccion, nombrecliente, cantidadlote, numeroorden, q1.numerolote, fechaanalisis, q1.numerocertificado, resultado>=min and resultado<=max as cumple from resultados r, certificado c, q1, q2 where r.idparametro=q2.idparametro and r.numerocertificado=q1.numerocertificado order by numeroorden";      
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
                        <th>Emitir Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Iterate through the query results
                    foreach ($results as $row) {
                      $rowData = json_encode($row);
                      echo "<tr>";
                      echo "<td>" . $row['secuenciainspeccion'] . "</td>";
                      echo "<td>" . $row['nombrecliente'] . "</td>";
                      echo "<td>" . $row['cantidadlote'] . "</td>";
                      echo "<td>" . $row['numeroorden'] . "</td>";
                      echo "<td>" . $row['numerolote'] . "</td>";
                      echo "<td>" . $row['fechaanalisis'] . "</td>";
                      echo "<td>" . $row['numerocertificado'] . "</td>";
                    
                      echo "<td><button type='submit' name='generate' value='" . $rowData . "'>Generar certificado</button></td>";
                      echo "</tr>";
                    }
                    ?>
                </tbody>
              </table>
          </tbody>
        </table>
        </form>
        <div class="footer-buttons">
            <button onclick="location.href='index.php'">Volver a la página principal</button>
        </div>
    </div>
</body>
</html>