<?php
session_start();
require '/config/db.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $begin = $_POST['begin'];
    $end = $_POST['end'];
    $place = $_POST['place'];
    $content = $_POST['content'];

    // データベースに新しいスケジュールを挿入
    $stmt = $pdo->prepare("INSERT INTO schedules (begin, end, place, content) VALUES (:begin, :end, :place, :content)");
    $stmt->execute([
        'begin' => $begin,
        'end' => $end,
        'place' => $place,
        'content' => $content
    ]);

    // 作成後、スケジュール一覧ページにリダイレクト
    header('Location: /schedule/index.php?message=created');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Create Schedule</title>
</head>
<body>
<div class="container mt-5">
    <h2>Create New Schedule</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="begin" class="form-label">Begin</label>
            <input type="datetime-local" name="begin" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end" class="form-label">End</label>
            <input type="datetime-local" name="end" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="place" class="form-label">Place</label>
            <input type="text" name="place" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="/schedule/index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
