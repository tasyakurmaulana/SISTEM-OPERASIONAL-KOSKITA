<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penjaga') {
    header("Location: ../login.php"); exit;
}

// Simulasi Data Keluhan Masuk
$daftar_keluhan = [
    ['id' => 'KLH-003', 'kamar' => 'B-04', 'nama' => 'Indra Febri', 'kategori' => 'Listrik', 'tgl' => '10 Apr 2026', 'deskripsi' => 'Lampu kamar mandi mati sejak kemarin malam.', 'status' => 'Menunggu', 'foto' => 'lampu_mati.jpg'],
    ['id' => 'KLH-002', 'kamar' => 'A-01', 'nama' => 'Budi Santoso', 'kategori' => 'Fasilitas', 'tgl' => '09 Apr 2026', 'deskripsi' => 'Pintu lemari lepas engselnya.', 'status' => 'Diproses', 'foto' => 'lemari_rusak.jpg']
];

$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_baru = $_POST['status'];
    $pesan_sukses = "Status keluhan berhasil diubah menjadi '$status_baru' dan balasan terkirim ke penghuni!";
}
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
            <a href="javascript:history.back()" class="mr-4 hover:bg-blue-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
            <div class="font-bold text-lg">Daftar Keluhan Masuk</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <?php if($pesan_sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm font-semibold text-sm">
            <?php echo $pesan_sukses; ?>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <?php foreach($daftar_keluhan as $k): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-3 border-b pb-3">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Kamar <?php echo $k['kamar']; ?> - <?php echo $k['nama']; ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Kategori: <span class="font-bold text-blue-600"><?php echo $k['kategori']; ?></span> | <?php echo $k['tgl']; ?></p>
                    </div>
                    <div>
                        <?php if($k['status'] == 'Menunggu'): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">Menunggu</span>
                        <?php else: ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Diproses</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <p class="text-sm text-gray-700 font-medium mb-2">"<?php echo $k['deskripsi']; ?>"</p>
                <button onclick="alert('Membuka file: <?php echo $k['foto']; ?>')" class="flex items-center text-xs text-blue-600 font-bold hover:underline mb-4 bg-blue-50 px-3 py-2 rounded-lg inline-flex">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Lihat Foto Bukti
                </button>

                <form method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <input type="hidden" name="id_keluhan" value="<?php echo $k['id']; ?>">
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Update Status Keluhan</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 bg-white">
                            <option value="Diterima" <?php if($k['status']=='Menunggu') echo 'selected'; ?>>Diterima (Akan dicek)</option>
                            <option value="Diproses" <?php if($k['status']=='Diproses') echo 'selected'; ?>>Sedang Diproses (Diperbaiki)</option>
                            <option value="Selesai">Selesai (Sudah diperbaiki)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Berikan Balasan/Tanggapan</label>
                        <input type="text" name="tanggapan" placeholder="Contoh: Baik, tukang sedang menuju ke sana..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 rounded-lg text-sm shadow transition">
                        Simpan Update
                    </button>
                </form>

            </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>