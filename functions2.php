<?php
/**
 * PHPの暗黙の型変換を禁止して、型指定に厳密にチェックする
*/
declare(strict_types=1);

/**
 * PDOインスタンスを取得する関数
 * 
 * (PDO::ATTR_ERRMODE)PDO::ERRMODE_EXCEPTIONの値を設定することでエラーが発生したときPDOExceptionの例外を投げる
 */
function connect(): PDO
{
    $pdo = new PDO('mysql:host=localhost; dbname=SEISAKU; charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}

/**
 * HTMLエスケープする関数
 * escape(特殊な文字を処理する)
 * string(文字列)
 * htmlspecialchars(コードをテキストとして表示させるときに使うメソッド)
 * ENT_QUOTES(" 'どちらも変換)
 * ENT_HTML5(コードをHTML5として扱う)
 */
function escape(?string $value)
{
    return htmlspecialchars(strval($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * LIKE演算子のワイルドカードをエスケープする関数
 * 
 */
function escapeLike(?string $value)
{
    return preg_replace('/([_%#])/u', '#${1}', $value);
}
