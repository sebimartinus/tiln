<?php

if(isset($_POST['trimitere-text'])){
	if(isset($_POST['text']) && $_POST['text']!='' && $_FILES["fisier-text"]['name']!=''){
			echo("Ati adaugat si fisier si text!");
			exit();
		}

	elseif($_FILES["fisier-text"]['name']!='')  {
		if($_FILES["fisier-text"]['type']!='text/plain'){
			echo("Doar fisiere text!");
			exit();
		}
		else{
			$cale_fisier = "./public/temp/".basename($_FILES["fisier-text"]['name']);
			if (move_uploaded_file($_FILES['fisier-text']['tmp_name'], $cale_fisier))
  					array_push($errors,"Imaginea a fost incarcata cu succes!");
  				else
  					array_push($errors,"Imaginea nu a putut fi incarcata!");
  			$text_preluat = file_get_contents($cale_fisier);
  			unlink($cale_fisier);
		}

	}
	elseif(isset($_POST['text']) && $_POST['text']!=''){
		$text_preluat = $_POST['text'];
	}
		
	}

ini_set('max_execution_time', 300);
set_time_limit(300);
#Afisare token cautare pe google.(Verificare existenta geolocatie)
function check_geolocation($nameofgeo){
	$url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".str_replace(" ","%20",$nameofgeo)."&key=AIzaSyDQfsEll4lB-xdxkLXGZA7_a2rMCyVM4Ok";
	$json = file_get_contents($url);
	$json_data = json_decode($json, true);
	if ($json_data["status"]=="ZERO_RESULTS")
		return 0;
	else
		return($json_data['results'][0]['formatted_address']);
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
			return(explode(",",$row['name']));
	}
	}
}

	$tari_europa=array(1=>'Austria','Belgium','Bulgaria','Czechia','Republic of Moldova','Cyprus','Croatia','Denmark','Estonia','Finland','France','Germany','Greece','Ireland','Italy','Latvia','Lithuania',
	'Luxembourg','Malta','Poland','Portugal','Romania','Slovakia','Spain','Switzerland','Sweden','Vatican City','Hungary','Monaco','San Marino','Liechtenstein','Malta','Andorra',
	'Georgia','Azerbaijan','Kosovo','Netherlands','Moldova','Turkey','North Macedonia','Albania','Iceland','Armenia','Belarus','Russia','Montenegro','Ukraine');
	$cuvinte=array();
	$adrese=array();
	
#Afisare toate orasele 
preg_match_all('/([A-Z][a-zâîșăț]*)(-)*\w+/',$text_preluat,$orase);
	if($orase[0]!=NULL){
		foreach($orase[0] as $row){
			$verif=check_city($row);
		if($verif!=''){
			if(in_array(trim($verif[1]),$tari_europa)){
				$verificare=check_geolocation($row);
				if($verificare!=NULL){
					$cuvinte[$row]=check_geolocation($row);
					$adrese[$row]=$row;
			}
			}
			}	
		}
	}
#Afisare toate cuvintele care incep cu litera mare si cele formate din mai multe substantive proprii.
preg_match_all('/( |-)*([A-Z]+[a-zăâîșț]+[a-zăâîșț]+)+( |-)+([A-Z]+[a-zăâîșț]+[a-zăâîșț]+)+(( |-)*([A-Z]+[a-zăâîșț]+[a-zăâîșț]+))*/',$text_preluat,$substproprii);
	if($substproprii[0]!=NULL){
		foreach($substproprii[0] as $row){
			$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
		}
	}

#Afisare toate numele de strazi,alei,cai sau sosele.
preg_match_all('/( strada| aleea| calea| soseaua| str.|Str.|Strada|Aleea|Calea|Soseaua)+(( |-)[A-Z|a-zăâîșț][a-zăâîșț.]+)+( )*([0-9])*/',$text_preluat,$strazi);
	if($strazi[0]!=NULL){
		foreach($strazi[0] as $row){
			$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
		} 
	}

#Afisare Biserici, Manastiri, Catedrale.
preg_match_all('/( |-)*(Biserica|Manastirea|Mănăstirea|Mitropolia|Catedrala|biserica|mănăstirea|mitropolia|catedrala)+( (Sf. )*[A-Z][a-zăâîșț]+[a-zăâîșț]+)+/',$text_preluat,$biserici);
if($biserici[0]!=NULL){
		foreach($biserici[0] as $row){
			$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
		} 
	}
#Afisare rauri, parauri, izvoare, cascade,lacuri,mari.
preg_match_all('/( )*(raul|paraul|izvorul|marea|lacul|cascada|Raul|Paraul|Cascada|Izvorul|Marea|Lacul)+(( |-)[A-Z][a-zăâîșț]+)+/',$text_preluat,$ape);
if($ape[0]!=NULL){
		foreach($ape[0] as $row){
			$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
		} 
	}
#Afisare toate formele de relief
preg_match_all('/( )*(campia|podisul|muntele|padurea|codrul|codrii|muntii|Campia|Podisul|Muntele|Muntii|Codrii|Padurea|Codrul)+(( |-)[A-Z][a-zăâîșț]+)+/',$text_preluat,$forme_relief);
if($forme_relief[0]!=NULL){
	foreach($forme_relief[0] as $row){
		$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
	}
}
#Afisare cuvinte cu cratima
preg_match_all('/[A-Z]\w+-([A-Z])\w+/',$text_preluat,$cuvinte_cratima);
if($cuvinte_cratima[0]!=NULL){
	foreach($cuvinte_cratima[0] as $row){
		$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
	}
}
#Afisare substantive cu articol
preg_match_all('/( )*([A-Z][a-zăâîșțA-Z]+)+( [A-Z][a-zăâîșțA-Z]+)*( |-)(cel|al|de|lui|din)*( |-)([A-Z][a-zăâîșț]+)+( [A-Z][a-zăâîșț]+)*/',$text_preluat,$propriicuarticol);
if($propriicuarticol[0]!=NULL){
		foreach($propriicuarticol[0] as $row){
			$verificare=check_geolocation($row);
			if($verificare!=NULL){
				$cuvinte[$row]=check_geolocation($row);
				$adrese[$row]=$row;
			}
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
	echo "<form action='generate-map.php' class='formular-geo' id='select-all' method='post'> ";
	foreach ($comb as $k=>$row){
		echo "<input class='checkbox' type='checkbox' name='val[]' value='$row'>";
		echo "<input type='hidden' name='key[]' value='$k'>";
		echo "<label for='$row'>$row-$k</label><br>";
	}
	echo "<input type='checkbox' id='select_all'>";
	echo "<label for='select_all' class='checkbox'>Selecteaza tot</label>";
	echo "<input type='submit' value='Creeaza' name='trimitere-geolocatii'>";
	echo "</form>";
?>
</div>
</div>
<script>
	var select_all = document.getElementById("select_all"); //select all checkbox
	var checkboxes = document.getElementsByClassName("checkbox"); //checkbox items

	//select all checkboxes
	select_all.addEventListener("change", function(e){
		for (i = 0; i < checkboxes.length; i++) { 
			checkboxes[i].checked = select_all.checked;
		}
	});


	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].addEventListener('change', function(e){ //".checkbox" change 
			//uncheck "select all", if one of the listed checkbox item is unchecked
			if(this.checked == false){
				select_all.checked = false;
			}
			//check "select all" if all checkbox items are checked
			if(document.querySelectorAll('.checkbox:checked').length == checkboxes.length){
				select_all.checked = true;
			}
		});
	}
</script>
</body>
</html>	


	
	
	
	
	

