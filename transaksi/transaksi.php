<?php
session_start();
include '../config.php';

if(!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit();
}

// Debug function
function debug_to_console($data) {
    echo "<script>console.log('Debug: " . json_encode($data) . "');</script>";
}

// Handle Top Up Process 
if(isset($_POST['topup'])) {
    $id_user = $_POST['id_user'];
    $amount = $_POST['amount'];
    
    if(!is_numeric($amount) || $amount <= 0) {
        echo "<script>showErrorModal('Jumlah top up tidak valid!'); window.location='transaksi.php';</script>";
        exit();
    }

    mysqli_autocommit($conn, FALSE);
    $success = true;

    // Insert into top_up table using prepared statement
    $stmt = $conn->prepare("INSERT INTO top_up (id_user, saldo, total_harga, status) VALUES (?, ?, ?, 'completed')");
    if($stmt) {
        $stmt->bind_param("idd", $id_user, $amount, $amount);
        if(!$stmt->execute()) {
            $success = false;
            debug_to_console("Error in top up insert: " . $stmt->error);
        }
        $stmt->close();
    } else {
        $success = false;
        debug_to_console("Error in prepare top up: " . $conn->error);
    }
    
    // Update user balance
    if($success) {
        $update_stmt = $conn->prepare("UPDATE user SET saldo = saldo + ? WHERE id_user = ?");
        if($update_stmt) {
            $update_stmt->bind_param("di", $amount, $id_user);
            if(!$update_stmt->execute()) {
                $success = false;
                debug_to_console("Error in update: " . $update_stmt->error);
            }
            $update_stmt->close();
        } else {
            $success = false;
            debug_to_console("Error in prepare update: " . $conn->error);
        }
    }

    if($success) {
        mysqli_commit($conn);
        echo "<script>showSuccessModal('Top up berhasil!'); window.location='transaksi.php';</script>";
    } else {
        mysqli_rollback($conn);
        echo "<script>showErrorModal('Error dalam proses top up'); window.location='transaksi.php';</script>";
    }
    
    mysqli_autocommit($conn, TRUE);
}

