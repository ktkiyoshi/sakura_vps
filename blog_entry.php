<?php
require_once('mysql.php');
$query1 = "select count(*) from wp_posts where post_status = 'publish' and post_type= 'post'";
$query2 = "select count(*) from wp_comments";
$query3 = "select post_date, post_content, post_title, guid from wp_posts where post_status = 'publish' order by post_date desc limit 1";

//月ごとの記事数取得
$month_count = array();
$i = 1;
date_default_timezone_set('Asia/Tokyo');
$day = getdate();
$y2 = $day['year'];
for($y=2006;$y<=$y2;$y++) {
	for($m=1;$m<=12;$m++) {
		if($m == 12) {
			$ym1 = $y.'-'.$m;
			$ym2 = ($y+1).'-01';
		} else if($m == 10 || $m == 11) {
			$ym1 = $y.'-'.$m;
			$ym2 = $y.'-'.($m+1);
		} else if($m == 9) {
			$ym1 = $y.'-0'.$m;
			$ym2 = $y.'-'.($m+1);
		} else {
			$ym1 = $y.'-0'.$m;
			$ym2 = $y.'-0'.($m+1);
		}
		$month_count[$i][$m] = "select count(*) from wp_posts where post_status = 'publish' and post_type = 'post' and post_date >= '$ym1' and post_date <= '$ym2'";
	}
		$i++;
}

//DB接続、クエリ実行
$link = mysql_connect($path, $user, $pass);
$sql = "SET CHARACTER SET utf8";
mysql_query($sql,$link);
mysql_select_db($db, $link);
$row1=mysql_fetch_array(mysql_query($query1, $link));
$row2=mysql_fetch_array(mysql_query($query2, $link));
$row3=mysql_fetch_array(mysql_query($query3, $link));
$count=$row1['count(*)'];
$count2=$row2['count(*)'];
$blogurl=$row3['guid'];
$content=$row3['post_content'];
$title=$row3['post_title'];
$post_date=$row3['post_date'];

//最初の画像のURLを取得
preg_match("/http:\/\/[a-z0-9\/\-_\.]+/i",$content,$match);
$content = $match[0];

//XMLファイルの生成
$i = 1;
$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
$xml .= "\n<blog>\n";
for($y=2006;$y<=$y2;$y++) {
	$xml .= "	<y$y>\n";
	for($m=1;$m<=12;$m++) {
		$row = mysql_fetch_array(mysql_query($month_count[$i][$m], $link));
		$xml .= "		<m$m>".$row['count(*)']."</m$m>\n";
	}
	$i++;
	$xml .= "	</y$y>\n";
}
$xml .="<count>".$count."</count>";
$xml .= "</blog>\n";
$handle = fopen('/var/www/html/entry_month.xml', 'w');
fwrite($handle, $xml);
fclose($handle);

//echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
// XMLファイルの読込み
$xmldata = simplexml_load_file('/var/www/html/entry_month.xml');
$item = array();
$i = 1;
for($y=2006;$y<=$y2;$y++) {
	$ya = 'y'.$y;
	for($m=1;$m<=12;$m++) {
		$ma = 'm'.$m;
		$item[$i][$m] = $xmldata->$ya->$ma;
	}
	$i++;
}
$count = $xmldata->count;
mysql_close($link);

echo $day['year'].'/'.$day['mon'].'/'.$day['mday'].'('.$day['weekday'].') '.$day['hours'].':'.$day['minutes'].' '.$count;
echo "\n";
?>
