<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

$pesan = "";

if (isset($_POST['tambah_penghuni'])) {
    $nama = $_POST['nama_lengkap'];
    $kamar = $_POST['nomor_kamar'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt_user = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'penghuni')");
    $stmt_user->bind_param("ss", $username, $password);

    if ($stmt_user->execute()) {
        $id_user_baru = $conn->insert_id;

        $stmt_profil = $conn->prepare("INSERT INTO penghuni (id_user, nama_lengkap, nomor_kamar) VALUES (?, ?, ?)");
        $stmt_profil->bind_param("iss", $id_user_baru, $nama, $kamar);

        if ($stmt_profil->execute()) {
            $pesan = "Penghuni baru berhasil didaftarkan!";
        } else {
            $pesan = "Gagal membuat profil penghuni.";
        }
    } else {
        $pesan = "Gagal membuat akun login (Username mungkin sudah ada).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penghuni - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Input Data Penghuni</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan): ?>
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded shadow-sm font-semibold">
            <?php echo $pesan; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-emerald-50/50">
                <h2 class="text-xl font-bold text-gray-800">Registrasi Penghuni Baru</h2>
                <p class="text-sm text-gray-500 mt-1">Isi formulir untuk membuat akun dan profil penghuni secara otomatis.</p>
            </div>

            <form method="POST" action="" class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Contoh: Indra Febri" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar" placeholder="Contoh: B-04" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Username Baru</label>
                        <input type="text" name="username" placeholder="Untuk login penghuni" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="tambah_penghuni" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200">
                        Daftarkan Penghuni
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-xs text-blue-800 leading-relaxed">
                <b>Catatan:</b> Setelah didaftarkan, berikan username dan password tersebut kepada penghuni agar mereka bisa mulai menggunakan aplikasi KosKita.
            </p>
        </div>
    </div>

</body>
</html>