<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query_user = $conn->query("SELECT username FROM users WHERE id_user = '$id_user'");
$username = ($query_user && $query_user->num_rows > 0) ? $query_user->fetch_assoc()['username'] : 'Pemilik';
$nama_pemilik = ucfirst($username);

$query_penghuni = $conn->query("SELECT COUNT(id_penghuni) as total_terisi FROM penghuni");
$kamar_terisi = ($query_penghuni) ? $query_penghuni->fetch_assoc()['total_terisi'] : 0;
$total_kamar_tersedia = 20; 

$bulan_array = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$bulan_ini = $bulan_array[date('n')];
$tahun_ini = date('Y');

$query_pendapatan = $conn->query("SELECT SUM(total_tagihan) as total_masuk FROM tagihan WHERE status = 'Lunas' AND bulan = '$bulan_ini' AND tahun = '$tahun_ini'");
$pendapatan = ($query_pendapatan && $query_pendapatan->num_rows > 0) ? $query_pendapatan->fetch_assoc()['total_masuk'] : 0;
if(is_null($pendapatan)) $pendapatan = 0;

$pendapatan_bulan_ini = "Rp " . number_format($pendapatan, 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-xl tracking-wide">KosKita <span class="text-sm font-normal text-emerald-200">| Pemilik</span></div>
            <a href="../logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">Keluar</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 flex items-center justify-between border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, Bapak <?php echo htmlspecialchars($nama_pemilik); ?>! 📈</h1>
                <p class="text-gray-500 mt-1">Okupansi: <span class="font-semibold text-emerald-600"><?php echo $kamar_terisi; ?>/<?php echo $total_kamar_tersedia; ?> Kamar Terisi</span></p>
            </div>
            <div class="w-14 h-14 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xl font-bold shadow-sm border border-emerald-200">
                <?php echo strtoupper(substr($nama_pemilik, 0, 1)); ?>
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl shadow-md p-6 mb-8 text-white flex items-center justify-between relative overflow-hidden">
            <svg class="absolute -right-4 -bottom-4 opacity-20 w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
            
            <div class="relative z-10">
                <h2 class="text-sm font-medium text-emerald-100 mb-1">Pendapatan Bersih (<?php echo $bulan_ini; ?>)</h2>
                <div class="text-3xl font-bold"><?php echo $pendapatan_bulan_ini; ?></div>
            </div>
            <div class="relative z-10 bg-white/20 p-3 rounded-full">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Menu Manajemen</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <a href="laporan_keuangan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-emerald-300 transition group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Laporan Keuangan</span>
            </a>

            <a href="keluhan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-orange-300 transition group">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Monitoring Keluhan</span>
            </a>

            <a href="pengingat_pembayaran.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-blue-300 transition group">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Pengingat Pembayaran</span>
            </a>

            <a href="peraturan_kos.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-purple-300 transition group">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Peraturan Kos</span>
            </a>

            <a href="tambah_penghuni.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-emerald-300 transition group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Data Penghuni</span>
            </a>

            <a href="kelola_tagihan.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-emerald-300 transition group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Buat Tagihan</span>
            </a>

            <a href="status_pembayaran.php" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center justify-center hover:shadow-md hover:border-emerald-300 transition group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Verifikasi Bayar</span>
            </a>
        </div>

    </div>

</body>
</html>
