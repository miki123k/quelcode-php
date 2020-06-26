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

//データベースから値をとってくる 参考サイト：https://www.youtube.com/watch?v=hnBUheNUAL0
//①SQLの準備
$sql = 'SELECT value FROM prechallenge3'; //prechallenge3テーブルに保存されている値を取り出す
//②SQLの実行
$stmt = $db->query($sql);
//③SQLの結果取り出し
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($result as $value) {
    $values[] = (int) $value; //取り出した値を文字列からint型に変換する
}
