<?php
include('session.php'); //hier werden die Account-Informationen über den angemeldeten Nutzer aus der Datenbank geholt
include('abstimmung.php'); //die Logik hinter dem Abstimmungsformular und dem Karte-Vorschlagen Formular
include('login.php'); //Falls der Nutzer noch nicht bestätigt war, kann er dies auf dieser Seite tun. Die Logik dahinter wird hier importiert
if(!isset($_SESSION["login_user"])){ //wenn kein Nutzer angemeldet ist, (die Session-Variable 'login_user' leer ist)
	header("location: anmelden.php"); // wird man wieder zur Anmeldung zurückgeschickt
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head> <!-- Im Head der Seite wird der Zeichensatz, der Seitentitel und das icon der Seite definiert. Zudem wird die CSS-Datei eingebunden -->
	<meta charset="utf-8"/>
	<title>UNBALANCED | Mitwirken</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" type="image/png" href="favicon.png">
</head>
<body>

<!-- Beginn Header Logo -->
<div id="logo">

  <div id="logo_in">
	<img src="img/cover.jpg" height="300px"> <!-- Cover des Brettspiels wird auf der linken oberen Seite dargestellt -->
  </div>

	<div id="head">
		<img src="img/logo.png" style="height:150px;"> <!-- Das Unbalanced-Logo wird zusammen mit -->
		<p><br><b>UNBALANCED | Mitwirken</b>		   <!-- dem Text UNBALANCED und der aktuell ausgewählten Seite und -->
		<br>
		Ein Spiel für die ganze Familie!<br>           <!-- einem Werbetext ausgegeben -->
		Was wird Sie erwarten?
	</div>
	
</div>
<!-- Ende Header Logo -->

<!-- Beginn des Rahmens der horizontalen Navigation -->
<div id="navi_rahmen">
<!-- Beginn der horizontalen Navigation -->
<div align="right">
<table id="navi_oben" cellpadding="0" cellspacing="0"> <!-- Die Tabelle wird geöffnet -->
  <tr>
    <td><a href="Home.html">Home</a></td>  <!-- Die einzelnen Menüpunkte werden angegeben, zusammen mit dem dazugehörigen Pfad -->
    <td><a href="Entwickler.htm">Entwickler</a></td>
    <td><a href="Karten.htm">Karten</a></td>
    <td><a href="anmelden.php">Profil</a></td>
	<td><a href="kontakt.php">Kontakt</a></td>
  </tr>
</table>  <!-- Und die Tabelle wird wieder beendet -->
</div>
<!-- Ende der horizontalen Navigation -->
</div>
<!-- Ende des Rahmens der horizontalen Navigation -->
<table id="abs" cellpadding="0" cellspacing="0">
  <tr>
    <td id="inhalt" valign="top">
	
<!-- ab hier beginnt der Inhalt -->

<h1>Ihr persönlicher Bereich:</h1> <!-- Zunächst werden die Accountdaten ausgegeben -->
<p><b>Willkommen, <i><?php echo $login_session; ?></i>!<?php if($accountbestaetigt =='n')echo " (unbestätigter Account)";else "g";?><br></b>
<p><b id="logout"><a href="accounteinstellungen.php">Account-Einstellungen</a></b>
<p>Hier können Sie sich <b id="logout"><a href="logout.php">Abmelden</a></b><br><br>

<?php
	if($typ=='admin'){ //wenn der Nutzer ein Admin ist, wird ihm der Knopf angezeigt, der ihn zum Admin-Bereich führt
		echo "<input onclick=\"location.href = 'admin.php';\" name=\"adminButton\" type=\"submit\" value=\" Admin-Bereich \">";
	}
?>


<h2>Abstimmen</h2>  <!-- Wenn man angemeldet ist, kann man hier für eine Karte abstimmen oder eine eigene vorschlagen -->
<p>
<table id="abs"><tr>
<?php
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"SELECT * FROM karten WHERE imSpiel='n'"); //die Karten, die zur Abstimmung bereitstehen werden aus der Datenbank geholt und der zweidimensionale Rückgabewert in $result gespeichert
	 
	while($row=mysqli_fetch_array($result)){ //solange es noch Zeilen zum ausgeben gibt, wird diese Schleife wiederholt
		if($aktuellAbgestimmt==$row['id']) //wenn der Nutzer für die aktuell anzuzeigende Karte abgestimmt hat, wird diese rot markiert ausgegeben
			echo '<tr><td><b>'.$row['id'].': <b style="color:red">'.$row['name'].'</b> ('.$row['farbe'].'): '.$row['beschreibung'].'</td></tr>';
		if($aktuellAbgestimmt!=$row['id']) //ansonsten wird sie ganz normal ausgegeben
			echo '<tr><td><b>'.$row['id'].': '.$row['name'].'</b> ('.$row['farbe'].'): '.$row['beschreibung'].'</td></tr>';
	}
	echo "</tr></table><br>";

	if($accountbestaetigt=='j'){ //wenn der Nutzer einen bestätigten Account hat, wird ihm der Abstimm- und Vorschlagdialog angezeigt
		echo "<p>Für welche der Karten stimmen Sie? Sie können nur für eine Karte Abstimmen! (aktuell: ";if($aktuellAbgestimmt>0)echo $aktuellAbgestimmt;else echo "Keine!";echo ")";
		echo "<div id=\"login\">"; //ein ganz normales HTML-Formular
			echo "<h2 type=\"center\">Abstimmungsformular</h2>";
			echo "<form action=\"\" method=\"post\">";
				echo "<label>Karten-ID:</label>";
				echo "<input id=\"name\" name=\"karteAbgestimmtID\" placeholder=\"z.B. 12\" type=\"text\"><br><br>";
				echo "<input name=\"einreichen\" type=\"submit\" value=\" Einreichen \">";
				echo "<span>";echo $error;echo "</span>";
			echo "</form>";
		echo "</div><br>";
		
		echo "<div id=\"login\">";
			echo "<h2 type=\"center\">Eingene Karte vorschlagen</h2>";
			echo "<form action=\"\" method=\"post\">";
			if($karteVorgeschlagen=='n' || $karteVorgeschlagen=='z'){ //wenn der Nutzer schon eine Karte vorgeschlagen hat, muss er sie zuerst zurücknehmen, bevor er eine neue vorschlagen kann
				echo "<label>Name:</label>";
				echo "<input id=\"name\" name=\"name\" type=\"text\">";
				echo "<label>Beschreibung:</label>";
				echo "<input id=\"name\" name=\"beschreibung\" type=\"text\">";
				echo "<label>Farbe:</label>";
				echo "<input id=\"name\" name=\"farbe\" type=\"text\"><br><br>";
				echo "<input name=\"karteVorschlagen\" type=\"submit\" value=\" Vorschlagen \">";
			}
			if($karteVorgeschlagen=='j') //wenn er schon eine Karte vorgeschlagen hat, wird diese ihm hier angezeigt
				echo "<p>Deine aktuelle Karte:<br>".$kartenname.": ".$kartenbeschreibung." (".$kartenfarbe.")</p><input name=\"vorschlagzurueck\" type=\"submit\" value=\" Vorschlag zurücknehmen \">";
			echo "<span>";echo $errorKarteVorschlagen;echo "</span>";
			echo "</form>";
		echo "</div>";
		
	}else{ //wenn der Nutzer noch nicht bestätigt ist, kann er hier einen Bestätigungscode angeben. Dieser wird dann in 'login.php' verarbeitet
		echo "Um Abstimmen zu können, müssen Sie zunächst einen Bestätigungscode eingeben.";
		echo "<div id=\"login\">";
			echo "<h2 type=\"center\">Bestätigungscode:</h2>";
			echo "<form action=\"\" method=\"post\">";
			echo "<label>Zu finden in der Spielebox</label>";
			echo "<input id=\"name\" name=\"code\" placeholder=\"1B7RH8T2\" type=\"text\">";
				echo "<input name=\"accountbestaetigen\" type=\"submit\" value=\" Bestätigen \">";
				echo "<span>";echo $error;echo "</span>";
			echo "</form>";
		echo "</div>";
	}
	mysqli_close($conn); //die Verbindung wird wieder getrennt
?>

<br><br><p>Klicken sie <a href="meinedaten.php">hier</a>, um herauszufinden, welche Daten wir über Sie besitzen.  <!-- Einfach ein Link zu 'meinedaten.php' -->
	
<!-- Ende des Inhalts -->
</td>
  </tr>
</table>
<!-- Beginn Tabelle der Fu&szlig;zeile -->
<div id="footer">
UNBALANCED | <a href="impressum.html" style="color:#ffffff">IMPRESSUM</a> <!-- Der Inhalt der Fußzeile -->

<!-- Ende des Backlink-Codes -->
</div>
<!-- Ende Tabelle der Fu&szlig;zeile -->
</body>
</html>