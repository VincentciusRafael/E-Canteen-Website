<?php
session_start();
include '../config.php';
if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}

// Logika untuk menghapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    if (is_numeric($id)) {
        $result = mysqli_query($conn, "DELETE FROM user WHERE id_user='$id'");
        if ($result) {
            $_SESSION['delete_success'] = true;
            header("Location: user.php");
            exit();
        } else {
            echo "<script>
                    alert('Gagal menghapus user!');
                    window.location='user.php';
                </script>";
        }
    }
}

// Logika untuk menambah user
if (isset($_POST['tambah'])) {
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $saldo = mysqli_real_escape_string($conn, $_POST['saldo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $insert_query = "INSERT INTO user (nama_user, username, email, role, saldo, password, tanggal_bergabung) 
                     VALUES ('$nama_user', '$username', '$email', '$role', '$saldo', '$password', NOW())";
    
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['add_success'] = true;
        header("Location: user.php");
        exit();
    } else {
        echo "<script>alert('Gagal menambah user!');</script>";
    }
}

// Logika untuk edit user
if (isset($_POST['edit'])) {
    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $saldo = mysqli_real_escape_string($conn, $_POST['saldo']);
    
    // Build the base update query
    $update_query = "UPDATE user SET 
                    nama_user='$nama_user', 
                    username='$username', 
                    email='$email', 
                    role='$role', 
                    saldo='$saldo'";
    
    // Add password to update query only if a new password was provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query .= ", password='$password'";
    }
    
    // Complete the query with the WHERE clause
    $update_query .= " WHERE id_user='$id_user'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['edit_success'] = true;
        header("Location: user.php");
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate user!');</script>";
    }
}

// Ambil nilai filter role dari GET
$roleFilter = isset($_GET['roleFilter']) ? $_GET['roleFilter'] : '';

// Base query
$sql = "SELECT * FROM user";
$conditions = [];

// Jika ada filter role, tambahkan ke kondisi
if ($roleFilter !== '') {
    $conditions[] = "role = '$roleFilter'";
}

// Jika ada kondisi, tambahkan ke query
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Konfigurasi Pagination
$hal = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$batas = 5; // Jumlah data per halaman
$mulai = ($hal - 1) * $batas;

// Count total rows for pagination
$total_query = mysqli_query($conn, $sql);
$total_data = mysqli_num_rows($total_query);
$total_halaman = ceil($total_data / $batas);

// Add LIMIT clause for pagination
$sql .= " LIMIT $mulai, $batas";
$query = mysqli_query($conn, $sql);

