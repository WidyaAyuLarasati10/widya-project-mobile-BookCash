<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Memanggil koneksi database
require_once 'koneksi.php';

// Menerima data POST dari App Inventor
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validasi jika kosong
if (empty($username) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Username atau password wajib diisi"
    ]);
    exit();
}

// PERBAIKAN: Ubah 'kasir' menjadi 'users' sesuai nama tabel di phpMyAdmin kamu
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Jika data ditemukan / cocok
    echo json_encode([
        "status" => "success",
        "message" => "Login berhasil"
    ]);
} else {
    // Jika data tidak cocok
    echo json_encode([
        "status" => "error",
        "message" => "Username atau password salah"
    ]);
}

$conn->close();
?>