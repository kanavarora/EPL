<?php
require_once 'facebook.php';

$appapikey = 'd304828ff5aa30e7cba0e182edc5d541';
$appsecret = '4080ba42974e1cc707e615ca8a273b8e';
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

//[todo: change the following url to your callback url]
$appcallbackurl = 'http://www.siascholarship.com/epl/';
$baseUrl = 'http://www.siascholarship.com/epl/'  ;
$fbBaseUrl = 'http://apps.facebook.com/epllaunch/' ;

//catch the exception that gets thrown if the cookie has an invalid session_key in it
try {
  if (!$facebook->api_client->users_isAppAdded()) {
    $facebook->redirect($facebook->get_add_url());
  }
} catch (Exception $ex) {
  //this will clear cookies for your app and redirect them to a login prompt
  $facebook->set_user(null, null);
  $facebook->redirect($appcallbackurl);
}

$teams = array(
	"Arsenal" ,
	"Aston Villa" ,
	"Birmingham" ,
	"Blackburn" ,
	"Bolton" ,
	"Chelsea" ,
	"Derby" ,
	"Everton" ,
	"Fulham" ,
	"Liverpool" ,
	"Man United" ,
	"Manchester City" ,
	"Middlesbrough" ,
	"Newcastle" ,
	"Portsmouth" ,
	"Reading" ,
	"Sunderland" ,
	"Tottenham" ,
	"West Ham" ,
	"Wigan"
	) ;

?>