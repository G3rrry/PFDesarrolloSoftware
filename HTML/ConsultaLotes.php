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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Lotes - Harinas Elizondo</title>
    <link rel="stylesheet" type="text/css" href="../CSS/header&footer.css">
    <link rel="stylesheet" type="text/css" href="../CSS/ContactUs.css">
    <link rel="stylesheet" href="../CSS/ConsultaClientes.css">
    <link rel="stylesheet" href="../CSS/ConsultaLotes.css">
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
        <div class="search-container">
            <input type="search" id="search-lot" placeholder="Buscar lote..." name="search">
            <button type="submit" id="search-button">Buscar</button>
        </div>

        <h2>Listado de Lotes</h2>

        <table class="lot-table">
            <thead>
                <tr>
                    <th>Fecha de Producción</th>
                    <th>Fecha de Caducidad</th>
                    <th>Peso (Toneladas)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lotes as $lote): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lote['fechaproduccion']); ?></td>
                    <td><?php echo htmlspecialchars($lote['fechacaducidad']); ?></td>
                    <td><?php echo htmlspecialchars($lote['cantidadlote']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer-buttons">
            <button onclick="location.href='index.php'">Volver a la página principal</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-lot');
            const searchButton = document.getElementById('search-button');
            const tableBody = document.querySelector('.lot-table tbody');

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
	    </body>
