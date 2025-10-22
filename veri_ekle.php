<?php
// Bu dosyayı sadece test verisi eklemek için kullanacağız.
// Bir kez çalıştırdıktan sonra silebilir veya bırakabilirsiniz.
require 'includes/db_connect.php';

try {
    echo "Veri ekleme işlemi başlatıldı...<br>";

    // Örnek Firmalar Ekle
    $pdo->exec("INSERT INTO Companies (name) VALUES ('Mardin Seyahat');");
    $pdo->exec("INSERT INTO Companies (name) VALUES ('Metro Turizm');");
    echo "İki firma eklendi: Mardin Seyahat, Metro Turizm.<br>";

    // Örnek Seferler (Buses) Ekle
    // Not: company_id'ler 1 ve 2 olacak çünkü ilk iki firma onlar.
    $seferler = [
        [1, 'Mardin', 'İstanbul', date('Y-m-d H:i:s', strtotime('+1 day 2 hours')), date('Y-m-d H:i:s', strtotime('+2 days')), 45, 1200.00],
        [1, 'Mardin', 'Ankara', date('Y-m-d H:i:s', strtotime('+1 day 4 hours')), date('Y-m-d H:i:s', strtotime('+1 day 20 hours')), 45, 950.50],
        [2, 'İstanbul', 'İzmir', date('Y-m-d H:i:s', strtotime('+2 days 8 hours')), date('Y-m-d H:i:s', strtotime('+2 days 16 hours')), 50, 800.00],
        [2, 'Ankara', 'Mardin', date('Y-m-d H:i:s', strtotime('+2 days 10 hours')), date('Y-m-d H:i:s', strtotime('+3 days 2 hours')), 45, 1000.00]
    ];

    $sql = "INSERT INTO Buses (company_id, departure_city, arrival_city, departure_time, arrival_time, seat_count, price) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    foreach ($seferler as $sefer) {
        $stmt->execute($sefer);
    }
    echo "4 adet örnek sefer başarıyla eklendi.<br>";
    echo "<b>İşlem tamam!</b>";

} catch (PDOException $e) {
    die("Veri eklenirken hata oluştu: " . $e->getMessage());
}
?>