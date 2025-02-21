<?php
session_start();
include '../config.php';

// Handle Top Up Process
if(isset($_POST['topup'])) {
    $id_user = $_POST['id_user'];
    $amount = $_POST['amount'];
    $current_date = date('Y-m-d H:i:s');
    
    // Insert into transaksi table with all required fields
    $query = "INSERT INTO top_up (id_user, saldo, total_harga, status, tanggal_transaksi) 
              VALUES ($id_user, $amount, $amount, 'completed', '$current_date')";
    
    if(mysqli_query($conn, $query)) {
        // Update user balance
        $update_query = "UPDATE user SET saldo = saldo + $amount WHERE id_user = $id_user";
        if(mysqli_query($conn, $update_query)) {
            $_SESSION['topup_success'] = true;
            echo json_encode(['success' => true, 'message' => 'Top up berhasil']);
            exit();
        }
    } 
    
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
    exit();
}

// Handle Withdrawal Process
if(isset($_POST['withdraw'])) {
    $id_penjual = $_POST['id_penjual'];
    $amount = $_POST['amount'];

    // Check seller's balance
    $seller = mysqli_fetch_assoc(mysqli_query($conn, "SELECT saldo FROM penjual WHERE id_penjual = $id_penjual"));

    if($seller['saldo'] >= $amount) {
        mysqli_begin_transaction($conn); // Mulai transaksi
        try {
            // Update saldo penjual
            mysqli_query($conn, "UPDATE penjual SET saldo = saldo - $amount WHERE id_penjual = $id_penjual");

            // Catat ke tabel withdrawals dengan status 'pending'
            mysqli_query($conn, "INSERT INTO withdrawals (id_penjual, saldo, total_withdrawal, status) 
VALUES ($id_penjual, $amount, $amount, 'pending')");

            // Update status menjadi 'completed' setelah penarikan berhasil diproses
            $id_withdrawal = mysqli_insert_id($conn);
            mysqli_query($conn, "UPDATE withdrawals SET status = 'completed' WHERE id_withdrawal = $id_withdrawal");

            mysqli_commit($conn); // Commit jika semua query sukses
            echo json_encode(['success' => true, 'message' => 'Penarikan berhasil']);
        } catch (Exception $e) {
            mysqli_rollback($conn); // Rollback jika ada error
            echo json_encode(['success' => false, 'message' => 'Gagal memproses penarikan: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Saldo tidak mencukupi']);
    }
    exit();
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
                        <form id="topupForm" class="space-y-4">
                            <div class="relative">
                                <label class="block text-gray-700 mb-2">Pilih User</label>
                                <input type="text" class="search-input w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400" placeholder="Cari user..." data-target="userDropdown">
                                <input type="hidden" name="id_user" id="selected_user_id">
                                <div class="custom-dropdown hidden absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-60 overflow-y-auto" id="userDropdown">
                                    <?php
                                    $users = mysqli_query($conn, "SELECT * FROM user");
                                    while($user = mysqli_fetch_array($users)) {
                                        echo "<div class='p-2 hover:bg-gray-100 cursor-pointer' data-value='{$user['id_user']}'>{$user['nama_user']} - Saldo: Rp " . number_format($user['saldo'],0,',','.') . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Jumlah Top Up</label>
                                <input type="number" name="amount" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required>
                            </div>
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                Proses Top Up
                            </button>
                        </form>
                    </div>

                    <!-- Withdrawal Section -->
                    <div class="bg-white rounded-lg shadow-lg p-6 fade-in">
                        <h2 class="text-2xl font-bold mb-6 text-green-600">Penarikan Saldo Penjual</h2>
                        <form id="withdrawForm" class="space-y-4">
                            <div class="relative">
                                <label class="block text-gray-700 mb-2">Pilih Penjual</label>
                                <input type="text" class="search-input w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-400" 
                                    placeholder="Cari penjual..." data-target="sellerDropdown">
                                <input type="hidden" name="id_penjual" id="selected_seller_id">
                                <div class="custom-dropdown hidden absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-60 overflow-y-auto" 
                                    id="sellerDropdown">
                                    <?php
                                    $sellers = mysqli_query($conn, "SELECT * FROM penjual");
                                    while($seller = mysqli_fetch_array($sellers)) {
                                        echo "<div class='p-2 hover:bg-gray-100 cursor-pointer' 
                                                data-value='{$seller['id_penjual']}'>{$seller['nama_penjual']} - Saldo: Rp " . 
                                                number_format($seller['saldo'],0,',','.') . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Jumlah Penarikan</label>
                                <input type="number" name="amount" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-400" required>
                            </div>
                            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors">
                                Proses Penarikan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Up Success Modal -->
    <div id="topupSuccessModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full text-center">
            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
            <h2 class="text-2xl font-bold mb-4">Berhasil!</h2>
            <p class="mb-6">Top up saldo berhasil dilakukan.</p>
            <button onclick="closeSuccessModal('topup')"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Tutup
            </button>
        </div>
    </div>

    <!-- Withdraw Success Modal -->
    <div id="withdrawSuccessModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm w-full text-center">
            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
            <h2 class="text-2xl font-bold mb-4">Berhasil!</h2>
            <p class="mb-6">Penarikan saldo berhasil dilakukan.</p>
            <button onclick="closeSuccessModal('withdraw')"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Tutup
            </button>
        </div>
    </div>

    <script>
    // Initialize searchable dropdowns
    function initSearchableDropdowns() {
        const searchInputs = document.querySelectorAll('.search-input');
        
        searchInputs.forEach(input => {
            const dropdownId = input.dataset.target;
            const dropdown = document.getElementById(dropdownId);
            
            // Show dropdown on input focus
            input.addEventListener('focus', () => {
                dropdown.classList.remove('hidden');
            });
            
            // Filter dropdown items
            input.addEventListener('input', () => {
                const searchText = input.value.toLowerCase();
                const items = dropdown.querySelectorAll('div');
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchText) ? 'block' : 'none';
                });
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Handle item selection
            const items = dropdown.querySelectorAll('div');
            items.forEach(item => {
                item.addEventListener('click', () => {
                    input.value = item.textContent;
                    if (dropdownId === 'userDropdown') {
                        document.getElementById('selected_user_id').value = item.dataset.value;
                    } else if (dropdownId === 'sellerDropdown') {
                        document.getElementById('selected_seller_id').value = item.dataset.value;
                    }
                    dropdown.classList.add('hidden');
                });
            });
        });
    }

    // Success modal handling
    function closeSuccessModal(type) {
        document.getElementById(type + 'SuccessModal').classList.add('hidden');
        document.getElementById(type + 'SuccessModal').classList.remove('flex');
        window.location.reload();
    }

    // Initialize dropdowns when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initSearchableDropdowns();
    });

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

    // Top Up Form Submission
    document.getElementById('topupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('topup', '1'); // Add this to trigger the PHP topup processing
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show success modal
                document.getElementById('topupSuccessModal').classList.remove('hidden');
                document.getElementById('topupSuccessModal').classList.add('flex');
                
                // Reload page after delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses top up');
        });
    });

    // Withdraw Form Submission
    document.getElementById('withdrawForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('withdraw', '1');

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tampilkan modal sukses
            document.getElementById('withdrawSuccessModal').classList.remove('hidden');
            document.getElementById('withdrawSuccessModal').classList.add('flex');

            // Reload halaman setelah 2 detik
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses penarikan');
    });
});
</script>

    <style>
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes modalFadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }

        .modal-transition {
            transition: opacity 0.3s ease-in-out;
        }

        .fade-in {
            animation: modalFadeIn 0.3s ease-out;
        }
    </style>
</body>
</html>