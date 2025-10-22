<?php
session_start();
require 'includes/db_connect.php';

// Güvenlik 1: Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Güvenlik 2: Sadece POST metodu ile gelinmeli
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['ticket_id'])) {
    header("Location: biletlerim.php");
    exit();
}

$ticket_id = $_POST['ticket_id'];
$user_id = $_SESSION['user_id'];

try {
    // "Ya hep ya hiç" işlemi için transaction başlat
    $pdo->beginTransaction();

    // Adım 1: Biletin bilgilerini ve ait olduğu seferin kalkış saatini çek.
    // Güvenlik için biletin bu kullanıcıya ait olup olmadığını da kontrol et (user_id = ?).
    $sql = "SELECT Tickets.purchase_price, Buses.departure_time 
            FROM Tickets
            JOIN Buses ON Tickets.bus_id = Buses.id
            WHERE Tickets.id = ? AND Tickets.user_id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_id, $user_id]);
    $bilet = $stmt->fetch(PDO::FETCH_ASSOC);

    // Bilet bulunamazsa (veya başkasına aitse), işlemi durdur.
    if (!$bilet) {
        throw new Exception("Geçersiz bilet veya yetkisiz erişim.");
    }

    // Adım 2: Zaman kontrolü yap.
    $kalkis_zamani = new DateTime($bilet['departure_time']);
    $simdiki_zaman = new DateTime();
    $fark = $simdiki_zaman->diff($kalkis_zamani);

    // Kalkış zamanı geçmişse veya 1 saatten az kalmışsa iptal etme.
    // $fark->invert == 1 ise tarih geçmiş demektir.
    $saniye_farki = $kalkis_zamani->getTimestamp() - $simdiki_zaman->getTimestamp();

    if ($saniye_farki < 3600) { // 3600 saniye = 1 saat
        $pdo->rollBack(); // İşlemi geri al
        header("Location: biletlerim.php?error=time_limit_exceeded");
        exit();
    }

    // --- Zaman kontrolü başarılı, iptal işlemlerini yap ---

    // Adım 3: Bilet ücretini kullanıcının bakiyesine iade et.
    $iade_tutari = $bilet['purchase_price'];
    $sql_update_balance = "UPDATE Users SET balance = balance + ? WHERE id = ?";
    $stmt_update = $pdo->prepare($sql_update_balance);
    $stmt_update->execute([$iade_tutari, $user_id]);

    // Adım 4: Bileti 'Tickets' tablosundan sil.
    $sql_delete_ticket = "DELETE FROM Tickets WHERE id = ?";
    $stmt_delete = $pdo->prepare($sql_delete_ticket);
    $stmt_delete->execute([$ticket_id]);

    // Tüm işlemler başarılı, onayla.
    $pdo->commit();

    header("Location: biletlerim.php?success=cancellation_complete");
    exit();

} catch (Exception $e) {
    // Herhangi bir hata olursa tüm işlemleri geri al.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("İptal işlemi sırasında bir hata oluştu: " . $e->getMessage());
}
?>