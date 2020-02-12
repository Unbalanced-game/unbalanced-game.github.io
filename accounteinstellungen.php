<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
if(!isset($_SESSION["login_user"])) //wenn kein Nutzer angemeldet ist,  (die Session-Variable 'login_user' leer ist)
	header("location: anmelden.php"); // wird man wieder zum index zurückgeschickt
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

<h1>Account-Einstellungen</h1>
<p>Hier können Sie einige Dinge festlegen.</p><br><br>
<div id="login">
	<h2 type="center">Name ändern</h2>
	<form action="" method="post">
		<input id="name" name="username" placeholder="Max Mustermann" type="text"><br><br>
		<input name="nameaendern" type="submit" value=" Ändern ">
		<span><?php echo $_SESSION["changeNameMessage"]; ?></span>
	</form>
</div><br>
<div id="login">
	<h2 type="center">Passwort ändern</h2>
	<form action="" method="post">
		<label>Altes Passwort:</label>
		<input id="password" name="oldpassword" placeholder="**********" type="password"><br><br>
		<label>Neues Passwort:</label>
		<input id="password" name="password" placeholder="************" type="password"><br><br>
		<input name="passwortaendern" type="submit" value=" Ändern ">
		<span><?php echo $_SESSION["changePasswordMessage"]; ?></span>
	</form>
</div><br>
<div id="login">
	<h2 type="center">Account löschen</h2>
	<form action="" method="post">
		<label>Zur Bestätigung bitte 'Löschen' eingeben:</label>
		<input id="name" name="bestaetigen" placeholder="Löschen" type="text"><br><br>
		<input name="accountloeschen" type="submit" value=" Löschen ">
		<label>Alle mit ihnen verknüpften Daten werden damit unwiderruflich gelöscht.<?php if($accountbestaetigt=='j')echo "<br>Ihr Bestätigungscode wird dann wieder aktiviert.";?></label>
	</form>
