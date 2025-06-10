<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo "ログインしていません。ログインしてください。";
    exit();
}

try {
    // データベース接続
    $user = "root";
    $password = "";
    $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", $user, $password);

    // セッションからユーザーIDを取得
    $id = $_SESSION['id'];

    // ユーザー情報を取得
    $stmt = $dbh->prepare('SELECT * FROM login WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "ユーザーが見つかりません。";
        exit();
    }

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
    <link rel="stylesheet" href="css/style_p.css">
</head>
<body>
    <div id="pro">
    <!-- ユーザー名とアイコンの変更フォーム -->
        <h1>名前とアイコンの変更</h1>
        <form method="POST" action="update.php" enctype="multipart/form-data">
            <label>現在の名前: </label>
            <input type="text" name="current_name" value="<?php echo htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>" disabled><br><br>

            <label>新しい名前: </label>
            <input type="text" name="new_name" value="<?php echo htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

            <label>現在のアイコン: </label>
            <img class="profile-img" src="img/<?php echo $_SESSION['image']?>">

            <label>新しいアイコン: </label>
            <input type="file" name="profile_image"><br><br>


        <!-- 更新ボタン -->
            <input type="submit" value="情報を更新する">
            <div class="back">
                <p><a href="profile.php">プロフィールに戻る</a></p>
                <p><a href="index.html">メニューに戻る</a></p>
            </div>     
        </form>
    </div>
</body>
</html>
