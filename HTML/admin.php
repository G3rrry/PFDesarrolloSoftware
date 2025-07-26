<?php
session_start();
$rol = $_SESSION['rol'];
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
  <title>Inicio - Harinas Elizondo</title>
  <link rel="stylesheet" href="../CSS/header&footer.css">
  <link rel="stylesheet" href="../CSS/index.css">
  <link rel="stylesheet" href="../CSS/Login.css">
  <link rel="stylesheet" href="../CSS/estadistico.css">
  <link rel="stylesheet" href="../CSS/administrar.css">



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
    <h2>Registro de Usuarios</h2>
    <h3>AVISO: SOLO ADMINISTRADORES TIENEN ACCESSO A ESTA PAGINA. SI NO PERTENECES AL PERSONAL
    ADMINISTRATIVO, Y DE ALGUNA MANERA LLEGASTE A ESTA PAGINA, ABANDONALA Y REPORTA
    EL INCIDENTE. DE LO CONTRARIO, HABRA CONSECUENCIAS LEGALES.</h3>
      <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="POST">
        <label for="nombres">Nombres:</label>
        <input class="input"  type="text" id="nombres" name="nombres" required><br><br>
        
        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required><br><br>
        
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required><br><br>
        
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="1">Administrador</option>
            <option value="2">Gerente</option>
            <option value="3">Consultor</option>
        </select><br><br>
        <input type="submit" value="Registrar">
      </form>
  </div>
  <script>
      <?php if ($mensaje): ?>
          alert('<?php echo $mensaje; ?>');
      <?php endif; ?>
  </script>
</body>
</html>