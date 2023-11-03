<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (isset($_SESSION['user_directory'])) {
    $userDirectory = $_SESSION['user_directory'];

    // Sprawdź, czy użytkownik jest w katalogu macierzystym
    $isHomeDirectory = empty($_GET['subdirectory']);

    // Sprawdź, czy formularz został przesłany
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pobierz nazwę nowego podkatalogu z formularza
        $newSubdirectory = isset($_POST['new_subdirectory']) ? $_POST['new_subdirectory'] : '';

        // Sprawdź, czy nazwa podkatalogu jest niepusta
        if (!empty($newSubdirectory)) {
            // Utwórz nowy podkatalog
            $newSubdirectoryPath = $userDirectory . '/' . $newSubdirectory;

            if (!file_exists($newSubdirectoryPath) && mkdir($newSubdirectoryPath)) {
                echo '<p>Podkatalog "' . $newSubdirectory . '" został utworzony.</p>';
            } else {
                echo '<p>Nie udało się utworzyć podkatalogu.</p>';
            }
        } else {
            echo '<p>Nazwa podkatalogu nie może być pusta.</p>';
        }
    }

    // Wyświetl formularz tworzenia nowego podkatalogu
    if ($isHomeDirectory) {
        echo '<form method="POST" action="">
            <label>Nazwa nowego podkatalogu:</label>
            <input type="text" name="new_subdirectory" required>
            <button type="submit">Utwórz</button>
        </form>';
    }
} else {
    // Użytkownik nie jest zalogowany - przekieruj na stronę logowania
    header("Location: index.php");
    exit();
}
?>
