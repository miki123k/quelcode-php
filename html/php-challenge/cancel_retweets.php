<?php
session_start(); //セッションスタート
require('dbconnect.php'); //dbconnectファイルを呼び出しDBに接続


//投稿を検査し、自分がリツイートしたものであれば削除できる

$cancel_retweet = $db->prepare('DELETE FROM posts WHERE retweet_post_id=? AND retweet_member_id=?');
$cancel_retweet->execute(array($_GET['id'], $_SESSION['id']));




header('Location: index.php');
exit();   //登録されたらindex.phpに戻る
