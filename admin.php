<?php
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
include('adminactions.php'); //diese Datei enthält die Logik hinter den Aktionen, die hier vorgenommen werden können
if($typ!='admin') header("location: profile.php"); //wenn der Nutzer kein Admin ist, so soll er wieder zu seinem Profil geleitet werden
if(!isset($_SESSION["login_user"])) //wenn kein Nutzer angemeldet ist,  (die Session-Variable 'login_user' leer ist)
	header("location: anmelden.php"); // wird man wieder zum index zurückgeschickt
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta charset="utf-8"/>
	<title>UNBALANCED | <?php echo $login_session; ?></title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" type="image/png" href="favicon.png">
</head>
<body>

<!-- Beginn Header Logo -->
<div id="logo">

  <div id="logo_in">
	<img src="img/cover.jpg" height="300px">	
  </div>

	<div id="head">
		<img src="img/logo.png" style="height:150px;">
		<p><br><b>UNBALANCED | Admin-Bereich</b>
		<br>
		Ein Spiel für die ganze Familie!<br>
		Was wird Sie erwarten?
	</div>
</div>
<!-- Ende Header Logo -->

<!-- Beginn des Rahmens der horizontalen Navigation -->
<div id="navi_rahmen">
<!-- Beginn der horizontalen Navigation -->
<div align="right">
<table id="navi_oben" cellpadding="0" cellspacing="0">
  <tr>
    <td><a href="Home.html">Home</a></td>
    <td><a href="Entwickler.htm">Entwickler</a></td>
    <td><a href="Karten.htm">Karten</a></td>
    <td><a href="anmelden.php">Profil</a></td>
	<td><a href="kontakt.php">Kontakt</a></td>
  </tr>
</table>
</div>
<!-- Ende der horizontalen Navigation -->
</div>
<!-- Ende des Rahmens der horizontalen Navigation -->
<table id="abs" cellpadding="0" cellspacing="0">
  <tr>
    <td id="inhalt" valign="top">
<!-- ab hier beginnt der Inhalt -->

<h1>Admin-Bereich</h1>
<div id="login">
	<h2 type="center">Abstimmungskarten verwalten:</h2>
	<form action="" method="post">
		<label>Name:</label>
		<input id="name" name="name" type="text">
		<label>Beschreibung:</label>
		<input id="name" name="beschreibung" type="text">
		<label>Farbe:</label>
		<input id="name" name="farbe" type="text"><br><br>
		<input name="neueAbstimmung" type="submit" value=" Hinzufügen ">
		<input name="loescheAbstimmung" type="submit" value=" Löschen (nur Name) ">
		<span><?php echo $nachricht2;?></span>
	</form>
</div><br>

<div id="login">
	<form action="" method="post">
		<h2 type="center">Karte mit meisten Stimmen hinzufügen:</h2>
		<input name="karteDazu" type="submit" value=" Hinzufügen ">
		<span><?php echo $nachricht;?></span>
	</form>
</div><br><br><br>

<table id="abs"><tr>
<?php
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"SELECT * FROM karten"); //alle Karten werden geholt
	 
	while($row=mysqli_fetch_array($result)){ //und nacheinander aus dem Rückgabefeld geholt
		if($row['imSpiel']=='v') //wenn die Karte vorgeschlagen ist, soll sie hier ausgegeben werden
			echo '<tr><td><p><b>'.$row['karteVorgeschlagenOriginal'].': '.$row['name'].'</b> ('.$row['farbe'].'): '.$row['beschreibung'].'</td></tr>';
	}
	echo "</tr></table><br>";
?>

<div id="login">
	<form action="" method="post">
		<h2 type="center">Vorgeschlagene Karte zur Abstimmung bereitstellen</h2>
		<label>Vorgeschlagen von Nutzer mit ID:</label>
		<input id="name" name="idvorschlag" type="text"><br><br>
		<input name="vorgeschlageneKarteZurAbstimmung" type="submit" value=" Hinzufügen ">
		<span><?php echo $nachricht3;?></span>
	</form>
</div>

<br><br><p><a href="adminchat.php">Mit Kunden chatten</a></p>
	
<!-- Ende des Inhalts -->
</td>
  </tr>
</table>
<!-- Beginn Tabelle der Fu&szlig;zeile -->
<div id="footer">
UNBALANCED

<!-- Ende des Backlink-Codes -->
</div>
<!-- Ende Tabelle der Fu&szlig;zeile -->
</body>
</html>