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
        <i class="fa-solid fa-user-plus fa-2x" style="color: #007bff; margin-bottom: 1rem;"></i>
        <h2>Aramıza Katılın</h2>
        <p style="color: #6c757d; margin-top:-10px;">Hızlı ve güvenli yolculuk için hesap oluşturun.</p>

        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'email_exists') {
            echo '<p class="error-message">Bu e-posta adresi zaten kayıtlı!</p>';
        }
        ?>

        <form action="register_islemi.php" method="POST">
            <div class="form-group">
                <label for="fullname"><i class="fa-solid fa-user"></i> Ad Soyad:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> E-posta Adresi:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Hesap Oluştur</button>
        </form>
        <p style="margin-top: 1rem;">Zaten bir hesabınız var mı? <a href="login.php">Giriş Yapın</a></p>
    </div>
</body>
</html>