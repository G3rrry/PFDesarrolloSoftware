<?php
session_start();
$rol = $_SESSION['rol'];
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || 
    ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2 && $_SESSION['rol'] != 3)) {
    // Si no hay una sesión activa o si el rol no es 1, 2 o 3, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

try {
    // Conexión a la base de datos con PDO
    $pdo = new PDO('pgsql:host=localhost;dbname=ds', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener todos los equipos
    $sql = "SELECT claveEquipo, marca, modelo, descripcionCorta FROM EquipoLaboratorio";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
    $equipos = []; // Asegúrate de manejar correctamente la falta de datos
    echo "<script>alert('Error de conexión: " . $e->getMessage() . "');</script>";
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
    <link rel="stylesheet" href="../CSS/ConsultaEquipoLab.css">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-lab-equip');
            const searchButton = document.getElementById('search-button');
            const tableBody = document.querySelector('.equipment-table tbody');

            searchButton.addEventListener('click', function () {
                const searchText = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    let found = false;
                    const cells = rows[i].getElementsByTagName('td');
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(searchText)) {
                            found = true;
                            break;
                        }
                    }
                    rows[i].style.display = found ? '' : 'none';
                }
            });
        });
    </script>
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
  <body>
    <div class="content-container">
        <div class="search-container">
          <input type="search" id="search-lab-equip" placeholder="Buscar texto..." name="search">
          <button type="button" id="search-button">Buscar</button>
        </div>

        <h2>Listado de equipos</h2>

        <table class="equipment-table">
            <thead>
                <tr>
                    <th>Clave equipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Descripción corta</th>
                    <th>Mostrar más</th>
                    <?php if ($rol == 1 || $rol == 2): ?>
                    <th>Modificar</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($equipo['claveequipo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['marca']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['descripcioncorta']); ?></td>
                    <td><button onclick="location.href='InformacionEquipos.php?claveEquipo=<?php echo $equipo['claveequipo']; ?>'">Ver</button></td>
                    <?php if ($rol == 1 || $rol == 2): ?>
                    <td><button onclick="location.href='ActualizarEquiposLab.php?claveEquipo=<?php echo $equipo['claveequipo']; ?>'">Editar</button></td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer-buttons">
            <button onclick="location.href='index.php'">Volver a la página principal</button>
        </div>
    </div>

    <!-- Aquí puedes poner tu script para mostrar el mensaje si es necesario -->
    <script>
        var mensaje = <?php echo json_encode($mensaje); ?>;
        if (mensaje) {
            alert(mensaje);
        }
    </script>
</body>
</html>
