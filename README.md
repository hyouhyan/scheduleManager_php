# スケジュール管理アプリ
2024年度 後期 Webプログラミング及び演習 の第6回授業で作成  
PHPとMySQLを使用したスケジュール管理アプリ

## 仕様
- 使用技術: PHP, MySQL
- 実行環境
    - OS: MacOS 15.0.1(Sequoia)
    - MAMP 7.0
        - Apache(port: 80)
        - PHP 8.3.9
        - MySQL(port: 3306)

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
3. dbを抜ける
    mysql > `exit`

# ファイル構成
```
phproot/
├── auth/
│   ├── login.php
│   └── logout.php
├── config/
│   └── db.php
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
