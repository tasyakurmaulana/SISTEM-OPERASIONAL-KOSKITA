<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

$bulan_array = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : $bulan_array[date('n')];
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');


$q_kamar = $conn->query("SELECT COUNT(id_penghuni) as total FROM penghuni");
$total_kamar = ($q_kamar) ? $q_kamar->fetch_assoc()['total'] : 0;
if ($total_kamar == 0) $total_kamar = 1;

$q_target = $conn->query("SELECT SUM(total_tagihan) as target FROM tagihan WHERE bulan = '$filter_bulan' AND tahun = '$filter_tahun'");
$target_pendapatan = ($q_target) ? $q_target->fetch_assoc()['target'] : 0;
$target_pendapatan = $target_pendapatan ?? 0;

$q_lunas = $conn->query("SELECT SUM(total_tagihan) as masuk, COUNT(id_tagihan) as jml_lunas FROM tagihan WHERE status = 'Lunas' AND bulan = '$filter_bulan' AND tahun = '$filter_tahun'");
$data_lunas = $q_lunas->fetch_assoc();
$pendapatan_masuk = $data_lunas['masuk'] ?? 0;
$kamar_terbayar = $data_lunas['jml_lunas'] ?? 0;

$total_tunggakan = $target_pendapatan - $pendapatan_masuk;

$query_tunggakan = $conn->query("
    SELECT p.nomor_kamar, p.nama_lengkap, t.jatuh_tempo, t.total_tagihan, t.status 
    FROM tagihan t 
    JOIN penghuni p ON t.id_penghuni = p.id_penghuni 
    WHERE t.status != 'Lunas' AND t.bulan = '$filter_bulan' AND t.tahun = '$filter_tahun'
    ORDER BY t.status DESC, p.nomor_kamar ASC
");

$query_riwayat = $conn->query("
    SELECT pb.tanggal_bayar, p.nomor_kamar, p.nama_lengkap, pb.jumlah_bayar, m.nama_provider 
    FROM pembayaran pb
    JOIN tagihan t ON pb.id_tagihan = t.id_tagihan
    JOIN penghuni p ON pb.id_penghuni = p.id_penghuni
    JOIN metode_pembayaran m ON pb.id_metode = m.id_metode
    WHERE pb.status_pembayaran = 'Berhasil' AND t.bulan = '$filter_bulan' AND t.tahun = '$filter_tahun'
    ORDER BY pb.tanggal_bayar DESC
    LIMIT 10
");

function formatRupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Pemilik Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white; }
            .shadow-sm, .shadow-md { box-shadow: none !important; border: 1px solid #e5e7eb; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50 no-print">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="dashboard_pemilik.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="font-bold text-lg">Laporan Keuangan</div>
            </div>
            <button onclick="window.print()" class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-lg flex items-center transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </button>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3 no-print">
            <h2 class="text-xl font-bold text-gray-800">Periode: <?php echo htmlspecialchars($filter_bulan . " " . $filter_tahun); ?></h2>
            
            <form method="GET" action="" class="flex items-center gap-2">
                <select name="bulan" class="bg-white border border-gray-300 text-gray-700 py-1.5 px-3 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <?php foreach($bulan_array as $b): ?>
                        <option value="<?php echo $b; ?>" <?php if($filter_bulan == $b) echo 'selected'; ?>><?php echo $b; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="bg-white border border-gray-300 text-gray-700 py-1.5 px-3 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <?php for($t = date('Y') - 1; $t <= date('Y') + 1; $t++): ?>
                        <option value="<?php echo $t; ?>" <?php if($filter_tahun == $t) echo 'selected'; ?>><?php echo $t; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white p-1.5 rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <h2 class="hidden print:block text-2xl font-bold text-center mb-6">Laporan Keuangan KosKita - <?php echo $filter_bulan . " " . $filter_tahun; ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-emerald-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Pendapatan Masuk</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo formatRupiah($pendapatan_masuk); ?></h3>
                <p class="text-xs text-emerald-600 font-medium mt-2 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Target: <?php echo formatRupiah($target_pendapatan); ?>
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Total Tunggakan</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo formatRupiah($total_tunggakan); ?></h3>
                <p class="text-xs text-red-500 font-medium mt-2">Masih ditunggu</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-5">
                <p class="text-sm text-gray-500 font-semibold mb-1">Kamar Terbayar</p>
                <h3 class="text-2xl font-extrabold text-gray-800"><?php echo $kamar_terbayar; ?> <span class="text-base font-medium text-gray-400">/ <?php echo $total_kamar; ?></span></h3>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?php echo ($kamar_terbayar / $total_kamar) * 100; ?>%"></div>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Daftar Tunggakan & Proses Bayar</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-red-50 text-red-800 text-sm">
                            <th class="p-3 border-b border-red-100 font-semibold">Kamar & Nama</th>
                            <th class="p-3 border-b border-red-100 font-semibold">Jatuh Tempo</th>
                            <th class="p-3 border-b border-red-100 font-semibold">Status</th>
                            <th class="p-3 border-b border-red-100 font-semibold text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php if($query_tunggakan && $query_tunggakan->num_rows > 0): ?>
                            <?php while($t = $query_tunggakan->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <span class="font-bold text-gray-700"><?php echo htmlspecialchars($t['nomor_kamar']); ?></span> - 
                                    <span class="text-gray-600"><?php echo htmlspecialchars($t['nama_lengkap']); ?></span>
                                </td>
                                <td class="p-3 text-gray-500"><?php echo date('d M Y', strtotime($t['jatuh_tempo'] ?? date('Y-m-10'))); ?></td>
                                <td class="p-3">
                                    <?php if($t['status'] == 'Menunggu Konfirmasi'): ?>
                                        <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold">Cek Transfer</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Belum Bayar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 font-bold text-gray-800 text-right"><?php echo formatRupiah($t['total_tagihan']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="p-4 text-center text-gray-500 font-medium">Bagus! Tidak ada tunggakan bulan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Riwayat Pembayaran Masuk</h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm">
                            <th class="p-3 border-b border-gray-200 font-semibold">Tanggal & Waktu</th>
                            <th class="p-3 border-b border-gray-200 font-semibold">Kamar & Nama</th>
                            <th class="p-3 border-b border-gray-200 font-semibold">Metode</th>
                            <th class="p-3 border-b border-gray-200 font-semibold text-right">Nominal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php if($query_riwayat && $query_riwayat->num_rows > 0): ?>
                            <?php while($trx = $query_riwayat->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-gray-500 whitespace-nowrap"><?php echo date('d M Y, H:i', strtotime($trx['tanggal_bayar'])); ?></td>
                                <td class="p-3">
                                    <span class="font-bold text-gray-800 mr-1"><?php echo htmlspecialchars($trx['nomor_kamar']); ?></span> 
                                    <span class="text-gray-500">- <?php echo htmlspecialchars($trx['nama_lengkap']); ?></span>
                                </td>
                                <td class="p-3 text-gray-500">
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-[11px] font-bold"><?php echo htmlspecialchars($trx['nama_provider']); ?></span>
                                </td>
                                <td class="p-3 font-bold text-emerald-600 text-right">+ <?php echo formatRupiah($trx['jumlah_bayar']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="p-4 text-center text-gray-500 font-medium">Belum ada pembayaran lunas bulan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
