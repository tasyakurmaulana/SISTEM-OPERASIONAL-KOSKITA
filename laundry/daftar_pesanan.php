<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'laundry') {
    header("Location: ../login.php");
    exit;
}

// Simulasi Data Pesanan Masuk (Sesuai PBI-22)
$daftar_pesanan = [
    ['id_pesanan' => 'LND-1003', 'kamar' => 'B-04', 'nama' => 'Indra Febri', 'tanggal' => '05 Apr 2026', 'status' => 'Menunggu Diambil', 'biaya' => 0],
    ['id_pesanan' => 'LND-1002', 'kamar' => 'A-01', 'nama' => 'Budi Santoso', 'tanggal' => '05 Apr 2026', 'status' => 'Diproses', 'biaya' => 0],
    ['id_pesanan' => 'LND-0988', 'kamar' => 'C-01', 'nama' => 'Rina Melati', 'tanggal' => '01 Apr 2026', 'status' => 'Selesai', 'biaya' => 25000],
];

// Simulasi Notifikasi Aksi
$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];
    if ($aksi == 'proses') {
        $pesan_sukses = "Status pesanan diubah menjadi 'Sedang Diproses'!"; // PBI-23
    } elseif ($aksi == 'selesai') {
        $biaya = $_POST['biaya_akhir'];
        $pesan_sukses = "Pesanan Selesai! Biaya akhir Rp " . number_format($biaya, 0, ',', '.') . " telah otomatis masuk ke tagihan penghuni."; // PBI-23 & PBI-24
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan Laundry - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-cyan-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-cyan-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Daftar Pesanan Laundry</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <button class="bg-cyan-600 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-sm whitespace-nowrap">Semua</button>
            <button class="bg-white border border-gray-300 text-gray-600 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm whitespace-nowrap hover:bg-gray-50">Menunggu</button>
            <button class="bg-white border border-gray-300 text-gray-600 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm whitespace-nowrap hover:bg-gray-50">Diproses</button>
        </div>

        <div class="space-y-4">
            <?php foreach($daftar_pesanan as $p): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start border-b pb-3 mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Kamar <?php echo $p['kamar']; ?> - <?php echo $p['nama']; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">ID: <?php echo $p['id_pesanan']; ?> | Tgl: <?php echo $p['tanggal']; ?></p>
                    </div>
                    <div class="text-right">
                        <?php if($p['status'] == 'Menunggu Diambil'): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Menunggu</span>
                        <?php elseif($p['status'] == 'Diproses'): ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Diproses</span>
                        <?php else: ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Selesai</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($p['status'] == 'Menunggu Diambil'): ?>
                    <form method="POST" class="flex gap-3">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p['id_pesanan']; ?>">
                        <button type="submit" name="aksi" value="proses" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-sm">
                            Ambil & Proses Cucian
                        </button>
                    </form>

                <?php elseif($p['status'] == 'Diproses'): ?>
                    <form method="POST" class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Input Biaya Akhir (Rp)</label>
                        <div class="flex gap-2">
                            <input type="hidden" name="id_pesanan" value="<?php echo $p['id_pesanan']; ?>">
                            <input type="number" name="biaya_akhir" placeholder="Contoh: 25000" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 focus:outline-none" required>
                            <button type="submit" name="aksi" value="selesai" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                                Selesai
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">*Biaya ini akan otomatis ditambahkan ke tagihan kos penghuni.</p>
                    </form>

                <?php elseif($p['status'] == 'Selesai'): ?>
                    <div class="flex justify-between items-center">
                        <span class="font-extrabold text-gray-800">Total: Rp <?php echo number_format($p['biaya'], 0, ',', '.'); ?></span>
                        <button onclick="window.print()" class="text-cyan-600 hover:text-cyan-800 text-sm font-bold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak Struk
                        </button>
                    </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>