<?php
// Oturumu (session) başlatıyoruz. Kullanıcı bilgilerini saklamak için bu gereklidir.
session_start();

// Sadece POST metodu ile bu sayfaya erişim izni veriyoruz.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Veritabanı bağlantı dosyamızı dahil ediyoruz.
require 'includes/db_connect.php';

// Formdan gelen verileri değişkenlere alıyoruz.
$email = $_POST['email'];
$password = $_POST['password'];

try {
    // Kullanıcıyı e-posta adresine göre veritabanından bulmak için sorgumuzu hazırlıyoruz.
    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Kullanıcıyı dizi olarak alıyoruz

    // Kullanıcı bulunduysa VE girdiği şifre veritabanındaki hash ile eşleşiyorsa...
    if ($user && password_verify($password, $user['password'])) {
        // Giriş başarılıdır.

        // Kullanıcının önemli bilgilerini session'a kaydediyoruz.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fullname'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['company_id'] = $user['company_id']; // Firma Admin'i için bu gerekli.

        // --- ROLE GÖRE YÖNLENDİRME MANTIĞI ---
        if ($user['role'] == 'Admin') {
            // Eğer rol Admin ise, onu süper admin paneline yönlendir.
            header("Location: super_admin/index.php");
        } elseif ($user['role'] == 'Firma Admin') {
            // Eğer rol Firma Admin ise, onu firma paneline yönlendir.
            header("Location: firma_admin/index.php");
        } else {
         // Diğer tüm roller (User) için normal kullanıcı paneline yönlendir.
            header("Location: dashboard.php");
        }
        exit(); // Yönlendirme sonrası kodun çalışmasını durdur!

    } else {
        // Kullanıcı bulunamadıysa VEYA şifre yanlışsa,
        // giriş sayfasına hata mesajıyla geri yönlendiriyoruz.
        header("Location: login.php?error=invalid_credentials");
        exit();
    }

} catch (PDOException $e) {
    // Veritabanı hatası olursa programı durdur ve hatayı göster.
    die("Giriş sırasında bir veritabanı hatası oluştu: " . $e->getMessage());
}
?>