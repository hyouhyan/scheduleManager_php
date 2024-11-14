<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/config/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/config/japaneseHoliday.php';

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

$start_of_week = date('Y-m-d', strtotime("{$year}-W" . str_pad($week, 2, '0', STR_PAD_LEFT) . "0"));
$end_of_week = date('Y-m-d', strtotime("{$year}-W" . str_pad($week, 2, '0', STR_PAD_LEFT) . "-6"));

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

$holidays = getJapaneseHolidays($year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Weekly Schedule</title>
    <style>
        .sunday {
            color: #ff2222 !important; /* 日曜日の背景を赤 */
        }
        .saturday {
            color: #2244ff !important; /* 土曜日の背景を青 */
        }
    </style>
</head>
<body>
<div class="container mt-3 mb-3">
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

            $day_of_week = date('w', strtotime($current_date));

            // 曜日に応じたクラスを適用
            $class = '';
            if ($day_of_week == 0) {
                $class = 'sunday';
            } elseif ($day_of_week == 6) {
                $class = 'saturday';
            }

            // 祝日の場合はクラスを追加
            if (isset($holidays[$current_date])) {
                $class = 'sunday';
            }

            // 今日が月曜日で、かつ前日が祝日の場合、振替休日とする
            if ($day_of_week == 1 && isset($holidays[date('Y-m-d', strtotime($current_date . ' -1 day'))])) {
                $class = 'sunday';
            }

            ?>
            <div class="list-group-item">
                <h5 class='<?php echo $class ?>'>
                    <?php
                        $day_of_week = date('w', strtotime($current_date));
                        // MM/DD(曜日) の形式で表示
                        echo date('m/d', strtotime($current_date)) . ' (' . $days_of_week[$day_of_week] . ')';

                        // 祝日の場合は表示
                        if (isset($holidays[$current_date])) {
                            echo ' ' . $holidays[$current_date];
                        }

                        // 振替休日の場合
                        if ($day_of_week == 1 && isset($holidays[date('Y-m-d', strtotime($current_date . ' -1 day'))])) {
                            echo ' ' . "振替休日";
                        }
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

                                $begin_time = date('H:i', strtotime($schedule['begin']));
                                $end_time = date('H:i', strtotime($schedule['end']));
                                // 終了日が翌日以降の時、Day2, Day3, ... と表示
                                // beginの日付とendの日付が異なる時
                                if (strpos($begin_date, $end_date) !== 0) {
                                    // 今日が何日目かを計算
                                    $days_diff = (strtotime($current_date) - strtotime($schedule['begin'])) / (60 * 60 * 24);
                                    $title .= ' (Day' . $days_diff + 1 . ')';
                                    
                                    if ($current_date != $end_date) {
                                        $end_time = '24:00';
                                    }
                                }
                                ?>
                                <strong><?= $title ?></strong>
                                <br>
                                <small><?= "{$begin_time} - {$end_time}" ?> @ <?= htmlspecialchars($schedule['place']) ?></small>
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
