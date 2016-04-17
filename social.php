<?php
require_once('mysql.php');

$query =
  "SELECT ID, post_title AS title, post_date AS date,
   YEAR(post_date) AS year, DATE_FORMAT(post_date, '%m') AS month, DATE_FORMAT(post_date, '%d') AS day, post_name
  FROM wp_posts WHERE post_type = 'post' AND post_status = 'publish'
  ORDER BY date DESC LIMIT 0,10";

$link = mysql_connect($path, $user, $pass);
mysql_query("SET CHARACTER SET utf8");
mysql_select_db($db, $link);
$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		$url = $domain."wp/".$row["year"]."/".$row["month"]."/".$row["day"]."/".$row["post_name"]."/";
    
    // facebook
    $fql = urlencode("SELECT like_count, share_count, comment_count FROM link_stat WHERE url=\"");
    $get_facebook = "https://api.facebook.com/method/fql.query?query=".$fql.$url."\"";
		$xml = simplexml_load_string(file_get_contents($get_facebook));
    $likes = $xml->link_stat->like_count;
    $share = $xml->link_stat->share_count;
    $comment = $xml->link_stat->comment_count;
    if ($likes == '') {$likes = 0;}
    if ($share == '') {$share = 0;}
    if ($comment == '') {$comment = 0;}
    
    // twitter
    //$tweets = json_decode(file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=".$url));
    //$tweet = $tweets->count;
    $tweet = 0;

    // google plus
    //$shares = json_decode(file_get_contents('http://api.sharedcount.com/?url='.$url));
    //$gplus = $shares->{'GooglePlusOne'};  
    $gplus = 0;

    // hatena
    $hatena = file_get_contents('http://api.b.st-hatena.com/entry.count?url='.urlencode($url));
    if($hatena == ''){$hatena=0;}

    $check = "select * from wp_social where ID = '".$row["ID"]."';";
     // echo $check."\n";
    $checked = mysql_query($check);

    if(mysql_num_rows($checked) == 0) {
      $insert = "INSERT INTO wp_social VALUES('".$row["ID"]."', '".$likes."', '".$share."', '".$comment."', '.$tweet.', '.$gplus.', '.$hatena.', '".$row["date"]."', now());";
       // echo $insert."\n";
      $inserted = mysql_query($insert);
      if (!$inserted) {
        die("INSERT QUERY IS FAILED\n".mysql_error()."\n");
      }
    } else {
      $update = "update wp_social set fb_like = '".$likes."', fb_share = '".$share."', fb_comment = '".$comment."', tweet = '.$tweet.', go_plus = '$gplus', hatena = '.$hatena.', updated_date = now() where ID = '".$row["ID"]."';";
       // echo $update."\n";
      $updated = mysql_query($update);
      if (!$updated) {
        die("UPDATE QUERY IS FAILED\n".mysql_error()."\n");
      }
    }
	}
mysql_close($link);
?>
