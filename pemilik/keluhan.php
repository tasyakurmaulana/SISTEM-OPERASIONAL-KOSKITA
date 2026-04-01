<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php"); exit;
}

// Data Analitik Simulasi
$total_keluhan = 15;
$keluhan_selesai = 12;
$keluhan_aktif = 3; // Menunggu/Diproses

// Data Monitoring (Semua Keluhan)
$semua_keluhan = [
    ['id' => 'KLH-003', 'kamar' => 'B-04', 'kategori' => 'Listrik', 'tgl' => '10 Apr 2026', 'status' => 'Menunggu', 'penanganan' => 'Belum ada tindakan'],
    ['id' => 'KLH-002', 'kamar' => 'A-01', 'kategori' => 'Fasilitas', 'tgl' => '09 Apr 2026', 'status' => 'Diproses', 'penanganan' => 'Tukang kayu sedang memperbaiki'],
    ['id' => 'KLH-001', 'kamar' => 'C-02', 'kategori' => 'Air', 'tgl' => '02 Apr 2026', 'status' => 'Selesai', 'penanganan' => 'Keran sudah diganti baru']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Monitoring Keluhan - Pemilik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans pb-10">
    <nav class="bg-emerald-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="javascript:history.back()" class="mr-4 hover:bg-emerald-700 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></a>
                <div class="font-bold text-lg">Monitoring Keluhan</div>
            </div>
            <button onclick="window.print()" class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-lg flex items-center transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak
            </button>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-gray-400 text-center">
                <p class="text-xs text-gray-500 font-bold uppercase">Total Bulan Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $total_keluhan; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-emerald-500 text-center">
                <p class="text-xs text-emerald-600 font-bold uppercase">Sudah Selesai</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $keluhan_selesai; ?></h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-red-500 text-center">
                <p class="text-xs text-red-600 font-bold uppercase">Belum Selesai</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1"><?php echo $keluhan_aktif; ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-emerald-50 text-emerald-800 text-sm">
                            <th class="p-3 border-b border-emerald-100 font-semibold">Tgl Masuk</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Kamar</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Kategori</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Status</th>
                            <th class="p-3 border-b border-emerald-100 font-semibold">Penanganan Penjaga</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php foreach($semua_keluhan as $klh): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-600 whitespace-nowrap"><?php echo $klh['tgl']; ?></td>
                            <td class="p-3 font-bold text-gray-800"><?php echo $klh['kamar']; ?></td>
                            <td class="p-3 text-gray-600"><?php echo $klh['kategori']; ?></td>
                            <td class="p-3">
                                <?php if($klh['status'] == 'Selesai'): ?>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Selesai</span>
                                <?php elseif($klh['status'] == 'Diproses'): ?>
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Diproses</span>
                                <?php else: ?>
                                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold">Menunggu</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-gray-500 italic">"<?php echo $klh['penanganan']; ?>"</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>