<?php

try {

    $user = "root";
    $password = "";

    $dbh = new PDO("mysql:host=localhost; dbname=seisaku; charset=utf8", "$user", "$password");

    $stmt = $dbh->prepare('DELETE FROM review WHERE id = :id');

    $stmt->execute(array(':id' => $_GET["id"]));

    echo "削除しました。";

} catch (Exception $e) {
          echo 'エラーが発生しました。:' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>削除完了</title>
  </head>
  <body>          
  <p>
      <a href="index.php">レビューリストへ</a>
  </p> 
  </body>
</html>
