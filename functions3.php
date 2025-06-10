<?php
//XSS対策
function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//セッションにトークンセット
function setToken(){
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

//セッション変数のトークンとPOSTされたトークンをチェック
function checkToken(){
    if(empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])){
        echo 'Invalid POST', PHP_EOL;
        exit;
    }
}

//POSTされた値のバリデーション
function validation($datas,$confirm = true)
{
    $errors = [];

    //生徒名と成績のチェック
    if(empty($datas['evaluation'])) {
        $errors['evaluation'] = 'Please enter username.';
    }else if(mb_strlen($datas['evaluation']) > 20) {
        $errors['evaluation'] = 'Please enter up to 20 characters.';
    }

    if(empty($datas['restaurantname'])) {
        $errors['restaurantname'] = 'Please enter username.';
    }else if(mb_strlen($datas['restaurantname']) > 20) {
        $errors['restaurantname'] = 'Please enter up to 20 characters.';
    }
    if(empty($datas['username'])) {
        $errors['username'] = 'Please enter username.';
    }else if(mb_strlen($datas['username']) > 20) {
        $errors['username'] = 'Please enter up to 20 characters.';
    }

    if(empty($datas['review'])) {
        $errors['review'] = 'Please enter username.';
    }else if(mb_strlen($datas['review']) > 100) {
        $errors['review'] = 'Please enter up to 100 characters.';
    }
    if(empty($datas['published'])) {
        $errors['published'] = 'Please enter username.';
    }else if(mb_strlen($datas['published']) > 20) {
        $errors['published'] = 'Please enter up to 20 characters.';
    }



}