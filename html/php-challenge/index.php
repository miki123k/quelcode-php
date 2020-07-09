<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
	// ログインしている
	$_SESSION['time'] = time();

	$members = $db->prepare('SELECT * FROM members WHERE id=?');
	$members->execute(array($_SESSION['id']));
	$member = $members->fetch();
} else {
	// ログインしていない
	header('Location: login.php');
	exit();
}

// 投稿を記録する
if (!empty($_POST)) {
	if ($_POST['message'] != '') {
		$message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
		$message->execute(array(
			$member['id'],
			$_POST['message'],
			$_POST['reply_post_id']
		));

		header('Location: index.php');
		exit();
	}
}

// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
	$page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
	$response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
	$response->execute(array($_REQUEST['res']));

	$table = $response->fetch();
	$message = '@' . $table['name'] . ' ' . $table['message'];
}


// htmlspecialcharsのショートカット
function h($value)
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定します
function makeLink($value)
{
	return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css?20200708">
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>ひとこと掲示板</h1>
		</div>
		<div id="content">
			<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
			<form action="" method="post">
				<dl>
					<dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
					<dd>
						<textarea name="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
						<input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
					</dd>
				</dl>
				<div>
					<p>
						<input type="submit" value="投稿する" />
					</p>
				</div>
			</form>

			<?php
			foreach ($posts as $post) : //投稿ごとに繰り返されている。
				//オリジナル投稿であればpostのid、リツイートの場合はretweet_post_idを代入する
				if ($post['retweet_post_id'] == 0) {
					$origin_post_id = $post['id'];
				} else {
					$origin_post_id = $post['retweet_post_id'];
				}

				//いいね機能
				//ログインしているユーザーがその投稿をいいねしているか判定する。
				//この投稿にいいねしているidに自分のIDがあるか探す。
				$likes = $db->prepare('SELECT * FROM likes WHERE member_id=? AND post_id=?');
				$likes->execute(array($member['id'], $origin_post_id));
				$like = $likes->fetch();

				//いいね数の計算
				$likes_cnt = $db->prepare('SELECT COUNT(*) AS cnt FROM likes WHERE post_id=?');
				$likes_cnt->execute(array($origin_post_id));
				$like_cnt = $likes_cnt->fetch();


				//リツイート機能
				//この投稿をリツイートしている投稿があるか探す(投稿のidがretweet_post_idに入っているものがあるかどうか)
				//オリジナル投稿であればpostのidで検索し、リツイートの場合はretweet_post_idで検索する
				$retweets = $db->prepare('SELECT * FROM posts WHERE retweet_post_id=? AND retweet_member_id=?');
				$retweets->execute(array($origin_post_id, $member['id']));
				$retweet = $retweets->fetch();

				//リツイート数の計算
				//オリジナル投稿であればpostのidで検索し、リツイートの場合はretweet_post_idで検索する
				$retweets_cnt = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE retweet_post_id=?');
				$retweets_cnt->execute(array($origin_post_id));
				$retweet_cnt = $retweets_cnt->fetch();

				//リツイート者の表示
				$retweet_members = $db->prepare(('SELECT name FROM members WHERE id=?'));
				$retweet_members->execute(array($post['retweet_member_id']));
				$retweet_member = $retweet_members->fetch();
			?>
				<div class="msg">
					<?php
					if ($post['retweet_member_id'] === $member['id']) :
					?>
						<p class="retweet_message">リツイート済み</p>
					<?php
					elseif ($retweet_member) :
					?>
						<p class="retweet_message"><?php echo h($retweet_member['name']) ?>さんがリツイートしました</p>
					<?php
					endif;
					?>
					<img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
					<p><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
					<p class="day"><a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
						<?php
						if ($post['reply_post_id'] > 0) :
						?>
							<a href="view.php?id=<?php echo
														h($post['reply_post_id']); ?>">
								返信元のメッセージ</a>
						<?php
						endif;
						?>
						<?php
						if ($_SESSION['id'] === $post['member_id']) :
						?>
							[<a href="delete.php?id=<?php echo h($origin_post_id); ?>" style="color: #F33;">削除</a>]
						<?php
						endif;
						?>
						<div class="action">
							<!--いいね-->
							<div class="like">
								<?php
								if ($like) : //もし$likeに入っていれば赤いハートを表示
								?>
									<a href="cancel_likes.php?id=<?php echo h($post['id']); ?>"><img src="images/ハートのマーク.png" width="15" height="15" alt="いいね済みハート"></img></a>
								<?php
								elseif ($post['id'] == $post['retweet_post_id']) : //リツイート投稿があればそちらにも赤いハートを表示
								?>
									<a href="cancel_retweets.php?id=<?php echo h($post['id']); ?>"><img src="images/リツイート済みアイコン.png" width="15" height="15" alt="リツイート済み"></a>
								<?php
								else : //なければ色の無いハートを表示
								?>
									<a href="likes.php?id=<?php echo h($post['id']); ?>"><img src="images/8760.png" width="15" height="15" alt="色無しハート"></img></a>
								<?php
								endif;
								?>
								<?php
								if (!$like_cnt['cnt'] == 0) : //もしいいね数が０でなければ
								?>
									<?php
									echo $like_cnt['cnt']; //いいねのカウント数表示する
									?>
								<?php
								endif;
								?>
							</div>
							<div class="retweet">
								<!--リツイート-->
								<?php //自分のidとpostsテーブルのretweet_member_idが同じであり、投稿のidとretweet_post_idも同じものがあれば$retweetに値が入っている。
								if ($retweet) : //もし入っていれば(リツイートされていれば)水色のリツイート済みアイコンを表示
								?>
									<a href="cancel_retweets.php?id=<?php echo h($origin_post_id); ?>"><img src="images/リツイート済みアイコン.png" width="15" height="15" alt="リツイート済み"></a>
								<?php
								else :  //なければ灰色のリツイートアイコンを表示
								?>
									<a href="retweet.php?id=<?php echo h($origin_post_id); ?>"><img src="images/リツイートアイコン.png" width="15" height="15" alt="リツイート"></a>
								<?php
								endif;
								?>
								<?php
								if (!$retweet_cnt['cnt'] == 0) : //もしリツイート数が０でなければ
									echo $retweet_cnt['cnt']; //リツイートのカウント数表示する
								?>
								<?php
								endif;
								?>
							</div>
						</div>
					</p>
				</div>
			<?php
			endforeach;
			?>
			<ul class="paging">
				<?php
				if ($page > 1) {
				?>
					<li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
				<?php
				} else {
				?>
					<li>前のページへ</li>
				<?php
				}
				?>
				<?php
				if ($page < $maxPage) {
				?>
					<li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
				<?php
				} else {
				?>
					<li>次のページへ</li>
				<?php
				}
				?>
			</ul>
		</div>
	</div>
</body>

</html>
