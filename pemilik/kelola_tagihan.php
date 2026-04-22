<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php"); exit;
}

$pesan = "";
$bulan_array = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
$bulan_ini = $bulan_array[date('n')];
$tahun_ini = date('Y');


if (isset($_POST['buat_tagihan_massal'])) {
    
    $q_tarif = $conn->query("SELECT nominal FROM pengaturan_harga WHERE id_pengaturan = 1");
    $tarif_dasar = ($q_tarif->num_rows > 0) ? (float)$q_tarif->fetch_assoc()['nominal'] : 0;
    
    $biaya_tambahan = [];
    $total_tambahan = 0;
    $q_tambahan = $conn->query("SELECT nama_biaya, nominal FROM pengaturan_harga WHERE id_pengaturan > 1");
    while($row = $q_tambahan->fetch_assoc()) {
        $biaya_tambahan[] = $row;
        $total_tambahan += (float)$row['nominal'];
    }

    $grand_total = $tarif_dasar + $total_tambahan;
    $jatuh_tempo = date('Y-m-10'); 

    $q_penghuni = $conn->query("SELECT id_penghuni FROM penghuni");
    $sukses = 0;
    
    while ($p = $q_penghuni->fetch_assoc()) {
        $id_p = $p['id_penghuni'];
        $cek_dobel = $conn->query("SELECT id_tagihan FROM tagihan WHERE id_penghuni='$id_p' AND bulan='$bulan_ini' AND tahun='$tahun_ini'");
        
        if ($cek_dobel->num_rows == 0) {
            $conn->query("INSERT INTO tagihan (id_penghuni, bulan, tahun, total_tagihan, status, jatuh_tempo) 
                          VALUES ('$id_p', '$bulan_ini', '$tahun_ini', '$grand_total', 'Belum Lunas', '$jatuh_tempo')");
            $id_tagihan_baru = $conn->insert_id;
            
            $conn->query("INSERT INTO detail_tagihan (id_tagihan, nama_item, nominal) 
                          VALUES ('$id_tagihan_baru', 'Tarif Dasar Kamar', '$tarif_dasar')");
            
            foreach ($biaya_tambahan as $tambahan) {
                $nama_item = $conn->real_escape_string($tambahan['nama_biaya']);
                $nominal_item = $tambahan['nominal'];
                $conn->query("INSERT INTO detail_tagihan (id_tagihan, nama_item, nominal) 
                              VALUES ('$id_tagihan_baru', '$nama_item', '$nominal_item')");
            }
            $sukses++;
        }
    }
    $pesan = "Berhasil membuat $sukses tagihan dengan total Rp " . number_format($grand_total, 0, ',', '.') . "/kamar.";
}


if (isset($_POST['reset_tagihan_massal'])) {
    $q_hapus = $conn->query("SELECT id_tagihan FROM tagihan WHERE bulan='$bulan_ini' AND tahun='$tahun_ini'");
    $terhapus = 0;
    while($row = $q_hapus->fetch_assoc()){
        $id_del = $row['id_tagihan'];
        $conn->query("DELETE FROM detail_tagihan WHERE id_tagihan='$id_del'");
        $conn->query("DELETE FROM pembayaran WHERE id_tagihan='$id_del'");
        $conn->query("DELETE FROM tagihan WHERE id_tagihan='$id_del'");
        $terhapus++;
    }
    if ($terhapus > 0) {
        $pesan = "HARD RESET BERHASIL! $terhapus data tagihan bulan ini telah dihapus total dari sistem.";
    } else {
        $pesan = "Tidak ada tagihan yang bisa di-reset bulan ini.";
    }
}


$cek_tagihan = $conn->query("SELECT COUNT(id_tagihan) as total FROM tagihan WHERE bulan='$bulan_ini' AND tahun='$tahun_ini'");
$tagihan_dibuat = $cek_tagihan->fetch_assoc()['total'];

$query_list = $conn->query("
    SELECT t.id_tagihan, p.nomor_kamar, p.nama_lengkap, t.total_tagihan, t.status 
    FROM tagihan t 
    JOIN penghuni p ON t.id_penghuni = p.id_penghuni 
    WHERE t.bulan='$bulan_ini' AND t.tahun='$tahun_ini'
    ORDER BY p.nomor_kamar ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tagihan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_pemilik.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Kelola Tagihan Bulan Ini</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold">
                <?php echo $pesan; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 text-center flex flex-col items-center">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Periode: <?php echo $bulan_ini . " " . $tahun_ini; ?></h2>
            
            <?php if ($tagihan_dibuat == 0): ?>
                <div class="bg-orange-50 text-orange-800 p-4 rounded-xl mb-4 border border-orange-100 text-sm w-full">
                    Tagihan untuk bulan ini <b>belum dibuat</b>. Klik tombol di bawah untuk men-generate tagihan ke semua penghuni secara otomatis.
                </div>
                <form method="POST">
                    <button type="submit" name="buat_tagihan_massal" onclick="return confirm('Buat tagihan untuk semua penghuni bulan ini?')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Generate Tagihan Massal
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-100 text-sm mb-4 w-full">
                    ✅ Tagihan bulan ini sudah berhasil di-generate untuk <b><?php echo $tagihan_dibuat; ?> kamar</b>.
                </div>
                
                <form method="POST">
                    <button type="submit" name="reset_tagihan_massal" onclick="return confirm('⚠️ PERINGATAN KERAS! ⚠️\n\nYakin ingin mereset/menghapus SEMUA tagihan bulan ini?\n\nTagihan yang SUDAH LUNAS juga akan ikut terhapus dan data pembayarannya akan hilang permanen dari sistem!')" class="bg-red-100 hover:bg-red-200 text-red-700 font-bold py-2.5 px-5 rounded-lg border border-red-300 transition inline-flex items-center text-sm shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Total Tagihan Bulan Ini
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($tagihan_dibuat > 0): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-700">Daftar Tagihan Penghuni</h3>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php while($row = $query_list->fetch_assoc()): ?>
                <li class="p-4 hover:bg-gray-50 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Kamar <?php echo htmlspecialchars($row['nomor_kamar']); ?> - <?php echo htmlspecialchars($row['nama_lengkap']); ?></p>
                        <p class="text-xs text-gray-500 font-semibold mt-1">Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        <?php if($row['status'] == 'Lunas'): ?>
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold border border-green-200">LUNAS</span>
                        <?php elseif($row['status'] == 'Menunggu Konfirmasi'): ?>
                            <span class="bg-orange-100 text-orange-800 text-xs px-2.5 py-1 rounded-full font-bold border border-orange-200">CEK TRANSFER</span>
                        <?php else: ?>
                            <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-bold border border-red-200">BELUM BAYAR</span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>