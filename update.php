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

    // 名前の更新処理
    if (isset($_POST['new_name']) && !empty($_POST['new_name'])) {
        $new_name = $_POST['new_name'];
        
        // 名前を更新するSQLクエリ
        $stmt = $dbh->prepare('UPDATE login SET name = :new_name WHERE id = :id');
        $stmt->execute([':new_name' => $new_name, ':id' => $id]);

        // セッションのユーザー名も更新
        $_SESSION['name'] = $new_name;
    }

    // プロフィール画像の更新処理
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // アップロードされた画像の情報を取得
        $file_name = $_FILES['profile_image']['name'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_type = $_FILES['profile_image']['type'];

        // 画像の拡張子を取得
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // 画像の拡張子チェック（例: jpg, png, gif など）
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_exts)) {
            echo "画像の拡張子はjpg, jpeg, png, gifのいずれかである必要があります。";
            exit();
        }

        // ファイル名をユニークにするために時間を使って名前を変更
        $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
        $upload_dir = 'img/'; // アップロードディレクトリ

        // 画像を指定のディレクトリに移動
        if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            // データベースに画像のパスを保存
            $stmt = $dbh->prepare('UPDATE login SET image = :image WHERE id = :id');
            $stmt->execute([':image' => $new_file_name, ':id' => $id]);

            // セッションのプロフィール画像も更新
            $_SESSION['image'] = $new_file_name;

            echo "プロフィール画像が更新されました。";
        } else {
            echo "画像のアップロードに失敗しました。";
        }
    }

    echo "情報が更新されました。";
    header('Location: profile_update.php'); // 更新後、プロフィールページにリダイレクト

} catch (Exception $e) {
    echo 'エラーが発生しました。:' . $e->getMessage();
}
?>
