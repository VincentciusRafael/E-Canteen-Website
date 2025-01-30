<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id_user = $_SESSION['id_user'];

try {
    $query = "
        SELECT 
            p.id_pembelian,
            p.tanggal_pembelian,
            p.total_harga,
            p.status,
            p.catatan
        FROM pembelian p
        WHERE p.id_user = ?
        ORDER BY p.tanggal_pembelian DESC
    ";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_user);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Query execution failed: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $orders = [];

    while ($order = mysqli_fetch_assoc($result)) {
        // Get items for each order
        $items_query = "
            SELECT 
                dp.jumlah,
                dp.harga_satuan,
                p.nama_produk
            FROM detail_pembelian dp
            JOIN produk p ON dp.id_produk = p.id_produk
            WHERE dp.id_pembelian = ?
        ";
        
        $items_stmt = mysqli_prepare($conn, $items_query);
        mysqli_stmt_bind_param($items_stmt, "i", $order['id_pembelian']); // Changed from id_order to id_pembelian
        mysqli_stmt_execute($items_stmt);
        $items_result = mysqli_stmt_get_result($items_stmt);
        
        $order['items'] = [];
        while ($item = mysqli_fetch_assoc($items_result)) {
            $order['items'][] = $item;
        }
        
        $orders[] = $order;
    }

    echo json_encode($orders);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>