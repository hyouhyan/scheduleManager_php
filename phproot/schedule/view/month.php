<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/config/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/config/japaneseHoliday.php';

// ユーザーがログインしていない場合、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// 現在の年月を取得
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// 月のオーバーフローを調整
if ($month < 1) {
    $year--;
    $month = 12;
    header("Location: ?year=$year&month=$month");
} elseif ($month > 12) {
    $year++;
    $month = 1;
    header("Location: ?year=$year&month=$month");
}

// 月の初日と最終日を取得
$first_day = "$year-$month-01";
$last_day = date('Y-m-t', strtotime($first_day));

// スケジュールを取得
$stmt = $pdo->prepare("
    SELECT * FROM schedules 
    WHERE 
        (begin BETWEEN :first_day AND :last_day) OR 
        (end BETWEEN :first_day AND :last_day) OR 
        (begin <= :first_day AND end >= :last_day)
    ORDER BY begin ASC
");
$stmt->execute(['first_day' => $first_day, 'last_day' => $last_day]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// デバッグ用にスケジュールを表示
// echo '<pre>';
// print_r($schedules);
// echo '</pre>';

// カレンダーのヘッダーを作成
$days_of_week = ['日', '月', '火', '水', '木', '金', '土'];
$first_day_of_week = date('w', strtotime($first_day));
$total_days = date('t', strtotime($first_day));

$holidays = getJapaneseHolidays($year);
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
        .calendar th {
            text-align: center;
            background-color: #f8f9fa;
        }
        .calendar td {
            text-align: center;
            vertical-align: top;
            height: 100px;
        }
        .schedule-item {
            font-size: 0.85em;
            margin-bottom: 5px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            padding: 4px;
            border-radius: 4px;
        }
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="?year=<?= $year ?>&month=<?= $month - 1 ?>" class="btn btn-outline-secondary">&lt; 先月</a>
        <h2>月間スケジュール 
            <?php
            $formatted_month = str_pad($month, 2, '0', STR_PAD_LEFT);
            echo $year."年".$formatted_month."月";
            ?>
        </h2>
        <a href="?year=<?= $year ?>&month=<?= $month + 1 ?>" class="btn btn-outline-secondary">翌月 &gt;</a>
    </div>
    <table class="table table-bordered calendar">
        <thead>
        <tr>
            <?php foreach ($days_of_week as $index => $day): ?>
                <?php 
                    $class = '';
                    if ($index == 0) {
                        $class = 'sunday'; // 日曜日
                    } elseif ($index == 6) {
                        $class = 'saturday'; // 土曜日
                    }
                ?>
                <th class="<?= $class ?>"><?= $day ?></th>
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
                $formatted_month = str_pad($month, 2, '0', STR_PAD_LEFT);
                $formatted_day = str_pad($day, 2, '0', STR_PAD_LEFT);

                $current_date = "$year-$formatted_month-$formatted_day";
                $day_of_week = date('w', strtotime($current_date));

                // 曜日に応じたクラスを適用
                $class = '';
                if ($day_of_week == 0) {
                    $class = 'sunday';
                } elseif ($day_of_week == 6) {
                    $class = 'saturday';
                }

                // 祝日の場合はクラスを追加
                if (isset($holidays[$current_date])){
                    $class = 'sunday';
                }

                // 今日が月曜日で、かつ前日が祝日の場合、振替休日とする
                if ($day_of_week == 1 && isset($holidays[date('Y-m-d', strtotime($current_date . ' -1 day'))])) {
                    $class = 'sunday';
                }

                echo "<td>";
                echo "<strong class='$class'>$day</strong>";

                // 祝日の場合名称を表示
                if (isset($holidays[$current_date])){
                    echo "<br><small class='$class'>".$holidays[$current_date]."</small>";
                }

                // 振替休日の場合
                if ($day_of_week == 1 && isset($holidays[date('Y-m-d', strtotime($current_date . ' -1 day'))])) {
                    echo "<br><small class='$class'>振替休日</small>";
                }

                // スケジュールを表示
                foreach ($schedules as $schedule) {
                    // begin <= current_date <= end の場合
                    // 時間を考慮せず日付のみで判定
                    $begin_date = date('Y-m-d', strtotime($schedule['begin']));
                    $end_date = date('Y-m-d', strtotime($schedule['end']));
                    if ($begin_date <= $current_date && $current_date <= $end_date) {
                        $title = htmlspecialchars($schedule['content']);
                        $begin_time = date('H:i', strtotime($schedule['begin']));
                        $end_time = date('H:i', strtotime($schedule['end']));
                        // beginの日付とendの日付が異なる時
                        if (strpos($begin_date, $end_date) !== 0) {
                            // 今日が何日目かを計算
                            $days_diff = (strtotime($current_date) - strtotime($schedule['begin'])) / (60 * 60 * 24);
                            $title .= ' (Day' . $days_diff + 1 . ')';
                            
                            // 最終日以外は日を跨ぐので、終了日を24:00に設定
                            if ($current_date != $end_date) {
                                $end_time = '24:00';
                            }
                        }
                        
                        echo '<div class="schedule-item">';
                        echo $title;
                        echo '<br><small>@ ' . htmlspecialchars($schedule['place']) . '</small>';
                        echo '<br><small>' . $begin_time . ' - ' . $end_time . '</small>';
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
    <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="?year=<?= $year ?>&month=<?= $month - 1 ?>" class="btn btn-outline-secondary">&lt; 先月</a>
        <a href="?year=<?= $year ?>&month=<?= $month + 1 ?>" class="btn btn-outline-secondary">翌月 &gt;</a>
    </div>
    <a href="/schedule/index.php" class="btn btn-secondary mt-3">一覧表示に戻る</a>
</div>
</body>
</html>
