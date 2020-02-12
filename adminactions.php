<?php
$nachricht = ''; //die (Fehler-)Nachrichten werden initialisert
$nachricht2 = '';
$nachricht3 = '';
if(isset($_POST['karteDazu'])){ //es soll die Karte mit den meisten Stimmen hinzugefügt werden
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	
	//Welche Karte hat gewonnen?
	$result=mysqli_query($DB,"SELECT COUNT(karteAbgestimmtID) as Stimmen,karteAbgestimmtID as ID FROM nutzerdaten,karten WHERE karteAbgestimmtID!=0 && nutzerdaten.karteAbgestimmtID=karten.id && karten.imSpiel='n' GROUP BY karteAbgestimmtID ORDER BY Stimmen DESC");
	$row=mysqli_fetch_array($result); //Ergenis wird geholt, die ID der Karte und die Anzahl der Stimmen der Gewinnerkarte stehen in der ersten Zeile
	$platz1ID=$row['ID'];
	$platz1Stimmen=$row['Stimmen'];
	
	//hole Daten der Karte
	$result=mysqli_query($DB,"SELECT name,beschreibung,farbe FROM karten WHERE id=".$platz1ID); //Daten der Karte werden aus der Datenbank geholt, damit sie ausgegeben werden können
	$row=mysqli_fetch_array($result);
	$name=$row['name'];
	$beschreibung=$row['beschreibung'];
	$farbe=$row['farbe'];
	$nachricht='Gewonnen hat Karte '.$name.' ('.$beschreibung.') ('.$farbe.') mit '.$platz1Stimmen.' Stimme(n)!'; //Text wird ausgegeben
	
	//alle möglichen Dinge setzen...
	$result=mysqli_query($DB,"UPDATE karten SET imSpiel = 'j' WHERE karten.id = ".$platz1ID); //Kartenstatus der Gewinnerkarte wird aktualisiert
	$result=mysqli_query($DB,"UPDATE nutzerdaten SET karteAbgestimmtID = 0"); //alle Stimmen werden zurückgesetzt
	mysqli_close($DB); //die Verbindung wird wieder getrennt
	
}else if(isset($_POST['neueAbstimmung']) && !empty($_POST['name']) && !empty($_POST['beschreibung']) && !empty($_POST['farbe'])){ ////es soll eine Karte zur Abstimmung hinzugefügt werden
	$name = $_POST['name']; //die Daten werden aus dem Formular der Seite geholt
	$beschreibung = $_POST['beschreibung'];
	$farbe = $_POST['farbe'];
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"INSERT INTO karten (id, name, beschreibung, farbe) VALUES (NULL, '".$name."', '".$beschreibung."', '".$farbe."')"); //die neue Karte wird zur Tabelle 'karten' hinzugefügt
	$nachricht2='Karte zur Abstimmung hinzugefügt!';
	mysqli_close($DB); //die Verbindung wird wieder getrennt
	
}else if(isset($_POST['loescheAbstimmung']) && !empty($_POST['name'])){ //es soll eine Karte gelöscht werden
	$name = $_POST['name']; //die Daten werden aus dem Formular der Seite geholt
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"DELETE FROM karten WHERE karten.name = '".$name."';"); //die entsprechende Karte wird aus der Tabege 'karten' gelöscht
	$nachricht2='Karte gelöscht!';
	mysqli_close($DB); //die Verbindung wird wieder getrennt
	
}else if(isset($_POST['vorgeschlageneKarteZurAbstimmung']) && !empty($_POST['idvorschlag'])){ //eine der vorgeschlagenen Karten soll zur Abstimmung hinzugefügt werden
	$idvorschlag = $_POST['idvorschlag']; //die Daten werden aus dem Formular der Seite geholt
	$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
	$result=mysqli_query($DB,"UPDATE karten SET imSpiel = 'n' WHERE karteVorgeschlagenOriginal = ".$idvorschlag); //die betroffene Karte wird zur Abstimmung bereitgestellt ('n')
	$result=mysqli_query($DB,"UPDATE karten SET vorgeschlagenVon = '0' WHERE karteVorgeschlagenOriginal = ".$idvorschlag);
	$result=mysqli_query($DB,"UPDATE nutzerdaten SET karteVorgeschlagen = 'n' WHERE id = '".$idvorschlag."'"); //der Nutzer hat nun keine Karte mehr vorgeschlagen und kann eine neue vorgeschlagen
	$nachricht3='Erfolgreich Karte zur Abstimmung hinzugefügt!';
	mysqli_close($DB); //die Verbindung wird wieder getrennt
}
?>