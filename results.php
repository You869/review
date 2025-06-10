<?php

declare(strict_types=1);

require_once dirname(__FILE__) . '/functions2.php';

// メインルーチン
//$_GET[inputのname=""]
//変数に値が入っていたら || 余計な空白を取り除く
try {
    if (!isset($_GET['title']) || trim($_GET['title']) === '') {
        return;
    }
    // データの取得
    $pdo = connect();
    $statement = $pdo->prepare("SELECT * FROM review WHERE dishname LIKE :dishname ESCAPE '#' ORDER BY published DESC");
    $statement->bindValue(':dishname', '%' . escapeLike($_GET['title']) . '%', PDO::PARAM_STR);
    $statement->execute();
} catch (PDOException $e) {
    echo '料理名の検索に失敗しました';
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style2.css">
    <title>検索結果</title>
</head>
<body>    
    <div class="review_list">
        <h3>「<?=escape($_GET['title'])?>」を含む料理の検索結果</h3>
        <table border="1" >
            <tr>
                <th>評価</th>     
                <th>店名</th>
                <th>料理名</th>
                <th>名前</th>
                <th>レビュー</th>
                <th>登録日</th>
            </tr>
            
            <!-- データの取り出し -->
            <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?=escape($row['evaluation'])?></td>
                    <td><?=escape($row['restaurantname'])?></td>
                    <td><?=escape($row['dishname'])?></td>
                    <td><?=escape($row['username'])?></td>
                    <td><?=escape($row['review'])?></td>
                    <td><?=escape($row['published'])?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p><a href="index.html">メニューへ</a></p>
        <p><a href="search.html">検索</a></p>
    </div>
</body>
</html>