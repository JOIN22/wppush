<?php
// PluginBuddy.com & iThemes.com
// Author: Dustin Bolton February 24, 2010
// Iteration: 45
// Updated: June 1, 2010
//
// Gets updater webpage and returns it to user.
// This is used so that data may be passed back to the calling server directly.

auth_redirect(); // Handles login check and redirects to WP Login if needed.

$url = $_GET['url'];

//
// TODO: switch to using wp_remote_get
//

$crl = curl_init();

if ( isset($_POST) ) {
	$postvars = '';
	while ($element = current($_POST)) {
		$postvars .= key($_POST).'='.$element.'&';
		next($_POST);
	}
	curl_setopt ($crl, CURLOPT_POST, true);
	curl_setopt ($crl, CURLOPT_POSTFIELDS, $postvars);
}

curl_setopt ($crl, CURLOPT_URL,$url);
curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, 45);
$html = curl_exec($crl);
curl_close($crl);

$newline_pos = strpos($html,"\n");

// Take first line and unserialize it.  If its an array then enter the data.
$response = unserialize( substr($html, 0, $newline_pos) ); // Turn first line into array.
if (is_array($response)) { // First line is array data to server.
	$options = get_option('ithemes-'.$response['product']); // Get current plugin options.
	//echo 'ithemes-'.$response['product'];

	if ( isset( $response['set_key'] ) ) {
		$options['updater']['key'] = $response['set_key']; // Change key value.
	} elseif ( isset( $response['unset_key'] ) ) {
		$options['updater']['key'] = '';
	} else {
		echo 'ERROR: UNKNOWN CALLBACK COMMAND! ERROR #85433256.';
	}
	
	$options['updater']['last_check'] = 0; // Force update on next refresh.
	update_option('ithemes-'.$response['product'], $options);
	
	/*
	echo '<pre>';
	print_r( $options );
	echo '</pre>';
	*/
	
	echo substr( $html, $newline_pos+1 );
} else {
	echo $html;
}
?>