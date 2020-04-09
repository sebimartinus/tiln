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
<div class="container">
	<span class="title">Route tracer following text descriptions</span>
	<div class="texturi-preluate">
		<a href="./crawler.php" class="site-uri">Text de la Dana Rogoz</a>
		<a href="./crawler2.php"class="site-uri">Text de la Adela Popescu</a>
		<div class="c"></div>
	</div>
		<form action="text-utilizator.php" method="post" class="formular-text-utilizator">
			<label> Introduceti textul dorit</label>
			<textarea name="text"> </textarea>
			<input type="submit" name="trimitere-text" value="Trimite">
		</form>
</div>
</body>
</html>