<?php
// File: controllers/TransaksiController.php

class TransaksiController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Mendapatkan semua transaksi dengan filter
    public function getTransaksi($filter = []) {
        $query = "
            SELECT 
                p.id_pembelian,
                u.nama_user,
                pj.nama_penjual,
                p.total_harga,
                p.status,
                p.metode_pembayaran,
                p.tanggal_pembelian,
                p.waktu_selesai,
                p.status_pesanan,
                p.catatan
            FROM 
                pembelian p
                JOIN user u ON p.id_user = u.id_user
                JOIN penjual pj ON p.id_penjual = pj.id_penjual
            WHERE 1=1
        ";
        
        // Add filters
        if (!empty($filter['status'])) {
            $query .= " AND p.status = :status";
        }
        if (!empty($filter['date_start'])) {
            $query .= " AND p.tanggal_pembelian >= :date_start";
        }
        if (!empty($filter['date_end'])) {
            $query .= " AND p.tanggal_pembelian <= :date_end";
        }
        
        $query .= " ORDER BY p.tanggal_pembelian DESC";
        
        return $query;
    }
    
    // Mendapatkan detail transaksi
    public function getDetailTransaksi($id_pembelian) {
        $query = "
            SELECT 
                dp.*,
                pr.nama_produk,
                pr.deskripsi_produk
            FROM 
                detail_pembelian dp
                JOIN produk pr ON dp.id_produk = pr.id_produk
            WHERE 
                dp.id_pembelian = :id_pembelian
        ";
        
        return $query;
    }
    
    // Generate laporan penjualan per penjual
    public function getLaporanPenjual($tanggal_awal, $tanggal_akhir) {
        $query = "
            SELECT 
                pj.nama_penjual,
                COUNT(p.id_pembelian) as total_transaksi,
                SUM(p.total_harga) as total_pendapatan,
                COUNT(CASE WHEN p.status = 'completed' THEN 1 END) as transaksi_sukses,
                COUNT(CASE WHEN p.status = 'cancelled' THEN 1 END) as transaksi_batal
            FROM 
                penjual pj
                LEFT JOIN pembelian p ON pj.id_penjual = p.id_penjual
                AND p.tanggal_pembelian BETWEEN :tanggal_awal AND :tanggal_akhir
            GROUP BY 
                pj.id_penjual, pj.nama_penjual
            ORDER BY 
                total_pendapatan DESC
        ";
        
        return $query;
    }
    
    // Generate laporan produk terlaris
    public function getTopProducts($limit = 10) {
        $query = "
            SELECT 
                pr.nama_produk,
                pr.deskripsi_produk,
                pj.nama_penjual,
                SUM(dp.jumlah) as total_terjual,
                SUM(dp.subtotal) as total_pendapatan
            FROM 
                produk pr
                JOIN detail_pembelian dp ON pr.id_produk = dp.id_produk
                JOIN pembelian p ON dp.id_pembelian = p.id_pembelian
                JOIN penjual pj ON pr.id_penjual = pj.id_penjual
            WHERE 
                p.status = 'completed'
            GROUP BY 
                pr.id_produk, pr.nama_produk, pr.deskripsi_produk, pj.nama_penjual
            ORDER BY 
                total_terjual DESC
            LIMIT :limit
        ";
        
        return $query;
    }
}
?>

<!-- File: views/transaksi/index.php -->
<div class="container">
    <h2>Monitoring Transaksi</h2>
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="date_start" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="date_end" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Transaction Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pembeli</th>
                            <th>Penjual</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Metode Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Status Pesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaksi as $t): ?>
                        <tr>
                            <td><?= $t['id_pembelian'] ?></td>
                            <td><?= $t['nama_user'] ?></td>
                            <td><?= $t['nama_penjual'] ?></td>
                            <td>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($t['status']) ?>">
                                    <?= $t['status'] ?>
                                </span>
                            </td>
                            <td><?= $t['metode_pembayaran'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['tanggal_pembelian'])) ?></td>
                            <td>
                                <span class="badge bg-<?= getOrderStatusColor($t['status_pesanan']) ?>">
                                    <?= $t['status_pesanan'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="detail.php?id=<?= $t['id_pembelian'] ?>" 
                                   class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- File: views/transaksi/detail.php -->
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Detail Transaksi #<?= $transaksi['id_pembelian'] ?></h4>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Informasi Pembeli</h5>
                    <p>Nama: <?= $transaksi['nama_user'] ?></p>
                    <p>Tanggal: <?= date('d/m/Y H:i', strtotime($transaksi['tanggal_pembelian'])) ?></p>
                    <p>Status: <?= $transaksi['status'] ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Penjual</h5>
                    <p>Nama: <?= $transaksi['nama_penjual'] ?></p>
                    <p>Metode Pembayaran: <?= $transaksi['metode_pembayaran'] ?></p>
                    <p>Status Pesanan: <?= $transaksi['status_pesanan'] ?></p>
                </div>
            </div>
            
            <h5>Detail Produk</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail_transaksi as $detail): ?>
                        <tr>
                            <td><?= $detail['nama_produk'] ?></td>
                            <td>Rp <?= number_format($detail['harga_satuan'], 0, ',', '.') ?></td>
                            <td><?= $detail['jumlah'] ?></td>
                            <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td><strong>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <?php if ($transaksi['catatan']): ?>
            <div class="mt-4">
                <h5>Catatan</h5>
                <p><?= $transaksi['catatan'] ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>