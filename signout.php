<?php

// セッションを開始する
session_start();
// セッションを破棄する
session_destroy();
// ログイン画面にリダイレクトする
header("location: login.php");