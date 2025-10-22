<?php
session_start();
require '../includes/db_connect.php'; // Bir üst klasördeki dosyayı çağırıyoruz

// --- GÜVENLİK ---
// Giriş yapılmamışsa veya rolü 'Firma Admin' değilse, ana sayfaya at.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php"); // Ana dizindeki login sayfasına yönlendir
    exit();
}

$company_id = $_SESSION['company_id'];
$seferler = [];

try {
    // Sadece bu firma admininin kendi firmasına ait seferleri çek (company_id = ?)
    $sql = "SELECT * FROM Buses WHERE company_id = ? ORDER BY departure_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$company_id]);
    $seferler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Seferler çekilirken bir hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Admin Paneli</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .panel-container { max-width: 1000px; margin: 2rem auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .action-buttons a, .action-buttons button { margin-right: 5px; }
        .logout-button { background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; float: right; }
    </style>
</head>
<body>
    <div class="panel-container">
        <a href="../logout.php"><button class="logout-button">Çıkış Yap</button></a>
        <h2>Firma Yönetim Paneli</h2>
        <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>!</p>
        
        <hr style="margin: 20px 0;">

        <h3>Seferleriniz</h3>
        <a href="sefer_ekle.php"><button>Yeni Sefer Ekle</button></a>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>Güzergah</th>
                    <th>Kalkış Zamanı</th>
                    <th>Fiyat</th>
                    <th>Koltuk Sayısı</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($seferler) > 0): ?>
                    <?php foreach ($seferler as $sefer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sefer['departure_city']); ?> -> <?php echo htmlspecialchars($sefer['arrival_city']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($sefer['departure_time'])); ?></td>
                            <td><?php echo $sefer['price']; ?> TL</td>
                            <td><?php echo $sefer['seat_count']; ?></td>
                            <td class="action-buttons">
                                <a href="sefer_duzenle.php?id=<?php echo $sefer['id']; ?>"><button>Düzenle</button></a>
                                <a href="sefer_sil.php?id=<?php echo $sefer['id']; ?>" onclick="return confirm('Bu seferi kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.');"><button style="background-color: #dc3545;">Sil</button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Henüz firmanıza ait bir sefer bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>