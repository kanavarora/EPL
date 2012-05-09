<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php') ;
require_once('header.php') ;
?>
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">


<?php
$profileId=$user ;
if (isset($_POST['Supporter'])){
	query('UPDATE Personal SET `Supporter` = "'.$_POST['Supporter'].
		'", `Reasons` = "'.$_POST['Reasons'].'", `Enemies` = "'.$_POST['Enemies'].
		'", `OtherTeams` = "'.$_POST['OtherTeams'].'", `FavoritePlayers` = "'.$_POST['FavoritePlayers'].
		'" WHERE `Id`='.$user) ;
	echo '<font face="Georgia" style="font-size: 10pt;">' ; 	
	echo '<br/><b><center>Your changes were saved!</center></b><br>'
		.'<center><b><a href="index.php">Go to your Home Page</a></b></center>' ;
	echo '</font>';	
$res = query('SELECT `Id`, `Supporter`, `Reasons`,`Enemies`,`OtherTeams`, `FavoritePlayers` FROM Personal WHERE `Id` = '.$user) ;
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
.'<td><div align="center"><img src="'.$baseUrl.'teamImages/'.rawurlencode($profile['Supporter']).'.gif" width="100" height="100"/></div></td></tr>';

$postfbml = 

'<tr>'
.'<td width="50%"><div align="center"><font color="#000000" style="font-size: 10pt;"><b>Supporter:<b></font>'
.'<font style="font-size: 10pt;">'
.'<b><fb:if value="'.$isSupporter.'"><a href="'.$fbBaseUrl.'teampage.php?team='.$profile['Supporter'].'"></fb:if>'.$profile['Supporter'].'<fb:if value="'.$isSupporter.'"></a></fb:if></b></div>'
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


$postlinks = '<center><a href="'.$fbBaseUrl.'profile.php?id='.$profileId.'">View <fb:name uid='.$profileId.' possessive="true" firstnameonly="true" linked="false"/> EPL profile</a>'
		.'<br><a href="'.$fbBaseUrl.'teampage.php?team='.$profile['Supporter'].'">View video highlights from '.$profile['Supporter'].'\'s recent matches</a>'
		.'<br><a href="'.$fbBaseUrl.'index.php">Go to EPL Fan Application</a></center>' ;

$fbml = $prefbml.$postfbml.$postlinks ; 

$profile_action = '<fb:profile-action url="http://apps.facebook.com/epllaunch/profile.php">View English Premier League Profile</fb:profile-action>';

$facebook->api_client->profile_setFBML($fbml, $user,'',$profile_action); 

$title_template = '{actor} has added the <a href="http://apps.facebook.com/epllaunch/">English Premier League</a> video highlights application';
$body_template = 'They are now a proud supporter of <a href="'.$fbBaseUrl.'teampage.php?team='.$profile['Supporter'].'">'.$profile['Supporter'].'</a>'
				.'. Check out their <a href="http://apps.facebook.com/epllaunch/profile.php?id='.$user.'">EPL Profile';

$image1 = $baseUrl.'teamImagesSmall/'.rawurlencode($profile['Supporter']).'.gif';
$image1_link = 'http://apps.facebook.com/epllaunch/teampage.php?team='.rawurlencode($profile['Supporter']);
$facebook->api_client->feed_publishTemplatizedAction($user, $title_template,"",$body_template,"","",$image1,$image1_link);


/*
// send a news feed to the user saying that they have updated their own profile
$heading = '<a href="www.apps.facebook.com/epllaunch">English Premier League</a>';
$story = '<fb:name uid='.$profileId.' possessive="true" linked="true"/> edited their EPL profile';
$facebook->api_client->feed_publishStoryToUser($heading,$story);



// publish action to friends


$message = '<fb:name uid="'.$user.'" linked="true"/> edited their EPL profile. They now support '.$profile['Supporter'];
$img1_src = $baseUrl.'teamImages/'.rawurlencode($profile['Supporter']).'.gif';
$img1_link = "www.apps.facebook.com/epllaunch";
$ret = $facebook->api_client->feed_publishActionOfUser($heading, $message,$img1_src,img1_link); 

//echo '<table><tr><td>'.$ret.' this was it </td></tr></table>'; 

*/

}

	

}
?>

<?php
$profileId= $user ;
$res = query('SELECT `Id`, `Supporter`, `Reasons`,`Enemies`,`OtherTeams`, `FavoritePlayers` FROM Personal WHERE `Id` = '.$profileId) ;
$profiles = array();
  while ($row = mysql_fetch_assoc($res)) {
    $profiles[] = $row;
  }
if (count($profiles)==1){
	$profile = $profiles[0] ;
} else {
	query('INSERT INTO Personal SET `Id` = '.$profileId) ; 
	$profile['Supporter'] = "" ;
	$profile['Reasons'] = "" ;
	$profile['Enemies'] = "" ;
	$profile['OtherTeams'] = "" ;
	$profile['FavoritePlayers'] = "" ;
}
?>

<fb:editor action="" labelwidth="100">
	<fb:editor-custom label="Supporter">
		<select name="Supporter">
			<?php
			foreach ($teams as $team){
			if ($team == $profile['Supporter']){
				echo '<option value="'.$team.'" selected>'.$team.'</option>' ;
			} else{
				echo '<option value="'.$team.'" >'.$team.'</option>' ;
			}
			}
			?>
		</select>
	</fb:editor-custom>
	<fb:editor-textarea label="Reasons for supporting" name="Reasons" rows="4"><?=$profile['Reasons']?></fb:editor-textarea>
	<fb:editor-text label="Favorite Enemies" name="Enemies" value="<?=$profile['Enemies']?>" maxlength="200"/>
	<fb:editor-text label="Other Teams supported" name="OtherTeams" value="<?=$profile['OtherTeams']?>" maxlength="200"/>
	<fb:editor-text label="Favorite Players" name="FavoritePlayers" value="<?=$profile['FavoritePlayers']?>" maxlength="200"/>
	<fb:editor-buttonset>
		<fb:editor-button value="Save Changes"/>
		<fb:editor-cancel value="Cancel Changes" href="edit.php"/>
	</fb:editor-buttonset>
</fb:editor>




</td>
<td width="2" bgcolor= "#0000FF"> </td> 
<td  valign="top">
<?php
include ('frame.php') ;
?>
</td>
</tr>
</table>
