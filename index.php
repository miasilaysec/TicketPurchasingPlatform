<?php
session_start(); // Kullanıcının giriş yapıp yapmadığını bilmek için oturumu başlat
require 'includes/db_connect.php'; // Veritabanı bağlantısını dahil et

$seferler = []; // Seferleri tutacağımız boş bir dizi
$arama_yapildi = false;

// Eğer form GET metodu ile gönderildiyse (kullanıcı arama yaptıysa)
if (isset($_GET['kalkis']) && isset($_GET['varis']) && !empty($_GET['kalkis']) && !empty($_GET['varis'])) {
    $arama_yapildi = true;
    $kalkis_sehri = $_GET['kalkis'];
    $varis_sehri = $_GET['varis'];

    // Veritabanından seferleri arama sorgusu
    // Birebir eşleşme için LIKE yerine = kullanabiliriz.
    // Farklı şehir isimleri (örn: İstanbul, Istanbul) için LIKE daha esnek olabilir.
    $sql = "SELECT * FROM Buses WHERE departure_city LIKE ? AND arrival_city LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$kalkis_sehri%", "%$varis_sehri%"]);
    $seferler = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF--8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilet Satın Alma Platformu</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Sadece bu sayfa için ek stiller */
        .main-container { max-width: 800px; }
        .search-form { background-color: #e9ecef; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
        .search-results { margin-top: 2rem; }
        .trip-item { background-color: #fff; border: 1px solid #ddd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .trip-item h4 { margin-top: 0; }
    </style>
</head>
<body>

    <div class="main-container">
        <h2 style="text-align: center;">Otobüs Bileti Bul</h2>

        <div class="search-form">
            <form action="index.php" method="GET">
                <div class="form-group">
                    <label for="kalkis">Nereden:</label>
                    <input type="text" id="kalkis" name="kalkis" placeholder="Örn: Mardin" required>
                </div>
                <div class="form-group">
                    <label for="varis">Nereye:</label>
                    <input type="text" id="varis" name="varis" placeholder="Örn: İstanbul" required>
                </div>
                <button type="submit">Sefer Ara</button>
            </form>
        </div>

        <div class="search-results">
            <?php if ($arama_yapildi): ?>
                <h3>Arama Sonuçları</h3>
                <?php if (count($seferler) > 0): ?>
                    <?php foreach ($seferler as $sefer): ?>
                        <div class="trip-item">
                            <h4><?php echo htmlspecialchars($sefer['departure_city']); ?> -> <?php echo htmlspecialchars($sefer['arrival_city']); ?></h4>
                            <p>
                                <strong>Kalkış Zamanı:</strong> <?php echo date('d M Y H:i', strtotime($sefer['departure_time'])); ?>
                            </p>
                            <p>
                                <strong>Fiyat:</strong> <?php echo htmlspecialchars($sefer['price']); ?> TL
                            </p>
                            <a href="sefer_detay.php?id=<?php echo $sefer['id']; ?>">
                                <button>Detayları Gör</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aradığınız kriterlere uygun sefer bulunamadı.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>