// Function to build pagination URL
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['hal'] = $page;
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1> 
            </div>

            <div class="flex-1 p-4">
                <!-- Content Header -->
                <div class="flex justify-between items-center py-2">
                    <h1 class="text-2xl font-semibold">Kelola User</h1>
                    <button onclick="toggleModal('add')" 
                            class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Tambah User
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="search-input form-control p-2 border rounded" placeholder="Cari User...">
                    <select id="roleFilter" name="roleFilter" class="p-2 border rounded bg-white" onchange="applyFilter()">
                        <option value="">Semua Role</option>
                        <option value="user" <?php echo ($roleFilter === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="guru" <?php echo ($roleFilter === 'guru') ? 'selected' : ''; ?>>Guru</option>
                    </select>
                </div>

                <!-- User Table -->
                <div class="card shadow-md rounded-lg overflow-hidden h-[calc(100vh-250px)] overflow-y-auto custom-scrollbar">
                    <div class="card-body p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead class="sticky-header">
                                    <tr class="bg-gray-300 text-center">
                                        <th class="px-4 py-2 border border-gray-400">No</th>
                                        <th class="px-4 py-2 border border-gray-400">Nama User</th>
                                        <th class="px-4 py-2 border border-gray-400">Username</th>
                                        <th class="px-4 py-2 border border-gray-400">Email</th>
                                        <th class="px-4 py-2 border border-gray-400">Role</th>
                                        <th class="px-4 py-2 border border-gray-400">Saldo</th>
                                        <th class="px-4 py-2 border border-gray-400">Tanggal Bergabung</th>
                                        <th class="px-4 py-2 border border-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = $mulai + 1;
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo '<tr>
                                            <td class="px-4 py-2 text-center border border-gray-400">' . $no . '</td>
                                            <td class="px-4 py-2 border border-gray-400">' . htmlspecialchars($row['nama_user']) . '</td>
                                            <td class="px-4 py-2 border border-gray-400">' . htmlspecialchars($row['username']) . '</td>
                                            <td class="px-4 py-2 border border-gray-400">' . htmlspecialchars($row['email']) . '</td>
                                            <td class="px-4 py-2 border border-gray-400">' . htmlspecialchars($row['role']) . '</td>
                                            <td class="px-4 py-2 border border-gray-400">Rp ' . number_format($row['saldo'], 0, ',', '.') . '</td>
                                            <td class="px-4 py-2 border border-gray-400">' . htmlspecialchars($row['tanggal_bergabung']) . '</td>
                                            <td class="px-4 py-2 border border-gray-400">
                                                <button onclick="openEditModal(
                                                    \'' . $row['id_user'] . '\',
                                                    \'' . htmlspecialchars($row['nama_user'], ENT_QUOTES) . '\',
                                                    \'' . htmlspecialchars($row['username'], ENT_QUOTES) . '\',
                                                    \'' . htmlspecialchars($row['email'], ENT_QUOTES) . '\',
                                                    \'' . htmlspecialchars($row['role'], ENT_QUOTES) . '\',
                                                    \'' . $row['saldo'] . '\'
                                                )" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                    Edit
                                                </button>
                                                <a onclick="openDeleteModal(' . $row['id_user'] . ')" 
                                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>';
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex justify-between items-center">
                            <!-- Container untuk pagination -->
                            <div class="flex items-center space-x-2">
                                <?php
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
                                $jumlah_number = 5; // Jumlah button number yang akan ditampilkan
                                $start_number = ($hal > $jumlah_number) ? $hal - floor($jumlah_number/2) : 1;
                                $end_number = $start_number + $jumlah_number - 1;

                                if ($end_number > $total_halaman) {
                                    $end_number = $total_halaman;
                                    $start_number = ($total_halaman - $jumlah_number + 1 < 1) ? 1 : $total_halaman - $jumlah_number + 1;
                                }

                                if ($start_number > 1) {
                                    echo "<span class='px-3 py-2 bg-gray-200 text-gray-700 rounded'>...</span>";
                                }

                                for ($i = $start_number; $i <= $end_number; $i++) {
                                    $activeClass = $i == $hal ? 'bg-blue-600 text-white' : 'bg-blue-200 text-gray-700 hover:bg-blue-500 hover:text-white';
                                    echo "<a href='?hal=$i' class='px-3 py-2 rounded $activeClass'>$i</a>";
                                }

                                if ($end_number < $total_halaman) {
                                    echo "<span class='px-3 py-2 bg-gray-200 text-gray-700 rounded'>...</span>";
                                }

                                // Tombol Next dan Last
                                if ($hal < $total_halaman) {
                                    echo "<a href='?hal=" . ($hal + 1) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                            Next <i class='fas fa-chevron-right'></i>
                                        </a>";
                                    echo "<a href='?hal=$total_halaman' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                            Last
                                        </a>";
                                }
                                ?>
                            </div>
                            <!-- Total produk -->
                            <div class="text-gray-600">
                                Total <?php echo $total_data; ?> produk
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>  

        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Tambah User Baru
                </h2>
                <form id="addUserForm" method="POST">
                    <div class="mb-4">
                        <label for="nama_user" class="block text-gray-700 font-medium mb-2">
                            Nama User
                        </label>
                        <input id="nama_user" name="nama_user" type="text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Nama - Kelas">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="username">
                            Username
                        </label>
                        <input id="username" name="username" type="text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="email">
                            Email
                        </label>
                        <input id="email" name="email" type="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="role">
                            Role
                        </label>
                        <select id="role" name="role" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Role</option>
                            <option value="user">User</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="saldo">
                            Saldo
                        </label>
                        <input id="saldo" name="saldo" type="number" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2" for="password">
                            Password
                        </label>
                        <input id="password" name="password" type="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="toggleModal('add')"class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit User</h2>
                <form method="POST">
                    <input type="hidden" id="edit_id_user" name="id_user">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_nama_user">
                            Nama User
                        </label>
                        <input id="edit_nama_user" name="nama_user" type="text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_username">
                            Username
                        </label>
                        <input id="edit_username" name="username" type="text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_email">
                            Email
                        </label>
                        <input id="edit_email" name="email" type="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_role">
                            Role
                        </label>
                        <select id="edit_role" name="role" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Role</option>
                            <option value="user">User</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_saldo">
                            Saldo
                        </label>
                        <input id="edit_saldo" name="saldo" type="number" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="edit_password">
                            Password Baru
                        </label>
                        <input id="edit_password" name="password" type="password" 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="toggleModal('edit')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">
                            Batal
                        </button>
                        <button type="submit" name="edit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                            Update 
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteUserModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-sm w-full">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                    <h2 class="text-2xl font-bold mb-4">Konfirmasi Hapus</h2>
                    <p class="mb-6">Apakah Anda yakin ingin menghapus user ini?</p>
                    <div class="flex justify-center gap-4">
                        <button onclick="toggleModal('delete')"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Batal
                        </button>
                        <a id="confirmDeleteLink" 
                           href="#" 
                           class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Hapus
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modals -->
        <?php foreach(['add', 'edit', 'delete'] as $action): ?>
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
</div>

