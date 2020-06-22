<?php
$limit = $_GET['target'];

//$limitが1以上の整数かチェック
if (!preg_match("/^[\d]+$/", $limit) || $limit <= 0 || preg_match("/^[0]/", $limit)) { //数字以外をチェック, 0以下をチェック, 先頭が０をチェック
    http_response_code(400); //1以上の整数でない場合はHTTPレスポンスステータスコード400で返す
    exit();
}
$limit = (int) $limit; //受け取ったものはstring(文字列)型なのでint(整数)型に変換する


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

//データベースから値をとってくる

//全ての数字の組み合わせをつくる（コピペでOK）2×８−１＝２５６の組み合わせになる

//作られた組み合わせから合計と一致するものを出力

//それをJSON形式で出力
