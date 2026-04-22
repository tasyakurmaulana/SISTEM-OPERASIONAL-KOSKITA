<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') { 
    header("Location: ../login.php"); 
    exit; 
}

$id_user = $_SESSION['id_user'];

$query_penghuni = $conn->query("SELECT id_penghuni FROM penghuni WHERE id_user = '$id_user'");
$id_penghuni = ($query_penghuni->num_rows > 0) ? $query_penghuni->fetch_assoc()['id_penghuni'] : 0;

$query_riwayat = $conn->query("
    SELECT 
        t.bulan, 
        t.tahun, 
        p.id_pembayaran,
        p.tanggal_bayar, 
        p.jumlah_bayar, 
        p.status_pembayaran 
    FROM pembayaran p
    JOIN tagihan t ON p.id_tagihan = t.id_tagihan
    WHERE p.id_penghuni = '$id_penghuni'
    ORDER BY p.tanggal_bayar DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penghuni.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Riwayat Pembayaran</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="space-y-4">
            
            <?php if ($query_riwayat && $query_riwayat->num_rows > 0): ?>
                <?php while($r = $query_riwayat->fetch_assoc()): ?>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800">Tagihan <?php echo htmlspecialchars($r['bulan'] . ' ' . $r['tahun']); ?></h3>
                        <p class="text-xs text-gray-500"><?php echo date('d M Y, H:i', strtotime($r['tanggal_bayar'])); ?></p>
                        <p class="font-extrabold text-blue-600 mt-1">Rp <?php echo number_format($r['jumlah_bayar'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        
                        <?php if($r['status_pembayaran'] == 'Pending'): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">Diproses</span>
                        
                        <?php elseif($r['status_pembayaran'] == 'Ditolak'): ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Ditolak</span>
                            <p class="text-[10px] text-red-500 mt-1">Silakan upload ulang</p>

                        <?php else: ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Berhasil</span>
                            <a href="cetak_receipt.php?id=<?php echo $r['id_pembayaran']; ?>" class="block mt-2 text-xs font-bold text-blue-500 hover:underline">
                                Lihat E-Receipt &rarr;
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-8 rounded-2xl shadow-sm text-center border border-gray-100">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-gray-700 font-bold text-lg mb-1">Belum Ada Riwayat</h3>
                    <p class="text-gray-500 text-sm">Kamu belum pernah melakukan pembayaran tagihan.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
