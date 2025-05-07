<?php
require_once 'config/database.php';

// Hapus user admin yang ada (jika ada)
$stmt = $pdo->prepare("DELETE FROM users WHERE username = 'admin'");
$stmt->execute();

// Buat user admin baru dengan password yang benar
$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
$stmt->execute([$username, $hashed_password]);

echo "Admin user telah dibuat ulang dengan password yang benar!<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<a href='index.php'>Klik di sini untuk login</a>";
?> 