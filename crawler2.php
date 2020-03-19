<?php 
	error_reporting(E_ERROR | E_PARSE);
	function _http ( $target, $referer ) {
	//Initialize Handle
	$handle = curl_init();
	//Define Settings
	curl_setopt ( $handle, CURLOPT_HTTPGET, true );
	curl_setopt ( $handle, CURLOPT_HEADER, true );
	curl_setopt ( $handle, CURLOPT_COOKIEJAR, "cookie_jar.txt" );
	curl_setopt ( $handle, CURLOPT_COOKIEFILE, "cookies.txt" );
	curl_setopt ( $handle, CURLOPT_USERAGENT, "web-crawler-tutorial-test" );
	curl_setopt ( $handle, CURLOPT_URL, $target );
	curl_setopt ( $handle, CURLOPT_REFERER, $referer );
	curl_setopt ( $handle, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt ( $handle, CURLOPT_MAXREDIRS, 4 );
	curl_setopt ( $handle, CURLOPT_RETURNTRANSFER, true );
	//Execute Request
	$output = curl_exec ( $handle );
	//Close cURL handle
	curl_close ( $handle );
	//Separate Header and Body
	$separator = "\r\n\r\n";
	$header = substr( $output, 0, strpos( $output, $separator ) );
	$body_start = strlen( $header ) + strlen( $separator );
	$body = substr( $output, $body_start, strlen( $output ) - $body_start );
	//Parse Headers
	$header_array = Array();
	foreach ( explode ( "\r\n", $header ) as $i => $line ) {
		if($i === 0) {
			$header_array['http_code'] = $line;
			$status_info = explode( " ", $line );
			$header_array['status_info'] = $status_info;
		} else {
			list ( $key, $value ) = explode ( ': ', $line );
			$header_array[$key] = $value;
		}
	}
	//Form Return Structure
	$ret = Array("headers" => $header_array, "body" => $body );
	return $ret;
}
$page = _http( "https://www.adelapopescu.eu/vacanta-cu-doi-copii-in-sri-lank/", "" );
$headers = $page['headers'];
$http_status_code = $headers['http_code'];
$body = $page['body'];


$dom = new DOMDocument();
$dom->loadHTML($body);

$xpath = new DOMXPath($dom);
$tags = $xpath->query('//div[@id="page"]');
foreach ($tags as $tag) {
    $node_value = trim($tag->nodeValue);
    echo $node_value;
}
?>