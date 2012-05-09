<?php
$maxVideosToBeSearched = "100" ; 
$variantTeamArray = array(
			"Arsenal" => array("Arsenal"), 
			"Aston Villa" => array("Aston", "Villa"),
			"Birmingham" => array("Birmingham") ,
			"Blackburn" => array("Blackburn", "Rovers") ,
			"Bolton" => array("Bolton", "Wanderers") ,
			"Chelsea" => array("Chelsea") ,
			"Derby" => array("Derby") ,
			"Everton" => array("Everton") ,
			"Fulham" => array("Fulham") ,
			"Liverpool" => array("Liverpool") ,
			"Man United" => array("Manchester", "Man United", "ManU", "Man Utd") ,
			"Manchester City" => array("Manchester City", "Man City") ,
			"Middlesbrough" => array("Middlesbrough") ,
			"Newcastle" => array("Newcastle") ,
			"Portsmouth" => array("Portsmouth") ,
			"Reading" => array("Reading") ,
			"Sunderland" => array("Sunderland") ,
			"Tottenham" => array("Tottenham", "Hotspurs", "Spurs") ,
			"West Ham" => array("West Ham", "Ham United", "Ham Utd", "WestHam") ,
			"Wigan" => array("Wigan", "Athletic") 
		) ;

$monthToInt = array(
		"Jan" => 1 ,
		"Feb" => 2 , 
		"Mar" => 3 ,
		"Apr" => 4 ,
		"May" => 5 ,
		"Jun" => 6 ,
		"Jul" => 7 ,
		"Aug" => 8 ,
		"Sep" => 9 ,
		"Oct" => 10 ,
		"Nov" => 11 ,
		"Dec" => 12 
		) ;

$spammers = array("http://", "www." , "camera", "ryanflemingo@googlemail.com") ; 
$essentials = array("MOTD", "Match of the Day", "highlights") ; 
$spammingAuthors = array("RIPsupporter0");

function search($team1, $team2, $timeOfMatch){
	global $essentials ; 
	$YouTube_array = searchYouTube($team1, $team2, $timeOfMatch) ; 
	$BlinkX_array = searchBlinkX($team1, $team2, $timeOfMatch) ; 
	$Truveo_array = searchTruveo($team1, $team2, $timeOfMatch) ; 
	$YouTube_filtered_array = filterSearchYouTube($YouTube_array, $team1, $team2, $timeOfMatch) ; 
	$Truveo_filtered_array = filterSearchTruveo($Truveo_array, $team1, $team2, $timeOfMatch) ; 
	$BlinkX_filtered_array = filterSearchBlinkX($BlinkX_array, $team1, $team2, $timeOfMatch) ;
	$search_array = array_merge($BlinkX_filtered_array, $Truveo_filtered_array, $YouTube_filtered_array) ; 
	
	$autoFilteredFeeds = array() ; 
	$essential_array = array() ; 
	foreach($search_array as $itemRSS){
		if (descriptionContains($itemRSS, $essentials)){
			array_push($essential_array , $itemRSS) ;
		}else{
			array_push($autoFilteredFeeds, $itemRSS) ;
		}
	}
	$FilteredFeeds = array_merge($essential_array,  $autoFilteredFeeds) ; 

	$videosSelected = 1 ;
	$finalVideos = array() ; 
	foreach ($FilteredFeeds as $itemRSS){
		if ($videosSelected > 4){
			break ;
		}
		$videoToBeAdded = true ;
		foreach ($finalVideos as $video){
			if ($video['title']== $itemRSS['title'] ){
				$videoToBeAdded = false ;
				break ;
			}  
		}
		if ($videoToBeAdded){
			array_push($finalVideos , $itemRSS) ;
			$videosSelected++ ; 
		}
	}
	return $finalVideos ; 
}



