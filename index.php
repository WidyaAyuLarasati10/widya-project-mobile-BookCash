<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';

// PROSES HAPUS TRANSAKSI
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM detail_transaksi WHERE id_transaksi = $id_hapus");
    $conn->query("DELETE FROM transaksi WHERE id_transaksi = $id_hapus");
    header("Location: index.php");
    exit();
}

// HITUNG TOTAL JENIS BUKU
$qBuku = $conn->query("SELECT COUNT(*) AS total_buku FROM buku");
$d1 = $qBuku->fetch_assoc();
$totalBuku = $d1['total_buku'];

// AMBIL PARAMETER FILTER TANGGAL
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';

$where_sql = "";
if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    $where_sql = " WHERE DATE(tanggal) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
}

// HITUNG TOTAL TRANSAKSI & PENDAPATAN
$qTransaksi = $conn->query("SELECT COUNT(*) AS total_transaksi, SUM(total) AS total_pendapatan FROM transaksi" . $where_sql);
$d2 = $qTransaksi->fetch_assoc();
$totalTransaksi = $d2['total_transaksi'];
$totalPendapatan = $d2['total_pendapatan'] ? $d2['total_pendapatan'] : 0;

// QUERY TRANSAKSI UTAMA
$query = "SELECT * FROM transaksi" . $where_sql . " ORDER BY id_transaksi DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookCash POS - Pink Aesthetic Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pink-bg: #fff0f5;
            --pink-card: #ffffff;
            --pink-primary: #ec4899;
            --pink-hover: #db2777;
            --pink-light: #fce7f3;
            --pink-dark: #831843;
            --pink-gradient: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
            --purple-gradient: linear-gradient(135deg, #c084fc 0%, #a855f7 100%);
            --peach-gradient: linear-gradient(135deg, #fb7185 0%, #f43f5e 100%);
            --text-dark: #4a044e;
            --border-pink: #fbcfe8;
        }

        body { 
            background-color: var(--pink-bg); 
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        
        .navbar-custom { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--border-pink);
        }

        .card-custom { 
            background: var(--pink-card); 
            border: 1px solid var(--border-pink); 
            border-radius: 20px; 
            box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.08);
            transition: all 0.3s ease;
        }
        
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -5px rgba(236, 72, 153, 0.15);
        }

        .icon-shape {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(236, 72, 153, 0.25);
        }

        .table-custom {
            color: #701a75;
        }

        .table-custom th { 
            background-color: #fdf2f8; 
            color: #a21caf; 
            font-weight: 700; 
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.8px;
            border-bottom: 2px solid var(--border-pink); 
            padding: 16px;
        }

        .table-custom td {
            border-bottom: 1px solid #fce7f3;
            padding: 16px;
        }

        .badge-trx {
            background: var(--pink-light);
            color: #be185d;
            border: 1px solid var(--border-pink);
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 12px;
        }

        .form-control-pink {
            background-color: #fff;
            border: 1.5px solid var(--border-pink);
            color: #701a75;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .form-control-pink:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.15);
        }

        .btn-pink-primary {
            background: var(--pink-gradient);
            border: none;
            color: white;
            font-weight: 700;
            box-shadow: 0 6px 18px rgba(236, 72, 153, 0.3);
            border-radius: 12px;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .btn-pink-primary:hover {
            background: linear-gradient(135deg, #e879f9 0%, #db2777 100%);
            color: white;
            transform: translateY(-2px);
        }

        .btn-action-detail {
            background: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
            border-radius: 10px;
            font-weight: 600;
            padding: 6px 14px;
            transition: 0.2s;
        }

        .btn-action-detail:hover {
            background: var(--pink-primary);
            color: white;
        }

        .btn-action-delete {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            border-radius: 10px;
            font-weight: 600;
            padding: 6px 14px;
            transition: 0.2s;
        }

        .btn-action-delete:hover {
            background: #e11d48;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Navbar Minimalis Pink Aesthetic -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top px-4 py-3 mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="#">
                <div class="p-2 rounded-3 me-2 text-white shadow-sm" style="background: var(--pink-gradient);">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <span class="fs-4" style="color: #701a75;">BookCash <span class="fw-normal text-pink" style="color: var(--pink-primary);">POS</span></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="small px-3 py-2 rounded-pill fw-semibold" style="background: var(--pink-light); color: #be185d; border: 1px solid var(--border-pink);">
                    <i class="fa-regular fa-calendar-heart me-2"></i> <?= date('d M Y'); ?>
                </span>
                <a href="login.php" class="btn btn-outline-danger btn-sm px-3 py-2 rounded-pill fw-bold border-2">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">
        <!-- STATISTIK UTAMA -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase fw-bold small" style="color: #a21caf;">Katalog Buku</span>
                            <h2 class="fw-extrabold mt-2 mb-0 display-6" style="color: #701a75;"><?= $totalBuku; ?></h2>
                        </div>
                        <div class="icon-shape" style="background: var(--pink-gradient);">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase fw-bold small" style="color: #a21caf;">Total Transaksi</span>
                            <h2 class="fw-extrabold mt-2 mb-0 display-6" style="color: #701a75;"><?= $totalTransaksi; ?></h2>
                        </div>
                        <div class="icon-shape" style="background: var(--purple-gradient);">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase fw-bold small" style="color: #a21caf;">Total Pendapatan</span>
                            <h2 class="fw-extrabold mt-2 mb-0 display-6" style="color: #be185d;">Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></h2>
                        </div>
                        <div class="icon-shape" style="background: var(--peach-gradient);">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL FILTER TANGGAL -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-sparkles me-2" style="color: var(--pink-primary);"></i>
                <h6 class="fw-bold m-0" style="color: #701a75;">Filter Laporan Penjualan</h6>
            </div>
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold" style="color: #a21caf;">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control form-control-pink" value="<?= $tanggal_mulai; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold" style="color: #a21caf;">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" class="form-control form-control-pink" value="<?= $tanggal_selesai; ?>" required>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-pink-primary w-100">
                        <i class="fa-solid fa-filter me-1"></i> Terapkan
                    </button>
                    <a href="index.php" class="btn btn-light border-2 px-3 py-2 rounded-3" style="border-color: var(--border-pink); color: #be185d;">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- TABEL DATA -->
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold m-0" style="color: #701a75;">Riwayat Transaksi Kasir</h4>
                    <small style="color: #a21caf;">Catatan riwayat penjualan dari aplikasi kasir</small>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-danger btn-sm px-3 py-2 fw-semibold rounded-3">
                        <i class="fa-solid fa-print me-1"></i> Cetak
                    </button>
                    <a href="export.php" class="btn btn-success btn-sm px-3 py-2 fw-semibold rounded-3">
                        <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu & Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Total Belanja</th>
                            <th>Uang Bayar</th>
                            <th>Kembalian</th>
                            <th>Kasir</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rows = [];
                        if ($result->num_rows > 0) {
                            while ($r = $result->fetch_assoc()) {
                                $rows[] = $r;
                            }
                        }
                        ?>

                        <?php if (count($rows) > 0) : ?>
                            <?php $no = 1; foreach ($rows as $row) : ?>
                            <tr>
                                <td class="fw-bold" style="color: #a21caf;"><?= $no++; ?></td>
                                <td class="fw-medium"><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                <td>
                                    <span class="badge-trx">
                                        TRX-<?= date('Ymd', strtotime($row['tanggal'])) . '-' . sprintf('%04d', $row['id_transaksi']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold" style="color: #be185d;">Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                                <td>Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></td>
                                <td style="color: #059669;" class="fw-bold">Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?></td>
                                <td><span class="badge px-3 py-2 rounded-pill" style="background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8;">Kasir</span></td>
                                <td class="text-center">
                                    <button class="btn btn-action-detail me-1" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $row['id_transaksi']; ?>">
                                        <i class="fa-solid fa-eye me-1"></i> Detail
                                    </button>
                                    <a href="index.php?hapus=<?= $row['id_transaksi']; ?>" class="btn btn-action-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center py-5" style="color: #a21caf;">
                                    <i class="fa-solid fa-heart-crack fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada data transaksi yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL TRANSAKSI PINK -->
    <?php foreach ($rows as $row) : ?>
    <div class="modal fade" id="modalDetail<?= $row['id_transaksi']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header text-white p-4" style="background: var(--pink-gradient);">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-receipt me-2"></i> Rincian Transaksi #<?= $row['id_transaksi']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #fff0f5;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #a21caf;">Waktu Transaksi:</span>
                        <span class="fw-bold" style="color: #701a75;"><?= $row['tanggal']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="color: #a21caf;">Metode Pembayaran:</span>
                        <span class="badge px-3 py-1" style="background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8;"><?= isset($row['metode']) ? $row['metode'] : 'Tunai'; ?></span>
                    </div>
                    <hr style="border-color: #fbcfe8;">
                    <p class="fw-bold mb-2" style="color: #701a75;">Item Buku yang Dibeli:</p>
                    <div class="p-3 rounded-4 mb-3 bg-white" style="border: 1.5px solid #fbcfe8;">
                        <?php
                        $id_transaksi = $row['id_transaksi'];
                        $qDetail = $conn->query("SELECT dt.*, b.judul 
                                                 FROM detail_transaksi dt 
                                                 JOIN buku b ON dt.id_buku = b.id_buku 
                                                 WHERE dt.id_transaksi = '$id_transaksi'");

                        if ($qDetail && $qDetail->num_rows > 0) {
                            while ($d = $qDetail->fetch_assoc()) {
                                echo "<div class='d-flex justify-content-between border-bottom pb-2 mb-2' style='border-color: #fce7f3 !important;'>";
                                echo "<span>• " . htmlspecialchars($d['judul']) . " <span style='color: #be185d;'>(" . $d['jumlah'] . "x)</span></span>";
                                echo "<span class='fw-bold' style='color: #701a75;'>Rp " . number_format($d['subtotal'], 0, ',', '.') . "</span>";
                                echo "</div>";
                            }
                        } else {
                            echo '<span style="color: #a21caf;">Belum ada rincian item buku di database.</span>';
                        }
                        ?>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #a21caf;">Total Tagihan:</span>
                        <strong class="fs-5" style="color: #be185d;">Rp <?= number_format($row['total'], 0, ',', '.'); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #a21caf;">Nominal Bayar:</span>
                        <span class="fw-semibold">Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: #a21caf;">Kembalian:</span>
                        <span style="color: #059669;" class="fw-bold">Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-white border-0 justify-content-center">
                    <button type="button" class="btn btn-pink-primary px-5 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>