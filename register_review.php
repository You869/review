<?php
// ファイルの読み込み
require_once "dbconnect.php";
require_once "functions3.php";

// セッションの開始
session_start();
$user_id = $_SESSION['id'];  // セッションから現在のユーザーIDを取得

try {
    $user = "root";
    $password = "";
    $dbh = new PDO("mysql:host=localhost; dbname=seisaku; charset=utf8", $user, $password);
    $stmt = $dbh->query('SELECT * FROM review ORDER BY id DESC');
    $result = 0;
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
}

// POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'user_id'   => '',
    'evaluation'  => '',
    'restaurantname'  => '',
    'dishname' => '',
    'username' => '',
    'review' => '',
    'image' => '',  // 画像のファイル名を保存するための変数
    'tags' => '',  // タグの入力値
];

// GET通信だった場合はセッション変数にトークンを追加
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    setToken();
}

// POST通信だった場合はDBへの新規登録処理を開始
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach ($datas as $key => $value) {
        if ($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $datas[$key] = $value;
        }
    }

    // `user_id`を追加
    $datas['user_id'] = $user_id;  // セッションから取得したユーザーIDをdatas配列に追加


    // 画像ファイルの処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        // 画像の保存先ディレクトリ
        $uploadDir = 'img/';

        // 画像のファイル名をユニークに変更
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid('review_', true) . '.' . $imageExtension;
        $uploadPath = $uploadDir . $newImageName;

        // 画像を img フォルダに移動
        if (move_uploaded_file($imageTmpPath, $uploadPath)) {
            $datas['image'] = $newImageName; // 新しい画像名を保存
        } else {
            echo '画像のアップロードに失敗しました。';
        }
    }

    // バリデーション
    $errors = validation($datas);

    // エラーがなかったらDBへの新規登録を実行
    if (empty($errors)) {
        $params = [
            'user_id' => $datas['user_id'],
            'evaluation' => $datas['evaluation'],
            'restaurantname' => $datas['restaurantname'],
            'dishname' => $datas['dishname'],
            'username' => $datas['username'],
            'review' => $datas['review'],
            'image' => $datas['image'],  // 新しい画像名をデータベースに保存
            'tags'  => $datas['tags'],
        ];

        // カラム名と値の準備
        $count = 0;
        $columns = '';
        $values = '';
        foreach (array_keys($params) as $key) {
            if ($count > 0) {
                $columns .= ',';
                $values .= ',';
            }
            $columns .= $key;
            $values .= ':' . $key;
            $count++;
        }

        $dbh->beginTransaction(); // トランザクション処理
        try {
            $sql = 'INSERT INTO review (' . $columns . ') VALUES (' . $values . ')';
            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            $reviewId = $dbh->lastInsertId();  // 新しく挿入したレビューのIDを取得

            // タグ処理
            if (!empty($datas['tags'])) {
                $tags = explode(',', $datas['tags']);  // カンマ区切りでタグを分割
                foreach ($tags as $tag) {
                    $tag = trim($tag);  // 前後の空白を削除

                    // タグが既に存在するか確認
                    $stmt = $dbh->prepare('SELECT id FROM tags WHERE name = :name');
                    $stmt->execute(['name' => $tag]);
                    $existingTag = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingTag) {
                        // 既存タグがあれば、review_tagsに挿入
                        $tagId = $existingTag['id'];
                    } else {
                        // 新しいタグをtagsテーブルに挿入
                        $stmt = $dbh->prepare('INSERT INTO tags (name) VALUES (:name)');
                        $stmt->execute(['name' => $tag]);
                        $tagId = $dbh->lastInsertId();  // 新しく挿入されたタグのIDを取得
                    }

                    // review_tagsテーブルにデータを挿入
                    $stmt = $dbh->prepare('INSERT INTO review_tags (review_id, tag_id) VALUES (:review_id, :tag_id)');
                    $stmt->execute(['review_id' => $reviewId, 'tag_id' => $tagId]);
                }
            }

            $dbh->commit();  // コミット
            header("Location: index.php");  // 成功後、レビュー一覧ページへリダイレクト
            exit;
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            $dbh->rollBack();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/review.css">
    <style>

    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/icon150×150.png">
            <h1>Review</h1>
        </div>
        <div class="item">
            <p><a href="profile.php">プロフィール</a></p>
        </div>
        <div class="item">
            <p><a href="login.php">ログイン</a></p>
        </div>
    </header>
    <main>
        <div class="bg_pattern2 Rectangles2">
            <div class="form-group">
            <form action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post" enctype="multipart/form-data">
                <label>評価</label>
                <div class="rate-form">
                    <input id="star5" type="radio" name="evaluation" value="⭐⭐⭐⭐⭐" class="form-control <?php echo (!empty(h($errors['evaluation']))) ? 'is-invalid' : ''; ?>">
                    <label for="star5">★</label>
                    <input id="star4" type="radio" name="evaluation" value="⭐⭐⭐⭐" class="form-control <?php echo (!empty(h($errors['evaluation']))) ? 'is-invalid' : ''; ?>">
                    <label for="star4">★</label>
                    <input id="star3" type="radio" name="evaluation" value="⭐⭐⭐" class="form-control <?php echo (!empty(h($errors['evaluation']))) ? 'is-invalid' : ''; ?>">
                    <label for="star3">★</label>
                    <input id="star2" type="radio" name="evaluation" value="⭐⭐" class="form-control <?php echo (!empty(h($errors['evaluation']))) ? 'is-invalid' : ''; ?>">
                    <label for="star2">★</label>
                    <input id="star1" type="radio" name="evaluation" value="⭐" class="form-control <?php echo (!empty(h($errors['evaluation']))) ? 'is-invalid' : ''; ?>">
                    <label for="star1">★</label>
                </div>
                <span class="invalid-feedback"><?php echo h($errors['evaluation']); ?></span>

                <label>店名</label>
                <input type="text" name="restaurantname" class="form-control <?php echo (!empty(h($errors['restaurantname']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['restaurantname']);?>">
                <span class="invalid-feedback"><?php echo h($errors['restaurantname']); ?></span>

                <label>料理名</label>
                <input type="text" name="dishname" class="form-control <?php echo (!empty(h($errors['dishname']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['dishname']);?>">
                <span class="invalid-feedback"><?php echo h($errors['dishname']); ?></span>

                <label>レビュー</label>
                <input type="text" name="review" class="form-control <?php echo (!empty(h($errors['review']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['review']); ?>">
                <span class="invalid-feedback"><?php echo h($errors['review']); ?></span>

                <div class="none">
                <label>ユーザーネーム</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty(h($errors['username']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['username']); ?><?php echo htmlspecialchars($_SESSION["name"]);?>">
                    <span class="invalid-feedback"><?php echo h($errors['username']); ?></span>
                </div>

                <label>タグ（カンマ区切り）</label>
                <input type="text" name="tags" class="form-control <?php echo (!empty($errors['tags'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['tags']); ?>">
                <span class="invalid-feedback"><?php echo h($errors['tags']); ?></span>

                <label>画像</label>
                <input type="file" name="image" class="form-control <?php echo (!empty(h($errors['image']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['image']); ?>">

                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <input type="submit" class="decorated-btn click-down" value="追加">
            </form>
            </div>
            <div class="menubtn">
                <p><a href="index.html">メニューへ</a></p>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy;OCA制作展</p>
    </footer>
</body>
</html>
