<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

// Simulasi Data Riwayat Pesanan Penghuni
$pesanan_saya = [
    ['id_pesanan' => 'LND-1002', 'tanggal' => '05 Apr 2026', 'status' => 'Diproses', 'total' => 0], // Total 0 karena belum dihitung mbak laundry
    ['id_pesanan' => 'LND-0988', 'tanggal' => '01 Apr 2026', 'status' => 'Selesai', 'total' => 25000]
];

$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan_sukses = "Pesanan laundry berhasil dibuat! Silakan taruh keranjang cucian di depan kamar, Mbak Laundry akan segera mengambilnya.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-teal-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-teal-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Layanan Laundry</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Berhasil!</p>
            <p class="text-sm mt-1"><?php echo $pesan_sukses; ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-teal-100 p-6 mb-8 text-center relative overflow-hidden">
            <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Pakaian Sudah Menumpuk?</h2>
            <p class="text-sm text-gray-500 mb-6">Klik tombol di bawah ini untuk memanggil Mbak Laundry. Biaya akan otomatis diakumulasikan ke tagihan kos bulan depan.</p>
            
            <form method="POST" action="">
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-lg">
                    Pesan Laundry Sekarang
                </button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Status Cucian Saya</h3>
        <div class="space-y-4">
            <?php foreach($pesanan_saya as $pesanan): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-800"><?php echo $pesanan['id_pesanan']; ?></h4>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $pesanan['tanggal']; ?></p>
                    
                    <?php if($pesanan['total'] > 0): ?>
                        <p class="font-extrabold text-teal-600 mt-2">Rp <?php echo number_format($pesanan['total'], 0, ',', '.'); ?></p>
                    <?php else: ?>
                        <p class="text-xs text-orange-500 italic mt-2">Menunggu dihitung</p>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <?php if($pesanan['status'] == 'Selesai'): ?>
                        <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-xs font-bold border border-green-200">SELESAI</span>
                    <?php elseif($pesanan['status'] == 'Diproses'): ?>
                        <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-bold border border-blue-200">DIPROSES</span>
                    <?php else: ?>
                        <span class="bg-orange-100 text-orange-700 px-3 py-1.5 rounded-full text-xs font-bold border border-orange-200">MENUNGGU</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>