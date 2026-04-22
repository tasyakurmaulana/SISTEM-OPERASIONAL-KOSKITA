<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: ../login.php"); exit;
}

$id_pembayaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_pembayaran === 0) {
    die("Data receipt tidak ditemukan.");
}

$query = $conn->query("
    SELECT 
        pemb.id_pembayaran, pemb.tanggal_bayar, pemb.jumlah_bayar, pemb.status_pembayaran,
        t.bulan, t.tahun,
        penghuni.nama_lengkap, penghuni.nomor_kamar,
        metode.nama_provider, metode.nomor_rekening
    FROM pembayaran pemb
    JOIN tagihan t ON pemb.id_tagihan = t.id_tagihan
    JOIN penghuni ON pemb.id_penghuni = penghuni.id_penghuni
    JOIN metode_pembayaran metode ON pemb.id_metode = metode.id_metode
    WHERE pemb.id_pembayaran = '$id_pembayaran'
");

$data = $query->fetch_assoc();

if (!$data || $data['status_pembayaran'] !== 'Berhasil') {
    die("<div style='text-align:center; padding:50px;'>
            <h2>Akses Ditolak</h2>
            <p>Struk hanya tersedia untuk pembayaran yang sudah diverifikasi (Berhasil).</p>
            <a href='riwayat_pembayaran.php'>Kembali ke Riwayat</a>
         </div>");
}

$nomor_invoice = "INV/" . $data['tahun'] . "/" . str_pad($id_pembayaran, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Receipt - <?php echo $nomor_invoice; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
            .receipt-box { border: none; shadow: none; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-4 sm:p-10">

    <div class="max-w-xl mx-auto mb-6 flex justify-between no-print">
        <a href="riwayat_pembayaran.php" class="text-blue-600 font-bold flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow-md hover:bg-blue-700 transition">
            Cetak Struk / PDF
        </button>
    </div>

    <div class="max-w-xl mx-auto bg-white shadow-xl border border-gray-200 rounded-lg overflow-hidden receipt-box">
        
        <div class="bg-blue-600 p-8 text-white text-center">
            <h1 class="text-3xl font-black tracking-tighter">KosKita</h1>
            <p class="text-blue-100 text-sm mt-1">Bukti Pembayaran Elektronik Resmi</p>
        </div>

        <div class="p-8">
            <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
                <div>
                    <h2 class="text-gray-400 text-xs font-bold uppercase tracking-widest">Nomor Invoice</h2>
                    <p class="text-lg font-bold text-gray-800"><?php echo $nomor_invoice; ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-gray-400 text-xs font-bold uppercase tracking-widest">Tanggal Bayar</h2>
                    <p class="text-gray-800 font-medium"><?php echo date('d F Y', strtotime($data['tanggal_bayar'])); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div>
                    <h2 class="text-gray-400 text-xs font-bold uppercase mb-1">Diterima Dari:</h2>
                    <p class="text-gray-800 font-bold"><?php echo htmlspecialchars($data['nama_lengkap']); ?></p>
                    <p class="text-gray-500 text-sm">Kamar <?php echo htmlspecialchars($data['nomor_kamar']); ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-gray-400 text-xs font-bold uppercase mb-1">Metode Pembayaran:</h2>
                    <p class="text-gray-800 font-bold"><?php echo htmlspecialchars($data['nama_provider']); ?></p>
                    <p class="text-gray-500 text-sm">Transfer Rekening</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-100">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-200">
                            <th class="pb-2">Deskripsi Layanan</th>
                            <th class="pb-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr>
                            <td class="py-4 text-sm font-medium">Tagihan Sewa Kos Bulan <?php echo $data['bulan'] . " " . $data['tahun']; ?></td>
                            <td class="py-4 text-right font-bold">Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center bg-blue-50 p-4 rounded-xl border-2 border-blue-100 mb-8">
                <span class="text-blue-800 font-bold uppercase text-xs tracking-widest">Total Bayar</span>
                <span class="text-blue-600 font-black text-2xl">Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></span>
            </div>

            <div class="text-center border-t border-dashed border-gray-200 pt-8">
                <div class="inline-block bg-green-100 text-green-700 border border-green-200 px-6 py-1 rounded-full text-xs font-bold mb-4">
                    STATUS: LUNAS & TERVERIFIKASI
                </div>
                <p class="text-gray-400 text-[10px] leading-relaxed">
                    Struk ini diterbitkan secara otomatis oleh sistem KosKita dan sah tanpa tanda tangan basah.<br>
                    Terima kasih telah melakukan pembayaran tepat waktu.
                </p>
            </div>
        </div>
    </div>

</body>
</html>