<script>
// Modal handling functions
function toggleModal(type) {
    const modalMap = {
        'add': 'addUserModal',
        'edit': 'editUserModal',
        'delete': 'deleteUserModal',
        'successAdd': 'addSuccessModal',
        'successEdit': 'editSuccessModal',
        'successDelete': 'deleteSuccessModal'
    };

    const modalId = modalMap[type] || type;
    const modal = document.getElementById(modalId);
    
    if (modal) {
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('modal-active');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('modal-active');
        }
    }
}

function openEditModal(id_user, nama_user, username, email, role, saldo) {
    document.getElementById('edit_id_user').value = id_user;
    document.getElementById('edit_nama_user').value = nama_user;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_saldo').value = saldo;
    toggleModal('edit');
}

function openDeleteModal(id) {
    document.getElementById('confirmDeleteLink').href = '?hapus=' + id;
    toggleModal('delete');
}

function closeSuccessModal(type) {
    toggleModal('success' + type.charAt(0).toUpperCase() + type.slice(1));
    window.location.reload();
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchQuery = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchQuery) ? '' : 'none';
    });
});

// Filter functionality
function applyFilter() {
    const roleFilter = document.getElementById('roleFilter').value;
    const currentUrl = new URL(window.location.href);
    
    if (roleFilter) {
        currentUrl.searchParams.set('roleFilter', roleFilter);
    } else {
        currentUrl.searchParams.delete('roleFilter');
    }
    
    window.location.href = currentUrl.toString();
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                const modalId = this.id;
                const modalType = Object.entries(modalMap).find(([key, value]) => value === modalId)?.[0];
                if (modalType) {
                    toggleModal(modalType);
                }
            }
        });
    });

    // Show success modals if needed
    <?php if (isset($_SESSION['add_success'])): ?>
        toggleModal('successAdd');
        <?php unset($_SESSION['add_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['edit_success'])): ?>
        toggleModal('successEdit');
        <?php unset($_SESSION['edit_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_success'])): ?>
        toggleModal('successDelete');
        <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>