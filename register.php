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
</head>

<body>
<?php
session_start();

// Funkcja do sprawdzania, czy login spełnia warunki
function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9]+$/', $username);
}

// Sprawdź, czy dane przekazane w zapytaniu są ustawione
if (isset($_POST['login'], $_POST['pass'], $_POST['repeat_pass'])) {
    $user = htmlentities($_POST['login'], ENT_QUOTES, "UTF-8");
    $pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");
    $repeatPass = htmlentities($_POST['repeat_pass'], ENT_QUOTES, "UTF-8");

    // Sprawdź, czy hasła są identyczne
    if ($pass === $repeatPass) {
        // Sprawdź, czy login spełnia warunki
        if (isValidUsername($user)) {
            $link = mysqli_connect("mysql01.wizawi.beep.pl", "wz_z5", "waz_z5", "wz_z5");
            if (!$link) {
                die("Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error());
            }

            mysqli_query($link, "SET NAMES 'utf8'");
            
            // Sprawdź, czy login jest unikalny
            $checkUserQuery = mysqli_query($link, "SELECT user FROM users WHERE user = '$user'");
            if (mysqli_num_rows($checkUserQuery) == 0) {
                // Login jest unikalny, można kontynuować proces rejestracji
                $result = mysqli_query($link, "INSERT INTO users (user, password) VALUES ('$user', '$pass')");

                // Sprawdź, czy operacja zakończyła się sukcesem
                if ($result) {
                    // Utwórz katalog macierzysty
                    $userDirectory = "users/$user";
                    mkdir($userDirectory);

                    // Zapisz ścieżkę katalogu macierzystego w sesji
                    $_SESSION['user_directory'] = $userDirectory;

                    echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "success",
                                    title: "Rejestracja udana!",
                                    text: "Teraz możesz się zalogować."
                                }).then(() => {
                                    window.location.href = "index.php";
                                });
                            });
                          </script>';
                } else {
                    echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "error",
                                    title: "Błąd rejestracji",
                                    text: "Coś poszło nie tak. Spróbuj ponownie."
                                }).then(() => {
                                    window.location.href = "registration.php";
                                });
                            });
                          </script>';
                }
            } else {
                // Login już istnieje
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Błąd rejestracji",
                                text: "Login już istnieje. Wybierz inny login."
                            }).then(() => {
                                window.location.href = "registration.php";
                            });
                        });
                      </script>';
            }

            mysqli_close($link);
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Błąd rejestracji",
                            text: "Login zawiera niedozwolone znaki. Spróbuj ponownie."
                        }).then(() => {
                            window.location.href = "registration.php";
                        });
                    });
                  </script>';
        }
    } else {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Błąd rejestracji",
                        text: "Hasła nie są identyczne. Spróbuj ponownie."
                    }).then(() => {
                        window.location.href = "registration.php";
                    });
                });
              </script>';
    }
} else {
    // Dane nie są ustawione - przekieruj na stronę rejestracji
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Błąd rejestracji",
                    text: "Brak wymaganych danych. Spróbuj ponownie."
                }).then(() => {
                    window.location.href = "registration.php";
                });
            });
          </script>';
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