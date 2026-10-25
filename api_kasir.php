<?php
date_default_timezone_set('Asia/Jakarta'); // Mengatur zona waktu agar jam sesuai waktu lokal (WIB)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");

require_once 'koneksi.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. ACTION: CARI BUKU BERDASARKAN BARCODE
if ($action == 'cari') {
    $barcode = isset($_GET['barcode']) ? $_GET['barcode'] : '';
    
    if (empty($barcode)) {
        echo json_encode(["status" => "error", "message" => "Barcode kosong"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM buku WHERE kode_barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "id_buku" => $row['id_buku'],
            "judul" => $row['judul'],
            "harga" => $row['harga'],
            "stok" => $row['stok']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Buku tidak ditemukan"]);
    }
    $stmt->close();

// 2. ACTION: SIMPAN TRANSAKSI, DETAIL, & POTONG STOK
} elseif ($action == 'simpan') {
    $total = isset($_GET['total']) ? $_GET['total'] : 0;
    $bayar = isset($_GET['bayar']) ? $_GET['bayar'] : 0;
    $metode = isset($_GET['metode']) ? $_GET['metode'] : 'Tunai';
    
    $raw_items = isset($_GET['items']) ? $_GET['items'] : '';

    if ($total <= 0) {
        echo "failed_total";
        exit();
    }

    // Perbaikan rumus kembalian
    $kembalian = $bayar - $total;
    $tanggal = date('Y-m-d H:i:s');

    // Buat tabel transaksi jika belum ada
    $conn->query("CREATE TABLE IF NOT EXISTS transaksi (
        id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
        tanggal DATETIME,
        total DECIMAL(10,2),
        bayar DECIMAL(10,2),
        kembalian DECIMAL(10,2),
        metode VARCHAR(50)
    )");

    // Buat tabel detail_transaksi jika belum ada
    $conn->query("CREATE TABLE IF NOT EXISTS detail_transaksi (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_transaksi INT,
        id_buku INT,
        jumlah INT,
        harga DECIMAL(12,2),
        subtotal DECIMAL(12,2)
    )");

    $stmt = $conn->prepare("INSERT INTO transaksi (tanggal, total, bayar, kembalian, metode) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddds", $tanggal, $total, $bayar, $kembalian, $metode);

    if ($stmt->execute()) {
        $id_transaksi_baru = $stmt->insert_id;
        $stmt->close();

        // Parse data items yang dikirim dari App Inventor
        if (!empty($raw_items)) {
            $items = str_getcsv($raw_items);

            foreach ($items as $item) {
                $id_buku = 0;
                $jumlah = 1;
                $harga = 0;
                $subtotal = 0;

                // Format teks dari App Inventor: Judul Buku | jumlah = Rp subtotal
                $parts = explode('|', $item);
                if (count($parts) >= 2) {
                    $judul_raw = trim(str_replace(["Judul:", "\""], "", $parts[0]));
                    $sub_parts = explode('=', $parts[1]);
                    $jumlah = intval(trim($sub_parts[0]));

                    // Cari id_buku dan harga berdasarkan judul di database
                    $stmt_buku = $conn->prepare("SELECT id_buku, harga FROM buku WHERE judul LIKE ?");
                    $search_title = "%" . $judul_raw . "%";
                    $stmt_buku->bind_param("s", $search_title);
                    $stmt_buku->execute();
                    $res_buku = $stmt_buku->get_result();
                    if ($buku_row = $res_buku->fetch_assoc()) {
                        $id_buku = $buku_row['id_buku'];
                        $harga = $buku_row['harga'];
                    }
                    $stmt_buku->close();

                    // Perbaikan rumus subtotal
                    $subtotal = $jumlah * $harga;
                }

                if ($id_buku > 0 && $jumlah > 0) {
                    // 1. Simpan ke tabel detail_transaksi
                    $stmt_detail = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_buku, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $stmt_detail->bind_param("iiidd", $id_transaksi_baru, $id_buku, $jumlah, $harga, $subtotal);
                    $stmt_detail->execute();
                    $stmt_detail->close();

                    // 2. Potong stok buku di tabel buku secara otomatis
                    $stmt_stok = $conn->prepare("UPDATE buku SET stok = stok - ? WHERE id_buku = ?");
                    $stmt_stok->bind_param("ii", $jumlah, $id_buku);
                    $stmt_stok->execute();
                    $stmt_stok->close();
                }
            }
        }

        echo "success";
    } else {
        echo "failed_db";
        $stmt->close();
    }

// 3. ACTION: RIWAYAT TRANSAKSI
} elseif ($action == 'riwayat') {
    $checkTable = $conn->query("SHOW TABLES LIKE 'transaksi'");
    if ($checkTable->num_rows == 0) {
        echo json_encode([]);
        exit();
    }

    $result = $conn->query("SELECT * FROM transaksi ORDER BY id_transaksi DESC");
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $item = "#" . $row['id_transaksi'] . " | " . $row['tanggal'] . " | Total: Rp " . number_format($row['total'], 0, ',', '.') . " | " . $row['metode'];
        $data[] = $item;
    }

    echo json_encode($data);

} else {
    echo json_encode(["status" => "invalid_action"]);
}

$conn->close();
?>