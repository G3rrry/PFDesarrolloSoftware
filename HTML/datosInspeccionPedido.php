<?php
session_start();
$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id'])) {
	    header("Location: login.php");
	        exit();
}
try {
	    $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
	        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	        $sql = "with ord as (select numerolote, sum(cantidadlote) as sum from orden o, certificado c where c.numerocertificado=o.numerocertificado group by numerolote), aja as (SELECT l.numerolote, fechaProduccion, fechaCaducidad, l.cantidadLote-sum as cantidadreal FROM Lotes l, ord where ord.numerolote=l.numerolote union select numerolote, fechaproduccion, fechacaducidad, cantidadlote as cantidadreal from lotes) select numerolote, fechaproduccion, fechacaducidad, min(cantidadreal) as cantidadlote from aja group by numerolote, fechaproduccion, fechacaducidad order by fechacaducidad";
		    $stmt = $pdo->prepare($sql);
		    $stmt->execute();
		        $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	    echo "Error de conexión: " . $e->getMessage();
	        exit;
}
?>

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
    <h2>Registro Exitoso de Resultados</h2>
    <div class="box-and-logo">
        <div class="login-box">
          <h3>Datos insertados</h3>

          <p></p>
          <h3>Resultados Farinografo</h3>
          <p>Resultado Tenacidad: <?php echo $_POST['resultado_tenacidad']; ?></p>
          <p>Extensibilidad: <?php echo $_POST['extensibilidad']; ?></p>
          <p>Resultado Fuerza Panadera: <?php echo $_POST['resultado_fuerza_panadera']; ?></p>
          <p>Resultado Relación Curva: <?php echo $_POST['resultado_relacion_curva']; ?></p>

          <h3>Resultados Alveografo</h3>
          <p>Resultado Absorción Agua: <?php echo $_POST['resultado_absorcion_agua']; ?></p>
          <p>Resultado Tiempo Desarrollo Masa: <?php echo $_POST['resultado_tiempo_desarrollo_masa']; ?></p>
          <p>Resultado Estabilidad: <?php echo $_POST['resultado_estabilidad']; ?></p>
          <p>Resultado Grado Reblandecimiento: <?php echo $_POST['resultado_grado_reblandecimiento']; ?></p>

            <?php
            session_start();

            
            // Conexión a la base de datos
            $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Obtener el último número de certificado y calcular el siguiente
            $nextCertificado = $_POST['numerocertificado'];
            echo $nextCertificado;

            // Datos del lote recibidos por POST
            $numerolote = $_POST['numero-lote'];

            $fechaAnalisis = $_POST['fechadeanalisis'];
            $secuenciaInspeccion = $_POST['secuencia-inspeccion'];
            $secuenciaInspeccion = trim($secuenciaInspeccion); // Elimina espacios del inicio y final
            $secuenciaInspeccion = str_replace(' ', '', $secuenciaInspeccion); // Elimina todos los espacios en blanco
            if (strlen($secuenciaInspeccion) > 2) {
                die("Error: La secuencia de inspección es demasiado larga.");
            }

            try {

                // Array de parámetros con sus correspondientes resultados
                $parametros = [
                    ['idparametro' => 1, 'resultado' => $_POST['resultado_tenacidad'], 'claveequipo' => $_POST['equipo']],
                    ['idparametro' => 2, 'resultado' => $_POST['extensibilidad'], 'claveequipo' => $_POST['equipo']],
                    ['idparametro' => 3, 'resultado' => $_POST['resultado_fuerza_panadera'], 'claveequipo' => $_POST['equipo']],
                    ['idparametro' => 4, 'resultado' => $_POST['resultado_relacion_curva'], 'claveequipo' => $_POST['equipo']],
                    ['idparametro' => 5, 'resultado' => $_POST['resultado_absorcion_agua'], 'claveequipo' => $_POST['equipofar']],
                    ['idparametro' => 6, 'resultado' => $_POST['resultado_tiempo_desarrollo_masa'], 'claveequipo' => $_POST['equipofar']],
                    ['idparametro' => 7, 'resultado' => $_POST['resultado_estabilidad'], 'claveequipo' => $_POST['equipofar']],
                    ['idparametro' => 8, 'resultado' => $_POST['resultado_grado_reblandecimiento'], 'claveequipo' => $_POST['equipofar']],
                ];

                // Insertar los resultados usando el nuevo número de certificado
                foreach ($parametros as $param) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO resultados (resultado, numerocertificado, idparametro, claveequipo) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$param['resultado'], $nextCertificado, $param['idparametro'], $param['claveequipo']]);
                    } catch (PDOException $e) {
                        echo "Error al insertar resultado para el parámetro ID {$param['idparametro']}: " . $e->getMessage();
                    }
                }
            } catch (PDOException $e) {
                echo "Error al preparar la inserción de resultados: " . $e->getMessage();
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO fechadeanalisis (numerocertificado, fechaanalisis, secuenciainspeccion) VALUES (?, ?, ?)");
                $stmt->execute([$nextCertificado, $fechaAnalisis, $secuenciaInspeccion]);
                echo "Fecha de análisis registrada correctamente.";
            } catch (PDOException $e) {
                echo "Error al insertar fecha de análisis: " . $e->getMessage();
            }


            ?>
        </div>
    </div>
</div>
<div id="countdown">15</div>
<script src="../JS/kaboomRegistro.js"></script>


</html>
