<?php
session_start(); // Asegúrate de iniciar la sesión al principio del archivo
$rol = $_SESSION['rol'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Conexión a la base de datos
    try {
        $pdo = new PDO("pgsql:host=localhost;dbname=ds", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara la consulta SQL para evitar inyecciones SQL
        $stmt = $pdo->prepare("SELECT id, nombres, apellidos, rol FROM usuarios WHERE id = :usuario AND contrasena = :contrasena");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombres'] = $row['nombres'];
            $_SESSION['rol'] = $row['rol'];

            header("Location: index.php"); // Redirigir al usuario a la página principal
            exit();
        } else {
            echo "<p class='error'>Usuario o contraseña incorrecta</p>";
        }
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Harinas Elizondo: Control de Laboratorio - Iniciar sesión</title>
  <link rel="stylesheet" href="../CSS/Login.css">
  <link rel="stylesheet" href="../CSS/header&footer.css">
</head>
<body>
  <header class="header">
    <div class="nav-container">
      <a href="index.php" class="logo">
        <img src="../Imagenes/LogoHeader.png" alt="Logo">
      </a>
      <nav class="navbar">
        <ul class="nav-links">
          <li class="nav-item">
            <a href="login.php">Inicio Sesión</a>
          </li>
          <li class="nav-item">
            <a href="index.php">Índice</a>
          </li>
          <!-- Más elementos según sea necesario -->
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h2>Harinas Elizondo: Control de Laboratorio</h2>
    <div class="box-and-logo">
      <div class="login-box">
        <p>Ingresa tus credenciales para tener acceso</p>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿Problemas? Contacta soporte <a href="ContactUs.html">aquí</a>.</p>
      </div>
      <div class="logo">
        <img src="../Imagenes/Logo-Harinas-Elizondo.png" alt="Logo">
      </div>
    </div>
  </div>
  <script src="../JS/RegistroCliente.js"></script>
  <script src="../JS/Header&Footer.js"></script>
</body>
</html>

