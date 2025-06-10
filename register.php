<?php
// ファイルの読み込み
require_once "dbconnect.php";
require_once "functions.php";

// セッションの開始
session_start();

// POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'name'  => '',
    'password'  => '',
    'confirm_password'  => '',
    'birth_year' => '',
    'birth_month' => '',
    'birth_day' => ''
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
        $datas[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
    }

    // バリデーション
    $errors = validation($datas);

    // データベースの中に同一ユーザー名が存在していないか確認
    if (empty($errors['name'])) {
        $sql = "SELECT id FROM login WHERE name = :name";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':name', $datas['name'], PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $errors['name'] = 'このユーザー名はすでに使用されています。';
        }
    }

    // エラーがなかったらDBへの新規登録を実行
    if (empty($errors)) {
        // 生年月日を1つの文字列（YYYY-MM-DD）に統合
        $birth_date = $datas['birth_year'] . '-' . $datas['birth_month'] . '-' . $datas['birth_day'];

        // データベースへの登録処理
        $params = [
            'name' => $datas['name'],
            'password' => password_hash($datas['password'], PASSWORD_DEFAULT),
            'birth_date' => $birth_date // 生年月日を追加
        ];

        // SQLのカラム名と値のセットを作成
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
            // SQLの実行
            $sql = 'INSERT INTO login (' . $columns . ') VALUES (' . $values . ')';
            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            $dbh->commit();
            header("location: login.php"); // 登録後、ログインページへリダイレクト
            exit;
        } catch (PDOException $e) {
            // エラー発生時にロールバック
            echo 'ERROR: ' . $e->getMessage(); 
            $dbh->rollBack();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント作成</title>
    <link rel="stylesheet" href="css/style2.css">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>
<body>
    <div class="wrapper">
        <div class="logo">
            <img src="img/logo 3.png">
        </div>
        <div class="text">
            <h2>アカウント作成</h2>
        </div>
        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
                <p>ユーザーID</p>
                <input type="text" name="name" class="form-control <?php echo (!empty($errors['name'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datas['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <!-- <span class="invalid-feedback"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></span> -->
            </div>    
            <div class="form-group">
                <p>パスワード</p>
                <input type="password" name="password" class="form-control <?php echo (!empty($errors['password'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datas['password'], ENT_QUOTES, 'UTF-8'); ?>">
                <!-- <span class="invalid-feedback"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></span> -->
            </div>
            <div class="form-group">
                <p>パスワード(確認)</p>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($errors['confirm_password'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datas['confirm_password'], ENT_QUOTES, 'UTF-8'); ?>">
                <!-- <span class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8'); ?></span> -->
            </div>

            <!-- 生年月日選択 -->
            <div class="birth-group">
                <label>生年月日: </label><br>

                <select name="birth_year" required>
                    <option value="">年を選択</option>
                    <?php
                        $current_year = date("Y");
                        for ($year = $current_year; $year >= 1900; $year--) {
                            echo "<option value='$year'" . ($datas['birth_year'] == $year ? ' selected' : '') . ">$year</option>";
                        }
                    ?>
                </select>

                <select name="birth_month" required>
                    <option value="">月を選択</option>
                    <?php
                        for ($month = 1; $month <= 12; $month++) {
                            $month_str = str_pad($month, 2, "0", STR_PAD_LEFT);
                            echo "<option value='$month_str'" . ($datas['birth_month'] == $month_str ? ' selected' : '') . ">$month_str 月</option>";
                        }
                    ?>
                </select>

                <select name="birth_day" required>
                    <option value="">日を選択</option>
                    <?php
                        $days_in_month = date("t", strtotime("{$datas['birth_year']}-{$datas['birth_month']}-01"));
                        for ($day = 1; $day <= $days_in_month; $day++) {
                            $day_str = str_pad($day, 2, "0", STR_PAD_LEFT);
                            echo "<option value='$day_str'" . ($datas['birth_day'] == $day_str ? ' selected' : '') . ">$day_str 日</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="submit" class="btn btn-primary" value="アカウント作成">
            </div>
            <div class="login-move">
                <p>アカウントを持っている？ <a href="login.php">ログイン</a>.</p>
            </div>  
        </form>
    </div>    
</body>
</html>
