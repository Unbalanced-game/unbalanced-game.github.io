<?php
error_reporting(0); //Fehlermeldungen werden ausgeschaltet, um den Nutzer im Fall eines Fehlers nicht zu verwirren
session_start();
$error = '';$error2 = ''; //!!!! SIEHE 'login.php' FÜR DIE KOMMENTARE DIESER SEITE !!!!
if (isset($_POST['anmelden'])){
	if(empty($_POST['username']) || empty($_POST['password'])){
		$error = "Ungültiger Nutzername oder Passwort";
	}else{
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if(preg_match("/[^a-zA-Z0-9\s]/",$password)==1 || preg_match("/[^a-zA-Z0-9\s]/",$username)==1){
			$error = 'Passwort und Nutzer dürfen nur Buchstaben und Zahlen enthalten.';$error2 = '';
		}else{
			$username=preg_replace("[^A-Za-z0-9\s]","",$username);$password = preg_replace("/[^a-zA-Z0-9\s]/", "", $password);
			$username=htmlspecialchars($username);
			$password=htmlspecialchars($password);
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$result=mysqli_query($DB,"SELECT password FROM nutzerdaten WHERE username='".$username."' LIMIT 1");
			$row=mysqli_fetch_array($result);
			if(password_verify($password,$row['password'])){
				$_SESSION["login_user"] = $username;
				header("location: kontakt.php");
			}else{
				$query = $DB->query("SELECT username, password FROM nutzerdaten WHERE username='".$username."' LIMIT 1");
				if($query->num_rows == 0){
					$error = 'Dieser Nutzer existiert nicht.';$error2 = '';
				}else{
					$error = 'Falsches Passwort.';$error2 = '';
				}
			}
		}
	}

}else if(isset($_POST['accounterstellen'])){
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['mail'])){
		$error2 = "Bitte füllen Sie alle Felder aus.";
	}else{
		$username = $_POST['username'];
		$password = $_POST['password'];
		$mail = $_POST['mail'];
		$code = $_POST['code'];
		
		if(preg_match("/[^a-zA-Z0-9\s]/",$password)==1 || preg_match("/[^a-zA-Z0-9\s]/",$username)==1){
			$error = '';$error2 = 'Passwort und Nutzer dürfen nur Buchstaben und Zahlen enthalten.';
		}else if(preg_match("/[A-Za-z-0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]/",$mail)==0){
			$error = '';$error2 = 'Ungültige E-Mail Adresse.';
		}else if(!empty($_POST['code']) && (preg_match("/[^a-zA-Z0-9]/",$code)==1 || !(strlen($code)==8))){
			$error = '';$error2 = 'Bestätigungscode entspricht nicht dem gültigen Muster. Bitte geben Sie ihren unverändert ein.';
		}else{
			$username=htmlspecialchars($username);
			$password=htmlspecialchars($password);
			$password = password_hash($password, PASSWORD_DEFAULT);
			$mail=htmlspecialchars($mail);
			$code=htmlspecialchars($code);
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$username."' LIMIT 1");
			
			if($query->num_rows > 0){
				$error = "";
				$error2 = "Dieser Nutzer existiert bereits.";
			}else{
				$query = $DB->query("SELECT code FROM bestaetigungscodes WHERE code='".$code."' LIMIT 1");
				if($query->num_rows > 0){
						$erstellen = $DB->query("INSERT INTO nutzerdaten (id, username, password, mail, accountbestaetigt, verwendeterCode) VALUES (NULL, '".$username."', '".$password."', '".$mail."', 'j', '".$code."')");
					if($erstellen){
						$error2="Erfolgreich bestätigten Account erstellt!";
						$query = $DB->query("DELETE FROM bestaetigungscodes WHERE bestaetigungscodes.code = '".$code."'");
						$_SESSION["login_user"] = $username;
						header("location: kontakt.php");
					}else{$error2="Ein Fehler beim Erstellen ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}else{
					$erstellen = $DB->query("INSERT INTO nutzerdaten (id, username, password, mail, accountbestaetigt) VALUES (NULL, '".$username."', '".$password."', '".$mail."', 'n')");
					if($erstellen){
						$error2="Erfolgreich Account erstellt!";
						$_SESSION["login_user"] = $username;
						header("location: kontakt.php");
					}else{$error2="Ein Fehler beim Erstellen ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}
			}
			mysqli_close($DB); //die Verbindung wird wieder getrennt
		}
	}
	
}else if(isset($_POST['accountbestaetigen'])){
	if (empty($_POST['code'])){
		$error = "Bitte füllen Sie das Eingabefeld aus.";
	}else{
		$code = $_POST['code'];
		
		if(preg_match("/[^a-zA-Z0-9]/",$code)==1 || !(strlen($code)==8)){
			$error = 'Bestätigungscode entspricht nicht dem gültigen Muster. Bitte geben Sie ihren unverändert ein.';
		}else{
			$code=htmlspecialchars($code);
			$DB = mysqli_connect("localhost", "root", "", "unbalanced"); //Verbindung zur Datenbank wird hergestellt
			
			$query = $DB->query("SELECT username FROM nutzerdaten WHERE username='".$_SESSION["login_user"]."' LIMIT 1");
			
			if($query->num_rows == 0){
				$error = "Dieser Nutzer existiert leider noch nicht. Erstellen Sie mit ihrem Code einen neuen Nutzer.";
			}else{
				$query = $DB->query("SELECT code FROM bestaetigungscodes WHERE code='".$code."' LIMIT 1");
				if($query->num_rows > 0){
						$erstellen = $DB->query("UPDATE nutzerdaten SET accountbestaetigt = \"j\" WHERE nutzerdaten.username = '".$_SESSION["login_user"]."'");
					if($erstellen){
						$erstellen = $DB->query("UPDATE nutzerdaten SET verwendeterCode = \"".$code."\" WHERE nutzerdaten.username = '".$_SESSION["login_user"]."'");
						$query = $DB->query("DELETE FROM bestaetigungscodes WHERE bestaetigungscodes.code = '".$code."'");
						$error2="Erfolgreich Account bestätigt!";
						header("location: kontakt.php");
					}else{$error = "Ein Fehler ist aufgetreten. Bitte melden Sie sich bei yan2014wittmann@gmail.com";}
				}
			}
			mysqli_close($DB); //die Verbindung wird wieder getrennt
		}
	}
}
?>