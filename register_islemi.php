<?php
// Sadece POST metodu ile bu sayfaya erişim izni veriyoruz.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// Veritabanı bağlantı dosyamızı dahil ediyoruz.
require 'includes/db_connect.php';

// Formdan gelen verileri değişkenlere alıyoruz.
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];

// *** GÜVENLİK İÇİN EN ÖNEMLİ ADIM: ŞİFREYİ HASHLEME ***
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // SQL Injection saldırılarını önlemek için '?' yer tutucularını kullanıyoruz.
    $sql = "INSERT INTO Users (fullname, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fullname, $email, $hashed_password]);

    // Kayıt başarılı olduysa, kullanıcıyı giriş sayfasına yönlendir.
    header('Location: login.php?success=registered');
    exit();

} catch (PDOException $e) {
    // Hata kodu 23000 SQLite'da "benzersizlik kuralı ihlali" demektir.
    // E-posta adresi UNIQUE olduğu için, bu hata girilen e-postanın zaten var olduğu anlamına gelir.
    if ($e->getCode() == '23000') {
        header('Location: register.php?error=email_exists');
        exit();
    } else {
        // Başka bir veritabanı hatası varsa, ekrana hatayı yazdır.
        die("Kayıt sırasında bir veritabanı hatası oluştu: " . $e->getMessage());
    }
}
?>