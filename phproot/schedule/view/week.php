<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// 現在の週を取得
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$week = isset($_GET['week']) ? (int)$_GET['week'] : date('W');

// 週のオーバーフローを調整
if ($week < 1) {
    $year--;
    $week = 52;
    header("Location: ?year=$year&week=$week");
} elseif ($week > 52) {
    $year++;
    $week = 1;
    header("Location: ?year=$year&week=$week");
}

// 週の開始日と終了日を計算
$start_of_week = date('Y-m-d', strtotime("$year-W$week-1"));
$end_of_week = date('Y-m-d', strtotime("$year-W$week-7"));

// スケジュールを取得
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE DATE(begin) BETWEEN :start_of_week AND :end_of_week ORDER BY begin ASC");
$stmt->execute(['start_of_week' => $start_of_week, 'end_of_week' => $end_of_week]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 曜日配列
$days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Weekly Schedule</title>
</head>
<body>
<div class="container mt-5">
    <h2>Weekly Schedule for Week <?= $week ?>, <?= $year ?></h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="?year=<?= $year ?>&week=<?= $week - 1 ?>" class="btn btn-outline-secondary">&lt; Previous Week</a>
        <a href="?year=<?= $year ?>&week=<?= $week + 1 ?>" class="btn btn-outline-secondary">Next Week &gt;</a>
    </div>

    <div class="list-group">
        <?php foreach ($days_of_week as $i => $day): ?>
            <?php
            $current_date = date('Y-m-d', strtotime("$start_of_week +$i days"));
            $day_schedules = array_filter($schedules, fn($schedule) => strpos($schedule['begin'], $current_date) === 0);
            ?>
            <div class="list-group-item">
                <h5><?= $day ?> (<?= date('Y-m-d', strtotime($current_date)) ?>)</h5>
                <?php if (count($day_schedules) > 0): ?>
                    <ul class="list-unstyled">
                        <?php foreach ($day_schedules as $schedule): ?>
                            <li>
                                <strong><?= htmlspecialchars($schedule['content']) ?></strong>
                                <br>
                                <small><?= date('H:i', strtotime($schedule['begin'])) ?> - <?= date('H:i', strtotime($schedule['end'])) ?> @ <?= htmlspecialchars($schedule['place']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No schedules for this day.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="/schedule/index.php" class="btn btn-secondary mt-3">Back to Schedules</a>
</div>
</body>
</html>
