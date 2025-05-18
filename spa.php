<?php
require_once __DIR__ . '/config/init.php'; 

$page_title = "Spa & Wellness - AquaParadise"; 

// Dołączamy header.php, który zawiera DOCTYPE, <head> i otwarcie <body>
include BASE_PATH . '/includes/header.php'; 
?>

    <header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%), url('spa_assets/back.jpg');">
        <div class="content">
            <h1>Oaza Relaksu: Spa & Wellness AquaParadise</h1>
            <p>Zanurz się w świecie spokoju, odprężenia i regeneracji. Czekają na Ciebie luksusowe zabiegi i terapie.</p>
        </div>
    </header>

<?php 
include BASE_PATH . '/includes/navigation.php'; 
?>

    <div class="page-wrapper">
        <section class="section-title-container">
            <h2>Nasza Oferta Zabiegów Spa</h2>
        </section>

        <section class="spa-sections cards-grid">
            <div class="spa-service-card card">
                <div class="spa-img card-image">
                    <img src="spa_assets/terapie_masazu.jpeg" alt="Terapie Masażu">
                </div>
                <div class="spa-content card-content">
                    <h3><i class="fas fa-hand-holding-heart"></i> Kojące Terapie Masażu</h3>
                    <p>Odprężające masaże dostosowane do Twoich potrzeb, od klasycznych po egzotyczne rytuały.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-spa"></i>Masaż Relaksacyjny Klasyczny - 250 PLN</li>
                        <li><i class="fas fa-spa"></i>Masaż Lomi Lomi Nui - 320 PLN</li>
                        <li><i class="fas fa-spa"></i>Masaż Gorącymi Kamieniami Wulkanicznymi - 350 PLN</li>
                    </ul>
                    <a href="spa_assets/spa_b.php" class="cta-button cta-button-small" style="margin-top: auto; align-self: flex-start;">Szczegóły i Rezerwacja <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="spa-service-card card">
                 <div class="spa-img card-image">
                    <img src="spa_assets/zabiegi_na_twarz.webp" alt="Zabiegi na Twarz">
                </div>
                <div class="spa-content card-content">
                    <h3><i class="fas fa-mask"></i> Odmładzające Zabiegi na Twarz</h3>
                    <p>Profesjonalne zabiegi kosmetyczne przywracające skórze blask, nawilżenie i młody wygląd.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-spa"></i>Intensywnie Nawilżający Zabieg HydraBoost - 280 PLN</li>
                        <li><i class="fas fa-spa"></i>Liftingujący Zabieg Anti-Aging Gold Therapy - 350 PLN</li>
                        <li><i class="fas fa-spa"></i>Oczyszczający Zabieg dla Skóry Problematycznej - 260 PLN</li>
                    </ul>
                     <a href="spa_assets/spa_b.php" class="cta-button cta-button-small" style="margin-top: auto; align-self: flex-start;">Szczegóły i Rezerwacja <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="spa-service-card card">
                <div class="spa-img card-image">
                    <img src="spa_assets/zabiegi_na_cialo.jpg" alt="Zabiegi na Ciało">
                </div>
                <div class="spa-content card-content">
                    <h3><i class="fas fa-leaf"></i> Rozluźniające Zabiegi na Ciało</h3>
                    <p>Peelingi, okłady i rytuały pielęgnacyjne, które odżywią Twoją skórę i zmysły.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-spa"></i>Aromatyczny Peeling Cukrowy Całego Ciała - 190 PLN</li>
                        <li><i class="fas fa-spa"></i>Odżywczy Okład Czekoladowy - 330 PLN</li>
                        <li><i class="fas fa-spa"></i>Detoksykujący Rytuał z Zieloną Herbatą - 380 PLN</li>
                    </ul>
                     <a href="spa_assets/spa_b.php" class="cta-button cta-button-small" style="margin-top: auto; align-self: flex-start;">Szczegóły i Rezerwacja <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="spa-service-card card">
                 <div class="spa-img card-image">
                    <img src="spa_assets/welness.jpg" alt="Pakiety Wellness">
                </div>
                 <div class="spa-content card-content">
                    <h3><i class="fas fa-gifts"></i> Ekskluzywne Pakiety Wellness</h3>
                    <p>Kompleksowe pakiety łączące najlepsze zabiegi dla pełnej regeneracji i głębokiego relaksu.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-spa"></i>Pakiet "Harmonia Zmysłów" (Masaż + Zabieg na Twarz) - 500 PLN</li>
                        <li><i class="fas fa-spa"></i>Pakiet "Królewski Relaks" (Peeling + Okład + Masaż) - 680 PLN</li>
                        <li><i class="fas fa-spa"></i>Romantyczny Rytuał dla Dwojga - 850 PLN</li>
                    </ul>
                     <a href="spa_assets/spa_b.php" class="cta-button cta-button-small" style="margin-top: auto; align-self: flex-start;">Szczegóły i Rezerwacja <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </section>
    </div>

    <section class="cta-section">
        <a href="spa_assets/spa_b.php" class="cta-button">Zarezerwuj Swój Zabieg Już Dziś <i class="far fa-calendar-check"></i></a>
    </section>

<<<<<<< HEAD
    <footer class="main-footer">
        <div class="footer-inner-content">
            <div class="footer-content-columns">
                 <div>
                    <h4>AquaParadise Spa & Wellness</h4>
                    <p>Twoja przystań spokoju i odnowy biologicznej. Pozwól nam zadbać o Twoje ciało i duszę.</p>
                </div>
                 <div>
                    <h4>Godziny Otwarcia Spa</h4>
                    <p><strong>Codziennie:</strong> 10:00 - 22:00</p>
                    <p><small>Rezerwacja telefoniczna i online zalecana.</small></p>
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
=======
<?php 
include BASE_PATH . '/includes/footer.php'; 
?>
>>>>>>> feature
