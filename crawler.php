<?php 
ini_set('max_execution_time', 300);
set_time_limit(300);
#Afisare token cautare pe google.(Verificare existenta geolocatie)
function check_geolocation($nameofgeo){
	$url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=".str_replace(" ","%",$nameofgeo)."&inputtype=textquery&fields=photos,formatted_address,name&locationbias=circle:2000@47.6918452,-122.2226413&key=AIzaSyDQfsEll4lB-xdxkLXGZA7_a2rMCyVM4Ok";
	$json = file_get_contents($url);
	$json_data = json_decode($json, true);
	if ($json_data["status"]=="ZERO_RESULTS")
		return("Numele introdus nu este o geolocatie!");
	else{
		#print_r($json_data['candidates'][0]['name']);
		#echo " este nume pentru-> "; 
		#echo "<br><br>";
		return($json_data["status"]);
	}
}

function check_city($name_of_city){
$curl = curl_init();
$url="https://devru-latitude-longitude-find-v1.p.rapidapi.com/latlon.php?location=".str_replace(" ","%20",$name_of_city);
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"x-rapidapi-host: devru-latitude-longitude-find-v1.p.rapidapi.com",
		"x-rapidapi-key: e45c7284efmsh14dfcf76789ddf7p18a940jsncfa57aa5ec9e",
		"Accept: application/json"
	),
));
$response = curl_exec($curl);
$err = curl_error($curl);
$datasearch = json_decode($response, true);
if ($err) {
	echo "cURL Error #:" . $err;
} else {
	foreach($datasearch['Results'] as $row){
		if($name_of_city==explode(",",$row['name'])[0])
			return(explode(",",$row['name'])[0]);
	}
	}
}

	$cuvinte=array();
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
$page = _http( "https://aventurescu.ro/szentendre-bijuteria-colorata-de-langa-budapesta/", "" );
$headers = $page['headers'];
$http_status_code = $headers['http_code'];
$body = $page['body'];


$dom = new DOMDocument();
$dom->loadHTML($body);
#Afisare text preluat din site.
$xpath = new DOMXPath($dom);
$tags = $xpath->query('//div[@class="post-dynamic"]');
foreach ($tags as $tag) {
    $node_value = trim($tag->nodeValue);
    echo $node_value;
	echo "<br>";
	echo "<br>";
	}
	
preg_match_all('/([A-Z][a-zâîșăț]*)(-)*\w+/',$text_preluat,$orase);
	if($orase[0]!=NULL){
		foreach($orase[0] as $row){
			if(check_city($row)==$row){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
	
		}
	}
#Afisare toate cuvintele care incep cu litera mare si cele formate din mai multe substantive proprii.
preg_match_all('/( [A-Z][a-zA-Z]+[a-zA-Z]+)( [A-Z][a-zA-Z]+[a-zA-Z]+)+/',$node_value,$substproprii);
	if($substproprii[0]!=NULL){
		for ($i = 1; $i <= sizeof($substproprii[0]); $i++){ 
			if(check_geolocation($substproprii[0][$i])=="OK")
				if(check_dictionary($substproprii[0][$i])!="BAD"){
					if(!in_array($substproprii[0][$i],$cuvinte))
						array_push($cuvinte,$substproprii[0][$i]);
				}
		}
	}

echo "<br>";
echo "<br>";
#Afisare toate numele de strazi,alei,cai sau sosele.
preg_match_all('/ (strada|aleea|calea|soseaua|Strada|Aleea|Calea|Soseaua)+( [A-Z][a-zA-Z]+)+ [0-9]*/',$node_value,$strazi);
	if($strazi[0]!=NULL){
		for ($i = 1; $i <= sizeof($strazi[0]); $i++){ 
			if(check_geolocation($strazi[0][$i])=="OK")
				if(check_dictionary($strazi[0][$i])!="BAD"){
					if(!in_array($strazi[0][$i],$cuvinte))
						array_push($cuvinte,$strazi[0][$i]);
				}
		} 
			print_r($strazi[0][$i]);
	}

#Afisare Biserici, Manastiri, Catedrale.
preg_match_all('/ (Biserica|Manastirea|Mitropolia|Catedrala|biserica|manastirea|mitropolia|catedrala)+( [A-Z][a-zA-Z]+)+/',$node_value,$biserici);
if($biserici[0]!=NULL){
		for ($i = 1; $i <= sizeof($biserici[0]); $i++) 
			{ 
			if(check_geolocation($biserici[0][$i])=="OK")
				if(check_dictionary($biserici[0][$i])!="BAD"){
					if(!in_array($biserici[0][$i],$cuvinte))
						array_push($cuvinte,$biserici[0][$i]);
				}
			}
			print_r($biserici[0][$i]);
	}
#Afisare rauri, parauri, izvoare, cascade,lacuri,mari.
preg_match_all('/ (raul|paraul|izvorul|marea|lacul|cascada|Raul|Paraul|Cascada|Izvorul|Marea|Lacul)+( [A-Z][a-zA-Z]+)+/',$node_value,$ape);
if($ape[0]!=NULL){
		for ($i = 1; $i <= sizeof($ape[0]); $i++) 
			{ 
			if(check_geolocation($ape[0][$i])=="OK")
				if(check_dictionary($ape[0][$i])!="BAD"){
					if(!in_array($ape[0][$i],$cuvinte))
						array_push($cuvinte,$ape[0][$i]);
				}
		}
	}
#Afisare substantive cu articol
preg_match_all('/( )*([A-Z][a-zA-Z]+)+ (cel|al|de|lui) ([A-Z][a-zA-Z]+)+/',$node_value,$propriicuarticol);
if($propriicuarticol[0]!=NULL){
		for ($i = 1; $i <= sizeof($propriicuarticol[0]); $i++) 
			{ 
			if(check_geolocation($propriicuarticol[0][$i])=="OK")
				if(check_dictionary($propriicuarticol[0][$i])!="BAD"){
					if(!in_array($biserici[0][$i],$cuvinte))
						array_push($cuvinte,$propriicuarticol[0][$i]);
				}
		}
	}
	print_r($cuvinte);
?>