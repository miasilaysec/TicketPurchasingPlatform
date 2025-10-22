<?php
// Oturumu başlatıyoruz. Bu, login_islemi.php'de kaydettiğimiz bilgilere ulaşmamızı sağlar.
session_start();

// Kullanıcının giriş yapıp yapmadığını kontrol ediyoruz.
// Eğer $_SESSION['user_id'] değeri ayarlanmamışsa (yoksa), kullanıcı giriş yapmamış demektir.
if (!isset($_SESSION['user_id'])) {
    // Giriş yapmamış kullanıcıyı login sayfasına yönlendiriyoruz.
    header("Location: login.php");
    exit(); // Kodun devamının çalışmasını engelliyoruz.
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Sadece bu sayfa için ek CSS stilleri */
        .dashboard-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logout-button {
            background-color: #dc3545; /* Kırmızı renk */
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            width: auto; /* Otomatik genişlik */
        }
        .logout-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px; text-align: center;">
        <div class="card">
            <div style="padding: 1rem;">
                <i class="fa-solid fa-user-check fa-3x" style="color: #28a745; margin-bottom: 1rem;"></i>
                <h2 class="card-header">Tekrar Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>!</h2>
                <p style="color: #6c757d;">Biletlerinizi yönetebilir veya yeni yolculuklar planlayabilirsiniz.</p>
                
                <div style="margin-top: 2rem; border-top: 1px solid #eee; padding-top: 2rem;">
                    <a href="index.php">
                        <button><i class="fa-solid fa-search"></i> Yeni Sefer Ara</button>
                    </a>
                    <a href="biletlerim.php" style="text-decoration: none;">
                        <button class="btn-secondary" style="margin-top: 1rem;"><i class="fa-solid fa-ticket"></i> Biletlerimi Görüntüle</button>
                    </a>
                </div>
            </div>
        </div>
        <p style="margin-top: 1.5rem;"><a href="logout.php">Çıkış Yap</a></p>
    </div>
</body>
</html>