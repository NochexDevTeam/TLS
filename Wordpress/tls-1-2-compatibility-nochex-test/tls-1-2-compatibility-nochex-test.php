<?php
/*
Plugin Name: TLS 1.2 Compatibility Nochex Test 
Description: Verify TLS 1.2 support
Version: 1
Author: Nochex  
*/ 

/**
 * Add settings page
 */
//add menu item
function tls12ct_admin_menu() {
	add_management_page('TLS 1.2 Test', 'TLS 1.2 Test', 'manage_options', 'tls12ct-tests', 'tls12ct_tests_page');
}
add_action('admin_menu', 'tls12ct_admin_menu');

/**
 * Force cURL to use TLS 1.2 if needed.
 */
function tls12ct_http_api_curl($handle) {	
	//check option
	$curlopt_sslversion = get_option('tls12ct_curlopt_sslversion', false);	
	
	if($curlopt_sslversion)	
		curl_setopt( $handle, CURLOPT_SSLVERSION, 6 );
}
add_action('http_api_curl', 'tls12ct_http_api_curl');

/**
 * Get endpoints and tests.
 */
function tls12ct_getEndPoints() {
	return array(
		'nochex'=>array(
				'name'=>'nochex', 
				'label'=>'Nochex', 
				'url'=>'https://tlstest.nochex.com/', 
				'callback'=>'tls12ct_test_nochex'
				),
	);
}

function tls12ct_test_endpoint($endpoint) {
	$get = wp_remote_get( 
							$endpoint['url'], 
							array(
									'timeout' => 60,
									'sslverify' => FALSE,
									'httpversion' => '1.1'
							)
						);
					
	if(is_wp_error($get))		
		return array('enabled'=>false, 'message'=>implode('<br />', $get->get_error_messages()));
	
	//get result and process	
	return call_user_func($endpoint['callback'], wp_remote_retrieve_body($get));
}

function tls12ct_test_nochex($result) {	
	if($result == 'NOCHEX_Connection_OK')
		return array('enabled'=>true, 'message'=>$result);
	else
		return array('enabled'=>false, 'message'=>$result);	
}

/**
 * Process Result from HowsMySSL Endpoint
 */
function tls12ct_test_howsmyssl($result) {	
	$result = json_decode($result, true);
	
	if($result['tls_version'] == 'TLS 1.2') {
		$enabled = true;
		$message = 'TLS 1.2 enabled.';
	} else {
		$enabled = false;	
		$message = 'TLS 1.2 not enabled.';
	}
		
	return array('enabled'=>$enabled, 'message'=>$message);
}

/**
 * Load TLS 1.2 Test Page
 */
