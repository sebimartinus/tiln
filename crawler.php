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
$page = _http( "http://www.danarogoz.com/ne-am-reintalnit-in-2020/", "" );
$headers = $page['headers'];
$http_status_code = $headers['http_code'];
$body = $page['body'];


$dom = new DOMDocument();
$dom->loadHTML($body);
#Afisare text preluat din site.
$xpath = new DOMXPath($dom);
$tags = $xpath->query('//div[@class="post_text"]');
foreach ($tags as $tag) {
    $node_value = trim($tag->nodeValue);
    echo $node_value;
	echo "<br>";
	echo "<br>";
	}
#Afisare toate cuvintele care incep cu litera mare si cele formate din mai multe substantive proprii.
preg_match_all('/( [A-Z][a-zA-Z]+)+/',$node_value,$substproprii);
	if($substproprii[0]!=NULL){
		for ($i = 1; $i <= sizeof($substproprii[0]); $i++){ 
			print_r($substproprii[0][$i]);
			echo "<br>";
		}
	}

echo "<br>";
echo "<br>";
#Afisare toate numele de strazi,alei,cai sau sosele.
preg_match_all('/ (strada|aleea|calea|soseaua|Strada|Aleea|Calea|Soseaua)+( [A-Z][a-zA-Z]+)+ [0-9]*/',$node_value,$strazi);
	if($strazi[0]!=NULL){
		for ($i = 1; $i <= sizeof($strazi[0]); $i++) 
			print_r($strazi[0][$i]);
	}

#Afisare Biserici, Manastiri, Catedrale.
preg_match_all('/ (Biserica|Manastirea|Mitropolia|Catedrala|biserica|manastirea|mitropolia|catedrala)+( [A-Z][a-zA-Z]+)+/',$node_value,$biserici);
if($biserici[0]!=NULL){
		for ($i = 1; $i <= sizeof($biserici[0]); $i++) 
			print_r($biserici[0][$i]);
	}
#Afisare rauri, parauri, izvoare, cascade,lacuri,mari.
preg_match_all('/ (raul|paraul|izvorul|marea|lacul|cascada|Raul|Paraul|Cascada|Izvorul|Marea|Lacul)+( [A-Z][a-zA-Z]+)+/',$node_value,$ape);
if($ape[0]!=NULL){
		for ($i = 1; $i <= sizeof($ape[0]); $i++) 
			print_r($ape[0][$i]);
	}
#Afisare substantive cu articol
preg_match_all('/( )*([A-Z][a-zA-Z]+)+ (cel|al|de|lui) ([A-Z][a-zA-Z]+)+/',$node_value,$propriicuarticol);
if($propriicuarticol[0]!=NULL){
		for ($i = 1; $i <= sizeof($propriicuarticol[0]); $i++) 
			print_r($propriicuarticol[0][$i]);
	}

#Afisare token cautare pe google.(Verificare existenta geolocatie)
echo "<br>";
echo "<br>";
function check_geolocation($nameofgeo){
$url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=".str_replace(" ","%",$nameofgeo)."&inputtype=textquery&fields=photos,formatted_address,name,opening_hours,rating&locationbias=circle:2000@47.6918452,-122.2226413&key=AIzaSyDQfsEll4lB-xdxkLXGZA7_a2rMCyVM4Ok";
$json = file_get_contents($url);
$json_data = json_decode($json, true);
if ($json_data["status"]=="ZERO_RESULTS")
	echo("Numele introdus nu este o geolocatie!");
else
	echo "OK if this is a Geolocation: ". $json_data["status"];
}
echo ("Check if Iasi is Geolocation");
echo ("<br>");
check_geolocation("LOVE");
?>