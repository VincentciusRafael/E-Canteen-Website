<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Dashboard - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            overflow: hidden;
        }

        .main-content {
            width: calc(100% - 16.6667%);
            /* 5/6 sesuai w-5/6 */
            overflow: hidden;
        }

        .stats-card {
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 20rem;
        }

        @media screen and (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media screen and (max-width: 640px) {
            .grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-1/6 bg-blue-200 h-screen flex flex-col items-center py-6 px-4">
            <div class="flex flex-col items-center mb-6">
                <img src="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg" alt="Logo" class="rounded-full w-full h-auto" mb-2" style="height: 100px;">
                <h1 class="text-xl font-semibold text-gray-700">Admin</h1>
            </div><br>

            <!-- Navigation -->
            <nav class="space-y-2 w-full">
                <a href="../dashboard/dashboard.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-home mr-2 text-lg"></i> Dashboard
                </a>
                <a href="../admin/admin.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'admin.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-user mr-2 text-lg"></i> Admin
                </a>
                <a href="../penjual/penjual.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'penjual.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-user-tie mr-2 text-lg"></i> Penjual
                </a>
                <a href="../user/user.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'user.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-user mr-2 text-lg"></i> User
                </a>
                <a href="../barang/barang.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'barang.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-boxes mr-2 text-lg"></i> Produk
                </a>
                <a href="../transaksi/transaksi.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'transaksi.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-exchange-alt mr-2 text-lg"></i> Transaksi
                </a>
                <a href="../riwayat/riwayat.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'bg-blue-500 text-white' : ''; ?>">
                    <i class="fas fa-history mr-2 text-lg"></i> Riwayat
                </a>
                <a href="../logout.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white rounded-lg">
                    <i class="fas fa-sign-out-alt mr-2 text-lg"></i> Log Out
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex flex-col">
            <div class="bg-blue-200 text-center text-xl font-bold text-black py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1>
            </div>

            <div class="p-6">
                <!-- Stats Section -->
                <div class="grid grid-cols-4 gap-12 mb-10">
                    <?php
                    $sections = [
                        ["Admin", "admin", "../images/user.png"],
                        ["User", "user", "../images/history.png"],
                        ["Penjual", "penjual", "../images/salary.png"],
                        ["Barang", "produk", "../images/barang.png"]
                    ];
                    foreach ($sections as $section) {
                        $query = mysqli_query($conn, "SELECT COUNT(*) as total FROM " . $section[1]);
                        $data = mysqli_fetch_assoc($query);
                    ?>
                        <div class="flex items-center justify-between stats-card bg-gray-100 p-4 h-32 flex-grow rounded-lg shadow-lg border-4 border-blue-200">
                            <div>
                                <h1 class="text-2xl"><?= $section[0]; ?></h1>
                                <p class="text-xl font-bold text-gray-800 mt-2"><?= number_format($data['total']); ?></p>
                            </div>
                            <i class="text-3xl text-gray-700 rounded-full">
                                <img src="<?= $section[2]; ?>" style="height: 70px;" class="w-full h-auto">
                            </i>
                        </div>
                    <?php } ?>
                </div>

                <!-- History Section -->
                <div class="mb-4 bg-white shadow rounded-lg">
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <h6 class="text-xl font-bold text-blue-500">History Pembelian Hari Ini</h6>
                        <div class="flex space-x-2">
                            <a href="../pembelian/pembelian.php" class="text-sm px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">Lihat Pembelian</a>
                            <a href="../riwayat/riwayat.php" class="text-sm px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">Lihat Semua</a>
                        </div>
                    </div>

                    <div class="p-4">
                        <?php
                        $query = mysqli_query($conn, "SELECT p.nama_penjual, SUM(pb.total_harga) AS total_pemasukan 
                                                      FROM pembelian pb 
                                                      JOIN penjual p ON pb.id_penjual = p.id_penjual 
                                                      WHERE DATE(pb.tanggal_pembelian) = CURDATE() 
                                                      GROUP BY p.id_penjual 
                                                      ORDER BY total_pemasukan DESC");
                        $rowCount = mysqli_num_rows($query);
                        ?>
                        <div class="table-container w-full <?php echo ($rowCount > 5) ? 'overflow-y-scroll max-h-80' : ''; ?>">
                            <table class="min-w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-300 text-center">
                                        <th class="px-4 py-2 border border-gray-400 font-bold text-gray-700">Nama Penjual</th>
                                        <th class="px-4 py-2 border border-gray-400 font-bold text-gray-700">Pemasukan Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($data = mysqli_fetch_assoc($query)) {
                                        echo '<tr class="hover:bg-gray-100">';
                                        echo '<td class="px-4 py-2 border border-gray-400 text-sm text-gray-700">' . htmlspecialchars($data['nama_penjual']) . '</td>';
                                        echo '<td class="px-4 py-2 border border-gray-400 text-sm text-gray-700">Rp ' . number_format($data['total_pemasukan'], 0, ',', '.') . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
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
