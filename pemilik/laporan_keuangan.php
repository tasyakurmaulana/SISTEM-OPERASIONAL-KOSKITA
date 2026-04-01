<?php
session_start();

// Proteksi Halaman: Hanya untuk role 'pemilik'
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI QUERY DATABASE ---
$bulan_ini = "April 2026";

// Ringkasan Keuangan
$keuangan = [
    'target_pendapatan' => 17000000,
    'pendapatan_masuk'  => 14500000,
    'total_tunggakan'   => 2500000,
    'kamar_terbayar'    => 17, // dari 20 kamar
    'total_kamar'       => 20
];

// Data Pembayaran Terbaru (Riwayat Transaksi)
$transaksi_terbaru = [
    ['tanggal' => '2026-04-05', 'kamar' => 'A-01', 'nama' => 'Budi Santoso', 'nominal' => 850000, 'metode' => 'Transfer Bank'],
    ['tanggal' => '2026-04-04', 'kamar' => 'A-02', 'nama' => 'Andi Wijaya', 'nominal' => 800000, 'metode' => 'E-Wallet (OVO)'],
    ['tanggal' => '2026-04-02', 'kamar' => 'B-04', 'nama' => 'Indra Febri', 'nominal' => 850000, 'metode' => 'Transfer Bank'],
];

// Data Penghuni Menunggak
$tunggakan = [
    ['kamar' => 'B-01', 'nama' => 'Siti Aminah', 'nominal' => 850000, 'jatuh_tempo' => '2026-04-10'],
    ['kamar' => 'C-01', 'nama' => 'Rina Melati', 'nominal' => 800000, 'jatuh_tempo' => '2026-04-10'],
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
    <title>Laporan Keuangan - Pemilik Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="dashboard.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="font-bold text-lg">Laporan Keuangan</div>
            </div>
            <button class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-lg flex items-center transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </button>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Periode: <?php echo $bulan_ini; ?></h2>
            <select class="bg-white border border-gray-300 text-gray-700 py-1.5 px-3 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option>April 2026</option>
                <option>Maret 2026</option>
                <option>Februari 2026</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-emerald-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Pendapatan Masuk</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo formatRupiah($keuangan['pendapatan_masuk']); ?></h3>
                <p class="text-xs text-emerald-600 font-medium mt-2 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Sesuai Target
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Total Tunggakan</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo formatRupiah($keuangan['total_tunggakan']); ?></h3>
                <p class="text-xs text-red-500 font-medium mt-2">Menunggu pembayaran</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Kamar Terbayar</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo $keuangan['kamar_terbayar']; ?> <span class="text-base font-medium text-gray-400">/ <?php echo $keuangan['total_kamar']; ?></span></h3>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?php echo ($keuangan['kamar_terbayar'] / $keuangan['total_kamar']) * 100; ?>%"></div>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Daftar Tunggakan Bulan Ini</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-red-50 text-red-800 text-sm">
                            <th class="p-3 border-b border-red-100 font-semibold">Kamar</th>
                            <th class="p-3 border-b border-red-100 font-semibold">Nama Penghuni</th>
                            <th class="p-3 border-b border-red-100 font-semibold">Jatuh Tempo</th>
                            <th class="p-3 border-b border-red-100 font-semibold text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php foreach($tunggakan as $t): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-700"><?php echo $t['kamar']; ?></td>
                            <td class="p-3 text-gray-600"><?php echo $t['nama']; ?></td>
                            <td class="p-3 text-red-500"><?php echo date('d M Y', strtotime($t['jatuh_tempo'])); ?></td>
                            <td class="p-3 font-bold text-gray-800 text-right"><?php echo formatRupiah($t['nominal']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Pembayaran Terbaru Masuk</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm">
                            <th class="p-3 border-b border-gray-200 font-semibold">Tanggal</th>
                            <th class="p-3 border-b border-gray-200 font-semibold">Kamar & Nama</th>
                            <th class="p-3 border-b border-gray-200 font-semibold">Metode</th>
                            <th class="p-3 border-b border-gray-200 font-semibold text-right">Nominal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php foreach($transaksi_terbaru as $trx): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-500 whitespace-nowrap"><?php echo date('d M Y', strtotime($trx['tanggal'])); ?></td>
                            <td class="p-3">
                                <span class="font-bold text-gray-800 mr-1"><?php echo $trx['kamar']; ?></span> 
                                <span class="text-gray-500">- <?php echo $trx['nama']; ?></span>
                            </td>
                            <td class="p-3 text-gray-500">
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-xs font-semibold"><?php echo $trx['metode']; ?></span>
                            </td>
                            <td class="p-3 font-bold text-emerald-600 text-right"><?php echo formatRupiah($trx['nominal']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-gray-50 text-center border-t border-gray-100">
                <a href="#" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua Transaksi &rarr;</a>
            </div>
        </div>

    </div>

</body>
</html>