<?php
session_start();
require '../includes/db_connect.php'; // Bir üst klasördeki dosyayı çağırıyoruz

// --- GÜVENLİK ---
// Giriş yapılmamışsa veya rolü 'Admin' değilse, ana sayfaya at.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$firmalar = [];
try {
    // Veritabanındaki tüm firmaları çek
    $firmalar = $pdo->query("SELECT * FROM Companies ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Firmalar çekilirken bir hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Süper Admin Paneli</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .panel-container { max-width: 1000px; margin: 2rem auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .action-buttons a, .action-buttons button { margin-right: 5px; }
        .logout-button { background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; float: right; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="panel-container">
        <div class="panel-header">
            <h2>Süper Admin Paneli</h2>
            <a href="../logout.php"><button class="logout-button">Çıkış Yap</button></a>
        </div>
        <p>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>!</p>
        
        <hr style="margin: 20px 0;">

        <h3>Firma Yönetimi</h3>
        <a href="firma_ekle.php"><button>Yeni Firma Ekle</button></a>
        <a href="firma_admin_yonetimi.php"><button style="background-color: #17a2b8;">Firma Adminlerini Yönet</button></a>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>Firma Adı</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($firmalar) > 0): ?>
                    <?php foreach ($firmalar as $firma): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($firma['name']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($firma['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="firma_duzenle.php?id=<?php echo $firma['id']; ?>"><button>Düzenle</button></a>
                                <a href="firma_sil.php?id=<?php echo $firma['id']; ?>" onclick="return confirm('Bu firmayı kalıcı olarak silmek istediğinizden emin misiniz? Bu firmaya ait tüm seferler de silinecektir ve bu işlem geri alınamaz!');"><button style="background-color: #dc3545;">Sil</button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">Sistemde kayıtlı firma bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>