<?php

function dbc()
{
    /* ①　データベースの接続情報を定数に格納する */
    $host = "localhost";
    $dbname = "file_db";
    $user = "root";
    $pass = "";
    
    $dns = "mysql:host=$host;dbname=$dbname;charset=utf8";

    //②　例外処理を使って、DBにPDO接続する
    try {
        $pdo = new PDO($dns, $user, $pass,[
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES =>false
        ]);
        return $pdo;
        } catch (PDOException $e){
            exit($e->getMessage());
        }
    }

/**
 * ファイルデータを保存
 * @param string $filename ファイル名
 * @param string $save_path 保存先のパス
 * @return string $caption 投稿の説明
 * @return bool $result
 */

function fileSave($filename, $save_path, $caption)
{
    $result = False;

    $sql = "INSERT INTO file_table (file_name, file_path, description) VALUE(?, ?, ?)";
    
    try{
        $stmt = dbc()->prepare($sql);
        $stmt->bindValue(1, $filename);
        $stmt->bindvalue(2, $save_path);
        $stmt->bindValue(3, $caption);
        $result = $stmt->execute();
        return $result;    
    }catch(\Exception $e){
        echo $e->getMessage();
        return $result;
    }
}
function getALLFile()
{
    $sql = "SELECT * FROM file_table";

    $fileData = dbc()->query($sql);

    return $fileData;
}
function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}