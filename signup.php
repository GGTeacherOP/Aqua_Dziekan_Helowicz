<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "aquapark";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sanitize and validate input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $surname = mysqli_real_escape_string($conn, trim($_POST['surname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, trim($_POST['confirm_password']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Hasła nie pasują do siebie.');</script>";
    } else {
        // Insert data into the database without hashing the password
        $sql = "INSERT INTO users (imie, nazwisko, email, password, phone) 
                VALUES ('$name', '$surname', '$email', '$password', '$phone')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Rejestracja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            form.addEventListener("submit", function (event) {
                let isValid = true;
                const requiredFields = form.querySelectorAll("[required]");

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.border = "2px solid red";
                    } else {
                        field.style.border = "";
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    alert("Proszę wypełnić wszystkie wymagane pola.");
                }
            });
        });
    </script>
</head>
<body>
    <div class="auth-page-container">
        <div class="return-button-container">
            <a href="index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
        </div>

        <div class="form-container-wrapper">
            <div class="form-container auth-form">
                <h2>Rejestracja</h2>
                <form action="signup.php" method="POST">
                    <div class="form-group">
                        <label for="register-name"><i class="fas fa-user"></i> Imię</label>
                        <input type="text" id="register-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="register-surname"><i class="fas fa-user"></i> Nazwisko</label>
                        <input type="text" id="register-surname" name="surname" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email"><i class="fas fa-envelope"></i> Adres Email</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password"><i class="fas fa-lock"></i> Hasło</label>
                        <input type="password" id="register-password" name="password" required minlength="8">
                        <small>Minimum 8 znaków, w tym duża litera i cyfra.</small>
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password"><i class="fas fa-redo-alt"></i> Potwierdź Hasło</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                    </div>
                     <div class="form-group">
                        <label for="register-phone"><i class="fas fa-phone"></i> Numer Telefonu (opcjonalnie)</label>
                        <input type="tel" id="register-phone" name="phone">
                    </div>
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms_agree" required>
                            Akceptuję <a href="#" class="form-link">regulamin serwisu</a>*
                        </label>
                    </div>
                     <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="newsletter_agree">
                            Chcę otrzymywać informacje o promocjach i nowościach (newsletter)
                        </label>
                    </div>
                    <button type="submit" class="cta-button form-submit-button">Zarejestruj się</button>
                </form>
                <p class="form-switch">Masz już konto? <a href="login.php" class="form-link">Zaloguj się!</a></p>
            </div>
        </div>
    </div>
</body>
</html>