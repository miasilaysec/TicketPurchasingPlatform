<?php
session_start();
require '../includes/db_connect.php';

// Güvenlik kontrolleri
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Firma Admin') {
    header("Location: ../login.php");
    exit();
}

// Formdan gelen verileri al
$sefer_id = $_POST['sefer_id'];
$departure_city = $_POST['departure_city'];
$arrival_city = $_POST['arrival_city'];
$departure_time = $_POST['departure_time'];
$arrival_time = $_POST['arrival_time'];
$seat_count = $_POST['seat_count'];
$price = $_POST['price'];
$company_id = $_SESSION['company_id'];

try {
    // Veritabanındaki seferi güncelle
    $sql = "UPDATE Buses SET 
                departure_city = ?, 
                arrival_city = ?, 
                departure_time = ?, 
                arrival_time = ?, 
                seat_count = ?, 
                price = ? 
            WHERE id = ? AND company_id = ?"; // Güvenlik: Sadece kendi firmasının seferini güncelleyebilsin.
    
    $stmt = $pdo->prepare($sql);
    
    $formatted_departure_time = date('Y-m-d H:i:s', strtotime($departure_time));
    $formatted_arrival_time = date('Y-m-d H:i:s', strtotime($arrival_time));
    
    $stmt->execute([
        $departure_city,
        $arrival_city,
        $formatted_departure_time,
        $formatted_arrival_time,
        $seat_count,
        $price,
        $sefer_id,
        $company_id
    ]);

    // Başarılı olursa ana panele geri yönlendir
    header("Location: index.php?success=sefer_guncellendi");
    exit();

} catch (PDOException $e) {
    // Hata olursa ekrana yazdır
    die("Sefer güncellenirken bir hata oluştu: " . $e->getMessage());
}
?>