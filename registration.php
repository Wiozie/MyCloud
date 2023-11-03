<?php declare(strict_types=1);  /* Ta linia musi być pierwsza */ ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Zaloguj</title>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            margin-bottom: 60px; /* Wysokość stopki (footer) */
        }

        form {
            max-width: 300px;
            width: 100%;
            margin-bottom: 60px; /* Dodałem margines na dole formularza */
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f8f9fa; /* Domyślny kolor tła stopki w Bootstrapie */
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>
    <form class="text-center" method="post" action="register.php" enctype="multipart/form-data">
        <div class="form-outline mb-4">
            <h3>Zarejestruj się</h3>
        </div>
        <!-- Login input -->
        <div class="form-outline mb-4">
            <input type="text" id="form2Example1" class="form-control" name="login" />
            <label class="form-label" for="form2Example1">Login</label>
        </div>

        <!-- Password input -->
        <div class="form-outline mb-4">
            <input type="password" id="form2Example2" class="form-control" name="pass" />
            <label class="form-label" for="form2Example2">Hasło</label>
        </div>

        <!-- Repeat Password input -->
        <div class="form-outline mb-4">
            <input type="password" id="form2Example22" class="form-control" name="repeat_pass" />
            <label class="form-label" for="form2Example22">Powtórz hasło</label>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block mb-4">Zarejestruj</button>
    </form>

    <?php require_once 'footer.php'; ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
