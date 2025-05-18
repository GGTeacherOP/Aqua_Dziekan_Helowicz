<?php
require_once __DIR__ . '/../config/init.php'; 
$page_title = "Rezerwacja Stolika - AquaParadise";

include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>
<link rel="stylesheet" href="../style.css"> 

<div class="auth-page-container">
    <div class="return-button-container">
        <a href="../index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form booking-form">
            <h2>Rezerwacja Stolika w Restauracji</h2>
            <?php
            if (function_exists('display_flash_message')) {
                display_flash_message();
            }
            ?>
            <form action="../cart_actions.php" method="POST" id="restaurantReservationForm">
                <input type="hidden" name="action" value="add_to_cart">
                <?php
                    $reservationProductId = null;
                    if(isset($pdo)){
                        $stmtResProd = $pdo->prepare("SELECT product_id FROM Products WHERE name LIKE 'Rezerwacja Stolika%' LIMIT 1");
                        $stmtResProd->execute();
                        $resProd = $stmtResProd->fetch();
                        if($resProd) $reservationProductId = $resProd['product_id'];
                        else error_log("Produkt 'Rezerwacja Stolika' nie znaleziony w bazie.");
                    }
                ?>
                <input type="hidden" name="product_id" value="<?php echo e($reservationProductId ?? '0'); ?>"> <?php // Użyj ID produktu "Rezerwacja" lub 0/null jeśli obsługa inna ?>
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="item_details[reservation_type]" value="restaurant_table">

                <div class="form-group">
                    <label for="res_name"><i class="fas fa-user"></i> Imię i Nazwisko na rezerwacji:</label>
                    <input type="text" id="res_name" name="item_details[booking_name]" placeholder="Wpisz swoje imię i nazwisko" required>
                </div>
                <div class="form-group">
                    <label for="res_phone"><i class="fas fa-phone"></i> Numer Telefonu kontaktowy:</label>
                    <input type="tel" id="res_phone" name="item_details[booking_phone]" placeholder="Wpisz swój numer telefonu" required>
                </div>
                <div class="form-group">
                    <label for="res_email"><i class="fas fa-envelope"></i> Adres Email (opcjonalnie):</label>
                    <input type="email" id="res_email" name="item_details[booking_email]" placeholder="Wpisz swój adres email">
                </div>
                <div class="form-group">
                    <label for="res_date"><i class="fas fa-calendar-alt"></i> Data Rezerwacji:</label>
                    <input type="date" id="res_date" name="item_details[reservation_date]" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="res_time"><i class="fas fa-clock"></i> Godzina Rezerwacji:</label>
                    <input type="time" id="res_time" name="item_details[reservation_time]" required>
                </div>
                <div class="form-group">
                    <label for="res_guests"><i class="fas fa-users"></i> Liczba Osób:</label>
                    <input type="number" id="res_guests" name="item_details[num_guests]" min="1" value="1" placeholder="Podaj liczbę osób" required>
                </div>
                <div class="form-group">
                    <label for="res_notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                    <textarea id="res_notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, alergie itp."></textarea>
                </div>

                <button type="submit" class="cta-button form-submit-button">Zarezerwuj Stolik</button>
            </form>
        </div>
    </div>
</div>
<script src="../script.js"></script> 
</body>
</html>