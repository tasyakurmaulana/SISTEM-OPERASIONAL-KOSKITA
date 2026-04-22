<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php"); exit;
}

$id_user = $_SESSION['id_user'];

$query_penghuni = $conn->query("SELECT id_penghuni FROM penghuni WHERE id_user = '$id_user'");
$id_penghuni = ($query_penghuni->num_rows > 0) ? $query_penghuni->fetch_assoc()['id_penghuni'] : 0;

$id_tagihan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_tagihan === 0) {
    die("<div style='padding:20px; text-align:center;'>Tagihan tidak ditemukan. <a href='tagihan_penghuni.php'>Kembali</a></div>");
}

$query_tagihan = $conn->query("SELECT bulan, tahun, total_tagihan, status FROM tagihan WHERE id_tagihan = '$id_tagihan' AND id_penghuni = '$id_penghuni'");
if ($query_tagihan->num_rows == 0) {
    die("<div style='padding:20px; text-align:center;'>Data tagihan tidak valid. <a href='tagihan_penghuni.php'>Kembali</a></div>");
}

$tagihan = $query_tagihan->fetch_assoc();
$tagihan_bulan = $tagihan['bulan'] . " " . $tagihan['tahun'];
$total_bayar = $tagihan['total_tagihan'];

if ($tagihan['status'] !== 'Belum Lunas') {
    header("Location: riwayat_pembayaran.php"); exit;
}

$metode_list = [];
$query_metode = $conn->query("SELECT * FROM metode_pembayaran");
if ($query_metode) {
    while ($row = $query_metode->fetch_assoc()) {
        $metode_list[] = $row;
    }
}

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_metode = (int)$_POST['metode'];

    $target_dir = "../uploads/bukti/"; 
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = $_FILES["bukti_transfer"]["name"];
    $file_tmp = $_FILES["bukti_transfer"]["tmp_name"];
    $file_size = $_FILES["bukti_transfer"]["size"];
    $file_error = $_FILES["bukti_transfer"]["error"];

    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if ($file_error === 0) {
        if (in_array($ext, $allowed_ext)) {
            if ($file_size <= 2097152) {
                
                $new_file_name = "bukti_" . $id_tagihan . "_" . time() . "." . $ext;
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    
                    $stmt = $conn->prepare("INSERT INTO pembayaran (id_tagihan, id_penghuni, id_metode, jumlah_bayar, bukti_transfer, status_pembayaran) VALUES (?, ?, ?, ?, ?, 'Pending')");
                    $stmt->bind_param("iiiis", $id_tagihan, $id_penghuni, $id_metode, $total_bayar, $new_file_name);

                    if ($stmt->execute()) {
                        $conn->query("UPDATE tagihan SET status = 'Menunggu Konfirmasi' WHERE id_tagihan = '$id_tagihan'");
                        $pesan_sukses = "Bukti pembayaran berhasil diunggah! Menunggu konfirmasi pemilik.";
                    } else {
                        $pesan_error = "Gagal menyimpan data ke database.";
                    }
                } else {
                    $pesan_error = "Gagal mengunggah file ke server.";
                }
            } else {
                $pesan_error = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
        } else {
            $pesan_error = "Format file tidak didukung. Gunakan JPG atau PNG.";
        }
    } else {
        $pesan_error = "Terjadi kesalahan saat memilih file. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tagihan - KosKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center">
            <a href="tagihan_penghuni.php" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Bayar Tagihan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
                <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>

        <?php if($pesan_sukses): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Terkirim!</h2>
                <p class="text-gray-500 mb-6"><?php echo $pesan_sukses; ?></p>
                <a href="riwayat_pembayaran.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition inline-block">Cek Status Riwayat</a>
            </div>
        
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 text-center">
                <p class="text-gray-500 text-sm">Total yang harus dibayar (<?php echo htmlspecialchars($tagihan_bulan); ?>)</p>
                <h1 class="text-4xl font-extrabold text-blue-600 mt-2">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></h1>
            </div>

            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Rekening Tujuan</label>
                    <select name="metode" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 bg-gray-50" required>
                        <option value="" disabled selected>-- Pilih Bank / E-Wallet --</option>
                        <?php foreach($metode_list as $m): ?>
                            <option value="<?php echo $m['id_metode']; ?>">
                                <?php echo $m['nama_provider']; ?> - <?php echo $m['nomor_rekening']; ?> (a.n <?php echo $m['atas_nama']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Transfer</label>
                    <input type="file" name="bukti_transfer" accept="image/png, image/jpeg" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-white focus:ring-blue-500 cursor-pointer" required>
                    <p class="text-xs text-gray-400 mt-1">*Format: JPG, PNG. Maksimal 2MB.</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md">
                    Konfirmasi Pembayaran
                </button>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>
