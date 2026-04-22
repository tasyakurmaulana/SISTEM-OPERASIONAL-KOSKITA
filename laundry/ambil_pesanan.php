<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'laundry') {
    header("Location: ../login.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'proses') {
    $id_pesanan = (int)$_POST['id_pesanan'];
    
    $update = $conn->query("UPDATE pesanan_laundry SET status = 'Diproses' WHERE id_laundry = '$id_pesanan'");
    
    if($update) {
        $pesan_sukses = "Pesanan LND-" . str_pad($id_pesanan, 4, '0', STR_PAD_LEFT) . " berhasil diambil dan sedang diproses!";
    } else {
        $pesan_error = "Gagal memproses pesanan.";
    }
}


$query_pesanan = $conn->query("
    SELECT l.id_laundry, l.tanggal_pesan, p.nomor_kamar, p.nama_lengkap 
    FROM pesanan_laundry l
    LEFT JOIN penghuni p ON l.id_penghuni = p.id_penghuni
    WHERE l.status = 'Menunggu'
    ORDER BY l.tanggal_pesan ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Cucian - Admin Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-cyan-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_laundry.php" class="mr-4 hover:bg-cyan-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Ambil & Proses Cucian</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <?php if($pesan_error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
            <?php echo $pesan_error; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!$query_pesanan): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
            <b>Error Database:</b> <?php echo $conn->error; ?>
        </div>
        <?php endif; ?>

        <div class="bg-cyan-50 border border-cyan-200 text-cyan-800 p-4 mb-6 rounded-xl text-sm flex items-start shadow-sm">
            <svg class="w-5 h-5 mr-2 mt-0.5 text-cyan-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>Daftar di bawah ini adalah anak kos yang menekan tombol "Pesan Laundry". Segera ambil keranjang cucian di depan kamar mereka lalu klik <b>Ambil & Proses</b>.</p>
        </div>

        <div class="space-y-4">
            <?php if ($query_pesanan && $query_pesanan->num_rows > 0): ?>
                <?php while($p = $query_pesanan->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-cyan-200 transition">
                    <div class="flex justify-between items-start border-b pb-3 mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Kamar <?php echo htmlspecialchars($p['nomor_kamar'] ?? '??'); ?> - <?php echo htmlspecialchars($p['nama_lengkap'] ?? 'Data Belum Lengkap'); ?></h3>
                            <p class="text-xs text-gray-500 mt-1">
                                ID: LND-<?php echo str_pad($p['id_laundry'], 4, '0', STR_PAD_LEFT); ?> | 
                                Panggilan Masuk: <?php echo date('d M Y, H:i', strtotime($p['tanggal_pesan'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[10px] uppercase font-black tracking-widest border border-orange-200 shadow-sm animate-pulse">Menunggu</span>
                        </div>
                    </div>

                    <form method="POST" class="flex gap-3">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p['id_laundry']; ?>">
                        <button type="submit" name="aksi" value="proses" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-sm flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Ambil & Proses Cucian
                        </button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-8 rounded-xl shadow-sm text-center border border-gray-100">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada panggilan laundry baru.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>