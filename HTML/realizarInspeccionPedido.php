<?php
session_start();
$rol = $_SESSION['rol'];

echo "<pre>";
var_dump($_GET);
echo "</pre>";

session_start();
$rol = $_SESSION['rol'];

// Conexión a la base de datos
$pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_GET['numerolote'])) {
    $numerolote = $_GET['numerolote'];
    echo "Número de lote recibido: " . $numerolote;
} else {
    echo "Número de lote no recibido.";
    exit;
}

$nextCertificado = $_GET['numerocertificado'];

// Preparar y ejecutar la consulta
$stmt = $pdo->prepare("SELECT claveequipo, marca, modelo FROM EquipoLaboratorio WHERE farinografo='0' AND estado = 'activo'");
$stmt->execute();
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar y ejecutar la consulta
$stmt = $pdo->prepare("SELECT claveequipo, marca, modelo FROM EquipoLaboratorio WHERE farinografo='1' AND estado = 'activo'");
$stmt->execute();
$equiposfar = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    // Consulta para obtener la máxima secuencia de inspección para un lote específico
    $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
    $stmt = $pdo->prepare("SELECT max(f.secuenciainspeccion) as maxsecuencia FROM certificado c JOIN fechadeanalisis f ON c.numerocertificado = f.numerocertificado WHERE c.numerolote = ?");
    $stmt->execute([$numerolote]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentSecuencia = $result ? $result['maxsecuencia'] : ''; // Asegúrate de que existe un valor
    echo $currentSecuencia;
    // Incrementa la secuencia
    $nextSecuencia = incrementLetterSequence($currentSecuencia);
    

    // Aquí podrías realizar más operaciones, como insertar un nuevo registro con $nextSecuencia
    echo "La siguiente secuencia es: $nextSecuencia";

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para incrementar la secuencia de letras
function incrementLetterSequence($seq) {
    if (empty($seq)) return 'A';
    $length = strlen($seq);
    $lastChar = $seq[$length - 1];
    $rest = substr($seq, 0, $length - 1);
    if ($lastChar == 'Z') {
        return $length > 1 ? incrementLetterSequence($rest) . 'A' : 'AA';
    } else {
        $lastChar++;
        return $rest . $lastChar;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Resultados - Harinas Elizondo</title>
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
          <li class="nav-item"><a href="ContactUs.php">Contacto Sistemas</a></li>
          
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
            <form action="datosInspeccionPedido.php" method="post">
                <h2>Registro de Resultados</h2>
                <!-- Busqueda de Numero de Lote CON SEARCHBAR -->
                <!-- Busqueda de Numero de Lote CON SEARCHBAR -->

                <div class="search-container">
                    <label for="numero-lote">Número de Lote:</label>
                    <input type="text" id="numero-lote" name="numero-lote" value="<?php echo htmlspecialchars($numerolote); ?>" readonly onfocus="this.blur()"> 
                </div>
                
                <label for="secuencia-inspeccion">Secuencia de Inspección:</label>
                <input type="text" id="secuencia-inspeccion" name="secuencia-inspeccion" value=" <?php echo htmlspecialchars($nextSecuencia); ?> " readonly>            
                <input type="hidden" name="numerocertificado" value="<?php echo htmlspecialchars($nextCertificado); ?>">
                    
            <!-- Parámetros Alveógrafo -->
                <div id="parametros_alveografo">
                    <h3>Parámetros Alveógrafo</h3>
                    <label for="tenacidad">Tenacidad (50-55)</label>
                    <input type="text" id="resultado_tenacidad" name="resultado_tenacidad" maxlength="2" pattern="[0-9]{2}">
                    
                    <label for="extensibilidad">Extensibilidad (110-120)</label>
                    <input type="text" id="extensibilidad" name="extensibilidad" maxlength="3" pattern="[0-9]{3}">
                    
                    
                    <label for="fuerza_panadera">Fuerza Panadera (180-250)</label>
                    <input type="text" id="resultado_fuerza_panadera" name="resultado_fuerza_panadera" maxlength="3" pattern="[0-9]{3}">
                    
                    <label for="relacion_curva">Relación de la Curva (0.4-0.6)</label>
                    <input type="text" id="resultado_relacion_curva" name="resultado_relacion_curva" maxlength="5" pattern="\d+(\.\d{1,2})?">

                    <select id="equipo" name="equipo" required>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo htmlspecialchars($equipo['claveequipo']); ?>">
                                <?php echo htmlspecialchars($equipo['claveequipo'] . ' - ' .$equipo['marca']) . ' - ' . htmlspecialchars($equipo['modelo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                


                <!-- Parámetros Farinógrafo -->
                <div id="parametros_farinografo">
                    <h3>Parámetros Farinógrafo</h3>
                    <label for="absorcion_agua">Absorción del Agua (0.55-0.65)</label>
                    <input type="text" id="resultado_absorcion_agua" name="resultado_absorcion_agua" maxlength="5" pattern="\d+(\.\d{1,2})?">
                    
                    
                    <label for="tiempo_desarrollo_masa">Tiempo de Desarrollo de la Masa (180-300)</label>
                    <input type="text" id="resultado_tiempo_desarrollo_masa" name="resultado_tiempo_desarrollo_masa" maxlength="3" pattern="[0-9]{3}">

                    
                    <label for="estabilidad">Estabilidad (300-600)</label>
                    <input type="text" id="resultado_estabilidad" name="resultado_estabilidad" maxlength="3" pattern="[0-9]{3}">
                    
                    
                    <label for="grado_reblandecimiento">Grado de Reblandecimiento (40-90)</label>
                    <input type="text" id="resultado_grado_reblandecimiento" name="resultado_grado_reblandecimiento" maxlength="2" pattern="[0-9]{2}">

                    <select id="equipofar" name="equipofar" required>
                        <?php foreach ($equiposfar as $equipofar): ?>
                            <option value="<?php echo htmlspecialchars($equipofar['claveequipo']); ?>">
                                <?php echo htmlspecialchars($equipofar['claveequipo'] . ' - ' . $equipofar['marca']) . ' - ' . htmlspecialchars($equipo['modelo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    
                </div>

                <label for="fechadeanalisis">Fecha de Análisis:</label>
                <input type="date" id="fechadeanalisis" name="fechadeanalisis" required>
                
                <!-- Submit y Exit Buttons -->
                <button type="submit" class="submit-button" >Registrar Resultados</button>
                <button type="button" class="submit-button" onclick="location.href='index.php'">Volver a la página principal</button>
            </form>
        </div>
    </div>
</body>
  

</html>