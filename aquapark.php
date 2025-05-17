
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaParadise - Aquapark</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%), url('aquapark_header.jpg');">
        <div class="content">
            <h1>Nasz Niezwykły Aquapark</h1>
            <p>Zanurz się w świecie wodnych emocji i niezapomnianej zabawy dla całej rodziny!</p>
        </div>
    </header>

    <nav class="main-nav">
        <div class="nav-links">
            <a href="index.php">Strona Główna</a>
            <a href="aquapark.php" class="active">Aquapark</a>
            <a href="hotel.php">Hotel</a>
            <a href="spa.php">Spa & Wellness</a>
            <a href="restaurant.php">Restauracja</a>
        </div>
        <div class="nav-auth">
            <a href="login.php" class="auth-button login-button" id="loginButton">Zaloguj się</a>
            <a href="signup.php" class="auth-button register-button" id="registerButton">Zarejestruj się</a>
            <a href="#" class="auth-button login-button" id="logoutButton" style="display:none;">Wyloguj się</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <section class="section-title-container">
            <h2>Główne Atrakcje Aquaparku</h2>
        </section>

        <section class="attractions-list horizontal-layout">
            <div class="attraction-card">
                <div class="attraction-img">
                    <img src="aquapark_assets/fala.jpg" alt="Basen z Falą">
                </div>
                <div class="attraction-content">
                    <h3>Basen z Falą Morską</h3>
                    <p>Poczuj prawdziwą morską bryzę i daj się ponieść falom w naszym imponującym basenie.</p>
                    <div class="features-tags">
                        <span class="feature-tag">Dla Rodzin</span>
                        <span class="feature-tag">Fale co 10 min</span>
                    </div>
                    </div>
            </div>

            <div class="attraction-card">
                <div class="attraction-img">
                    <img src="aquapark_assets/adrenalina.jfif" alt="Ekscytujące Zjeżdżalnie">
                </div>
                <div class="attraction-content">
                    <h3>Adrenalinowe Zjeżdżalnie</h3>
                    <p>Dla miłośników mocnych wrażeń! Nasze zjeżdżalnie dostarczą niezapomnianych emocji.</p>
                    <div class="features-tags">
                        <span class="feature-tag">Ekstremalne</span>
                        <span class="feature-tag">Wysoka Prędkość</span>
                    </div>
                </div>
            </div>

            <div class="attraction-card" id="kids-zone">
                <div class="attraction-img">
                    <img src="aquapark_assets/raj.jfif" alt="Raj dla Dzieci">
                </div>
                <div class="attraction-content">
                    <h3>Dziecięcy Raj Wodny</h3>
                    <p>Bezpieczna strefa dla najmłodszych z mini zjeżdżalniami, brodzikami i fontannami.</p>
                     <div class="features-tags">
                        <span class="feature-tag">Dla Maluchów</span>
                        <span class="feature-tag">Bezpieczna Zabawa</span>
                    </div>
                </div>
            </div>

             <div class="attraction-card">
                <div class="attraction-img">
                    <img src="aquapark_assets/sauna.jpg" alt="Strefa Relaksu i Saun">
                </div>
                <div class="attraction-content">
                    <h3>Strefa Relaksu i Saun</h3>
                    <p>Po dniu pełnym wrażeń, zrelaksuj się w naszej strefie wellness z jacuzzi i saunami.</p>
                     <div class="features-tags">
                        <span class="feature-tag">Wyciszenie</span>
                        <span class="feature-tag">Sauny</span>
                        <span class="feature-tag">Jacuzzi</span>
                    </div>
                    </div>
            </div>
        </section>

        <section class="stats-counter-grid">
            <div class="counter-item">
                <i class="fas fa-swimming-pool"></i>
                <div class="stat-number">12</div>
                <div class="stat-label">Unikalnych Basenów</div>
            </div>
            <div class="counter-item">
                <i class="fas fa-tint"></i>
                <div class="stat-number">20+</div>
                <div class="stat-label">Zjeżdżalni Wodnych</div>
            </div>
            <div class="counter-item">
                <i class="fas fa-child"></i>
                <div class="stat-number">3</div>
                <div class="stat-label">Interaktywne Strefy Dziecięce</div>
            </div>
            <div class="counter-item">
                <i class="fas fa-users"></i>
                <div class="stat-number">1000+</div>
                <div class="stat-label">Zadowolonych Gości Dziennie</div>
            </div>
        </section>

        <section class="section-title-container" id="gallery">
            <h2>Galeria Zdjęć z Aquaparku</h2>
        </section>
        <section class="gallery-grid">
            <div class="gallery-item"><img src="aquapark_gallery2.jpg" alt="Zjeżdżalnie wodne"></div>
            <div class="gallery-item"><img src="aquapark_gallery3.jpg" alt="Zabawa w strefie dla dzieci"></div>
            <div class="gallery-item"><img src="aquapark_gallery4.jpg" alt="Basen z hydromasażem"></div>
            <div class="gallery-item"><img src="aquapark_gallery6.jpg" alt="Wewnętrzna strefa relaksu"></div>
            <div class="gallery-item"><img src="aquapark_wave_pool.jpg" alt="Akcja w basenie z falą"></div>
            <div class="gallery-item"><img src="aquapark_slides.jpg" alt="Zjeżdżalnia z bliska"></div>
        </section>

        <section class="cta-section" id="contact-aquapark">
            <a href="index_assets/przerwa.php" class="cta-button">Kup Bilety i Zaplanuj Przygodę! <i class="fas fa-ticket-alt"></i></a>
        </section>
    </div>

    <footer class="main-footer">
        <div class="footer-inner-content">
            <div class="footer-content-columns">
                 <div>
                    <h4>AquaParadise Aquapark</h4>
                    <p>Centrum wodnej rozrywki dla każdego! Czekają na Ciebie niezapomniane atrakcje i mnóstwo radości.</p>
                </div>
                <div>
                    <h4>Godziny Otwarcia Aquaparku</h4>
                    <p><strong>Sezon letni (VI-VIII):</strong> 09:00 - 21:00</p>
                    <p><strong>Poza sezonem:</strong> 10:00 - 20:00</p>
                </div>
                <div>
                    <h4>Skontaktuj się z Nami</h4>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><a href="https://maps.google.com/?q=ul.+S%C5%82oneczne+Wybrze%C5%BCe+7,+00-123+Rajskie+Miasto" target="_blank" rel="noopener noreferrer">ul. Słoneczne Wybrzeże 7, <br>00-123 Rajskie Miasto</a></span>
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