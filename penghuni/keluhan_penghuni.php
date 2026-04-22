<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php"); exit;
}

$id_user = $_SESSION['id_user'];

$query_penghuni = $conn->query("SELECT id_penghuni FROM penghuni WHERE id_user = '$id_user'");
$id_penghuni = ($query_penghuni->num_rows > 0) ? $query_penghuni->fetch_assoc()['id_penghuni'] : 0;

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

    $target_dir = "../uploads/keluhan/"; 
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = $_FILES["foto_bukti"]["name"];
    $file_tmp = $_FILES["foto_bukti"]["tmp_name"];
    $file_size = $_FILES["foto_bukti"]["size"];
    $file_error = $_FILES["foto_bukti"]["error"];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if ($file_error === 0) {
        if (in_array($ext, $allowed_ext)) {
            if ($file_size <= 2097152) {
                
                $new_file_name = "keluhan_" . $id_penghuni . "_" . time() . "." . $ext;
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO keluhan (id_penghuni, kategori, deskripsi, foto_bukti, status) VALUES (?, ?, ?, ?, 'Menunggu')");
                    $stmt->bind_param("isss", $id_penghuni, $kategori, $deskripsi, $new_file_name);
                    
                    if ($stmt->execute()) {
                        $pesan_sukses = "Keluhan berhasil dikirim! Penjaga kos akan segera memprosesnya.";
                    } else {
                        $pesan_error = "Gagal menyimpan keluhan ke database.";
                    }
                } else {
                    $pesan_error = "Gagal mengunggah foto ke server.";
                }
            } else {
                $pesan_error = "Ukuran foto terlalu besar. Maksimal 2MB.";
            }
        } else {
            $pesan_error = "Format foto tidak didukung. Gunakan JPG atau PNG.";
        }
    } else {
        $pesan_error = "Terjadi kesalahan saat memilih foto.";
    }
}


$query_riwayat = $conn->query("SELECT * FROM keluhan WHERE id_penghuni = '$id_penghuni' ORDER BY tanggal_pengajuan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Keluhan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penghuni.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Pusat Bantuan & Keluhan</div>
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Ajukan Keluhan Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Masalah</label>
                    <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:ring-blue-500" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="Air">Saluran Air / Ledeng</option>
                        <option value="Listrik">Listrik / Lampu</option>
                        <option value="Fasilitas">Fasilitas Kamar (Kasur, Lemari, dll)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Detail</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan masalah yang terjadi secara detail..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500" required></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Foto Bukti (Wajib)</label>
                    <input type="file" name="foto_bukti" accept="image/png, image/jpeg" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50 cursor-pointer" required>
                    <p class="text-xs text-gray-400 mt-1">*Format: JPG, PNG. Ukuran maksimal 2MB.</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">Kirim Keluhan</button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Status Keluhan Saya</h3>
        <div class="space-y-4">
            <?php if ($query_riwayat && $query_riwayat->num_rows > 0): ?>
                <?php while($k = $query_riwayat->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-bold uppercase border border-gray-200"><?php echo htmlspecialchars($k['kategori']); ?></span>
                            <p class="text-xs text-gray-400 mt-1"><?php echo date('d M Y, H:i', strtotime($k['tanggal_pengajuan'])); ?></p>
                        </div>
                        <div>
                            <?php if($k['status'] == 'Selesai'): ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Selesai</span>
                            <?php elseif($k['status'] == 'Diproses'): ?>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Diproses</span>
                            <?php else: ?>
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Menunggu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h4 class="font-bold text-gray-800 text-sm mt-3 border-t pt-2"><?php echo htmlspecialchars($k['deskripsi']); ?></h4>
                    
                    <a href="../uploads/keluhan/<?php echo htmlspecialchars($k['foto_bukti']); ?>" target="_blank" class="text-xs text-blue-500 font-bold hover:underline mt-1 inline-block flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Lihat Lampiran Foto
                    </a>

                    <?php if(!empty($k['tanggapan_penjaga'])): ?>
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-800">
                            <span class="font-bold">Balasan Penjaga:</span> <?php echo htmlspecialchars($k['tanggapan_penjaga']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-6 rounded-xl shadow-sm text-center border border-gray-100">
                    <p class="text-gray-500 text-sm">Belum ada keluhan yang pernah kamu ajukan.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
