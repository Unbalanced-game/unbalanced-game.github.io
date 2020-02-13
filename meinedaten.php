<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
include('loginshowdata.php'); //falls der Nutzer sich noch anmelden muss passiert hier die Logik
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

<?php
if(!isset($_SESSION["login_user"])){ //wenn kein Nutzer angemeldet ist, wird das Anmeldeformular angezeigt
	echo "<h1>Sie müssen sich erst anmelden, bevor ihre Daten angezeigt werden können!</h1>";
	echo"<div id=\"login\">
		<h2 type=\"center\">Anmeldeformular:</h2>
		<form action=\"\" method=\"post\">
			<label>Nutzername:</label>
			<input id=\"name\" name=\"username\" placeholder=\"Max Mustermann\" type=\"text\">
			<label>Passwort:</label>
			<input id=\"password\" name=\"password\" placeholder=\"**********\" type=\"password\"><br><br>
			<input name=\"anmelden\" type=\"submit\" value=\" Anmelden \">
			<span><?php echo $error; ?></span>
		</form>
	</div>

	<br>

	<p>Sie sind noch nicht registriert? Das können Sie hier tun:
	<div id=\"login\">
		<h2 type=\"center\">Registrieren:</h2>
		<form action=\"\" method=\"post\">
			<label>Nutzername:</label>
			<input id=\"name\" name=\"username\" placeholder=\"Max Mustermann\" type=\"text\">
			<label>E-Mail:</label>
			<input id=\"name\" name=\"mail\" placeholder=\"mail@maxmustermann.de\" type=\"text\">
			<label>Bestätigungscode (in der Spielebox/optional):</label>
			<input id=\"name\" name=\"code\" placeholder=\"1B7RH8T2\" type=\"text\">
			<label>Passwort:</label>
			<input id=\"password\" name=\"password\" placeholder=\"**********\" type=\"password\"><br><br>
			<input name=\"accounterstellen\" type=\"submit\" value=\" Account erstellen \">
			<span><?php echo $error2; ?></span>
		</form>
	</div>";
}else{ //wenn der Nutzer angemeldet ist
	$conn = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$user_check = $_SESSION['login_user'];
	$query = "SELECT username, mail, karteAbgestimmtID, typ, password, id, accountbestaetigt, verwendeterCode from nutzerdaten where username = '$user_check'";
	$ses_sql = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($ses_sql);
	echo "<h1>Ihre Daten:</h1><br>"; //alle Daten des Nutzers werden ausgegeben
	echo "<p><table style=\"border: 1px solid #000000;text-align: left;padding: 8px;\">";
	echo "<tr><th>Name: ⠀</th><th>".$row["username"]."</tr>";
	echo "<tr><th>E-Mail: ⠀</th><th>".$row["mail"]."</tr>";
	echo "<tr><th>Passwort (hash): ⠀</th><th>".$row["password"]."</tr>";
	echo "<tr><th>Account bestätigt: ⠀</th><th>".$row["accountbestaetigt"]."</tr>";
	echo "<tr><th>Für Karten-ID abgestimmt: ⠀</th><th>".$row["karteAbgestimmtID"]."</tr>";
	echo "<tr><th>Account-ID: ⠀</th><th>".$row["id"]."</tr>";
	echo "<tr><th>Account-Typ: ⠀</th><th>".$row["typ"]."</tr>";
	echo "<tr><th>Verwendeter Bestätigungscode: ⠀</th><th>".$row["verwendeterCode"]."</tr>";
	if(strlen($kartenname)>0) echo "<tr><th>Vorgeschlagene Karte: ⠀</th><th>".$kartenname.": ".$kartenbeschreibung." (".$kartenfarbe.")</tr>"; //falls er eine Karte vorgeschlagen hat, wird sie ihm angezeigt
	echo "<tr><th>Chatverlauf: ⠀</th><th>Klicken Sie <a href=\"kontakt.php\">hier!</a></tr>";
	echo "</table></p>";
}
?>

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