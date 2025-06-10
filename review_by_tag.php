<?php
// タグを取得
if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];

    try {
        $user = "root";
        $password = "";
        $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", "$user", "$password");

        // タグに関連するレビューを取得
        $stmt = $dbh->prepare('
            SELECT review.*, GROUP_CONCAT(tags.name ORDER BY tags.name ASC) AS tag_names
            FROM review
            LEFT JOIN review_tags ON review.id = review_tags.review_id
            LEFT JOIN tags ON review_tags.tag_id = tags.id
            WHERE tags.name = :tag
            GROUP BY review.id
            ORDER BY review.id DESC
        ');
        $stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'エラーが発生しました。:' . $e->getMessage();
    }
} else {
    echo 'タグが指定されていません。';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tag); ?> タグのレビュー一覧</title>
    <link rel="stylesheet" href="css/reviewlist.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($tag); ?> タグのレビューリスト</h1>
        <div class="item">
            <p><a href="profile.php">プロフィール</a></p>
            <p><a href="login.php">ログイン</a></p>
        </div>
    </header>
    <main>
        <div class="bg_pattern3 Rectangles3">
            <div class="menu-list2">
                <div class="list">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>画像</th>
                                <th>評価</th>
                                <th>ユーザーネーム</th>
                                <th>店名</th>
                                <th>料理名</th>
                                <th>レビュー</th>
                                <th>タグ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $review): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($review['image'])): ?>
                                            <a href="img/<?php echo htmlspecialchars($review['image']); ?>"><img src="img/<?php echo htmlspecialchars($review['image']); ?>" alt="レビュー画像" class="img-fluid" style="width: 100px;"></a>
                                        <?php else: ?>
                                            画像なし
                                        <?php endif; ?>
                                    </td>
                                    <!-- 評価 -->
                                    <td><?php echo htmlspecialchars($review['evaluation']); ?></td>
                                    <!-- ユーザーネーム -->
                                    <td><?php echo htmlspecialchars($review['username']); ?></td>
                                    <!-- 店名 -->
                                    <td><?php echo htmlspecialchars($review['restaurantname']); ?></td>
                                    <!-- 料理名 -->
                                    <td><?php echo htmlspecialchars($review['dishname']); ?></td>
                                    <!-- レビュー -->
                                    <td><?php echo htmlspecialchars($review['review']); ?></td>
                                    <!-- タグ（コンマ区切りで表示） -->
                                    <td>
                                        <?php 
                                            $tags = explode(',', $review['tags']);
                                            foreach ($tags as $tag) {
                                                echo '<a href="review_by_tag.php?tag=' . urlencode($tag) . '">' . htmlspecialchars($tag) . '</a> ';
                                            }
                                        ?>
                                    </td>                                    

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
