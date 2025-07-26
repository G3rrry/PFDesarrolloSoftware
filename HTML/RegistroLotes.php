<?php
  session_start();
  if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
	        header("Location: login.php");
		      exit();
		  }
  $rol = $_SESSION['rol'];
    try {
	         
	          $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
		        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		        $mensaje = '';

			      if ($_SERVER["REQUEST_METHOD"] == "POST") {
				                $fechaProduccion = $_POST['fecha-produccion'];
						          $fechaCaducidad = $_POST['fecha-caducidad'];
						          $peso = intval($_POST['peso']);
							            $fechaProduccion = date('Y-m-d', strtotime($fechaProduccion));
							            $fechaCaducidad = date('Y-m-d', strtotime($fechaCaducidad));

								              $sql = "INSERT INTO Lotes (fechaCaducidad, fechaProduccion, cantidadLote) VALUES (?, ?, ?)";

								             
								              $stmt = $pdo->prepare($sql);
									                $stmt->bindParam(1, $fechaCaducidad, PDO::PARAM_STR);
									                $stmt->bindParam(2, $fechaProduccion, PDO::PARAM_STR);
											          $stmt->bindParam(3, $peso, PDO::PARAM_INT);

											          if ($stmt->execute()) {
													                $mensaje = "Registro exitoso!";
															          } else {
																	                $mensaje = "Error al registrar: " . $stmt->errorInfo()[2];
																			          }
												        }

			  } catch (PDOException $e) {
				        die("Error de conexión: " . $e->getMessage());
					  }
?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Lotes - Harinas Elizondo</title>
    <link rel="stylesheet" type="text/css" href="../CSS/header&footer.css">
    <link rel="stylesheet" type="text/css" href="../CSS/ContactUs.css">
    <link rel="stylesheet" type="text/css" href="../CSS/RNU.css">
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
     
      <div class="form-container">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
              <label for="fecha-produccion">Fecha de Producción:</label> <p></p>
              <input type="date" id="fecha-produccion" name="fecha-produccion" required><p></p>

              <label for="fecha-caducidad">Fecha de Caducidad:</label> <p></p>
              <input type="date" id="fecha-caducidad" name="fecha-caducidad" required>
              <p></p>
              <label for="peso">Peso (Toneladas):</label> <p></p>
              <input type="number" id="peso" name="peso" required step="0.001" maxlength="3">

              
              <button type="submit" class="submit-button">Registrar Lote</button>
              
              <button type="button" class="submit-button" onclick="location.href='index.php'">Volver a la página principal</button>
          </form>
      </div>
  </div>

  <!--SCRIPTS-->
  <script>
        <?php if ($mensaje): ?>
            alert('<?php echo $mensaje; ?>');
        <?php endif; ?>
  </script>
</body>
</html>
