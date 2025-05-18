<?php
// hotel.php
require_once __DIR__ . '/config/init.php';

$page_title = "Hotel - AquaParadise"; // Ustaw tytuł strony

include BASE_PATH . '/includes/header.php'; // Dołącz główny nagłówek
?>

    <header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%), url('hotel_header.jpg');">
        <div class="content">
            <h1>Luksusowy Hotel AquaParadise</h1>
            <p>Odkryj komfort i elegancję w sercu wodnego raju. Idealne miejsce na Twój wypoczynek.</p>
        </div>
    </header>

<?php
// Dołącz dynamiczną nawigację
include BASE_PATH . '/includes/navigation.php';
?>

    <div class="page-wrapper">
        <section class="section-title-container">
            <h2>Komfortowe Pokoje i Apartamenty</h2>
        </section>

        <section class="rooms-list">
            <div class="room-card">
                <div class="room-img">
                    <img src="hotel_assets/standard.webp" alt="Pokój Standardowy">
                </div>
                <div class="room-content">
                    <h3>Pokój Standard</h3>
                    <p>Elegancki i funkcjonalny pokój, zapewniający komfortowy wypoczynek po dniu pełnym wrażeń.</p>
                    <div class="features-tags">
                        <span class="feature-tag">Łóżko Małżeńskie</span>
                        <span class="feature-tag">Widok na Ogród</span>
                        <span class="feature-tag">Klimatyzacja</span>
                        <span class="feature-tag">TV SAT</span>
                    </div>
                    <p><strong>Od 450 PLN / noc</strong></p>
                    <a href="hotel_assets/hotel_b.php" class="cta-button">Zarezerwuj Teraz</a>
                </div>
            </div>

            <div class="room-card">
                <div class="room-img">
                    <img src="hotel_assets/deluxe.webp" alt="Pokój Deluxe">
                </div>
                <div class="room-content">
                    <h3>Pokój Deluxe z Balkonem</h3>
                    <p>Przestronny pokój z prywatnym balkonem i zachwycającym widokiem, idealny dla wymagających gości.</p>
                    <div class="features-tags">
                        <span class="feature-tag">Balkon z Widokiem</span>
                        <span class="feature-tag">Większa Przestrzeń</span>
                        <span class="feature-tag">Minibar</span>
                        <span class="feature-tag">Dostęp do Strefy Spa</span>
                    </div>
                     <p><strong>Od 650 PLN / noc</strong></p>
                    <a href="hotel_assets/hotel_b.php" class="cta-button">Zarezerwuj Teraz</a>
                </div>
            </div>

            <div class="room-card">
                <div class="room-img">
                    <img src="hotel_assets/apartament.webp" alt="Apartament Luksusowy">
                </div>
                <div class="room-content">
                    <h3>Apartament Luksusowy</h3>
                    <p>Najwyższy standard komfortu: oddzielna sypialnia, przestronny salon oraz ekskluzywne wyposażenie.</p>
                     <div class="features-tags">
                        <span class="feature-tag">Oddzielny Salon</span>
                        <span class="feature-tag">Wanna z Hydromasażem</span>
                        <span class="feature-tag">Serwis Kawowy</span>
                        <span class="feature-tag">Pełen Dostęp VIP</span>
                    </div>
                    <p><strong>Od 1200 PLN / noc</strong></p>
                    <a href="hotel_assets/hotel_b.php" class="cta-button">Zarezerwuj Teraz</a>
                </div>
            </div>

            <div class="room-card family-room-card">
                <div class="room-img">
                    <img src="hotel_assets/rodzinny.jpg" alt="Pokój Rodzinny">
                </div>
                <div class="room-content">
                    <h3>Pokój Rodzinny Superior</h3>
                    <p>Dużo miejsca dla całej rodziny, z dodatkowymi udogodnieniami zapewniającymi komfortowy pobyt z dziećmi.</p>
                     <div class="features-tags">
                        <span class="feature-tag">Dwa Duże Łóżka</span>
                        <span class="feature-tag">Kącik dla Dzieci</span>
                        <span class="feature-tag">Możliwość Dostawki</span>
                        <span class="feature-tag">Gry Planszowe</span>
                    </div>
                    <p><strong>Od 750 PLN / noc</strong></p>
                    <a href="hotel_assets/hotel_b.php" class="cta-button">Zarezerwuj Teraz</a>
                </div>
            </div>
        </section>

        <section class="section-title-container amenities-title-container"> 
            <h2>Nasze Udogodnienia</h2>
        </section>
        <section class="amenities-grid">
            <div class="amenity-card">
                <i class="fas fa-concierge-bell"></i>
                <h3>Obsługa Concierge</h3>
            </div>
            <div class="amenity-card">
                <i class="fas fa-dumbbell"></i>
                <h3>Centrum Fitness</h3>
            </div>
             <div class="amenity-card">
                <i class="fas fa-wifi"></i>
                <h3>Bezpłatne Wi-Fi</h3>
            </div>
        </section>
    </div>

<?php
// Dołącz główną stopkę
include BASE_PATH . '/includes/footer.php';
?>