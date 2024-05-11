<?php
$host = "222.165.237.166:33661";
$username = "sgv";
$password = "Secr3t@2016";
$database = "db_balitimbungan";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
