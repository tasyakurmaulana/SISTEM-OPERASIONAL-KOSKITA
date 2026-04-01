<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php"); exit;
}

// Simulasi Data Riwayat Keluhan (PBI-34)
$riwayat_keluhan = [
    ['id' => 'KLH-002', 'kategori' => 'Listrik', 'tgl' => '10 Apr 2026', 'deskripsi' => 'Lampu kamar mandi mati sejak kemarin malam.', 'status' => 'Menunggu', 'tanggapan' => ''],
    ['id' => 'KLH-001', 'kategori' => 'Air', 'tgl' => '02 Apr 2026', 'deskripsi' => 'Keran wastafel bocor dan menetes terus.', 'status' => 'Selesai', 'tanggapan' => 'Sudah diganti dengan keran baru oleh tukang.']
];

$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulasi PBI-32 & PBI-33 (Upload Foto & Insert Data)
    $pesan_sukses = "Keluhan berhasil dikirim. Penjaga kos akan segera memprosesnya!";
}
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
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Pusat Bantuan & Keluhan</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Ajukan Keluhan Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Masalah</label>
                    <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:ring-blue-500" required>
                        <option value="">-- Pilih Kategori --</option>
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
                    <input type="file" name="foto_bukti" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50" required>
                    <p class="text-xs text-gray-400 mt-1">*Format: JPG, PNG. Ukuran maksimal 2MB.</p>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">Kirim Keluhan</button>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-4">Status Keluhan Saya</h3>
        <div class="space-y-4">
            <?php foreach($riwayat_keluhan as $k): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-bold uppercase"><?php echo $k['kategori']; ?></span>
                        <p class="text-xs text-gray-400 mt-1"><?php echo $k['tgl']; ?></p>
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
                
                <h4 class="font-bold text-gray-800 text-sm mt-3 border-t pt-2"><?php echo $k['deskripsi']; ?></h4>
                <button onclick="alert('Membuka foto lampiran...')" class="text-xs text-blue-500 font-bold hover:underline mt-1">Lihat Lampiran Foto</button>

                <?php if($k['tanggapan']): ?>
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-800">
                        <span class="font-bold">Balasan Penjaga:</span> <?php echo $k['tanggapan']; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>