function searchTruveo($team1, $team2, $timeOfMatch){
	$preUrl = "http://xml.truveo.com/rss?query=" ;
	$searchUrl = urlencode($team1." ".$team2) ;
	$postUrl = "%20sort%3AmostRecent&results=50" ;
	$url = $preUrl.$searchUrl.$postUrl ;
	echo $url . "\n" ;
	$doc = new DOMDocument() ;
	$doc->preserveWhiteSpace = false ;
	//this is an alternative for file_get_contents because external url file access is disabled in Dreamhost
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	$doc->loadXML($file_contents) ;
	$arrFeeds = array() ;
	foreach ($doc->getElementsByTagName('item') as $node) {
		$itemRSS = array() ;
		//echo "X" ; 
		foreach($node->childNodes as $child){
			if ($child->nodeName=='pubDate'){
				$itemRSS['publishedTime'] = $child->nodeValue ;
			} elseif ($child->nodeName == 'source') {
				$itemRSS['channel'] = $child->nodeValue ; 
			} elseif ($child->nodeName == 'title') {
				$itemRSS['title'] = $child->nodeValue ; 
			} elseif ($child->nodeName == 'link') {
				$itemRSS['url'] = $child->nodeValue ; 
			} elseif ($child->nodeName == 'description') {
				$itemRSS['description'] = removePictureFromTruveoDescription($child->nodeValue) ; 
			} elseif($child->nodeName =='media:content'){
				$itemRSS['duration'] = $child->attributes->getNamedItem('duration')->value ;
				if (!isset($itemRSS['duration'])){
					$itemRSS['duration'] = -1 ;
				}
			} elseif ($child->nodeName == 'media:thumbnail') {
				$itemRSS['thumbnail'] = $child->attributes->getNamedItem('url')->value ; 
			}
		}
		if ((stripos($itemRSS['channel'],'YouTube') === false) && (stripos($itemRSS['channel'],'GoogleVideo') === false) && (stripos($itemRSS['channel'], 'MSN') === false) ){
			array_push($arrFeeds, $itemRSS) ;
			//print_r($itemRSS);
		}
	}
	
	//echo count($arrFeeds) ; 
	return $arrFeeds ;
}

function removePictureFromTruveoDescription($description){
	$leftBracket = stripos($description, "<") ;
	//echo $leftBracket ; 
	if ($leftBracket === false){
		return $description ;
	} else {
		$rightBracket = stripos($description, ">") ; 
		$description = substr_replace($description, "", $leftBracket, $rightBracket + 1 - $leftBracket) ;  
		return removePictureFromTruveoDescription($description) ; 
	}	
}

function filterSearchTruveo($arrFeeds, $team1, $team2, $timeOfMatch){
	global $spammers, $essentials ; 
	$filteredFeedsByTime = array() ;
	//echo $timeOfMatch . "\n";
	foreach($arrFeeds as $itemRSS){
		$timePublished = convertToTimestamp($itemRSS['publishedTime']) ;
		//echo $timePublished ."\n"  ;
		if ($timePublished > $timeOfMatch){
			array_push($filteredFeedsByTime, $itemRSS) ;
		}
	}

	//usort($filteredFeedsByTime, "compareFeedsByDuration") ; 
	
	// now checking for each video whether the both the keywords or the variants of them are present in the title
	$FilteredFeeds = array() ;
	//$lastDuration = 0 ; 
	//echo count($filteredFeedsByTime) ; 
	foreach($filteredFeedsByTime as $itemRSS){
		$currentTitle = $itemRSS['title'] ;
		if (titleContains($currentTitle, $team1)  && titleContains($currentTitle, $team2) ){
			array_push($FilteredFeeds , $itemRSS) ;  
		}
	}
	//echo count($FilteredFeeds) ; 
	return $FilteredFeeds ; 
	/*
	$autoFilteredFeeds = array() ; 
	$essential_array = array() ; 
	foreach($FilteredFeeds as $itemRSS){
		if (descriptionContains($itemRSS, $essentials)){
			array_push($essential_array , $itemRSS) ;
		}else{
			array_push($autoFilteredFeeds, $itemRSS) ;
		}
	}
	return array_merge($essential_array,  $autoFilteredFeeds) ; 
*/		
}


function searchYouTube($team1, $team2, $timeOfMatch){
	global $maxVideosToBeSearched, $spammingAuthors ;
	$preUrl = "http://video.google.com/videosearch?q=" ;
	$searchUrl = urlencode($team1." ".$team2) ;
	$postUrl = "&num=". $maxVideosToBeSearched ."&so=1&output=rss" ;
	$url = $preUrl.$searchUrl.$postUrl ;
	echo $url . "\n" ;
	$doc = new DOMDocument() ;
	$doc->preserveWhiteSpace = false ;
	//this is an alternative for file_get_contents because external url file access is disabled in Dreamhost
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	$doc->loadXML($file_contents) ;
	$arrFeeds = array() ;
	foreach ($doc->getElementsByTagName('item') as $node) {
		$itemRSS = array() ;
		foreach($node->childNodes as $child){
			if ($child->nodeName=='pubDate'){
				$itemRSS['publishedTime'] = $child->nodeValue ;
			} elseif ($child->nodeName=='author'){
				$itemRSS['author'] = $child->nodeValue ;
			}elseif($child->nodeName=='media:group'){
				$mediaChildren = $child->childNodes ;
				foreach ($mediaChildren as $mediaChild){
					if ($mediaChild->nodeName=='media:content'){
						$mediaContentAttributes = $mediaChild->attributes ;
						if ($mediaContentAttributes->getNamedItem('type')->value ==  "application/x-shockwave-flash"){
							$itemRSS['type'] = $mediaContentAttributes->getNamedItem('type')->value;
							$itemRSS['url'] = $mediaContentAttributes->getNamedItem('url')->value;
							$itemRSS['duration'] = $mediaContentAttributes->getNamedItem('duration')->value;
						}
					}elseif ($mediaChild->nodeName=='media:title'){
						$itemRSS['title'] = $mediaChild->nodeValue;
					}elseif ($mediaChild->nodeName=='media:description'){
						$itemRSS['description'] = $mediaChild->nodeValue;
					}elseif ($mediaChild->nodeName=='media:thumbnail'){
						$itemRSS['thumbnail'] = $mediaChild->attributes->getNamedItem('url')->value ;
					}
				}
			}
		
		}
		if ($itemRSS['type'] == "application/x-shockwave-flash"){
			$videoToBeAdded = true ; 
			foreach ($spammingAuthors as $author){
				if ($itemRSS['author'] == $author){
					$videoToBeAdded = false ;
					break ; 
				}
			}
			if ($videoToBeAdded){
				array_push($arrFeeds, $itemRSS) ;
			}
		}
	}
	//echo count($arrFeeds) ; 
	return $arrFeeds ;
}


