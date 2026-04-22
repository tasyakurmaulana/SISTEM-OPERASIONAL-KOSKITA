<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query_penghuni = $conn->query("SELECT id_penghuni FROM penghuni WHERE id_user = '$id_user'");
$id_penghuni = ($query_penghuni && $query_penghuni->num_rows > 0) ? $query_penghuni->fetch_assoc()['id_penghuni'] : 0;

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cek_pending = $conn->query("SELECT id_laundry FROM pesanan_laundry WHERE id_penghuni = '$id_penghuni' AND status = 'Menunggu'");
    
    if ($cek_pending && $cek_pending->num_rows > 0) {
        $pesan_error = "Anda masih memiliki pesanan yang belum diambil oleh Mbak Laundry. Harap tunggu ya!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pesanan_laundry (id_penghuni) VALUES (?)");
        $stmt->bind_param("i", $id_penghuni);
        if ($stmt->execute()) {
            $pesan_sukses = "Pesanan laundry berhasil dibuat! Silakan taruh keranjang cucian di depan kamar, Mbak Laundry akan segera mengambilnya.";
        } else {
            $pesan_error = "Terjadi kesalahan sistem, gagal membuat pesanan.";
        }
    }
}

$query_riwayat = $conn->query("SELECT * FROM pesanan_laundry WHERE id_penghuni = '$id_penghuni' ORDER BY tanggal_pesan DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Laundry - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-teal-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penghuni.php" class="mr-4 hover:bg-teal-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Layanan Laundry</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Berhasil!</p>
            <p class="text-sm mt-1"><?php echo $pesan_sukses; ?></p>
        </div>
        <?php endif; ?>

        <?php if($pesan_error): ?>
        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Oops!</p>
            <p class="text-sm mt-1"><?php echo $pesan_error; ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-teal-100 p-6 mb-8 text-center relative overflow-hidden">
            <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Pakaian Sudah Menumpuk?</h2>
            <p class="text-sm text-gray-500 mb-6">Klik tombol di bawah ini untuk memanggil Mbak Laundry</p>
            
            <form method="POST" action="">
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-lg">
                    Pesan Laundry Sekarang
                </button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Status Cucian Saya</h3>
        <div class="space-y-4">
            <?php if ($query_riwayat && $query_riwayat->num_rows > 0): ?>
                <?php while($pesanan = $query_riwayat->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800">LND-<?php echo str_pad($pesanan['id_laundry'], 4, '0', STR_PAD_LEFT); ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo date('d M Y, H:i', strtotime($pesanan['tanggal_pesan'])); ?></p>
                        
                        <?php if($pesanan['total_biaya'] > 0): ?>
                            <div class="flex items-center gap-2 mt-2">
                                <p class="font-extrabold text-teal-600">Rp <?php echo number_format($pesanan['total_biaya'], 0, ',', '.'); ?></p>
                                
                                <?php if($pesanan['status_bayar'] == 'Lunas'): ?>
                                    <span class="text-[10px] bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded">LUNAS</span>
                                <?php else: ?>
                                    <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded animate-pulse">BELUM BAYAR</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold mt-0.5"><?php echo $pesanan['berat']; ?> Kg</p>
                        <?php else: ?>
                            <p class="text-xs text-orange-500 italic mt-2">Menunggu ditimbang</p>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <?php if($pesanan['status'] == 'Selesai'): ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-xs font-bold border border-green-200">SELESAI</span>
                        <?php elseif($pesanan['status'] == 'Diproses'): ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-bold border border-blue-200">DIPROSES</span>
                        <?php else: ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1.5 rounded-full text-xs font-bold border border-orange-200">MENUNGGU</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-6 rounded-xl shadow-sm text-center border border-gray-100">
                    <p class="text-gray-500 text-sm">Belum ada riwayat pesanan laundry.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
