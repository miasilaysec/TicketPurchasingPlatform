<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik kontrolleri
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Formdan gelen verileri al
$firma_id = $_POST['firma_id'];
$firma_adi = $_POST['name'];

try {
    // Veritabanındaki firma adını güncelle
    $sql = "UPDATE Companies SET name = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$firma_adi, $firma_id]);

    // Başarılı olursa ana panele geri yönlendir
    header("Location: index.php?success=firma_guncellendi");
    exit();

} catch (PDOException $e) {
    // Eğer yeni firma adı zaten mevcutsa, UNIQUE hatası verebilir.
    if ($e->getCode() == '23000') {
         header("Location: firma_duzenle.php?id=$firma_id&error=firma_mevcut");
         exit();
    }
    // Başka bir hata olursa ekrana yazdır
    die("Firma güncellenirken bir hata oluştu: " . $e->getMessage());
}
?>