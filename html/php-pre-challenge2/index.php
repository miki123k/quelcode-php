<?php
$array = explode(',', $_GET['array']);

// 修正はここから
$count = count($array);
for ($i = 0; $i < $count - 1; $i++) {
    for ($j = $i + 1; $j < $count; $j++) {
        //$iの値が$jの値より大きければ
        if ($array[$i] > $array[$j]) {
            //$tmpに$iを一旦代入
            $tmp = $array[$i];
            //$iに$jを代入して入れ替える
            $array[$i] = $array[$j];
            //$jに$tmpを代入
            $array[$j] = $tmp;
        }
    }
}
//参考にした動画はこちらです。https://www.youtube.com/watch?v=WY-_iKLNhA0
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";
