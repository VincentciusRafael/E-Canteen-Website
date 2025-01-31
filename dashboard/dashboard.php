<?php
session_start();
if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-Canteen </title>
    <link rel="icon" href="images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .stats-card {
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard E-Canteen</title>
	<link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-1/6 bg-blue-200 h-screen flex flex-col items-center py-6 px-4">
            <!-- Logo -->
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

        <!-- Main Content -->
        <div class="w-5/6 flex flex-col">
            <!-- Topbar -->
            <div class="bg-blue-200 text-center text-xl font-bold text-black py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1> 
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <!-- Stats Section -->
                <div class="grid grid-cols-4 gap-12 mb-10">
                    <div class="flex items-center justify-between stats-card bg-gray-100 p-4 h-32 flex-grow rounded-lg shadow-lg border-4 border-blue-200">
                        <div>
                            <h1 class="text-2xl">Admin</h1>
                            <p class="text-xl font-bold text-gray-800 mt-2">
                                <?php
                                    $query = mysqli_query($conn, "SELECT COUNT(*) as total from admin");
                                    $data = mysqli_fetch_assoc($query);
                                    echo number_format($data['total']);
                                ?>
                            </p>
                        </div>
                        <i class=" text-3xl text-gray-700 rounded-full">
                            <img src="../images/user.png" style="height: 70px;">
                        </i>
                    </div>
                    <div class="flex items-center justify-between stats-card bg-gray-100 p-4 h-32 flex-grow rounded-lg shadow-lg border-4 border-blue-200">
                        <div>
                            <h1 class="text-2xl">User</h1>
                            <p class="text-xl font-bold text-gray-800 mt-2">
                            <?php
                                    $query = mysqli_query($conn, "SELECT COUNT(*) as total from user");
                                    $data = mysqli_fetch_assoc($query);
                                    echo number_format($data['total']);
                                ?>
                            </p>
                        </div>
                        <i class=" text-3xl text-gray-700 rounded-full">
                            <img src="../images/history.png" style="height: 70px;">
                        </i>
                    </div>
                    <div class="flex items-center justify-between stats-card bg-gray-100 p-4 h-32 flex-grow rounded-lg shadow-lg border-4 border-blue-200">
                        <div>
                            <h1 class="text-2xl">Penjual</h1>
                            <p class="text-xl font-bold text-gray-800 mt-2">
                            <?php
                                    $query = mysqli_query($conn, "SELECT COUNT(*) as total from penjual");
                                    $data = mysqli_fetch_assoc($query);
                                    echo number_format($data['total']);
                                ?>
                            </p>
                        </div>
                        <i class=" text-3xl text-gray-700 rounded-full">
                            <img src="../images/salary.png" style="height: 70px;">
                        </i>
                    </div>
                    <div class="flex items-center justify-between stats-card bg-gray-100 p-4 h-32 flex-grow rounded-lg shadow-lg border-4 border-blue-200">
                        <div>
                            <h1 class="text-2xl">Barang</h1>
                            <p class="text-xl font-bold text-gray-800 mt-2">
                            <?php
                                    $query = mysqli_query($conn, "SELECT COUNT(*) as total from produk");
                                    $data = mysqli_fetch_assoc($query);
                                    echo number_format($data['total']);
                                ?>
                            </p>
                        </div>
                        <i class=" text-3xl text-gray-700 rounded-full">
                            <img src="../images/barang.png" style="height: 70px;">
                        </i>
                    </div>
                </div>

                <!-- History Section -->
                <div class="mb-4 bg-white shadow rounded-lg">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <h6 class="text-xl font-bold text-blue-500">History Pembelian Hari Ini</h6>
                        <div class="flex space-x-2">
                            <a href="../pembelian/pembelian.php" class="text-sm px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">Lihat Pembelian</a>
                            <a href="riwayat.php" class="text-sm px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama Penjual</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Jumlah Pembelian</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Pemasukan (Total)</th>
                                    </tr>
                                </thead>
                                <tbod
                                    <!-- Example Data Rows (Replace with Dynamic Data) -->
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">Penjual A</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">50</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">Rp 500,000</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">Penjual B</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">30</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">Rp 300,000</td>
                                    </tr>
                                    <!-- End of Example Data Rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
