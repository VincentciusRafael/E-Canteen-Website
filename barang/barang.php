<?php
session_start();
include '../config.php';

if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}

// Logika untuk menghapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete_query = "DELETE FROM produk WHERE id_produk = '$id'";  // Gunakan $id yang sudah diambil dari $_GET
    if (mysqli_query($conn, $delete_query)) {
        // Gunakan session untuk menandai sukses
        $_SESSION['delete_success'] = true;
        header("Location: barang.php");
        exit();
    } else {
        echo "<script>
                alert('Gagal menghapus barang');
                window.location.href = 'barang.php';
              </script>";
    }
}

// Tambahkan di bagian awal setelah session_start()
if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
    echo "<script>
            window.onload = function() {
                openSuccessModal();
            }
          </script>";
    unset($_SESSION['delete_success']); // Hapus session setelah digunakan
}

// Handle add product
if (isset($_POST['add_produk'])) {
    $id_penjual = mysqli_real_escape_string($conn, $_POST['id_penjual']);
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($conn, $_POST['deskripsi_produk']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        list($uploadSuccess, $uploadResult) = handleFileUpload($_FILES['image']);
        
        if ($uploadSuccess) {
            $image_path = $uploadResult;
            
            // Insert query with image
            $query = "INSERT INTO produk (id_penjual, nama_produk, deskripsi_produk, harga, stok, image) 
                     VALUES ('$id_penjual', '$nama_produk', '$deskripsi_produk', '$harga', '$stok', '$image_path')";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['add_success'] = true;
                header("Location: barang.php");
                exit();
            } else {
                echo "<script>
                        alert('Gagal menambahkan produk: " . mysqli_error($conn) . "');
                        window.location.href = 'barang.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('$uploadResult');
                    window.location.href = 'barang.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Silakan pilih gambar untuk produk');
                window.location.href = 'barang.php';
              </script>";
    }
}
// Tambahkan ini setelah bagian session_start() dan include

function handleFileUpload($file) {
    // Cek apakah direktori uploads ada, jika tidak buat direktori
    $uploadDir = __DIR__ . '/../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Validasi file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return [false, "Tipe file tidak didukung. Harap upload file JPG, PNG, atau GIF."];
    }

    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $newFileName;

    // Coba pindahkan file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [true, '../uploads/' . $newFileName];
    } else {
        return [false, "Gagal mengupload file. Silakan coba lagi."];
    }
}

// Modifikasi bagian edit produk
if (isset($_POST['edit'])) {
    $id_produk = mysqli_real_escape_string($conn, $_POST['id_produk']);
    $id_penjual = mysqli_real_escape_string($conn, $_POST['id_penjual']);
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($conn, $_POST['deskripsi_produk']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    
    $image_query = "";
    
    // Handle file upload jika ada
    if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        list($uploadSuccess, $uploadResult) = handleFileUpload($_FILES['image']);
        
        if ($uploadSuccess) {
            $image_query = ", image='$uploadResult'";
        } else {
            echo "<script>alert('$uploadResult');</script>";
            exit;
        }
    }
    
    // Update query
    $query = "UPDATE produk SET 
              id_penjual='$id_penjual', 
              nama_produk='$nama_produk', 
              deskripsi_produk='$deskripsi_produk', 
              harga='$harga', 
              stok='$stok'
              $image_query 
              WHERE id_produk='$id_produk'";
              
    if (mysqli_query($conn, $query)) {
        $_SESSION['edit_success'] = true;
        header("Location: barang.php");
        exit();
    } else {
        echo "<script>
                alert('Gagal mengupdate produk: " . mysqli_error($conn) . "');
                window.location.href = 'barang.php';
              </script>";
    }
}

// Tambahkan ini setelah konfigurasi pagination
$hal = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$batas = 5;
$mulai = ($hal - 1) * $batas;

// Tambahkan kode filter
$seller_filter = isset($_GET['seller']) ? mysqli_real_escape_string($conn, $_GET['seller']) : '';

// Modifikasi query utama
$base_query = "SELECT produk.*, penjual.nama_penjual 
               FROM produk
               LEFT JOIN penjual ON produk.id_penjual = penjual.id_penjual";

// Tambahkan WHERE clause jika ada filter penjual
if ($seller_filter) {
    $base_query .= " WHERE penjual.nama_penjual = '$seller_filter'";
}

