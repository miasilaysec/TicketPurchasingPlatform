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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
        <h2 style="text-align: center; font-weight: 700; font-size: 2.2rem;">Hayalindeki Yolculuğu Planla</h2>
        <p style="text-align: center; color: #6c757d; margin-top: -10px;">Tek tıkla yüzlerce sefer arasından biletini bul.</p>

        <div class="search-form">
            <form action="index.php" method="GET">
                <div class="form-group">
                    <label for="kalkis"><i class="fas fa-plane-departure icon-prefix"></i> Nereden</label>
                    <input type="text" id="kalkis" name="kalkis" placeholder="Kalkış Şehri" required>
                </div>
                <div class="form-group">
                    <label for="varis"><i class="fas fa-plane-arrival icon-prefix"></i> Nereye</label>
                    <input type="text" id="varis" name="varis" placeholder="Varış Şehri" required>
                </div>
                <button type="submit"><i class="fas fa-search"></i> Seferleri Bul</button>
            </form>
        </div>

        <div class="search-results">
            <?php if ($arama_yapildi): ?>
                <?php if (count($seferler) > 0): ?>
                    <?php foreach ($seferler as $sefer): ?>
                        
                        <div class="bus-ticket">
                            <div class="ticket-main">
                                <h4 style="margin: 0; font-size: 1.4rem; font-weight: 600;">
                                    <?php echo htmlspecialchars($sefer['departure_city']); ?> 
                                    <i class="fas fa-long-arrow-alt-right" style="color:#ccc; margin: 0 10px;"></i> 
                                    <?php echo htmlspecialchars($sefer['arrival_city']); ?>
                                </h4>
                                <p style="margin: 0.5rem 0; color: #6c757d;">
                                    <i class="fas fa-calendar-alt" style="color:#999; margin-right: 5px;"></i> 
                                    <strong>Kalkış:</strong> <?php echo date('d M Y, H:i', strtotime($sefer['departure_time'])); ?>
                                </p>
                            </div>
                            <div class="ticket-rip"></div>
                            <div class="ticket-actions">
                                <div class="ticket-price"><?php echo htmlspecialchars($sefer['price']); ?> TL</div>
                                <a href="sefer_detay.php?id=<?php echo $sefer['id']; ?>">
                                    <button class="btn-details">Detaylar</button>
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="search-form" style="text-align: center;">
                        <p>Aradığınız kriterlere uygun sefer bulunamadı.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>