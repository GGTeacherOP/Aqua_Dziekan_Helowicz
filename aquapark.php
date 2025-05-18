<?php
require_once __DIR__ . '/config/init.php'; // Zawiera $pdo, session_start(), BASE_PATH, funkcje e()
$page_title = "Aquapark - AquaParadise";

// Dołączamy header.php, który zawiera DOCTYPE, <head> i otwarcie <body>
include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>
<style>
/* Dodatkowe style dla biletów, jeśli potrzebne specyficznie tutaj */
.tickets-section { padding: 2em; background-color: #f9f9f9; border-radius: 8px; margin-top: 30px; }
.ticket-card { border: 1px solid var(--border-color); padding: 20px; margin-bottom: 20px; border-radius: 8px; background-color: white; display: flex; flex-direction: column; justify-content: space-between; }
.ticket-card h4 { margin-top: 0; color: var(--primary-dark-color); }
.ticket-card .price { font-size: 1.5em; font-weight: bold; color: var(--primary-color); margin: 10px 0; }
.ticket-card p { flex-grow: 1; margin-bottom: 15px; } /* Aby przycisk był na dole, jeśli opisy mają różną długość */
.add-to-cart-form { margin-top: auto; } /* Wypchnij formularz na dół karty */
.add-to-cart-form label { margin-right: 5px; font-size: 0.9em;}
.add-to-cart-form input[type="number"] { width: 60px; padding: 8px; text-align: center; margin-right: 10px; border: 1px solid var(--border-color); border-radius: 4px;}
</style>
<?php
$aquapark_assets_path = 'aquapark_assets/'; 

// Definicja $base_url_detected dla ścieżek obrazków (jeśli nie jest globalnie dostępna z init.php)
if (!isset($base_url_detected) && defined('BASE_URL')) { 
    $base_url_detected = BASE_URL;
} elseif (!isset($base_url_detected)) {
    // Próba automatycznego wykrycia BASE_URL, jeśli nie jest zdefiniowana
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $project_folder_script = str_replace(basename($script_name), '', $script_name);
    $base_url_detected = rtrim($protocol . $host . $project_folder_script, '/');
}
?>

    <header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%), url('<?php echo e($aquapark_assets_path); ?>aquapark_header.jpg');">
        <div class="content">
            <h1>Nasz Niezwykły Aquapark</h1>
            <p>Zanurz się w świecie wodnych emocji i niezapomnianej zabawy dla całej rodziny!</p>
        </div>
    </header>