function compareFeedsByDuration($a, $b){
	if ($a['duration']==$b['duration']){
		return 0 ;
	} else{
		return (($a['duration']>$b['duration']) ? -1 : 1) ; 
	}
}



function filterSearchYouTube($arrFeeds, $team1, $team2, $timeOfMatch){
	global $spammers, $essentials ; 
	$filteredFeedsByTime = array() ;
	//echo $timeOfMatch . "\n";
	foreach($arrFeeds as $itemRSS){
		$timePublished = convertToTimestamp($itemRSS['publishedTime']) ;
		//echo $timePublished ."\n"  ;
		if ($timePublished > $timeOfMatch){
			array_push($filteredFeedsByTime, $itemRSS) ;
		}
	}
	//echo count($filteredFeedsByTime) ;
	// sorting by duration of video
	usort($filteredFeedsByTime, "compareFeedsByDuration") ; 
	
	// now checking for each video whether the both the keywords or the variants of them are present in the title
	$FilteredFeeds = array() ;
	$lastDuration = 0 ; 
	foreach($filteredFeedsByTime as $itemRSS){
		$currentTitle = $itemRSS['title'] ;
		if (titleContains($currentTitle, $team1) && (!descriptionContains($itemRSS, $spammers)) && titleContains($currentTitle, $team2) && $itemRSS['duration'] != $lastDuration){
			//printVideoFeed($itemRSS) ;
			$lastDuration = $itemRSS['duration'] ; 
			array_push($FilteredFeeds , $itemRSS) ; 
		}
	}
	return $FilteredFeeds ; 
/*	
	$autoFilteredFeeds = array() ; 
	$essential_array = array() ; 
	foreach($FilteredFeeds as $itemRSS){
		if (descriptionContains($itemRSS, $essentials)){
			array_push($essential_array , $itemRSS) ;
		}else{
			array_push($autoFilteredFeeds, $itemRSS) ;
		}
	}
	return array_merge($essential_array,  $autoFilteredFeeds) ; 		
*/
}

function printVideoFeed($itemRSS){
	echo $itemRSS['title']."\n" ;
	echo $itemRSS['description']."\n" ;
	echo $itemRSS['type']."\n" ;
	echo $itemRSS['url']."\n" ;
	echo $itemRSS['thumbnail']."\n" ;
	echo $itemRSS['duration'] . '   ' . $itemRSS['publishedTime'] ;
	echo "\n\n\n" ;
}


// google published time is of the form Sun, 13 Jan 2008 08:33:35 PST
function convertToTimestamp($googleTime){
	global $monthToInt ;
	$indexOfComma = stripos($googleTime, ",") ;
	$startOfDate = $indexOfComma + 2 ;
	$indexOfSpaceBeforeMonth = stripos($googleTime, " " , $startOfDate) ;
	$d = substr($googleTime, $startOfDate, $indexOfSpaceBeforeMonth - $startOfDate) ;
	$indexOfSpaceBeforeYear = stripos($googleTime, " ", $indexOfSpaceBeforeMonth + 1) ;
	$m = substr($googleTime, $indexOfSpaceBeforeMonth + 1, $indexOfSpaceBeforeYear - $indexOfSpaceBeforeMonth - 1) ;
	$y = substr($googleTime, $indexOfSpaceBeforeYear + 1, 4) ; 
	//echo "$d, $y, $m\n" ; 
	$unixTime = mktime(0,0,0, $monthToInt[$m] , $d, $y) ;
	return $unixTime ; 

}


function titleContains($title, $team){
	global $variantTeamArray ;
	if(array_key_exists($team, $variantTeamArray)){
		$variants = $variantTeamArray[$team] ;
		//print_r($variants) ;
		foreach ($variants as $variant){
			if(stripos($title, $variant) !== false){
				 return true  ;
			}
		}
		return false ;
	} else {
		return stripos($title, $team) ;
	}
}

