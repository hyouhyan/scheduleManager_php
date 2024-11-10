<?php
session_start();
require '/config/db.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// GETリクエストにスケジュールIDがない場合、スケジュール一覧ページにリダイレクト
if (!isset($_GET['id'])) {
    header('Location: schedule.php');
    exit;
}

$schedule_id = $_GET['id'];

// スケジュールの詳細を取得
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = :id");
$stmt->execute(['id' => $schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    echo "Schedule not found.";
    exit;
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $begin = $_POST['begin'];
    $end = $_POST['end'];
    $place = $_POST['place'];
    $content = $_POST['content'];

    // 更新クエリの実行
    $update_stmt = $pdo->prepare("UPDATE schedules SET begin = :begin, end = :end, place = :place, content = :content WHERE id = :id");
    $update_stmt->execute([
        'begin' => $begin,
        'end' => $end,
        'place' => $place,
        'content' => $content,
        'id' => $schedule_id
    ]);

    // 更新後、スケジュール一覧ページにリダイレクト
    header('Location: schedule.php?message=updated');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Schedule</title>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Schedule</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="begin" class="form-label">Begin</label>
            <input type="datetime-local" name="begin" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['begin']))) ?>" required>
        </div>
        <div class="mb-3">
            <label for="end" class="form-label">End</label>
            <input type="datetime-local" name="end" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($schedule['end']))) ?>" required>
        </div>
        <div class="mb-3">
            <label for="place" class="form-label">Place</label>
            <input type="text" name="place" class="form-control" value="<?= htmlspecialchars($schedule['place']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="3" required><?= htmlspecialchars($schedule['content']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="schedule.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
