<?php
// セッション開始
session_start();

// データベース接続
try {
    $dbh = new PDO("mysql:host=localhost; dbname=seisaku; charset=utf8", "root", "");
    
    // ユーザーID（セッションから取得）
    $user_id = $_SESSION['user_id']; // ログイン中のユーザーIDをセッションに保存しておく必要があります
    $review_id = $_POST['review_id']; // POSTされたレビューID
    
    // すでにお気に入りに追加されているか確認
    $stmt = $dbh->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND review_id = :review_id");
    $stmt->execute(['user_id' => $user_id, 'review_id' => $review_id]);
    $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$favorite) {
        // お気に入りに追加
        $stmt = $dbh->prepare("INSERT INTO favorites (user_id, review_id) VALUES (:user_id, :review_id)");
        $stmt->execute(['user_id' => $user_id, 'review_id' => $review_id]);
        header("Location: index.php"); // お気に入りを追加した後にリダイレクト
        exit;
    } else {
        echo "すでにお気に入りに登録されています。";
    }
} catch (PDOException $e) {
    echo 'エラー: ' . $e->getMessage();
}
?>
