<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">



<?php
$pageToBeLoaded = false ;
if (isset($_GET['team1']) && isset($_GET['team2']) && isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
	
	$res = query ('SELECT * FROM MatchesVideos 
				WHERE 
				`Team1` = "'.$_GET['team1'].'" AND `Team2` = "'.$_GET['team2'].'"
				AND `Day` = '.$_GET['d'].' AND `Month` = '.$_GET['m'].' AND
				`Year` = '.$_GET['y']) ;

	$matchVideos = array() ;
	while($row = mysql_fetch_assoc($res)){
		$matchVideos[] = $row;
	}
	if (count($matchVideos) ==0){
		echo 'No video for the specified teams at the given date' ; 
	} elseif( count($matchVideos) >1){
		echo 'More than one video between the same teams at the same date. Some problem. We will figure it out soon.';
	} else{
		$pageToBeLoaded = true ;
	}
}
?>

<?php
if ($pageToBeLoaded){
	
	$matchVideo = $matchVideos[0] ; 
	printVideoFeed($matchVideo) ;
}

?>

<?php
if ($pageToBeLoaded){
	
	
	if (isset($_POST['comment'])){
		query('INSERT INTO MatchesComments SET 
				`From` = '.$user.', 
				`Team1` = "'.$matchVideo['Team1'].'", 
				`Team2` = "'.$matchVideo['Team2'].'",
				`Day` = '.$matchVideo['Day'].',
				`Month` = '.$matchVideo['Month'].',
				`Year` = '.$matchVideo['Year'].', 
				`Comment` = "'.$_POST['comment'].'", 
				`Time` = '.time()) ;
	}
	
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

	echo '<fb:wall>' ; 
	$res = query('SELECT * FROM MatchesComments WHERE 
				`Team1` = "'.$matchVideo['Team1'].'" AND 
				`Team2` = "'.$matchVideo['Team2'].'" AND
				`Day` = '.$matchVideo['Day'].' AND
				`Month` = '.$matchVideo['Month'].' AND
				`Year` = '.$matchVideo['Year'].'
				ORDER BY `Time` DESC') ;
	$comments = array();
  	while ($row = mysql_fetch_assoc($res)) {
    		$comments[] = $row;
  	}
	
	foreach ($comments as $comment){
		echo '<fb:wallpost uid='.$comment['From'].' t='.$comment['Time'].'>'
			. $comment['Comment'] 
		 	.'<fb:wallpost-action href="'.$fbBaseUrl.'profile.php?id='.$comment['From'].'">View <fb:name uid='.$comment['From'].'" firstnameonly="true" possessive="true" linked="false"/> EPL profile</fb:wallpost-action></fb:wallpost>';
	}

	echo '</fb:wall>' ;
}
?>


<?php

function printVideoFeed($matchVideo){

	///title
	global $teams ; 
	echo '<table width="100%" cellpadding="1"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<b><font face="Verdana" style="font-size: 12pt;" color="#FFFFFF">' ;
	if (in_array($matchVideo['Team1'], $teams)){
		echo '<a href="teampage.php?team='.$matchVideo['Team1'].'"><font color="#FFFFFF">' ; 
	}
	echo $matchVideo['Team1'] ;
	if (in_array($matchVideo['Team1'], $teams)){
		echo '</font></a>' ; 
	}
	echo ' Vs ' ; 
	if (in_array($matchVideo['Team2'], $teams)){
		echo '<a href="teampage.php?team='.$matchVideo['Team2'].'"><font color="#FFFFFF">' ; 
	}
	echo $matchVideo['Team2'] ;
	if (in_array($matchVideo['Team2'], $teams)){
		echo '</font></a>' ; 
	}
	echo '</font></b>'
	.'<br><font face="Verdana" style="font-size: 8pt;" color="#FFFFFF">'. $matchVideo['Month'].'/'.$matchVideo['Day'].'/'.$matchVideo['Year']
	.'</td></tr></table>'
	.'<table width="100%"><tr bgcolor="#F7F7F7">'
	.'<td height="30" valign="top" width ="100%" bgcolor="#F7F7F7">If you don\'t see what you are looking for, please check back a bit later.</td></tr></table>'
	.'<table width="100%" cellpadding="5">';



	$num = 0 ;  //subvideos shown in one tab, max 4
	$video = array() ; 
	if ($matchVideo['MUrl1'] != NULL){
		$video['Url'] = $matchVideo['MUrl1'] ; 
		$video['Thumbnail'] = $matchVideo['MThumbnail1'] ;
		$video['Description'] = $matchVideo['MDescription1'] ;
		$video['Title'] = $matchVideo['MTitle1'] ; 	
		$video['Duration'] = $matchVideo['MDuration1'] ; 	
		$video['PublishedTime'] = $matchVideo['MPublishedTime1'] ;
		echo printSubVideoFeed($video) ; 
		//print_r($video) ; 	
		$num++ ;
	}
	if ($matchVideo['MUrl2'] != NULL){
		$video['Url'] = $matchVideo['MUrl2'] ; 
		$video['Thumbnail'] = $matchVideo['MThumbnail2'] ;
		$video['Description'] = $matchVideo['MDescription2'] ;
		$video['Title'] = $matchVideo['MTitle2'] ; 	
		$video['Duration'] = $matchVideo['MDuration2'] ; 	
		$video['PublishedTime'] = $matchVideo['MPublishedTime2'] ;
		echo printSubVideoFeed($video) ; 
		//print_r($video) ;  	
		$num++ ;
	}
	if ($matchVideo['AUrl1'] != NULL){
		$video['Url'] = $matchVideo['AUrl1'] ;
		$video['Thumbnail'] = $matchVideo['AThumbnail1'] ; 
		$video['Description'] = $matchVideo['ADescription1'] ;
		$video['Title'] = $matchVideo['ATitle1'] ; 	
		$video['Duration'] = $matchVideo['ADuration1'] ; 	
		$video['PublishedTime'] = $matchVideo['APublishedTime1'] ; 
		echo printSubVideoFeed($video) ; 
		//print_r($video) ; 	
		$num++ ;
	}
	if ($matchVideo['AUrl2'] != NULL){
		$video['Url'] = $matchVideo['AUrl2'] ; 
		$video['Thumbnail'] = $matchVideo['AThumbnail2'] ;
		$video['Description'] = $matchVideo['ADescription2'] ;
		$video['Title'] = $matchVideo['ATitle2'] ; 	
		$video['Duration'] = $matchVideo['ADuration2'] ; 	
		$video['PublishedTime'] = $matchVideo['APublishedTime2'] ; 
		echo printSubVideoFeed($video) ; 
		//print_r($video) ; 	
		$num++ ;
	}
	if ($matchVideo['AUrl3'] != NULL && $num < 4){
		$video['Url'] = $matchVideo['AUrl3'] ;
		$video['Thumbnail'] = $matchVideo['AThumbnail3'] ; 
		$video['Description'] = $matchVideo['ADescription3'] ;
		$video['Title'] = $matchVideo['ATitle3'] ; 	
		$video['Duration'] = $matchVideo['ADuration3'] ; 	
		$video['PublishedTime'] = $matchVideo['APublishedTime3'] ; 
		echo printSubVideoFeed($video) ; 
		//print_r($video) ; 	
		$num++ ;
	}
	if ($matchVideo['AUrl4'] != NULL && $num < 4){
		$video['Url'] = $matchVideo['AUrl4'] ; 
		$video['Thumbnail'] = $matchVideo['AThumbnail4'] ;
		$video['Description'] = $matchVideo['ADescription4'] ;
		$video['Title'] = $matchVideo['ATitle4'] ; 	
		$video['Duration'] = $matchVideo['ADuration4'] ; 	
		$video['PublishedTime'] = $matchVideo['APublishedTime4'] ; 	
		echo printSubVideoFeed($video) ; 
		//print_r($video) ; 
		$num++ ;
	}

	echo '</table>' ;

}
?>


<?php
function printSubVideoFeed($video){
	if (substr($video['Thumbnail'], -3) == "swf"){
		return '<tr bgcolor="#F7F7F7">'
		.'<td width="0.5%" height="100" bgcolor="#F7F7F7"></td>'
		.'<td width="28%" align="center" valign="middle" height="100" bgcolor="#F7F7F7"><a href="'.$video['Url'].'" target="_blank"><fb:swf swfbgcolor="#F7F7F7" swfsrc="'.$video['Thumbnail'].'" height="100" width="125"/></a></td>'
		.'<td width="61%" valign="top" height="100" bgcolor="#F7F7F7"><b><a href="'.$video['Url'].'" target="_blank">'.$video['Title'].'</a><br />'.printDuration($video['Duration']).'</b>   '.printDay($video['PublishedTime'])
		.'<br />'. printDescription($video['Description']).'</td>' 
		.'<td width="0.5%" height="100" bgcolor="#F7F7F7"></td></tr>' ;
	
	} else {
		return '<tr bgcolor="#F7F7F7">'
		.'<td width="0.5%" height="100" bgcolor="#F7F7F7"></td>'
		.'<td width="28%" align="center" valign="middle" height="100" bgcolor="#F7F7F7"><a href="'.$video['Url'].'" target="_blank"><img src="'.$video['Thumbnail'].'" height="100" width="125"/></a></td>'
		.'<td width="61%" valign="top" height="100" bgcolor="#F7F7F7"><b><a href="'.$video['Url'].'" target="_blank">'.$video['Title'].'</a><br />'.printDuration($video['Duration']).'</b>   '.printDay($video['PublishedTime'])
		.'<br />'. printDescription($video['Description']).'</td>' 
		.'<td width="0.5%" height="100" bgcolor="#F7F7F7"></td></tr>' ; 
	}
}


function printDuration($duration){
	if ($duration < 0){
		return "" ;
	}
	$sec= $duration %60 ;
	$min = ($duration - $sec)/60 ;
	if($sec <10){
		return '<b>'.$min.':0'.$sec.'</b>' ;
	}else{
		return '<b>'.$min.':'.$sec.'</b>' ;
	}
}

function printDay($time){
	return 'Added '.$time ; 
}

function printDescription($description){
	if (strlen($description) <200){
		return $description ;
	} else {
		return substr($description, 0, 197).'...' ;
	}
}
?>


</td>
<td width="2" bgcolor= "#0000FF"> </td> 
<td  valign="top">
<?php
include ('frame.php') ;
?>
</td>
</tr>
</table>
