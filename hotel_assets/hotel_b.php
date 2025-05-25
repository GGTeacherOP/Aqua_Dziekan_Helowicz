<?php
// hotel_assets/hotel_b.php
require_once __DIR__ . '/../config/init.php'; // Poprawna ścieżka
$page_title = "Rezerwacja Hotelu - AquaParadise";
include BASE_PATH . '/includes/header.php'; // Główny header
?>
<title><?php echo e($page_title); ?></title>
<?php
// Nie dołączamy tu includes/navigation.php, bo to strona w podfolderze,
// która ma tylko przycisk powrotu. Można by stworzyć uproszczoną nawigację,
// ale na razie zostawiamy jak było.
?>

<div class="auth-page-container">
    <div class="return-button-container">
        <a href="../index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form booking-form">
            <h2>Rezerwacja Hotelu</h2>
            <?php display_flash_message(); // Wyświetl komunikaty, jeśli są ?>

            <form action="../cart_actions.php" method="POST" id="hotelReservationForm">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="quantity" value="1"> <input type="hidden" name="item_details[reservation_type]" value="hotel_room">


                <div class="form-group">
                    <label for="name_booking"><i class="fas fa-user"></i> Imię i Nazwisko na rezerwacji:</label>
                    <input type="text" id="name_booking" name="item_details[booking_name]" required>
                </div>
                <div class="form-group">
                    <label for="email_booking"><i class="fas fa-envelope"></i> Adres Email kontaktowy:</label>
                    <input type="email" id="email_booking" name="item_details[booking_email]" required>
                </div>
                <div class="form-group">
                    <label for="phone_booking"><i class="fas fa-phone"></i> Telefon kontaktowy:</label>
                    <input type="tel" id="phone_booking" name="item_details[booking_phone]" required>
                </div>

                <div class="form-group">
                    <label for="roomTypeSelect"><i class="fas fa-bed"></i> Typ Pokoju:</label>
                    <select id="roomTypeSelect" name="product_id" required>
                        <option value="" disabled selected>-- Wybierz typ pokoju --</option>
                        <?php
                        // Pobierz pokoje hotelowe z bazy
                        $stmtRooms = $pdo->prepare(
                            "SELECT p.product_id, p.name, p.price
                             FROM Products p
                             JOIN Categories c ON p.category_id = c.category_id
                             WHERE c.name = 'Hotel - Pokoje' AND p.is_active = TRUE ORDER BY p.name"
                        );
                        $stmtRooms->execute();
                        while ($room = $stmtRooms->fetch()):
                        ?>
                            <option value="<?php echo $room['product_id']; ?>">
                                <?php echo e($room['name']) . " (" . number_format($room['price'], 2, ',', ' ') . " PLN/noc)"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="checkin"><i class="fas fa-calendar-alt"></i> Data Przyjazdu:</label>
                    <input type="date" id="checkin" name="item_details[check_in_date]" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="checkout"><i class="fas fa-calendar-alt"></i> Data Wyjazdu:</label>
                    <input type="date" id="checkout" name="item_details[check_out_date]" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="guests"><i class="fas fa-users"></i> Liczba Gości:</label>
                    <input type="number" id="guests" name="item_details[num_guests]" min="1" value="1" required>
                </div>
                 <div class="form-group">
                    <label for="notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi (opcjonalnie):</label>
                    <textarea id="notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, preferencje"></textarea>
                </div>

                <button type="submit" class="cta-button form-submit-button">Dodaj do koszyka i rezerwuj</button>
            </form>
        </div>
    </div>
</div>

<script src="../script.js"></script> 
<link rel="stylesheet" href="../style.css">
<?php
// Nie ma potrzeby standardowej stopki, bo to strona formularza
// Możesz dodać minimalną stopkę lub pominąć.
?>
</body>
</html>