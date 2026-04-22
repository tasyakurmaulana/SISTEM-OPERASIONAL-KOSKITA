<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php"); exit;
}

$pesan = "";

if (isset($_POST['aksi'])) {
    $id_tagihan = (int)$_POST['id_tagihan'];
    $id_pembayaran = (int)$_POST['id_pembayaran'];
    $aksi = $_POST['aksi'];

    if ($aksi === 'terima') {
        $conn->query("UPDATE tagihan SET status = 'Lunas' WHERE id_tagihan = '$id_tagihan'");
        $conn->query("UPDATE pembayaran SET status_pembayaran = 'Berhasil' WHERE id_pembayaran = '$id_pembayaran'");
        
        $pesan = "<div class='bg-green-100 text-green-700 p-4 mb-6 rounded-lg font-bold'>Pembayaran berhasil DITERIMA. Tagihan Lunas!</div>";
    
    } elseif ($aksi === 'tolak') {
        $conn->query("UPDATE tagihan SET status = 'Belum Lunas' WHERE id_tagihan = '$id_tagihan'");
        $conn->query("UPDATE pembayaran SET status_pembayaran = 'Ditolak' WHERE id_pembayaran = '$id_pembayaran'");
        
        $pesan = "<div class='bg-red-100 text-red-700 p-4 mb-6 rounded-lg font-bold'>Pembayaran DITOLAK. Menunggu penghuni upload ulang.</div>";
    }
}

$query = $conn->query("
    SELECT 
        pemb.id_pembayaran, pemb.bukti_transfer, pemb.tanggal_bayar,
        t.id_tagihan, t.bulan, t.tahun, t.total_tagihan,
        penghuni.nomor_kamar, penghuni.nama_lengkap,
        metode.nama_provider
    FROM pembayaran pemb
    JOIN tagihan t ON pemb.id_tagihan = t.id_tagihan
    JOIN penghuni ON pemb.id_penghuni = penghuni.id_penghuni
    JOIN metode_pembayaran metode ON pemb.id_metode = metode.id_metode
    WHERE t.status = 'Menunggu Konfirmasi' AND pemb.status_pembayaran = 'Pending'
    ORDER BY pemb.tanggal_bayar ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verifikasi Pembayaran - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_pemilik.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Verifikasi Pembayaran</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <?php echo $pesan; ?>

        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-4 mb-6 rounded-xl text-sm flex items-start shadow-sm">
            <svg class="w-5 h-5 mr-2 mt-0.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p>Daftar di bawah adalah anak kos yang sudah upload bukti transfer. Silakan cek foto buktinya dan cocokkan dengan mutasi rekening Anda sebelum menekan tombol Terima.</p>
        </div>

        <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Menunggu Verifikasi Anda</h2>
        
        <?php if ($query->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while($row = $query->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 overflow-hidden relative">
                    <div class="absolute top-0 right-0 bg-orange-400 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg">BUTUH CEK</div>

                    <div class="flex flex-col md:flex-row justify-between mb-4 mt-2">
                        <div>
                            <h3 class="font-extrabold text-gray-800 text-xl">Kamar <?php echo htmlspecialchars($row['nomor_kamar']); ?> - <?php echo htmlspecialchars($row['nama_lengkap']); ?></h3>
                            <p class="text-sm text-gray-500 mt-1">Tagihan: <span class="font-semibold text-gray-700"><?php echo $row['bulan']." ".$row['tahun']; ?></span></p>
                            <p class="text-xs text-gray-400 mt-1">Waktu Upload: <?php echo date('d M Y, H:i', strtotime($row['tanggal_bayar'])); ?></p>
                        </div>
                        <div class="mt-3 md:mt-0 md:text-right">
                            <span class="font-black text-emerald-600 text-2xl">Rp <?php echo number_format($row['total_tagihan'], 0, ',', '.'); ?></span>
                            <p class="text-xs text-gray-500 mt-1 font-medium bg-gray-100 inline-block px-2 py-1 rounded">Via <?php echo htmlspecialchars($row['nama_provider']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 items-center border-t border-gray-100 pt-4 mt-2">
                        <a href="../uploads/bukti/<?php echo htmlspecialchars($row['bukti_transfer']); ?>" target="_blank" class="w-full sm:w-1/3 flex items-center justify-center bg-gray-100 text-gray-700 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition border border-gray-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Cek Gambar Struk
                        </a>
                        
                        <form method="POST" class="w-full sm:w-2/3 flex gap-2">
                            <input type="hidden" name="id_tagihan" value="<?php echo $row['id_tagihan']; ?>">
                            <input type="hidden" name="id_pembayaran" value="<?php echo $row['id_pembayaran']; ?>">
                            
                            <button type="submit" name="aksi" value="tolak" onclick="return confirm('Yakin ingin MENOLAK bukti transfer ini?')" class="w-1/3 bg-red-100 text-red-600 py-3 rounded-xl font-bold text-sm hover:bg-red-200 transition">
                                Tolak
                            </button>
                            <button type="submit" name="aksi" value="terima" onclick="return confirm('Pastikan uang sudah masuk ke rekening Anda. Lanjutkan?')" class="w-2/3 bg-emerald-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-md transition flex justify-center items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Terima & Lunas
                            </button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-white p-8 rounded-2xl shadow-sm text-center border border-gray-100">
                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-gray-700 font-bold text-lg mb-1">Semua Beres!</h3>
                <p class="text-gray-500 text-sm">Tidak ada pembayaran yang butuh konfirmasi saat ini.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
