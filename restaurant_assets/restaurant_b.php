<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Rezerwacja Stolika</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-page-container">
        <div class="return-button-container">
            <a href="../index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
        </div>

        <div class="form-container-wrapper">
            <div class="form-container auth-form booking-form">
                <h2>Rezerwacja Stolika w Restauracji</h2>
                <form action="#" method="POST" id="restaurantReservationForm"> 
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Imię i Nazwisko:</label>
                        <input type="text" id="name" name="name" placeholder="Wpisz swoje imię i nazwisko" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Numer Telefonu:</label>
                        <input type="tel" id="phone" name="phone" placeholder="Wpisz swój numer telefonu" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adres Email:</label>
                        <input type="email" id="email" name="email" placeholder="Wpisz swój adres email (opcjonalnie)">
                    </div>

                    <div class="form-group">
                        <label for="date"><i class="fas fa-calendar-alt"></i> Data Rezerwacji:</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="time"><i class="fas fa-clock"></i> Godzina Rezerwacji:</label>
                        <input type="time" id="time" name="time" required>
                    </div>

                    <div class="form-group">
                        <label for="guests"><i class="fas fa-users"></i> Liczba Osób:</label>
                        <input type="number" id="guests" name="guests" min="1" value="1" placeholder="Podaj liczbę osób" required>
                    </div>

                    <div class="form-group">
                        <label for="notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Specjalne życzenia, alergie itp."></textarea>
                    </div>

                    <button type="submit" class="cta-button form-submit-button">Zarezerwuj Stolik</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../script.js"></script>
</body>
</html>