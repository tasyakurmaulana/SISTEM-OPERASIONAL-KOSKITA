<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

// --- SIMULASI QUERY DATABASE ---
// Di aplikasi aslinya, kamu akan melakukan query JOIN antara tabel 'tagihan' dan 'detail_tagihan'
// berdasarkan id_penghuni yang sedang login.

// Data simulasi tagihan utama
$data_tagihan = [
    'bulan' => 'April 2026',
    'total_tagihan' => 850000,
    'status' => 'Belum Lunas', // 'Belum Lunas', 'Menunggu Konfirmasi', 'Lunas'
    'jatuh_tempo' => '2026-04-10'
];

// Data simulasi rincian/detail tagihan
$detail_tagihan = [
    ['nama_item' => 'Sewa Kamar Standar', 'nominal' => 800000],
    ['nama_item' => 'Tagihan Laundry (Maret)', 'nominal' => 50000]
];

// Data simulasi pengingat dari pemilik (jika ada)
$pesan_pengingat = "Mohon segera melunasi tagihan bulan ini sebelum tanggal jatuh tempo ya. Terima kasih!";

// Fungsi format rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Kos - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen pb-20">

    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Rincian Tagihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if(!empty($pesan_pengingat) && $data_tagihan['status'] == 'Belum Lunas'): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm flex items-start">
            <svg class="w-6 h-6 text-yellow-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="text-sm font-bold text-yellow-800">Pesan dari Pemilik Kos</h3>
                <p class="text-sm text-yellow-700 mt-1"><?php echo $pesan_pengingat; ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="bg-blue-50 p-6 text-center border-b border-gray-100 relative">
                <p class="text-sm text-gray-500 font-medium mb-1">Total Tagihan <?php echo $data_tagihan['bulan']; ?></p>
                <h1 class="text-4xl font-extrabold text-blue-600 tracking-tight"><?php echo formatRupiah($data_tagihan['total_tagihan']); ?></h1>
                
                <div class="mt-4">
                    <?php if($data_tagihan['status'] == 'Lunas'): ?>
                        <span class="bg-green-100 text-green-800 px-4 py-1.5 rounded-full text-sm font-bold border border-green-200">LUNAS</span>
                    <?php elseif($data_tagihan['status'] == 'Menunggu Konfirmasi'): ?>
                        <span class="bg-orange-100 text-orange-800 px-4 py-1.5 rounded-full text-sm font-bold border border-orange-200">MENUNGGU KONFIRMASI</span>
                    <?php else: ?>
                        <span class="bg-red-100 text-red-800 px-4 py-1.5 rounded-full text-sm font-bold border border-red-200">BELUM LUNAS</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="p-4 flex justify-between items-center text-sm">
                <span class="text-gray-500">Jatuh Tempo</span>
                <span class="font-bold text-gray-800"><?php echo date('d M Y', strtotime($data_tagihan['jatuh_tempo'])); ?></span>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-3 px-1">Rincian Biaya</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <ul class="divide-y divide-gray-50">
                <?php foreach($detail_tagihan as $item): ?>
                <li class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                    <span class="text-gray-700 text-sm"><?php echo $item['nama_item']; ?></span>
                    <span class="font-semibold text-gray-800 text-sm"><?php echo formatRupiah($item['nominal']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-between items-center">
                <span class="font-bold text-gray-700">Total Keseluruhan</span>
                <span class="font-extrabold text-blue-600 text-lg"><?php echo formatRupiah($data_tagihan['total_tagihan']); ?></span>
            </div>
        </div>

    </div>

    <?php if($data_tagihan['status'] == 'Belum Lunas'): ?>
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-40">
        <div class="max-w-4xl mx-auto flex gap-3">
            <a href="riwayat_pembayaran.php" class="w-1/3 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition text-sm">
                Riwayat
            </a>
            <a href="bayar_tagihan.php" class="w-2/3 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-sm">
                Bayar Sekarang
            </a>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>