<?php
try {
  $user = "root";
  $password = "";
  $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", "$user", "$password");       
  // レビューとタグを関連付けて取得
  $stmt = $dbh->prepare('
      SELECT review.*, GROUP_CONCAT(tags.name ORDER BY tags.name ASC) AS tags
      FROM review
      LEFT JOIN review_tags ON review.id = review_tags.review_id
      LEFT JOIN tags ON review_tags.tag_id = tags.id
      GROUP BY review.id
      ORDER BY review.id DESC
  ');
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo 'エラーが発生しました。:' . $e->getMessage();
}
?>
 <!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>レビュー一覧</title>
    <link rel="stylesheet" href="css/reviewlist.css">

  </head>
  <body>
    <header>
      <h1>review list</h1>
      <div class="item">
            <p><a href="profile.php">profile</a></p>
        </div>
        <div class="item2">
            <p><a href="login.php">login</a></p>
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
                <th>削除</th>
              </tr>
            </thead>
              <tbody>
                    <?php foreach ($result as $review): ?>
                        <tr>
                        <td>
                          <?php if (!empty($review['image'])): ?>
                            <a href="img/<?php echo htmlspecialchars($review['image']); ?>">
                              <div class="img-container">
                                <img src="img/<?php echo htmlspecialchars($review['image']); ?>" alt="レビュー画像" class="img-fluid" >
                              </div>
                            </a>
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
                            <!-- 画像（もし存在すれば） -->
                             <!-- タグ（コンマ区切りで表示） -->
                             <td>
                                  <?php 
                                      $tags = explode(',', $review['tags']);
                                      foreach ($tags as $tag) {
                                          echo '<a href="review_by_tag.php?tag=' . urlencode($tag) . '">' . htmlspecialchars($tag) . '</a> ';
                                      }
                                  ?>
                              </td>                                    
                            <!-- 削除リンク -->
                            <td>
                                <a href="delete.php?id=<?php echo htmlspecialchars($review['id']); ?>" onclick="return confirm('本当に削除してもよろしいですか？');">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>            </table>
          </div>
          <!-- https://qiita.com/wakahara3/items/841b5c1abfe5e6e59249 -->
          <div class="btn1">
            <p><a href="index.html">メニューに戻る</a></p>
          </div>
          <div class="btn2">
            <p><a href="register_review.php">レビューする</a></p>
          </div>

      <script src="script.js"></script>
      </div>

    </main>
    
<footer>&copy; OCA制作</footer>

  </body>
</html>

