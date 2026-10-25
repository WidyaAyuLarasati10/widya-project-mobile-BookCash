<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Memanggil koneksi database
require_once 'koneksi.php';

// Mengambil semua data buku dari tabel database
$result = $conn->query("SELECT * FROM buku");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id_buku" => $row['id_buku'],
        "kode_barcode" => $row['kode_barcode'],
        "judul" => $row['judul'],
        "penulis" => $row['penulis'],
        "harga" => $row['harga'],
        "stok" => $row['stok']
    ];
}

// Mengirimkan data dalam format JSON yang sesuai dengan Screen 3
echo json_encode([
    "status" => "success",
    "data" => $data
]);

$conn->close();
?>