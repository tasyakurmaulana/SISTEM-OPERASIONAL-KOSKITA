<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query_profil = $conn->query("SELECT id_penghuni, nama_lengkap, nomor_kamar FROM penghuni WHERE id_user = '$id_user'");

if ($query_profil && $query_profil->num_rows > 0) {
    $profil = $query_profil->fetch_assoc();
    $id_penghuni = $profil['id_penghuni'];
    $nama_penghuni = $profil['nama_lengkap'];
    $nomor_kamar = $profil['nomor_kamar'];
} else {
    $id_penghuni = 0;
    $nama_penghuni = "Penghuni";
    $nomor_kamar = "-";
}

$bulan_array = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$bulan_ini = $bulan_array[date('n')];
$tahun_ini = date('Y');

$query_tagihan = $conn->query("SELECT total_tagihan, status, pesan_pengingat FROM tagihan WHERE id_penghuni = '$id_penghuni' AND bulan = '$bulan_ini' AND tahun = '$tahun_ini'");

$pesan_dari_pemilik = "";

if ($query_tagihan && $query_tagihan->num_rows > 0) {
    $tagihan = $query_tagihan->fetch_assoc();
    $total_tagihan_angka = $tagihan['total_tagihan'];
    $total_tagihan = "Rp " . number_format($total_tagihan_angka, 0, ',', '.');
    $status_tagihan = $tagihan['status'];
    
    if (!empty($tagihan['pesan_pengingat']) && $status_tagihan !== 'Lunas') {
        $pesan_dari_pemilik = $tagihan['pesan_pengingat'];
    }
} else {
    $total_tagihan_angka = 0;
    $total_tagihan = "Rp 0";
    $status_tagihan = "Belum Ada Tagihan";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penghuni - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }
        
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 relative min-h-screen">

    <div class="fixed top-0 left-0 w-full h-96 bg-gradient-to-b from-blue-100/50 to-transparent -z-10 pointer-events-none"></div>

    <nav class="bg-white/70 backdrop-blur-xl border-b border-white/50 shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <div class="font-black text-xl tracking-tight text-slate-800">KosKita</div>
            </div>
            <a href="../logout.php" class="text-sm bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white px-5 py-2 rounded-full transition-all duration-300 font-bold shadow-sm">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8 animate-fade-in-up">
        
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl shadow-slate-200/50 p-6 sm:p-8 mb-6 flex items-center justify-between border border-white">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Selamat datang kembali,</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($nama_penghuni); ?>! 👋</h1>
                <div class="mt-3 inline-flex items-center bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Kamar <?php echo htmlspecialchars($nomor_kamar); ?></span>
                </div>
            </div>
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-tr from-blue-100 to-indigo-100 text-blue-600 rounded-full flex items-center justify-center text-2xl sm:text-3xl font-black shadow-inner border-4 border-white animate-float">
                <?php echo strtoupper(substr($nama_penghuni, 0, 1)); ?>
            </div>
        </div>

        <?php if($pesan_dari_pemilik): ?>
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-5 mb-6 rounded-2xl shadow-lg shadow-amber-100/50 animate-fade-in-up delay-100 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-200 rounded-full mix-blend-multiply opacity-40 animate-pulse"></div>
            <div class="flex items-start relative z-10">
                <div class="flex-shrink-0 bg-amber-100 p-2 rounded-full">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-xs font-black text-amber-800 uppercase tracking-widest mb-1">Pesan dari Pemilik Kos</h3>
                    <p class="text-sm text-amber-900 font-medium leading-relaxed italic">"<?php echo htmlspecialchars($pesan_dari_pemilik); ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-3xl shadow-2xl shadow-indigo-300/50 p-6 sm:p-8 mb-8 text-white relative overflow-hidden transform transition duration-500 hover:scale-[1.01] animate-fade-in-up delay-100">
            <svg class="absolute right-0 bottom-0 opacity-10 w-48 h-48 transform translate-x-10 translate-y-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path></svg>
            <div class="absolute -top-20 -left-20 w-40 h-40 bg-white rounded-full mix-blend-overlay opacity-20 filter blur-xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm sm:text-base font-semibold text-blue-100 tracking-wide">Tagihan <?php echo $bulan_ini . " " . $tahun_ini; ?></h2>
                    <?php if($status_tagihan == 'Lunas'): ?>
                        <span class="bg-emerald-400 text-emerald-950 text-xs font-black px-4 py-1.5 rounded-full shadow-[0_0_15px_rgba(52,211,153,0.5)] tracking-widest uppercase">LUNAS</span>
                    <?php elseif($status_tagihan == 'Menunggu Konfirmasi'): ?>
                        <span class="bg-amber-400 text-amber-950 text-xs font-black px-4 py-1.5 rounded-full shadow-[0_0_15px_rgba(251,191,36,0.5)] tracking-widest uppercase animate-pulse">DIPROSES</span>
                    <?php elseif($status_tagihan == 'Belum Ada Tagihan'): ?>
                        <span class="bg-white/20 text-white text-xs font-black px-4 py-1.5 rounded-full backdrop-blur-md tracking-widest uppercase">KOSONG</span>
                    <?php else: ?>
                        <span class="bg-rose-500 text-white text-xs font-black px-4 py-1.5 rounded-full shadow-[0_0_15px_rgba(244,63,94,0.6)] tracking-widest uppercase animate-pulse">BELUM BAYAR</span>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-end mt-4">
                    <span class="text-sm font-bold text-blue-200 mb-1 mr-1">Rp</span>
                    <div class="text-4xl sm:text-5xl font-black tracking-tight"><?php echo number_format($total_tagihan_angka, 0, ',', '.'); ?></div>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-black text-slate-800 mb-5 tracking-tight flex items-center">
            Menu Utama
            <span class="ml-3 h-px bg-slate-200 flex-1"></span>
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 animate-fade-in-up delay-200">
            
            <a href="tagihan_penghuni.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-2xl hover:shadow-blue-100 hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 transform group-hover:rotate-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm sm:text-base font-bold text-slate-700 text-center group-hover:text-blue-600 transition-colors">Bayar Tagihan</span>
            </a>

            <a href="riwayat_pembayaran.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-2xl hover:shadow-emerald-100 hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 transform group-hover:-rotate-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-sm sm:text-base font-bold text-slate-700 text-center group-hover:text-emerald-600 transition-colors">Riwayat</span>
            </a>

            <a href="laundry_penghuni.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-2xl hover:shadow-teal-100 hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-16 h-16 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-teal-500 group-hover:text-white transition-colors duration-300 transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <span class="text-sm sm:text-base font-bold text-slate-700 text-center group-hover:text-teal-600 transition-colors">Laundry</span>
            </a>

            <a href="keluhan_penghuni.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-2xl hover:shadow-orange-100 hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300 transform group-hover:-rotate-12">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <span class="text-sm sm:text-base font-bold text-slate-700 text-center group-hover:text-orange-600 transition-colors">Keluhan</span>
            </a>

            <a href="peraturan_kos.php" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center hover:shadow-2xl hover:shadow-purple-100 hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300 transform group-hover:rotate-12">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="text-sm sm:text-base font-bold text-slate-700 text-center group-hover:text-purple-600 transition-colors">Peraturan</span>
            </a>
            
            <div class="bg-slate-50/50 rounded-3xl border border-dashed border-slate-200 p-6 flex flex-col items-center justify-center text-slate-400 opacity-50">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                <span class="text-xs font-bold">Segera Hadir</span>
            </div>
            
        </div>
    </div>

</body>
</html>
