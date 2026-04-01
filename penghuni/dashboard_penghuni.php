<?php
session_start();


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI DATA PENGHUNI ---
// Nanti bagian ini akan diganti dengan query SELECT ke tabel 'penghuni' berdasarkan $_SESSION['id_user']
$nama_penghuni = "Indra Febri"; 
$nomor_kamar = "B-04";
$status_tagihan = "Belum Lunas"; // Bisa "Lunas" atau "Belum Lunas"
$total_tagihan = "Rp 850.000";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penghuni - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-blue-600 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-xl tracking-wide">KosKita</div>
            <a href="../logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 flex items-center justify-between border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Halo, <?php echo $nama_penghuni; ?>! 👋</h1>
                <p class="text-gray-500 mt-1">Kamar: <span class="font-semibold text-blue-600"><?php echo $nomor_kamar; ?></span></p>
            </div>
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl font-bold">
                <?php echo substr($nama_penghuni, 0, 1); ?>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-2xl shadow-md p-6 mb-8 text-white">
            <h2 class="text-sm font-medium text-blue-100 mb-1">Tagihan Bulan Ini</h2>
            <div class="flex justify-between items-end">
                <div class="text-3xl font-bold"><?php echo $total_tagihan; ?></div>
                <?php if($status_tagihan == 'Lunas'): ?>
                    <span class="bg-green-400 text-green-900 text-xs font-bold px-3 py-1 rounded-full">LUNAS</span>
                <?php else: ?>
                    <span class="bg-red-400 text-red-900 text-xs font-bold px-3 py-1 rounded-full animate-pulse">BELUM LUNAS</span>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Menu Utama</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            
            <a href="tagihan_penghuni.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-blue-300 transition group">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Tagihan</span>
            </a>

            <a href="riwayat_pembayaran.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-green-300 transition group">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-green-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Riwayat Pembayaran</span>
            </a>

            <a href="laundry_penghuni.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Laundry</span>
            </a>

            <a href="keluhan_penghuni.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-orange-300 transition group">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Keluhan</span>
            </a>

            <a href="peraturan_kos.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-purple-300 transition group">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Peraturan</span>
            </a>

        </div>
    </div>

</body>
</html>