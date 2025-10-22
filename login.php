<?php session_start(); // Sayfaya gelen mesajları veya oturum bilgilerini yönetmek için session'ı başlatıyoruz. ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Bilet Satın Alma Platformu</h2>
        <h3>Giriş Yap</h3>

        <?php
        // register.php'den başarılı bir kayıt sonrası yönlendirme olduysa
        if (isset($_GET['success']) && $_GET['success'] == 'registered') {
            echo '<p class="success-message">Kayıt başarılı! Lütfen giriş yapın.</p>';
        }

        // login_islemi.php'den hatalı giriş denemesi sonrası yönlendirme olduysa
        if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
            echo '<p class="error-message">E-posta veya şifre hatalı!</p>';
        }
        ?>

        <form action="login_islemi.php" method="POST">
            <div class="form-group">
                <label for="email">E-posta Adresi:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
    </div>
</body>
</html>