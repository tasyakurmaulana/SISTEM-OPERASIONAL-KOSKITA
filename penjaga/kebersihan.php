<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penghuni = (int)$_POST['id_penghuni'];
    $status_bersih = $conn->real_escape_string($_POST['status']);
    $catatan = $conn->real_escape_string($_POST['catatan']);

    $stmt = $conn->prepare("INSERT INTO checklist_kebersihan (id_penghuni, status, catatan) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_penghuni, $status_bersih, $catatan);
    
    if ($stmt->execute()) {
        $pesan_sukses = "Checklist kebersihan berhasil disimpan ke database!";
    } else {
        $pesan_error = "Gagal menyimpan data pengecekan.";
    }
}


$query_kamar = $conn->query("SELECT id_penghuni, nomor_kamar, nama_lengkap FROM penghuni ORDER BY nomor_kamar ASC");


$query_riwayat = $conn->query("
    SELECT c.tanggal_cek, c.status, c.catatan, p.nomor_kamar, p.nama_lengkap 
    FROM checklist_kebersihan c
    JOIN penghuni p ON c.id_penghuni = p.id_penghuni
    ORDER BY c.tanggal_cek DESC 
    LIMIT 20
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kebersihan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_penjaga.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Checklist Kebersihan</div>
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
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Form Pengecekan Harian</h2>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kamar</label>
                    <select name="id_penghuni" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50 text-gray-700" required>
                        <option value="" disabled selected>-- Pilih Nomor Kamar --</option>
                        <?php if($query_kamar && $query_kamar->num_rows > 0): ?>
                            <?php while($k = $query_kamar->fetch_assoc()): ?>
                                <option value="<?php echo $k['id_penghuni']; ?>">
                                    Kamar <?php echo htmlspecialchars($k['nomor_kamar']); ?> - <?php echo htmlspecialchars($k['nama_lengkap']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Belum ada data penghuni</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Kebersihan Area Kamar</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Bersih" class="peer sr-only" required>
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">✨</span>
                                <span class="text-xs font-bold">Bersih</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Kurang Bersih" class="peer sr-only">
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-orange-50 peer-checked:border-orange-500 peer-checked:text-orange-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">🍂</span>
                                <span class="text-xs font-bold">Kurang</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="Kotor" class="peer sr-only">
                            <div class="text-center p-3 rounded-lg border border-gray-200 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 hover:bg-gray-50 transition">
                                <span class="block text-xl mb-1">🗑️</span>
                                <span class="text-xs font-bold">Kotor</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Pengecekan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: Ada tumpukan piring kotor di teras kamar..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
                    Simpan Laporan Kebersihan
                </button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Riwayat Pengecekan Terbaru</h3>
        <div class="space-y-4">
            <?php if ($query_riwayat && $query_riwayat->num_rows > 0): ?>
                <?php while($riwayat = $query_riwayat->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">Kamar <?php echo htmlspecialchars($riwayat['nomor_kamar']); ?></h4>
                            <p class="text-xs text-gray-500 mt-0.5"><?php echo date('d M Y, H:i', strtotime($riwayat['tanggal_cek'])); ?></p>
                        </div>
                        <div>
                            <?php if($riwayat['status'] == 'Bersih'): ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">Bersih</span>
                            <?php elseif($riwayat['status'] == 'Kurang Bersih'): ?>
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Kurang Bersih</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">Kotor</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if(!empty($riwayat['catatan'])): ?>
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">Catatan:</span> <?php echo htmlspecialchars($riwayat['catatan']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-6 rounded-xl shadow-sm text-center border border-gray-100">
                    <p class="text-gray-500 text-sm">Belum ada riwayat pengecekan kebersihan.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
