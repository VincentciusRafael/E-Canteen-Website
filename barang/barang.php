<?php
session_start();
include '../config.php';

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
                    <h1 class="text-xl font-semibold text-gray-700">Admin</h1> 
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
                <h1 class="text-3xl font-bold text-gray-800">E-Canteen</h1> 
            </div>

            <div class="flex-1 p-4 overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Kelola Produk</h1>
                    <button onclick="toggleModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Tambah Produk
                    </button>
                </div>

                <div class="mb-3">
                    <input type="text" id="searchInput" class="search-input form-control p-2 border rounded w-full md:w-1/2" placeholder="Cari Produk...">
                    <select id="sellerFilter" class="p-2 border rounded">
                        <option value="">Cari Sesuai Nama Penjual</option>
                        <?php
                        $penjualQuery = mysqli_query($conn, "SELECT DISTINCT penjual.id_penjual, penjual.nama_penjual 
                                                            FROM penjual 
                                                            INNER JOIN produk ON penjual.id_penjual = produk.id_penjual 
                                                            ORDER BY penjual.nama_penjual");
                        while ($penjual = mysqli_fetch_assoc($penjualQuery)) {
                            $selected = (isset($_GET['seller']) && $_GET['seller'] == $penjual['nama_penjual']) ? 'selected' : '';
                            echo "<option value='{$penjual['nama_penjual']}' {$selected}>{$penjual['nama_penjual']}</option>";
                        }
                        ?>
                    </select>
                    <select id="priceFilter" class="p-2 border rounded ml-2">
                        <option value="">Urutkan Berdasarkan Harga</option>
                        <option value="asc">Harga Terendah ke Tertinggi</option>
                        <option value="desc">Harga Tertinggi ke Terendah</option>
                    </select>
                </div>

                <div class="card shadow-md rounded-lg overflow-hidden h-[calc(100vh-250px)] overflow-y-auto custom-scrollbar">
                    <div class="card-body p-4">
                        <div class="overflow-x-auto"  style="max-height: calc(100vh - 250px);">
                            <table class="min-w-full table-auto border-collapse">
                                <thead class="sticky-header">
                                    <tr class="bg-gray-300 text-center">
                                        <th class="px-4 py-2 border border-gray-400">No</th>
                                        <th class="px-4 py-2 border border-gray-400">Nama Produk</th>
                                        <th class="px-4 py-2 border border-gray-400">Nama Penjual</th>
                                        <th class="px-4 py-2 border border-gray-400">Deskripsi Singkat</th>
                                        <th class="px-4 py-2 border border-gray-400">Harga</th>
                                        <th class="px-4 py-2 border border-gray-400">Stok</th>
                                        <th class="px-4 py-2 border border-gray-400">Image</th>
                                        <th class="px-4 py-2 border border-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query dengan pagination
                                    $query = mysqli_query($conn, "SELECT produk.*, penjual.nama_penjual FROM produk
                                                            LEFT JOIN penjual ON produk.id_penjual = penjual.id_penjual 
                                                            ORDER BY produk.id_produk DESC 
                                                            LIMIT $mulai, $batas");
                                    $no = $mulai + 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        echo "
                                        <tr class='hover:bg-gray-100'>
                                            <td class='px-4 py-2 text-center border border-gray-400'>{$no}</td>
                                            <td class='px-4 py-2 border border-gray-400'>{$row['nama_produk']}</td>
                                            <td class='px-4 py-2 border border-gray-400'>{$row['nama_penjual']}</td>
                                            <td class='px-4 py-2 border border-gray-400'>{$row['deskripsi_produk']}</td>
                                            <td class='px-4 py-2 border border-gray-400'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                                            <td class='px-4 py-2 border border-gray-400'>{$row['stok']}</td>
                                            <td class='px-4 py-2 text-center border border-gray-400'><img src='{$row['image']}' alt='Product Image' class='w-16 h-16 object-cover'></td>
                                            <td class='px-4 py-2 text-center border border-gray-400'>
                                                <button onclick=\"openEditModal(
                                                    '{$row['id_produk']}', 
                                                    '" . htmlspecialchars($row['nama_produk'], ENT_QUOTES) . "',
                                                    '{$row['id_penjual']}', 
                                                    '" . htmlspecialchars($row['deskripsi_produk'], ENT_QUOTES) . "', 
                                                    '{$row['harga']}', 
                                                    '{$row['stok']}',
                                                    '{$row['image']}'
                                                )\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
                                                <a href='#' onclick=\"openDeleteModal('{$row['id_produk']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Hapus</a>
                                            </td>
                                        </tr>
                                        ";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center">
                            <div class="pagination-container flex items-center space-x-2">
                                <?php
                                // Fungsi untuk membuat query string dengan parameter yang ada
                                function buildQueryString($hal) {
                                    $params = $_GET;
                                    $params['hal'] = $hal;
                                    return http_build_query($params);
                                }

                                // Tombol Previous 
                                if ($hal > 1) {
                                    echo "<a href='?hal=1' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                            First
                                        </a>";
                                    echo "<a href='?hal=" . ($hal - 1) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                            <i class='fas fa-chevron-left'></i> Prev
                                        </a>";
                                }

                                // Nomor halaman
                                $total_show = 5; // Jumlah kotak yang ditampilkan
                                $start_range = max(1, min($hal - 2, $total_halaman - 4));
                                $end_range = min($start_range + 4, $total_halaman);

                                if ($start_range > 1) {
                                    echo "<a href='?" . buildQueryString(1) . "' class='px-3 py-2 rounded bg-blue-200 text-gray-700 hover:bg-blue-500 hover:text-white'>1</a>";
                                    if ($start_range > 2) {
                                        echo "<span class='px-3 py-2'>...</span>";
                                    }
                                }

                                for ($i = $start_range; $i <= $end_range; $i++) {
                                    $activeClass = $i == $hal ? 'bg-blue-600 text-white' : 'bg-blue-200 text-gray-700 hover:bg-blue-500 hover:text-white';
                                    echo "<a href='?" . buildQueryString($i) . "' class='px-3 py-2 rounded $activeClass'>$i</a>";
                                }

                                if ($end_range < $total_halaman) {
                                    if ($end_range < $total_halaman - 1) {
                                        echo "<span class='px-3 py-2'>...</span>";
                                    }
                                    echo "<a href='?" . buildQueryString($total_halaman) . "' class='px-3 py-2 rounded bg-blue-200 text-gray-700 hover:bg-blue-500 hover:text-white'>$total_halaman</a>";
                                }

                                // Tombol Next dan Last
                                if ($hal < $total_halaman) {
                                    echo "<a href='?" . buildQueryString($hal + 1) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                            Next <i class='fas fa-chevron-right'></i>
                                        </a>";
                                    echo "<a href='?" . buildQueryString($total_halaman) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>Last</a>";
                                }
                                ?>
                            </div>
                            <div class="text-gray-600">
                                Total <?php echo $total_data; ?> produk
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>

            <!-- Modal untuk Tambah -->
            <div id="addProdukModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Produk</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="nama_produk" class="block text-gray-700 font-medium mb-2">Nama Produk</label>
                            <input type="text" name="nama_produk" id="nama_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <label for="deskripsi_produk" class="block text-gray-700 font-medium mb-2">Deskripsi Produk</label>
                            <input type="text" name="deskripsi_produk" id="deskripsi_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="harga" class="block text-gray-700 font-medium mb-2">Harga</label>
                            <input type="number" name="harga" id="harga" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="stok" class="block text-gray-700 font-medium mb-2">Stok</label>
                            <input type="number" name="stok" id="stok" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Pilih Gambar</label>
                            <input type="file" name="image" id="image"  accept=""  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="previewImage(event)">
                            <div id="image-preview" class="mt-4">
                                <img id="preview" src="#" alt="Pratinjau Gambar" class="hidden w-32 h-32 object-cover rounded border">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4">
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
            <div id="editProdukModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Produk</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id_produk" name="id_produk">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Nama Produk
                            </label>
                            <input type="text" id="edit_nama_produk" name="nama_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                            <textarea id="edit_deskripsi_produk" name="deskripsi_produk" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Harga</label>
                            <input type="number" id="edit_harga" name="harga" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Stok</label>
                            <input type="number" id="edit_stok" name="stok" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Edit Gambar</label>
                            
                            <!-- Pratinjau Gambar Lama -->
                            <div class="mb-2">
                                <p class="text-sm text-gray-500">Gambar Saat Ini:</p>
                                <img id="current-image" src="" alt="Gambar Lama" class="w-32 h-32 object-cover rounded border">
                            </div>

                            <!-- Input File untuk Gambar Baru -->
                            <input 
                                type="file" 
                                name="image" 
                                id="edit_image" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                accept="image/*" 
                                onchange="previewNewImage(event)"
                            >

                            <!-- Pratinjau Gambar Baru -->
                            <div id="new-image-preview" class="mt-4 hidden">
                                <p class="text-sm text-gray-500">Gambar Baru:</p>
                                <img id="new-preview" src="#" alt="Pratinjau Gambar Baru" class="w-32 h-32 object-cover rounded border">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="toggleEditModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">Batal</button>
                            <button type="submit" name="edit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">Update</button>
                        </div>
                    </form>
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
        </script>

</body>
</html>