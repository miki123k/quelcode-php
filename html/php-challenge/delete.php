<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
	$id = $_REQUEST['id'];


	// 投稿を検査する
	$messages = $db->prepare('SELECT * FROM posts WHERE id=?');
	$messages->execute(array($id));
	$message = $messages->fetch();

	//文字列なのでint型に変換
	//$member_id = (int) $message['member_id'];
	//$retweet_post_id = (int) $message['retweet_post_id'];



	if ($message['member_id'] == $_SESSION['id']) { //自分の投稿したものであれば
		// 自分の投稿を削除する
		$del = $db->prepare('DELETE FROM posts WHERE id=?');
		$del->execute(array($id));
		//リツイートがあればリツイートも削除
		//$idとretweet_post_idが同じものがリツイート投稿なのでそれも削除


		if ($message['retweet_post_id'] == 0) { //オリジナル投稿ならリツイート投稿も削除する　
			$del3 = $db->prepare('DELETE FROM posts WHERE retweet_post_id=?');
			$del3->execute(array($id));
		}
	}
}

header('Location: index.php');
exit();
