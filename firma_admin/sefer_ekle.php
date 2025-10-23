<?php
session_start();
// Güvenlik: Giriş yapılmamışsa veya rolü 'Firma Admin' değilse, ana sayfaya at.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Sefer Ekle</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container" style="max-width: 600px; margin-top: 2rem;">
        <h2>Yeni Sefer Oluştur</h2>
        <form action="sefer_ekle_islemi.php" method="POST">
            <div class="form-group">
                <label for="departure_city">Kalkış Şehri:</label>
                <input type="text" id="departure_city" name="departure_city" required>
            </div>
            <div class="form-group">
                <label for="arrival_city">Varış Şehri:</label>
                <input type="text" id="arrival_city" name="arrival_city" required>
            </div>
            <div class="form-group">
                <label for="departure_time">Kalkış Tarihi ve Saati:</label>
                <input type="datetime-local" id="departure_time" name="departure_time" required>
            </div>
            <div class="form-group">
                <label for="arrival_time">Varış Tarihi ve Saati:</label>
                <input type="datetime-local" id="arrival_time" name="arrival_time" required>
            </div>
            <div class="form-group">
                <label for="seat_count">Toplam Koltuk Sayısı:</label>
                <input type="number" id="seat_count" name="seat_count" required>
            </div>
            <div class="form-group">
                <label for="price">Bilet Fiyatı (TL):</label>
                <input type="text" id="price" name="price" required>
            </div>
            <button type="submit">Seferi Kaydet</button>
        </form>
        <p><a href="index.php">Panele Geri Dön</a></p>
    </div>
</body>
</html>