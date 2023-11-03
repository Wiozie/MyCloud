<?php
// show_image.php

if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Sprawdź, czy plik istnieje
    if (file_exists($file)) {
        // Ustaw nagłówek do wyświetlenia obrazu
        header('Content-Type: image/jpeg'); // Zmień na odpowiedni format obrazu

        // Wyświetl zawartość pliku
        readfile($file);
        exit();
    }
}
?>
