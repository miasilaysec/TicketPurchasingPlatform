<?php
session_start();
require '../includes/db_connect.php';

// --- GÜVENLİK KONTROLLERİ ---
// 1. Kullanıcı giriş yapmış mı ve rolü doğru mu?
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php");
    exit();
}

// 2. Silinecek seferin ID'si adres çubuğunda (GET) gönderilmiş mi?
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=gecersiz_id");
    exit();
}

$sefer_id = $_GET['id'];
$company_id = $_SESSION['company_id']; // Giriş yapmış adminin firma ID'si

try {
    // --- YETKİ KONTROLÜ (ÇOK ÖNEMLİ!) ---
    // Silinmek istenen seferin, bu firma admininin kendi firmasına ait olup olmadığını kontrol et.
    // Bu, bir firmanın başka bir firmanın seferini silmesini engeller.
    $sql_check = "SELECT id FROM Buses WHERE id = ? AND company_id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$sefer_id, $company_id]);
    
    if ($stmt_check->fetch() === false) {
        // Eğer sorgu sonuç döndürmezse, bu sefer bu firmaya ait değildir.
        header("Location: index.php?error=yetkisiz_islem");
        exit();
    }

    // Yetki kontrolü başarılı, silme işlemini yap.
    $sql_delete = "DELETE FROM Buses WHERE id = ?";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([$sefer_id]);

    // Başarılı olursa ana panele geri yönlendir.
    header("Location: index.php?success=sefer_silindi");
    exit();

} catch (PDOException $e) {
    // Hata olursa ekrana yazdır.
    die("Sefer silinirken bir hata oluştu: " . $e->getMessage());
}
?>