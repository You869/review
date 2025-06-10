<?php
session_start();
// セッション変数 $_SESSION["loggedin"]を確認。ログイン済だったらウェルカムページへリダイレクト
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/style2.css">
</head>
<body>
    <main>
        <div class="welcome">
            <h1 class="my-5">ようこそ！<b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>さん。</h1>
            <div class="menu">
                <ul>
                    <li><a href="signout.php">Sign Out<i class="fas fa-angle-right fa-position-right"></i></a></li>
                    <li><a href="index.html">MENU<i class="fas fa-angle-right fa-position-right"></i></a></li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
