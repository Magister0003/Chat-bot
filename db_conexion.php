<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "miempresabot";

$conexion = mysqli_connect($servername, $username, $password, $dbname); // Cambiado de $conn a $conexion

if (!$conexion) {
  die("Connection failed: " . mysqli_connect_error());
}
?>