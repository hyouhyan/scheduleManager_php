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

## MySQL db構築

1. mysqlに入る
    $ `/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -p`
    Enter Password: `root`
2. db作成
    mysql > `source ./init/schedule_db.sql`
3. dbを抜ける
    mysql > `exit`