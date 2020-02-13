<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
include('loginkontakt.php'); //sollte der Nutzer noch nicht angemeldet sein, kann er das auf dieser Seite ebenfalls tun, dann soll er aber auf die Seite 'kontakt.php' weitergeleitet werden
if($accountbestaetigt=='n'){header("location: profile.php");} //wenn er nicht bestätigt ist, darf er nicht das Kontaktformular verwenden und wird zu seinem Profil zurückgeschickt
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta charset="utf-8"/>
	<title>UNBALANCED | Kontakt</title>
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
		<p><br><b>UNBALANCED | Kontakt</b>
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
if(!isset($_SESSION["login_user"])){ //wenn kein Nutzer angemeldet ist, wird das Login-Formular ausgegeben
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
	
}else{ //wenn der Nutzer angemeldet ist, wird der Chat-Bereich angezeigt
	echo "<p>Aktualisieren Sie die Seite, um nach neuen Nachrichten des YAPAC-Teams zu prüfen.";
	echo "<p><b>Schwarze</b> Nachrichten sind ihre, die <b style=\"color:blue\">blauen</b> die des YAPAC-Teams.<br><br>";
	echo"<div id=\"chat\">
		<h2 type=\"center\">Schreiben Sie uns etwas:</h2>
		<form action=\"\" method=\"post\">
			<input id=\"name\" name=\"message\" placeholder=\"\" type=\"text\"><br><br>
			<input name=\"senden\" type=\"submit\" value=\" Absenden \">
			<span><?php echo $error3; ?></span>
		</form>
	</div>";
	echo "<br><br>";
	
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"SELECT message, timestamp, adminID FROM kontakt WHERE userID=".$id." ORDER BY timestamp DESC"); //alle Nachrichten des Nutzers werden geladen...
	$anzahlErgebnisse=0;
	while($row=mysqli_fetch_array($result)){ //... und hier gezählt
		$anzahlErgebnisse=$anzahlErgebnisse+1;
	}
	
	if($_GET['n']==0) //die Anzahl der anzuzeigenden Ergebnisse ist 0, d.h. es wurde nichts angegeben. Es sollen 30 Stück angezeigt werden
		$result=mysqli_query($DB,"SELECT message, timestamp, adminID FROM kontakt WHERE userID=".$id." ORDER BY timestamp DESC LIMIT 30");
	else //es wurde etwas angegeben, es werden so viele angezeigt
		$result=mysqli_query($DB,"SELECT message, timestamp, adminID FROM kontakt WHERE userID=".$id." ORDER BY timestamp DESC LIMIT ".$_GET['n']);
	
	echo "<table id=\"abs\"><tr>";
	while($row=mysqli_fetch_array($result)){ //jeder Eintrag wird geholt
		if($row['adminID']==-1) //wenn die Nachricht nicht von einem Admin geschrieben wurde
			echo '<tr><td><p style="color:black">'.$row['timestamp'].': '.$row['message'].'</p></td></tr>'; //soll sie schwarz sein
		else //wenn sie von einem Nutzer ist
			echo '<tr><td><p style="color:blue">'.$row['timestamp'].': '.$row['message'].'</p></td></tr>'; //soll sie blau sein
	}
	echo "</tr></table><br>";
	
	if (isset($_POST['senden'])){ //wenn der Nutzer eine Nachricht absendet
		$message=htmlspecialchars($_POST['message']); //sollen zunächst alle HTML-Sonderzeichen erstzt werden
		if(strlen($message)>1){
			$query = $DB->query("INSERT INTO kontakt (id, userID, adminID, message, timestamp) VALUES (NULL, '".$id."', '-1', '".$message."', current_timestamp())"); //und dann in die Datenbank eingefügt werden
			if(!$query)$error3="Ein Fehler ist aufgetreten! Ihre Nachricht darf maximal 1000 Zeichen lang sein.";
		}
		header("location: kontakt.php");
	}
	
	if($anzahlErgebnisse>$_GET['n'] && $anzahlErgebnisse>30){ //wenn es noch imemr mehr Ergebnisse gibt, als angezeigt werden, so soll 'Ältere Nachrichten anzeigen' angezeigt werden
		if(isset($_GET['n']))	echo "<p><a href=\"kontakt.php?n=".($_GET['n']+30)."\">Ältere Nachrichten anzeigen</a></p>";
		else					echo "<p><a href=\"kontakt.php?n=60\">Ältere Nachrichten anzeigen</a></p>";
	}
	
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
