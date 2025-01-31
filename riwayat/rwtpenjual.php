<?php
session_start();
include '../config.php';

if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}

// Pagination Configuration
$hal = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$batas = 10; // Items per page
$mulai = ($hal - 1) * $batas;

// Get total records for pagination
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM withdrawals");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_halaman = ceil($total_data / $batas);

// Query for withdrawals with pagination
$query = "SELECT w.*, p.nama_penjual 
          FROM withdrawals w
          JOIN penjual p ON w.id_penjual = p.id_penjual
          ORDER BY w.tanggal_withdrawal DESC
          LIMIT $mulai, $batas";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penarikan - E-Canteen</title>
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

        .sticky-header th {
            position: sticky;
            top: 0;
            background-color: rgb(209 213 219);
            z-index: 10;
        }

        .table-container {
            min-width: 1000px;
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
                    <a href="../dashboard/dashboard.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-home mr-2 text-lg"></i> Dashboard
                    </a>
                    <a href="../admin/admin.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-user mr-2 text-lg"></i> Admin
                    </a>
                    <a href="../penjual/penjual.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-user-tie mr-2 text-lg"></i> Penjual
                    </a>
                    <a href="../user/user.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-user mr-2 text-lg"></i> User
                    </a>
                    <a href="../barang/barang.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-boxes mr-2 text-lg"></i> Produk
                    </a>
                    <a href="../transaksi/transaksi.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg">
                        <i class="fas fa-exchange-alt mr-2 text-lg"></i> Transaksi
                    </a>
                    <a href="../riwayat/riwayat.php" class="nav-link flex items-center p-3 w-full text-gray-700 hover:bg-blue-500 hover:text-white transition-all duration-300 rounded-lg bg-blue-500 text-white">
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
            <div class="bg-blue-200 text-center py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1>
            </div>

            <!-- Withdrawal History -->
            <div class="flex-1 p-4">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Riwayat Penarikan Penjual</h2>
                        <a href="riwayat.php" class="text-sm px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                            Lihat Riwayat Top Up
                        </a>
                    </div>
                    
                    <!-- Search and Filter -->
                    <div class="mb-6 flex gap-4">
                        <input type="text" id="searchInput" placeholder="Cari penarikan..." 
                               class="p-2 border rounded-lg w-64 focus:ring-2 focus:ring-blue-400">
                        <select id="statusFilter" class="p-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                            <option value="">Semua Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <!-- Withdrawals Table -->
                    <div class="overflow-x-auto h-[calc(100vh-250px)] overflow-y-auto custom-scrollbar">
                        <table class="min-w-full bg-white table-auto border-collapse">
                            <thead class="sticky-header">
                                <tr class="bg-gray-300 text-center">
                                    <th class="py-3 px-4 border border-gray-400">ID Penarikan</th>
                                    <th class="py-3 px-4 border border-gray-400">Tanggal</th>
                                    <th class="py-3 px-4 border border-gray-400">Nama Penjual</th>
                                    <th class="py-3 px-4 border border-gray-400">Jumlah</th>
                                    <th class="py-3 px-4 border border-gray-400">Status</th>
                                    <th class="py-3 px-4 border border-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            while($row = mysqli_fetch_array($result)) {
                                $statusClass = $row['status'] == 'completed' ? 'text-green-600' : 
                                            ($row['status'] == 'failed' ? 'text-red-600' : 'text-yellow-600');
                                echo "<tr class='border-b hover:bg-gray-50'>
                                        <td class='py-3 px-4 text-center border border-gray-400'>WD" . str_pad($row['id_withdrawal'], 6, '0', STR_PAD_LEFT) . "</td>
                                        <td class='py-3 px-4 border border-gray-400'>{$row['tanggal_withdrawal']}</td>
                                        <td class='py-3 px-4 border border-gray-400'>{$row['nama_penjual']}</td>
                                        <td class='py-3 px-4 border border-gray-400'>Rp " . number_format($row['saldo'],0,',','.') . "</td>
                                        <td class='py-3 px-4 border border-gray-400 {$statusClass}'>{$row['status']}</td>
                                        <td class='py-3 px-4 text-center border border-gray-400'>
                                            <button onclick='printReceipt(\"WD" . str_pad($row['id_withdrawal'], 6, '0', STR_PAD_LEFT) . "\", \"{$row['nama_penjual']}\", {$row['saldo']}, \"{$row['tanggal_withdrawal']}\", \"Withdrawal\")' 
                                                    class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors'>
                                                <i class='fas fa-print mr-1'></i> Cetak
                                            </button>
                                        </td>
                                    </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <?php
                            if ($hal > 1) {
                                echo "<a href='?hal=" . ($hal - 1) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                        <i class='fas fa-chevron-left'></i> Prev
                                    </a>";
                            }

                            $start_page = max(1, min($hal - 1, $total_halaman - 2));
                            $end_page = min($start_page + 2, $total_halaman);
                            
                            if ($end_page - $start_page < 2) {
                                $start_page = max(1, $end_page - 2);
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $activeClass = $i == $hal ? 'bg-blue-600 text-white' : 'bg-blue-200 text-gray-700 hover:bg-blue-500 hover:text-white';
                                echo "<a href='?hal=$i' class='px-3 py-2 rounded $activeClass'>$i</a>";
                            }

                            if ($hal < $total_halaman) {
                                echo "<a href='?hal=" . ($hal + 1) . "' class='px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600'>
                                        Next <i class='fas fa-chevron-right'></i>
                                    </a>";
                            }
                            ?>
                        </div>

                        <div class="text-gray-600">
                            Total <?php echo $total_data; ?> penarikan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <div id="receiptContent" class="text-center space-y-4">
                <!-- Receipt content will be populated by JavaScript -->
            </div>
            <div class="mt-6 flex justify-center space-x-4">
                <button onclick="printReceiptContent()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <button onclick="closeReceiptModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function printReceipt(transactionId, sellerName, amount, transactionDate) {
            const modal = document.getElementById('receiptModal');
            const content = document.getElementById('receiptContent');
            const formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
            const date = new Date(transactionDate);
            
            content.innerHTML = `
                <div class="text-left" style="font-family: 'Courier New', monospace;">
                    <div class="text-center mb-4">
                        <div class="text-2xl font-bold">E-CANTEEN</div>
                        <div class="text-sm">Kantin Digital SMKN 10 Surabaya</div>
                        <div class="text-sm">Jl. Keputih Tegal No.54</div>
                        <div class="text-sm">Surabaya, Jawa Timur</div>
                        <div class="text-xs">================================</div>
                    </div>

                    <div class="mb-4 text-sm">
                        <div>No. Transaksi: ${transactionId}</div>
                        <div>Tanggal: ${date.toLocaleString('id-ID')}</div>
                        <div>Kasir: ADMIN</div>
                        <div>Penjual: ${sellerName}</div>
                    </div>

                    <div class="text-xs">--------------------------------</div>
                    
                    <div class="mb-4">
                        <div class="flex justify-between">
                            <span>PENARIKAN SALDO</span>
                            <span>${formattedAmount}</span>
                        </div>
                    </div>

                    <div class="text-xs">--------------------------------</div>
                    
                    <div class="mb-4">
                        <div class="flex justify-between font-bold">
                            <span>TOTAL</span>
                            <span>${formattedAmount}</span>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <div class="text-sm">Terima Kasih</div>
                        <div class="text-sm">Atas Kunjungan Anda</div>
                        <div class="text-xs mt-2">================================</div>
                        <div class="text-xs">${new Date().toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
        }

        function printReceiptContent() {
            const receiptContent = document.getElementById('receiptContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        body { font-family: 'Courier New', monospace; }
                        .text-center { text-align: center; }
                        .mb-4 { margin-bottom: 1rem; }
                        .mt-4 { margin-top: 1rem; }
                        .text-2xl { font-size: 1.5rem; }
                        .text-sm { font-size: 0.875rem; }
                        .text-xs { font-size: 0.75rem; }
                        .font-bold { font-weight: bold; }
                        @media print {
                            @page { margin: 0; }
                            body { margin: 1cm; }
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');

        function filterTable() {
            const searchQuery = searchInput.value.toLowerCase();
            const statusQuery = statusFilter.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const matchesSearch = text.includes(searchQuery);
                const matchesStatus = statusQuery === '' || status === statusQuery;
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeReceiptModal();
            }
        });
    </script>
</body>
</html>