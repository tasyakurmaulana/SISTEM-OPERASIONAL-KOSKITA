<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') { header("Location: ../login.php"); exit; }

$pembayaran_masuk = [
    ['id' => 1, 'kamar' => 'B-04', 'nama' => 'Indra Febri', 'nominal' => 850000, 'metode' => 'OVO', 'tgl' => '5 Apr 2026', 'status' => 'Pending']
];

$pesan = "";
if (isset($_POST['aksi'])) {
    $pesan = ($_POST['aksi'] == 'terima') ? "Pembayaran berhasil diterima. E-Receipt otomatis dikirim ke penghuni!" : "Pembayaran ditolak.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verifikasi - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-emerald-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Verifikasi Pembayaran</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <?php if($pesan): ?><div class="bg-green-100 text-green-700 p-4 mb-6 rounded-lg font-bold"><?php echo $pesan; ?></div><?php endif; ?>

        <h2 class="text-lg font-bold text-gray-700 mb-4">Butuh Verifikasi</h2>
        
        <?php foreach($pembayaran_masuk as $p): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
            <div class="flex justify-between border-b pb-3 mb-3">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg"><?php echo $p['kamar']; ?> - <?php echo $p['nama']; ?></h3>
                    <p class="text-sm text-gray-500 mt-1"><?php echo $p['tgl']; ?> | Via <?php echo $p['metode']; ?></p>
                </div>
                <div class="text-right">
                    <span class="font-extrabold text-emerald-600 text-lg">Rp <?php echo number_format($p['nominal'], 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="flex gap-3 items-center">
                <button onclick="alert('Menampilkan gambar bukti transfer...')" class="w-1/3 bg-gray-100 text-gray-700 py-2 rounded-lg font-bold text-sm hover:bg-gray-200">Cek Bukti</button>
                <form method="POST" class="w-2/3 flex gap-2">
                    <button type="submit" name="aksi" value="tolak" class="w-1/2 bg-red-100 text-red-600 py-2 rounded-lg font-bold text-sm hover:bg-red-200">Tolak</button>
                    <button type="submit" name="aksi" value="terima" class="w-1/2 bg-emerald-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-emerald-700">Terima</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>