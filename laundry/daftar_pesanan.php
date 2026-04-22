<?php
session_start();
require '../koneksi.php'; 

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'laundry') {
    header("Location: ../login.php"); exit;
}

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'selesai') {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $berat = (float)$_POST['berat_cucian']; 
    $biaya = $berat * 6000;

    $stmt = $conn->prepare("UPDATE pesanan_laundry SET status = 'Selesai', total_biaya = ?, berat = ? WHERE id_laundry = ?");
    $stmt->bind_param("ddi", $biaya, $berat, $id_pesanan);
    
    if($stmt->execute()) {
        $pesan_sukses = "Cucian LND-" . str_pad($id_pesanan, 4, '0', STR_PAD_LEFT) . " selesai! Total Tagihan Rp " . number_format($biaya, 0, ',', '.') . ".";
    } else { $pesan_error = "Gagal memperbarui data pesanan."; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'lunas') {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $update_lunas = $conn->query("UPDATE pesanan_laundry SET status_bayar = 'Lunas' WHERE id_laundry = '$id_pesanan'");
    if($update_lunas) {
        $pesan_sukses = "Pembayaran untuk LND-" . str_pad($id_pesanan, 4, '0', STR_PAD_LEFT) . " berhasil dilunasi!";
    }
}

$tab_aktif = isset($_GET['tab']) ? $_GET['tab'] : 'Diproses';
$where_klausul = "WHERE l.status = 'Diproses'";
if ($tab_aktif == 'Selesai') { $where_klausul = "WHERE l.status = 'Selesai'"; } 
elseif ($tab_aktif == 'Semua') { $where_klausul = "WHERE l.status IN ('Diproses', 'Selesai')"; }

$query_pesanan = $conn->query("
    SELECT l.id_laundry, l.tanggal_pesan, l.status, l.total_biaya, l.berat, l.status_bayar, p.nomor_kamar, p.nama_lengkap 
    FROM pesanan_laundry l
    LEFT JOIN penghuni p ON l.id_penghuni = p.id_penghuni
    $where_klausul
    ORDER BY l.status_bayar ASC, l.tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Daftar Pesanan - Admin Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-cyan-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_laundry.php" class="mr-3 hover:bg-cyan-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Kelola Pesanan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <?php if($pesan_sukses): ?><div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm text-sm font-semibold"><?php echo $pesan_sukses; ?></div><?php endif; ?>
        <?php if($pesan_error): ?><div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm text-sm font-semibold"><?php echo $pesan_error; ?></div><?php endif; ?>

        <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <a href="?tab=Diproses" class="<?php echo ($tab_aktif == 'Diproses') ? 'bg-cyan-600 text-white' : 'bg-white border border-gray-300 text-gray-600'; ?> px-4 py-1.5 rounded-full text-sm font-bold shadow-sm whitespace-nowrap">Sedang Diproses</a>
            <a href="?tab=Selesai" class="<?php echo ($tab_aktif == 'Selesai') ? 'bg-cyan-600 text-white' : 'bg-white border border-gray-300 text-gray-600'; ?> px-4 py-1.5 rounded-full text-sm font-bold shadow-sm whitespace-nowrap">Riwayat Selesai</a>
        </div>

        <div class="space-y-4">
            <?php if ($query_pesanan && $query_pesanan->num_rows > 0): ?>
                <?php while($p = $query_pesanan->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Kamar <?php echo htmlspecialchars($p['nomor_kamar'] ?? '??'); ?> - <?php echo htmlspecialchars($p['nama_lengkap'] ?? 'Data Belum Lengkap'); ?></h3>
                            <p class="text-xs text-gray-500 mt-1">LND-<?php echo str_pad($p['id_laundry'], 4, '0', STR_PAD_LEFT); ?> | <?php echo date('d M Y, H:i', strtotime($p['tanggal_pesan'])); ?></p>
                        </div>
                        <div>
                            <?php if($p['status'] == 'Diproses'): ?>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold border border-blue-200 uppercase">Diproses</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold border border-green-200 uppercase">Selesai</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($p['status'] == 'Diproses'): ?>
                        <form method="POST" class="bg-cyan-50 p-4 rounded-lg border border-cyan-100">
                            <input type="hidden" name="id_pesanan" value="<?php echo $p['id_laundry']; ?>">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Berat Cucian Terkini (Kg)</label>
                            <div class="relative mb-3">
                                <input type="number" step="0.1" name="berat_cucian" placeholder="Contoh: 2.5" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-cyan-500 pr-20" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs text-gray-400 font-bold">x Rp 6.000</div>
                            </div>
                            <button type="submit" name="aksi" value="selesai" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">Simpan Harga & Selesai</button>
                        </form>
                    <?php else: ?>
                        <div class="flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="w-full sm:w-auto mb-3 sm:mb-0">
                                <p class="text-xs text-gray-500 font-semibold uppercase">Total Tagihan Laundry</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="font-black text-gray-800 text-xl">Rp <?php echo number_format($p['total_biaya'], 0, ',', '.'); ?></span>
                                    <span class="text-xs font-bold text-gray-400">(<?php echo $p['berat']; ?> Kg)</span>
                                </div>
                            </div>
                            
                            <div class="w-full sm:w-auto text-right">
                                <?php if($p['status_bayar'] == 'Belum Bayar'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="id_pesanan" value="<?php echo $p['id_laundry']; ?>">
                                        <button type="submit" name="aksi" value="lunas" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-sm">Terima Pembayaran</button>
                                    </form>
                                <?php else: ?>
                                    <span class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-black border border-green-200"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> LUNAS DIBAYAR</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-8 rounded-xl shadow-sm text-center border border-gray-100"><p class="text-gray-500 font-medium">Tidak ada data pesanan.</p></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
