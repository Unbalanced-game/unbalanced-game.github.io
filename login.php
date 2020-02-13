<?php
session_start();
$error = '';$error2 = ''; //die Fehlernachrichten werden initialisiert
if (isset($_POST['anmelden'])){ //der Nutzer will sich anmelden
	if(empty($_POST['username']) || empty($_POST['password'])){ //hat er alle Felder ausgefüllt?
		$error = "Ungültiger Nutzername oder Passwort";
	}else{
		$username = $_POST['username']; //die Login-Daten werden aus dem Formular der Seite geholt
		$password = $_POST['password'];
		
		if(preg_match("/[^a-zA-Z0-9\s]/",$password)==1 || preg_match("/[^a-zA-Z0-9\s]/",$username)==1){ //es wird nach invaliden Zeichen geprüft
			$error = 'Passwort und Nutzer dürfen nur Buchstaben, Zahlen und Leerzeichen enthalten.';$error2 = ''; //falls welche enthalten sein sollten, wird eine Fehlermeldung ausgegeben
		}else{
			$username = preg_replace("[^A-Za-z0-9\s]","",$username); //zur Sicherheit werden nun die invaliden Zeichen aus den Login-Daten gelöscht
			$password = preg_replace("/[^a-zA-Z0-9\s]/", "", $password);
			$username = htmlspecialchars($username); //und die HTML-Sonderzeichen eretzt
			$password = htmlspecialchars($password);
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$result=mysqli_query($DB,"SELECT password FROM nutzerdaten WHERE username='".$username."' LIMIT 1"); //die Zeile des Nutzers, der sich versucht anzumelden, wird ausgewählt
			$row=mysqli_fetch_array($result);
			if(password_verify($password,$row['password'])){ //wenn das Passwort gleich dem Hash ist, der in der Datenbank steht, darf der Nutzer sich einloggen
				$_SESSION["login_user"] = $username; //die Session-Variable 'login_user' wird auf den Nutzernamen gesetzt
				header("location: profile.php"); //und der Nutzer wird an sein Profil weitergeleitet
			}else{ //wenn das Passwort falsch war, gibt es zwei mögliche Szenarios:
				$query = $DB->query("SELECT username, password FROM nutzerdaten WHERE username='".$username."' LIMIT 1");
				if($query->num_rows == 0){ //das Passwort war falsch, da es nicht existiert, der Nutzername ist also flasch
					$error = 'Dieser Nutzer existiert nicht.';$error2 = '';
				}else{ //oder das Passwort ist einfach tatsächlich falsch
					$error = 'Falsches Passwort.';$error2 = '';
				}
			}
		}
	}
	include('session.php'); //hier werden noch ein mal die Daten des angemeldeten Nutzers geholt. Wenn der Nutzer die ID 0 hat, also der erste Account ist, der erstellt wird,
	if($id==0){ //dann wird er automatisch zum Admin gemacht
		$makeAdmin = mysqli_query($DB, "UPDATE nutzerdaten SET typ = 'admin' WHERE id = 0");
	}

}else if(isset($_POST['accounterstellen'])){ //der Nutzer will einen neuen Account erstellen
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['mail'])){ //es wird geprüft, ob alle Felder ausgefüllt sind
		$error2 = "Bitte füllen Sie alle Felder aus.";
	}else{ //wenn alle Felder ausgefüllt sind, kann der Account erstellt werden
		$username = $_POST['username']; //die Registrierungs-Daten werden aus dem Formular der Seite geholt
		$password = $_POST['password'];
		$mail = $_POST['mail'];
		$code = $_POST['code'];
		
		if(preg_match("/[^a-zA-Z0-9\s]/",$password)==1 || preg_match("/[^a-zA-Z0-9\s]/",$username)==1){ //wenn der Nutzername oder das Passwort nicht dem richtigen Muster entsprechen
			$error = '';$error2 = 'Passwort und Nutzer dürfen nur Buchstaben und Zahlen enthalten.'; //wird eine Fehlermeldung ausgegeben
		}else if(preg_match("/[A-Za-z-0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]/",$mail)==0){ //die E-Mail brauche eine etwas größere Regular Expression, funktioniert aber genau gleich
			$error = '';$error2 = 'Ungültige E-Mail Adresse.';
		}else if(!empty($_POST['code']) && (preg_match("/[^a-zA-Z0-9]/",$code)==1 || !(strlen($code)==8))){ //es muss kein Code eingegeben werden, aber wenn, dann muss er einigen Kriterien entsprechen
			$error = '';$error2 = 'Bestätigungscode entspricht nicht dem gültigen Muster. Bitte geben Sie ihren unverändert ein.';
		}else{ //wenn keiner der Fälle eingetroffen ist, kann der Account erstellt werden
			$username=htmlspecialchars($username); //zur Sicherheit werden auch hier die HTML-Sonderzeichen ersetzt
			$password=htmlspecialchars($password);
			$password = password_hash($password, PASSWORD_DEFAULT); //das Passwort wird gehashed, um später in der Datenbank abgelegt werden zu können
			$mail=htmlspecialchars($mail);
			$code=htmlspecialchars($code);
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$username."' LIMIT 1"); //es muss zunächst geprüft werden, ob der Nutzer bereits existiert
			
			if($query->num_rows > 0){ //wenn es ihn bereits gibt, darf nicht noch einer mit dem gleichen Namen erstellt werden
				$error = "";$error2 = "Dieser Nutzer existiert bereits.";
			}else{ //ansonsten kann weitergemacht werden
				$query = $DB->query("SELECT code FROM bestaetigungscodes WHERE code='".$code."' LIMIT 1"); //es wird geschaut, ob es den eingegebenen Bestätigungscode gibt
				if($query->num_rows > 0){ //wenn ja, soll ein Account mit 'j' bei accountbestaetigt erstellt werden:
						$erstellen = $DB->query("INSERT INTO nutzerdaten (id, username, password, mail, accountbestaetigt, verwendeterCode) VALUES (NULL, '".$username."', '".$password."', '".$mail."', 'j', '".$code."')");
					if($erstellen){ //wenn der Account erfolgreich erstellt wurde
						$error2="Erfolgreich bestätigten Account erstellt!"; //wird eine Nachricht ausgegeben,
						$query = $DB->query("DELETE FROM bestaetigungscodes WHERE bestaetigungscodes.code = '".$code."'"); //der Code aus der Tabelle gelöscht,
						$_SESSION["login_user"] = $username; //die Session-Variable 'login_user' wird auf den Nutzernamen gesetzt
						header("location: profile.php"); //und der Nutzer wird weitergeleitet
					}else{$error2="Ein Fehler beim Erstellen ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}else{ //falls es den Code nicht gibt, wird ein 'n' bei accountbestaetigt eingefügt
					$erstellen = $DB->query("INSERT INTO nutzerdaten (id, username, password, mail, accountbestaetigt) VALUES (NULL, '".$username."', '".$password."', '".$mail."', 'n')");
					if($erstellen){ //ansonsten passiert das gleiche, nur, dass kein Code gelöscht wird
						$error2="Erfolgreich Account erstellt!";
						$_SESSION["login_user"] = $username;
						header("location: profile.php");
					}else{$error2="Ein Fehler beim Erstellen ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}
			}
			mysqli_close($DB); //die Verbindung wird wieder getrennt
		}
	}
	
}else if(isset($_POST['accountbestaetigen'])){ //soll der Account im nachhinein bestätigt werden?
	if (empty($_POST['code'])){ //dann muss das Feld mit dem Bestätigungscode ausgefüllt sein
		$error = "Bitte füllen Sie das Eingabefeld aus.";
	}else{
		$code = $_POST['code'];//der Code wird aus dem Formular der Seite geholt
		
		if(preg_match("/[^a-zA-Z0-9]/",$code)==1 || !(strlen($code)==8)){ //gleiche Bedingung wie oben bei dem Registrierungsvorgang
			$error = 'Bestätigungscode entspricht nicht dem gültigen Muster. Bitte geben Sie ihren unverändert ein.';
		}else{ //ist vom Formalen ein gültiger Code
			$code=htmlspecialchars($code); //zur Sicherheit werden alle HTML-Sonderzeichen ersetzt
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$_SESSION["login_user"]."' LIMIT 1"); //der Nutzername des aktuellen Nutzers wird geholt...
			
			if($query->num_rows == 0){ //... um zu überprüfen, ob es ihn gibt. Einfach nur so, zur Sicherheit
				$error = "Dieser Nutzer existiert leider noch nicht. Erstellen Sie mit ihrem Code einen neuen Nutzer.";
			}else{
				$query = $DB->query("SELECT code FROM bestaetigungscodes WHERE code='".$code."' LIMIT 1"); //gibt es den Code?
				if($query->num_rows > 0){ //wenn ja, soll der Nutzer ein 'j' bei accountbestaetigt eingetragen bekommen
						$erstellen = $DB->query("UPDATE nutzerdaten SET accountbestaetigt = \"j\" WHERE nutzerdaten.username = '".$_SESSION["login_user"]."'");
					if($erstellen){ //im Erfolgfall:
						$erstellen = $DB->query("UPDATE nutzerdaten SET verwendeterCode = \"".$code."\" WHERE nutzerdaten.username = '".$_SESSION["login_user"]."'"); //der verwendete Code wird beim Nutzer eingetragen
						$query = $DB->query("DELETE FROM bestaetigungscodes WHERE bestaetigungscodes.code = '".$code."'"); //und aus der Code-Tabelle gelöscht
						$error2="Erfolgreich Account bestätigt!";
						header("location: profile.php");
					}else{$error = "Ein Fehler ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}
			}
			mysqli_close($DB); //die Verbindung wird wieder getrennt
		}
	}
}
?>