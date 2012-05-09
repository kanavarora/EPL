<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php');
?>





<?php
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">

<?php




echo '<table table border="5" bordercolor=#3b5998 width="100%" >'; 
echo '<table width="100%" cellpadding="5" bgcolor="#F7F7F7">' ;
echo '<tr>';	
echo '<td width="60%" align="left">' ;	
echo  '<br/>';	
echo '<font face="Georgia" style="font-size: 10pt;"><b> Hello <fb:name uid='.$user.' useyou="false" firstnameonly="true" linked = "false"/>!</b></font><br/><br/>' ;


$res = query('SELECT `Id`, `Supporter` FROM Personal WHERE `Id` = '.$user) ;
$profiles = array();
while ($row = mysql_fetch_assoc($res)) {
	$profiles[] = $row;
}



if ((count($profiles) == 1) && $profiles[0]['Supporter']!= NULL){ 
$profile = $profiles[0];

$res = query ('SELECT * FROM MatchesVideos 
				WHERE 
				`Team1` = "'.$profile['Supporter'].'" OR `Team2` = "'.$profile['Supporter'].'"
				ORDER BY		
				`Year` DESC, `Month` DESC, `Day` DESC') ;
$matchVideos = array() ;
while($row = mysql_fetch_assoc($res)){
	$matchVideos[] = $row;
}

$matchVideo = $matchVideos[0] ;




echo '<font face="Verdana" style="font-size: 10pt;">' ; 
echo '<a href="matchvideo.php?team1='.$matchVideo['Team1'].'&team2='.$matchVideo['Team2'].'&m='.$matchVideo['Month'].'&d='.$matchVideo['Day'].'&y='.$matchVideo['Year'].'">';
echo 'Watch the video highlights</a> from <a href="teampage.php?team='.$profile['Supporter'].'">'.$profile['Supporter'].'\'s</a> last match.<br/><br/>' ;
echo '<a href="invite.php">Invite your friends</a> to become <a href="teampage.php?team='.$profile['Supporter'].'">'.$profile['Supporter'].'</a> Supporters. See which teams your <a href="friends.php">friends</a> are currently supporting. <br/><br/>' ;
echo 'Also, check out video highlights from other teams. Click on the team name on the right menu.<br/><br/>' ; 
echo '</font>' ;
echo '</td>';

echo '<td width="40%"><div align="center"><img src="'.$baseUrl.'teamImages/'.$profile['Supporter'].'.gif" height="200" width="200"></td>' ;
echo '</td></tr>';
echo '</table></table>';
}

else{
	echo '<font face="Verdana" style="font-size: 9pt;">' ; 
	echo 'Our records indicate that you still have not created your EPL profile. You can do so by going to the <a href="edit.php">Edit Profile</a> page.' 
		. ' After creating a profile and selecting a team you support for in the English Premiere League, you could see pages customized accordingly. ' ;
	echo '</font>' ;
	echo '</td></tr>';
	echo '</table></table>';
	
}

echo '<font face="Verdana" style="font-size: 9pt;">' ; 
echo '<table border="5" bordercolor=#3b5998 width="100%" >'; 
echo '<table width="100%" cellpadding="5" bgcolor="#F7F7F7">' ;
printCommentsFeed() ;
printNewsFeed() ;  
echo '<br/></table></table>';
echo '</font>';




$rs = $facebook->api_client->fql_query("SELECT uid FROM user WHERE has_added_app=1 and uid IN (SELECT uid2 FROM friend WHERE uid1 = $user)");

$arFriends = ""; 

// Build an delimited list of users... 
if ($rs) 
	{ for ( $i = 0; $i < count($rs); $i++ ) 
		{ if ( $arFriends != "" ) 
			$arFriends .= ","; 
			$arFriends .= $rs[$i]["uid"]; 
		} 
	}

$invfbml = 'You have been invited to be an EPL Fan. <br/>' 
		. '<fb:name uid="'.$user.'" firstnameonly="true" shownetwork="false"/> thinks you are a true football fan. Join this application to catch your favorite teams in action and watch the video highlights of all the matches at one place.'
		. '<fb:req-choice url="'.$facebook->get_add_url().'" label="Do you like football?" />' ;

?>

<fb:request-form type="English Premiere League" action="index.php" content="<?=htmlentities($invfbml)?>" invite="true"> 
<fb:multi-friend-selector max="20" actiontext="Here are your friends who are still not English Premiere League Fans. Invite them here!" showborder="true" rows="5" exclude_ids="<?=$arFriends?>"/>
</fb:request-form>



<?php

function printVideoFeed($matchVideo){

	//title
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
	$totalComments =  count($comments) ; 
	echo '</table>' ;
	echo '<table width="100%"><tr bgcolor="#F7F7F7">'
		.'<td height="30"  width ="100%" bgcolor="#F7F7F7">'
		.'<a href="matchvideo.php?team1='.$matchVideo['Team1'].'&team2='.$matchVideo['Team2'].'&m='.$matchVideo['Month'].'&d='.$matchVideo['Day'].'&y='.$matchVideo['Year'].'">'
		.'<font face="Verdana" style="font-size: 9pt;" color="#3B5998"><b>Post/View Comments on this Match['.$totalComments.']</b></font></a>'
		.'</td></tr></table>' ;
	echo '<br/><br/>' ; 


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


<?php

function printNewsFeed(){
	$currentTime =  mktime(0,0,0,date("m"),date("d"),date("Y")) ; 
	$weekBeforeTime =  $currentTime - (10*24*3600) -100 ; 
	$res = query('SELECT `Team1`, `Team2`, `Year`, `Month`, `Day` FROM MatchesVideos ORDER BY `Year` DESC, `Month` DESC, `Day` DESC') ;
	$matches = array() ;
	while($row = mysql_fetch_assoc($res)){
		$matches[] = $row;
	}
	$maxNumberToBeShown = 10 ; 
	$i = 0 ;
	echo '<b><font size="2">Recent Matches</font></b><br/><br/>' ; 
	while($i<$maxNumberToBeShown){
		$matchVideo = $matches[$i] ; 
		$matchTime = mktime(0,0,0,$matchVideo['Month'],$matchVideo['Day'],$matchVideo['Year']) ;
		if ($matchTime < $weekBeforeTime){
			break ;
		}else{
			echo $matchVideo['Month'].'/'.$matchVideo['Day'].'/'.$matchVideo['Year'].': ' ;
			echo '<a href="matchvideo.php?team1='.$matchVideo['Team1'].'&team2='.$matchVideo['Team2'].'&m='.$matchVideo['Month'].'&d='.$matchVideo['Day'].'&y='.$matchVideo['Year'].'">' ; 
			echo $matchVideo['Team1'].' Vs '.$matchVideo['Team2'].'</a>' ; 
			echo '<br/>' ; 
			$i++ ;
		}
	}
	
	echo '<br/>' ;
	echo '<b><font size="2"><a href="classified.php">Recent Classified Videos</a></font></b><br/><br/>' ;
	$res = query('SELECT * FROM Classified ORDER BY `time` DESC') ; 
	$classified =  array() ; 
	while($row = mysql_fetch_assoc($res)){
		$classified[] = $row;
	}
	$maxNumberToBeShown = 3 ; 
	$i= 0 ;
	while($i < $maxNumberToBeShown){
		$video = $classified[$i] ;
		echo str_ireplace('-','/', substr($video['time'], 0 , 10)). ': ' ;
		echo '<b><a href="video.php?num='.$i.'">'.$video['Title'].'</a></b><br/>'  ;
		if (strlen($video['Description'])<100){
			echo $video['Description'] ; 
		}else{
			echo substr($video['Description'], 0, 97).'...' ;
		}
		if( $i != $maxNumberToBeShown -1) echo '<hr size="1"/>' ;
		$i++ ;
	}
		 
		  
}


function printCommentsFeed(){
	$currentTime =  mktime(0,0,0,date("m"),date("d"),date("Y")) ; 
	$weekBeforeTime =  $currentTime - (7*24*3600) -100 ; 
	$res = query('SELECT * FROM MatchesComments ORDER BY `Time` DESC') ;
	$comments = array() ;
	while($row = mysql_fetch_assoc($res)){
		$comments[] = $row;
	}
	$maxNumberToBeShown = 10 ; 
	$i = 0 ;
	echo '<font size="3"> <b>Recent Fan Activity</b></font><br/><br/>' ;
	while($i<$maxNumberToBeShown){
		$comment = $comments[$i] ;
		$commentTime = $comment['Time'] ; 
		if ($commentTime < $weekBeforeTime){
			break ;
		}else{
			//echo '<b><a href="profile.php?id='.$comment['From'].'"><fb:name uid="'.$comment['From'].'" linked="false" firstnameonly="true"/></a></b> said ' ;
			echo '<font face="Verdana" style="font-size: 10pt;"><b>"' ; 
			if (strlen($comment['Comment']) > 40){
				echo substr($comment['Comment'], 0, 37).'...' ;
			} else {
				echo $comment['Comment'] ; 
			}
			echo '"</b></font>' ;
			echo '  on ';
			echo '<a href="matchvideo.php?team1='.$comment['Team1'].'&team2='.$comment['Team2'].'&m='.$comment['Month'].'&d='.$comment['Day'].'&y='.$comment['Year'].'">' ;
			echo $comment['Team1'].' Vs '. $comment['Team2'].'</a>' ; 
			echo '<br/>' ; 
			$i++ ;
		}
	}
	echo '<br/>';
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



