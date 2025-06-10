<?php
try {
    $user = "root";
    $password = "";
    $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", "$user", "$password");
    
    // タグを全て取得
    $stmt = $dbh->query('SELECT * FROM tags ORDER BY name ASC');
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タグ一覧</title>
    <link rel="stylesheet" href="css/reviewlist.css">
</head>
<body>
<header>
        <div class="logo">
            <img src="img/icon150×150.png">
            <h1>Restaurant evalution</h1>
        </div>
        <div class="item">
            <p><a href="tags_list.php">タグで検索</a></p>
        </div>
        <div class="item">
            <p><a href="profile.php">profile</a></p>
        </div>
        <div class="item2">
            <p><a href="login.php">login</a></p>
        </div>
    </header>
    <main>
        <div class="tag_form">
            <div class="menu-list2">
                <div class="list">
                    <h2>タグ一覧</h2>
                    <div class="tags">
                    <ul>
                        <?php foreach ($tags as $tag): ?>
                            <li>
                                <a class="tag" href="review_by_tag.php?tag=<?php echo urlencode($tag['name']); ?>"><?php echo htmlspecialchars($tag['name']); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    </div>
                </div>
                <div class="button">
                    <p><a href="index.html">メニューに戻る</a></p>
                </div>
            </div>
        </div>
    </main>
    <footer>&copy; OCA制作</footer>
</body>
</html>
