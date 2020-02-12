<?php
session_start();
if(session_destroy()) //die Session wird zerstört
header("Location: anmelden.php"); //und der Nutzer wird wieder an 'anmelden.php' geleitet
?>