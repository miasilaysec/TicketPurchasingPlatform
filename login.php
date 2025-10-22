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
        <i class="fa-solid fa-right-to-bracket fa-2x" style="color: #007bff; margin-bottom: 1rem;"></i>
        <h2>Tekrar Hoş Geldiniz!</h2>
        <p style="color: #6c757d; margin-top:-10px;">Giriş yaparak biletlerinizi yönetin.</p>
        
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 'registered') {
            echo '<p class="success-message">Kayıt başarılı! Lütfen giriş yapın.</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') {
            echo '<p class="error-message">E-posta veya şifre hatalı!</p>';
        }
        ?>

        <form action="login_islemi.php" method="POST">
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> E-posta Adresi:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Giriş Yap</button>
        </form>
        <p style="margin-top: 1rem;">Hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
    </div>
</body>
</html>