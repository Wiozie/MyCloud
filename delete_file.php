<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (isset($_SESSION['user_directory'])) {
    $userDirectory = $_SESSION['user_directory'];

    // Sprawdź, czy przekazano nazwę pliku do usunięcia
    if (isset($_GET['filename'])) {
        $filename = $_GET['filename'];
        $filePath = $userDirectory . '/' . $filename;

        // Sprawdź, czy plik istnieje
        if (file_exists($filePath)) {
            // Usuń plik lub katalog
            deleteFileOrDirectory($filePath);
            echo '<p>Plik lub katalog "' . $filename . '" został usunięty.</p>';
        } else {
            echo '<p>Plik lub katalog o nazwie "' . $filename . '" nie istnieje.</p>';
        }
    } else {
        echo '<p>Nieprawidłowe żądanie usunięcia pliku lub katalogu.</p>';
    }
} else {
    // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
    header("Location: index.php");
    exit();
}

// Funkcja usuwająca plik lub katalog (zdefiniowana na początku kodu)
function deleteFileOrDirectory($path) {
    if (is_file($path)) {
        unlink($path); // Usuń plik
    } elseif (is_dir($path)) {
        // Usuń zawartość katalogu rekurencyjnie
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            deleteFileOrDirectory($path . '/' . $file);
        }
        rmdir($path); // Usuń pusty katalog
    }
}
?>
