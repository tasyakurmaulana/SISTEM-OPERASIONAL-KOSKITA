<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') { header("Location: ../login.php"); exit; }

$riwayat = [
    ['bulan' => 'April 2026', 'tanggal' => '2026-04-05', 'nominal' => 850000, 'status' => 'Pending'],
    ['bulan' => 'Maret 2026', 'tanggal' => '2026-03-02', 'nominal' => 850000, 'status' => 'Berhasil', 'receipt' => 'REC-0326-001'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Riwayat - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Riwayat Pembayaran</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="space-y-4">
            <?php foreach($riwayat as $r): ?>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Tagihan <?php echo $r['bulan']; ?></h3>
                    <p class="text-xs text-gray-500"><?php echo $r['tanggal']; ?></p>
                    <p class="font-extrabold text-blue-600 mt-1">Rp <?php echo number_format($r['nominal'], 0, ',', '.'); ?></p>
                </div>
                <div class="text-right">
                    <?php if($r['status'] == 'Pending'): ?>
                        <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Diproses</span>
                    <?php else: ?>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Berhasil</span>
                        <a href="#" onclick="alert('Membuka PDF Receipt: <?php echo $r['receipt']; ?>')" class="block mt-2 text-xs font-bold text-blue-500 hover:underline">Lihat E-Receipt &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>