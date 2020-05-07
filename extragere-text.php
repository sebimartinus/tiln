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
            <form action="text-utilizator.php" method="post" class="formular-text-utilizator">
                <label> Introduceți textul dorit</label>
                <div class = "line"></div> 
                <span class = "text-diactritice">Vă rugăm să introduceți locațiile cu diacritice pentru a vă oferi locația corespunzătoare.</span>        
                <textarea name="text"> </textarea>
                <input type="submit" name="trimitere-text" value="Trimite">
            </form>
            </div>
</div>
</body>
</html>