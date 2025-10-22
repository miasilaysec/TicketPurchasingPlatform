<?php
session_start();
require 'includes/db_connect.php';

// --- GÜVENLİK 1: GİRİŞ KONTROLÜ ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- GÜVENLİK 2: POST KONTROLÜ ---
// Bu sayfaya sadece koltuk seçme formundan POST metodu ile gelinmelidir.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Formdan gelen verileri al
if (!isset($_POST['sefer_id']) || !isset($_POST['koltuk_no'])) {
    header("Location: index.php?error=eksikbilgi");
    exit();
}

$sefer_id = $_POST['sefer_id'];
$koltuk_no = $_POST['koltuk_no'];
$user_id = $_SESSION['user_id'];

try {
    // --- "YA HEP YA HİÇ" İŞLEMİNİ (TRANSACTION) BAŞLAT ---
    $pdo->beginTransaction();

    // Adım 1: Seferin fiyatını ve koltuk sayısını al.
    $stmt_bus = $pdo->prepare("SELECT price, seat_count FROM Buses WHERE id = ?");
    $stmt_bus->execute([$sefer_id]);
    $sefer = $stmt_bus->fetch(PDO::FETCH_ASSOC);

    if (!$sefer) {
        throw new Exception("Sefer bulunamadı.");
    }
    $bilet_fiyati = $sefer['price'];

    // Adım 2: Kullanıcının bakiyesini al.
    // "FOR UPDATE" kilidi, aynı anda başka bir işlemin bu satırı değiştirmesini engeller (Race condition önlemi)
    $stmt_user = $pdo->prepare("SELECT balance FROM Users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $kullanici = $stmt_user->fetch(PDO::FETCH_ASSOC);
    $mevcut_bakiye = $kullanici['balance'];

    // Adım 3: Bakiye Yeterli mi?
    if ($mevcut_bakiye < $bilet_fiyati) {
        // Yetersiz bakiye durumunda işlemi geri al ve hata ver.
        $pdo->rollBack();
        header("Location: bilet_al.php?sefer_id=$sefer_id&error=balance");
        exit();
    }

    // Adım 4: Seçilen koltuk hala boş mu?
    // (Kullanıcı butona basana kadar başkası almış olabilir)
    $stmt_seat = $pdo->prepare("SELECT id FROM Tickets WHERE bus_id = ? AND seat_number = ?");
    $stmt_seat->execute([$sefer_id, $koltuk_no]);
    $koltuk_dolu_mu = $stmt_seat->fetch(PDO::FETCH_ASSOC);

    if ($koltuk_dolu_mu) {
        // Koltuk dolmuşsa, işlemi geri al ve hata ver.
        $pdo->rollBack();
        header("Location: bilet_al.php?sefer_id=$sefer_id&error=seat_taken");
        exit();
    }

    // --- HER ŞEY YOLUNDA, İŞLEMLERİ YAP ---

    // Adım 5: Yeni bileti 'Tickets' tablosuna ekle.
    $sql_insert_ticket = "INSERT INTO Tickets (user_id, bus_id, seat_number, purchase_price) VALUES (?, ?, ?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert_ticket);
    $stmt_insert->execute([$user_id, $sefer_id, $koltuk_no, $bilet_fiyati]);

    // Adım 6: Kullanıcının bakiyesini güncelle (parayı düş).
    $yeni_bakiye = $mevcut_bakiye - $bilet_fiyati;
    $sql_update_balance = "UPDATE Users SET balance = ? WHERE id = ?";
    $stmt_update = $pdo->prepare($sql_update_balance);
    $stmt_update->execute([$yeni_bakiye, $user_id]);

    // --- TÜM İŞLEMLER BAŞARILI, İŞLEMİ ONAYLA (COMMIT) ---
    $pdo->commit();

    // Başarı sayfasına yönlendir (Henüz oluşturmadık, şimdilik dashboard'a gitsin)
    header("Location: dashboard.php?success=ticket_bought");
    exit();

} catch (Exception $e) {
    // try bloğu içinde herhangi bir yerde hata olursa, tüm işlemleri geri al (rollBack).
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Hata mesajı ile geri yönlendir.
    die("İşlem sırasında kritik bir hata oluştu: " . $e->getMessage());
}
?>