<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

$pesan_sukses = "";

if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    if ($id_hapus !== 1) {
        $conn->query("DELETE FROM pengaturan_harga WHERE id_pengaturan = $id_hapus");
        $pesan_sukses = "Biaya tambahan berhasil dihapus!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_semua'])) {
    
    $tarif_dasar = (float)$_POST['tarif_dasar'];
    $conn->query("UPDATE pengaturan_harga SET nominal = $tarif_dasar WHERE id_pengaturan = 1");

    if (!empty($_POST['nama_biaya_baru']) && !empty($_POST['nominal_biaya_baru'])) {
        $nama_baru = $conn->real_escape_string($_POST['nama_biaya_baru']);
        $nominal_baru = (float)$_POST['nominal_biaya_baru'];
        $conn->query("INSERT INTO pengaturan_harga (nama_biaya, nominal) VALUES ('$nama_baru', $nominal_baru)");
    }

    $aturan_baru = $_POST['aturan_kos'];
    $stmt = $conn->prepare("UPDATE pengaturan_aturan SET aturan_kos = ? WHERE id = 1");
    $stmt->bind_param("s", $aturan_baru);
    $stmt->execute();

    $pesan_sukses = "Semua pengaturan berhasil diperbarui ke database!";
}

$query_dasar = $conn->query("SELECT nominal FROM pengaturan_harga WHERE id_pengaturan = 1");
$tarif_dasar = ($query_dasar->num_rows > 0) ? $query_dasar->fetch_assoc()['nominal'] : 0;

$query_tambahan = $conn->query("SELECT * FROM pengaturan_harga WHERE id_pengaturan > 1");
$biaya_tambahan = [];
while ($row = $query_tambahan->fetch_assoc()) {
    $biaya_tambahan[] = $row;
}

$query_aturan = $conn->query("SELECT aturan_kos FROM pengaturan_aturan WHERE id = 1");
$aturan_kos = ($query_aturan->num_rows > 0) ? $query_aturan->fetch_assoc()['aturan_kos'] : "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Harga & Aturan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased pb-10">

    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="dashboard_pemilik.php" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="font-bold text-lg">Pengaturan Harga & Aturan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Pengaturan Harga Sewa</h3>
                
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif Dasar Kamar (Per Bulan)</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 font-bold">Rp</span>
                        <input type="number" name="tarif_dasar" value="<?php echo floatval($tarif_dasar); ?>" class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Biaya Tambahan Aktif</label>
                    <?php if (empty($biaya_tambahan)): ?>
                        <p class="text-sm text-gray-400 italic mb-2">Belum ada biaya tambahan.</p>
                    <?php else: ?>
                        <?php foreach($biaya_tambahan as $biaya): ?>
                        <div class="flex gap-2 mb-2 items-center">
                            <input type="text" value="<?php echo htmlspecialchars($biaya['nama_biaya']); ?>" class="w-1/2 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-100 text-gray-600 font-medium" readonly>
                            <input type="number" value="<?php echo floatval($biaya['nominal']); ?>" class="w-1/3 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-100 text-gray-600 font-medium" readonly>
                            <a href="?hapus=<?php echo $biaya['id_pengaturan']; ?>" onclick="return confirm('Yakin ingin menghapus biaya <?php echo $biaya['nama_biaya']; ?>?')" class="w-1/6 text-center bg-red-100 text-red-600 rounded-lg py-2 font-bold hover:bg-red-200 transition">X</a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mt-4 p-4 bg-emerald-50 border border-emerald-100 rounded-lg">
                    <label class="block text-xs font-bold text-emerald-800 mb-2">+ Tambah Biaya Baru (Opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" name="nama_biaya_baru" placeholder="Nama Biaya (Cth: Parkir Mobil)" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:outline-none">
                        <input type="number" name="nominal_biaya_baru" placeholder="Nominal (Cth: 50000)" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Aturan & Tata Tertib Kos</h3>
                <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded mb-4 border border-blue-100">
                    <b>Tips:</b> Gunakan "Enter" untuk memisahkan setiap poin aturan agar otomatis bernomor di halaman penghuni.
                </div>
                
                <textarea name="aturan_kos" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm leading-relaxed" required><?php echo htmlspecialchars($aturan_kos); ?></textarea>
            </div>

            <button type="submit" name="simpan_semua" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
                Simpan Semua Perubahan
            </button>
        </form>

    </div>
</body>
</html>
