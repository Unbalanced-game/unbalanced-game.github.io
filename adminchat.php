<?php
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
if($typ!='admin') header("location: profile.php"); //wenn der Nutzer kein Admin ist, so soll er wieder zu seinem Profil geleitet werden
if(!isset($_SESSION["login_user"])){ //wenn kein Nutzer angemeldet ist,  (die Session-Variable 'login_user' leer ist)
	header("location: anmelden.php"); // wird man wieder zum index zurückgeschickt
}
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

<h1>Admin Chat-Bereich</h1>

<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
if($_GET['userID']>0){ //wenn in der URL ein Nutzer mit 'GET' angegeben ist, soll dessen Chatverlauf und Chat-Eingabefeld zu sehen sein
	$result=mysqli_query($DB,"SELECT username, id FROM nutzerdaten WHERE id=".$_GET['userID']); //der Name und die ID dieses Nutzers werden geholt
	$row=mysqli_fetch_array($result);
	echo "<p>Mit <b>".$row['username']."</b> chatten:"; //und eine Nachricht mit dem Namen der Person ausgegeben
	
	
	echo "<p>Aktualisieren Sie die Seite, um nach neuen Nachrichten des Nutzers zu prüfen.";
	echo "<p><b>Schwarze</b> Nachrichten sind die des Nutzers, die <b style=\"color:blue\">blauen</b> ihre.<br><br>"; //das Eingabeformular:
	echo"<div id=\"chat\">
		<h2 type=\"center\">Schreiben Sie ".$row['username']." etwas:</h2>
		<form action=\"\" method=\"post\">
			<input id=\"name\" name=\"message\" placeholder=\"\" type=\"text\"><br><br>
			<input name=\"senden\" type=\"submit\" value=\" Absenden \">
			<span><?php echo $error3; ?></span>
		</form>
	</div>";
	echo "<br><br>";
	
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$gelesen=mysqli_query($DB,"UPDATE kontakt SET gelesen = 'j' WHERE gelesen='n' AND userID=".$row['id']); //die Spalte 'gelesen' soll auf 'j' gesetzt werden, bei denen der Nutzer gleich dem ausgeählten Nutzer ist und die Nachricht noch nicht gelesen war
	$result=mysqli_query($DB,"SELECT message, timestamp, adminID FROM kontakt WHERE userID=".$row['id']." ORDER BY timestamp DESC LIMIT 1000"); //die ersten 1000 Nachrichten sollen angezeigt werden
	
	
	if (isset($_POST['senden'])){ //wenn 'Senden' gedrückt wurde, soll ein neuer Eintrag in 'kontakt' angelegt werden
		$query = $DB->query("INSERT INTO kontakt (id, userID, adminID, message, timestamp, gelesen) VALUES (NULL, '".$_GET['userID']."', '".$id."', '".$_POST['message']."', current_timestamp(), 'j')");
		if(!$query)$error3="Ein Fehler ist aufgetreten! Ihre Nachricht darf maximal 1000 Zeichen lang sein.";
		header("location: adminchat.php?userID=".$_GET['userID']);
	}
	
	echo "<table id=\"abs\"><tr>";
	while($row=mysqli_fetch_array($result)){ //die ganzen einzelnen Einträge im Chatverlauf werden aus dem Ergebnis rausgeholt und angezeigt
		if($row['adminID']==-1)
			echo '<tr><td><p style="color:black">'.$row['timestamp'].': '.$row['message'].'</p></td></tr>'; //schwarz, wenn es eine Nachricht des Nutzers ist
		else
			echo '<tr><td><p style="color:blue">'.$row['timestamp'].': '.$row['message'].'</p></td></tr>'; //blau, wenn es ein Admin geschrieben hat
	}
	echo "</tr></table><br>";
	
	
	echo "<br><br><p><a href=\"adminchat.php\">Einen anderen Nutzer auswählen</a><br><br>";
	
	
}else{ //es ist noch kein Nutzer angegeben, darum soll die Nutzerauswahl angezeigt werden
	echo "<p>Bitte wählen Sie einen Nutzer zum Chatten aus:";
	
	echo "<table id=\"abs\"><tr>";
	$result=mysqli_query($DB,"SELECT username, id, accountbestaetigt FROM nutzerdaten WHERE accountbestaetigt = 'j'"); //alle bestätigten Nutzer werden geholt
	while($row=mysqli_fetch_array($result)){ //und ausgegeben
		$neueNachricht=mysqli_query($DB,"SELECT gelesen FROM kontakt WHERE userID=".$row['id']." ORDER BY timestamp DESC"); //es wird geprüft, ob es eine ungelesene Nachricht gibt,
		$checkGelesen=mysqli_fetch_array($neueNachricht);
		if($checkGelesen['gelesen']=='n')echo '<tr><td><a style="color:red" href="adminchat.php?userID='.$row['id'].'">('.$row['id'].') '.$row['username'].'</a></td></tr>'; //dann wird der Link rot dargestellt
		else echo '<tr><td><a href="adminchat.php?userID='.$row['id'].'">('.$row['id'].') '.$row['username'].'</a></td></tr>'; //ansonsten blau
	}
	echo "</tr></table><br>";
}


?>

<p><a href="admin.php">Wieder zurück zum Admin-Bereich</a></p>
	
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