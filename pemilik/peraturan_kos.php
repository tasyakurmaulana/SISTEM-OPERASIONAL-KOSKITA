<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

// Simulasi Data Pengaturan
$tarif_dasar = 800000;
$biaya_tambahan = [
    ['id' => 1, 'nama' => 'Parkir Mobil', 'nominal' => 50000],
    ['id' => 2, 'nama' => 'Tambah Elektronik (Kulkas/TV)', 'nominal' => 30000]
];
$aturan_kos = "1. Jam malam maksimal pukul 22.00 WIB.\n2. Dilarang membawa hewan peliharaan.\n3. Tamu menginap wajib lapor penjaga dan dikenakan biaya tambahan.";

// Notifikasi simulasi jika form disubmit
$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan_sukses = "Pengaturan Harga & Aturan berhasil diperbarui!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Harga & Aturan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Pengaturan Harga & Aturan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Pengaturan Harga Sewa</h3>
                
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif Dasar Kamar (Per Bulan)</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 font-bold">Rp</span>
                        <input type="number" name="tarif_dasar" value="<?php echo $tarif_dasar; ?>" class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Biaya Tambahan (Opsional)</label>
                    <?php foreach($biaya_tambahan as $biaya): ?>
                    <div class="flex gap-2 mb-2">
                        <input type="text" value="<?php echo $biaya['nama']; ?>" class="w-1/2 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50" readonly>
                        <input type="number" value="<?php echo $biaya['nominal']; ?>" class="w-1/3 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50" readonly>
                        <button type="button" class="w-1/6 bg-red-100 text-red-600 rounded-lg font-bold hover:bg-red-200">X</button>
                    </div>
                    <?php endforeach; ?>
                    <button type="button" class="mt-2 text-sm text-emerald-600 font-bold hover:underline">+ Tambah Biaya Baru</button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Aturan & Tata Tertib Kos</h3>
                <textarea name="aturan_kos" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm leading-relaxed"><?php echo $aturan_kos; ?></textarea>
                <p class="text-xs text-gray-500 mt-2">*Aturan ini akan tampil di dashboard penghuni.</p>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</body>
</html>