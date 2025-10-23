<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik kontrolleri
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=gecersiz_id");
    exit();
}

$firma_id = $_GET['id'];

try {
    // "Ya hep ya hiç" kuralı için transaction başlatıyoruz.
    $pdo->beginTransaction();

    // Adım 1: Bu firmaya ait tüm seferleri (Buses) sil.
    $sql_delete_buses = "DELETE FROM Buses WHERE company_id = ?";
    $stmt_delete_buses = $pdo->prepare($sql_delete_buses);
    $stmt_delete_buses->execute([$firma_id]);

    // Adım 2: Seferler silindikten sonra firmanın kendisini (Companies) sil.
    $sql_delete_company = "DELETE FROM Companies WHERE id = ?";
    $stmt_delete_company = $pdo->prepare($sql_delete_company);
    $stmt_delete_company->execute([$firma_id]);

    // Tüm işlemler başarılı, onayla.
    $pdo->commit();

    // Başarılı olursa ana panele geri yönlendir.
    header("Location: index.php?success=firma_silindi");
    exit();

} catch (PDOException $e) {
    // Herhangi bir hata olursa tüm işlemleri geri al.
    $pdo->rollBack();
    die("Firma silinirken bir hata oluştu: " . $e->getMessage());
}
?>