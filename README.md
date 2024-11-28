# スケジュール管理アプリ
[2024年度 後期 Webプログラミング及び演習](https://github.com/hyouhyan/edu_2024-WebProgramming) の第6回授業で作成  
PHPとMySQLを使用したスケジュール管理アプリ

## 仕様

### 技術スタック
- サーバーサイド：PHP
- データベース：MySQL
- フロントエンド：HTML, CSS, Bootstrap

### 要求仕様
1. 登録ユーザでログイン後、スケジュールの表示、登録、変更、削除が出来る。
1. 表示は、リスト形式とする。
1. スケジュールはデータベースにテーブルを作成して登録する。
1. スケジュールは開始日時、終了日時、場所、内容を必ず含むものとする。
1. 週間のスケジュールを表示できるようにする。
1. 月間のスケジュールを表示できるようにする。


# 実行
`phproot`をルートディレクトリとしてMAMPに設定

## MySQL db構築

1. mysqlに入る  
    $ `/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p`  
    Enter Password: `root`
2. db作成  
    mysql > `source ./init/schedule_db.sql`  
4. テストレコードの適用  
    (テストレコードを利用しない場合、この手順は飛ばす)  
    mysql > `source ./init/add_test_record.sql`
3. dbを抜ける  
    mysql > `exit`

# 構成
## ファイル構成
```
phproot/
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   ├── db.php
│   └── japaneseHoliday.php
├── schedule/
│   ├── manage/
│   │   ├── create.php
│   │   ├── delete.php
│   │   └── edit.php
│   ├── view/
│   │   ├── month.php
│   │   └── week.php
│   └── index.php
└── index.html
```

## データベース構成
- データベース名: `schedule`
- 文字セット: `utf8mb4`
- 照合順序: `utf8mb4_general_ci`
- 主キー: `id`

### テーブル構成

`schedules` テーブル

| カラム名 | データ型         | 必須 | 説明                     |
|----------|------------------|------|--------------------------|
| `id`     | `int(11)`        | YES  | 自動増分の主キー         |
| `begin`  | `datetime`       | YES  | スケジュールの開始日時   |
| `end`    | `datetime`       | YES  | スケジュールの終了日時   |
| `place`  | `varchar(256)`   | YES  | スケジュールの場所       |
| `content`| `text`           | YES  | スケジュールの内容       |


## 画面推移図
※とても見づらい

![Image from Gyazo](https://i.gyazo.com/631d0e3126115265802e813a8f41334e.png)

