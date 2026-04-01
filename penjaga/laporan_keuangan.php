<?php
session_start();

// Proteksi Halaman: Hanya untuk role 'penjaga'
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI QUERY DATABASE ---
// Di aplikasi aslinya, data ini diambil menggunakan query SUM() dari tabel 'tagihan' pada bulan berjalan.

$bulan_ini = "April 2026";

// Ringkasan Rekap
$rekap = [
    'total_tagihan_keseluruhan' => 15000000,
    'total_sudah_dibayar' => 12500000,
    'total_belum_dibayar' => 2500000,
];

// Persentase untuk progress bar
$persentase_terkumpul = ($rekap['total_sudah_dibayar'] / $rekap['total_tagihan_keseluruhan']) * 100;

// Daftar Status Pembayaran per Kamar
$daftar_kamar = [
    ['kamar' => 'A-01', 'nama' => 'Budi Santoso', 'nominal' => 850000, 'status' => 'Lunas'],
    ['kamar' => 'A-02', 'nama' => 'Andi Wijaya', 'nominal' => 800000, 'status' => 'Lunas'],
    ['kamar' => 'B-01', 'nama' => 'Siti Aminah', 'nominal' => 850000, 'status' => 'Belum Lunas'],
    ['kamar' => 'B-02', 'nama' => 'Indra Febri', 'nominal' => 850000, 'status' => 'Menunggu Konfirmasi'],
    ['kamar' => 'C-01', 'nama' => 'Rina Melati', 'nominal' => 800000, 'status' => 'Belum Lunas'],
];

function formatRupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Uang Bulanan - Penjaga Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen">

    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Rekap Uang Bulanan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Bulan: <?php echo $bulan_ini; ?></h2>
            <button class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm font-semibold flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Pilih Bulan
            </button>
        </div>

        <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-2xl shadow-md p-6 mb-6 text-white relative overflow-hidden">
            <h3 class="text-sm font-medium text-gray-300 mb-1">Total Target Pendapatan</h3>
            <div class="text-3xl font-bold mb-4"><?php echo formatRupiah($rekap['total_tagihan_keseluruhan']); ?></div>
            
            <div class="w-full bg-gray-600 rounded-full h-2.5 mb-2">
                <div class="bg-green-400 h-2.5 rounded-full" style="width: <?php echo $persentase_terkumpul;