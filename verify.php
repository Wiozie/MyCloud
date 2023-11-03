<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
session_start(); // Rozpocznij sesję

// Funkcja do logowania prób logowania
function logLoginAttempt($username, $ip, $success) {
    $link = mysqli_connect("mysql01.wizawi.beep.pl", "wz_z5", "waz_z5", "wz_z5"); // połączenie z BD – wpisać swoje dane
    if (!$link) {
        echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit();
    }

    mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków

    $successValue = $success ? 1 : 0;

    // Dodaj logowanie do tabeli guests
    $insertQuery = "INSERT INTO guests (user, ip_address, success) VALUES ('$username', '$ip', $successValue)";
    mysqli_query($link, $insertQuery);
}

// Funkcja do blokowania konta na 1 minutę
function blockAccount($username) {
    $link = mysqli_connect("mysql01.wizawi.beep.pl", "wz_z5", "waz_z5", "wz_z5"); // połączenie z BD – wpisać swoje dane
    if (!$link) {
        echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit();
    }

    mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków

    $blockUntil = date('Y-m-d H:i:s', strtotime('+1 minute'));
    // Zablokuj konto na 1 minutę
    $blockAccountQuery = "UPDATE users SET blocked_until = '$blockUntil' WHERE user = '$username'";
    mysqli_query($link, $blockAccountQuery);
}


// Sprawdź, czy dane POST są ustawione
if (isset($_POST['login'], $_POST['pass'])) {
    $user = htmlentities($_POST['login'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $user
    $pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass

    $link = mysqli_connect("mysql01.wizawi.beep.pl", "wz_z5", "waz_z5", "wz_z5"); // połączenie z BD – wpisać swoje dane
    if (!$link) {
        echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit();
    }

    mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
    $result = mysqli_query($link, "SELECT * FROM users WHERE user='$user'"); // wiersza, w którym login=login z formularza
    $rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD

    if (!$rekord) { // Jeśli brak, to nie ma użytkownika o podanym loginie
        mysqli_close($link); // zamknięcie połączenia z BD
        echo "Brak użytkownika o takim loginie !"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
    } else { // jeśli $rekord istnieje
        if ($rekord['blocked_until'] && $rekord['blocked_until'] > date('Y-m-d H:i:s')) {
            echo "Twoje konto jest zablokowane. Spróbuj ponownie później.";
            mysqli_close($link);
            exit();
        }
        if ($rekord['password'] == $pass) { // czy hasło zgadza się z BD
            $_SESSION['loggedin'] = true; // Ustaw zmienną sesyjną na true
            $_SESSION['login'] = $user; // Ustaw zmienną sesyjną login na wartość loginu użytkownika
            $_SESSION['user_directory'] = "users/$user";
            logLoginAttempt($user, $_SERVER['REMOTE_ADDR'], true);
        
            if ($rekord['blocked_until'] && $rekord['failed_login_attempts'] > 0) {
                $checkIpQuery = "SELECT ip_address FROM break_ins WHERE user = '$user' ORDER BY attempt_time DESC LIMIT 1";
                $ipResult = mysqli_query($link, $checkIpQuery);
        
                if ($ipResult) {
                    $ipRow = mysqli_fetch_assoc($ipResult);
                    $ip_address = isset($ipRow['ip_address']) ? $ipRow['ip_address'] : 'N/A';
                } else {
                    $ip_address = 'N/A';
                }
        
                // Display warning message
                echo "Twoje konto było zablokowane do: " . $rekord['blocked_until'] . ".\nOstatnie błędne logowanie z IP: " . $ip_address;
        
                // Update failed login attempts and unblock the account
                $updateAttemptsQuery = "UPDATE users SET failed_login_attempts = 0, blocked_until = NULL WHERE user = '$user'";
                mysqli_query($link, $updateAttemptsQuery);

                exit();
            } else {
                // Redirect to user_panel.php
                header('Location: user_panel.php');
                exit();
            }
        } else {
            echo "Błąd w haśle !"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
            logLoginAttempt($user, $_SERVER['REMOTE_ADDR'], false); // Logowanie nieudanej próby logowania
            // Zwiększ liczbę błędnych logowań
            $updateAttemptsQuery = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE user = '$user'";
            mysqli_query($link, $updateAttemptsQuery);

            $attemptsCount = $rekord['failed_login_attempts'] + 1;

            if ($attemptsCount > 0 && $attemptsCount % 3 == 0) {
                // Dodaj błędne logowanie do tabeli break_ins
                $insertBreakInQuery = "INSERT INTO break_ins (user, ip_address, attempt_time) VALUES ('$user', '$_SERVER[REMOTE_ADDR]', NOW())";
                mysqli_query($link, $insertBreakInQuery);
                // Zablokuj konto na 1 minutę
                blockAccount($user);
                echo "Twoje konto zostało zablokowane na 1 minutę. Spróbuj ponownie później.";
                mysqli_close($link);
                exit();
            }
        }
    }
    mysqli_close($link);
} else {
    echo "Brak danych w formularzu!";
}
?>
