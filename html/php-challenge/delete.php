<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
	$id = $_REQUEST['id'];


	// 投稿を検査する
	$messages = $db->prepare('SELECT * FROM posts WHERE id=?');
	$messages->execute(array($id));
	$message = $messages->fetch();

	if ($message['member_id'] === $_SESSION['id']) { //自分の投稿したものであれば
		// 自分の投稿を削除する
		$del = $db->prepare('DELETE FROM posts WHERE id=?');
		$del->execute(array($message['id']));
		//リツイートがあればリツイートも削除
		//$idとretweet_post_idが同じものがリツイート投稿なのでそれも削除


		if ($retweet_post_id == 0) { //オリジナル投稿ならリツイート投稿も削除する　
			$del2 = $db->prepare('DELETE FROM posts WHERE retweet_post_id=?');
			$del2->execute(array($message['id']));
		}
	}
}

header('Location: index.php');
exit();
