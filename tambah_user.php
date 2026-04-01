<?php
require 'koneksi.php';

// Kita buat password default: 123456
$password_default = password_hash('123456', PASSWORD_DEFAULT);

// Data 4 user untuk testing
$users = [
    ['username' => 'penghuni1', 'role' => 'penghuni'],
    ['username' => 'boskos', 'role' => 'pemilik'],
    ['username' => 'penjaga1', 'role' => 'penjaga'],
    ['username' => 'laundry1', 'role' => 'laundry']
];

foreach ($users as $u) {
    $username = $u['username'];
    $role = $u['role'];
    
    // Cek apakah username sudah ada agar tidak error duplikat
    $cek = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($cek->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password_default, $role);
        if ($stmt->execute()) {
            echo "Akun $role (Username: $username) berhasil dibuat!<br>";
        }
    } else {
        echo "Akun $role sudah ada di database.<br>";
    }
}

echo "<br><a href='login.php'>Klik di sini untuk mencoba Login</a>";
?>