<?php
session_start();
include '../config.php';

// Set timezone PHP
date_default_timezone_set('Asia/Jakarta');

// Logika untuk menghapus penjual
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = mysqli_query($conn, "DELETE FROM penjual WHERE id_penjual='$id'");
    if ($delete) {
        // Gunakan session untuk menandai sukses
        $_SESSION['delete_success'] = true;
        header("Location: penjual.php");
        exit();
    } else {
        echo "<script>
                alert('Gagal menghapus penjual');
                window.location.href = 'penjual.php';
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

// Logika untuk menambahkan penjual
if (isset($_POST['tambah'])) {
    $nama_penjual = $_POST['nama_penjual'];
    $kontak = $_POST['kontak'];
    $status = $_POST['status'];
    $tanggal_bergabung = date('Y-m-d H:i:s'); // Waktu saat ini dengan timezone PHP

    $query = "INSERT INTO penjual (nama_penjual, kontak, status, tanggal_bergabung) 
              VALUES ('$nama_penjual', '$kontak', '$status', '$tanggal_bergabung')";

    mysqli_query($conn, $query);
    echo "<script>
            window.onload = function() {
                openSuccessAddModal();
            }
          </script>";
}

// Logika untuk mengedit penjual
if (isset($_POST['edit'])) {
    $id_penjual = $_POST['id_penjual'];
    $nama_penjual = $_POST['nama_penjual'];
    $status = $_POST['status'];
    $kontak = $_POST['kontak']; // Kontak penjual

    mysqli_query($conn, "UPDATE penjual 
                         SET nama_penjual='$nama_penjual', kontak='$kontak', status='$status' 
                         WHERE id_penjual='$id_penjual'");
    echo "<script>
            window.onload = function() {
                openSuccessEditModal();
            }
          </script>";
}

// Ambil nilai filter status dan pencarian dari GET
$statusFilter = isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Modifikasi query untuk menyertakan filter status
$sql = "SELECT * FROM penjual";
$conditions = [];

// Jika ada filter status, tambahkan ke kondisi
if ($statusFilter !== '') {
    $conditions[] = "status = '$statusFilter'";
}

// Jika ada filter pencarian, tambahkan ke kondisi
if ($searchQuery !== '') {
    $conditions[] = "nama_penjual LIKE '%$searchQuery%'";
}

// Jika ada kondisi, tambahkan ke query
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$query = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjual - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        function toggleModal() {
            const modal = document.getElementById('addPenjualModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function toggleEditModal() {
            const modal = document.getElementById('editPenjualModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openEditModal(id_penjual, nama_penjual, kontak, status) {
        // Set nilai pada form edit
        document.getElementById('edit_id_penjual').value = id_penjual;
        document.getElementById('edit_nama_penjual').value = nama_penjual;
        document.getElementById('edit_kontak').value = kontak;
        
        // Set nilai status pada select option
        const statusSelect = document.getElementById('edit_status');
        for(let i = 0; i < statusSelect.options.length; i++) {
            if(statusSelect.options[i].value === status) {
                statusSelect.selectedIndex = i;
                break;
            }
        }
        
        // Tampilkan modal
        const modal = document.getElementById('editPenjualModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    </script>
    <style>
        .search-input {
            max-width: 250px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex flex h-screen overflow-hidden">
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

            <div class="flex-1 p-4">
                <div class="flex justify-between items-center py-3 mb-3">
                    <h1 class="text-2xl font-semibold">Kelola Penjual</h1>
                    <button onclick="toggleModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Tambah Penjual
                    </button>
                </div>

                <div class="mb-3">
                    <input type="text" id="searchInput" class="search-input form-control p-2 border rounded w-full md:w-1/2" placeholder="Cari Penjual...">
                    <select id="statusFilter" name="statusFilter" class="p-2 border rounded bg-white">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?php echo (isset($_GET['statusFilter']) && $_GET['statusFilter'] === 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Non-Aktif" <?php echo (isset($_GET['statusFilter']) && $_GET['statusFilter'] === 'Non-Aktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                    </select>
                </div>

                <div class="card shadow-md rounded-lg overflow-hidden">
                    <div class="card-body p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-300 text-center">
                                        <th class="px-4 py-2 border border-gray-400">No</th>
                                        <th class="px-4 py-2 border border-gray-400">Nama Penjual</th>
                                        <th class="px-4 py-2 border border-gray-400">Kontak</th>
                                        <th class="px-4 py-2 border border-gray-400">Status</th>
                                        <th class="px-4 py-2 border border-gray-400">Saldo</th>
                                        <th class="px-4 py-2 border border-gray-400">Tanggal Bergabung</th>
                                        <th class="px-4 py-2 border border-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "
                                            <tr class='hover:bg-gray-100'>
                                                <td class='px-4 py-2 text-center border border-gray-400'>{$no}</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['nama_penjual']}</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['kontak']}</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['status']}</td>
                                                <td class='px-4 py-2 border border-gray-400'>Rp " . number_format($row['saldo'], 0, ',', '.') . "</td>
                                                <td class='px-4 py-2 border border-gray-400'>{$row['tanggal_bergabung']}</td>
                                                <td class='px-4 py-2 text-center border border-gray-400'>
                                                    <button onclick=\"openEditModal('{$row['id_penjual']}', '{$row['nama_penjual']}', '{$row['kontak']}', '{$row['status']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
                                                    <a href='#' onclick=\"openDeleteModal('{$row['id_penjual']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Hapus</a>
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

            <!-- Modal Tambah Penjual -->
            <div id="addPenjualModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tambah Penjual</h2>
                    <form method="POST">
                        <div class="mb-4">
                            <label for="nama_penjual" class="block text-gray-700 font-medium mb-2">Nama Penjual</label>
                            <input type="text" id="nama_penjual" name="nama_penjual" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="kontak" class="block text-gray-700 font-medium mb-2">Kontak</label>
                            <input type="text" id="kontak" name="kontak" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                            <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="toggleModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Edit Penjual -->
            <div id="editPenjualModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Penjual</h2>
                    <form method="POST">
                        <input type="hidden" id="edit_id_penjual" name="id_penjual">
                        <div class="mb-4">
                            <label for="edit_nama_penjual" class="block text-gray-700 font-medium mb-2">Nama Penjual</label>
                            <input type="text" id="edit_nama_penjual" name="nama_penjual" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_kontak" class="block text-gray-700 font-medium mb-2">Kontak</label>
                            <input type="text" id="edit_kontak" name="kontak" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_status" class="block text-gray-700 font-medium mb-2">Status</label>
                            <select id="edit_status" name="status" class="w-full px-4 py-2 border rounded-lg">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="toggleEditModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Batal</button>
                            <button type="submit" name="edit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Sukses Tambah Peenjual -->
            <div id="successAddModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Penjual Berhasil Ditambahkan</h2>
                    <p class="mb-4">Data penjual baru telah disimpan dalam sistem.</p>
                    <button onclick="closeSuccessAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

            <!-- Modal Sukses Edit Penjual -->
            <div id="successEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500 text-6xl mx-auto"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Penjual Berhasil Diperbarui</h2>
                    <p class="mb-4">Data penjual telah berhasil diupdate.</p>
                    <button onclick="closeSuccessEditModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tutup</button>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Penjual -->
            <div id="deletePenjualModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-95">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Hapus Penjual</h3>
                        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus penjual ini? Tindakan ini tidak dapat dibatalkan.</p>
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
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Penjual Berhasil Dihapus</h2>
                    <p class="mb-4">Data penjual telah dihapus dari sistem.</p>
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
        document.getElementById('statusFilter').addEventListener('change', function() {
            const selectedStatus = this.value;
            const currentSearch = document.getElementById('searchInput').value;
            
            // Buat URL baru berdasarkan status yang dipilih
            let url = `?`;
            if (selectedStatus) {
                url += `statusFilter=${selectedStatus}&`;
            }
            if (currentSearch) {
                url += `search=${currentSearch}&`;
            }
            window.location.href = url;
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
            window.location.href = 'penjual.php'; // Refresh halaman
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
            window.location.href = 'penjual.php'; // Refresh halaman
        }

        function openDeleteModal(id) {
            const deleteModal = document.getElementById('deletePenjualModal');
            const confirmDeleteLink = document.getElementById('confirmDeleteLink');
            confirmDeleteLink.href = '?hapus=' + id;
            
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function toggleDeleteModal() {
            const deleteModal = document.getElementById('deletePenjualModal');
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
            window.location.href = 'penjual.php';
        }

        // Tambahkan event listener untuk memastikan modal bisa ditutup
        document.addEventListener('DOMContentLoaded', function() {
            const closeButton = document.querySelector('#successDeleteModal button');
            if (closeButton) {
                closeButton.addEventListener('click', closeSuccessModal);
            }
        });
    </script>

</body>
</html>
