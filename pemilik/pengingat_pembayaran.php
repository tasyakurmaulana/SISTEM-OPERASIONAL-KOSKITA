<?php
session_start();
require '../koneksi.php'; 

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php"); exit;
}

$pesan_sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim'])) {
    $id_tagihan = (int)$_POST['id_tagihan'];
    $pesan = $conn->real_escape_string($_POST['pesan']);

    $update = $conn->query("UPDATE tagihan SET pesan_pengingat = '$pesan', jumlah_diingatkan = jumlah_diingatkan + 1 WHERE id_tagihan = '$id_tagihan'");
    if($update) { $pesan_sukses = "Pengingat terkirim ke dashboard penghuni!"; }
}

$bulan_ini = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'][date('n')];
$tahun_ini = date('Y');

$query_tunggakan = $conn->query("
    SELECT t.id_tagihan, t.total_tagihan, t.jatuh_tempo, t.pesan_pengingat, t.jumlah_diingatkan, p.nomor_kamar, p.nama_lengkap 
    FROM tagihan t 
    JOIN penghuni p ON t.id_penghuni = p.id_penghuni 
    WHERE t.status = 'Belum Lunas' AND t.bulan = '$bulan_ini' AND t.tahun = '$tahun_ini'
    ORDER BY p.nomor_kamar ASC
");

function formatRupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengingat Pembayaran - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-20 sm:pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_pemilik.php" class="mr-3 hover:bg-emerald-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-base sm:text-lg truncate">Kirim Pengingat Tagihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-4 sm:mb-6 rounded shadow-sm text-sm font-semibold">
                <?php echo $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 sm:p-4 mb-6 rounded-xl text-xs sm:text-sm flex items-start shadow-sm">
            <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>Daftar di bawah mencakup <b>seluruh penghuni yang belum lunas</b> bulan ini. Anda bisa mengirimkan pengingat meski belum jatuh tempo.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <ul class="divide-y divide-gray-100">
                <?php if($query_tunggakan && $query_tunggakan->num_rows > 0): ?>
                    <?php while($t = $query_tunggakan->fetch_assoc()): ?>
                    <li class="p-4 sm:p-5 hover:bg-gray-50">
                        
                        <div class="flex flex-col sm:flex-row justify-between items-start mb-3 gap-2">
                            <div>
                                <span class="font-bold text-gray-800 text-base sm:text-lg block">Kamar <?php echo htmlspecialchars($t['nomor_kamar']); ?></span>
                                <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($t['nama_lengkap']); ?></span>
                                <p class="text-xs font-bold mt-1 <?php echo (strtotime($t['jatuh_tempo']) < time()) ? 'text-red-500' : 'text-gray-400'; ?>">
                                    Jatuh Tempo: <?php echo date('d M Y', strtotime($t['jatuh_tempo'])); ?>
                                    <?php if(strtotime($t['jatuh_tempo']) < time()) echo " (Terlambat!)"; ?>
                                </p>
                            </div>
                            
                            <div class="sm:text-right mt-1 sm:mt-0 bg-gray-50 sm:bg-transparent p-2 sm:p-0 rounded-lg w-full sm:w-auto border sm:border-none border-gray-100">
                                <span class="font-black text-gray-800 text-lg sm:text-xl block"><?php echo formatRupiah($t['total_tagihan']); ?></span>
                                <p class="text-[10px] uppercase tracking-tighter font-bold mt-1 <?php echo ($t['jumlah_diingatkan'] > 0) ? 'text-orange-500' : 'text-gray-400'; ?>">
                                    <?php echo ($t['jumlah_diingatkan'] > 0) ? "Diingatkan ".$t['jumlah_diingatkan']."x" : "Belum Diingatkan"; ?>
                                </p>
                            </div>
                        </div>
                        
                        <form method="POST" class="flex flex-col mt-2">
                            <input type="hidden" name="id_tagihan" value="<?php echo $t['id_tagihan']; ?>">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="pesan" class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 w-full" 
                                       value="<?php echo htmlspecialchars($t['pesan_pengingat'] ?? 'Halo, jangan lupa tagihan kos bulan ini ya. Terima kasih!'); ?>">
                                <button type="submit" name="kirim" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition shadow-sm w-full sm:w-auto whitespace-nowrap">
                                    Kirim Notif
                                </button>
                            </div>
                        </form>
                        
                    </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="p-8 text-center text-gray-500 font-medium">Semua penghuni sudah membayar lunas! 🎉</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
