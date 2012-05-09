<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>
<?php
require_once 'dbappinclude.php' ;
require_once 'header.php' ;
// Get list of friends who have this app installed... 
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
		. '<fb:name uid="'.$user.'" firstnameonly="true" shownetwork="false"/> thinks you are a true English Premeire League fan. Join this application to support your favorite team and watch video highlights of all the football matches.'
		. '<fb:req-choice url="'.$facebook->get_add_url().'" label="DO you like football?" />' ;

?>
<fb:request-form type="English Premiere League" action="index.php" content="<?=htmlentities($invfbml)?>" invite="true"> 
<fb:multi-friend-selector max="20" actiontext="Here are your friends who are still not English Premiere League Fans. Invite them here!" showborder="true" rows="5" exclude_ids="<?=$arFriends?>"/>
</fb:request-form>
<fb:iframe src="http://ads.socialmedia.com/facebook/monetize.php?width=645&height=60&pubid=65b9db3f78ed23142d551b3f12f7f01b" border="0" width="645" height="60" scrolling="no" frameborder="0"></fb:iframe>