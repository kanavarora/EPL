<?php
require_once 'dbappinclude.php' ;
$to = "" ;
if (isset($_POST['to'])){
	$to = $_POST['to'] ;
} else {
	if (isset($_GET['to'])){
		$to = $_GET['to'] ;
	} else{
		$to  = $user ;
	}
}

if (isset($_POST['wallpost'])) {
	$comment = $_POST['wallpost'] ;
	$rs = query('INSERT INTO wallposts SET `time`= '. time().', `from` = '.$user.' , `comment` = \''.$_POST['wallpost'].'\' ,`to` = '.$to) ;
}
echo '<form action="" method="post">' ; 
echo '<input name="submit" type="submit" value="POST SOMETHING" />' ;
echo '<input type ="hidden" name="to" value='."$to".' />' ;
echo '<input type ="text" name="wallpost" value="" />'; 
echo '</form>' ;

$res = query('SELECT `from`, `to`, `time`, `comment` FROM wallposts WHERE `to` = '. $to .' ORDER BY `time` DESC') ;
$prints = array();
  while ($row = mysql_fetch_assoc($res)) {
    $prints[] = $row;
  }
foreach ($prints as $post){
		echo '<fb:if-can-see uid="' . $post['from'] . '"><div style="clear: both; padding: 3px;">'
           .   '<fb:profile-pic style="float: left;" uid="' . $post['from'] . '" size="square"/>'
           .   '<fb:name uid="' . $post['from'] . '" capitalize="true"/> wrote'
           .   ' at <fb:time t="'.$post['time'] .'"/>. '
	    .   '<br/> ' . $post['comment'] 
           .   '<br/>' . '<a href="http://apps.facebook.com/educationportal/wall.php?to=' . $post['from'] . '">'
	    .	 '<fb:if-is-user uid = "' . $to . '">'	
      	    .   'Reply to <fb:name uid="' . $post['from'] . '" firstnameonly="true" linked = "false"/>'
	    .   '</fb:if-is-user>'
           . '</a>'
	    . '<br/>'
           . '</div></fb:if-can-see>';

}
?>   