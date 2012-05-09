<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once('dbappinclude.php');
?>

<?php
require_once('header.php') ;
?>
<table table border="5" bordercolor=#3b5998 width="100%" > 
<table border="0" width="100%" cellpadding="0">
<tr>
<td width="85%" valign="top">


<?php
$rs = $facebook->api_client->fql_query("SELECT uid FROM user WHERE has_added_app=1 and uid IN (SELECT uid2 FROM friend WHERE uid1 = $user)");

$friendsTeams=  array() ;
foreach ($teams as $team){
	$friendsTeams[$team] = array() ;
}

$arFriends = ""; 

// Build an delimited list of users... 
if ($rs) 
	{ for ( $i = 0; $i < count($rs); $i++ ) 
		{ if ( $arFriends != "" ) 
			$arFriends .= ","; 
			$arFriends .= $rs[$i]["uid"]; 
		} 
	}

if($arFriends != "") 
{	

	$res = query('SELECT `Id`, `Supporter` from Personal WHERE `Id` in ('.$arFriends.')') ;
	
	$friendsDetails = array() ;
	while($row = mysql_fetch_assoc($res)){
		$friendsDetails[] = $row;
	}
	
	foreach ($friendsDetails as $friendDetails){
		if ($friendDetails['Supporter'] != NULL){
			array_push($friendsTeams[$friendDetails['Supporter']], $friendDetails['Id']) ;
		}
	}
	
	echo ' <font face="Verdana" style=" font-size: 10pt;"> ';
	echo '<br/>Here are your friends who have added this application. To invite more friends';
	echo ' <b><a href="invite.php">click here</a></b> </br></br> ';
	echo '</font>';
 
	$numberInRow = 5 ; 
	$widthPercentage =  20 ; 
	foreach ($teams as $team){
		if (count($friendsTeams[$team]) != 0){
			echo '<table border="0" width = "100%" cellpadding="2" bgcolor="#FFFFFF" frame="border" ><tr>' ;
			echo '<font style=" font-size: 12pt;">';
			echo '<b><a href="teampage.php?team='.$team.'">'.$team.'</a></b></font></tr>' ;
			$i = 0 ;  
			foreach ($friendsTeams[$team] as $uid){
				if (($i % $numberInRow) == 0 ){
					echo '<tr>'  ;
				} 
				echo '<td align="center" width="'.$widthPercentage.'%"><center>'  ;
				echo '<a href= "profile.php?id='.$uid.'">' ;
				echo '<fb:profile-pic size="square"  uid="'.$uid.'" linked="false" /><br/>' ;
				echo '<fb:name uid="'.$uid.'" linked="false" firstnameonly= "true"/>';
				echo '</a></center></td>' ;
				if (($i % $numberInRow) == ($numberInRow-1)){
					echo '</tr>' ;
				}
				
				$i++  ;
			}
			while(  ($i % $numberInRow) !=0)
			{
				echo '<td align="center" width="'.$widthPercentage.'%"> </td>';
				$i++  ;
			}
			echo '</table>' ; 
			echo'<br/>';
		}
	}
}
else
{
	echo ' <font face="Verdana" style=" font-size: 10pt;"> ';
	echo '<br/>None of your friends have added the EPL application.';
	echo ' <b><a href="invite.php">Invite</a></b> them now!!</br></br> ';
	echo '</font>';
	
		
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
</table>




