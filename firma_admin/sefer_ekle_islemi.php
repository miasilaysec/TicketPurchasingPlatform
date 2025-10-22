<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik kontrolleri
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php");
    exit();
}

// Formdan gelen verileri al
$departure_city = $_POST['departure_city'];
$arrival_city = $_POST['arrival_city'];
$departure_time = $_POST['departure_time'];
$arrival_time = $_POST['arrival_time'];
$seat_count = $_POST['seat_count'];
$price = $_POST['price'];
$company_id = $_SESSION['company_id']; // Giriş yapmış adminin firma ID'si

try {
    // Veritabanına yeni seferi ekle
    $sql = "INSERT INTO Buses (company_id, departure_city, arrival_city, departure_time, arrival_time, seat_count, price) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    // Tarih formatını veritabanının anlayacağı 'Y-m-d H:i:s' formatına çeviriyoruz.
    $formatted_departure_time = date('Y-m-d H:i:s', strtotime($departure_time));
    $formatted_arrival_time = date('Y-m-d H:i:s', strtotime($arrival_time));
    
    $stmt->execute([
        $company_id,
        $departure_city,
        $arrival_city,
        $formatted_departure_time,
        $formatted_arrival_time,
        $seat_count,
        $price
    ]);

    // Başarılı olursa ana panele geri yönlendir
    header("Location: index.php?success=sefer_eklendi");
    exit();

} catch (PDOException $e) {
    // Hata olursa ekrana yazdır
    die("Sefer eklenirken bir hata oluştu: " . $e->getMessage());
}
?>