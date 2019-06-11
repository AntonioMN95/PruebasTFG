s<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
	<title>Banco Tiempo </title>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="icon" href="../../resources/img/favicon.ico">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="../../css/banco.css">

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>

<body>
	<?php
    // Conectamos con mysql
	include("../conexion.php");
	$fecha_inicial=date("2018-09-01");
	$fecha_actual=date("Y-m-d");
	$semana = date('Y-m-d', strtotime("$fecha_actual + 7 days"));
	$buscar="where (fechainicio between '$fecha_inicial' and '$semana')";
	$buscarTexto="Noticias del ".date("d-m-Y", strtotime("$fecha_inicial"))." al ".date('d-m-Y', strtotime("$fecha_actual + 7 days"));
	if(isset($_GET['mes']))  // Determinamos el periodo exacto par realizar la consulta
	{
		$periodo=substr($_GET['mes'],3);
		if ($periodo=='Primer Trimestre')
			$busqueda=" '%Primer Trimestre%' or mes like '%Septiembre%' or mes like '%Octubre%' or mes like '%Noviembre%' or mes like '%Diciembre%' or month(fechainicio) between 9 and 12";
		if ($periodo=='Segundo Trimestre')
			$busqueda=" '%Segundo Trimestre%' or mes like '%Enero%' or mes like '%Febrero%' or mes like '%Marzo%' or mes like '%Diciembre%' or month(fechainicio) = 12 or month(fechainicio) between 1 and 3";
		if ($periodo=='Tercer Trimestre')
			$busqueda=" '%Tercer Trimestre%' or mes like '%Abril%' or mes like '%Mayo%' or mes like '%Marzo%' or mes like '%Junio%' or month(fechainicio) between 3 and 6";
		if ($periodo=='Sin determinar')
			$busqueda=" '%Sin determinar%'";
		if ($periodo=='Enero')
			$busqueda=" '%Enero%' or month(fechainicio) = 1";
		if ($periodo=='Febrero')
			$busqueda=" '%Febrero%' or month(fechainicio) = 2";
		if ($periodo=='Marzo')
			$busqueda=" '%Marzo%' or month(fechainicio) = 3";
		if ($periodo=='Abril')
			$busqueda=" '%Abril%' or month(fechainicio) = 4";
		if ($periodo=='Mayo')
			$busqueda=" '%Mayo%' or month(fechainicio) = 5";
		if ($periodo=='Junio')
			$busqueda=" '%Junio%' or month(fechainicio) = 6";
		if ($periodo=='Julio')
			$busqueda=" '%Julio%' or month(fechainicio) = 7";
		if ($periodo=='Agosto')
			$busqueda=" '%Agosto%' or month(fechainicio) = 8";
		if ($periodo=='Septiembre')
			$busqueda=" '%Septiembre%' or month(fechainicio) = 9";
		if ($periodo=='Octubre')
			$busqueda=" '%Octubre%' or month(fechainicio) = 10";
		if ($periodo=='Noviembre')
			$busqueda=" '%Noviembre%' or month(fechainicio) = 11";
		if ($periodo=='Diciembre')
			$busqueda=" '%Diciembre%' or month(fechainicio) = 12";
	}
	if(isset($_GET['not']) and $_GET['not']!='' and isset($_GET['mes']) and $_GET['mes']!='')
	{
		
		$buscar=" where ((actividad like '%$_GET[not]%' or participantes like '%$_GET[not]%') and (mes like $busqueda))";
		$buscarTexto= "$_GET[not] $periodo";
	}
	else
	{
		if(isset($_GET['not']) and $_GET['not']!='')
		{
			$buscar=" where (actividad like '%$_GET[not]%' or participantes like '%$_GET[not]%')";
			$buscarTexto= "$_GET[not]";

		}
		if(isset($_GET['mes'])and $_GET['mes']!='')
		{
			$buscar=" where mes like $busqueda";
			$buscarTexto= "$periodo";

		}
	}
		

	// Creamos la consulta
	$sql = "SELECT * FROM gn_noticias as gn ".$buscar." ORDER BY fechainicio DESC, mes DESC";

	$registros=mysqli_query($conexion,$sql);
	$total=mysqli_num_rows($registros);
	
	// Comprobamos si el usuario que accede tiene perfil de direccion
	$sql1 = "SELECT a.*, d.departamento, p.perfil FROM accesos as a,perfiles as p, departamentos as d WHERE a.idprofesor = '$_SESSION[idprofesor]' and a.idperfil=p.idperfil and a.iddepartamento=d.iddepartamento";
	$registros1=mysqli_query($conexion,$sql1);
	$ok=0;
	$departamento="";
	while($linea1=mysqli_fetch_array($registros1))
    {
		if($linea1['perfil']=="Dirección" || $linea1['perfil']=="Comunicación Social" || $linea1['perfil']=="Escuela Empresa")
			$ok=1;
		if($linea1['perfil']=="Jefe de Departamento")
		{	$ok=2;
			$departamento=$linea1['departamento']; //Guardo el Dpto para que el JD sólo pueda cambiar las de su departamento.
		}
		if($linea1['perfil']=="Profesor")
		{	$ok=3;
			
		}


	}

	?>
	
	<!-- Preparamos la estructura de la pagina -->
	<div class="container">
		<!-- Navbar -->
	<?php 
	include("../navForms.php");				//Barra de navegación
	?>

				<div class="form-row">
					<div class="form-group col-lg-12">
						<div class="alert alert-secondary" role="alert">
						  <?php echo $buscarTexto ?>
						</div>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-lg-6">
						<input type="text" name="not" id="not" class="form-control" placeholder="Buscador por titulo o Departamento">
					</div>
			
		
				</div>
				<!-- <div class="form-row"> -->
			
			<!-- </div> -->
	</div>
</body>
</html>