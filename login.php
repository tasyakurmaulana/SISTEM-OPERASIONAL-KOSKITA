<?php
session_start();
require 'koneksi.php'; 

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'penghuni') {
                header("Location: penghuni/dashboard_penghuni.php");
            } else if ($user['role'] == 'pemilik') {
                header("Location: pemilik/dashboard_pemilik.php");
            } else if ($user['role'] == 'penjaga') {
                header("Location: penjaga/dashboard_penjaga.php");
            } else if ($user['role'] == 'laundry') {
                header("Location: laundry/dashboard_laundry.php");
            }
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        @keyframes gradient-xy {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient-xy {
            background-size: 400% 400%;
            animation: gradient-xy 15s ease infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-indigo-800 to-emerald-800 animate-gradient-xy flex items-center justify-center min-h-screen px-4 font-sans text-gray-800">

    <div class="relative w-full max-w-md pt-20">
        
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-48 h-48 z-20 pointer-events-none -mt-12">
            <lottie-player 
                src="uploads/running_cat.json" 
                background="transparent" 
                speed="1" 
                style="width: 100%; height: 100%;" 
                loop 
                autoplay>
            </lottie-player>
        </div>

        <div class="bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl relative overflow-hidden border border-white/20 z-10 pt-12">
            
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-500 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-emerald-500 rounded-full mix-blend-multiply filter blur-2xl opacity-30"></div>

            <div class="text-center mb-8 relative z-10">
                <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-700 tracking-tight">KosKita</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Sistem Manajemen Kos Terpadu</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 mb-6 text-sm rounded-xl flex items-start shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-semibold"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="relative z-10">
                <div class="mb-5">
                    <label class="block text-gray-600 text-xs font-bold mb-2 uppercase tracking-wide" for="username">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-inner" id="username" type="text" name="username" placeholder="Masukkan username Anda" required>
                    </div>
                </div>
                
                <div class="mb-8">
                    <label class="block text-gray-600 text-xs font-bold mb-2 uppercase tracking-wide" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-inner" id="password" type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <button class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-blue-500/30 flex justify-center items-center" type="submit" name="login">
                    <span>Mulai Sesi</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
