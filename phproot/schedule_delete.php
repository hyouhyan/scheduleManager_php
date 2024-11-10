<?php
session_start();
require 'db.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// IDがGETリクエストに存在しない場合、スケジュールページにリダイレクト
if (!isset($_GET['id'])) {
    header('Location: schedule.php');
    exit;
}

// スケジュールIDを取得
$schedule_id = $_GET['id'];

// スケジュールを削除するためのクエリを準備
$stmt = $pdo->prepare("DELETE FROM schedules WHERE id = :id");
$stmt->bindParam(':id', $schedule_id, PDO::PARAM_INT);

// 削除を実行
if ($stmt->execute()) {
    // 削除が成功した場合、スケジュールページにリダイレクト
    header('Location: schedule.php?message=deleted');
    exit;
} else {
    // エラーが発生した場合、エラーメッセージを表示
    echo "Failed to delete schedule.";
}
?>
