<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">




<?php
$maxVideosOnPage = 2 ;
$pageToBeLoaded = false ; 
if(!isset($_GET['team'])){
	echo '<center>No team selected. Select one from the right hand side.</center>' ;
} else {
	$team = $_GET['team'] ;
	if (!in_array($team, $teams)){
		echo '<center>No team by this name. Select one from the right hand side.</center>' ;
	} else {
		$pageToBeLoaded = true ; 
	}
}
$pageNumber = 0 ; 

if (!isset($_GET['page'])){
	$pageNumber = 0 ;
} else{
	$pageNumber = $_GET['page'] ;
}
?>


<?php
if ($pageToBeLoaded){

	$res = query('SELECT `Id` FROM Personal WHERE `Supporter`="'.$team.'"') ;
	$supporters = array() ;
	while($row = mysql_fetch_assoc($res)){
		$supporters[] = $row;
	}
	$totalSupporters = count($supporters) ;
	
	$res = query('SELECT * FROM Matches WHERE (`Team1` = "'.$team.'" OR `Team2` = "'.$team.'") AND (
			(`Year` > '.date("Y").') OR
			(`Year` = '.date("Y").' AND `Month` > '.date("m").') OR 
			(`Day` > '.date("d").' AND `Month` = '.date("m").' AND `Year` = '.date("Y").') 
			)ORDER BY
			`Year` ASC, `Month` ASC,`Day` ASC') ;
	$upcomingMatches = array() ;
	while ($row=mysql_fetch_assoc($res)){
		$upcomingMatches[] = $row ;
	}
	
	echo '<table table border="5" bordercolor=#3b5998 width="100%" >'; 
	echo '<table width="100%" cellpadding="5" bgcolor="#F7F7F7">' ;
	echo '<tr>';
	echo '<td width="50%" align="center"><img src="'.$baseUrl.'teamImages/'.$team.'.gif" height="200" width="200"></td>' ;
	echo '<td width="40%"><div align="left"><font size=3 face="Verdana"><b>'.$team.'</b></font>' ;
	echo '<br/>Number of Supporters: '.$totalSupporters ;
	echo '<br/><br/><b>Upcoming Matches</b><br/>' ;
	$i = 0 ; 
	foreach ($upcomingMatches as $upcomingMatch){
		if ($i>2){
			break ; 
		}else {
			echo $upcomingMatch['Month'].'/'.$upcomingMatch['Day'].'/'.$upcomingMatch['Year'].' '.$upcomingMatch['Team1'].' Vs '.$upcomingMatch['Team2'].'<br/>' ; 
			$i++ ;
		}	
	}
	echo '</div></td>' ;
	echo '</tr></table></table><br/><br/>' ;

	$res = query ('SELECT * FROM MatchesVideos 
				WHERE 
				`Team1` = "'.$team.'" OR `Team2` = "'.$team.'"
				ORDER BY		
				`Year` DESC, `Month` DESC, `Day` DESC') ;
	$matchVideos = array() ;
	while($row = mysql_fetch_assoc($res)){
		$matchVideos[] = $row;
	}
	
	$totalVideos = count($matchVideos) ;
//	echo ($totalVideos) ;
	if ((! (($pageNumber * $maxVideosOnPage) < $totalVideos)) || $pageNumber < 0 ){
		$pageNumber = 0  ; 
	}


	$i = $maxVideosOnPage * $pageNumber ;
	$totalPages = (($totalVideos -1 - (($totalVideos-1) % $maxVideosOnPage))/$maxVideosOnPage)  + 1;
	for (; (($i< $totalVideos) && ($i< $maxVideosOnPage * ($pageNumber + 1))) ; $i++ ){
		$matchVideo = $matchVideos[$i] ;
		printVideoFeed($matchVideo) ;
	}
	

	// scroll between pages	
	echo '<table width="100%" cellpadding="1"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<font face="Verdana" style="font-size: 9pt;" color="#FFFFFF">' ;
	echo '<center>Page ' ; 
	echo $pageNumber + 1 . ' of '. $totalPages . '  ' 
	. '<a href="teampage.php?team='.$team.'&page=0"><font color="white"><u>First</u></font></a>  ' ;

	if ($pageNumber == 0){
		echo 'Previous  ' ;
	} else{
		$prevPage = $pageNumber -1 ;
		echo '<a href="teampage.php?team='.$team.'&page='.$prevPage.'"><font color="white"><u>Previous</u></font></a>  ' ;
	}
	$lastPage = $totalPages - 1;
	if ($pageNumber == $lastPage){
		echo 'Next  ' ;
	} else{
		$nextPage = $pageNumber +1 ;
		echo '<a href="teampage.php?team='.$team.'&page='.$nextPage.'"><font color="white"><u>Next</u></font></a>  ' ;
	}
	echo '<a href="teampage.php?team='.$team.'&page='.$lastPage.'"><font color="white"><u>Last</u></font></a>  </center>' ;
	echo '</font></td></tr></table><br/>' ;


}


?>


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


</td>
<td width="2" bgcolor= "#0000FF"> </td> 
<td  valign="top">
<?php
include ('frame.php') ;
?>
</td>
</tr>
</table>

