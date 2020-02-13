<?php
include('login.php'); //hier findet die Logik hinter dem Anmeldevorgang statt
if(isset($_SESSION['login_user'])){ //wenn schon ein Nutzer angemeldet ist
	header("location: profile.php"); // wird er an die Profilseite weitergeleitet
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta charset="utf-8"/>
	<title>UNBALANCED | Mitwirken</title>
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
		<p><br><b>UNBALANCED | Mitwirken</b>
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
	    
<h1>Über neue Karten abstimmen</h1>
<p>Helfen Sie uns zu entscheiden, welche der Karten als nächstes zum<br>
   offiziellen Spiel hinzugefügt werden!
<p>Bevor sie anstimmen können, müssen Sie sich anmelden:
<div id="login">
	<h2 type="center">Anmeldeformular:</h2>
	<form action="" method="post">
		<label>Nutzername:</label>
		<input id="name" name="username" placeholder="Max Mustermann" type="text">
		<label>Passwort:</label>
		<input id="password" name="password" placeholder="**********" type="password"><br><br>
		<input name="anmelden" type="submit" value=" Anmelden ">
		<span><?php echo $error; ?></span>
	</form>
</div>

<br>

<p>Sie sind noch nicht registriert? Das können Sie hier tun:
<div id="login">
	<h2 type="center">Registrieren:</h2>
	<form action="" method="post">
		<label>Nutzername:</label>
		<input id="name" name="username" placeholder="Max Mustermann" type="text">
		<label>E-Mail:</label>
		<input id="name" name="mail" placeholder="mail@maxmustermann.de" type="text">
		<label>Bestätigungscode (in der Spielebox/optional):</label>
		<input id="name" name="code" placeholder="1B7RH8T2" type="text">
		<label>Passwort:</label>
		<input id="password" name="password" placeholder="**********" type="password"><br><br>
		<input name="accounterstellen" type="submit" value=" Account erstellen ">
		<span><?php echo $error2; ?></span>
	</form>
</div>
	
<!-- Ende des Inhalts -->
</td>
  </tr>
</table>
<!-- Beginn Tabelle der Fu&szlig;zeile -->
<div id="footer">
UNBALANCED | <a href="impressum.html" style="color:#ffffff">IMPRESSUM</a>

<!-- Ende des Backlink-Codes -->
</div>
<!-- Ende Tabelle der Fu&szlig;zeile -->
</body>
</html>
