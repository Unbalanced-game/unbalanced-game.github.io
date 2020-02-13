<?php
$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
session_start();
$user_check = $_SESSION['login_user'];
$query = "SELECT id, username, mail, karteAbgestimmtID, typ, accountbestaetigt, karteVorgeschlagen FROM nutzerdaten WHERE username = '$user_check'"; //alle Daten des Nutzers werden aus der Datebank geholt
$ses_sql = mysqli_query($DB, $query);
$row = mysqli_fetch_assoc($ses_sql);
$login_session = $row['username']; //und in Variablen gespeichert
$mail = $row['mail'];
$aktuellAbgestimmt = $row['karteAbgestimmtID'];
$typ = $row['typ'];
$accountbestaetigt = $row['accountbestaetigt'];
$karteVorgeschlagen = $row['karteVorgeschlagen'];
$id = $row['id'];

$query = "SELECT name,beschreibung,farbe FROM karten WHERE (imSpiel = 'v' AND vorgeschlagenVon = ".$id.")"; //die Karte, für die der Nutzer abgestimmt hat, wird ebenfalls geholt
$kartenAbfrage = mysqli_query($DB, $query);
$kartendaten = mysqli_fetch_assoc($kartenAbfrage);
$kartenname = $kartendaten['name']; //und abgespeichert
$kartenbeschreibung = $kartendaten['beschreibung'];
$kartenfarbe = $kartendaten['farbe'];
?>