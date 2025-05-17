<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Rezerwacja Hotelu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-page-container">
        <div class="return-button-container">
            <a href="../index.html" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
        </div>

        <div class="form-container-wrapper">
            <div class="form-container auth-form booking-form">
                <h2>Rezerwacja Hotelu</h2>
                <form action="#" method="POST" id="hotelReservationForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Imię i Nazwisko:</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adres Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="checkin"><i class="fas fa-calendar-alt"></i> Data Przyjazdu:</label>
                        <input type="date" id="checkin" name="checkin" required>
                    </div>

                    <div class="form-group">
                        <label for="checkout"><i class="fas fa-calendar-alt"></i> Data Wyjazdu:</label>
                        <input type="date" id="checkout" name="checkout" required>
                    </div>

                    <div class="form-group">
                        <label for="roomType"><i class="fas fa-bed"></i> Typ Pokoju:</label>
                        <select id="roomType" name="roomType" required>
                            <option value="" disabled selected>-- Wybierz typ pokoju --</option>
                            <option value="standard">Pokój Standard</option>
                            <option value="deluxe_balkon">Pokój Deluxe z Balkonem</option>
                            <option value="luksusowy_apartament">Apartament Luksusowy</option>
                            <option value="rodzinny_superior">Pokój Rodzinny Superior</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="guests"><i class="fas fa-users"></i> Liczba Gości:</label>
                        <input type="number" id="guests" name="guests" min="1" value="1" required>
                    </div>

                    <button type="submit" class="cta-button form-submit-button">Zarezerwuj Teraz</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../script.js"></script>
</body>
</html>