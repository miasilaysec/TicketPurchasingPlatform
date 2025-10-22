<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Atama yapmak için sistemdeki tüm firmaları çekiyoruz.
$firmalar = $pdo->query("SELECT id, name FROM Companies ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Firma Admin Ekle</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container" style="max-width: 600px; margin-top: 2rem;">
        <h2>Yeni Firma Admin Oluştur</h2>
        <form action="firma_admin_ekle_islemi.php" method="POST">
            <div class="form-group">
                <label for="fullname">Ad Soyad:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta Adresi:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Geçici Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="company_id">Atanacak Firma:</label>
                <select id="company_id" name="company_id" required>
                    <option value="">-- Firma Seçin --</option>
                    <?php foreach ($firmalar as $firma): ?>
                        <option value="<?php echo $firma['id']; ?>"><?php echo htmlspecialchars($firma['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Firma Adminini Kaydet</button>
        </form>
        <p><a href="firma_admin_yonetimi.php">Yönetim Paneline Geri Dön</a></p>
    </div>
</body>
</html>