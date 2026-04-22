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


$query = "
    SELECT 
        p.nomor_kamar, 
        p.nama_lengkap, 
        t.total_tagihan, 
        t.status AS status_bayar
    FROM penghuni p
    LEFT JOIN tagihan t ON p.id_penghuni = t.id_penghuni 
                       AND t.bulan = '$filter_bulan' 
                       AND t.tahun = '$filter_tahun'
    ORDER BY p.nomor_kamar ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - Penjaga Kos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="dashboard_penjaga.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="font-bold text-lg">Status Pembayaran</div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3 border-b pb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Penghuni</h2>
                <p class="text-sm text-gray-500 mt-1">Status Tagihan Periode Ini</p>
            </div>
            
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

                <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white p-2.5 rounded-lg shadow-sm transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <ul class="divide-y divide-gray-100">
                <?php 
                if ($result && $result->num_rows > 0):
                    while($row = $result->fetch_assoc()): 
                        $status = $row['status_bayar'] ?? 'Belum Dibuat';
                ?>
                <li class="p-4 hover:bg-gray-50 transition flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-extrabold mr-4 border border-blue-200">
                            <?php echo htmlspecialchars($row['nomor_kamar']); ?>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-base"><?php echo htmlspecialchars($row['nama_lengkap']); ?></p>
                            <?php if ($row['total_tagihan']): ?>
                                <p class="text-xs text-gray-500 mt-0.5">Tagihan: Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></p>
                            <?php else: ?>
                                <p class="text-xs text-gray-400 mt-0.5 italic">Belum ada tagihan masuk</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <?php if($status == 'Lunas'): ?>
                            <span class="inline-flex items-center bg-green-100 text-green-800 text-xs px-3 py-1.5 rounded-full font-bold border border-green-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                LUNAS
                            </span>
                        <?php elseif($status == 'Menunggu Konfirmasi'): ?>
                            <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs px-3 py-1.5 rounded-full font-bold border border-orange-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                CEK TRANSFER
                            </span>
                        <?php elseif($status == 'Belum Lunas'): ?>
                            <span class="inline-flex items-center bg-red-100 text-red-800 text-xs px-3 py-1.5 rounded-full font-bold border border-red-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                NUNGGAK
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center bg-gray-100 text-gray-600 text-xs px-3 py-1.5 rounded-full font-bold border border-gray-200">
                                KOSONG
                            </span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <li class="p-6 text-center text-gray-500 font-medium">
                        Belum ada data penghuni di database.
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</body>
</html>