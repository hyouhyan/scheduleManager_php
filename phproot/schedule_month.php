<?php
session_start();
require '/config/db.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// 現在の年月を取得
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// 月の初日と最終日を取得
$first_day = "$year-$month-01";
$last_day = date('Y-m-t', strtotime($first_day));

// スケジュールを取得
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE begin BETWEEN :first_day AND :last_day ORDER BY begin ASC");
$stmt->execute(['first_day' => $first_day, 'last_day' => $last_day]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// カレンダーのヘッダーを作成
$days_of_week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$first_day_of_week = date('w', strtotime($first_day));
$total_days = date('t', strtotime($first_day));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Monthly Schedule</title>
    <style>
        .calendar {
            table-layout: fixed;
        }
        .calendar th, .calendar td {
            text-align: center;
            vertical-align: top;
            height: 100px;
        }
        .schedule-item {
            font-size: 0.85em;
            margin-bottom: 5px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 2px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Monthly Schedule for <?= date('F Y', strtotime($first_day)) ?></h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="schedule_month.php?year=<?= $year ?>&month=<?= $month - 1 ?>" class="btn btn-outline-secondary">&lt; Previous</a>
        <a href="schedule_month.php?year=<?= $year ?>&month=<?= $month + 1 ?>" class="btn btn-outline-secondary">Next &gt;</a>
    </div>
    <table class="table table-bordered calendar">
        <thead>
        <tr>
            <?php foreach ($days_of_week as $day): ?>
                <th><?= $day ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php
            // 空白セルを挿入
            for ($i = 0; $i < $first_day_of_week; $i++) {
                echo '<td></td>';
            }

            // カレンダーの日付を挿入
            for ($day = 1; $day <= $total_days; $day++) {
                $current_date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                echo '<td>';
                echo "<strong>$day</strong>";

                // スケジュールを表示
                foreach ($schedules as $schedule) {
                    if (strpos($schedule['begin'], $current_date) === 0) {
                        echo '<div class="schedule-item">';
                        echo htmlspecialchars($schedule['content']);
                        echo '<br><small>' . date('H:i', strtotime($schedule['begin'])) . ' - ' . date('H:i', strtotime($schedule['end'])) . '</small>';
                        echo '</div>';
                    }
                }

                echo '</td>';

                // 週が終わるごとに新しい行を開始
                if (($day + $first_day_of_week) % 7 == 0) {
                    echo '</tr><tr>';
                }
            }

            // 最後の行を空白セルで埋める
            $remaining_days = (7 - ($total_days + $first_day_of_week) % 7) % 7;
            for ($i = 0; $i < $remaining_days; $i++) {
                echo '<td></td>';
            }
            ?>
        </tr>
        </tbody>
    </table>
    <a href="schedule.php" class="btn btn-secondary">Back to Schedules</a>
</div>
</body>
</html>
