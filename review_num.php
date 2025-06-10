<?php
    // ファイルの読み込み
    require_once "dbconnect.php";
    require_once "functions3.php";

    // セッションの開始
    session_start();
    

    // 現在ログインしているユーザーのIDを取得
    $userid = $_SESSION['id'];
    $proimg = $_SESSION['image'];

    try {
        $user = "root";
        $password = "";
        $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", "$user", "$password");       
        $stmt = $dbh->query('SELECT * FROM review');
        $result = 0;
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
            echo 'エラーが発生しました。:' . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <div class="birth-date">
            <!-- プロフィール画像 -->
            <img class="profile-img" src="img/<?php echo htmlspecialchars($_SESSION['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="プロフィール画像">
        </div>
        <!-- ユーザー名 -->
        <h1><?php echo htmlspecialchars($_SESSION["name"], ENT_QUOTES, 'UTF-8'); ?> さん</h1>

    <a href="index.html">メニューに戻る</a>
</body>
</html>