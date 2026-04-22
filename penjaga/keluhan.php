<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php"); exit;
}

$pesan_sukses = "";
$pesan_error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_keluhan'])) {
    $id_keluhan = (int)$_POST['id_keluhan'];
    $status_baru = $conn->real_escape_string($_POST['status']);
    $tanggapan = $conn->real_escape_string($_POST['tanggapan']);

    $update = $conn->prepare("UPDATE keluhan SET status = ?, tanggapan_penjaga = ? WHERE id_keluhan = ?");
    $update->bind_param("ssi", $status_baru, $tanggapan, $id_keluhan);
    
    if ($update->execute()) {
        $pesan_sukses = "Status keluhan berhasil diubah menjadi '$status_baru' dan balasan terkirim ke penghuni!";
    } else {
        $pesan_error = "Gagal memperbarui keluhan.";
    }
}


$query_keluhan = $conn->query("
    SELECT 
        k.id_keluhan, k.kategori, k.deskripsi, k.foto_bukti, k.tanggal_pengajuan, k.status, k.tanggapan_penjaga,
        p.nomor_kamar, p.nama_lengkap
    FROM keluhan k
    JOIN penghuni p ON k.id_penghuni = p.id_penghuni
    ORDER BY 
        FIELD(k.status, 'Menunggu', 'Diproses', 'Selesai'), 
        k.tanggal_pengajuan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kelola Keluhan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penjaga.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Daftar Keluhan Masuk</div>
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

        <div class="space-y-4">
            <?php if ($query_keluhan && $query_keluhan->num_rows > 0): ?>
                <?php while($k = $query_keluhan->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 <?php echo ($k['status'] == 'Selesai') ? 'opacity-70' : ''; ?>">
                    <div class="flex justify-between items-start mb-3 border-b pb-3">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Kamar <?php echo htmlspecialchars($k['nomor_kamar']); ?> - <?php echo htmlspecialchars($k['nama_lengkap']); ?></h3>
                            <p class="text-xs text-gray-500 mt-1">Kategori: <span class="font-bold text-blue-600"><?php echo htmlspecialchars($k['kategori']); ?></span> | <?php echo date('d M Y, H:i', strtotime($k['tanggal_pengajuan'])); ?></p>
                        </div>
                        <div>
                            <?php if($k['status'] == 'Menunggu'): ?>
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Menunggu</span>
                            <?php elseif($k['status'] == 'Diproses'): ?>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Diproses</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Selesai</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-700 font-medium mb-3">"<?php echo htmlspecialchars($k['deskripsi']); ?>"</p>
                    
                    <a href="../uploads/keluhan/<?php echo htmlspecialchars($k['foto_bukti']); ?>" target="_blank" class="flex items-center text-xs text-blue-600 font-bold hover:underline mb-4 bg-blue-50 px-3 py-2 rounded-lg inline-flex border border-blue-100">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Lihat Foto Bukti
                    </a>

                    <?php if($k['status'] !== 'Selesai'): ?>
                    <form method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <input type="hidden" name="id_keluhan" value="<?php echo $k['id_keluhan']; ?>">
                        
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Update Status Keluhan</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 bg-white">
                                <option value="Menunggu" <?php if($k['status']=='Menunggu') echo 'selected'; ?>>Menunggu (Belum dicek)</option>
                                <option value="Diproses" <?php if($k['status']=='Diproses') echo 'selected'; ?>>Sedang Diproses (Diperbaiki)</option>
                                <option value="Selesai" <?php if($k['status']=='Selesai') echo 'selected'; ?>>Selesai (Sudah diperbaiki)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Berikan Balasan/Tanggapan</label>
                            <input type="text" name="tanggapan" value="<?php echo htmlspecialchars($k['tanggapan_penjaga'] ?? ''); ?>" placeholder="Contoh: Baik, tukang sedang menuju ke sana..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500" required>
                        </div>
                        <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 rounded-lg text-sm shadow transition">
                            Simpan Update
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="bg-green-50 border border-green-100 p-3 rounded-lg text-sm text-green-800 mt-2">
                            <span class="font-bold">Balasan Anda:</span> <?php echo htmlspecialchars($k['tanggapan_penjaga']); ?>
                        </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-8 rounded-2xl shadow-sm text-center border border-gray-100">
                    <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-gray-700 font-bold text-lg mb-1">Aman Terkendali!</h3>
                    <p class="text-gray-500 text-sm">Tidak ada keluhan masuk dari penghuni kos.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
