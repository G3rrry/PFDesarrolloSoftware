<?php
session_start();
$rol = $_SESSION['rol'];

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas de Resultados</title>
  <link rel="stylesheet" href="../CSS/estadistico.css">
  <link rel="stylesheet" href="../CSS/Login.css">
  <link rel="stylesheet" href="../CSS/RNU.css">
  <link rel="stylesheet" href="../CSS/header&footer.css">
  <link rel="stylesheet" href="../CSS/RegistroBoton.css">
</head>

<body>
<header class="header">
    <div class="nav-container">
      <a href="index.php" class="logo">
        <img src="../Imagenes/LogoHeader.png" alt="Logo">
      </a>
      <nav class="navbar">
        <ul class="nav-links">
          <li class="nav-item"><a href="index.php">Inicio</a></li>
          <li class="nav-item"><a href="ContactUs.html">Contacto Sistemas</a></li>
          <?php if ($rol == 1 || $rol == 2): ?>
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
          <li class="nav-item"><a href="EmisionCertificado.php">Emisión Certificado</a></li>
          <?php endif; ?>
          <li class="nav-item"><a href="ConsultaHistorialCertif.php">Consulta Historial Certificados</a></li>
          <?php if ($rol == 1): ?>
          <li class="nav-item"><a href="admin.php">Administrar Usuarios</a></li>
          <?php endif; ?>
          <li class="nav-item"><a href="login.php">Cerrar Sesión</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <h2>Estadística en periodo</h2>
  
  <h3>Seleccionar Periodo</h3>

  <div class="filters">
    <form method="POST" action="">
      <select name="month" id="month-select">
        <option value="1">Enero</option>
        <option value="2">Febrero</option>
        <option value="3">Marzo</option>
        <option value="4">Abril</option>
        <option value="5">Mayo</option>
        <option value="6">Junio</option>
        <option value="7">Julio</option>
        <option value="8">Agosto</option>
        <option value="9">Septiembre</option>
        <option value="10">Octubre</option>
        <option value="11">Noviembre</option>
        <option value="12">Diciembre</option>
      </select>
      <select name="year" id="year-select">
        <option value="2022">2022</option>
        <option value="2023">2023</option>
        <option value="2024">2024</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
        <option value="2027">2027</option>
        <option value="2028">2028</option>
        <option value="2029">2029</option>
        <option value="2030">2030</option>
        <option value="2031">2031</option>
        <option value="2032">2032</option>
        <option value="2033">2033</option>
      </select>
      <button type="submit" id="buscar_periodo_boton">Buscar</button>
    </form>
  </div>

  <?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $month = $_POST['month'];
      $year = $_POST['year'];

      $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $sql = "WITH q1 AS (
                  SELECT f.secuenciainspeccion, o.numeroorden, l.numerolote, fechaanalisis, 
                         c.numerocertificado, cl.id_sap, nombrecliente, o.cantidadlote  
                  FROM orden AS o
                  JOIN lotes AS l ON l.numerolote = o.numerolote
                  JOIN certificado AS c ON c.numerolote = l.numerolote
                  JOIN fechadeanalisis AS f ON f.numerocertificado = c.numerocertificado
                  JOIN pedido AS p ON o.numeroorden = p.numeroorden
                  JOIN domicilio AS d ON p.iddomicilio = d.iddomicilio
                  JOIN clientes AS cl ON d.id_sap = cl.id_sap
                  WHERE EXTRACT(MONTH FROM f.fechaAnalisis) = :month 
                  AND EXTRACT(YEAR FROM f.fechaAnalisis) = :year
                  AND path IS NULL
              ), 
              q2 AS (
                  SELECT idparametro, min, max 
                  FROM valoresdereferencia v 
                  JOIN q1 ON v.id_sap = q1.id_sap 
                  UNION 
                  SELECT idparametro, minint AS min, maxint AS max 
                  FROM parametros 
                  WHERE idparametro NOT IN (
                      SELECT idparametro 
                      FROM valoresdereferencia v 
                      JOIN q1 ON v.id_sap = q1.id_sap
                  )
              ) 
              SELECT DISTINCT secuenciainspeccion, nombrecliente, cantidadlote, numeroorden, q1.numerolote, fechaanalisis, 
                              q1.numerocertificado, resultado >= min AND resultado <= max AS cumple 
              FROM resultados r
              JOIN certificado c ON r.numerocertificado = q1.numerocertificado
              JOIN q1 ON r.numerocertificado = q1.numerocertificado
              JOIN q2 ON r.idparametro = q2.idparametro 
              ORDER BY numeroorden";

      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':month', $month, PDO::PARAM_INT);
      $stmt->bindParam(':year', $year, PDO::PARAM_INT);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $trueCount = 0;
      $falseCount = 0;

      foreach ($results as $row) {
        if ($row['cumple']) {
            $trueCount++;
        } else {
            $falseCount++;
        }
      }

      $total = $trueCount + $falseCount;

      $porcentaje_dentro = ($trueCount / $total) * 100;
      $porcentaje_fuera = ($falseCount / $total) * 100;
  } else {
      // Default values if no search has been made
      $porcentaje_dentro = 50; // Example default value
      $porcentaje_fuera = 50; // Example default value
  }
  ?>

  <div class="simple-bar-chart">
      <div class="barra" style="--clr: #3498db; --val: <?php echo $porcentaje_dentro; ?>%">
          <div class="label">Debajo de los parámetros</div>
          <div class="value"><?php echo $porcentaje_dentro; ?>%</div>
      </div>

      <div class="barra" style="--clr: #FCB72A; --val: <?php echo $porcentaje_fuera; ?>%">
          <div class="label">Encima de los parámetros</div>
          <div class="value"><?php echo $porcentaje_fuera; ?>%</div>
      </div>
  </div>

</body>
</html>
