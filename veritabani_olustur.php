<?php
// Hata raporlamayı açalım ki ne olduğunu görelim
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Veritabanı dosyasının yolu (bu dosyanın yanındaki 'db' klasörünün içinde olacak)
    $db_path = __DIR__ . '/db/bilet.db';

    // 'db' klasörü yoksa, onu oluşturalım
    if (!is_dir(__DIR__ . '/db')) {
        mkdir(__DIR__ . '/db');
    }

    // PDO ile SQLite veritabanına bağlanalım
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Veritabanı dosyasına başarıyla bağlanıldı.<br>";

    // Görev dökümanına (PDF) uygun tabloları oluşturacak SQL komutları
    $commands = [
        // Users Tablosu
        'CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT "User",
            company_id INTEGER NULL,
            balance REAL NOT NULL DEFAULT 0.0
        )',
        // Companies Tablosu
        'CREATE TABLE IF NOT EXISTS Companies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )',
        // Buses Tablosu
        'CREATE TABLE IF NOT EXISTS Buses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER NOT NULL,
            departure_city TEXT NOT NULL,
            arrival_city TEXT NOT NULL,
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            seat_count INTEGER NOT NULL,
            price REAL NOT NULL,
            FOREIGN KEY (company_id) REFERENCES Companies (id)
        )',
        // Tickets Tablosu
        'CREATE TABLE IF NOT EXISTS Tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            bus_id INTEGER NOT NULL,
            seat_number INTEGER NOT NULL,
            purchase_price REAL NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users (id),
            FOREIGN KEY (bus_id) REFERENCES Buses (id)
        )',
        // Coupons Tablosu
        'CREATE TABLE IF NOT EXISTS Coupons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            discount_rate REAL NOT NULL,
            usage_limit INTEGER NOT NULL,
            expiration_date DATE NOT NULL,
            company_id INTEGER NULL
        )'
    ];

    // Komutları çalıştır
    foreach ($commands as $command) {
        $pdo->exec($command);
    }

    echo "<b>BAŞARILI!</b> Görev dökümanında istenen tüm tablolar oluşturuldu.";

} catch (PDOException $e) {
    echo "<b>HATA!</b> Bir sorun oluştu: " . $e->getMessage();
}
?>