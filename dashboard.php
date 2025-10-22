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
    <div class="dashboard-container">
        <h1>Hoş Geldiniz!</h1>

        <p>Merhaba, <strong><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></strong>!</p>
        <p>Kullanıcı rolünüz: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>

        <p>Burası sizin ana paneliniz. Bilet arama, satın alma ve diğer işlemleri buradan yapabileceksiniz.</p>
        <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
            <a href="index.php"><button>Yeni Sefer Ara</button></a>
            <a href="biletlerim.php"><button style="background-color: #17a2b8;">Biletlerim</button></a>
        </div>
        <a href="logout.php">
            <button class="logout-button">Çıkış Yap</button>
        </a>
    </div>
</body>
</html>