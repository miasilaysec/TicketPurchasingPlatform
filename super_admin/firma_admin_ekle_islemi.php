<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Formdan gelen verileri al
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$company_id = $_POST['company_id'];
$role = 'Firma Admin'; // Rolü doğrudan belirliyoruz.

// Şifreyi güvenli bir şekilde hash'liyoruz.
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Veritabanına yeni Firma Admin'i ekle
    $sql = "INSERT INTO Users (fullname, email, password, role, company_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fullname, $email, $hashed_password, $role, $company_id]);

    // Başarılı olursa yönetim paneline geri yönlendir
    header("Location: firma_admin_yonetimi.php?success=admin_eklendi");
    exit();

} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
         header("Location: firma_admin_ekle.php?error=email_mevcut");
         exit();
    }
    die("Firma Admin eklenirken bir hata oluştu: " . $e->getMessage());
}
?>