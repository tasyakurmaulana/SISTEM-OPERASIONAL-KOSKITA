<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

$query_aturan = $conn->query("SELECT aturan_kos FROM pengaturan_aturan WHERE id = 1");
if ($query_aturan && $query_aturan->num_rows > 0) {
    $data_aturan = $query_aturan->fetch_assoc();
    $teks_aturan_mentah = $data_aturan['aturan_kos'];
} else {
    $teks_aturan_mentah = "Belum ada peraturan yang diatur oleh pemilik kos.";
}

$aturan_kos = array_values(array_filter(array_map('trim', explode("\n", $teks_aturan_mentah))));

$query_dasar = $conn->query("SELECT nominal FROM pengaturan_harga WHERE id_pengaturan = 1");
$tarif_dasar = ($query_dasar && $query_dasar->num_rows > 0) ? $query_dasar->fetch_assoc()['nominal'] : 0;

$query_tambahan = $conn->query("SELECT * FROM pengaturan_harga WHERE id_pengaturan > 1");
$biaya_tambahan = [];
if ($query_tambahan) {
    while ($row = $query_tambahan->fetch_assoc()) {
        $biaya_tambahan[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peraturan & Harga Kos - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Informasi Kos</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-md p-6 mb-6 text-white text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold mb-1">Peraturan & Informasi Harga</h1>
            <p class="text-sm text-indigo-100">Transparansi biaya dan tata tertib KosKita.</p>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-3 ml-1">Rincian Biaya Sewa</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
            
            <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">Tarif Dasar Kamar</h4>
                        <p class="text-xs text-gray-500">Per Bulan</p>
                    </div>
                </div>
                <div class="font-extrabold text-blue-600 text-lg">
                    Rp <?php echo number_format($tarif_dasar, 0, ',', '.'); ?>
                </div>
            </div>

            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Biaya Tambahan (Opsional)</h4>
            <?php if (empty($biaya_tambahan)): ?>
                <p class="text-sm text-gray-500 italic">Tidak ada biaya tambahan.</p>
            <?php else: ?>
                <ul class="space-y-3">
                    <?php foreach($biaya_tambahan as $biaya): ?>
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($biaya['nama_biaya']); ?></span>
                        <span class="text-sm font-bold text-gray-800">+ Rp <?php echo number_format($biaya['nominal'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-3 ml-1">Tata Tertib KosKita</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <ul class="divide-y divide-gray-100">
                <?php if(empty($aturan_kos)): ?>
                    <li class="p-5 text-center text-gray-500">Tidak ada peraturan yang ditampilkan.</li>
                <?php else: ?>
                    <?php 
                    foreach($aturan_kos as $index => $aturan): 
                        $aturan_bersih = preg_replace('/^[0-9]+\.\s*/', '', $aturan);
                    ?>
                    <li class="p-5 flex items-start hover:bg-gray-50 transition">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center text-sm mr-4 mt-0.5 shadow-sm">
                            <?php echo $index + 1; ?>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed pt-1">
                            <?php echo htmlspecialchars($aturan_bersih); ?>
                        </p>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>

</body>
</html>