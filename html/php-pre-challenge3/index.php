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
//全ての組み合わせを作る　参考サイト：https://stabucky.com/wp/archives/2188
function combinations($array, $pick) //combinationファンクションで$array(配列全体)、$pick(選びとる数)の全ての組み合わせを作成
{
    $arrayCount = count($array);
    if ($arrayCount < $pick) {
        return;
    } elseif ($pick == 1) {
        for ($i = 0; $i < $arrayCount; $i++) {
            $arrs[$i] = array($array[$i]);
        }
    } elseif ($pick > 1) {
        $j = 0;
        for ($i = 0; $i < $arrayCount - $pick + 1; $i++) {
            $ts = combinations(array_slice($array, $i + 1), $pick - 1);
            foreach ($ts as $t) {
                array_unshift($t, $array[$i]);
                $arrs[$j] = $t;
                $j++;
            }
        }
    }
    return $arrs;
}
//作られた組み合わせから合計と一致するものを出力する

$length = count($values);          //$valueはデータベースに入っている値、$lengthにデータベースの要素の数を代入
$answers = [];                        //一致するものを$answersに入れる
for ($i = 1; $i < $length + 1; $i++) {
    $contents = combinations($values, $i);   //データベースの組み合わせの結果を代入
    foreach ($contents as $content) {
        if (array_sum($content) === $limit) { //$contentの合計を$limit(getパラメータの値)と比較
            $answers[] = $content;  //同一であれば$answersに$contentを含める array_pushより[]の方が速度が早いようなので[]であとで書き換える
        }
    }
}

//JSON形式で出力
echo json_encode($answers);
