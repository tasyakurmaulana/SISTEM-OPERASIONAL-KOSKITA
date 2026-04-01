<?php
session_start();


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'laundry') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI DATA LAUNDRY ---
// Nanti diganti dengan query database ke tabel staf/laundry
$nama_laundry = "Mbak Ani"; 
$pesanan_aktif = 5; // Simulasi jumlah pesanan yang belum selesai
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Laundry - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-cyan-600 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-xl tracking-wide">KosKita <span class="text-sm font-normal text-cyan-200">| Laundry</span></div>
            <a href="../logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 flex items-center justify-between border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Halo, <?php echo $nama_laundry; ?>! 🫧</h1>
                <p class="text-gray-500 mt-1">Semangat mencuci hari ini!</p>
            </div>
            <div class="w-14 h-14 bg-cyan-100 text-cyan-700 rounded-full flex items-center justify-center text-xl font-bold shadow-sm border border-cyan-200">
                <?php echo substr($nama_laundry, 0, 1); ?>
            </div>
        </div>

        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl shadow-md p-6 mb-8 text-white flex items-center justify-between relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 opacity-20 w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.954C8.775 2.954 6.068 5.253 5.305 8.3c-2.434.542-4.223 2.684-4.223 5.286 0 2.986 2.457 5.412 5.485 5.412h11.836c2.618 0 4.747-2.11 4.747-4.707 0-2.52-1.99-4.576-4.48-4.705C17.962 6.06 15.298 2.954 12 2.954z"></path></svg>
            
            <div class="relative z-10">
                <h2 class="text-sm font-medium text-cyan-100 mb-1">Antrean Cucian Aktif</h2>
                <div class="text-3xl font-bold"><?php echo $pesanan_aktif; ?> <span class="text-lg font-normal">Pesanan</span></div>
            </div>
            <div class="relative z-10 bg-white/20 p-3 rounded-full">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Kelola Pesanan</h3>
        
        <div class="grid grid-cols-2 gap-4">
            
            <a href="tambah_pesanan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center hover:shadow-md hover:border-cyan-300 transition group">
                <div class="w-14 h-14 bg-cyan-50 text-cyan-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-cyan-600 group-hover:text-white transition shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-base font-bold text-gray-700 text-center">Tambah Pesanan</span>
                <p class="text-xs text-gray-400 text-center mt-1">Input cucian baru</p>
            </a>

            <a href="daftar_pesanan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center hover:shadow-md hover:border-blue-300 transition group">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-base font-bold text-gray-700 text-center">Daftar Pesanan</span>
                <p class="text-xs text-gray-400 text-center mt-1">Cek & update status</p>
            </a>

        </div>

    </div>

</body>
</html>