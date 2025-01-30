<?php
session_start();
include '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Receive JSON data from the request
$input = json_decode(file_get_contents('php://input'), true);

// Response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['id_user'])) {
        throw new Exception('Silakan login terlebih dahulu');
    }

    $id_user = $_SESSION['id_user'];
    
    // Start a database transaction
    $conn->begin_transaction();

    // Validate input
    if (!isset($input['items']) || empty($input['items'])) {
        throw new Exception('Data pesanan tidak valid');
    }

    // Calculate total order price
    $total_harga = 0;
    foreach ($input['items'] as $item) {
        $total_harga += $item['harga'] * $item['quantity'];
    }

    // Check user's balance
    $balance_query = "SELECT saldo FROM user WHERE id_user = ?";
    $balance_stmt = $conn->prepare($balance_query);
    $balance_stmt->bind_param('i', $id_user);
    $balance_stmt->execute();
    $balance_result = $balance_stmt->get_result();
    $user = $balance_result->fetch_assoc();

    if ($user['saldo'] < $total_harga) {
        throw new Exception('Saldo tidak mencukupi untuk melakukan transaksi');
    }

    // Subtract balance from user
    $update_balance_query = "UPDATE user SET saldo = saldo - ? WHERE id_user = ?";
    $update_balance_stmt = $conn->prepare($update_balance_query);
    $update_balance_stmt->bind_param('di', $total_harga, $id_user);
    $update_balance_stmt->execute();

    // Group items by seller
    $seller_items = [];
    foreach ($input['items'] as $item) {
        // Get seller ID for each product
        $seller_query = "SELECT id_penjual FROM produk WHERE id_produk = ?";
        $seller_stmt = $conn->prepare($seller_query);
        $seller_stmt->bind_param('i', $item['id_produk']);
        $seller_stmt->execute();
        $seller_result = $seller_stmt->get_result();
        $seller = $seller_result->fetch_assoc();
        $id_penjual = $seller['id_penjual'];

        // Group items by seller
        if (!isset($seller_items[$id_penjual])) {
            $seller_items[$id_penjual] = [];
        }
        $seller_items[$id_penjual][] = $item;
    }

    // Process orders for each seller
    foreach ($seller_items as $id_penjual => $items) {
        $seller_total = 0;
        foreach ($items as $item) {
            $seller_total += $item['harga'] * $item['quantity'];
        }

        // Create order for seller
        $order_query = "INSERT INTO pembelian (id_user, id_penjual, total_harga, status, metode_pembayaran, 
                        tanggal_pembelian, catatan, status_pesanan) 
                        VALUES (?, ?, ?, 'completed', ?, NOW(), ?, 'menunggu')";
        $order_stmt = $conn->prepare($order_query);
        $payment_method = $input['metode_pembayaran'] ?? 'Saldo';
        $notes = $input['catatan'] ?? '';
        $order_stmt->bind_param('iidss', $id_user, $id_penjual, $seller_total, $payment_method, $notes);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Add seller's earnings
        $update_seller_saldo = "UPDATE penjual SET saldo = saldo + ? WHERE id_penjual = ?";
        $seller_saldo_stmt = $conn->prepare($update_seller_saldo);
        $seller_saldo_stmt->bind_param('di', $seller_total, $id_penjual);
        $seller_saldo_stmt->execute();

        // Insert order items and update product stock
        $item_query = "INSERT INTO detail_pembelian (id_pembelian, id_produk, jumlah, harga_satuan, subtotal) 
                       VALUES (?, ?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_query);

        $update_stock_query = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
        $update_stock_stmt = $conn->prepare($update_stock_query);

        foreach ($items as $item) {
            $subtotal = $item['harga'] * $item['quantity'];
            
            // Insert order item
            $item_stmt->bind_param('iiidd', 
                $order_id, 
                $item['id_produk'], 
                $item['quantity'], 
                $item['harga'],
                $subtotal
            );
            $item_stmt->execute();

            // Update product stock
            $update_stock_stmt->bind_param('ii', 
                $item['quantity'], 
                $item['id_produk']
            );
            $update_stock_stmt->execute();
        }
    }

    // Commit transaction
    $conn->commit();

    // Prepare success response
    $response['success'] = true;
    $response['message'] = 'Pesanan berhasil dibuat';

} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();

    // Set error response
    $response['message'] = $e->getMessage();
} finally {
    // Close all statements and connection
    if (isset($balance_stmt)) $balance_stmt->close();
    if (isset($update_balance_stmt)) $update_balance_stmt->close();
    if (isset($seller_stmt)) $seller_stmt->close();
    if (isset($order_stmt)) $order_stmt->close();
    if (isset($seller_saldo_stmt)) $seller_saldo_stmt->close();
    if (isset($item_stmt)) $item_stmt->close();
    if (isset($update_stock_stmt)) $update_stock_stmt->close();
    $conn->close();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>