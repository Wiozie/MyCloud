<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (isset($_SESSION['user_directory'])) {
    // Pobierz nazwę podkatalogu i nazwę pliku z parametrów GET
    $subdirectory = isset($_GET['subdirectory']) ? $_GET['subdirectory'] : '';
    $filename = isset($_GET['filename']) ? $_GET['filename'] : '';

    // Zbuduj pełną ścieżkę do pliku
    $userDirectory = $_SESSION['user_directory'];
    $filePath = $userDirectory . '/' . $subdirectory . '/' . $filename;

    // Sprawdź, czy plik istnieje
    if (file_exists($filePath)) {
        // Ustaw nagłówki do obsługi pobierania pliku
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));

        // Wysyłanie zawartości pliku
        readfile($filePath);
        exit();
    } else {
        echo 'Plik nie istnieje.';
    }
} else {
    // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
    header("Location: index.php");
    exit();
}
?>
