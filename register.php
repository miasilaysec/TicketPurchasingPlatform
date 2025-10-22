<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Bilet Satın Alma Platformu</h2>
        <h3>Yeni Hesap Oluştur</h3>

        <?php
        // Eğer register_islemi.php'den bir hata mesajıyla yönlendirme olduysa
        if (isset($_GET['error']) && $_GET['error'] == 'email_exists') {
            echo '<p class="error-message">Bu e-posta adresi zaten kayıtlı!</p>';
        }
        ?>

        <form action="register_islemi.php" method="POST">
            <div class="form-group">
                <label for="fullname">Ad Soyad:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta Adresi:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Kayıt Ol</button>
        </form>
        <p>Zaten bir hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
    </div>
</body>
</html>