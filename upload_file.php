<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Sprawdź, czy użytkownik jest zalogowany
    if (isset($_SESSION['user_directory'])) {
        $userDirectory = $_SESSION['user_directory'];

        // Pobierz podkatalog z parametru GET
        $subdirectory = isset($_GET['subdirectory']) ? $_GET['subdirectory'] : '';
        $targetDirectory = $userDirectory . '/' . $subdirectory;

        $fileName = basename($_FILES['file']['name']);
        $targetPath = $targetDirectory . '/' . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            echo '<p>Plik został pomyślnie przesłany.</p>';
        } else {
            echo '<p>Błąd podczas przesyłania pliku.</p>';
        }
    } else {
        // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
        header("Location: index.php");
        exit();
    }
} else {
    // Nieprawidłowe żądanie, przekieruj gdzie indziej lub wyświetl komunikat błędu
    header("Location: index.php");
    exit();
}
?>
