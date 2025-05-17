<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Rezerwacja Spa</title>
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
                <h2>Zarezerwuj Wizytę w Spa</h2>
                <form action="#" method="POST" id="spaBookingForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Imię i Nazwisko:</label>
                        <input type="text" id="name" name="name" placeholder="Wpisz swoje imię i nazwisko" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adres Email:</label>
                        <input type="email" id="email" name="email" placeholder="Wpisz swój adres email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Numer Telefonu:</label>
                        <input type="tel" id="phone" name="phone" placeholder="Wpisz swój numer telefonu" required>
                    </div>

                    <div class="form-group">
                        <label for="date"><i class="fas fa-calendar-alt"></i> Preferowana Data:</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="spaPackageButton"><i class="fas fa-spa"></i> Wybór Masażu/Pakietu:</label>
                        <button type="button" id="spaPackageButton" class="cta-button secondary-cta">Wybierz Masaż lub Pakiet</button>
                        <input type="hidden" id="selectedSpaPackages" name="selectedSpaPackages">
                         <div id="selectedTreatmentsDisplay" class="selected-treatments-display"></div>
                    </div>

                    <div class="form-group">
                        <label for="notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Specjalne życzenia, preferencje"></textarea>
                    </div>

                    <button type="submit" class="cta-button form-submit-button">Zarezerwuj Teraz</button>
                </form>
            </div>
        </div>
    </div>

    <div id="spaPackagesModal" class="modal">
        <div class="modal-content wide-modal"> 
            <span class="close-button">&times;</span>
            <h3>Wybierz Masaże i Pakiety</h3>
            <div id="spaPackagesHorizontalList" class="modal-horizontal-options-list">
            </div>
            <button id="confirmSpaSelection" class="cta-button">Potwierdź Wybór</button>
        </div>
    </div>

    <script src="../script.js"></script>
</body>
</html>