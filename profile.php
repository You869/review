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

    // 生年月日を取得
    // birth_dateが存在しない場合に備えてissetでチェック
    $birth_date = isset($result['birth_date']) ? $result['birth_date'] : null;

    // 初期化
    $formatted_birth_date = '生年月日が設定されていません'; // デフォルトメッセージを設定

    // 生年月日が存在する場合のみフォーマット
    if ($birth_date) {
        // 'YYYY-MM-DD' 形式の日付を表示形式に変換
        $formatted_birth_date = date("Y年m月d日", strtotime($birth_date));
    }

} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
    <link rel="stylesheet" href="css/style_p.css">
    <style>
        /* 画像を丸くするCSS */

    </style>
</head>
<body>
    <main>
        <div class="myprofile">
            <?php if (isset($_SESSION['image']) && isset($_SESSION['name'])): ?>
            <div class="icon">
                <div class="birth-date">
                    <!-- プロフィール画像 -->
                    <img class="profile-img" src="img/<?php echo htmlspecialchars($_SESSION['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="プロフィール画像">
                    <!-- 生年月日 -->
                    <div class="birth">
                        <p>生年月日</p>
                        <p><?php echo htmlspecialchars($formatted_birth_date, ENT_QUOTES, 'UTF-8'); ?></p> <!-- 生年月日を表示 -->
                    </div>
                </div>
            </div>
            <!-- ユーザー名 -->
            <h1><?php echo htmlspecialchars($_SESSION["name"], ENT_QUOTES, 'UTF-8'); ?> さん</h1>
            <!-- 編集リンク -->
            <div class="button">
                <p><a href="profile_update.php"><span>名前とアイコンの編集</span></a></p>
            </div>
            <?php else: ?>
                <p>プロフィール情報が設定されていません。</p>
            <?php endif; ?>
            <div class="button">
                <p><a href="myreview.php"><span>自分のレビューを見る</span></a></p>
            </div>
            <div class="menu_btn">
                <a href="index.html">メニューに戻る</a>
            </div>
        </div>
    </main>
</body>
</html>
