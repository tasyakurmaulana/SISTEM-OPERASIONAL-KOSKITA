<?php
session_start();
require '../koneksi.php';


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];


$query_user = $conn->query("SELECT username FROM users WHERE id_user = '$id_user'");
$username_penjaga = ($query_user && $query_user->num_rows > 0) ? $query_user->fetch_assoc()['username'] : 'Penjaga';
$nama_penjaga = ucfirst($username_penjaga); 


$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tanggal_sekarang = $hari[date('w')] . ', ' . date('j') . ' ' . $bulan[date('n')] . ' ' . date('Y');


$query_keluhan = $conn->query("SELECT COUNT(id_keluhan) as total FROM keluhan WHERE status = 'Menunggu'");
$keluhan_aktif = ($query_keluhan) ? $query_keluhan->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjaga - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-slide-down { animation: slideDown 0.6s ease-out forwards; }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen relative">

    <div class="fixed top-0 left-0 w-full h-80 bg-gradient-to-b from-slate-200/50 to-transparent -z-10 pointer-events-none"></div>

    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div class="font-black text-xl tracking-tight text-slate-800">KosKita <span class="text-sm font-semibold text-slate-400 ml-1">| Penjaga</span></div>
            </div>
            <a href="../logout.php" class="text-sm bg-slate-100 text-slate-600 hover:bg-rose-500 hover:text-white px-5 py-2 rounded-full transition-all duration-300 font-bold">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8 animate-slide-down">
        
        <div class="bg-white rounded-3xl shadow-lg shadow-slate-200/50 p-6 sm:p-8 mb-6 flex items-center justify-between border border-slate-100">
            <div>
                <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mb-1">Status: Siaga</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Halo, Pak <?php echo htmlspecialchars($nama_penjaga); ?>! 👮‍♂️</h1>
                <div class="mt-3 inline-flex items-center bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <svg class="w-4 h-4 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-xs font-bold text-slate-600"><?php echo $tanggal_sekarang; ?></span>
                </div>
            </div>
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-800 text-white rounded-2xl flex items-center justify-center text-2xl sm:text-3xl font-black shadow-lg transform rotate-3 hover:rotate-0 transition duration-300">
                <?php echo strtoupper(substr($nama_penjaga, 0, 1)); ?>
            </div>
        </div>

        <div class="bg-slate-800 rounded-3xl shadow-xl shadow-slate-900/20 p-6 sm:p-8 mb-8 text-white relative overflow-hidden animate-fade-in delay-100">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-slate-700 rounded-full mix-blend-overlay opacity-50 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-slate-600 rounded-full mix-blend-overlay opacity-30 blur-xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-400 tracking-wider uppercase mb-1">Status Keluhan Masuk</h2>
                    <div class="flex items-baseline gap-2">
                        <div class="text-5xl font-black tracking-tighter text-white"><?php echo $keluhan_aktif; ?></div>
                        <span class="text-lg font-medium text-slate-300">Menunggu</span>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="bg-slate-700/50 backdrop-blur-sm p-4 rounded-2xl border border-slate-600">
                        <svg class="w-10 h-10 text-slate-200 <?php echo ($keluhan_aktif > 0) ? 'animate-bounce text-orange-400' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <?php if($keluhan_aktif > 0): ?>
                        <span class="absolute -top-2 -right-2 flex h-5 w-5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-5 w-5 bg-rose-500 border-2 border-slate-800"></span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-black text-slate-800 mb-5 tracking-tight flex items-center">
            Menu Operasional
            <span class="ml-3 h-px bg-slate-200 flex-1"></span>
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 animate-fade-in delay-200">
            
            <a href="keluhan.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-xl hover:shadow-orange-100 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <?php if($keluhan_aktif > 0): ?>
                    <div class="absolute top-0 right-0 w-16 h-16 bg-orange-500 transform rotate-45 translate-x-8 -translate-y-8 flex items-end justify-center pb-1">
                        <span class="text-[10px] font-bold text-white transform -rotate-45">NEW</span>
                    </div>
                <?php endif; ?>
                
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300 transform group-hover:scale-110">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <span class="text-sm font-bold text-slate-700 text-center group-hover:text-orange-600 transition-colors">Keluhan</span>
            </a>

            <a href="kebersihan.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-xl hover:shadow-emerald-100 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 transform group-hover:rotate-12">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="text-sm font-bold text-slate-700 text-center group-hover:text-emerald-600 transition-colors">Kebersihan</span>
            </a>

            <a href="laporan_keuangan.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-xl hover:shadow-blue-100 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 transform group-hover:-rotate-12">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-bold text-slate-700 text-center group-hover:text-blue-600 transition-colors">Keuangan</span>
            </a>

            <a href="status_pembayaran.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-xl hover:shadow-purple-100 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300 transform group-hover:scale-110">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <span class="text-sm font-bold text-slate-700 text-center group-hover:text-purple-600 transition-colors">Pembayaran</span>
            </a>

        </div>

    </div>

</body>
</html>
