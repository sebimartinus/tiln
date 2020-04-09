<?php



if(isset($_POST['trimitere-text']))
	$text_preluat = $_POST['text'];
ini_set('max_execution_time', 300);
set_time_limit(300);
#Afisare token cautare pe google.(Verificare existenta geolocatie)
function check_geolocation($nameofgeo){
	$url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=".str_replace(" ","%20",$nameofgeo)."&inputtype=textquery&fields=photos,formatted_address,name&locationbias=circle:2000@47.6918452,-122.2226413&key=AIzaSyDQfsEll4lB-xdxkLXGZA7_a2rMCyVM4Ok";
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

	$cuvinte=array();
#Afisare toate cuvintele care incep cu litera mare si cele formate din mai multe substantive proprii.
preg_match_all('/(( [A-Z])|[A-Z][a-zA-Z]+[a-zA-Z]+)( [A-Z][a-zA-Z]+[a-zA-Z]+)+/',$text_preluat,$substproprii);
	if($substproprii[0]!=NULL){
		foreach($substproprii[0] as $row){
			if(check_geolocation($row)=="OK")
				$cuvinte[$row]=$row;
		}
	}

echo "<br>";
echo "<br>";
#Afisare toate numele de strazi,alei,cai sau sosele.
preg_match_all('/( strada| aleea| calea| soseaua|Strada|Aleea|Calea|Soseaua)+( [A-Z][a-zA-Z]+)+ ([0-9])*/',$text_preluat,$strazi);
	if($strazi[0]!=NULL){
		foreach($strazi[0] as $row){
			if(check_geolocation($row)=="OK")
				$cuvinte[$row]=$row;
		} 
	}

#Afisare Biserici, Manastiri, Catedrale.
preg_match_all('/( )*(Biserica|Manastirea|Mitropolia|Catedrala|biserica|manastirea|mitropolia|catedrala)+( [A-Z][a-zA-Z]+)+/',$text_preluat,$biserici);
if($biserici[0]!=NULL){
		foreach($biserici[0] as $row){
			if(check_geolocation($row)=="OK")
				$cuvinte[$row]=$row;
		} 
	}
#Afisare rauri, parauri, izvoare, cascade,lacuri,mari.
preg_match_all('/( )*(raul|paraul|izvorul|marea|lacul|cascada|Raul|Paraul|Cascada|Izvorul|Marea|Lacul)+( [A-Z][a-zA-Z]+)+/',$text_preluat,$ape);
if($ape[0]!=NULL){
		foreach($ape[0] as $row){
			if(check_geolocation($row)=="OK")
				$cuvinte[$row]=$row;
		} 
	}
#Afisare substantive cu articol
preg_match_all('/( )*([A-Z][a-zA-Z]+)+ (cel|al|de|lui) ([A-Z][a-zA-Z]+)+/',$text_preluat,$propriicuarticol);
if($propriicuarticol[0]!=NULL){
		foreach($propriicuarticol[0] as $row){
			if(check_geolocation($row)=="OK")
				$cuvinte[$row]=$row;
		} 
	}
	$aux=array();
#Eliminare dubluri din cuvinte array ul cu cuvinte(Unele fac match de doua ori.)
	foreach($cuvinte as $k1 => $row1){
		$ok=1;
		foreach($cuvinte as $k2 => $row2){
			
			if(strstr($row2,$row1)!=false && $k1!=$k2){
				$ok=0;
			}
		}
		if($ok==1){
			$aux[$k1]=$row1;
		}
	}
	$cuvinte=$aux;	
?>	
<html>
<head>
	<title>CRFTW</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<div class="container">
	<span class="title">Route tracer following text descriptions</span>
	<span class="title2">Din textul introdus au fost selectate geolocatiile de mai jos. Va rugam sa le selectati doar pe cele cu care doriti sa se construiasca un itinerariu.</span>
<?php		
	echo "<form action='generate-map.php' class='formular-geo' method='post'> ";
	foreach ($cuvinte as $k=>$row){
		echo "<input type='checkbox' name='geo[]' value='$row'>";
		echo "<label for='$row'>$row</label><br>";
	}
	echo "<input type='submit' value='Creeaza' name='trimitere-geolocatii'>";
	echo "</form>";
?>
</div>
</body>
</html>	
	
	
	
	
	

