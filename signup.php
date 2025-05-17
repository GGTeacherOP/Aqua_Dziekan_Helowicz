<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Rejestracja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-page-container">
        <div class="return-button-container">
            <a href="index.html" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
        </div>

        <div class="form-container-wrapper">
            <div class="form-container auth-form">
                <h2>Rejestracja</h2>
                <form action="#" method="POST" id="signupForm">
                    <div class="form-group">
                        <label for="register-firstname"><i class="fas fa-user"></i> Imię</label>
                        <input type="text" id="register-firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="register-lastname"><i class="fas fa-user-tag"></i> Nazwisko</label> 
                        <input type="text" id="register-lastname" name="lastname" required>
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
                            Akceptuję <a href="regulamin.html" target="_blank" class="form-link">regulamin serwisu</a>*
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
                <p class="form-switch">Masz już konto? <a href="login.html" class="form-link">Zaloguj się!</a></p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>