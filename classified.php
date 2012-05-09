<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe> 

<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
$maxVideosOnPage =4 ;
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
$pageNumber = 0 ;
if (!isset($_GET['index'])){
	$pageNumber = 0 ;
} else{
	$pageNumber = $_GET['index'] ;
}

$totalVideos = count($videos) ;
if ((! (($pageNumber * $maxVideosOnPage) < $totalVideos)) || ($pageNumber < 0)){
	$pageNumber = 0  ; 
}



echo '<table table border="5" bordercolor=#3b5998 width="100%" >'; 
echo '<table width="100%" cellpadding="5" bgcolor="#F7F7F7">' ;
echo '<tr><td>';	
	
echo '<font face="Verdana" style="font-size: 10pt;">Here you can view some football videos specially which do not necessarily limit themselves to EPL. If you are a true football fan, you will just love these.</font></br/><br/>' ;

echo'</td></tr></table></table>';
$i = $maxVideosOnPage * $pageNumber ;
$totalPages = (($totalVideos - 1 - (($totalVideos-1) % $maxVideosOnPage))/$maxVideosOnPage)  + 1;
for (; (($i< $totalVideos) && ($i< $maxVideosOnPage * ($pageNumber + 1))) ; $i++ ){
	$post =  $videos[$i] ;
	$res = query('SELECT `From`, `Comment`, `Video`,`Time` FROM Comments WHERE `Video` = "'.$post['Video'].'" ORDER BY `time` DESC') ;
	$comments = array();
  	while ($row = mysql_fetch_assoc($res)) {
    		$comments[] = $row;
  	}
	$totalComments = count($comments) ; 
	
	echo '<table width="100%" cellpadding="5"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<b><a href="video.php?num='.$i.'"><font face="Verdana" style="font-size: 9pt;" color="white">'.$post['Title'].'</font></a></b></td>'
	.'</tr></table><table width="100%" cellpadding="10"><tr>'
	.'<td bgcolor="#F7F7F7"  valign="top"><font size="2">'.$post['Description'].'</font></td></tr><tr>'
	.'<td bgcolor = "#F7F7F7" valign="top">'
		. '<center><fb:swf swfbgcolor="000000" swfsrc="'.$post['Video'].'" height="350" width="441" /></center>'
		. '<a href="video.php?num='.$i.'"><b>Post/View Comments['.$totalComments.']</b></a>'
		. '<div align="right">Posted at: '.$post['time'].'</div><br /><br /></td></tr></table>' ;

}
echo '<table width="100%" cellpadding="1"><tr>'
	.'<td bgcolor="#3B5998" height="5" style="border: 1px solid #3b5998;">'
	.'<font face="Verdana" style="font-size: 9pt;" color="#FFFFFF">' ;
echo '<center>Page ' ; 
echo $pageNumber + 1 . ' of '. $totalPages . '  ' 
	. '<a href="classified.php?index=0"><font color="white"><u>First</u></font></a>  ' ;

if ($pageNumber == 0){
	echo 'Previous  ' ;
} else{
	$prevPage = $pageNumber -1 ;
	echo '<a href="classified.php?index='.$prevPage.'"><font color="white"><u>Previous</u></font></a>  ' ;
}
$lastPage = $totalPages - 1;
if ($pageNumber == $lastPage){
	echo 'Next  ' ;
} else{
	$nextPage = $pageNumber +1 ;
	echo '<a href="classified.php?index='.$nextPage.'"><font color="white"><u>Next</u></font></a>  ' ;
}
echo '<a href="classified.php?index='.$lastPage.'"><font color="white"><u>Last</u></font></a>  </center>' ;
echo '</font></td></tr></table>' ;

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

