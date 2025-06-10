<?php
// DB接続設定
$user = "root";
$password = "";
$dsn = "mysql:host=localhost;dbname=seisaku;charset=utf8";

// 検索ワードを受け取る
$title = isset($_GET['title']) ? $_GET['title'] : '';

if ($title !== '') {
    try {
        // データベース接続
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQLクエリで料理名を検索（部分一致検索）
        $stmt = $dbh->prepare('SELECT * FROM review WHERE dishname LIKE :title ORDER BY id DESC');
        $stmt->bindValue(':title', '%' . $title . '%', PDO::PARAM_STR);
        $stmt->execute();

        // 結果を配列として取得
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 結果に画像のパスを含めてJSON形式で返す
        foreach ($results as &$result) {
            // 画像のパスが存在すれば追加
            if (!empty($result['image'])) {
                $result['image_url'] = 'img/' . $result['image']; // 画像のパスを追加
            } else {
                $result['image_url'] = ''; // 画像がない場合は空
            }
        }

        // JSON形式で結果を返す
        echo json_encode($results);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'エラーが発生しました。' . $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}

