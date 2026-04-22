<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php"); exit;
}

$bulan_ini = date('m');
$tahun_ini = date('Y');

$q_total = $conn->query("SELECT COUNT(id_keluhan) as total FROM keluhan WHERE MONTH(tanggal_pengajuan) = '$bulan_ini' AND YEAR(tanggal_pengajuan) = '$tahun_ini'");
$total_keluhan = $q_total->fetch_assoc()['total'];

$q_selesai = $conn->query("SELECT COUNT(id_keluhan) as selesai FROM keluhan WHERE status = 'Selesai' AND MONTH(tanggal_pengajuan) = '$bulan_ini' AND YEAR(tanggal_pengajuan) = '$tahun_ini'");
$keluhan_selesai = $q_selesai->fetch_assoc()['selesai'];

$keluhan_aktif = $total_keluhan - $keluhan_selesai;

$query_semua = $conn->query("
    SELECT 
        k.tanggal_pengajuan, k.kategori, k.status, k.tanggapan_penjaga,
        p.nomor_kamar
    FROM keluhan k
    JOIN penghuni p ON k.id_penghuni = p.id_penghuni
    ORDER BY k.tanggal_pengajuan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Monitoring Keluhan - Pemilik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="dashboard_pemilik.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
                <div class="font-bold text-lg">Monitoring Keluhan</div>
            </div>
            <button onclick="window.print()" class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-lg flex items-center transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak
            </button>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Statistik Bulan <?php echo date('F Y'); ?></h2>
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-gray-400 text-center">
                <p class="text-xs text-gray-500 font-bold uppercase">Total</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $total_keluhan; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-emerald-500 text-center">
                <p class="text-xs text-emerald-600 font-bold uppercase">Selesai</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $keluhan_selesai; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-red-500 text-center">
                <p class="text-xs text-red-600 font-bold uppercase">Aktif</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $keluhan_aktif; ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-100">
                <h3 class="font-bold text-gray-700">Daftar Seluruh Keluhan Fasilitas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-emerald-50 text-emerald-800 text-sm">
                            <th class="p-3 border-b border-emerald-100 font-semibold">Tgl Masuk</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Kamar</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Kategori</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Status</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Penanganan Penjaga</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php if ($query_semua && $query_semua->num_rows > 0): ?>
                            <?php while($klh = $query_semua->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-gray-600 whitespace-nowrap"><?php echo date('d M Y', strtotime($klh['tanggal_pengajuan'])); ?></td>
                                <td class="p-3 font-bold text-gray-800"><?php echo htmlspecialchars($klh['nomor_kamar']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($klh['kategori']); ?></td>
                                <td class="p-3">
                                    <?php if($klh['status'] == 'Selesai'): ?>
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Selesai</span>
                                    <?php elseif($klh['status'] == 'Diproses'): ?>
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Diproses</span>
                                    <?php else: ?>
                                        <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-gray-500 italic">
                                    <?php echo !empty($klh['tanggapan_penjaga']) ? '"' . htmlspecialchars($klh['tanggapan_penjaga']) . '"' : '<span class="text-gray-400">Belum ada respon</span>'; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data keluhan yang masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
