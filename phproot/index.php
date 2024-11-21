<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Home Page</title>
</head>
<body>
    <div class="container mt-5 text-center">
        <h1>Schedule Management App</h1>
        <p class="lead">スケジュール管理アプリへようこそ</p>
        <div class="alert alert-warning" role="alert">
            TA(採点者)の方へ<br>
            本アプリケーションはパス指定を<strong>絶対パス</strong>で行っています。<br>
            ドキュメントルートにご注意ください。
        </div>
        <?php
        if($_SERVER['DOCUMENT_ROOT'] == __DIR__) {
            echo '<div class="alert alert-success">ドキュメントルートが正しく設定されています。<br>
                <strong>ドキュメントルート: ' . $_SERVER['DOCUMENT_ROOT'] . '</strong><br>
                <strong>設定されているパス: ' . __DIR__ . '</strong></div>';
        } else {
            echo '<div class="alert alert-danger">
                ドキュメントルートが正しく設定されていません。<br>
                <strong>ドキュメントルート: ' . $_SERVER['DOCUMENT_ROOT'] . '</strong><br>
                <strong>設定されているパス: ' . __DIR__ . '</strong>
            </div>';
        }
        ?>
        <div class="mt-4">
            <a href="/auth/login.php" class="btn btn-primary btn-lg">ログイン</a>
        </div>
    </div>
</body>
</html>
