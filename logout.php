<?php
// Oturumu başlat
session_start();

// Tüm session değişkenlerini temizle
$_SESSION = array();

// Session'ı yok et
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendir
header("Location: login.php");
exit();
?>