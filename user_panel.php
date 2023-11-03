<!-- index.php lub directory.php -->
<!doctype html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Twoje Pliki</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">WAZ</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="min-width: 120px;">
                        <a class="dropdown-item" href="logout.php">Wyloguj</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
<form action="upload_file.php" method="post" enctype="multipart/form-data" class="mt-3">
    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="fileInput" name="file" required>
            <label class="custom-file-label" for="fileInput">Wybierz plik</label>
        </div>
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Prześlij<i class="bi bi-cloud-upload"></i></button>
        </div>
    </div>
</form>
<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (isset($_SESSION['user_directory'])) {
    $userDirectory = $_SESSION['user_directory'];

    // Sprawdź, czy użytkownik jest w katalogu macierzystym
    $isHomeDirectory = empty($_GET['subdirectory']);

    // Pobierz tryb wyświetlania z parametru GET lub ustaw domyślny tryb
    $displayMode = isset($_GET['display_mode']) ? $_GET['display_mode'] : 'names';

    // Wyświetl listę plików i podkatalogów katalogu macierzystego
    $files = array_diff(scandir($userDirectory), ['.', '..']);

    echo '<p>Lista plików i podkatalogów katalogu ';
    if ($isHomeDirectory) {
        echo 'macierzystego';
        echo '<a href="create_subdirectory.php" class="btn btn-primary ml-2" role="button"><i class="bi bi-plus-circle"></i></a>';
    } else {
        echo 'podkatalogu ' . $_GET['subdirectory'];
    }

    // Dodaj menu rozwijane do wyboru trybu wyświetlania
    echo '
    <form action="" method="get" class="ml-2">
        <label for="displayMode">Tryb wyświetlania:</label>
        <select id="displayMode" name="display_mode" onchange="this.form.submit()">
            <option value="names" ' . ($displayMode == 'names' ? 'selected' : '') . '>Nazwy plików</option>
            <option value="thumbnails" ' . ($displayMode == 'thumbnails' ? 'selected' : '') . '>Miniatury i playery</option>
            <option value="details" ' . ($displayMode == 'details' ? 'selected' : '') . '>Szczegóły (data utworzenia i rozmiar)</option>
        </select>
    </form>';

    echo ':</p>';

    if (empty($files)) {
        echo '<p>Twój katalog ';
        if ($isHomeDirectory) {
            echo 'macierzysty';
        } else {
            echo 'podkatalogu ' . $_GET['subdirectory'];
        }
        echo ' jest pusty.</p>';
    } else {
        echo '<ul>';
        foreach ($files as $file) {
            echo '<li>';
            $filePath = $userDirectory . '/' . $file;
            if (is_file($filePath)) {
                // Wyświetl informacje w zależności od trybu wyświetlania
                switch ($displayMode) {
                    case 'thumbnails':
                        // Wyświetl miniaturę i link do pełnego rozmiaru dla plików graficznych
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($fileExtension, $imageExtensions) && is_file($filePath)) {
                            echo '<a href="show_image.php?file=' . $filePath . '" target="_blank">';
                            echo '<img src="thumbnail_generator.php?file=' . $filePath . '" alt="' . $file . '" class="thumbnail">';
                            echo '</a>';
                        } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg']) && is_file($filePath)) {
                            // Wyświetl odtwarzacz wideo dla plików wideo
                            echo '<video width="320" height="240" controls>';
                            echo '<source src="' . $filePath . '" type="video/mp4">';
                            echo 'Twoja przeglądarka nie obsługuje odtwarzacza wideo.';
                            echo '</video>';
                        } elseif (in_array($fileExtension, ['mp3', 'ogg', 'wav']) && is_file($filePath)) {
                            // Wyświetl odtwarzacz audio dla plików audio
                            echo '<audio controls>';
                            echo '<source src="' . $filePath . '" type="audio/mpeg">';
                            echo 'Twoja przeglądarka nie obsługuje odtwarzacza audio.';
                            echo '</audio>';
                        } else {
                            // Jeśli plik nie jest grafiką, wideo ani audio, wyświetl nazwę pliku
                            echo '<p>Nazwa pliku: ' . $file . '</p>';
                        }
                        break;

                    case 'details':
                        // Wyświetl nazwę pliku, datę utworzenia i rozmiar
                        echo '<p>Nazwa pliku: ' . $file . '</p>';
                        echo '<p>Data utworzenia: ' . date("Y-m-d H:i:s", filectime($filePath)) . '</p>';
                        echo '<p>Rozmiar: ' . filesize($filePath) . ' bajtów</p>';
                        break;

                    default:
                        // Domyślnie wyświetl nazwę pliku
                        echo '<p>Nazwa pliku: ' . $file . '</p>';
                        echo '<a href="download_file.php?filename=' . urlencode($file) . '" download class="btn btn-primary ml-2"><i class="bi bi-download"></i> Pobierz</a>';
                        break;
                }

                // Dodaj ikonę usuwania
                echo '<a href="delete_file.php?filename=' . $file . '" class="btn btn-danger ml-2" role="button" onclick="return confirm(\'Czy na pewno chcesz usunąć?\')"><i class="bi bi-trash"></i></a>';
            } elseif (is_dir($filePath)) {
                // Wyświetl nazwę katalogu jako link do wejścia
                echo '<a href="directory.php?subdirectory=' . $file . '">' . $file . '</a>';
                echo '<a href="delete_file.php?filename=' . $file . '" class="btn btn-danger ml-2" role="button" onclick="return confirm(\'Czy na pewno chcesz usunąć?\')"><i class="bi bi-trash"></i></a>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }
} else {
    // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
    header("Location: index.php");
    exit();
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.all.min.js"></script>

</body>

</html>
