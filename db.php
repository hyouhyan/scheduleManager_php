<?php
$host = 'localhost';
$dbname = 'schedule';
$user = 'root'; // 変更してください
$pass = 'root'; // 変更してください

$login_user = "testid";
$login_pass = "testpass";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
