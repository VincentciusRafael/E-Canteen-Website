<?php
session_start();
include '../config.php';

if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}

// Fungsi untuk mengenkripsi password
function encryptPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fungsi untuk memverifikasi password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Logika untuk menghapus admin
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = mysqli_query($conn, "DELETE FROM admin WHERE id_admin='$id'");
    if ($delete) {
        echo "<script>
                window.onload = function() {
                    openSuccessModal();
                }
              </script>";
    }
}

// Logika untuk menambahkan admin
if (isset($_POST['tambah'])) {
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid!');</script>";
    } else {
        $encryptedPassword = $password;
        $insert = mysqli_query($conn, "INSERT INTO admin (nama_admin, email, password) 
                             VALUES ('$nama_admin', '$email', '$encryptedPassword')");
        
        if ($insert) {
            echo "<script>
                    window.onload = function() {
                        openSuccessAddModal();
                    }
                  </script>";
        }
    }
}

// Logika untuk mengedit admin
if (isset($_POST['edit'])) {
    $id_admin = $_POST['id_admin'];
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek jika email diubah atau tidak
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid!');</script>";
    } else {
        // Jika email tidak diubah, gunakan email yang lama
        $existingQuery = mysqli_query($conn, "SELECT email FROM admin WHERE id_admin='$id_admin'");
        $existingAdmin = mysqli_fetch_array($existingQuery);
        if (empty($email)) {
            $email = $existingAdmin['email'];  // Gunakan email lama jika tidak ada perubahan
        }

        $updateQuery = "UPDATE admin SET nama_admin='$nama_admin', email='$email' WHERE id_admin='$id_admin'";

        // Update password jika ada perubahan
        if (!empty($password)) {
            $encryptedPassword = $password;
            $updateQuery = "UPDATE admin SET nama_admin='$nama_admin', email='$email', password='$encryptedPassword' WHERE id_admin='$id_admin'";
        }

        $update = mysqli_query($conn, $updateQuery);
        
        if ($update) {
            echo "<script>
                    window.onload = function() {
                        openSuccessEditModal();
                    }
                  </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        function toggleModal() {
            const modal = document.getElementById('addAdminModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function toggleEditModal() {
            const modal = document.getElementById('editAdminModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openEditModal(id, name, email, password) {
            document.getElementById('edit_id_admin').value = id;
            document.getElementById('edit_nama_admin').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_password').value = ''; // Kosongkan password agar tidak terlihat
            toggleEditModal();
        }
    </script>
    <style>
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
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-blue-200 text-center text-xl font-bold text-black py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1> 
            </div>

            <div class="flex-1 p-4">
                <div class="flex justify-between items-center py-3 mb-3">
                    <h1 class="text-2xl font-semibold">Kelola Admin</h1>
                    <button onclick="toggleModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Tambah Admin
                    </button>
                </div>

                <div class="mb-3">
                    <input type="text" id="searchInput" class="search-input form-control p-2 border rounded w-full md:w-1/2" placeholder="Cari Admin...">
                </div>

                <div class="card shadow-md rounded-lg overflow-hidden">
                    <div class="card-body p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-300 text-center">
                                        <th class="px-4 py-2 border border-gray-400">No</th>
                                        <th class="px-4 py-2 border border-gray-400">Nama Admin</th>
                                        <th class="px-4 py-2 border border-gray-400">Email Admin</th>
                                        <th class="px-4 py-2 border border-gray-400">Password</th>
                                        <th class="px-4 py-2 border border-gray-400">Tanggal Bergabung</th>
                                        <th class="px-4 py-2 border border-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM admin");
                                    $no = 1;
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "
                                            <tr class='hover:bg-gray-200'>
                                                <td class='px-4 py-2 text-center border border-gray-400'>{$no}</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['nama_admin']}</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['email']}</td>
                                                <td class='px-4 py-2 border border-gray-400'>" . str_repeat('●', strlen($row['password'])) . "</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['tanggal_bergabung']}</td>
                                                <td class='px-4 py-2 text-center border border-gray-400'>
                                                    <button onclick=\"openEditModal('{$row['id_admin']}', '{$row['nama_admin']}', '{$row['email']}', '{$row['password']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
                                                    <a href='#' onclick=\"openDeleteModal('{$row['id_admin']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Hapus</a>
                                                </td>
                                            </tr>
                                        ";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Admin -->
            <div id="addAdminModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Admin</h2>
                    <form method="POST">
                        <div class="mb-4">
                            <label for="nama_admin" class="block text-gray-700 font-medium mb-2">Nama Admin</label>
                            <input type="text" id="nama_admin" name="nama_admin" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full">Tambah Admin</button>
                        <button type="button" onclick="toggleModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mt-4 w-full">Tutup</button>
                    </form>
                </div>
            </div>

            <!-- Modal Edit Admin -->
            <div id="editAdminModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Admin</h2>
                    <form method="POST">
                        <input type="hidden" id="edit_id_admin" name="id_admin">
                        <div class="mb-4">
                            <label for="edit_nama_admin" class="block text-gray-700 font-medium mb-2">Nama Admin</label>
                            <input type="text" id="edit_nama_admin" name="nama_admin" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="edit_email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="edit_password" class="block text-gray-700 font-medium mb-2">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" id="edit_password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit" name="edit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full">Update Admin</button>
                        <button type="button" onclick="toggleEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mt-4 w-full">Tutup</button>
                    </form>
                </div>
            </div>

            <!-- Modal Sukses Tambah Admin -->
            <div id="successAddModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Berhasil Ditambahkan</h2>
                    <p class="mb-4">Data admin baru telah disimpan dalam sistem.</p>
                    <button onclick="closeSuccessAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

            <!-- Modal Sukses Edit Admin -->
            <div id="successEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Berhasil Diperbarui</h2>
                    <p class="mb-4">Data admin telah berhasil diupdate.</p>
                    <button onclick="closeSuccessEditModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Admin -->
            <div id="deleteAdminModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-95">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Hapus Admin</h3>
                        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus admin ini? Tindakan ini tidak dapat dibatalkan.</p>
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
            <!-- Modal Sukses Hapus Admin -->
            <div id="successDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Berhasil Dihapus</h2>
                    <p class="mb-4">Data admin telah dihapus dari sistem.</p>
                    <button onclick="closeSuccessModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchQuery = this.value.toLowerCase();
            let table = document.querySelector('table'); // Ambil tabel di DOM
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Mulai dari index 1, karena index 0 adalah header
                let cells = rows[i].getElementsByTagName('td'); // Ambil semua kolom dalam baris
                let match = false;

                for (let j = 0; j < cells.length; j++) { // Loop semua kolom di setiap baris
                    if (cells[j].textContent.toLowerCase().includes(searchQuery)) {
                        match = true; // Jika ada kecocokan, tandai baris sebagai cocok
                        break;
                    }
                }

                // Tampilkan atau sembunyikan baris berdasarkan kecocokan
                rows[i].style.display = match ? '' : 'none';
            }
        });

        function openSuccessEditModal() {
            const successModal = document.getElementById('successEditModal');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');
        }

        function closeSuccessEditModal() {
            const successModal = document.getElementById('successEditModal');
            successModal.classList.remove('flex');
            successModal.classList.add('hidden');
            window.location.href = 'admin.php'; // Refresh halaman
        }

        function openSuccessAddModal() {
            const successModal = document.getElementById('successAddModal');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');
        }

        function closeSuccessAddModal() {
            const successModal = document.getElementById('successAddModal');
            successModal.classList.remove('flex');
            successModal.classList.add('hidden');
            window.location.href = 'admin.php'; // Refresh halaman
        }
        
        function openDeleteModal(id) {
            const deleteModal = document.getElementById('deleteAdminModal');
            const confirmDeleteLink = document.getElementById('confirmDeleteLink');
            confirmDeleteLink.href = '?hapus=' + id;
            
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function toggleDeleteModal() {
            const deleteModal = document.getElementById('deleteAdminModal');
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
            window.location.href = 'admin.php'; // Refresh halaman
        }

        
    </script>

</body>
</html>
