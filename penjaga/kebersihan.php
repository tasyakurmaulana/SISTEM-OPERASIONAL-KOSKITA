<?php
session_start();
// Proteksi Halaman: Hanya untuk role 'penjaga'
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php");
    exit;
}

// Simulasi Data Kamar untuk Dropdown
$daftar_kamar = ['A-01', 'A-02', 'B-01', 'B-02', 'C-01'];

// Simulasi Data Riwayat Checklist Kebersihan (PBI-29)
$riwayat_kebersihan = [
    ['kamar' => 'A-01', 'tanggal' => '05 Apr 2026, 09:00', 'status' => 'Bersih', 'catatan' => 'Kamar rapi, tidak ada sampah di luar.'],
    ['kamar' => 'B-02', 'tanggal' => '05 Apr 2026, 09:15', 'status' => 'Kotor', 'catatan' => 'Ada tumpukan piring kotor dan sampah di depan pintu.'],
    ['kamar' => 'C-01', 'tanggal' => '04 Apr 2026, 16:00', 'status' => 'Kurang Bersih', 'catatan' => 'Lantai teras kamar agak berdebu.']
];

$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Di aplikasi asli, di sini adalah proses INSERT INTO checklist_kebersihan
    $kamar_dicek = $_POST['kamar'];
    $pesan_sukses = "Checklist kebersihan untuk kamar $kamar_dicek berhasil disimpan!"; // PBI-28
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kebersihan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Checklist Kebersihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Form Pengecekan Harian</h2>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kamar</label>
                    <select name="kamar" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50 text-gray-700">
                        <option value="" disabled selected>-- Pilih Nomor Kamar --</option>
                        <?php foreach($daftar_kamar as $k): ?>
                            <option value="<?php echo $k; ?>">Kamar <?php echo $k; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Kebersihan Area Kamar</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Bersih" class="peer sr-only" required>
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">✨</span>
                                <span class="text-xs font-bold">Bersih</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Kurang Bersih" class="peer sr-only">
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-orange-50 peer-checked:border-orange-500 peer-checked:text-orange-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">🍂</span>
                                <span class="text-xs font-bold">Kurang</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Kotor" class="peer sr-only">
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">🗑️</span>
                                <span class="text-xs font-bold">Kotor</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Pengecekan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: Ada sampah botol minum di depan kamar..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
                    Simpan Laporan Kebersihan
                </button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Riwayat Pengecekan Terbaru</h3>
        <div class="space-y-4">
            <?php foreach($riwayat_kebersihan as $riwayat): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Kamar <?php echo $riwayat['kamar']; ?></h4>
                        <p class="text-xs text-gray-500 mt-0.5"><?php echo $riwayat['tanggal']; ?></p>
                    </div>
                    <div>
                        <?php if($riwayat['status'] == 'Bersih'): ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Bersih</span>
                        <?php elseif($riwayat['status'] == 'Kurang Bersih'): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Kurang Bersih</span>
                        <?php else: ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Kotor</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($riwayat['catatan']): ?>
                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600">
                        <span class="font-semibold text-gray-700">Catatan:</span> <?php echo $riwayat['catatan']; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>