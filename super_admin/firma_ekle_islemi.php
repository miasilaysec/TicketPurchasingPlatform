<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik kontrolleri
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Formdan gelen firma adını al
$firma_adi = $_POST['name'];

try {
    // Veritabanına yeni firmayı ekle
    // Firma adının UNIQUE (benzersiz) olduğundan emin olmak için veritabanı kuralı zaten var.
    $sql = "INSERT INTO Companies (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$firma_adi]);

    // Başarılı olursa ana panele geri yönlendir
    header("Location: index.php?success=firma_eklendi");
    exit();

} catch (PDOException $e) {
    // Eğer firma adı zaten mevcutsa, veritabanı UNIQUE kuralı nedeniyle hata verecektir.
    if ($e->getCode() == '23000') {
         header("Location: firma_ekle.php?error=firma_mevcut");
         exit();
    }
    // Başka bir hata olursa ekrana yazdır
    die("Firma eklenirken bir hata oluştu: " . $e->getMessage());
}
?>