<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$firma_admins = [];
try {
    // Veritabanından rolü 'Firma Admin' olan tüm kullanıcıları çekiyoruz.
    // LEFT JOIN ile bu adminlerin hangi firmaya atandığını da alıyoruz.
    $sql = "SELECT Users.*, Companies.name AS company_name 
            FROM Users 
            LEFT JOIN Companies ON Users.company_id = Companies.id 
            WHERE Users.role = 'Firma Admin' 
            ORDER BY Users.fullname ASC";
            
    $firma_admins = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Firma Adminleri çekilirken bir hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Admin Yönetimi</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .panel-container { max-width: 1000px; margin: 2rem auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="panel-container">
        <h2>Firma Admin Yönetimi</h2>
        <p>Bu panelde yeni firma yetkilileri oluşturabilir ve mevcutları yönetebilirsiniz.</p>
        
        <hr style="margin: 20px 0;">

        <a href="firma_admin_ekle.php"><button>Yeni Firma Admin Ekle</button></a>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Atandığı Firma</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($firma_admins) > 0): ?>
                    <?php foreach ($firma_admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <?php 
                                // Eğer bir firmaya atanmışsa adını yaz, değilse "Atanmamış" de.
                                echo $admin['company_name'] ? htmlspecialchars($admin['company_name']) : '<span style="color:red;">Atanmamış</span>'; 
                                ?>
                            </td>
                            <td>
                                Atanacak
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Sistemde kayıtlı Firma Admin bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="index.php">Ana Admin Paneline Dön</a>
        </div>
    </div>
</body>
</html>