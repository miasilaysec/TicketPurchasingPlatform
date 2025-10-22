<?php
session_start();
require 'includes/db_connect.php';

// Adres çubuğundan gelen sefer ID'sini alıyoruz.
// Eğer ID yoksa veya geçerli bir sayı değilse, ana sayfaya yönlendiriyoruz.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$sefer_id = $_GET['id'];

// Veritabanından ilgili seferin ve ait olduğu firmanın bilgilerini çekiyoruz.
// JOIN komutu ile Buses ve Companies tablolarını birleştiriyoruz.
$sql = "SELECT Buses.*, Companies.name AS company_name 
        FROM Buses 
        JOIN Companies ON Buses.company_id = Companies.id 
        WHERE Buses.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$sefer_id]);
$sefer = $stmt->fetch(PDO::FETCH_ASSOC);

// Eğer bu ID'ye sahip bir sefer bulunamazsa, ana sayfaya yönlendir.
if (!$sefer) {
    header("Location: index.php?error=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sefer Detayları</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .detail-container { max-width: 600px; margin: auto; }
        .trip-details { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .trip-details h2 { margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 0.5rem; }
        .trip-details p { font-size: 1.1rem; line-height: 1.6; }
        .buy-button { background-color: #28a745; } /* Yeşil renk */
        .buy-button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-header">
                <i class="fa-solid fa-bus"></i> Sefer Detayları
            </h2>
            <div style="padding: 1rem;">
                <h3>
                    <?php echo htmlspecialchars($sefer['departure_city']); ?> 
                    <i class="fa-solid fa-arrow-right" style="font-size: 1rem; color: #6c757d;"></i> 
                    <?php echo htmlspecialchars($sefer['arrival_city']); ?>
                </h3>
                <p><i class="fa-solid fa-building"></i> <strong>Firma:</strong> <?php echo htmlspecialchars($sefer['company_name']); ?></p>
                <p><i class="fa-solid fa-clock"></i> <strong>Kalkış Zamanı:</strong> <?php echo date('d F Y, H:i', strtotime($sefer['departure_time'])); ?></p>
                <p><i class="fa-solid fa-flag-checkered"></i> <strong>Tahmini Varış:</strong> <?php echo date('d F Y, H:i', strtotime($sefer['arrival_time'])); ?></p>
                <p><i class="fa-solid fa-chair"></i> <strong>Toplam Koltuk:</strong> <?php echo htmlspecialchars($sefer['seat_count']); ?></p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #28a745;"><i class="fa-solid fa-tags"></i> Fiyat: <?php echo htmlspecialchars($sefer['price']); ?> TL</p>
                
                <hr style="margin: 1.5rem 0;">
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="bilet_al.php?sefer_id=<?php echo $sefer['id']; ?>"><button class="btn-success" style="width: 100%;"><i class="fa-solid fa-ticket-alt"></i> Hemen Bilet Al</button></a>
                <?php else: ?>
                    <p class="error-message">Bilet satın alabilmek için lütfen <a href="login.php">giriş yapın</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        <p style="text-align: center; margin-top: 1rem;"><a href="index.php">Yeni Arama Yap</a></p>
    </div>
</body>
</html>