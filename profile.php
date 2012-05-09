<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">

<?php
if (isset($_GET['id'])){
	$profileId = $_GET['id'] ;
} else {
	$profileId = $user ;
}
$res = query('SELECT `Id`, `Supporter`, `Reasons`,`Enemies`,`OtherTeams`, `FavoritePlayers` FROM Personal WHERE `Id` = '.$profileId) ;
$profiles = array();
  while ($row = mysql_fetch_assoc($res)) {
    $profiles[] = $row;
  }
if (count($profiles) ==1){  
	$profile = $profiles[0] ;
	$pageToBeLoaded = true ;
	} else {
	$pageToBeLoaded = false ;
	echo '<center> Our records indicate the user with id '.$profileId.' is not an EPL Fan and has not added this application</center>' ;
	}
	

if ($pageToBeLoaded){
/*if ($profileId== $user){
echo '<br/><center><font style="font-size: 10pt;"><b><a href="edit.php">Edit Your Profile</a></b></center><br/>' ;
}*/

$isSupporter = $profile['Supporter'] != NULL ; 
 
$prefbml = '<table width="100%" cellpadding="10">'
.'<tr>'
.'<td bgcolor="#3B5998" height="3" style="border: 2px solid #3b5998;">'
.'<b><font face="Verdana" style="font-size: 9pt;" color="#FFFFFF"><center>EPL PROFILE</center></font></b></td>'
.'</tr>'

.'<tr>'
.'<td bgcolor="#F7F7F7"  valign="top">'
.'<center>'

.'<table width="95%" cellpadding="2" bgcolor="#FFFFFF" frame="border" >'
.'<tr>'
.'<td width="50%" valign="top"><div align="center"><fb:profile-pic uid='.$profileId.' size =normal linked="true" /></div></td>'
.'<td width="50%"><div align="center"><img src="'.$baseUrl.'teamImages/'.$profile['Supporter'].'.gif" width="200" height="200"/></div></td></tr>';

$postfbml = //'<tr><td width="50%"><div align="center">'

'<tr><td width="50%"<div align="center"<font color="#000000" style="font-size: 10pt;"><b>Name:<b></font>'
.'<font style="font-size: 10pt;"> <b><fb:name useyou="false" uid='.$profileId.'/></b></font>'
.'</td>'

.'<td width="50%"><div align="center"><font color="#000000" style="font-size: 10pt;"><b>Supporter:<b></font>'
.'<font style="font-size: 10pt;">'
.'<b><fb:if value="'.$isSupporter.'"><a href="teampage.php?team='.$profile['Supporter'].'"></fb:if>'.$profile['Supporter'].'<fb:if value="'.$isSupporter.'"></a></fb:if></b></div>'
.'</font>'
.'</td>'

.'</tr>'

.'<tr>'
.'<td colspan="2" ><font color="#000000" style="font-size: 10pt;"><b>Reasons for supporting '.$profile['Supporter'].':<font></b><font color="#3B5998" style="font-size: 10pt;"> '.$profile['Reasons'].'</font></td>'
.'</tr>'
.'<tr>'
.'<td colspan="2"><font color="#000000" style="font-size: 10pt;"><b>Favorite Enemies: </b></font><font color="#3B5998" style="font-size: 10pt;">'.$profile['Enemies'].'</font></td>'
.'</tr>'
.'<tr>'
.'<td colspan="2"><font color="#000000" style="font-size: 10pt;"><b>OtherTeams supported:</b></font><font color="#3B5998" style="font-size: 10pt;"> '.$profile['OtherTeams'].'</font></td>'
.'</tr>'
.'<tr>'
.'<td colspan="2"><font color="#000000" style="font-size: 10pt;"><b>Favorite Player(s):</b></font><font color="#3B5998" style="font-size: 10pt;"> '.$profile['FavoritePlayers'].'</font></td>'
.'</tr>'
.'</table></center>'
.'</td></tr></table>' ;

$fbml = $prefbml.$postfbml ; 
echo $fbml ;
//$fbml = $prefbml .'<b><fb:name useyou="false" uid='.$profileId.' firstnameonly="true"/></b></td>' .$postfbml ; 
//$facebook->api_client->profile_setFBML($fbml, $user); 
}

if ($profileId== $user){
echo '<center><font style="font-size: 10pt;"><b><a href="edit.php">Edit Your Profile</a></b></center>' ;
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
