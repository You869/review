<?php
//ファイルの読み込み
require_once "dbconnect.php";
require_once "functions.php";
//セッション開始
session_start();

//セッション変数 $_SESSION["loggedin"]を確認。ログイン済だったらウェルカムページへリダイレクト
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

//POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'name'  => '',
    'password'  => '',
    'confirm_password'  => ''
];
$login_err = "";

//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}

//POST通信だった場合はログイン処理を開始
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ////CSRF対策
    checkToken();

    //POSTされてきたデータを変数に格納
    foreach($datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $datas[$key] = $value;
        }
    }

    //バリデーション
    $errors = validation($datas,false);
    if(empty($errors)){
        //ユーザーネームから該当するユーザー情報を取得
        $sql = "SELECT id, name, password, image FROM login WHERE name = :name";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue('name',$datas['name'],PDO::PARAM_INT);
        $stmt->execute();

        //ユーザー情報があれば変数に格納
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            //パスワードがあっているか確認
            if (password_verify($datas['password'],$row['password'])) {
                //セッションIDをふりなおす
                session_regenerate_id(true);
                //セッション変数にログイン情報を格納
                $_SESSION["loggedin"] = true;
                $_SESSION["image"] = $row['image'];
                $_SESSION["id"] = $row['id'];
                $_SESSION["name"] =  $row['name'];
                $_SESSION["image"] =  $row['image'];
                //ウェルカムページへリダイレクト
                header("location:index.html");
                exit();
            } else {
                $login_err = 'Invalid username or password.';
            }
        }else {
            $login_err = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style2.css">     
</head>
<body>
    <main>
        <div class="form">
            <div class="wrapper">
                <div class="logo">
                    <img src="img/logo 3.png">
                </div>
                <div class="text">
                    <h2>ログイン</h2>
                </div>
                <?php 
                if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }        
                ?>

                <form action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post">
                    <div class="form-group">
                        <label>ユーザーID</label>
                        <input type="text" name="name" placeholder="ユーザーIDを入力" class="form-control <?php echo (!empty(h($errors['name']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['name']); ?>">
                        <!-- <span class="invalid-feedback"><?php echo h($errors['name']); ?></span> -->
                    </div>    
                    <div class="form-group">
                        <label>パスワード</label>
                        <input type="password" name="password" placeholder="パスワードを入力" class="form-control <?php echo (!empty(h($errors['password']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['password']); ?>">
                        <!-- <span class="invalid-feedback"><?php echo h($errors['password']); ?></span> -->
                    </div>
                    <div class="form-btn">
                        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                    <div class="login-btn">
                        <p>アカウントをもっていませんか？ <a href="register.php">アカウント作成</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>