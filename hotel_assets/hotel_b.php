<?php
// hotel_assets/hotel_b.php
require_once __DIR__ . '/../config/init.php';
$page_title = "Rezerwacja Hotelu - AquaParadise";

// Nie dołączamy tutaj includes/header.php i footer.php, bo to samodzielna strona formularza
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css?v=<?php echo time(); ?>">
    <script>
        var isLoggedInFromPHP = <?php echo (isset($_SESSION['user_id']) ? 'true' : 'false'); ?>;
        var basePathJS = '<?php echo addslashes(BASE_URL); ?>';
        var currentUserFirstNameFromPHP = '<?php echo addslashes($_SESSION['user_first_name'] ?? ''); ?>';
        var currentUserLastNameFromPHP = '<?php echo addslashes($_SESSION['user_last_name'] ?? ''); ?>';
        var currentUserEmailFromPHP = '<?php echo addslashes($_SESSION['user_email'] ?? ''); ?>';
    </script>
</head>
<body>

<div class="auth-page-container">
    <div class="return-button-container">
        <a href="<?php echo BASE_URL; ?>index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form booking-form">
            <h2>Rezerwacja Pokoju Hotelowego</h2>
            <?php if(function_exists('display_flash_message')) display_flash_message(); ?>

            <form action="<?php echo BASE_URL; ?>cart_actions.php" method="POST" id="hotelReservationForm">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="item_details[reservation_type]" value="hotel_room">

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
                        if (isset($pdo)) {
                            $stmtRooms = $pdo->prepare(
                                "SELECT p.product_id, p.name, p.price, p.availability_details
                                 FROM Products p
                                 JOIN Categories c ON p.category_id = c.category_id
                                 WHERE c.name = 'Hotel - Pokoje' AND p.is_active = TRUE ORDER BY p.name"
                            );
                            $stmtRooms->execute();
                            while ($room = $stmtRooms->fetch()):
                                $details = $room['availability_details'] ? json_decode($room['availability_details'], true) : [];
                                $total_units = $details['total_units'] ?? 30; // Domyślnie 30, jeśli nie ma w JSON
                        ?>
                            <option value="<?php echo $room['product_id']; ?>" data-max-rooms="<?php echo e($total_units); ?>">
                                <?php echo e($room['name']) . " (" . number_format($room['price'], 2, ',', ' ') . " PLN/noc)"; ?>
                            </option>
                        <?php endwhile; 
                        }?>
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
                <div id="availability_status" style="margin-bottom:15px; padding:10px; border-radius:5px; text-align:center;"></div>
                 <div class="form-group">
                    <label for="notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi (opcjonalnie):</label>
                    <textarea id="notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, preferencje"></textarea>
                </div>

                <button type="submit" id="hotelSubmitButton" class="cta-button form-submit-button">Dodaj do koszyka i rezerwuj</button>
            </form>
        </div>
    </div>
</div>
<div id="authGuestModalContainer" style="position: relative; z-index: 2000;"></div>
<script src="<?php echo BASE_URL; ?>script.js?v=<?php echo time(); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const availabilityStatusDiv = document.getElementById('availability_status');
    const hotelSubmitButton = document.getElementById('hotelSubmitButton');

    function checkHotelAvailability() {
        const productId = roomTypeSelect.value;
        const checkinDate = checkinInput.value;
        const checkoutDate = checkoutInput.value;

        availabilityStatusDiv.textContent = 'Sprawdzanie dostępności...';
        availabilityStatusDiv.style.color = 'inherit';
        availabilityStatusDiv.style.backgroundColor = 'transparent';
        if (hotelSubmitButton) hotelSubmitButton.disabled = true;

        if (!productId || !checkinDate || !checkoutDate) {
            availabilityStatusDiv.textContent = 'Proszę wybrać typ pokoju oraz daty.';
            return;
        }

        if (new Date(checkinDate) >= new Date(checkoutDate)) {
            availabilityStatusDiv.textContent = 'Data wymeldowania musi być późniejsza niż data zameldowania.';
            availabilityStatusDiv.style.color = 'red';
            availabilityStatusDiv.style.backgroundColor = '#fdd';
            return;
        }
        
        // console.log(`Checking: ${basePathJS}ajax_get_hotel_availability.php?product_id=${productId}&check_in=${checkinDate}&check_out=${checkoutDate}`);

        fetch(`${basePathJS}ajax_get_hotel_availability.php?product_id=${productId}&check_in=${checkinDate}&check_out=${checkoutDate}`)
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok.'); }
                return response.json();
            })
            .then(data => {
                // console.log("Hotel availability response:", data);
                availabilityStatusDiv.textContent = data.message || 'Nie udało się sprawdzić dostępności.';
                if (data.available) {
                    availabilityStatusDiv.style.color = 'green';
                    availabilityStatusDiv.style.backgroundColor = '#d4edda';
                    if (hotelSubmitButton) hotelSubmitButton.disabled = false;
                } else {
                    availabilityStatusDiv.style.color = 'red';
                    availabilityStatusDiv.style.backgroundColor = '#f8d7da';
                    if (hotelSubmitButton) hotelSubmitButton.disabled = true;
                }
            })
            .catch(error => {
                console.error('Błąd Fetch przy sprawdzaniu dostępności hotelu:', error);
                availabilityStatusDiv.textContent = 'Błąd serwera podczas sprawdzania dostępności.';
                availabilityStatusDiv.style.color = 'red';
                availabilityStatusDiv.style.backgroundColor = '#f8d7da';
                if (hotelSubmitButton) hotelSubmitButton.disabled = true;
            });
    }

    if (roomTypeSelect) roomTypeSelect.addEventListener('change', checkHotelAvailability);
    if (checkinInput) {
        checkinInput.addEventListener('change', function() {
            // Ustaw minimalną datę wymeldowania na dzień po zameldowaniu
            if (checkoutInput && this.value) {
                let nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkoutInput.min = nextDay.toISOString().split('T')[0];
                // Jeśli data wymeldowania jest wcześniejsza niż nowa minimalna, wyczyść ją
                if (new Date(checkoutInput.value) <= new Date(this.value)) {
                    checkoutInput.value = '';
                }
            }
            checkHotelAvailability();
        });
    }
    if (checkoutInput) checkoutInput.addEventListener('change', checkHotelAvailability);

    // Wstępne sprawdzenie przy załadowaniu strony, jeśli daty są już ustawione
    if (roomTypeSelect && checkinInput && checkoutInput && roomTypeSelect.value && checkinInput.value && checkoutInput.value) {
        checkHotelAvailability();
    } else {
        if(hotelSubmitButton) hotelSubmitButton.disabled = true; // Domyślnie zablokuj
    }
});
</script>
</body>
</html>