<?php 

$contextOptions = [
    'ssl' => [
        'verify_peer' => true, 
        'CN_match' => 'tlstest.nochex.com',
    ]
];
$context = stream_context_create($contextOptions);

$data = file_get_contents('https://tlstest.nochex.com', false, $context);

if($data == "NOCHEX_Connection_OK"){
	echo "<h2>Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration.</h2>";
}else{

	$ctx = stream_context_create(['ssl' => [
		'capture_session_meta' => TRUE
	]]);
	 
	$html = file_get_contents('https://tlstest.nochex.com/', FALSE, $ctx);
	$meta = stream_context_get_options($ctx)['ssl']['session_meta'];

	var_dump($meta["protocol"]);

}

?>  
