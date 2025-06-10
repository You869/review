<?php

require_once "dbconnect.php"; // dbconnect.php で $dbh をセット

session_start();

// セッションが開始されているか、ユーザーがログインしているか確認
if (!isset($_SESSION['id'])) {
    header('Location: login.php');  // ログインしていない場合はログインページにリダイレクト
    exit();
}

// DB接続

// ユーザー情報の取得
$id = $_SESSION['id'];
$stmt = $dbh->prepare('SELECT * FROM login WHERE id = :id');
$stmt->execute([':id' => $id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "ユーザー情報が見つかりません。";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
    <link rel="stylesheet" href="css/style_p.css">
    <style>
            /* 画像を丸くして、ボーダーをつける */
    .profile-img {
        width: 150px;            /* 画像の幅 */
        height: 150px;           /* 画像の高さ */
        border-radius: 50%;      /* 丸くする */
        object-fit: cover;       /* 画像が縦横比を保持して、枠内に収まるようにトリミング */
        border: 2px solid #333;  /* 画像の周りに5pxの黒いボーダーを追加 */
    }

    </style>
</head>
<body>
    <h1>プロフィール編集</h1>
    <form action="profile_update.php" method="POST" enctype="multipart/form-data">
        <label>名前：</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

        <label>現在のプロフィール画像：</label>
        <?php if (!empty($result['image'])): ?>
            <br><img class="profile-img" src="img/<?php echo htmlspecialchars($result['image'], ENT_QUOTES, 'UTF-8'); ?>" width="100"><br><br>
        <?php endif; ?>
        <input type="file" name="image"><br><br>

        <input type="submit" value="更新する">
    </form>
</body>
</html>
