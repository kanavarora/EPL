#!/usr/local/bin/php -q
<?php
require_once ('searchLib.php') ;


$dbhost = 'mysql.siascholarship.com';
$dbuser = 'fb_test';
$dbpass = 'password';
$dbname = 'fb_test';

$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname, $conn);

function query($q) {
  global $conn;
  $result = mysql_query($q, $conn);
  if (!$result) {
    die("Invalid query -- $q -- " . mysql_error());
  }
  return $result;
}


if (isset($_GET['Team1']) && isset($_GET['Team2']) && isset($_GET['Day']) && isset($_GET['Month']) && isset($_GET['Year'])){
	$currentTime = mktime(0,0,0,date("m"),date("d"),date("Y")) + 50 ;
	$searchingTime = 3*24*3600 + 12*3600 + 200  ; //3.5 days, 100 to be on safe side
	$matchTime = mktime(0,0,0,$_GET['Month'],$_GET['Day'],$_GET['Year']) ;
	if ($matchTime < $currentTime){
		if ($currentTime > $matchTime + $searchingTime){
			break ;
		}
		$autoFilteredFeeds = search($_GET['Team1'],$_GET['Team2'], $matchTime -100) ;
		if (count($autoFilteredFeeds)>0){
			storeInDatabase($autoFilteredFeeds, $_GET) ;
		}
	}
} else {

$res = query('SELECT `Team1`, `Team2`, `Year`, `Month`, `Day` FROM Matches ORDER BY `Year` DESC, `Month` DESC, `Day` DESC') ;
$matches = array() ;
while($row = mysql_fetch_assoc($res)){
	$matches[] = $row;
}
$currentTime = mktime(0,0,0,date("m"),date("d"),date("Y")) + 50 ;
$searchingTime = 4*24*3600 + 200  ; //4 days, 100 to be on safe side
foreach ($matches as $match){
	$matchTime = mktime(0,0,0,$match['Month'],$match['Day'],$match['Year']) ;
	if ($matchTime < $currentTime){
		if ($currentTime > $matchTime + $searchingTime){
			break ;
		}
		$autoFilteredFeeds = search($match['Team1'],$match['Team2'], $matchTime -100) ;
		if (count($autoFilteredFeeds)>0){
			storeInDatabase($autoFilteredFeeds, $match) ;
		}
	}
}
}

function storeInDatabase($autoFilteredFeeds, $match){
	$res = query('SELECT `Team1`, `Team2`, `Year`, `Month`, `Day` FROM MatchesVideos 
				WHERE 
					`Team1`="'.$match['Team1'].'" AND
					`Team2`="'.$match['Team2'].'" AND
					`Year`='.$match['Year'].' AND
					`Month`='.$match['Month'].' AND
					`Day`='.$match['Day']) ;
	$matchVideos = array() ;
	while($row = mysql_fetch_assoc($res)){
		$matchVideos[] = $row ; 
	}
	if (count($matchVideos) > 1){
		echo "There is more than one video by the same teams and date\n" ;
		print_r($match) ;
	} else{
		if (count($matchVideos) == 0){
			query('INSERT INTO MatchesVideos (`Team1`, `Team2`, `Year`, `Month`, `Day`)
				VALUES   ("'.$match['Team1'].'", "'.$match['Team2'].'", '.$match['Year'].', '.$match['Month'].', '.$match['Day'].')') ; 	
		}
		
		$num =1 ;
		foreach ($autoFilteredFeeds as $feed){
			query('UPDATE MatchesVideos 
				SET 
					`AUrl'.$num.'` = "'.addslashes($feed['url']).'", 
					`ATitle'.$num.'` = "'.addslashes($feed['title']).'",
					`ADescription'.$num.'` = "'.addslashes($feed['description']).'",
					`AThumbnail'.$num.'` = "'.addslashes($feed['thumbnail']).'", 
					`ADuration'.$num.'` = '.addslashes($feed['duration']).', 
					`APublishedTime'.$num.'` = "'.$feed['publishedTime'].'",
					`LastUpdatedTime` = '.time() .'
				WHERE
					`Team1`="'.$match['Team1'].'" AND
					`Team2`="'.$match['Team2'].'" AND
					`Year`='.$match['Year'].' AND
					`Month`='.$match['Month'].' AND
					`Day`='.$match['Day']) ;

			$num++ ;
		}
				
	}
			
}

	
	
?>