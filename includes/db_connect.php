<?php
try {
    // Projenin ana dizininden veritabanı dosyasına ulaşıyoruz.
    // __DIR__ bu dosyanın bulunduğu klasörü ('includes') verir.
    // '/../' ise bir üst klasöre ('bilet-satin-alma') çıkmamızı sağlar.
    $pdo = new PDO('sqlite:' . __DIR__ . '/../db/bilet.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Veritabanına bağlanılamazsa, programı durdur ve hata mesajı göster.
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}
?>