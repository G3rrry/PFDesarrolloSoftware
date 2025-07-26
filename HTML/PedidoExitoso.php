<?php
session_start();
$rol = $_SESSION['rol'];

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {

	            header("Location: login.php");
		                    exit();
}
$rol = $_SESSION['rol'];
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
    <div class="box-and-logo">
      <div class="login-box">
        <H2>Registro de Nuevo pedido realizado con exito</H2>
      </div>

    </div>
  </div>


<?php
$conn = pg_connect("host=localhost dbname=ds user=root password=root")
	          or die('No se pudo conectar: ' . pg_last_error());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$sqlcantidad="SELECT sum(cantidadlote) FROM lotes where numerolote in (select numerolote from certificado)";
			$sqlcantidadorden="SELECT sum(cantidadlote) FROM orden";
			$sqllotes="with ord as (select numerolote, sum(cantidadlote) as sum from orden o, certificado c where c.numerocertificado=o.numerocertificado group by numerolote), aja as (SELECT l.numerolote, l.cantidadLote-sum as cantidadreal FROM Lotes l, ord where ord.numerolote=l.numerolote union select numerolote, cantidadlote as cantidadreal from lotes), opa as (select numerolote from aja group by numerolote having min(cantidadreal)>0), p as (select idparametro, min as min, max as max from valoresdereferencia where ID_SAP=$1 union select idparametro, minint as min, maxint as max from parametros p where idparametro not in (select idparametro from valoresdereferencia where ID_SAP=$1)), q as (select l.numerolote from lotes l, certificado c, resultados r, p where l.numerolote=c.numerolote and r.numerocertificado= c.numerocertificado and p.idparametro =r.idparametro and p.min<resultado and p.max>resultado and r.numerocertificado in (select r.numerocertificado from resultados r, certificado c, fechadeanalisis f where c.numerocertificado=r.numerocertificado and c.numerocertificado=f.numerocertificado and l.numerolote in (select numerolote from opa) and fechaanalisis in (select max(fechaanalisis) from fechadeanalisis f, certificado c where f.numerocertificado=c.numerocertificado group by numerolote)) group by l.numerolote order by fechacaducidad) select numerolote from q union all (select l.numerolote from lotes l, certificado c where l.numerolote=c.numerolote and l.numerolote in (select numerolote from opa) and l.numerolote not in (select numerolote from q) group by l.numerolote order by fechacaducidad)";
				$sqlcuantoxlote="SELECT cantidadlote FROM lotes where numerolote=$1";
				$sqlxordeado="select sum(cantidadlote) from orden";
					$sqlcertificado="INSERT INTO certificado (numerolote) values ($1)";
					$sqlpedido="INSERT INTO pedido (iddomicilio, cantidadtotal, numerofactura,fechaenvio, enviado) values ($1,$2,$3,$4,$5)";
						$sqlnumcert="select max(numerocertificado) from certificado";
						$sqlnumord="select max(numeroorden) from pedido";
							$sqlordenes="INSERT INTO orden (numeroorden, numerocertificado, cantidadlote) values ($1,$2,$3)";
						$datosp = array(
								(int) $_POST['ID_SAP_CLIENTE'],
									$_POST['iddomicilio'],
										(int) $_POST['cantidad-total'],
											date('Y-m-d', strtotime($_POST['fecha-envio'])),
												(int) $_POST['numerofactura']
						);
						$cantidadactual=$datosp[2];
						$total=pg_fetch_row(pg_query($conn, $sqlcantidad))[0]-pg_fetch_row(pg_query($conn, $sqlcantidadorden))[0];
						if($cantidadactual<=$total){
								$id_sap= array($datosp[0]);
									$altapedido= array($datosp[1], $datosp[2], $datosp[4], $datosp[3], 0);
									$exito1=pg_query_params($conn, $sqlpedido, $altapedido);
										$numerorden=pg_fetch_row(pg_query($conn, $sqlnumord))[0];
										$lotesamandar=pg_query_params($conn, $sqllotes, $id_sap);
											while ($row = pg_fetch_assoc($lotesamandar) and $cantidadactual>0) {
														$lotescogido=array($row['numerolote']);
																$exito2= pg_query_params($conn, $sqlcertificado, $lotescogido);
																$numcert=pg_fetch_row(pg_query($conn, $sqlnumcert))[0];
																		$loteordenado=pg_fetch_row(pg_query_params($conn, $sqlxordeado, $lotescogido))[0];
																		$cantidadlote=pg_fetch_row(pg_query_params($conn, $sqlcuantoxlote, $lotescogido))[0]-$loteordenado;
																				$orden=array($numerorden, $numcert, min($cantidadlote, $cantidadactual));
																				$exito3= pg_query_params($conn, $sqlordenes, $orden);
																						$cantidadactual-=$cantidadlote;
																					}
											if(!($exito1 and $exito2 and $exito3)){
														echo "<h1>Error al agregar pedido</h1>";
																echo pg_last_error($conn);
															}else{
																		echo "<h1>Pedido agregado exitosamente</h1>";
																			}
						}else{echo "<h1>Mayor a lo que hay en el almacen</h1>";}
} else {
	          echo "Error with POST";
}
?>
</body>
