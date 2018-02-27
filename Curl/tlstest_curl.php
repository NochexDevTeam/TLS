<?php

$url = "https://tlstest.nochex.com/";

$ch = curl_init ();
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
/* curl_setopt ($ch, CURLOPT_SSLVERSION,6);
(0,1,6 ** Works) (2,3,4,5 ++ Doesnt Work) */
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($ch, CURLOPT_FAILONERROR, true);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec ($ch);
 
if($output == "NOCHEX_Connection_OK"){
	echo "<h2>Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration.</h2>";
}else{

	$ctx = stream_context_create(['ssl' => [
		'capture_session_meta' => TRUE
	]]);
	 
	$html = file_get_contents('https://tlstest.nochex.com/', FALSE, $ctx);
	$meta = stream_context_get_options($ctx)['ssl']['session_meta'];

	echo "<h2 style='color:red'>OOPs something went wrong</h2> ".curl_error($ch)."<br /><br />";  
	
}

curl_close ($ch);
 
?>
