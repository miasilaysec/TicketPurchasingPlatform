<?php
session_start();
require 'includes/db_connect.php';

// --- GÜVENLİK ADIMI 1: GİRİŞ KONTROLÜ ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=notloggedin");
    exit();
}

// --- VERİ ÇEKME ADIMI 2: SEFER BİLGİLERİ ---
if (!isset($_GET['sefer_id']) || !is_numeric($_GET['sefer_id'])) {
    header("Location: index.php");
    exit();
}

$sefer_id = $_GET['sefer_id'];
$user_id = $_SESSION['user_id'];

try {
    // Sefer bilgilerini çek (fiyat, koltuk sayısı)
    $stmt_bus = $pdo->prepare("SELECT * FROM Buses WHERE id = ?");
    $stmt_bus->execute([$sefer_id]);
    $sefer = $stmt_bus->fetch(PDO::FETCH_ASSOC);

    if (!$sefer) {
        header("Location: index.php?error=seferbulunamadi");
        exit();
    }

    // --- VERİ ÇEKME ADIMI 3: KULLANICI BAKİYESİ ---
    $stmt_user = $pdo->prepare("SELECT balance FROM Users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $kullanici = $stmt_user->fetch(PDO::FETCH_ASSOC);
    $bakiye = $kullanici['balance'];

    // --- VERİ ÇEKME ADIMI 4: DOLU KOLTUKLAR ---
    // Bu sefere (bus_id) ait satılmış tüm biletlerin koltuk numaralarını çekiyoruz.
    $stmt_tickets = $pdo->prepare("SELECT seat_number FROM Tickets WHERE bus_id = ?");
    $stmt_tickets->execute([$sefer_id]);
    $dolu_koltuklar_dizisi = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);

    // Veritabanından gelen [{'seat_number': 5}, {'seat_number': 12}] şeklindeki diziyi
    // [5, 12] şeklinde basit bir diziye dönüştürüyoruz.
    $dolu_koltuklar_listesi = array_column($dolu_koltuklar_dizisi, 'seat_number');

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

$bilet_fiyati = $sefer['price'];
$toplam_koltuk_sayisi = $sefer['seat_count'];

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koltuk Seç ve Satın Al</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .purchase-container { max-width: 800px; margin: auto; }
        .seat-map { background: #f4f4f4; border: 1px solid #ccc; padding: 1rem; border-radius: 8px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
        .seat { padding: 10px; text-align: center; border-radius: 5px; cursor: pointer; border: none; font-weight: bold; }
        .seat.available { background-color: #28a745; color: white; } /* Yeşil - Boş */
        .seat.available:hover { background-color: #218838; }
        .seat.sold { background-color: #dc3545; color: white; cursor: not-allowed; } /* Kırmızı - Dolu */
        .seat.info { background-color: transparent; text-align: left; padding: 0; }
        .info-box { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Koltuk Seç ve Satın Al</h2>

        <div class="card">
            <h4 class="card-header">Sefer ve Bakiye Bilgileri</h4>
            <div style="padding: 1rem;">
                <p><i class="fa-solid fa-route"></i> <?php echo htmlspecialchars($sefer['departure_city']); ?> -> <?php echo htmlspecialchars($sefer['arrival_city']); ?></p>
                <p><i class="fa-solid fa-tags"></i> <strong>Bilet Fiyatı:</strong> <?php echo $bilet_fiyati; ?> TL</p>
                <hr>
                <p><i class="fa-solid fa-wallet"></i> <strong>Mevcut Bakiyeniz:</strong> <?php echo $bakiye; ?> TL</p>
            </div>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 'balance'): ?> <p class="error-message">İşlem başarısız! Yetersiz bakiye.</p>
            <?php elseif ($_GET['error'] == 'seat_taken'): ?> <p class="error-message">İşlem başarısız! Seçtiğiniz koltuk sizden hemen önce başkası tarafından alındı.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($bakiye < $bilet_fiyati): ?>
            <p class="error-message">Bu bileti almak için bakiyeniz yetersiz.</p>
        <?php else: ?>
            <div class="card">
                <h4 class="card-header">Koltuk Seçiniz</h4>
                <form action="odeme_islemi.php" method="POST">
                    <input type="hidden" name="sefer_id" value="<?php echo $sefer_id; ?>">
                    <div class="seat-map">
                        <div class="seat info"><strong>Şoför</strong></div>
                        <div class="seat info"></div><div class="seat info"></div><div class="seat info"></div><div class="seat info"></div>

                        <?php for ($i = 1; $i <= $toplam_koltuk_sayisi; $i++): ?>
                            <?php if (in_array($i, $dolu_koltuklar_listesi)): ?>
                                <button type="button" class="seat sold" disabled><?php echo $i; ?></button>
                            <?php else: ?>
                                <button type="submit" name="koltuk_no" value="<?php echo $i; ?>" class="seat available"><?php echo $i; ?></button>
                            <?php endif; ?>
                            <?php if ($i % 2 == 0 && $i % 4 != 0) { echo '<div class="seat info"></div>'; } ?>
                        <?php endfor; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <p style="text-align: center; margin-top: 1rem;"><a href="javascript:history.back()">Geri Dön</a></p>
    </div>
</body>
</html>