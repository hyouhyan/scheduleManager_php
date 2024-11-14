<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM schedules ORDER BY begin ASC");
$schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Schedules</title>
</head>
<body>
<div class="container mt-3 mb-3">
    <div class="d-flex justify-content-end">
        <a href="/auth/logout.php" class="btn btn-danger">ログアウト</a>
    </div>

    <h2>スケジュール一覧</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
    <div class="alert alert-success">スケジュールの削除に成功しました。</div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
    <div class="alert alert-success">スケジュールの更新に成功しました。</div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'created'): ?>
    <div class="alert alert-success">スケジュールの作成に成功しました。</div>
    <?php endif; ?>

    <a href="/schedule/manage/create.php" class="btn btn-success mb-3">追加</a>

    <div class="mb-3">
        <a href="/schedule/view/month.php" class="btn btn-info mr-1">月間</a>
        <a href="/schedule/view/week.php" class="btn btn-info mr-1">週間</a>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>名称</th>
            <th>場所</th>
            <th>開始</th>
            <th>終了</th>
            <th>編集</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?= htmlspecialchars($schedule['content']) ?></td>
                <td><?= htmlspecialchars($schedule['place']) ?></td>
                <td>
                    <?php
                        $begin = new DateTime($schedule['begin']);
                        echo $begin->format('Y年m月d日 H:i');
                    ?>
                </td>
                <td>
                    <?php
                        $end = new DateTime($schedule['end']);
                        echo $end->format('Y年m月d日 H:i');
                    ?>
                <td>
                    <a href="/schedule/manage/edit.php?id=<?= $schedule['id'] ?>" class="btn btn-warning btn-sm mb-1">編集</a>
                    <a href="/schedule/manage/delete.php?id=<?= $schedule['id'] ?>" class="btn btn-danger btn-sm mb-1">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
