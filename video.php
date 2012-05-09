<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">
<?php
$res = query('SELECT `Title`, `Description`, `Video`,`time` FROM Classified ORDER BY `time` DESC') ;
$videos = array();
  while ($row = mysql_fetch_assoc($res)) {
    $videos[] = $row;
  }

$totalVideos = count($videos) ;
$pageToBeLoaded = false ;

if (!isset($_GET['num'])) 
{
	echo '<center><b>Error: No Video selected to view</b></center>' ;
} elseif ($_GET['num'] < 0 || $_GET['num']>= $totalVideos)
{
	echo '<center><b>Error: The selected video does not exist</b></center>'  ;
} else 
{
	$i= $_GET['num'] ;
	$post = $videos[$i] ;


	echo '<table width="100%" cellpadding="5"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<b><font face="Verdana" style="font-size: 9pt;" color="#FFFFFF">'.$post['Title'].'</font></b></td>'
	.'</tr></table><table width="100%" cellpadding="10"><tr>'
	.'<td bgcolor="#F7F7F7"  valign="top"><font size="2">'.$post['Description'].'</font></td></tr><tr>'
	.'<td bgcolor = "#F7F7F7" valign="top">'
		. '<center><fb:swf swfbgcolor="000000" swfsrc="'.$post['Video'].'" height="350" width="441" /></center>'
		. '<div align="right">Posted at: '.$post['time'].'</div><br /><div align="right"><a href="classified.php">Go back to Classified Videos</a></div><br /></td></tr></table>'  ; 


	if (isset($_POST['comment'])){
		query('INSERT INTO Comments SET `From` = '.$user.', `Video` = "'.$post['Video'].'", `Comment` = "'.$_POST['comment'].'", `Time` = '.time()) ;
	}
	$pageToBeLoaded= true ;
	
	echo '<center> '
	.'<table width="100%"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<b><font face="Verdana" style="font-size: 9pt;" color="#FFFFFF"><center>COMMENTS</center></font></b></td>'
	.'</tr><tr>'
	.'<td bgcolor="#F7F7F7" class="imp" valign="top" height="100">'
	.'<form method="post" action="">'
	.'<center><textarea rows="4" name="comment" cols="65" style="border: 1px solid #e9e9e9; font-size: 8pt; font-family: Verdana;">Post something here'
	.'</textarea></center>'
	.'&nbsp; &nbsp; &nbsp; &nbsp; <input type="submit" value="Post" name="commentsubmit" style="border: 1px solid #3b5998; font-family: Verdana; font-size: 9pt; color: #ffffff; background-color: #3b5998;" />'
	.'</form></td>'
	.'</tr></table></center>' ;


}
?>


<fb:wall>
<?php
if($pageToBeLoaded){
	$res = query('SELECT `From`, `Comment`, `Video`,`Time` FROM Comments WHERE `Video` = "'.$post['Video'].'" ORDER BY `time` DESC') ;
	$comments = array();
  	while ($row = mysql_fetch_assoc($res)) {
    		$comments[] = $row;
  	}
	
	foreach ($comments as $comment){
		echo '<fb:wallpost uid='.$comment['From'].' t='.$comment['Time'].'>'
			. $comment['Comment'] 
		 	.'<fb:wallpost-action href="'.$fbBaseUrl.'profile.php?id='.$comment['From'].'">View <fb:name uid='.$comment['From'].'" firstnameonly="true" possessive="true" linked="false"/> EPL profile</fb:wallpost-action></fb:wallpost>';
	}
} 		


?>
</fb:wall>



</td>
<td width="2" bgcolor= "#0000FF"> </td> 
<td valign="top">
<?php
include ('frame.php') ;
?>
</td>
</tr>
</table>
