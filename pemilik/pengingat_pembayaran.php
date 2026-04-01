<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

// Simulasi Data Penghuni Menunggak
$tunggakan = [
    ['id_tagihan' => 101, 'kamar' => 'B-01', 'nama' => 'Siti Aminah', 'nominal' => 850000, 'jatuh_tempo' => '2026-04-10', 'status_ingat' => 'Belum Diingatkan'],
    ['id_tagihan' => 102, 'kamar' => 'C-01', 'nama' => 'Rina Melati', 'nominal' => 800000, 'jatuh_tempo' => '2026-04-10', 'status_ingat' => 'Sudah Diingatkan (1x)']
];

$pesan_sukses = "";
if (isset($_POST['kirim'])) {
    $pesan_sukses = "Pesan pengingat berhasil dikirim ke penghuni terkait!";
}

function formatRupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Kirim Pengingat Tagihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 mb-6 rounded-xl text-sm flex items-start shadow-sm">
            <svg class="w-5 h-5 mr-2 mt-0.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>Fitur ini akan mengirimkan pesan notifikasi langsung ke dashboard penghuni. Gunakan bahasa yang sopan namun tegas.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <ul class="divide-y divide-gray-100">
                <?php foreach($tunggakan as $t): ?>
                <li class="p-5 hover:bg-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="font-bold text-gray-800 text-lg"><?php echo $t['kamar']; ?> - <?php echo $t['nama']; ?></span>
                            <p class="text-sm text-red-500 font-semibold mt-1">Jatuh Tempo: <?php echo date('d M Y', strtotime($t['jatuh_tempo'])); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="font-extrabold text-gray-800 text-lg"><?php echo formatRupiah($t['nominal']); ?></span>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $t['status_ingat']; ?></p>
                        </div>
                    </div>
                    
                    <form method="POST" action="" class="flex gap-2">
                        <input type="hidden" name="id_tagihan" value="<?php echo $t['id_tagihan']; ?>">
                        <input type="text" name="pesan" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Ketik pesan pengingat..." value="Halo, mohon segera melunasi tagihan bulan ini ya. Terima kasih!">
                        <button type="submit" name="kirim" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                            Kirim Notif
                        </button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</body>
</html>