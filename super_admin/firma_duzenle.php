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
$firma = null;

try {
    // Düzenlenecek firmanın bilgilerini çek
    $sql = "SELECT * FROM Companies WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$firma_id]);
    $firma = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firma) {
        header("Location: index.php?error=firma_bulunamadi");
        exit();
    }
} catch (PDOException $e) {
    die("Firma bilgileri çekilirken hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firmayı Düzenle</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container" style="margin-top: 2rem;">
        <h2>Firmayı Düzenle</h2>
        <form action="firma_duzenle_islemi.php" method="POST">
            <input type="hidden" name="firma_id" value="<?php echo $firma['id']; ?>">

            <div class="form-group">
                <label for="name">Firma Adı:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($firma['name']); ?>" required>
            </div>
            <button type="submit">Değişiklikleri Kaydet</button>
        </form>
        <p><a href="index.php">Panele Geri Dön</a></p>
    </div>
</body>
</html>