// Handle Withdrawal Process
if(isset($_POST['withdraw'])) {
    $id_penjual = $_POST['id_penjual'];
    $amount = $_POST['amount'];
    
    if(!is_numeric($amount) || $amount <= 0) {
        echo "<script>showErrorModal('Jumlah penarikan tidak valid!'); window.location='transaksi.php';</script>";
        exit();
    }
    
    // Get current seller balance using prepared statement
    $balance_stmt = $conn->prepare("SELECT saldo FROM penjual WHERE id_penjual = ?");
    if(!$balance_stmt) {
        echo "<script>showErrorModal('Error system!'); window.location='transaksi.php';</script>";
        exit();
    }
    
    $balance_stmt->bind_param("i", $id_penjual);
    $balance_stmt->execute();
    $result = $balance_stmt->get_result();
    $seller = $result->fetch_assoc();
    $balance_stmt->close();
    
    if($seller && $seller['saldo'] >= $amount) {
        mysqli_autocommit($conn, FALSE);
        $success = true;
        
        // Get the next available ID
        $next_id_query = "SELECT COALESCE(MAX(id_withdrawal), 0) + 1 AS next_id FROM withdrawals";
        $next_id_result = mysqli_query($conn, $next_id_query);
        $next_id_row = mysqli_fetch_assoc($next_id_result);
        $next_id = $next_id_row['next_id'];
        
        // Insert withdrawal record with explicit ID
        $stmt = $conn->prepare("INSERT INTO withdrawals (id_withdrawal, id_penjual, saldo, status, tanggal_withdrawal, total_withdrawal) VALUES (?, ?, ?, 'completed', NOW(), ?)");
        if($stmt) {
            $stmt->bind_param("iidd", $next_id, $id_penjual, $amount, $amount);
            if(!$stmt->execute()) {
                $success = false;
                debug_to_console("Error in withdrawal insert: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $success = false;
            debug_to_console("Error in prepare withdrawal: " . $conn->error);
        }
        
        // Update seller balance
        if($success) {
            $update_stmt = $conn->prepare("UPDATE penjual SET saldo = saldo - ? WHERE id_penjual = ?");
            if($update_stmt) {
                $update_stmt->bind_param("di", $amount, $id_penjual);
                if(!$update_stmt->execute()) {
                    $success = false;
                    debug_to_console("Error in update: " . $update_stmt->error);
                }
                $update_stmt->close();
            } else {
                $success = false;
                debug_to_console("Error in prepare update: " . $conn->error);
            }
        }
        
        if($success) {
            mysqli_commit($conn);
            echo "<script>showSuccessModal('Penarikan berhasil!'); window.location='transaksi.php';</script>";
        } else {
            mysqli_rollback($conn);
            echo "<script>showErrorModal('Gagal melakukan penarikan: Database error'); window.location='transaksi.php';</script>";
        }
        
        mysqli_autocommit($conn, TRUE);
    } else {
        echo "<script>showErrorModal('Saldo tidak mencukupi atau penjual tidak ditemukan!'); window.location='transaksi.php';</script>";
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
                <h1 class="text-3xl font-bold text-gray-800">E-Kantin</h1>
            </div>

            <div class="flex-1 p-8">
                <div class="grid grid-cols-2 gap-8">
                    <!-- Top Up Section -->
                    <div class="bg-white rounded-lg shadow-lg p-6 fade-in">
                        <h2 class="text-2xl font-bold mb-6 text-blue-600">Top Up Saldo User</h2>
                        <form method="POST" class="space-y-4">
                            <div class="relative">
                                <label class="block text-gray-700 mb-2">Pilih User</label>
                                <input type="text" class="search-input w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400" placeholder="Cari user..." data-target="userDropdown">
                                <div class="custom-dropdown hidden absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-60 overflow-y-auto" id="userDropdown">
                                    <select name="id_user" class="hidden">
                                        <?php
                                        $users = mysqli_query($conn, "SELECT * FROM user");
                                        while($user = mysqli_fetch_array($users)) {
                                            echo "<option value='{$user['id_user']}'>{$user['nama_user']} - Saldo: Rp " . number_format($user['saldo'],0,',','.') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
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
                            <div class="relative">
                                <label class="block text-gray-700 mb-2">Pilih Penjual</label>
                                <input type="text" class="search-input w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400" placeholder="Cari penjual..." data-target="penjualDropdown">
                                <div class="custom-dropdown hidden absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-60 overflow-y-auto" id="penjualDropdown">
                                    <select name="id_penjual" class="hidden">
                                        <?php
                                        $sellers = mysqli_query($conn, "SELECT * FROM penjual");
                                        while($seller = mysqli_fetch_array($sellers)) {
                                            echo "<option value='{$seller['id_penjual']}'>{$seller['nama_penjual']} - Saldo: Rp " . number_format($seller['saldo'],0,',','.') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Jumlah Penarikan</label>
                                <input type="number" name="amount" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-400" required min="1">
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

    <!-- Transaction Modal -->
    <div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96 receipt">
            <div id="transactionContent" class="text-center space-y-4">
                <h2 class="text-2xl font-bold mb-4">E-Canteen</h2>
                <p class="text-gray-600 mb-4">Bukti Transaksi</p>
                
                <div class="border-t border-b py-4 my-4">
                    <div class="flex justify-between mb-2">
                        <span>No. Transaksi:</span>
                        <span id="transactionId"></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Tanggal:</span>
                        <span id="transactionDate"></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Tipe:</span>
                        <span id="transactionType"></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span id="personLabel"></span>
                        <span id="personName"></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Jumlah:</span>
                        <span id="transactionAmount" class="font-bold"></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Status:</span>
                        <span class="text-green-600">Berhasil</span>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600">
                    Terima kasih telah menggunakan layanan kami
                </p>
            </div>
            
            <div class="mt-6 flex justify-center space-x-4">
                <button onclick="printTransactionReceipt()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                    <i class="fas fa-print mr-1"></i> Cetak
                </button>
                <button onclick="closeTransactionModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize searchable dropdowns
            initSearchableDropdowns();
        });

        function initSearchableDropdowns() {
            const searchInputs = document.querySelectorAll('.search-input');
            
            searchInputs.forEach(input => {
                const dropdownId = input.dataset.target;
                const dropdown = document.getElementById(dropdownId);
                const select = dropdown.querySelector('select');
                
                // Create dropdown items from select options
                const options = Array.from(select.options);
                const dropdownList = document.createElement('div');
                dropdownList.className = 'dropdown-list';
                
                options.forEach(option => {
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                    item.textContent = option.text;
                    item.dataset.value = option.value;
                    
                    item.addEventListener('click', () => {
                        input.value = option.text;
                        select.value = option.value;
                        dropdown.classList.add('hidden');
                    });
                    
                    dropdownList.appendChild(item);
                });
                
                dropdown.appendChild(dropdownList);
                
                // Show dropdown on input focus
                input.addEventListener('focus', () => {
                    dropdown.classList.remove('hidden');
                });
                
                // Filter dropdown items
                input.addEventListener('input', () => {
                    const searchText = input.value.toLowerCase();
                    const items = dropdownList.children;
                    
                    Array.from(items).forEach(item => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(searchText) ? 'block' : 'none';
                    });
                    
                    dropdown.classList.remove('hidden');
                });
                
                // Hide dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        }
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

        function validateWithdrawalForm() {
            const form = document.querySelector('form');
            const selectedSeller = form.querySelector('select[name="id_penjual"]');
            const amount = form.querySelector('input[name="amount"]').value;
            
            if (!selectedSeller.value) {
                alert('Silakan pilih penjual terlebih dahulu!');
                return false;
            }
            
            const selectedOption = selectedSeller.options[selectedSeller.selectedIndex];
            const currentBalance = parseFloat(selectedOption.dataset.balance);
            
            if (parseFloat(amount) > currentBalance) {
                alert('Jumlah penarikan melebihi saldo yang tersedia!');
                return false;
            }
            
            if (parseFloat(amount) <= 0) {
                alert('Jumlah penarikan harus lebih dari 0!');
                return false;
            }
            
            return true;
        }
        
        // Log form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const formData = new FormData(this);
            console.log('Form submitted with data:', {
                id_penjual: formData.get('id_penjual'),
                amount: formData.get('amount')
            });
        });

        function showSuccessModal(message) {
            const modal = document.getElementById('receiptModal');
            const content = document.getElementById('receiptContent');
            
            content.innerHTML = `
                <div class="text-2xl font-bold mb-4">E-Canteen</div>
                <div class="text-sm text-gray-600 mb-4">${message}</div>
                <div class="border-t border-b py-4 my-4">
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

        function showErrorModal(message) {
            const modal = document.getElementById('receiptModal');
            const content = document.getElementById('receiptContent');
            
            content.innerHTML = `
                <div class="text-2xl font-bold mb-4">E-Canteen</div>
                <div class="text-sm text-gray-600 mb-4">${message}</div>
                <div class="border-t border-b py-4 my-4">
                    <div class="flex justify-between mb-2">
                        <span>Status:</span>
                        <span class="text-red-600">Gagal</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600 mt-4">
                    Silakan coba lagi atau hubungi administrator.
                </div>
            `;
            
            modal.classList.remove('hidden');
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