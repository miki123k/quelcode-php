<?php
session_start(); //セッションスタート
require('dbconnect.php'); //dbconnectファイルを呼び出しDBに接続

//postテーブルからリツイート投稿のオリジナル投稿の情報を取り出す
$posts = $db->prepare('SELECT * FROM posts WHERE id=?');
$posts->execute(array($_GET['id']));
$post = $posts->fetch();

//postsテーブルにリツイートしたものの情報を追加する※同じもののコピーを出したい
//もしリツイートしたものがリツイート投稿であればretweet_post_idに元投稿のidが入っているので今回のリツイート投稿のretweet_post_idにはこの投稿のidではなく元投稿のidが入るようにする
if (!$post['retweet_post_id'] == 0) {

    $retweets = $db->prepare('INSERT INTO posts SET message=?,member_id=?,retweet_post_id=?,retweet_member_id=?,created=NOW(),modified=NOW()');
    $retweets->execute(array($post['message'], $post['member_id'], $post['retweet_post_id'], $_SESSION['id']));
    $retweet = $retweets->fetch();
} else { //リツイート投稿でなければretweet_post_idに元の投稿のidが入るようにする
    $retweets = $db->prepare('INSERT INTO posts SET message=?,member_id=?,retweet_post_id=?,retweet_member_id=?,created=NOW(),modified=NOW()');
    $retweets->execute(array($post['message'], $post['member_id'], $post['id'], $_SESSION['id']));
    $retweet = $retweets->fetch();
}


header('Location: index.php');
exit();   //登録されたらindex.phpに戻る
