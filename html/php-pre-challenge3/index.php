<?php
$limit = $_GET['target'];

//$limitが1以上の整数かチェック
if (!preg_match("/^[\d]+$/", $limit) || $limit <= 0 || preg_match("/^[0]/", $limit)) { //数字以外をチェック, 0以下をチェック, 先頭が０をチェック
    http_response_code(400); //1以上の整数でない場合はHTTPレスポンスステータスコード400で返す
    exit();
}


$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';

//データベースに接続
try {
    $db = new PDO($dsn, $dbuser, $dbpassword); //データベースオブジェクトの作成
} catch (PDOException $e) {
    http_response_code(500); //データベースに接続できない際はHTTPレスポンスステータスコード500で返す
    exit();
}
