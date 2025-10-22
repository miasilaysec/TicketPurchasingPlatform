<?php
session_start();
require '../includes/db_connect.php';

// --- GÜVENLİK KONTROLLERİ ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=gecersiz_id");
    exit();
}

$sefer_id = $_GET['id'];
$company_id = $_SESSION['company_id'];
$sefer = null;

try {
    // Düzenlenecek seferin bilgilerini, bu firmanın kendi seferi olduğundan emin olarak çekiyoruz.
    $sql = "SELECT * FROM Buses WHERE id = ? AND company_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sefer_id, $company_id]);
    $sefer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Eğer sefer bulunamazsa (ya ID yanlış ya da başka firmaya ait), panele geri at.
    if (!$sefer) {
        header("Location: index.php?error=sefer_bulunamadi");
        exit();
    }
} catch (PDOException $e) {
    die("Sefer bilgileri çekilirken hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seferi Düzenle</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container" style="max-width: 600px; margin-top: 2rem;">
        <h2>Seferi Düzenle</h2>
        <form action="sefer_duzenle_islemi.php" method="POST">
            <input type="hidden" name="sefer_id" value="<?php echo $sefer['id']; ?>">

            <div class="form-group">
                <label for="departure_city">Kalkış Şehri:</label>
                <input type="text" id="departure_city" name="departure_city" value="<?php echo htmlspecialchars($sefer['departure_city']); ?>" required>
            </div>
            <div class="form-group">
                <label for="arrival_city">Varış Şehri:</label>
                <input type="text" id="arrival_city" name="arrival_city" value="<?php echo htmlspecialchars($sefer['arrival_city']); ?>" required>
            </div>
            <div class="form-group">
                <label for="departure_time">Kalkış Tarihi ve Saati:</label>
                <input type="datetime-local" id="departure_time" name="departure_time" value="<?php echo date('Y-m-d\TH:i', strtotime($sefer['departure_time'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="arrival_time">Varış Tarihi ve Saati:</label>
                <input type="datetime-local" id="arrival_time" name="arrival_time" value="<?php echo date('Y-m-d\TH:i', strtotime($sefer['arrival_time'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="seat_count">Toplam Koltuk Sayısı:</label>
                <input type="number" id="seat_count" name="seat_count" value="<?php echo $sefer['seat_count']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Bilet Fiyatı (TL):</label>
                <input type="text" id="price" name="price" value="<?php echo $sefer['price']; ?>" required>
            </div>
            <button type="submit">Değişiklikleri Kaydet</button>
        </form>
        <p><a href="index.php">Panele Geri Dön</a></p>
    </div>
</body>
</html>