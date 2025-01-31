<?php
session_start();
include '../config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login_pembeli.php");
    exit();
}

// Fetch user data - using prepared statement to prevent SQL injection
$username = $_SESSION['username'];
$user_query = mysqli_prepare($conn, "SELECT id_user, username, saldo FROM user WHERE username = ?");
mysqli_stmt_bind_param($user_query, "s", $username);
mysqli_stmt_execute($user_query);
$result = mysqli_stmt_get_result($user_query);
$user_data = mysqli_fetch_assoc($result);

// If user data not found, redirect to login
if (!$user_data) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$_SESSION['id_user'] = $user_data['id_user']; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembelian - E-Canteen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .search-input {
            max-width: 250px;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
         <!-- User Info Section -->
         <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-user text-blue-500 text-xl mr-2"></i>
                    <div>
                        <p class="font-semibold">Selamat datang, <?php echo htmlspecialchars($user_data['username']); ?></p>
                        <p class="text-gray-600">Saldo: Rp <?php echo number_format($user_data['saldo'], 0, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="toggleHistoryModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-history mr-1"></i> Riwayat
                    </button>
                    <a href="logout_pembelian.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">Daftar Produk E-Kantin</h1>
            <div class="mb-6 flex justify-between items-center">
                <div class="flex items-center">
                    <select id="sellerFilter" class="px-4 py-2 border rounded-lg mr-4">
                        <option value="">Semua Penjual</option>
                        <?php
                        $seller_query = mysqli_query($conn, "SELECT DISTINCT nama_penjual FROM penjual");
                        while ($seller = mysqli_fetch_assoc($seller_query)) {
                            echo "<option value='{$seller['nama_penjual']}'>{$seller['nama_penjual']}</option>";
                        }
                        ?>
                    </select>
                    <input type="text" id="searchInput" placeholder="Cari Produk..." 
                        class="search-input px-4 py-2 border rounded-lg">
                </div>
                <button id="cartButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full px-2 py-1 text-xs">0</span>
                </button>
            </div>

            <div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $query = "SELECT p.*, pj.nama_penjual 
                          FROM produk p 
                          JOIN penjual pj ON p.id_penjual = pj.id_penjual 
                          WHERE p.stok > 0 
                          ORDER BY p.id_produk DESC";
                $result = mysqli_query($conn, $query);

                while ($produk = mysqli_fetch_assoc($result)): 
                    // Convert the product data to a JSON string for use in JavaScript
                    $produkJson = json_encode($produk);
                ?>
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden" 
                         data-seller="<?php echo htmlspecialchars($produk['nama_penjual']); ?>"
                         data-product-id="<?php echo $produk['id_produk']; ?>">
                        <?php if (!empty($produk['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($produk['image']); ?>" 
                                alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" 
                                class="product-image"
                                onerror="this.src='../uploads/default.jpg'">
                        <?php else: ?>
                            <img src="../uploads/default.jpg" 
                                alt="Default Image" 
                                class="product-image">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                            <p class="text-gray-600 mb-1">Penjual: <?php echo htmlspecialchars($produk['nama_penjual']); ?></p>
                            <p class="text-blue-600 font-semibold mb-1">
                                Harga: Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                            </p>
                            <p class="text-green-600 mb-4">Stok: <?php echo $produk['stok']; ?></p>
                            
                            <div class="flex items-center">
                                <button onclick="decreaseQuantity(<?php echo $produk['id_produk']; ?>)" 
                                    class="bg-gray-200 px-2 py-1 rounded-l">-</button>
                                <input type="number" 
                                    id="quantity-<?php echo $produk['id_produk']; ?>" 
                                    value="1" 
                                    min="1" 
                                    max="<?php echo $produk['stok']; ?>" 
                                    class="w-12 text-center border"
                                    onchange="validateQuantity(<?php echo $produk['id_produk']; ?>)">
                                <button onclick="increaseQuantity(<?php echo $produk['id_produk']; ?>)" 
                                    class="bg-gray-200 px-2 py-1 rounded-r">+</button>
                                <button onclick='addToCart(<?php echo $produkJson; ?>)' 
                                    class="ml-2 w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Cart Modal -->
        <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center">
            <div class="bg-white w-full max-w-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Keranjang Anda</h3>
                    <button onclick="toggleCartModal()" class="text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="cartItems" class="space-y-4 max-h-64 overflow-y-auto">
                    <!-- Cart items will be dynamically added here -->
                </div>
                <div class="mt-4">
                    <div class="mb-4">
                        <label for="orderNotes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Pesanan:</label>
                        <textarea id="orderNotes" class="w-full px-3 py-2 border rounded-lg resize-none" rows="2"></textarea>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>Total:</span>
                        <span id="cartTotal">Rp 0</span>
                    </div>
                    <button onclick="checkout()" class="w-full bg-green-500 text-white py-2 rounded mt-4 hover:bg-green-600">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center">
            <div class="bg-white w-full max-w-3xl rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Riwayat Pembelian</h3>
                    <button onclick="toggleHistoryModal()" class="text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="historyContent" class="space-y-4 max-h-96 overflow-y-auto">
                    <!-- History items will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>

    <script>
    // Initialize cart from localStorage or empty array if none exists
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function updateCartUI() {
        const cartItems = document.getElementById('cartItems');
        const cartCount = document.getElementById('cartCount');
        const cartTotal = document.getElementById('cartTotal');
        
        // Save cart to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        cartItems.innerHTML = '';
        
        let total = 0;
        cart.forEach((item, index) => {
            const itemTotal = item.harga * item.quantity;
            total += itemTotal;
            
            const itemElement = document.createElement('div');
            itemElement.className = 'flex justify-between items-center p-2 border-b';
            itemElement.innerHTML = `
                <div class="flex-1">
                    <div class="font-semibold">${item.nama_produk}</div>
                    <div class="text-sm text-gray-600">${item.quantity} x Rp ${item.harga.toLocaleString()}</div>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold">Rp ${itemTotal.toLocaleString()}</span>
                    <button onclick="removeFromCart(${index})" class="ml-2 text-red-500">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            cartItems.appendChild(itemElement);
        });

        cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartTotal.textContent = `Rp ${total.toLocaleString()}`;
    }

    function addToCart(product) {
        const quantityInput = document.getElementById(`quantity-${product.id_produk}`);
        const quantity = parseInt(quantityInput.value);

        const existingProductIndex = cart.findIndex(item => item.id_produk === product.id_produk);
        
        if (existingProductIndex > -1) {
            const newQuantity = cart[existingProductIndex].quantity + quantity;
            if (newQuantity <= product.stok) {
                cart[existingProductIndex].quantity = newQuantity;
            } else {
                alert('Stok produk tidak mencukupi');
                return;
            }
        } else {
            if (quantity <= product.stok) {
                cart.push({...product, quantity: quantity});
            } else {
                alert('Stok produk tidak mencukupi');
                return;
            }
        }

        updateCartUI();
        quantityInput.value = 1;
        alert('Produk berhasil ditambahkan ke keranjang');
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function toggleCartModal() {
        const cartModal = document.getElementById('cartModal');
        cartModal.classList.toggle('hidden');
        cartModal.classList.toggle('flex');
    }

    function checkout() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong');
            return;
        }

        const orderNotes = document.getElementById('orderNotes').value;

        const itemsBySeller = cart.reduce((acc, item) => {
            const sellerId = item.id_penjual;
            if (!acc[sellerId]) {
                acc[sellerId] = [];
            }
            acc[sellerId].push(item);
            return acc;
        }, {});

        const orderData = {
            items: cart.map(item => ({
                id_produk: item.id_produk,
                quantity: item.quantity,
                harga: item.harga,
                id_penjual: item.id_penjual
            })),
            metode_pembayaran: 'Saldo',
            catatan: orderNotes
        };

        const totalAmount = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);

        const confirmMessage = `Detail Pesanan:
        Total Pembayaran: Rp ${totalAmount.toLocaleString()}
        Catatan: ${orderNotes || '(Tidak ada catatan)'}

        Lanjutkan pembelian?`;

        if (!confirm(confirmMessage)) {
            return;
        }

        fetch('order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Pesanan berhasil dibuat!');
                // Clear cart from localStorage
                localStorage.removeItem('cart');
                cart = [];
                updateCartUI();
                toggleCartModal();
                window.location.reload();
            } else {
                alert('Gagal membuat pesanan: ' + result.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat membuat pesanan');
            console.error('Error:', error);
        });
    }

    function toggleHistoryModal() {
        const historyModal = document.getElementById('historyModal');
        historyModal.classList.toggle('hidden');
        historyModal.classList.toggle('flex');
        
        if (!historyModal.classList.contains('hidden')) {
            loadPurchaseHistory();
        }
    }

    function loadPurchaseHistory() {
        fetch('history_pembelian.php')
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data);
                const historyContent = document.getElementById('historyContent');
                historyContent.innerHTML = '';
                
                if (data.length === 0) {
                    historyContent.innerHTML = '<p class="text-center text-gray-500">Belum ada riwayat pembelian</p>';
                    return;
                }

                data.forEach(order => {
                    const orderElement = document.createElement('div');
                    orderElement.className = 'bg-gray-50 rounded-lg p-4 mb-4';
                    
                    let itemsHtml = '';
                    if (Array.isArray(order.items)) {
                        order.items.forEach(item => {
                            itemsHtml += `
                                <div class="flex justify-between items-center py-2">
                                    <span>${item.nama_produk} (${item.jumlah}x)</span>
                                    <span>Rp ${parseInt(item.harga_satuan).toLocaleString()}</span>
                                </div>
                            `;
                        });
                    }
                    
                    orderElement.innerHTML = `
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <span class="font-semibold">Pembelian #${order.id_pembelian}</span>
                                <span class="text-sm text-gray-500 ml-2">${order.tanggal_pembelian}</span>
                            </div>
                            <span class="px-2 py-1 rounded ${order.status === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${order.status}
                            </span>
                        </div>
                        <div class="border-t border-gray-200 mt-2 pt-2">
                            ${itemsHtml}
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                            <span class="font-semibold">Total:</span>
                            <span class="font-semibold">Rp ${parseInt(order.total_harga).toLocaleString()}</span>
                        </div>
                        ${order.catatan ? `<div class="mt-2 text-sm text-gray-600">Catatan: ${order.catatan}</div>` : ''}
                    `;
                        
                    historyContent.appendChild(orderElement);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                const historyContent = document.getElementById('historyContent');
                historyContent.innerHTML = '<p class="text-center text-red-500">Gagal memuat riwayat pembelian</p>';
            });
    }

    // Quantity controls
    function increaseQuantity(productId) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        const currentQuantity = parseInt(quantityInput.value);
        const maxQuantity = parseInt(quantityInput.max);
        
        if (currentQuantity < maxQuantity) {
            quantityInput.value = currentQuantity + 1;
        }
    }

    function decreaseQuantity(productId) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        const currentQuantity = parseInt(quantityInput.value);
        
        if (currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
        }
    }

    function validateQuantity(productId) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        let currentQuantity = parseInt(quantityInput.value);
        const maxQuantity = parseInt(quantityInput.max);
        
        if (isNaN(currentQuantity) || currentQuantity < 1) {
            currentQuantity = 1;
        } else if (currentQuantity > maxQuantity) {
            currentQuantity = maxQuantity;
        }
        
        quantityInput.value = currentQuantity;
    }

    // Filter functionality
    const productCards = document.querySelectorAll('.product-card');

    function filterProducts() {
        const sellerFilter = document.getElementById('sellerFilter').value.toLowerCase();
        const searchInput = document.getElementById('searchInput').value.toLowerCase();

        productCards.forEach(card => {
            const sellerName = card.getAttribute('data-seller').toLowerCase();
            const productName = card.querySelector('.text-lg').textContent.toLowerCase();

            const sellerMatch = sellerFilter === '' || sellerName === sellerFilter;
            const searchMatch = productName.includes(searchInput) || sellerName.includes(searchInput);

            card.style.display = (sellerMatch && searchMatch) ? 'block' : 'none';
        });
    }

    // Event listeners
    document.getElementById('sellerFilter').addEventListener('change', filterProducts);
    document.getElementById('searchInput').addEventListener('input', filterProducts);
    document.getElementById('cartButton').addEventListener('click', toggleCartModal);

    // Initialize cart UI when page loads
    document.addEventListener('DOMContentLoaded', () => {
        updateCartUI();
    });
    </script>
</body>
</html>