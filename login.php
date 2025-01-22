<?php 
session_start();
include 'config.php';

$username_error = '';
$password_error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['nama_admin']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE nama_admin='$username'");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        if ($password == $data['password']) {
            $_SESSION['id_admin'] = $data['id_admin'];
            $_SESSION['nama_admin'] = $data['nama_admin'];
            header("Location: ./dashboard/dashboard.php");
        } else {
            $password_error = "Password salah!";
        }
    } else {
        $username_error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Canteen</title>
    <link rel="icon" href="images/WhatsApp Image 2025-01-04 at 10.08.50_8e6a12dc.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen relative">
        <!-- Background Image dengan Overlay -->
        <div class="absolute inset-0">
            <img 
                src="images/imgsekol_waifu2x_photo_noise3_scale.webp" 
                alt="Background" 
                class="w-full h-full min-h-screen" 
                style="
                    min-width: 100%;
                    min-height: 100%;
                    width: 100vw;
                    height: 100vh;
                    object-fit: cover;
                    object-position: center;
                    image-rendering: crisp-edges;
                    transform: scale(1); /* Sedikit zoom untuk menutupi area */
                "> 
            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <!-- Content -->
        <div class="relative min-h-screen flex flex-col items-center justify-center px-4">
            <!-- Welcome Message -->
            <div class="text-center mb-8 space-y-2">
                <h1 class="text-5xl font-bold text-white tracking-wide">Selamat Datang</h1>
                <p class="text-2xl text-white/90 font-medium">di E-Canteen SMKN 10 Surabaya</p>
            </div>
            
            <!-- Login Form -->
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 rounded-xl shadow-xl w-full max-w-md">
                <h2 class="text-3xl font-semibold text-center text-white mb-8">Login</h2>
                <form method="POST">
                    <!-- Username Field -->
                    <div class="mb-6">
                        <label for="nama_admin" class="block text-sm font-medium text-white/90 mb-2">Username:</label>
                        <input 
                            type="text" 
                            name="nama_admin" 
                            id="nama_admin" 
                            required 
                            value="<?php echo isset($_POST['nama_admin']) ? htmlspecialchars($_POST['nama_admin']) : ''; ?>"
                            placeholder="<?php echo $username_error ?: 'Masukkan username'; ?>"
                            class="w-full px-4 py-2.5 bg-white/15 border <?php echo !empty($username_error) ? 'border-red-500 placeholder-red-500' : 'border-white/25'; ?> text-white rounded-lg focus:ring-2 focus:ring-white/40 focus:border-white outline-none placeholder-white/60 autofill:bg-white/15 autofill:text-white"
                            style="
                                -webkit-text-fill-color: white;
                                transition: background-color 5000s ease-in-out 0s;
                            "
                        />
                        <?php if (!empty($username_error)): ?>
                            <div class="text-red-500 text-sm mt-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <?php echo $username_error; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-white/90 mb-2">Password:</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            placeholder="<?php echo $password_error ?: 'Masukkan password'; ?>"
                            class="w-full px-4 py-2.5 bg-white/15 border <?php echo !empty($password_error) ? 'border-red-500 placeholder-red-500' : 'border-white/25'; ?> text-white rounded-lg focus:ring-2 focus:ring-white/40 focus:border-white outline-none placeholder-white/60 autofill:bg-white/15 autofill:text-white"
                            style="
                                -webkit-text-fill-color: white;
                                transition: background-color 5000s ease-in-out 0s;
                            "
                        />
                        <?php if (!empty($password_error)): ?>
                            <div class="text-red-500 text-sm mt-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <?php echo $password_error; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button 
                            type="submit" 
                            name="login" 
                            class="w-full py-3 bg-white/20 text-white rounded-lg hover:bg-white/30 transition duration-300 font-medium text-lg"
                        >
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>