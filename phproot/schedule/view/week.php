<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$week = isset($_GET['week']) ? (int)$_GET['week'] : date('W');

if ($week < 1) {
    $year--;
    $week = 52;
    header("Location: ?year=$year&week=$week");
    exit;
} elseif ($week > 52) {
    $year++;
    $week = 1;
    header("Location: ?year=$year&week=$week");
    exit;
}

$start_of_week = date('Y-m-d', strtotime("{$year}-W" . str_pad($week, 2, '0', STR_PAD_LEFT) . "-1"));
$end_of_week = date('Y-m-d', strtotime("{$year}-W" . str_pad($week, 2, '0', STR_PAD_LEFT) . "-7"));

$stmt = $pdo->prepare("
    SELECT * FROM schedules 
    WHERE 
        (begin BETWEEN :start_of_week AND :end_of_week) OR 
        (end BETWEEN :start_of_week AND :end_of_week) OR 
        (begin <= :start_of_week AND end >= :end_of_week)
    ORDER BY begin ASC
");

$stmt->execute(['start_of_week' => $start_of_week, 'end_of_week' => $end_of_week]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$days_of_week = ['日', '月', '火', '水', '木', '金', '土'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Weekly Schedule</title>
</head>
<body>
<div class="container mt-5">
    <h2>
        <?php
            $start_of_week_date = date_create($start_of_week);
            $end_of_week_date = date_create($end_of_week);

            echo "{$start_of_week_date->format('Y年')} 第{$week}週 ({$start_of_week_date->format('m/d')} - {$end_of_week_date->format('m/d')})";
        ?>
    </h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="?year=<?= $year ?>&week=<?= $week - 1 ?>" class="btn btn-outline-secondary">&lt; 先週</a>
        <a href="?year=<?= $year ?>&week=<?= $week + 1 ?>" class="btn btn-outline-secondary">翌週 &gt;</a>
    </div>

    <div class="list-group">
        <?php foreach ($days_of_week as $i => $day): ?>
            <?php
            $current_date = date('Y-m-d', strtotime("$start_of_week +$i days"));
            $day_schedules = array_filter($schedules, function ($schedule) use ($current_date) {
                return 
                    ($schedule['begin'] <= $current_date && $schedule['end'] >= $current_date) ||
                    (strpos($schedule['begin'], $current_date) === 0) ||
                    (strpos($schedule['end'], $current_date) === 0);
            });
            ?>
            <div class="list-group-item">
                <h5>
                    <?php
                        $day_of_week = date('w', strtotime($current_date));
                        // MM/DD(曜日) の形式で表示
                        echo date('m/d', strtotime($current_date)) . ' (' . $days_of_week[$day_of_week] . ')';
                    ?>
                </h5>
                <?php if (count($day_schedules) > 0): ?>
                    <ul class="list-unstyled">
                        <?php foreach ($day_schedules as $schedule): ?>
                            <li>
                                <?php 
                                $title = htmlspecialchars($schedule['content']);
                                $begin_date = date('Y-m-d', strtotime($schedule['begin']));
                                $end_date = date('Y-m-d', strtotime($schedule['end']));
                                // 終了日が翌日以降の時、Day2, Day3, ... と表示
                                // beginの日付とendの日付が異なる時
                                if (strpos($begin_date, $end_date) !== 0) {
                                    // 今日が何日目かを計算
                                    $days_diff = (strtotime($current_date) - strtotime($schedule['begin'])) / (60 * 60 * 24);
                                    $title .= ' (Day' . $days_diff + 1 . ')';
                                    
                                }
                                ?>
                                <strong><?= $title ?></strong>
                                <br>
                                <small><?= date('H:i', strtotime($schedule['begin'])) ?> - <?= date('H:i', strtotime($schedule['end'])) ?> @ <?= htmlspecialchars($schedule['place']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>予定なし</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="/schedule/index.php" class="btn btn-secondary mt-3">一覧表示に戻る</a>
</div>
</body>
</html>
