<?php
session_start();  // セッションの開始

// ログインしているか確認
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

// 現在ログインしているユーザーのIDを取得
$userid = $_SESSION['id'];

try {
    $user = "root";
    $password = "";
    $dbh = new PDO("mysql:host=localhost; dbname=SEISAKU; charset=utf8", "$user", "$password");

    // user_idと$_SESSION['id']が一致するレビューのみを取得するクエリ
    $stmt = $dbh->prepare('SELECT * FROM review WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を取得
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
    exit;
}

// もし$resultが空の場合、$resultを空の配列で初期化
if (!$result) {
    $result = [];
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>レビュー一覧</title>
    <link rel="stylesheet" href="css/myreview.css">
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
        <div class="list">
          <table border="1">
            <thead>
              <tr>
                <th>評価</th>
                <th>店名</th>
                <th>料理名</th>
                <th>レビュー</th>
                <th>画像</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($result)): ?>
                  <tr><td colspan="7">レビューはまだありません。</td></tr>
              <?php else: ?>
                  <?php foreach ($result as $review): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($review['evaluation']); ?></td>
                      <td><?php echo htmlspecialchars($review['restaurantname']); ?></td>
                      <td><?php echo htmlspecialchars($review['dishname']); ?></td>
                      <td><?php echo htmlspecialchars($review['review']); ?></td>
                      <td>
                          <?php if (!empty($review['image'])): ?>
                              <img src="img/<?php echo htmlspecialchars($review['image']); ?>" alt="レビュー画像" class="img-fluid" style="width: 100px;">
                          <?php else: ?>
                              画像なし
                          <?php endif; ?>
                      </td>
                      <td>
                          <a href="delete.php?id=<?php echo htmlspecialchars($review['id']); ?>" onclick="return confirm('本当に削除してもよろしいですか？');">削除</a>
                      </td>

                  </tr>
                  <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
    </main>

      <div class="btn1">
        <p><a href="index.html">メニューに戻る</a></p>
        <p><a href="register_review.php">レビューする</a></p>
      </div>
    <footer>&copy; OCA制作</footer>
  </body>
</html>
