<?php
// Hata raporlamayı açarak sorunun ne olduğunu görelim
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// --- DÜZELTME BURADA ---
// 'require' komutu, dosyanın bulunduğu yere göre çalışır.
// Bu yüzden tam dosya yolunu belirtmek en garantisidir.
require __DIR__ . '/includes/db_connect.php';

// Güvenlik: Kullanıcı giriş yapmamışsa login sayfasına yönlendir.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$biletler = [];

// ... sayfanın geri kalan kodu aynı ...
try {
    // Veritabanından kullanıcının biletlerini çekmek için kapsamlı bir sorgu
    // 3 tabloyu birleştiriyoruz: Tickets, Buses, Companies
    $sql = "SELECT 
                Tickets.id AS ticket_id,
                Tickets.seat_number,
                Tickets.purchase_price,
                Buses.departure_city,
                Buses.arrival_city,
                Buses.departure_time,
                Companies.name AS company_name
            FROM Tickets
            JOIN Buses ON Tickets.bus_id = Buses.id
            JOIN Companies ON Buses.company_id = Companies.id
            WHERE Tickets.user_id = ?
            ORDER BY Buses.departure_time DESC"; // Biletleri en yeniden en eskiye sırala

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $biletler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Biletler çekilirken bir hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biletlerim</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-container { max-width: 900px; margin: auto; }
        .ticket-card { background: #fff; border: 1px solid #ddd; border-left: 5px solid #007bff; border-radius: 8px; margin-bottom: 1rem; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .ticket-info h4 { margin: 0; }
        .ticket-actions button { padding: 0.5rem 1rem; }
        .no-tickets { background: #fff; padding: 2rem; text-align: center; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="fa-solid fa-ticket fa-3x" style="color: #007bff;"></i>
            <h2 style="margin-top: 1rem;">Satın Alınan Biletler</h2>
        </div>
        
        <?php if (isset($_GET['error']) && $_GET['error'] == 'time_limit_exceeded'): ?>
            <p class="error-message">İptal işlemi başarısız! Seferin kalkışına 1 saatten az bir süre kaldığı için bilet iptal edilemez.</p>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'cancellation_complete'): ?>
            <p class="success-message">Biletiniz başarıyla iptal edildi ve ücret iadesi yapıldı.</p>
        <?php endif; ?>

        <?php if (count($biletler) > 0): ?>
            <?php foreach ($biletler as $bilet): ?>
                <div class="card">
                    <h4 class="card-header">
                        <?php echo htmlspecialchars($bilet['departure_city']); ?> -> <?php echo htmlspecialchars($bilet['arrival_city']); ?>
                    </h4>
                    <div style="padding: 1rem 0;">
                        <p><i class="fa-solid fa-building"></i> <strong>Firma:</strong> <?php echo htmlspecialchars($bilet['company_name']); ?></p>
                        <p><i class="fa-solid fa-calendar-day"></i> <strong>Kalkış:</strong> <?php echo date('d M Y, H:i', strtotime($bilet['departure_time'])); ?></p>
                        <p><i class="fa-solid fa-chair"></i> <strong>Koltuk No:</strong> <?php echo $bilet['seat_number']; ?> | <i class="fa-solid fa-lira-sign"></i> <strong>Ödenen Tutar:</strong> <?php echo $bilet['purchase_price']; ?> TL</p>
                    </div>
                    <div style="text-align: right;">
                        <a href="bilet_pdf.php?ticket_id=<?php echo $bilet['ticket_id']; ?>" target="_blank">
                            <button class="btn-secondary" style="width: auto; padding: 8px 12px;"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                        </a>
                        <form action="bilet_iptal.php" method="POST" style="display: inline;" onsubmit="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz?');">
                            <input type="hidden" name="ticket_id" value="<?php echo $bilet['ticket_id']; ?>">
                            <button type="submit" class="btn-danger" style="width: auto; padding: 8px 12px;"><i class="fa-solid fa-trash-alt"></i> İptal Et</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="text-align: center;">
                <h3>Henüz satın alınmış bir biletiniz bulunmamaktadır.</h3>
                <a href="index.php"><button style="width: auto; margin-top: 1rem;">Hemen Bilet Al</button></a>
            </div>
        <?php endif; ?>
        <p style="text-align: center; margin-top: 1rem;"><a href="dashboard.php">Ana Panele Dön</a></p>
    </div>
</body>
</html>