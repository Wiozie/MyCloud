<!doctype html>
<html lang="pl">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.css">
    <!-- Ikony bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>

<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (isset($_SESSION['user_directory'])) {
    // Pobierz nazwę podkatalogu z parametru GET
    $subdirectory = isset($_GET['subdirectory']) ? $_GET['subdirectory'] : '';

    // Zbuduj pełną ścieżkę do podkatalogu
    $userDirectory = $_SESSION['user_directory'];
    $fullPath = $userDirectory . '/' . $subdirectory;

    // Pobierz tryb wyświetlania z parametru GET lub ustaw domyślny tryb
    $displayMode = isset($_GET['display_mode']) ? $_GET['display_mode'] : 'names';

    // Sprawdź, czy podkatalog istnieje
    if (is_dir($fullPath)) {
        // Wyświetl zawartość podkatalogu
        $files = array_diff(scandir($fullPath), ['.', '..']);
        echo '<p>Lista plików i podkatalogów katalogu ' . $subdirectory . ':</p>';
        echo '<ul>';
        foreach ($files as $file) {
            echo '<li>';
            
            // Wyświetl nazwę pliku
            echo '<p>Nazwa pliku: ' . $file . '</p>';

            // Sprawdź, czy to plik graficzny
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

            if (in_array(strtolower($fileExtension), $imageExtensions) && is_file($fullPath . '/' . $file) && ($displayMode == 'thumbnails' || $displayMode == 'details')) {
                // Jeśli to plik graficzny, wyświetl miniaturę i link do pełnego rozmiaru
                echo '<a href="show_image.php?file=' . $fullPath . '/' . $file . '" target="_blank">';
                echo '<img src="thumbnail_generator.php?file=' . $fullPath . '/' . $file . '" alt="' . $file . '" class="thumbnail">';
                echo '</a>';
            }

            // Sprawdź, czy to plik wideo lub dźwięk
            $videoExtensions = ['mp4', 'webm', 'ogg'];
            $audioExtensions = ['mp3', 'ogg', 'wav'];

            if ((in_array(strtolower($fileExtension), $videoExtensions) || in_array(strtolower($fileExtension), $audioExtensions)) && is_file($fullPath . '/' . $file) && $displayMode == 'thumbnails') {
                // Jeśli to plik wideo lub dźwięk, wyświetl odtwarzacz
                if (in_array(strtolower($fileExtension), $videoExtensions)) {
                    // Wyświetl odtwarzacz wideo
                    echo '<video width="320" height="240" controls>';
                    echo '<source src="' . $fullPath . '/' . $file . '" type="video/mp4">';
                    echo 'Twoja przeglądarka nie obsługuje odtwarzacza wideo.';
                    echo '</video>';
                } elseif (in_array(strtolower($fileExtension), $audioExtensions)) {
                    // Wyświetl odtwarzacz audio
                    echo '<audio controls>';
                    echo '<source src="' . $fullPath . '/' . $file . '" type="audio/mpeg">';
                    echo 'Twoja przeglądarka nie obsługuje odtwarzacza audio.';
                    echo '</audio>';
                }
            } elseif ($displayMode == 'details') {
                // Wyświetl datę utworzenia i rozmiar
                echo '<p>Data utworzenia: ' . date("Y-m-d H:i:s", filectime($fullPath . '/' . $file)) . '</p>';
                echo '<p>Rozmiar: ' . filesize($fullPath . '/' . $file) . ' bajtów</p>';
            }

            // Jeśli tryb wyświetlania to 'names' lub plik nie spełnia warunków powyżej, wyświetl nazwę pliku jako link do pobrania
            if ($displayMode == 'names' || !(in_array(strtolower($fileExtension), $imageExtensions) || in_array(strtolower($fileExtension), $videoExtensions) || in_array(strtolower($fileExtension), $audioExtensions))) {
                echo '<a href="download_file.php?subdirectory=' . $subdirectory . '&filename=' . $file . '" download>' . $file . '</a>';
            }

            // Dodaj ikonę usuwania
            echo '<a href="delete_file.php?subdirectory=' . $subdirectory . '&filename=' . $file . '" class="btn btn-danger ml-2" role="button" onclick="return confirm(\'Czy na pewno chcesz usunąć?\')"><i class="bi bi-trash"></i></a>';
        
            echo '</li>';
        }
        
        echo '</ul>';

        // Formularz przesyłania plików do odpowiedniego podkatalogu
        echo '
        <form action="upload_file.php?subdirectory=' . $subdirectory . '" method="post" enctype="multipart/form-data" class="mt-3">
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fileInput" name="file" required>
                    <label class="custom-file-label" for="fileInput">Wybierz plik</label>
                </div>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Prześlij<i class="bi bi-cloud-upload"></i></button>
                </div>
            </div>
        </form>';

        // Dodaj link do zmiany trybu wyświetlania
        echo '
        <p>Wybierz tryb wyświetlania:</p>
        <ul>
            <li><a href="?subdirectory=' . $subdirectory . '&display_mode=names">Tylko nazwy</a></li>
            <li><a href="?subdirectory=' . $subdirectory . '&display_mode=thumbnails">Miniatury i playery</a></li>
            <li><a href="?subdirectory=' . $subdirectory . '&display_mode=details">Szczegóły (data i rozmiar)</a></li>
        </ul>';

        // Dodaj link do powrotu
        echo '<a href="user_panel.php" class="btn btn-secondary ml-2" role="button"><i class="bi bi-arrow-up-circle"></i> Powrót</a>';
    } else {
        echo '<p>Podkatalog nie istnieje.</p>';
    }
} else {
    // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
    header("Location: index.php");
    exit();
}
?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.all.min.js"></script>
</body>

</html>