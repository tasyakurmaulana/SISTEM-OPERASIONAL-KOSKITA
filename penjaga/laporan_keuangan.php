<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
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
$periode_tampil = $filter_bulan . " " . $filter_tahun;


$q_target = $conn->query("SELECT SUM(total_tagihan) as target FROM tagihan WHERE bulan = '$filter_bulan' AND tahun = '$filter_tahun'");
$total_target = ($q_target && $q_target->num_rows > 0) ? $q_target->fetch_assoc()['target'] : 0;

if(is_null($total_target)) $total_target = 0; 

$q_lunas = $conn->query("SELECT SUM(total_tagihan) as lunas FROM tagihan WHERE status = 'Lunas' AND bulan = '$filter_bulan' AND tahun = '$filter_tahun'");
$total_sudah_dibayar = ($q_lunas && $q_lunas->num_rows > 0) ? $q_lunas->fetch_assoc()['lunas'] : 0;
if(is_null($total_sudah_dibayar)) $total_sudah_dibayar = 0;


$total_belum_dibayar = $total_target - $total_sudah_dibayar;

if ($total_target > 0) {
    $persentase_terkumpul = ($total_sudah_dibayar / $total_target) * 100;
} else {
    $persentase_terkumpul = 0;
}


$query_daftar = $conn->query("
    SELECT t.total_tagihan, t.status, p.nomor_kamar, p.nama_lengkap 
    FROM tagihan t
    JOIN penghuni p ON t.id_penghuni = p.id_penghuni
    WHERE t.bulan = '$filter_bulan' AND t.tahun = '$filter_tahun'
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Uang Bulanan - Penjaga Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen pb-10">

    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penjaga.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Rekap Uang Bulanan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
            <h2 class="text-xl font-bold text-gray-800">Rekap: <?php echo htmlspecialchars($periode_tampil); ?></h2>
    
            <form method="GET" action="" class="flex items-center gap-2">
                <select name="bulan" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 shadow-sm">
                    <?php foreach($bulan_array as $angka => $nama_bulan): ?>
                        <option value="<?php echo $nama_bulan; ?>" <?php if($filter_bulan == $nama_bulan) echo 'selected'; ?>>
                    <?php echo $nama_bulan; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="tahun" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 shadow-sm">
                    <?php 
                    $tahun_sekarang = date('Y');
                    for($t = $tahun_sekarang - 1; $t <= $tahun_sekarang + 1; $t++): 
                    ?>
                        <option value="<?php echo $t; ?>" <?php if($filter_tahun == $t) echo 'selected'; ?>>
                            <?php echo $t; ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-lg shadow-sm transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-2xl shadow-md p-6 mb-6 text-white relative overflow-hidden">
            <h3 class="text-sm font-medium text-gray-300 mb-1">Total Target Pendapatan</h3>
            <div class="text-3xl font-bold mb-4"><?php echo formatRupiah($total_target); ?></div>
            
            <div class="w-full bg-gray-600 rounded-full h-2.5 mb-2">
                <div class="bg-green-400 h-2.5 rounded-full transition-all duration-1000" style="width: <?php echo $persentase_terkumpul; ?>%"></div>
            </div>
            
            <div class="flex justify-between text-xs font-semibold text-gray-300">
                <span>Terkumpul: <?php echo formatRupiah($total_sudah_dibayar); ?></span>
                <span><?php echo round($persentase_terkumpul); ?>% Selesai</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-8 flex justify-between items-center border-l-4 border-red-500">
            <div>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Sisa Belum Dibayar</h4>
                <p class="text-2xl font-black text-gray-800 mt-1"><?php echo formatRupiah($total_belum_dibayar); ?></p>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Status Pembayaran Penghuni</h3>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <?php if ($query_daftar && $query_daftar->num_rows > 0): ?>
            <ul class="divide-y divide-gray-100">
                <?php while($row = $query_daftar->fetch_assoc()): ?>
                <li class="p-4 hover:bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="mb-2 sm:mb-0">
                        <h4 class="font-bold text-gray-800">Kamar <?php echo htmlspecialchars($row['nomor_kamar']); ?> - <?php echo htmlspecialchars($row['nama_lengkap']); ?></h4>
                        <p class="text-sm font-semibold text-gray-600 mt-1"><?php echo formatRupiah($row['total_tagihan']); ?></p>
                    </div>
                    <div>
                        <?php if($row['status'] == 'Lunas'): ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Lunas</span>
                        <?php elseif($row['status'] == 'Menunggu Konfirmasi'): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Cek Transfer</span>
                        <?php else: ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200 inline-block mb-1">Nunggak</span>
                            <br class="hidden sm:block">
                            <a href="#" onclick="alert('Fitur Chat Penagihan WA segera hadir!')" class="text-[10px] font-bold text-blue-500 hover:underline inline-block sm:mt-1">Tagih via WA &rarr;</a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500 mb-2">Tagihan bulan ini belum dibuat oleh Pemilik Kos.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
