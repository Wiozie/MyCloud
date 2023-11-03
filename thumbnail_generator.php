<?php
// thumbnail_generator.php

if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Sprawdź, czy plik istnieje
    if (file_exists($file)) {
        // Ustaw rozmiary miniatury (możesz dostosować)
        $thumbWidth = 100;
        $thumbHeight = 100;

        // Pobierz oryginalne wymiary obrazka
        list($originalWidth, $originalHeight) = getimagesize($file);

        // Utwórz obraz miniatury
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Wczytaj oryginalny obraz
        $source = imagecreatefromjpeg($file); // Zmień na odpowiedni format obrazu

        // Stwórz miniaturę
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);

        // Ustaw nagłówek do wyświetlenia obrazu
        header('Content-Type: image/jpeg'); // Zmień na odpowiedni format obrazu

        // Wyświetl miniaturę
        imagejpeg($thumb);

        // Zwolnij pamięć
        imagedestroy($thumb);
        imagedestroy($source);
        exit();
    }
}
?>
