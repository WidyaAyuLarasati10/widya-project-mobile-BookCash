<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_bookcash";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal"]);
    exit();
}
?>