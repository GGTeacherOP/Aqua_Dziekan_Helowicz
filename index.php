<?php
// index.php
require_once __DIR__ . '/config/init.php';

$page_title = "AquaParadise - Witaj w Raju!"; // Ustaw tytuł strony

include BASE_PATH . '/includes/header.php'; // Dołącz główny nagłówek
?>

  <header class="main-header">
    <div class="header-content">
      <h1>Witaj w AquaParadise!</h1>
      <p class="subtitle">Twoje miejsce ucieczki do świata wodnych przygód, luksusowego wypoczynku i niezapomnianych chwil. Odkryj magię, która czeka na Ciebie.</p>
      <a href="#explore" class="cta-button header-cta">Odkryj Nasze Światy</a>
    </div>
  </header>

<?php
// Dołącz dynamiczną nawigację
include BASE_PATH . '/includes/navigation.php';
?>

  <div class="page-wrapper" id="explore">
    <section class="section-title-container">
      <h2>Eksploruj Nasze Światy</h2>
    </section>

    <section class="explore-worlds">
      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/aqua.jpg" alt="Strefa Rekreacji Aquaparku">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-water"></i> Niesamowity Aquapark</h3>
          <p>Zanurz się w ekscytującym świecie zjeżdżalni, basenów z falami i stref relaksu dla całej rodziny. Adrenalina i zabawa gwarantowana!</p>
        </div>
      </div>

      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/reception.jpg" alt="Luksusowy Hotel AquaParadise">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-concierge-bell"></i> Luksusowy Hotel</h3>
          <p>Odpocznij w komfortowych pokojach z widokiem na wodne atrakcje. Idealne miejsce na regenerację sił po dniu pełnym wrażeń.</p>
        </div>
      </div>

      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/spa.webp" alt="Strefa Spa & Wellness">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-spa"></i> Strefa Spa & Wellness</h3>
          <p>Zrelaksuj ciało i umysł w naszej ekskluzywnej strefie spa. Sauny, masaże i zabiegi, które przywrócą Ci energię i harmonię.</p>
        </div>
      </div>
      
      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/restauracja.jpg" alt="Wyjątkowe Restauracje">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-utensils"></i> Wyjątkowa Restauracja</h3>
          <p>Skosztuj kulinarnych arcydzieł w naszej restauracji i barze. Od szybkich przekąsek po eleganckie kolacje - każdy znajdzie coś dla siebie.</p>
        </div>
      </div>

      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/kids.jpg" alt="Dziecięca Kraina Zabaw">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-child"></i> Dziecięca Kraina Zabaw</h3>
          <p>Bezpieczne i ekscytujące atrakcje wodne zaprojektowane specjalnie dla naszych najmłodszych gości. Płytkie baseny, mini-zjeżdżalnie i interaktywne fontanny!</p>
        </div>
      </div>

      <div class="world-card">
        <div class="world-card-image">
          <img src="index_assets/party.jpg" alt="Imprezy i Wydarzenia Specjalne">
        </div>
        <div class="world-card-content">
          <h3><i class="fas fa-star"></i> Imprezy i Wydarzenia</h3>
          <p>Doświadcz więcej niż tylko wody! Dołącz do nas podczas weekendów tematycznych, wieczorów z muzyką na żywo, pokazów nocnych i sezonowych festiwali.</p>
        </div>
      </div>
    </section>

    <section class="cta-section-main" id="contact">
      <h2>Gotowy na Przygodę Życia?</h2>
      <p>Zaplanuj swój pobyt w AquaParadise już dziś i stwórz wspomnienia, które zostaną z Tobą na zawsze. Czekamy na Ciebie!</p>
      <a href="aquapark.php#bilety" class="cta-button">Kup Bilety lub Zobacz Ofertę</a>
    </section>
  </div>

<?php
// Dołącz główną stopkę
include BASE_PATH . '/includes/footer.php';
?>