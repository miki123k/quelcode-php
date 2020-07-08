<?php
session_start(); //セッションスタート
require('dbconnect.php'); //dbconnectファイルを呼び出しDBに接続


//自分がいいねした投稿がリツイート投稿であるか確認し、そうであればオリジナル投稿からもいいねを削除したい
$likes_retweet = $db->prepare('SELECT retweet_post_id FROM posts WHERE id=?');
$likes_retweet->execute(array($_GET['id']));
$like_retweet = $likes_retweet->fetch();


//もしリツイート投稿であればオリジナル投稿からもいいねを削除する。
if (!$like_retweet['retweet_post_id'] == 0) { //retweet_post_idに値が入っていれば
    $cancel_like_retweet = $db->prepare('DELETE FROM likes WHERE member_id=? AND post_id=?');
    $cancel_like_retweet->execute(array($_SESSION['id'], $like_retweet['retweet_post_id']));
}


//likesテーブルからいいねを削除

$cancel_origin_like = $db->prepare('DELETE FROM likes WHERE post_id=? AND member_id=?');
$cancel_origin_like->execute(array($_GET['id'], $_SESSION['id']));

header('Location: index.php');
exit();   //登録されたらindex.phpに戻る