// Query untuk total data dengan filter
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM ($base_query) as filtered_data");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_halaman = ceil($total_data / $batas);

// Query final dengan pagination
$query = mysqli_query($conn, $base_query . " ORDER BY produk.id_produk DESC LIMIT $mulai, $batas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        function toggleModal() {
            const modal = document.getElementById('addProdukModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function toggleEditModal() {
            const modal = document.getElementById('editProdukModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openEditModal(id_produk, nama_produk, id_penjual, deskripsi_produk, harga, stok, image) {
            // Populate form fields
            document.getElementById('edit_id_produk').value = id_produk;
            document.getElementById('edit_nama_produk').value = nama_produk;
            document.getElementById('edit_deskripsi_produk').value = deskripsi_produk;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_stok').value = stok;
            
            // Set the current image preview
            const currentImage = document.getElementById('current-image');
            if (currentImage) {
                currentImage.src = image || '#'; // Use placeholder if no image
                currentImage.style.display = image ? 'block' : 'none';
            }
            
            // Set penjual dropdown
            let penjualSelect = document.getElementById('edit_id_penjual');
            if (penjualSelect) {
                for (let option of penjualSelect.options) {
                    if (option.value == id_penjual) {
                        option.selected = true;
                        break;
                    }
                }
            }
            
            // Show modal
            const modal = document.getElementById('editProdukModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    </script>
    <!-- Update CSS untuk custom scrollbar -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Make table header sticky */
        .sticky-header th {
            position: sticky;
            top: 0;
            background-color: rgb(209 213 219);
            z-index: 10;
        }

        /* Ensure minimum content width */
        .table-container {
            min-width: 1000px;
        }

        .seller-card {
            transition: transform 0.3s ease;
        }
        .seller-card:hover {
            transform: translateY(-5px);
        }
        .product-grid {
            display: none;
        }
        .product-grid.active {
            display: grid;
        }
        .seller-grid.hidden {
            display: none;
        }
        .search-input {
            max-width: 250px;
        }
    </style>

</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-1/6 bg-blue-200">
            <!-- Logo -->
            <div class="flex flex-col items-center py-6 px-4">
                <div class="flex flex-col items-center mb-6">
                    <img src="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg" alt="Logo" class="rounded-full mb-2" style="height: 100px;">
                    <h1 class="text-xl font-semibold text-gray-700">Admin <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></h1> 
                </div><br>

                <!-- Navigation -->
                <nav class="space-y-2 w-full">
                    <a href="../dashboard/dashboard.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-home mr-2 text-lg"></i> Dashboard
                    </a>
                    <a href="../admin/admin.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-user mr-2 text-lg"></i> Admin
                    </a>
                    <a href="../penjual/penjual.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'penjual.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-user-tie mr-2 text-lg"></i> Penjual
                    </a>
                    <a href="../user/user.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'user.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-user mr-2 text-lg"></i> User
                    </a>
                    <a href="../barang/barang.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'barang.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-boxes mr-2 text-lg"></i> Produk
                    </a>
                    <a href="../transaksi/transaksi.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'transaksi.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-exchange-alt mr-2 text-lg"></i> Transaksi
                    </a>
                    <a href="../riwayat/riwayat.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'bg-blue-500 text-white' : ''; ?>">
                        <i class="fas fa-history mr-2 text-lg"></i> Riwayat
                    </a>
                    <a href="../logout.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2 text-lg"></i> Log Out
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-5/6 flex flex-col overflow-hidden">
            <div class="bg-blue-200 text-center text-xl font-bold text-black py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1> 
            </div>

            <div class="flex-1 p-6 overflow-y-auto">
                <!-- Header Section -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold" id="pageTitle">Pilih Penjual</h1>
                    <button onclick="toggleModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Tambah Produk
                    </button>
                </div>

                <!-- Back Button (Initially Hidden) -->
                <button id="backButton" onclick="showSellerGrid()" class="hidden mb-6 text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-chevron-left mr-2"></i> Kembali ke Daftar Penjual
                </button>

                <!-- Seller Grid -->
                <div id="sellerGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $seller_query = mysqli_query($conn, "SELECT DISTINCT p.id_penjual, pj.nama_penjual, 
                        COUNT(p.id_produk) as total_products 
                        FROM penjual pj 
                        LEFT JOIN produk p ON pj.id_penjual = p.id_penjual 
                        GROUP BY p.id_penjual, pj.nama_penjual");

                    while ($seller = mysqli_fetch_assoc($seller_query)) {
                        ?>
                        <div class="seller-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" 
                             onclick="showProducts('<?php echo $seller['id_penjual']; ?>', '<?php echo $seller['nama_penjual']; ?>')">
                            <div class="p-6 text-center">
                                <div class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-store text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo $seller['nama_penjual']; ?></h3>
                                <p class="text-gray-600 mt-2"><?php echo $seller['total_products']; ?> Produk</p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <!-- Product Grid (Initially Hidden) -->
                <div id="productGrid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    // Products will be loaded dynamically via JavaScript
                    ?>
                </div>
            </div>
        </div>
    </div> 

            <!-- Modal untuk Tambah -->
            <div id="addProdukModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Produk</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Kiri -->
                            <div class="space-y-4">
                                <div>
                                    <label for="nama_produk" class="block text-gray-700 font-medium mb-2">Nama Produk</label>
                                    <input type="text" name="nama_produk" id="nama_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label for="id_penjual" class="block text-gray-700 font-medium mb-2">Nama Penjual</label>
                                    <select name="id_penjual" id="id_penjual" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="" disabled selected>Pilih Penjual</option>
                                        <?php
                                        $penjualQuery = mysqli_query($conn, "SELECT id_penjual, nama_penjual FROM penjual");
                                        while ($penjual = mysqli_fetch_assoc($penjualQuery)) {
                                            echo "<option value='{$penjual['id_penjual']}'>{$penjual['nama_penjual']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="deskripsi_produk" class="block text-gray-700 font-medium mb-2">Deskripsi Produk</label>
                                    <input type="text" name="deskripsi_produk" id="deskripsi_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label for="harga" class="block text-gray-700 font-medium mb-2">Harga</label>
                                    <input type="number" name="harga" id="harga" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>
                            <!-- Kolom Kanan -->
                            <div class="space-y-4">
                                <div>
                                    <label for="stok" class="block text-gray-700 font-medium mb-2">Stok</label>
                                    <input type="number" name="stok" id="stok" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label for="image" class="block text-gray-700 font-medium mb-2">Pilih Gambar</label>
                                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="previewImage(event)">
                                    <div id="image-preview" class="mt-4">
                                        <img id="preview" src="#" alt="Pratinjau Gambar" class="hidden w-32 h-32 object-cover rounded border">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6 space-x-4 sticky bottom-0 bg-white pt-4 border-t">
                            <button type="button" onclick="toggleModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" name="add_produk" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Produk Modal -->
            <div id="editProdukModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 overflow-y-auto">
                <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto my-8">
                    <div class="max-h-[90vh] overflow-y-auto p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 sticky top-0 bg-white">Edit Produk</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="edit_id_produk" name="id_produk">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Kolom Kiri -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">
                                            Nama Produk
                                        </label>
                                        <input type="text" id="edit_nama_produk" name="nama_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">
                                            Nama Penjual
                                        </label>
                                        <select id="edit_id_penjual" name="id_penjual" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <?php
                                            $penjual_query = mysqli_query($conn, "SELECT id_penjual, nama_penjual FROM penjual");
                                            while ($penjual = mysqli_fetch_assoc($penjual_query)) {
                                                $selected = ($penjual['id_penjual'] == $id_penjual) ? 'selected' : '';
                                                echo "<option value='{$penjual['id_penjual']}' {$selected}>{$penjual['nama_penjual']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                                        <textarea id="edit_deskripsi_produk" name="deskripsi_produk" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Harga</label>
                                        <input type="number" id="edit_harga" name="harga" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Stok</label>
                                        <input type="number" id="edit_stok" name="stok" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">Edit Gambar</label>
                                        
                                        <!-- Preview Gambar Saat Ini -->
                                        <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                                            <p class="text-sm text-gray-500 mb-2">Gambar Saat Ini:</p>
                                            <img id="current-image" src="../<?php echo $produk['image']; ?>"  alt="Gambar Lama" class="w-full h-48 object-contain rounded border bg-white">
                                        </div>

                                        <!-- Input File & Preview Gambar Baru -->
                                        <div class="space-y-4">
                                            <input 
                                                type="file" 
                                                name="image" 
                                                id="edit_image" 
                                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                accept="image/*" 
                                                onchange="previewNewImage(event)"
                                            >

                                            <div id="new-image-preview" class="hidden p-4 border rounded-lg bg-gray-50">
                                                <p class="text-sm text-gray-500 mb-2">Gambar Baru:</p>
                                                <img id="new-preview" src="#" alt="Pratinjau Gambar Baru" class="w-full h-48 object-contain rounded border bg-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6 space-x-4 sticky bottom-0 bg-white pt-4 border-t">
                                <button type="button" onclick="toggleEditModal()" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" name="edit" class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Produk -->
            <div id="deleteProdukModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-95">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Hapus Produk</h3>
                        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="flex justify-center space-x-4">
                        <button onclick="toggleDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300">
                            Batal
                        </button>
                        <a id="confirmDeleteLink" href="#" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300">
                            Ya, Hapus
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Sukses Hapus Penjual -->
            <div id="successDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Produk Berhasil Dihapus</h2>
                    <p class="mb-4">Data produk telah dihapus dari sistem.</p>
                    <button onclick="closeSuccessModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

            <!-- Success Modals -->
            <?php foreach(['add', 'edit'] as $action): ?>
                <div id="<?php echo $action; ?>SuccessModal" 
                    class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-8 max-w-sm w-full text-center">
                        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                        <h2 class="text-2xl font-bold mb-4">Berhasil!</h2>
                        <p class="mb-6">User berhasil <?php 
                            echo $action === 'add' ? 'ditambahkan' : 
                                ($action === 'edit' ? 'diperbarui' : 'dihapus'); 
                        ?>.</p>
                        <button onclick="closeSuccessModal('<?php echo $action; ?>')"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Tutup
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    

        <script>
           // Variabel global untuk menyimpan data original
        let originalData = [];

        // Fungsi untuk menyimpan data original saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            let tableBody = document.querySelector('table tbody');
            originalData = Array.from(tableBody.querySelectorAll('tr')).map(row => row.cloneNode(true));
        });

        // Tambahkan event listener untuk semua filter
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('sellerFilter').addEventListener('change', filterTable);
        document.getElementById('priceFilter').addEventListener('change', filterTable);

        function filterTable() {
            let searchQuery = document.getElementById('searchInput').value.toLowerCase().trim();
            let selectedSeller = document.getElementById('sellerFilter').value.trim();
            let priceOrder = document.getElementById('priceFilter').value;
            
            // Gunakan data original untuk filtering
            let filteredRows = originalData.filter(row => {
                let cells = row.getElementsByTagName('td');
                let productName = cells[1].textContent.toLowerCase().trim();
                let sellerName = cells[2].textContent.trim();
                
                let matchSearch = productName.includes(searchQuery);
                // Perbaikan pada pencocokan nama penjual
                let matchSeller = selectedSeller === '' || 
                                sellerName.toLowerCase() === selectedSeller.toLowerCase();
                
                return matchSearch && matchSeller;
            });

            // Urutkan berdasarkan harga jika filter harga dipilih
            if (priceOrder) {
                filteredRows.sort((a, b) => {
                    let priceA = parseFloat(a.cells[4].textContent.replace(/[^0-9.-]+/g, ""));
                    let priceB = parseFloat(b.cells[4].textContent.replace(/[^0-9.-]+/g, ""));
                    return priceOrder === 'asc' ? priceA - priceB : priceB - priceA;
                });
            }

            // Tampilkan hasil filter
            let tableBody = document.querySelector('table tbody');
            tableBody.innerHTML = '';
            filteredRows.forEach((row, index) => {
                let newRow = row.cloneNode(true);
                newRow.cells[0].textContent = index + 1;
                tableBody.appendChild(newRow);
            });
        }

        // Reset filter dan table ke kondisi awal
        function resetTable() {
            let tableBody = document.querySelector('table tbody tr td');
            tableBody.innerHTML = '';
            originalData.forEach((row, index) => {
                let newRow = row.cloneNode(true);
                newRow.cells[0].textContent = index + 1;
                tableBody.appendChild(newRow);
            });

            // Reset semua filter
            document.getElementById('searchInput').value = '';
            document.getElementById('sellerFilter').value = '';
            document.getElementById('priceFilter').value = '';
        }

        function closeSuccessModal(type) {
            const modalId = type + 'SuccessModal';
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                window.location.reload();
            }
        }

        // Update your document ready function:
        document.addEventListener('DOMContentLoaded', function() {

                // Show success modals if needed
                <?php if (isset($_SESSION['add_success'])): ?>
                    document.getElementById('addSuccessModal').classList.remove('hidden');
                    document.getElementById('addSuccessModal').classList.add('flex');
                    <?php unset($_SESSION['add_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['edit_success'])): ?>
                    document.getElementById('editSuccessModal').classList.remove('hidden');
                    document.getElementById('editSuccessModal').classList.add('flex');
                    <?php unset($_SESSION['edit_success']); ?>
                <?php endif; ?>
            });

            function openDeleteModal(id) {
                const deleteModal = document.getElementById('deleteProdukModal');
                const confirmDeleteLink = document.getElementById('confirmDeleteLink');
                confirmDeleteLink.href = '?hapus=' + id;
                
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            }

            function toggleDeleteModal() {
                const deleteModal = document.getElementById('deleteProdukModal');
                deleteModal.classList.toggle('hidden');
                deleteModal.classList.toggle('flex');
            }

            function openSuccessModal() {
                const successModal = document.getElementById('successDeleteModal');
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');
            }

            function closeSuccessModal() {
                const successModal = document.getElementById('successDeleteModal');
                successModal.classList.remove('flex');
                successModal.classList.add('hidden');
                window.location.href = 'barang.php';
            }

            // Tambahkan event listener untuk memastikan modal bisa ditutup
            document.addEventListener('DOMContentLoaded', function() {
                const closeButton = document.querySelector('#successDeleteModal button');
                if (closeButton) {
                    closeButton.addEventListener('click', closeSuccessModal);
                }
            });

            function previewImage(event) {
                const input = event.target; // Mendapatkan elemen input file
                const preview = document.getElementById('preview'); // Elemen img untuk pratinjau
                const imagePreviewContainer = document.getElementById('image-preview');

                // Memeriksa apakah ada file yang diunggah
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    // Ketika file selesai dibaca
                    reader.onload = function(e) {
                        preview.src = e.target.result; // Menetapkan sumber gambar
                        preview.classList.remove('hidden'); // Menampilkan elemen img
                    };

                    reader.readAsDataURL(input.files[0]); // Membaca file sebagai Data URL
                } else {
                    preview.src = '#'; // Jika tidak ada gambar, kosongkan src
                    preview.classList.add('hidden'); // Sembunyikan elemen img
                }
            }

            function previewNewImage(event) {
                const input = event.target; // Mendapatkan elemen input file
                const newPreview = document.getElementById('new-preview'); // Elemen img untuk pratinjau gambar baru
                const newImagePreviewContainer = document.getElementById('new-image-preview');

                // Memeriksa apakah ada file yang diunggah
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    // Ketika file selesai dibaca
                    reader.onload = function(e) {
                        newPreview.src = e.target.result; // Menetapkan sumber gambar baru
                        newImagePreviewContainer.classList.remove('hidden'); // Menampilkan elemen pratinjau gambar baru
                    };

                    reader.readAsDataURL(input.files[0]); // Membaca file sebagai Data URL
                } else {
                    newPreview.src = '#'; // Jika tidak ada gambar, kosongkan src
                    newImagePreviewContainer.classList.add('hidden'); // Sembunyikan elemen img
                }
            }


            function showProducts(sellerId, sellerName) {
                document.getElementById('sellerGrid').classList.add('hidden');
                document.getElementById('backButton').classList.remove('hidden');
                document.getElementById('pageTitle').textContent = 'Produk ' + sellerName;
                
                // Fetch products for the selected seller
                fetch(`get_barang.php?seller_id=${sellerId}`)
                    .then(response => response.text())
                    .then(html => {
                        const productGrid = document.getElementById('productGrid');
                        productGrid.innerHTML = html;
                        productGrid.classList.remove('hidden');
                    });
            }

            function showSellerGrid() {
                document.getElementById('sellerGrid').classList.remove('hidden');
                document.getElementById('backButton').classList.add('hidden');
                document.getElementById('productGrid').classList.add('hidden');
                document.getElementById('pageTitle').textContent = 'Pilih Penjual';
            }
        </script>

</body>
</html>