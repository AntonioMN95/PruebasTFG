<?php
session_start();
?>
<?php
// Conectamos con mysql
include("../conexion.php");
$alum = $_POST['alumno'];
$cif = $_POST['cif'];
$hora = $_POST['horas'];

$sql="INSERT INTO gf_banco (idAlumno, numHoras, cif) VALUES ('$alum', '$cif','$numHoras')";
mysqli_query($conexion, $sql) or die ("Error en la consulta $sql");
mysqli_close($conexion);

//echo "<SCRIPT>history.back(1)</SCRIPT>";  
echo "<SCRIPT>window.open('formBancpTiempo.php','_parent')</SCRIPT>";  
?>