function tls12ct_tests_page() {
?>
<div class="wrap">
	<h2><?php _e('TLS 1.2 Compatibility Test', 'tls12ct'); ?></h2>
	<p class="description"><?php _e('Verify TLS 1.2 support for included API endpoints and diagnose a solution to enable compatibility.', 'tls12ct'); ?></p>
	<hr />
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">							
				<?php
					//get all endpoints
					$endpoints = tls12ct_getEndPoints();
					
					//which was chosen
					if(!empty($_POST['tls12ct_endpoint']) && check_admin_referer('refresh', 'tls12ct_nonce'))
						$tls12ct_endpoint = $_POST['tls12ct_endpoint'];
					else {
						$tls12ct_endpoint = array_keys($endpoints);
						if(is_array($tls12ct_endpoint) && !empty($tls12ct_endpoint))
							$tls12ct_endpoint = $tls12ct_endpoint[0];
						else
							$tls12ct_endpoint = 'nochex';
					}

					//get curlopt_sslversion setting
					if(!empty($_GET['curlopt_sslversion']) && !empty($_GET['_wpnonce']) && !wp_verify_nonce($_GET['_wpnonce'], 'curlopt_sslversion')) {
						if($_GET['curlopt_sslversion'] == 'enable')
							$curlopt_sslversion = true;
						else
							$curlopt_sslversion = false;
							
						update_option('tls12ct_curlopt_sslversion', $curlopt_sslversion, 'no');
					} else {
						$curlopt_sslversion = get_option('tls12ct_curlopt_sslversion', false);
					}
						
					//run test
					$tls12 = tls12ct_test_endpoint($endpoints[$tls12ct_endpoint]);
				?>				
				<div id="postbox-container-2" class="postbox-container">
					<div class="postbox">
						<h3 class="hndle"><?php printf(__('Test Results', 'tls12ct'), $endpoints[$tls12ct_endpoint]['label']); ?></h3>
						<div class="inside">
							<?php if($tls12['enabled']) { ?>
								<h1 style="color: green"><?php _e('TLS 1.2 Enabled', 'tls12ct');?></h1>
								<p><?php _e('Your site should work fine when making calls to gateways and APIs that require TLS 1.2. You may still want to consider the actions below to secure your site as much as possible.', 'tls12ct');?></p>
							<?php } else { ?>
								<h1 style="color: red"><?php _e('TLS 1.2 Not Enabled', 'tls12ct');?></h1>
								<p><?php _e('Your site is likely to fail when attempting calls to gateways and APIs that require TLS 1.2. Consider following the actions below to enable TLS 1.2.', 'tls12ct');?></p>
							<?php } ?>
						<table class="wp-list-table widefat fixed" width="100%" cellpadding="0" cellspacing="0" border="0">
							<thead>
								<tr class="alternate">
									<th><?php _e('Setting', 'tls12ct'); ?></th>
									<th><?php _e('Value', 'tls12ct'); ?></th>
									<th><?php _e('Action/Notes', 'tls12ct'); ?></th>
								</tr>
							</thead>
							<tbody>									
								<tr>
									<td><?php _e('Endpoint', 'tls12ct'); ?></td>
									<td><?php echo $endpoints[$tls12ct_endpoint]['url'] . ' (' . $endpoints[$tls12ct_endpoint]['label'] . ')'; ?></td>
									<td>
										<?php 
											if($tls12['enabled'])
												echo '<span style="color: green;">' . $tls12['message'] . '</span>';
											else
												echo '<strong style="color: red;">' . $tls12['message'] . '</strong>';
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td><?php _e('PHP Version', 'tls12ct'); ?></td>
									<td><?php echo phpversion(); ?></td>
									<td>
										<?php 
											if(version_compare(phpversion(), '5.5.19', '>='))
												echo '<span style="color: green;">' . __('PHP version 5.5.19 or higher detected.', 'tls12ct') . '</span>';
											else
												echo '<strong style="color: red;">' . __('Upgrade to PHP version 5.5.19 or higher.', 'tls12ct') . '</strong>';
										?>
									</td>
								</tr>
								<tr>
									<td><?php _e('cURL Version', 'tls12ct'); ?></td>
									<td>
										<?php 
											if(!function_exists('curl_version'))
												echo '<span style="color: green;">' . __('cURL not installed.', 'tls12ct') . '</span>';
											else {
												$curl_version = curl_version();
												echo $curl_version['version'];
											}
										?>
									</td>
									<td>
										<?php 
											if(version_compare($curl_version['version'], '7.34.0', '>='))
												echo '<span style="color: green;">' . __('cURL version 7.34.0 or higher detected.', 'tls12ct') . '</span>';
											else
												echo '<strong style="color: red;">' . __('Upgrade to cURL version 7.34.0 or higher.', 'tls12ct') . '</strong>';
										?>
									</td>
								</tr>
								<tr class="alternate">
									<td><?php _e('cURL SSL Version', 'tls12ct'); ?></td>
									<td>
										<?php 
											if(!function_exists('curl_version'))
												_e('cURL not installed.', 'tls12ct');
											else {
												$curl_version = curl_version();
												echo $curl_version['ssl_version'];
											}
										?>
									</td>
									<td>
										<?php
											if(function_exists('curl_version')) {
												echo __('Make sure you are running OpenSSL/1.0.1 or higher, NSS/3.15.1 or higher, or the latest version of other cryptographic libraries.',
												'tls12ct');
												
												if(!$tls12['enabled'] || $curlopt_sslversion) {
													if($curlopt_sslversion)
														echo '<br /><br /><strong style="color: green">' . sprintf(__('You have chosen to force TLS 1.2 for cURL. <a href="%s">Click here to disable this feature</a>.', 'tls12ct'), wp_nonce_url(admin_url('tools.php?page=tls12ct-tests&curlopt_sslversion=disable')), 'curlopt_sslversion', 'tls12ct_nonce') . '</strong>';
													else
														echo '<br /><br /><strong style="color: red;">' . sprintf(__('If your versions of PHP and cURL are not up to date, you may still be able to force TLS 1.2 connections. <a href="%s">Click here to force cURL to use TLS 1.2</a>.', 'tls12ct'), wp_nonce_url(admin_url('tools.php?page=tls12ct-tests&curlopt_sslversion=enable')), 'curlopt_sslversion', 'tls12ct_nonce') . '</strong>';
												}
											}
											else
												_e('Install cURL if requests are not working or not secure.', 'tls12ct');
										?>
									</td>
								</tr>									
							</tbody>
						</table>
						</div> <!-- end inside -->
					</div> <!-- end postbox -->
				</div> 
				
				<!-- end postbox-container-2 			
				<form class="settings" method="post" action="">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row"><?php _e('Select API Endpoint', 'tls12ct'); ?></th>
								<td>
									<select id="tls12ct_endpoint" name="tls12ct_endpoint">
										<?php
											foreach($endpoints as $endpoint) {
											?>
											<option value="<?php echo esc_attr($endpoint['name']);?>" <?php selected($tls12ct_endpoint, $endpoint['name']);?>><?php echo $endpoint['label'];?></option>
											<?php
											}
										?>
									</select>
									<span class="description"><?php _e('If the Nochex test works, you should be able to connect to other gateway APIs as well.', 'tls12ct'); ?></span>
								</td>
							</tr>
						</tbody>
					</table>
					<fieldset class="submit">
						<input class="button-primary" type="submit" name="tls12ct_test_submit" value="<?php _e('Refresh Test', 'tls12ct');?>">
						<?php wp_nonce_field('refresh', 'tls12ct_nonce'); ?>
					</fieldset>
				</form>												
			</div>-->		 <!-- end post-body-content -->
			
			
		</div> <!-- end post-body -->
	</div> <!-- end poststuff -->
</div> <!-- end wrap -->
<?php   
}
