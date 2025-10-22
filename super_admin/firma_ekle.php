<?php
session_start();
// Güvenlik: Giriş yapılmamışsa veya rolü 'Admin' değilse, ana sayfaya at.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Firma Ekle</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container" style="margin-top: 2rem;">
        <h2>Yeni Firma Oluştur</h2>
        <form action="firma_ekle_islemi.php" method="POST">
            <div class="form-group">
                <label for="name">Firma Adı:</label>
                <input type="text" id="name" name="name" placeholder="Örn: Pamukkale Turizm" required>
            </div>
            <button type="submit">Firmayı Kaydet</button>
        </form>
        <p><a href="index.php">Panele Geri Dön</a></p>
    </div>
</body>
</html>