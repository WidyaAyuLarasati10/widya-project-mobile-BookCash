<?php
// Header untuk memberitahu browser bahwa file ini diunduh sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Transaksi_BookCash.xls");

require_once 'koneksi.php';

$query = "SELECT t.*, 
          GROUP_CONCAT(CONCAT('- ', b.judul, ' (', dt.jumlah, 'x)') SEPARATOR '<br>') AS detail_buku 
          FROM transaksi t 
          LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi 
          LEFT JOIN buku b ON dt.id_buku = b.id_buku 
          GROUP BY t.id_transaksi 
          ORDER BY t.id_transaksi DESC";

$result = $conn->query($query);
?>

<h3 style="text-align: center;">Laporan Rekapitulasi Transaksi BookCash</h3>
<table border="1">
    <thead>
        <tr style="background-color: #17a2b8; color: white;">
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Detail Buku</th>
            <th>Total Belanja</th>
            <th>Bayar</th>
            <th>Kembalian</th>
            <th>Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td>#<?= $row['id_transaksi']; ?></td>
                <td><?= $row['tanggal']; ?></td>
                <td><?= $row['detail_buku'] ? strip_tags($row['detail_buku']) : '-'; ?></td>
                <td><?= $row['total']; ?></td>
                <td><?= $row['bayar']; ?></td>
                <td><?= $row['kembalian']; ?></td>
                <td><?= $row['metode']; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada data transaksi.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>