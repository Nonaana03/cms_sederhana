<?php
$host = 'localhost';
$username = 'root';
$password = ''; // kosong jika menggunakan XAMPP
$dbname = 'cms_sederhana';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?> 