</div>
<?php
if(isset($_POST["nameaendern"])){ //wurde Knopf 'Ändern' im Namensändern-Formular gedrückt?
	$username = $_POST['username']; //Nutzername wird aus Formular geholt
	if(preg_match("/[^a-zA-Z0-9\s]/",$username)==1){ //entspricht der eingegebene Name nicht dem erlaubten Muster?
		$_SESSION["changeNameMessage"] = 'Nutzername darf nur Buchstaben und Zahlen enthalten.'; //dann gib eine Fehlermeldung aus
	}else if(strlen($username)==0){ //Wenn kein Name eingegeben wurde, brich einfach ab
	}else{ //ansonsten ändere den Namen tatsächlich
		$username=preg_replace("[^A-Za-z0-9\s]","",$username); //ersetze zur Sicherheit die unerlaubten Zeichen
		$username=htmlspecialchars($username); //sowie die HTML-Sonderzeichen
		
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$_SESSION["login_user"]."' LIMIT 1"); //schaue nach, ob es den aktuellen Nutzer gibt (zur Sicherheit)
			if($query->num_rows > 0){ //falls ja (1),
				$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$username."' LIMIT 1"); //gibt es den eingegebenen Namen bereits?
				if($query->num_rows == 0){ //falls nein (2), aktualisiere den Namen
					$query = $DB->query("UPDATE nutzerdaten SET username = '".$username."' WHERE username='".$_SESSION["login_user"]."' LIMIT 1");
					$_SESSION["changeNameMessage"] = 'Accountname geändert zu: "'.$username.'". Merken Sie sich den Namen gut! Sie brauchen ihn zum Anmelden.'; //und gib eine Erfolgsnachricht aus
					
					$_SESSION["login_user"] = $username; //der aktuelle Nutzer in der Session-Variable muss hier noch aktualisiert werden, denn er kann unter dem Alten nicht mehr gefunden werden
				}else{ //falls ja (2), gib eine Fehlermeldung aus
					$_SESSION["changeNameMessage"] = 'Dieser Nutzername existiert bereits.';
			}}else{//falls nein (1), gib eine Fehlermeldung aus
				$_SESSION["changeNameMessage"] = 'Bitte melden Sie sich neu an und versuchen Sie es erneut.';
			}
	}
	header("location: accounteinstellungen.php");
	
	
}else if(isset($_POST["passwortaendern"])){ //das Passwort soll geändert werden
	$_SESSION["changePasswordMessage"] = '';
	$oldpassword = $_POST['oldpassword']; //die eingegeben Daten werden geholt
	$password = $_POST['password'];
	if(preg_match("/[^a-zA-Z0-9\s]/",$password)==1 || preg_match("/[^a-zA-Z0-9\s]/",$oldpassword)==1){ //gibt es ein unerlaubtes Zeichen im Passwort?
		$_SESSION["changePasswordMessage"] = 'Passwort darf nur Buchstaben und Zahlen enthalten.'; //dann gib eine Fehlermeldung aus
	}else if(strlen($password)==0 || strlen($oldpassword)==0){ //ist eines der Felder leer?
		$_SESSION["changePasswordMessage"] = '';
	}else if($oldpassword==$password){ //sind die Passwörter gleich?
		$_SESSION["changePasswordMessage"] = 'Neues Passwort darf nicht gleich dem alten sein.';
	}else{ //Passwort kann geändert werden
		$password=htmlspecialchars($password); //zur Sicherheit werden alle HTML-Sonderzeichen ersetzt
		$password = password_hash($password, PASSWORD_DEFAULT); //das Passwort wird gehashed, damit es nicht in plain-text gespeichert werden muss
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		$result=mysqli_query($DB,"SELECT password FROM nutzerdaten WHERE username='".$user_check."' LIMIT 1"); //das alte Passwort wird geholt
		$row=mysqli_fetch_array($result);
		if(password_verify($oldpassword,$row['password'])){ //und überprüft, ob das eingegebene mit dem gehashten in der Datenbank übereinstimmt
			$query=mysqli_query($DB,"UPDATE nutzerdaten SET password='".$password."' WHERE username='".$user_check."' LIMIT 1"); //dann wird das neue eingetragen
		$_SESSION["changePasswordMessage"] = 'Passwort erfolgreich aktualisiert. Merken Sie es sich gut, Sie brauchen es zum anmelden!';
		}
	}
	$_SESSION["changeNameMessage"]='';
	header("location: accounteinstellungen.php");
	
	
}else if(isset($_POST["accountloeschen"])){ //soll der Account gelöscht werden?
	$bestaetigen = $_POST['bestaetigen']; //der Bestätigungstext wird geholt
	if($bestaetigen=='Löschen'){ //wenn er gleich 'Löschen' ist, wurde er richtig eingegeben und er soll wirklich gelöscht werden
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		$query = $DB->query("SELECT verwendeterCode FROM nutzerdaten WHERE username='".$_SESSION["login_user"]."' LIMIT 1"); //der zum Bestätigen vom Nutzer verwendete Code wird geholt
		$row=mysqli_fetch_array($query);
		if(!($row["verwendeterCode"]=='00000000')){ //ist es der Standard-Code '00000000', so hat er keinen verwendet und es muss nichts gemacht werden
			$query = $DB->query("INSERT INTO bestaetigungscodes (id, code) VALUES (NULL, '".$row["verwendeterCode"]."')"); //ist es jedoch nicht '00000000', so wird dieser wieder in die 'bestaetigungscodes'-Tabelle eingetragen
		}
		$query = $DB->query("DELETE FROM nutzerdaten WHERE username='".$_SESSION["login_user"]."' LIMIT 1"); //der Nutzer wird aus der Datenbank gelöscht :(
		session_destroy(); //die Session wird wieder zerstört, sodass keine Informationen verbleiben
	}
	$_SESSION["changeNameMessage"]='';
	$_SESSION["changePasswordMessage"]='';
	header("location: accountgeloescht.html");
}else{
	$_SESSION["changeNameMessage"]='';
	$_SESSION["changePasswordMessage"]='';
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