<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
$error = '';$errorKarteVorschlagen = ''; //die beiden Fehlermeldungen werden initialisiert
if($accountbestaetigt){ //es wird noch mal überprüft, ob der nutzer bestätigt ist
	if(isset($_POST['einreichen']) && !empty($_POST['karteAbgestimmtID'])){ //ist der Knopf 'Einreichen' gedrückt worden und wurde eine ID eingegeben?
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		$karteAbgestimmtID = $_POST['karteAbgestimmtID']; //Daten werden aus dem Formular geholt
		$karteAbgestimmtID=preg_replace("[^0-9]","",$karteAbgestimmtID); //es sind nur Zahlen erlaubt, alle anderen werden durch die Regular Expression ersetzt
		$karteAbgestimmtID=htmlspecialchars($karteAbgestimmtID); //auch wenn theoretisch keine anderen Zeichen als Zahlen mehr möglich sind, werden hier dennoch alle HTML-bezogenen Zeichen ersetzt
		
		$result=mysqli_query($DB,"SELECT imSpiel FROM karten WHERE id='".$karteAbgestimmtID."' LIMIT 1"); //die Zeile mit der angegebenen Karte wird aus der Datenbank geholt
		$row=mysqli_fetch_array($result); //die erste (und einzige (LIMIT 1)) Zeile wird ausgelesen
		if($row['imSpiel']=='n'){ //wenn sie zur Abstimmung bereisteht...
			$sqlAnfrageUpdate = "UPDATE nutzerdaten SET karteAbgestimmtID = ".$karteAbgestimmtID." WHERE username = '".$_SESSION["login_user"]."'"; //...wird beim Nutzer die entsprechende Karte eingetragen
			if ($DB->query($sqlAnfrageUpdate) === TRUE){ //wenn erfolgreich abgestimmt wurde, wird eine Erfolgsnachricht ausgegeben
				$error='Erfolgreich für Karte '.$karteAbgestimmtID." abgestimmt!";
				$aktuellAbgestimmt=$karteAbgestimmtID;
			}else $error='Konnte Stimme nicht abgeben.'; //ansonsten ist es eine Fehlernachricht
		}else $error='Diese Karte steht nicht zur Abstimmung bereit!';
		mysqli_close($DB); //die Verbindung wird wieder getrennt
		
		
	}else if(isset($_POST['karteVorschlagen']) && !empty($_POST['name']) && !empty($_POST['beschreibung']) && !empty($_POST['farbe'])){ //wenn der 'Vorschlagen'-Knopf gedrückt und die anderen Felder ausgefüllt wurden
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		$name = $_POST['name']; //Variable wird aus Formular der Seite geholt
		$beschreibung = $_POST['beschreibung'];
		$farbe = $_POST['farbe'];
		
		$name=preg_replace("[^A-Za-z0-9\s]","",$name); //alle Zeichen, die nicht der Regular Expression entprechen, werden ersetzt,
		$name=htmlspecialchars($name); //genau wie die HTML-Sonderzeichen
		$beschreibung=preg_replace("[^A-Za-z0-9\s]","",$beschreibung);
		$beschreibung=htmlspecialchars($beschreibung);
		$farbe=preg_replace("[^A-Za-z0-9\s]","",$farbe);
		$farbe=htmlspecialchars($farbe);
		
		if($karteVorgeschlagen=='n'){ //wenn der Nutzer noch keine Karte vorgeschlagen hat, wird ein neuer Eintrag in der Tabelle 'karten' angelegt und
			$result=mysqli_query($DB,"INSERT INTO karten (id, name, beschreibung, farbe, vorgeschlagenVon, karteVorgeschlagenOriginal, imSpiel) VALUES (NULL, '".$name."', '".$beschreibung."', '".$farbe."', '".$id."','".$id."', 'v')");
			$sqlAnfrageUpdate=mysqli_query($DB,"UPDATE nutzerdaten SET karteVorgeschlagen = 'j' WHERE username = '".$_SESSION["login_user"]."'"); //dies beim Nutzer eingetragen
		
		}else if($karteVorgeschlagen=='z'){ //wenn er schon eine Karte eingereicht hatte und diese zurückgenommen hat,
			$result=mysqli_query($DB,"UPDATE karten SET name = '".$name."' WHERE ((imSpiel = 'z' OR imSpiel = 'v') AND vorgeschlagenVon = ".$id.")"); //wird der alte Eintrag einfach ersetzt
			$result=mysqli_query($DB,"UPDATE karten SET beschreibung = '".$beschreibung."' WHERE ((imSpiel = 'z' OR imSpiel = 'v') AND vorgeschlagenVon = ".$id.")");
			$result=mysqli_query($DB,"UPDATE karten SET farbe = '".$farbe."' WHERE ((imSpiel = 'z' OR imSpiel = 'v') AND vorgeschlagenVon = ".$id.")");
			$result=mysqli_query($DB,"UPDATE karten SET imSpiel = 'v' WHERE ((imSpiel = 'z' OR imSpiel = 'v') AND vorgeschlagenVon = ".$id.")");
			$result=mysqli_query($DB,"UPDATE nutzerdaten SET karteVorgeschlagen = 'j' WHERE username = '".$_SESSION["login_user"]."'"); //und dies beim Nutzer eingetragen
		}
		header("location: profile.php");
		
	}else if(isset($_POST['vorschlagzurueck'])){ //Knopf 'Zurücknehmen' gedrückt?
		$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
		if($karteVorgeschlagen=='j'){ //hat er bereits eine Karte vorgeschlagen (nur zur Sicherheit, falls er das Dokument bearbeitet haben sollte)
			$result=mysqli_query($DB,"UPDATE karten SET imSpiel = 'z' WHERE ((imSpiel = 'z' OR imSpiel = 'v') AND vorgeschlagenVon = ".$id.")"); //die Karte wird als 'Zurückgenommen' gekennzeichnet
			$sqlAnfrageUpdate=mysqli_query($DB,"UPDATE nutzerdaten SET karteVorgeschlagen = 'z' WHERE username = '".$_SESSION["login_user"]."'"); //ebenso beim Nutzer wird dies eingetragen
		}
		header("location: profile.php");
	}else{
		//keine passende Aktion gefunden, wird nichts gemacht
	}
}
?>
