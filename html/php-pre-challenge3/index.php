<?php
$limit = $_GET['target'];


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
