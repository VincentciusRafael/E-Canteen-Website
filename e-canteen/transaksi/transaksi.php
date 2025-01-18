<?php
session_start();
include '../config.php';

// Handle Top Up Process
if(isset($_POST['topup'])) {
    $id_user = $_POST['id_user'];
    $amount = $_POST['amount'];
    
    // Insert into transaksi table
    $query = "INSERT INTO top_up (id_user, saldo, total_harga, status) VALUES ($id_user, $amount, $amount, 'completed')";
    
    if(mysqli_query($conn, $query)) {
        // Update user balance
        mysqli_query($conn, "UPDATE user SET saldo = saldo + $amount WHERE id_user = $id_user");
        echo "<script>alert('Top up berhasil!'); window.location='transaksi.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='transaksi.php';</script>";
    }
}

// Handle Withdrawal Process
if(isset($_POST['withdraw'])) {
    $id_penjual = $_POST['id_penjual'];
    $amount = $_POST['amount'];
    
    // Check seller's balance
    $seller = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM penjual WHERE id_penjual = $id_penjual"));
    
    if($seller['saldo'] >= $amount) {
        // Update seller balance
        mysqli_query($conn, "UPDATE penjual SET saldo = saldo - $amount WHERE id_penjual = $id_penjual");
        echo "<script>alert('Penarikan berhasil!'); window.location='transaksi.php';</script>";
    } else {
        echo "<script>alert('Saldo tidak mencukupi!'); window.location='transaksi.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - E-Canteen</title>
    <link rel="icon" href="../images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        .receipt {
            background: repeating-linear-gradient(#fff, #fff 15px, #f0f0f0 15px, #f0f0f0 30px);
        }
    </style>
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
            <div class="bg-blue-200 text-center text-xl font-bold text-black py-6 border-b-3 border-blue-400">
                <h1 class="text-3xl font-bold text-gray-800">E-Canteen</h1>
            </div>

            <div class="flex-1 p-8">
                <div class="grid grid-cols-2 gap-8">
                    <!-- Top Up Section -->
                    <div class="bg-white rounded-lg shadow-lg p-6 fade-in">
                        <h2 class="text-2xl font-bold mb-6 text-blue-600">Top Up Saldo User</h2>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 mb-2">Pilih User</label>
                                
                                <select name="id_user" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400">
                                <option value="" disabled selected>Pilih User</option>
                                    <?php
                                    $users = mysqli_query($conn, "SELECT * FROM user");
                                    while($user = mysqli_fetch_array($users)) {
                                        echo "<option value='{$user['id_user']}'>{$user['nama_user']} - Saldo: Rp " . number_format($user['saldo'],0,',','.') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Jumlah Top Up</label>
                                <input type="number" name="amount" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                            </div>
                            <button type="submit" name="topup" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                Proses Top Up
                            </button>
                        </form>
                    </div>

                    <!-- Withdrawal Section -->
                    <div class="bg-white rounded-lg shadow-lg p-6 fade-in">
                        <h2 class="text-2xl font-bold mb-6 text-green-600">Penarikan Saldo Penjual</h2>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 mb-2">Pilih Penjual</label>
                                <select name="id_penjual" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-400">
                                    <option value="" disabled selected>Pilih Penjual</option>
                                    <?php
                                    $sellers = mysqli_query($conn, "SELECT * FROM penjual");
                                    while($seller = mysqli_fetch_array($sellers)) {
                                        echo "<option value='{$seller['id_penjual']}'>{$seller['nama_penjual']} - Saldo: Rp " . number_format($seller['saldo'],0,',','.') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Jumlah Penarikan</label>
                                <input type="number" name="amount" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-400" required>
                            </div>
                            <button type="submit" name="withdraw" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors">
                                Proses Penarikan
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96 receipt">
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
        function printReceipt(transactionId) {
            // Fetch transaction details from database using AJAX
            // For demo, using static content
            const modal = document.getElementById('receiptModal');
            const content = document.getElementById('receiptContent');
            
            content.innerHTML = `
                <div class="text-2xl font-bold mb-4">E-Canteen</div>
                <div class="text-sm text-gray-600 mb-4">Struk Transaksi</div>
                <div class="border-t border-b py-4 my-4">
                    <div class="flex justify-between mb-2">
                        <span>No. Transaksi:</span>
                        <span>#${transactionId}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Tanggal:</span>
                        <span>${new Date().toLocaleDateString()}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Status:</span>
                        <span class="text-green-600">Berhasil</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600 mt-4">
                    Terima kasih telah menggunakan layanan kami
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
        }

        function printReceiptContent() {
            const content = document.getElementById('receiptContent').innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Struk</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .receipt { max-width: 300px; margin: 0 auto; }
                        </style>
                    </head>
                    <body>
                        <div class="receipt">
                            ${content}
                        </div>
                    </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            let searchQuery = this.value.toLowerCase();
            let table = document.querySelector('table');
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(searchQuery)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' :
                rows[i].style.display = match ? '' : 'none';
            }
        });

        // Add smooth animations for table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseover', () => {
                row.style.transition = 'all 0.3s ease';
                row.style.transform = 'translateX(10px)';
            });

            row.addEventListener('mouseout', () => {
                row.style.transform = 'translateX(0)';
            });
        });

        // Add animation for success messages
        function showSuccessMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg fade-in';
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => alertDiv.remove(), 500);
            }, 3000);
        }

        // Add animation for error messages
        function showErrorMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg fade-in';
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => alertDiv.remove(), 500);
            }, 3000);
        }

        // Format currency input
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = new Intl.NumberFormat('id-ID').format(value);
            input.value = value;
        }

        // Validate form input
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const amount = form.querySelector('input[name="amount"]');
            
            if (!amount.value || amount.value <= 0) {
                showErrorMessage('Jumlah tidak valid');
                return false;
            }
            return true;
        }

        // Add animation for receipt modal
        const receiptModal = document.getElementById('receiptModal');
        receiptModal.addEventListener('click', (e) => {
            if (e.target === receiptModal) {
                closeReceiptModal();
            }
        });

        // Add keyboard support for modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !receiptModal.classList.contains('hidden')) {
                closeReceiptModal();
            }
        });

        // Add custom styles for receipt printing
        function getReceiptStyles() {
            return `
                @page {
                    size: 80mm 200mm;
                    margin: 0;
                }
                body {
                    font-family: 'Courier New', monospace;
                    width: 80mm;
                    margin: 0;
                    padding: 10mm;
                }
                .receipt-header {
                    text-align: center;
                    font-size: 14pt;
                    margin-bottom: 5mm;
                }
                .receipt-body {
                    font-size: 10pt;
                    line-height: 1.5;
                }
                .receipt-footer {
                    text-align: center;
                    font-size: 10pt;
                    margin-top: 5mm;
                    border-top: 1px dashed #000;
                    padding-top: 5mm;
                }
            `;
        }

        // Enhanced print receipt function
        function printReceiptContent() {
            const content = document.getElementById('receiptContent');
            const printWindow = window.open('', '', 'height=600,width=800');
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Struk Transaksi</title>
                    <style>${getReceiptStyles()}</style>
                </head>
                <body>
                    <div class="receipt-header">
                        <h1>E-Canteen</h1>
                        <p>Struk Transaksi</p>
                    </div>
                    <div class="receipt-body">
                        ${content.innerHTML}
                    </div>
                    <div class="receipt-footer">
                        <p>Terima kasih telah bertransaksi</p>
                        <p>Simpan struk ini sebagai bukti transaksi</p>
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>

    <style>
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }

        .modal-transition {
            transition: opacity 0.3s ease-in-out;
        }

        .receipt {
            position: relative;
            overflow: hidden;
        }

        .receipt::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: repeating-linear-gradient(
                45deg,
                #000,
                #000 10px,
                transparent 10px,
                transparent 20px
            );
        }

        @media print {
            .no-print {
                display: none;
            }
            .receipt {
                width: 80mm;
                margin: 0 auto;
            }
        }
    </style>
</body>
</html>