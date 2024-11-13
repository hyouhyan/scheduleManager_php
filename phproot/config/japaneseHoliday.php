<?php
function getJapaneseHolidays($year) {
    return [
        "$year-01-01" => "元日",
        "$year-02-11" => "建国記念の日",
        "$year-02-23" => "天皇誕生日",
        "$year-03-" . calculateSpringEquinox($year) => "春分の日",
        "$year-04-29" => "昭和の日",
        "$year-05-03" => "憲法記念日",
        "$year-05-04" => "みどりの日",
        "$year-05-05" => "こどもの日",
        "$year-07-" . calculateMarineDay($year) => "海の日",
        "$year-08-11" => "山の日",
        "$year-09-" . calculateAutumnEquinox($year) => "秋分の日",
        "$year-10-" . calculateSportsDay($year) => "スポーツの日",
        "$year-11-03" => "文化の日",
        "$year-11-23" => "勤労感謝の日",
    ];
}

// 春分の日を計算
function calculateSpringEquinox($year) {
    if ($year < 1980) {
        return 21;
    } elseif ($year < 2100) {
        return floor(20.8431 + 0.242194 * ($year - 1980)) - floor(($year - 1980) / 4);
    } else {
        return 20;
    }
}

// 秋分の日を計算
function calculateAutumnEquinox($year) {
    if ($year < 1980) {
        return 23;
    } elseif ($year < 2100) {
        return floor(23.2488 + 0.242194 * ($year - 1980)) - floor(($year - 1980) / 4);
    } else {
        return 24;
    }
}

// 2024年以降の体育の日（スポーツの日）を計算
function calculateSportsDay($year) {
    return ($year >= 2020) ? 10 : 8;
}

// 2024年以降の海の日を計算（通常は7月の第3月曜日）
function calculateMarineDay($year) {
    if ($year >= 2024) {
        return date('d', strtotime("third monday of july $year"));
    }
    return 20;
}
