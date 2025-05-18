<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_login = $_POST['email_login'];
    $password = $_POST['password'];

    // Database connection
    $conn = mysql_connect('localhost', 'root', '', 'aquapark');
    if (!$conn) {
        die("Connection failed: " . mysql_error());
    }

    // Query to check user credentials
    $email_login = mysql_real_escape_string($email_login);
    $query = "SELECT id, password FROM users WHERE email = '$email_login'";
    $result = mysql_query($query);

    if ($result && mysql_num_rows($result) === 1) {
        $user = mysql_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;

            // Redirect to a protected page
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Nieprawidłowe hasło.";
        }
    } else {
        $error = "Nie znaleziono użytkownika.";
    }

    mysql_close($conn);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Logowanie</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-page-container">
        <div class="return-button-container">
            <a href="index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
        </div>

        <div class="form-container-wrapper">
            <div class="form-container auth-form">
                <h2>Logowanie</h2>
                <form action="#" method="POST" id="loginForm"> 
                    <div class="form-group">
                        <label for="login-email"><i class="fas fa-envelope"></i> Adres Email</label>
                        <input type="email" id="login-email" name="email_login" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password"><i class="fas fa-lock"></i> Hasło</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_me">
                            Zapamiętaj mnie
                        </label>
                        <a href="#" class="form-link">Nie pamiętam hasła</a>
                    </div>
                    <button type="submit" class="cta-button form-submit-button">Zaloguj się</button>
                </form>
                <p class="form-switch">Nie masz jeszcze konta? <a href="signup.php" class="form-link">Zarejestruj się tutaj!</a></p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>