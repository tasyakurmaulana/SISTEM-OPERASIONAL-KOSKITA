<?php
session_start();
// Panggil file koneksi (pastikan variabel koneksinya bernama $conn)
require 'koneksi.php'; 

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username ke database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password yang sudah di-hash
        if (password_verify($password, $user['password'])) {
            
            // Simpan data ke session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
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
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">KosKita</h1>
            <p class="text-gray-500 text-sm mt-1">Silakan masuk ke akun Anda</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-5 text-sm rounded" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow-sm appearance-none border rounded-lg w-full py-2.5 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" id="username" type="text" name="username" placeholder="Masukkan username" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow-sm appearance-none border rounded-lg w-full py-2.5 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" id="password" type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-200" type="submit" name="login">
                    Masuk
                </button>
            </div>
        </form>
    </div>

</body>
</html>