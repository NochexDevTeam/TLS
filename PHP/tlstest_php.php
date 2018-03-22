<?php 
 
$url = 'tlstest.nochex.com';

// Create a stream
$opts = array(
  'https'=>array(
    'method'=>"POST",
    'header'=>"Accept-language: en\r\n" .
              "Cookie: foo=bar\r\n",
	'ssl'=>array(
		'verify_peer' => true, 
        'CN_match' => 'tlstest.nochex.com',
	)
  )
);

$context = stream_context_create($opts);

$file = file_get_contents('https://tlstest.nochex.com', false, $context);


if($file == "NOCHEX_Connection_OK"){
	echo "<h2>Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration.</h2>";
}else{
	$opt1s = array(
	'ssl'=>array(
		'verify_peer' => true, 
		'capture_session_meta' => TRUE,
        'CN_match' => 'tlstest.nochex.com',
	)
	);

	$ctx = stream_context_create($opt1s);
	 
	$html = file_get_contents('https://tlstest.nochex.com/', FALSE, $ctx);
	$meta = stream_context_get_options($ctx)['ssl']['session_meta'];

	echo "Current TLS / SSL Protocol: " . $meta["protocol"];
}

?> 
