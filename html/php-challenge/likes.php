<?php
session_start(); //セッションスタート
require('dbconnect.php'); //dbconnectファイルを呼び出しDBに接続

//投稿について調べる、リツイート先、オリジナル投稿がある場合はそちらにもいいねしたい

$retweet_likes = $db->prepare('SELECT * FROM posts WHERE ID=?');
$retweet_likes->execute(array($_GET['id']));
$retweet_like = $retweet_likes->fetch();



//いいねした投稿がリツイート投稿であればオリジナル投稿にもいいねされる。

if (!$retweet_like['retweet_post_id'] == 0) { //retweet_post_idに0以外の値が入っていれば
    $in_original_like = $db->prepare('INSERT INTO likes SET member_id=?,post_id=?');
    $in_original_like->execute(array($_SESSION['id'], $retweet_like['retweet_post_id']));
}


//いいねの情報をlikesテーブルに挿入
$likes = $db->prepare('INSERT INTO likes SET member_id=?,post_id=?');
$likes->execute(array($_SESSION['id'], $_GET['id']));


header('Location: index.php');
exit();   //登録されたらindex.phpに戻る
