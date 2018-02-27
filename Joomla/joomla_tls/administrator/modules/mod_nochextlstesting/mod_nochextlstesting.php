<?php
/*** Nochex TLS Testing ***/
// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
 
$TLS = $params->get('TLS');

if($TLS == 0){

$url = "https://tlstest.nochex.com/";

$ch = curl_init ();
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($ch, CURLOPT_FAILONERROR, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec ($ch);
 
if($output == "NOCHEX_Connection_OK"){
	echo "<p style=\"margin:15px;padding:15px;font-weight:bold;border-color: #d6e9c6;color: #3c763d;background-color: #dff0d8;\">Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration.</p>";
}else{

	$ctx = stream_context_create(['ssl' => [
		'capture_session_meta' => TRUE
	]]);
	 
	$html = file_get_contents('https://tlstest.nochex.com/', FALSE, $ctx);
	$meta = stream_context_get_options($ctx)['ssl']['session_meta'];

	echo "<p style=\"margin:15px;padding:15px;font-weight:bold;border-color: #f2dede;color: #a94442;background-color: #ebccd1;\">OOPs something went wrong</p> ".curl_error($ch)."<br /><br />";  
	
}

curl_close ($ch);

}


require JModuleHelper::getLayoutPath('mod_nochextlstesting');