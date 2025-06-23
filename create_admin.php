<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=cms_sederhana", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM visitors");
    $stmt->execute();

    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        echo $row['id'] . " - " . $row['ip_address'] . " - " . $row['visit_time'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
