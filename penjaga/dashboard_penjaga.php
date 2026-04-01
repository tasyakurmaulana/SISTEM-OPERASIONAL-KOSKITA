<?php
session_start();


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI DATA PENJAGA ---
// Nanti diganti dengan query database ke tabel staf/penjaga
$nama_penjaga = "Pak Yanto"; 
$status_shift = "Shift Pagi (08:00 - 16:00)";
$keluhan_aktif = 2; // Simulasi ada 2 keluhan yang belum ditangani
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjaga - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-blue-800 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-xl tracking-wide">KosKita <span class="text-sm font-normal text-blue-200">| Penjaga</span></div>
            <a href="../logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 flex items-center justify-between border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Halo, <?php echo $nama_penjaga; ?>! 👮‍♂️</h1>
                <p class="text-gray-500 mt-1">Jadwal: <span class="font-semibold text-blue-600"><?php echo $status_shift; ?></span></p>
            </div>
            <div class="w-14 h-14 bg-blue-800 text-white rounded-full flex items-center justify-center text-xl font-bold shadow-md">
                <?php echo substr($nama_penjaga, 0, 1); ?>
            </div>
        </div>

        <div class="bg-gradient-to-r from-gray-800 to-gray-600 rounded-2xl shadow-md p-6 mb-8 text-white flex items-center justify-between">
            <div>
                <h2 class="text-sm font-medium text-gray-300 mb-1">Status Keluhan Masuk</h2>
                <div class="text-3xl font-bold"><?php echo $keluhan_aktif; ?> <span class="text-lg font-normal">Menunggu</span></div>
            </div>
            <div class="bg-white/20 p-3 rounded-full">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Menu Operasional</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <a href="keluhan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-orange-300 transition group relative">
                <?php if($keluhan_aktif > 0): ?>
                <span class="absolute top-3 right-3 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                <?php endif; ?>
                
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Keluhan</span>
            </a>

            <a href="kebersihan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Kebersihan</span>
            </a>

            <a href="laporan_keuangan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-blue-300 transition group">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Laporan Keuangan</span>
            </a>

            <a href="status_pembayaran.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-purple-300 transition group">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Status Pembayaran</span>
            </a>

        </div>

    </div>

</body>
</html>