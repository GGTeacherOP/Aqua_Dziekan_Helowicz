
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AquaParadise - Restauracja AquaTaste</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%), url('restaurant_assets/lounge.jpg');">
    <div class="content">
      <h1>Kulinarne Doznania w AquaParadise</h1>
      <p>Odkryj bogactwo smaków w naszej eleganckiej restauracji i przytulnym barze. Świeże składniki, autorskie dania.</p>
    </div>
  </header>

    <nav class="main-nav">
        <div class="nav-links">
            <a href="index.php">Strona Główna</a>
            <a href="aquapark.php">Aquapark</a>
            <a href="hotel.php">Hotel</a>
            <a href="spa.php">Spa & Wellness</a>
            <a href="restaurant.php" class="active">Restauracja</a>
        </div>
        <div class="nav-auth">
            <a href="login.php" class="auth-button login-button" id="loginButton">Zaloguj się</a>
            <a href="signup.php" class="auth-button register-button" id="registerButton">Zarejestruj się</a>
            <a href="#" class="auth-button login-button" id="logoutButton" style="display:none;">Wyloguj się</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <section class="section-title-container">
            <h2>Karta Dań Restauracji AquaTaste</h2>
        </section>

        <section class="menu-sections">
            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/przystawki.jfif" alt="Przystawki">
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-pepper-hot"></i> Wyśmienite Przystawki</h3>
                    <p>Idealny początek kulinarnej podróży, lekkie i pełne smaku kompozycje.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Bruschetta z pomidorami concasse i bazylią - 28 PLN</li>
                        <li><i class="fas fa-utensils"></i>Chrupiące kalmary z sosem aioli - 38 PLN</li>
                        <li><i class="fas fa-utensils"></i>Carpaccio z polędwicy wołowej z parmezanem i rukolą - 45 PLN</li>
                    </ul>
                </div>
            </div>

            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/dania_glowne.jfif" alt="Dania Główne">
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-drumstick-bite"></i> Dania Główne Pełne Smaku</h3>
                    <p>Starannie przygotowane dania mięsne, rybne oraz wegetariańskie, które zadowolą każde podniebienie.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Filet z łososia na szpinaku z sosem cytrynowym - 65 PLN</li>
                        <li><i class="fas fa-utensils"></i>Domowe tagliatelle z borowikami i truflą - 52 PLN</li>
                        <li><i class="fas fa-utensils"></i>Stek z polędwicy wołowej z sosem pieprzowym i pieczonymi ziemniakami - 95 PLN</li>
                    </ul>
                </div>
            </div>

            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/vegan_vege.jfif" alt="Opcje Wegetariańskie i Wegańskie">
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-seedling"></i> Wegetariańskie i Wegańskie Specjały</h3>
                    <p>Bogactwo smaków natury w naszych kreatywnych daniach bezmięsnych.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Curry warzywne z mlekiem kokosowym i ryżem jaśminowym (VEGAN) - 48 PLN</li>
                        <li><i class="fas fa-utensils"></i>Burger z halloumi i pieczonymi warzywami - 42 PLN</li>
                        <li><i class="fas fa-utensils"></i>Risotto z sezonowymi grzybami i parmezanem (VEGE) - 55 PLN</li>
                    </ul>
                </div>
            </div>

            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/desery.jfif" alt="Słodkie Zakończenie - Desery">
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-ice-cream"></i> Słodkie Zakończenie - Desery</h3>
                    <p>Nasze autorskie desery to doskonałe ukoronowanie każdego posiłku.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Klasyczne włoskie Tiramisu - 28 PLN</li>
                        <li><i class="fas fa-utensils"></i>Lava cake z płynną czekoladą i lodami waniliowymi - 32 PLN</li>
                        <li><i class="fas fa-utensils"></i>Sernik z białej czekolady na spodzie brownie z musem malinowym - 30 PLN</li>
                    </ul>
                </div>
            </div>

            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/napoje.jfif" alt="Napoje i Koktajle">
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-cocktail"></i> Napoje i Koktajle</h3>
                    <p>Szeroki wybór win, autorskich koktajli, świeżych soków i aromatycznych kaw.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Selekcja win regionalnych i międzynarodowych - od 25 PLN/kieliszek</li>
                        <li><i class="fas fa-utensils"></i>Koktajl "AquaBlue Paradise" - 35 PLN</li>
                        <li><i class="fas fa-utensils"></i>Świeżo wyciskane soki owocowe i warzywne - 18 PLN</li>
                    </ul>
                </div>
            </div>
            
            <div class="menu-card horizontal-card">
                <div class="menu-img">
                    <img src="restaurant_assets/dania_sezonowe.jpg" alt="Dania Sezonowe"> 
                </div>
                <div class="menu-content">
                    <h3><i class="fas fa-star"></i> Dania Sezonowe Szefa Kuchni</h3>
                    <p>Zapytaj obsługę o aktualne specjały przygotowywane z najświeższych, sezonowych składników.</p>
                    <ul class="menu-item-list">
                        <li><i class="fas fa-utensils"></i>Krem z białych szparagów z chipsem z parmezanu (sezonowo)</li>
                        <li><i class="fas fa-utensils"></i>Pierś z kaczki na musie z dyni z sosem wiśniowym (sezonowo)</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <a href="restaurant_assets/restaurant_b.php" class="cta-button">Zarezerwuj Stolik Online <i class="far fa-calendar-alt"></i></a>
        </section>
    </div>

<footer class="main-footer">
    <div class="footer-inner-content">
        <div class="footer-content-columns">
            <div>
                <h4>Restauracja AquaTaste</h4>
                <p>Miejsce, gdzie smak spotyka się z elegancją. Zapraszamy na niezapomnianą podróż kulinarną.</p>
            </div>
            <div>
                <h4>Godziny Otwarcia Restauracji</h4>
                <p><strong>Codziennie:</strong> 13:00 - 23:00</p>
                <p><strong>Bar:</strong> 11:00 - 00:00</p>
            </div>
            <div>
                <h4>Rezerwacje</h4>
                 <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><a href="https://maps.google.com/?q=ul.+Słoneczne+Wybrzeże+7,+00-123+Rajskie+Miasto" target="_blank" rel="noopener noreferrer">ul. Słoneczne Wybrzeże 7, <br>00-123 Rajskie Miasto</a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span><a href="tel:+48500100200">+48 500 100 200</a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                     <span><a href="mailto:kontakt@aquaparadise.pl">kontakt@aquaparadise.pl</a></span>
                </div>
            </div>
        </div>
        <div class="footer-social-icons">
        <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="X (Twitter)" target="_blank" rel="noopener noreferrer"><i class="fab fa-x-twitter"></i></a> 
        </div>
        <p>© 2025 AquaParadise. Wszelkie prawa zastrzeżone.</p>
    </div>
</footer>
<script src="script.js"></script>
</body>
</html>