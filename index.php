<?php
if(isset($_POST['trimitere-text']))
	$comments= $_POST['text'];
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
            <form action="text-utilizator.php" method="post" class="formular-text-utilizator" enctype="multipart/form-data">
                <label> Introduceți textul dorit</label>
                <div class = "line"></div> 
                <span class = "text-diactritice">Introduceți în căsuța de mai jos textul pentru care doriți să se extragă geolocatiile și să se realizeze o rută sau alegeți un fișier text (ATENȚIE: doar fișiere cu extensia .txt). Timpul de așteptare poate varia în funcție de numărul geolocatiilor din text.</span>  
                <textarea name="text"> </textarea>
                <input type="file" name="fisier-text" id="fisier-text">
                <input type="submit" name="trimitere-text" value="Trimite">
            </form>
            </div>
</div>
</body>
</html>