function descriptionContains($itemRSS, $spammers){
	foreach ($spammers as $spammer){
		if (stripos($itemRSS['description'], $spammer) !== false || stripos($itemRSS['title'], $spammer) !== false ){
			return true ;
		}
	}
	return false ; 
}
		
//search("Man United", "Newcastle", mktime(0,0,0,1,11,2008));



function searchBlinkX($team1, $team2, $timeOfMatch){

	$preUrl = "http://www.blinkx.com/rss?apicall=action%3Dquery%26databasematch%3Dmedia%26totalresults%3Dtrue%26clientip%3D59.94.252.84%26text%3D" ;
	$searchUrl = urlencode($team1." ".$team2) ;
	$postUrl = "%26start%3D1%26maxresults%3D100%26sortby%3Ddate%26fieldtext%3D%26printfields%3Dsummary_link_text%2Csummary_link_href_field%2Cplaylistpath%2Cmedia_source_url%2Ccategory%2Cmedia_bitrate%2Cuse_lightning_cast%2Cuse_adult_full_video_adverts%2Cadditional_info%2Cmedia_duration%2Cdrecontent%2Ctranscription_ctm%2Csection_start%2Clink%2Cmedia_type%2Cdefault_hit_image_location%2Cexternal_player_url%2Cnum_dpflvs%2Ctitle%2Csource_page_url%2Csummary%2Cowner_id%2Cdefault_wide_image_location%2Cwide_image_link%2Cdefault_footer_image_location%2Cchannel%2Cmedia_publish_date%2Cid%2Cmedia_location%2Cnum_swfs%2Caverage_vote%2Cnum_views%2Cnum_preflvs%2Cnum_hdflvs%2Csafe_flag%2Cdisplay_name%2Cnum_comments%26newsresults%3Dtrue%26sportsresults%3Dtrue%26characters%3D10000&query=".$searchUrl ;
	$url = $preUrl.str_ireplace(" ", "%2B", $team1).'%2B'.str_ireplace(" ", "%2B", $team2).$postUrl ;
	echo $url . "\n" ;
	$doc = new DOMDocument() ;
	$doc->preserveWhiteSpace = false ;
	//this is an alternative for file_get_contents because external url file access is disabled in Dreamhost
	$ch = curl_init();
	$timeout = 10; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	$doc->loadXML($file_contents) ;
	$arrFeeds = array() ;
	foreach ($doc->getElementsByTagName('item') as $node) {
		$itemRSS = array() ; 
		foreach($node->childNodes as $child){
			if ($child->nodeName == 'title'){
				$itemRSS['title'] = $child->nodeValue ; 
			} elseif ($child->nodeName == 'guid'){ 
				$itemRSS['url'] = $child->nodeValue ;
			} elseif ($child->nodeName == 'description'){
				$itemRSS['description'] = $child->nodeValue ;
			} elseif ($child->nodeName == 'pubDate'){
				$itemRSS['publishedTime'] = $child->nodeValue ; 
			} elseif ($child->nodeName == 'blinkx:channel'){
				$itemRSS['channel'] = $child->nodeValue ;
			} elseif ($child->nodeName == 'blinkx:thumbnail'){
				$itemRSS['thumbnail'] = $child->nodeValue ; 
			}
			$itemRSS['duration'] = -1 ; 
		}
		if ($itemRSS['channel'] !='YouTube' && $itemRSS['channel'] != 'GoogleVideo' && $itemRSS['channel'] != 'Stage6'){
			array_push($arrFeeds, $itemRSS) ;
		}
	}
	return filterSearchBlinkX($arrFeeds, $team1, $team2, $timeOfMatch) ; 
}

function filterSearchBlinkX($arrFeeds, $team1, $team2, $timeOfMatch){
	global $spammers ; 
	$filteredFeedsByTime = array() ;
	//echo $timeOfMatch . "\n";
	foreach($arrFeeds as $itemRSS){
		$timePublished = convertToTimestamp($itemRSS['publishedTime']) ;
		//echo $timePublished ."\n"  ;
		if ($timePublished > $timeOfMatch){
			array_push($filteredFeedsByTime, $itemRSS) ;
		}
	}
	//echo count($filteredFeedsByTime) ;
	
	// now checking for each video whether the both the keywords or the variants of them are present in the title
	$FilteredFeeds = array() ;
	foreach($filteredFeedsByTime as $itemRSS){
		$currentTitle = $itemRSS['title'] ;
		if (titleContains($currentTitle, $team1) && (!descriptionContains($itemRSS, $spammers)) && titleContains($currentTitle, $team2) ){
			//printVideoFeed($itemRSS) ; 
			array_push($FilteredFeeds , $itemRSS) ; 
		}
	}
	return $FilteredFeeds ; 		
}

?>