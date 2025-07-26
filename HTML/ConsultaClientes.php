<?php
session_start();
$rol = $_SESSION['rol'];

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || 
    ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2 && $_SESSION['rol'] != 3)) {
    // Si no hay una sesión activa o si el rol no es 1, 2 o 3, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}


if (isset($_GET['ID_SAP_CLIENTE'])) {
    $idSap = $_GET['ID_SAP_CLIENTE'];

    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Clientes - Harinas Elizondo</title>
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

    <div class="form-container">
        <form action="" method="GET"> <!-- Enviar al mismo archivo PHP para procesamiento -->
            <div class="form-group">
                <label for="ID_SAP_CLIENTE">ID SAP:</label>
                <input type="text" id="ID_SAP_CLIENTE" name="ID_SAP_CLIENTE" required maxlength="6">
                <button type="submit">Buscar</button>
            </div>
        </form>
    </div>
    

    <div class="results-container">
        <div style="display: flex; justify-content: space-between;">

            <div style="width: 50%;">
                <h2>Información del Cliente</h2>
                <p><strong>Estado del cliente: </strong><span><?php echo $cliente['estadoCliente'] ? 'Inactivo': 'Activo' ; ?></span></p>
                <p><strong>ID SAP:</strong> <?php echo htmlspecialchars($idSap); ?></p>
                <p><strong>Nombre:</strong><span><?php echo htmlspecialchars($cliente['nombrecliente']); ?></span></p>
                <p><strong>Correo-e:</strong><span><?php echo htmlspecialchars($cliente['correo']); ?></span></p>
                <p><strong>Teléfono:</strong><span><?php echo htmlspecialchars($cliente['telefono']); ?></span></p>
                <p><strong>Requiere certificado?</strong><span><?php echo htmlspecialchars($cliente['requierecertificado']); ?></span></p>
            </div>

            <div style="width: 50%;">
                <h2>Información de Contacto</h2>
                <p><strong>Nombre del Contacto:</strong><span><?php echo htmlspecialchars($cliente['nombrecontacto']); ?></span></p>
                <p><strong>Correo-e del Contacto:</strong><span><?php echo htmlspecialchars($cliente['correocontacto']); ?></span></p>
                <p><strong>Teléfono de Contacto:</strong><span><?php echo htmlspecialchars($cliente['telefonocontacto']); ?></span></p>
                <!-- Espacio en blanco-->
                <p></p>
                <p></p>
                <h2>Estado del cliente</h2>
                <p><strong>Estado actual:</strong> <span><?php echo htmlspecialchars($cliente['estadocliente']); ?></span></p>
                <p><strong>Causa de baja:</strong> <span id="causa_baja"></span></p>
            </div>
            
        </div>
        
        <div style="display: flex; justify-content: space-between;">
          <div style="width: 50%;">
              <h2>Datos para Facturación</h2>
              <div>
                  <p><strong>RFC:</strong> <span><?php echo htmlspecialchars($cliente['rfc']); ?></span></p>
                  <p><strong>Domicilio Registrado Factura:</strong> <span><?php echo htmlspecialchars($cliente['domiciliofiscal']); ?></span></p>
              </div>
          </div>

          <div style="width: 50%;">
              <h2>Acciones</h2>
              <div style="display: flex; justify-content: space-between;">
                  <div style="flex: 1; margin-right: 10px;">
                      <?php if ($rol == 1 || $rol == 2): ?>
                      <form action="ActualizarCliente.php" method="get">
                          <input type="hidden" name="ID_SAP_CLIENTE" value="<?php echo htmlspecialchars($idSap); ?>">
                          <button type="submit" style="width: 100%; padding: 10px; background-color: #00143f; color: white; border: none; border-radius: 5px; cursor: pointer;">Actualizar Cliente</button>
                      </form>
                      <?php endif; ?>
                  </div>
                  <div style="flex: 1; margin-left: 10px;">
                      <form action="AgregarPedido.php" method="get">
                          <input type="hidden" name="ID_SAP_CLIENTE" value="<?php echo htmlspecialchars($idSap); ?>">
                          <?php if ($rol == 1 || $rol == 2): ?>
                          <button type="submit" style="width: 100%; padding: 10px; background-color: #00143f; color: white; border: none; border-radius: 5px; cursor: pointer;">Agregar Pedido</button>
                          <?php endif; ?>
                        </form>
                  </div>
                  <div style="flex: 1; margin-left: 10px;">
                      <form action="agregarNuevoDomicilio.php" method="get">
                          <input type="hidden" name="ID_SAP_CLIENTE" value="<?php echo htmlspecialchars($idSap); ?>">
                          <?php if ($rol == 1 || $rol == 2): ?>
                          <button type="submit" style="width: 100%; padding: 10px; background-color: #00143f; color: white; border: none; border-radius: 5px; cursor: pointer;">Agregar Domicilio</button>
                          <?php endif; ?>
                        </form>
                  </div>



              </div>
          </div>
          
      </div>
      <h2>Domicilios de Entrega</h2>
      <!-- Domicilios de entrega datos -->
      
      <table border="1" style="width: 100%;">
        <?php
          $sql="SELECT DOMICILIO FROM DOMICILIO AS D, CLIENTES AS C WHERE C.ID_SAP=D.ID_SAP AND C.ID_SAP=:idSap";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
          $stmt->execute();
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          $count = 0;
          foreach($rows as $row) {
            $count+=1;
          ?>
          <tr>
              <td><?php echo $count; ?></span></td>
              <td><?php echo htmlspecialchars($row['domicilio']); ?></span></td>
          </tr>
        <?php } ?>  
      </table>
<!--
     <table border="1" style="width: 100%;">
       <tr>
           <th><span id="domicilio">Calle</span></th>
           <th><span id="CP_entrega">Código Postal</span></th>
           <th><span id="colonia_cliente">Colonia Entrega:</span></th>
           <th><span id="NumExt_Entrega">Número exterior Entrega:</span></th>
           <th><span id="NumInt_Entrega">Número Interior Entrega:</span></th>

       </tr>
       <tr>
           <td><span id="domicilio">Test</span></td>
           <td><span id="CP_entrega"></span></td>
           <td><span id="colonia_cliente"></span></td>
           <td><span id="NumExt_Entrega"></span></td>
           <td><span id="NumInt_Entrega"></span></td>

       </tr>
       <-- Más filas según los datos de la base de datos
   </table>
-->
        <h2>Historial de Pedidos y Ordenes del Cliente</h2>
        <table border="1" style="width: 100%;">
          <tr>
              <th><span id="numeroPedido">Número de Orden</span></th>
              <th><span id="cantidadSolicitada">Cantidad Solicitada</span></th>
              <th><span id="cantidadTotal">Cantidad Total</span></th>
              <th><span id="numeroFactura">Número de Factura</span></th>
              <th><span id="fecha">Fecha</span></th>
          </tr>
          <!-- Los siguientes elementos deben ser llenados con los datos reales -->
          <?php
          $sql = "SELECT O.NUMEROORDEN, CANTIDADLOTE, CANTIDADTOTAL, NUMEROFACTURA, FECHAENVIO 
          FROM PEDIDO AS P, DOMICILIO AS D, ORDEN AS O 
          WHERE P.IDDOMICILIO = D.IDDOMICILIO 
          AND O.NUMEROORDEN = P.NUMEROORDEN 
          AND D.ID_SAP = :idSap ORDER BY FECHAENVIO";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
          $stmt->execute();
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          foreach ($rows as $row) { 
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['numeroorden']); ?></span></td>
                <td><?php echo htmlspecialchars($row['cantidadlote']); ?></span></td>
                <td><?php echo htmlspecialchars($row['cantidadtotal']); ?></span></td>
                <td><?php echo htmlspecialchars($row['numerofactura']); ?></span></td>
                <td><?php echo htmlspecialchars($row['fechaenvio']); ?></span></td>
            </tr>
          <?php } ?>
          
      </table>
      
      <h2>Parametros de Referencia</h2>

      <table border="1" style="width: 100%;">
        <tr>
          <th><span id="nombreparametro">Nombre Parámetro</span></th>
          <th><span id="min">Mínimo</span></th>
          <th><span id="max">Máximo</span></th>
        </tr>
        <?php
          $sql = "SELECT nombreparametro, min, max FROM clientes AS c, valoresdereferencia AS vr, parametros AS p
          WHERE c.id_sap=vr.id_sap AND vr.idparametro=p.idparametro AND c.id_sap=:idSap";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':idSap', $idSap, PDO::PARAM_INT);
          $stmt->execute();
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($rows as $row) { 
          ?>
          <tr>
              <th><?php echo ucwords(htmlspecialchars($row['nombreparametro'])); ?></span></th>
              <td><?php echo htmlspecialchars($row['min']); ?></span></td>
              <td><?php echo htmlspecialchars($row['max']); ?></span></td>
          </tr>
        <?php } ?>
      </table>
      <!--
          </br>
          </br>

      <h2>Parámetros Alveógrafo</h2>

      <table border="1" style="width: 100%;">
        <tr>
            <th><span id="tenacidad_inf tenacidad_sup">Tenacidad</span></th>
            <th><span id="extensibilidad_inf extensibilidad_sup">Extensibilidad</span></th>
            <th><span id="fuerza_panadera">Fuerza Panadera</span></th>
            <th><span id="area_curva">Área de la Curva</span></th>
            <th><span id="area_curva">Relación Curva</span></th>

        </tr>
        
        <tr>
            <td><span id="datosTenacidad">Test</span></td>
            <td><span id="datosExtensibilidad"></span></td>
            <td><span id="datosfuerzaPanadera"></span></td>
            <td><span id="datosAreaCurva"></span></td>
            <td><span id="RelacionCurva"></span></td>
        </tr>
    </table>     

    
    <h2>Parámetros Farinógrafo</h2>

    <table border="1" style="width: 100%;">
      <tr>
          <th><span id="absorcion_agua_inf absorcion_agua_sup">Absorción del agua</span></th>
          <th><span id="tiempo_desarrollo_masa_inf tiempo_desarrollo_masa_sup">Tiempo de desarrollo de la masa (En Segundos)</span></th>
          <th><span id="estabilidad_inf estabilidad_sup">Estabilidad (En Segundos)</span></th>
          <th><span id="grado_reblandecimiento_inf grado_reblandecimiento_sup">Grado reblandecimiento</span></th>
          <th><span id="numeroCalidadFQN">Número de calidad (FQN)</span></th>
      </tr>
      <tr>
          <td><span id="datosAbsorcionAgua">Test</span></td>
          <td><span id="datosTiempoDesMasa"></span></td>
          <td><span id="datosEstabilidad"></span></td>
          <td><span id="datosReblandecimiento"></span></td>
          <td><span id="datosNumFQN"></span></td>
      </tr>
  </table>   
  -->
    </div>
    <div class="container">
      <button type="button" class="submit-button" onclick="location.href='index.php'">Volver a la página principal</button>
    </div>
  </body>

  <script src="../JS/ConsultaClientes.js	"></script>
</html>
