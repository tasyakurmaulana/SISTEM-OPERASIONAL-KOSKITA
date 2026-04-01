<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php"); exit;
}

// Simulasi Tagihan yang mau dibayar
$tagihan_bulan = "April 2026";
$total_bayar = 850000;

// Notifikasi sukses
$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan_sukses = "Bukti pembayaran berhasil diunggah! Menunggu konfirmasi pemilik.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tagihan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Bayar Tagihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <?php if($pesan_sukses): ?>
            <div class="bg-green-100 text-green-700 p-4 mb-6 rounded-lg font-semibold"><?php echo $pesan_sukses; ?></div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 text-center">
                <p class="text-gray-500 text-sm">Total yang harus dibayar (<?php echo $tagihan_bulan; ?>)</p>
                <h1 class="text-4xl font-extrabold text-blue-600 mt-2">Rp 850.000</h1>
            </div>

            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Metode Pembayaran (Transfer/E-Wallet)</label>
                    <select name="metode" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500">
                        <option value="BCA">BCA - 1234567890 (a.n Budi Hartono)</option>
                        <option value="OVO">OVO - 08123456789 (a.n Budi Hartono)</option>
                        <option value="DANA">DANA - 08123456789 (a.n Budi Hartono)</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Transfer</label>
                    <input type="file" name="bukti_transfer" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50" required>
                    <p class="text-xs text-gray-400 mt-1">*Format: JPG, PNG. Maksimal 2MB.</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition">Konfirmasi Pembayaran</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>