<?php
include BASE_PATH . '/includes/navigation.php';
?>

    <div class="page-wrapper">
        <section class="section-title-container">
            <h2>Główne Atrakcje Aquaparku</h2>
        </section>
        <section class="attractions-list horizontal-layout">
            <div class="attraction-card">
                <div class="attraction-img"><img src="<?php echo e($aquapark_assets_path); ?>fala.jpg" alt="Basen z Falą"></div>
                <div class="attraction-content"><h3>Basen z Falą Morską</h3><p>Poczuj prawdziwą morską bryzę i daj się ponieść falom w naszym imponującym basenie.</p>
                    <div class="features-tags"><span class="feature-tag">Dla Rodzin</span><span class="feature-tag">Fale co 10 min</span></div>
                </div>
            </div>
            <div class="attraction-card">
                <div class="attraction-img"><img src="<?php echo e($aquapark_assets_path); ?>adrenalina.jfif" alt="Ekscytujące Zjeżdżalnie"></div>
                <div class="attraction-content"><h3>Adrenalinowe Zjeżdżalnie</h3><p>Dla miłośników mocnych wrażeń! Nasze zjeżdżalnie dostarczą niezapomnianych emocji.</p>
                    <div class="features-tags"><span class="feature-tag">Ekstremalne</span><span class="feature-tag">Wysoka Prędkość</span></div>
                </div>
            </div>
            <div class="attraction-card" id="kids-zone">
                <div class="attraction-img"><img src="<?php echo e($aquapark_assets_path); ?>raj.jfif" alt="Raj dla Dzieci"></div>
                <div class="attraction-content"><h3>Dziecięcy Raj Wodny</h3><p>Bezpieczna strefa dla najmłodszych z mini zjeżdżalniami, brodzikami i fontannami.</p>
                     <div class="features-tags"><span class="feature-tag">Dla Maluchów</span><span class="feature-tag">Bezpieczna Zabawa</span></div>
                </div>
            </div>
             <div class="attraction-card">
                <div class="attraction-img"><img src="<?php echo e($aquapark_assets_path); ?>sauna.jpg" alt="Strefa Relaksu i Saun"></div>
                <div class="attraction-content"><h3>Strefa Relaksu i Saun</h3><p>Po dniu pełnym wrażeń, zrelaksuj się w naszej strefie wellness z jacuzzi i saunami.</p>
                     <div class="features-tags"><span class="feature-tag">Wyciszenie</span><span class="feature-tag">Sauny</span><span class="feature-tag">Jacuzzi</span></div>
                </div>
            </div>
        </section>

        <section class="stats-counter-grid">
            <div class="counter-item"><i class="fas fa-swimming-pool"></i><div class="stat-number">12</div><div class="stat-label">Unikalnych Basenów</div></div>
            <div class="counter-item"><i class="fas fa-tint"></i><div class="stat-number">20+</div><div class="stat-label">Zjeżdżalni Wodnych</div></div>
            <div class="counter-item"><i class="fas fa-child"></i><div class="stat-number">3</div><div class="stat-label">Interaktywne Strefy Dziecięce</div></div>
            <div class="counter-item"><i class="fas fa-users"></i><div class="stat-number">1000+</div><div class="stat-label">Zadowolonych Gości Dziennie</div></div>
        </section>

        <section class="section-title-container" id="bilety">
            <h2>Kup Bilety Online</h2>
        </section>
        <div class="tickets-section">
            <?php
            try {
                $stmtTickets = $pdo->prepare(
                    "SELECT p.product_id, p.name, p.description, p.price, p.image_url
                     FROM Products p
                     JOIN Categories c ON p.category_id = c.category_id
                     WHERE c.name = 'Aquapark - Bilety' AND p.is_active = TRUE
                     ORDER BY p.price ASC"
                );
                $stmtTickets->execute();
                $tickets = $stmtTickets->fetchAll();

                if ($tickets):
                    foreach ($tickets as $ticket):
            ?>
                <div class="ticket-card">
                        <?php // endif; ?>
                    <h4><?php echo e($ticket['name']); ?></h4>
                    <p><?php echo e($ticket['description']); ?></p>
                    <div class="price"><?php echo number_format($ticket['price'], 2, ',', ' '); ?> PLN</div>
                    <form action="cart_actions.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="<?php echo $ticket['product_id']; ?>">
                        <label for="quantity_<?php echo $ticket['product_id']; ?>">Ilość:</label>
                        <input type="number" id="quantity_<?php echo $ticket['product_id']; ?>" name="quantity" value="1" min="1" required>
                        <button type="submit" class="cta-button cta-button-small">Dodaj do koszyka</button>
                    </form>
                </div>
            <?php
                    endforeach;
                else:
            ?>
                <p>Przepraszamy, obecnie brak dostępnych biletów online. Skontaktuj się z nami.</p>
            <?php
                endif;
            } catch (PDOException $e) {
                error_log("Błąd pobierania biletów w aquapark.php: " . $e->getMessage());
                echo "<p>Wystąpił błąd podczas ładowania oferty biletów. Prosimy spróbować później.</p>";
            }
            ?>
        </div>

        <section class="section-title-container" id="gallery">
            <h2>Galeria Zdjęć z Aquaparku</h2>
        </section>
        <section class="gallery-grid">
            <?php
            $gallery_images = [
                "aquapark_gallery2.jpg" => "Zjeżdżalnie wodne",
                "aquapark_gallery3.jpg" => "Zabawa w strefie dla dzieci",
                "aquapark_gallery4.jpg" => "Basen z hydromasażem",
                "aquapark_gallery6.jpg" => "Wewnętrzna strefa relaksu",
                "aquapark_wave_pool.jpg" => "Akcja w basenie z falą",
                "aquapark_slides.jpg" => "Zjeżdżalnia z bliska"
            ];
            foreach ($gallery_images as $img_file => $alt_text):
                $gallery_img_path = $img_file;
            ?>
                <div class="gallery-item"><img src="<?php echo e($gallery_img_path); ?>" alt="<?php echo e($alt_text); ?>"></div>
            <?php endforeach; ?>
        </section>

        <section class="cta-section" id="contact-aquapark">
            <a href="cart_view.php" class="cta-button">Przejdź do Koszyka <i class="fas fa-shopping-cart"></i></a>
        </section>
    </div>

<?php include BASE_PATH . '/includes/footer.php'; ?>