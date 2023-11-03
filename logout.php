<?php
session_start(); // Rozpocznij sesję, aby móc skorzystać z zmiennych sesyjnych
$_SESSION = array(); // Zeruj zmienne sesyjne
session_destroy(); // Zniszcz sesję
header('Location: index.php'); // Przekieruj użytkownika do strony logowania
exit;
?>