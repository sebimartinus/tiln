<?php



if(isset($_POST['trimitere-text']))
	$text_preluat = $_POST['text'];
ini_set('max_execution_time', 300);
set_time_limit(300);
#Afisare token cautare pe google.(Verificare existenta geolocatie)
function check_geolocation($nameofgeo){
	$url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".str_replace(" ","%20",$nameofgeo)."&key=AIzaSyDQfsEll4lB-xdxkLXGZA7_a2rMCyVM4Ok";
	$json = file_get_contents($url);
	$json_data = json_decode($json, true);
	if ($json_data["status"]=="ZERO_RESULTS")
		return("Numele introdus nu este o geolocatie!");
	else{
		#print_r($json_data['candidates'][0]['name']);
		#echo " este nume pentru-> "; 
		#echo "<br><br>";
		return($json_data['results'][0]['formatted_address']);
	}
}
	$tari_europa=array(1=>'Austria','Belgium','Bulgaria','Czechia','Cyprus','Croatia','Denmark','Estonia','Finland','France','Germany','Greece','Ireland','Italy','Latvia','Lithuania',
	'Luxembourg','Malta','Poland','Portugal','Romania','Slovakia','Spain','Switzerland','Sweden','Vatican City','Hungary','Monaco','San Marino','Liechtenstein','Malta','Andorra',
	'Georgia','Azerbaijan','Kosovo','Netherlands','Moldova','Turkey','North Macedonia','Albania','Iceland','Armenia','Belarus','Russia','Montenegro');
	$cuvinte=array();
	$adrese=array();
#Afisare toate orasele 
preg_match_all('/([A-Z][a-zâîșăț]*)(-)*\w+/',$text_preluat,$orase);
	if($orase[0]!=NULL){
		foreach($orase[0] as $row){
				$oras_test=explode(",",check_geolocation($row));
				if(isset($oras_test[1])){
				if(in_array(trim($oras_test[1]),$tari_europa) && $oras_test[0]==$row){
					$cuvinte[$row]=check_geolocation($row);
					$adrese[$row]=$row;
				}
		}
	}
}
#Afisare toate cuvintele care incep cu litera mare si cele formate din mai multe substantive proprii.
preg_match_all('/(( [A-Z])|[A-Z][a-zA-Z]+[a-zA-Z]+)(( |-)[A-Z][a-zA-Z]+[a-zA-Z]+)+/',$text_preluat,$substproprii);
	if($substproprii[0]!=NULL){
		foreach($substproprii[0] as $row){
			$cuvinte[$row]=check_geolocation($row);
			$adrese[$row]=$row;
		}
	}

echo "<br>";
echo "<br>";
#Afisare toate numele de strazi,alei,cai sau sosele.
preg_match_all('/( strada| aleea| calea| soseaua|Strada|Aleea|Calea|Soseaua)+(( |-)[A-Z][a-zA-Z]+)+( )*([0-9])*/',$text_preluat,$strazi);
	if($strazi[0]!=NULL){
		foreach($strazi[0] as $row){
			$cuvinte[$row]=check_geolocation($row);
			$adrese[$row]=$row;
		} 
	}

#Afisare Biserici, Manastiri, Catedrale.
preg_match_all('/( |-)*(Biserica|Manastirea|Mitropolia|Catedrala|biserica|manastirea|mitropolia|catedrala)+( [A-Z][a-zA-Z]+)+/',$text_preluat,$biserici);
if($biserici[0]!=NULL){
		foreach($biserici[0] as $row){
			$cuvinte[$row]=check_geolocation($row);
			$adrese[$row]=$row;
		} 
	}
#Afisare rauri, parauri, izvoare, cascade,lacuri,mari.
preg_match_all('/( )*(raul|paraul|izvorul|marea|lacul|cascada|Raul|Paraul|Cascada|Izvorul|Marea|Lacul)+(( |-)[A-Z][a-zA-Z]+)+/',$text_preluat,$ape);
if($ape[0]!=NULL){
		foreach($ape[0] as $row){
			$cuvinte[$row]=check_geolocation($row);
			$adrese[$row]=$row;
		} 
	}
#Afisare toate formele de relief
preg_match_all('/( )*(campia|podisul|muntele|padurea|codrul|codrii|muntii|Campia|Podisul|Muntele|Muntii|Codrii|Padurea|Codrul)+(( |-)[A-Z][a-zA-Z]+)+/',$text_preluat,$forme_relief);
if($forme_relief[0]!=NULL){
	foreach($forme_relief[0] as $row){
		$cuvinte[$row]=check_geolocation($row);
		$adrese[$row]=$row;
	}
}
#Afisare cuvinte cu cratima
preg_match_all('/[A-Z]\w+-([A-Z])\w+/',$text_preluat,$cuvinte_cratima);
if($cuvinte_cratima[0]!=NULL){
	foreach($cuvinte_cratima[0] as $row){
		$cuvinte[$row]=check_geolocation($row);
		$adrese[$row]=$row;
	}
}
#Afisare substantive cu articol
preg_match_all('/( )*([A-Z][a-zA-Z]+)+( |-)(cel|al|de|lui)*( |-)([A-Z][a-zA-Z]+)+/',$text_preluat,$propriicuarticol);
if($propriicuarticol[0]!=NULL){
		foreach($propriicuarticol[0] as $row){
			$cuvinte[$row]=check_geolocation($row);
			$adrese[$row]=$row;
		} 
	}
	$aux=array();
	$comb=array_combine($cuvinte,$adrese);
#Eliminare dubluri din cuvinte array ul cu cuvinte(Unele fac match de doua ori.)
	foreach($comb as $k1 => $row1){
		$ok=1;
		foreach($comb as $k2 => $row2){
			
			if(strstr($row2,$row1)!=false && $k1!=$k2){
				$ok=0;
			}
		}
		if($ok==1){
			$aux[$k1]=$row1;
		}
	}
	$comb=$aux;	
	
?>	
<html>
<head>
	<title>CRFTW</title>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <div class = "background-image">
<div class="container">
	<span class="title">Route tracer following text descriptions</span>
	<span class="title2">Din textul introdus au fost selectate geolocațiile de mai jos. Vă rugăm să le selectați doar pe cele cu care doriți să se construiască un itinerariu.</span>
	<?php		
	echo "<form action='generate-map.php' class='formular-geo' method='post'> ";
	foreach ($comb as $k=>$row){
		echo "<input type='checkbox' name='val[]' value='$row'>";
		echo "<input type='hidden' name='key[]' value='$k'>";
		echo "<label for='$row'>$row-$k</label><br>";
	}
	echo "<input type='submit' value='Creeaza' name='trimitere-geolocatii'>";
	echo "</form>";
?>
</div>
</div>
</body>
</html>	

	
	
	
	
	

