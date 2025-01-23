<?php
// get_products.php
include '../config.php';

if (isset($_GET['seller_id'])) {
    $seller_id = mysqli_real_escape_string($conn, $_GET['seller_id']);
    
    $query = mysqli_query($conn, "SELECT * FROM produk WHERE id_penjual = '$seller_id'");
    
    while ($product = mysqli_fetch_assoc($query)) {
        ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 overflow-hidden">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['nama_produk']; ?>" 
                     class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800"><?php echo $product['nama_produk']; ?></h3>
                <p class="text-gray-600">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                <p class="text-gray-600">Stok: <?php echo $product['stok']; ?></p>
                
                <div class="flex justify-end space-x-2 mt-4">
                    <button onclick="openEditModal(
                        '<?php echo $product['id_produk']; ?>', 
                        '<?php echo htmlspecialchars($product['nama_produk'], ENT_QUOTES); ?>',
                        '<?php echo $product['id_penjual']; ?>', 
                        '<?php echo htmlspecialchars($product['deskripsi_produk'], ENT_QUOTES); ?>', 
                        '<?php echo $product['harga']; ?>', 
                        '<?php echo $product['stok']; ?>',
                        '<?php echo $product['image']; ?>')"
                        class="text-yellow-600 hover:bg-yellow-100 p-2 rounded-full">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="openDeleteModal('<?php echo $product['id_produk']; ?>')"
                        class="text-red-600 hover:bg-red-100 p-2 rounded-full">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
?>