<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/fpdf/fpdf.php';

// Türkçe karakterleri FPDF için dönüştürecek özel bir fonksiyon
function tr_converter($text) {
    $search = array('ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü');
    $replace = array('c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U');
    return str_replace($search, $replace, $text);
}

// Güvenlik kontrolleri
if (!isset($_SESSION['user_id'])) {
    die("Bu sayfayı görüntülemek için giriş yapmalısınız.");
}
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die("Geçersiz bilet ID'si.");
}

$ticket_id = $_GET['ticket_id'];
$user_id = $_SESSION['user_id'];

try {
    // --- DÜZELTME BURADA ---
    // SQL sorgusundaki hatalı JOIN koşulunu düzeltiyoruz.
    $sql = "SELECT 
                Tickets.*,
                Users.fullname AS user_fullname,
                Buses.*,
                Companies.name AS company_name
            FROM Tickets
            JOIN Users ON Tickets.user_id = Users.id
            JOIN Buses ON Tickets.bus_id = Buses.id
            JOIN Companies ON Buses.company_id = Companies.id 
            WHERE Tickets.id = ? AND Tickets.user_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_id, $user_id]);
    $bilet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bilet) {
        die("Bilet bulunamadı veya bu bileti görüntüleme yetkiniz yok.");
    }

    // PDF Oluşturma kısmı (değişiklik yok)
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, tr_converter('YOLCU BİLETİ'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Firma:'));
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, tr_converter($bilet['company_name']), 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Yolcu Adı:'));
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, tr_converter($bilet['user_fullname']), 0, 1);
    $pdf->Ln(5);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Güzergah:'));
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, tr_converter($bilet['departure_city'] . ' -> ' . $bilet['arrival_city']), 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Kalkış Zamanı:'));
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, date('d F Y, H:i', strtotime($bilet['departure_time'])), 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Koltuk No:'));
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $bilet['seat_number'], 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, tr_converter('Ödenen Tutar:'));
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $bilet['purchase_price'] . ' TL', 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, tr_converter('İyi yolculuklar dileriz!'), 0, 1, 'C');
    $pdf->Output('D', 'bilet_' . $ticket_id . '.pdf');

} catch (Exception $e) {
    die("PDF oluşturulurken bir hata oluştu: " . $e->getMessage());
}
?>