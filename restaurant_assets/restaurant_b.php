<?php
// restaurant_assets/restaurant_b.php
require_once dirname(__DIR__) . '/config/init.php';

$page_title = "Rezerwacja Stolika - AquaParadise";

$reservationProductId = null;
$reservationProductName = 'Rezerwacja Stolika w Restauracji'; 
if(isset($pdo)){
    try {
        $stmtResProd = $pdo->prepare("SELECT product_id FROM Products WHERE name = :name AND is_active = TRUE LIMIT 1");
        $stmtResProd->bindParam(':name', $reservationProductName, PDO::PARAM_STR);
        $stmtResProd->execute();
        $resProd = $stmtResProd->fetch();
        if($resProd) {
            $reservationProductId = $resProd['product_id'];
        } else {
            error_log("restaurant_b.php: Produkt placeholder '" . e($reservationProductName) . "' nie został znaleziony lub jest nieaktywny.");
        }
    } catch (PDOException $e) {
        error_log("restaurant_b.php: Błąd pobierania ID produktu rezerwacji restauracji: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css?v=<?php echo file_exists(BASE_PATH . '/style.css') ? filemtime(BASE_PATH . '/style.css') : time(); ?>">
    <script>
        var isLoggedInFromPHP = <?php echo (isset($_SESSION['user_id']) ? 'true' : 'false'); ?>;
        var basePathJS = '<?php echo addslashes(BASE_URL); ?>';
        var currentUserFirstNameFromPHP = '<?php echo addslashes($_SESSION['user_first_name'] ?? ''); ?>';
        var currentUserLastNameFromPHP = '<?php echo addslashes($_SESSION['user_last_name'] ?? ''); ?>';
        var currentUserEmailFromPHP = '<?php echo addslashes($_SESSION['user_email'] ?? ''); ?>';
        var restaurantReservationProductIdJS = <?php echo json_encode($reservationProductId); ?>; // Przekaż ID produktu do JS
    </script>
</head>
<body>
<div class="auth-page-container">
    <div class="return-button-container">
        <a href="<?php echo BASE_URL; ?>index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form booking-form">
            <h2>Rezerwacja Stolika w Restauracji</h2>
            <?php if(function_exists('display_flash_message')) { display_flash_message(); } ?>

            <form action="<?php echo BASE_URL; ?>cart_actions.php" method="POST" id="restaurantReservationForm">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="<?php echo e($reservationProductId ?? ''); ?>">
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
                    <select id="res_time" name="item_details[reservation_time]" required>
                        <option value="" disabled selected>-- Wybierz datę, aby załadować godziny --</option>
                    </select>
                    <small id="res_time_status" style="display:block; margin-top:5px;"></small>
                </div>
                <div class="form-group">
                    <label for="res_guests"><i class="fas fa-users"></i> Liczba Osób:</label>
                    <input type="number" id="res_guests" name="item_details[num_guests]" min="1" value="1" placeholder="Podaj liczbę osób" required>
                </div>
                <div class="form-group">
                    <label for="res_notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                    <textarea id="res_notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, alergie itp."></textarea>
                </div>

                <button type="submit" id="restaurantSubmitButton" class="cta-button form-submit-button" <?php if(empty($reservationProductId)) echo 'disabled title="Produkt rezerwacji nie jest dostępny"'; ?>>
                    <?php echo empty($reservationProductId) ? 'Rezerwacja chwilowo niedostępna' : 'Zarezerwuj Stolik'; ?>
                </button>
                <?php if(empty($reservationProductId)): ?>
                    <p style="color:red; text-align:center; margin-top:10px;">Formularz rezerwacji jest tymczasowo nieaktywny z powodu braku konfiguracji produktu rezerwacyjnego.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<div id="authGuestModalContainer" style="position: relative; z-index: 2000;"></div>
<script src="<?php echo BASE_URL; ?>script.js?v=<?php echo file_exists(BASE_PATH . '/script.js') ? filemtime(BASE_PATH . '/script.js') : time(); ?>"></script>
<script>
// Ten skrypt jest specyficzny dla restaurant_b.php
document.addEventListener('DOMContentLoaded', function() {
    const resDateInput = document.getElementById('res_date');
    const resTimeSelect = document.getElementById('res_time');
    const resTimeStatus = document.getElementById('res_time_status');
    const resSubmitButton = document.getElementById('restaurantSubmitButton'); // ID dodane do przycisku submit

    function updateRestaurantTimeSlots(selectedDate) {
        if (!resTimeSelect || !resTimeStatus || !basePathJS || !restaurantReservationProductIdJS) return;

        resTimeSelect.innerHTML = '<option value="" disabled selected>Ładowanie godzin...</option>';
        resTimeStatus.textContent = 'Sprawdzanie dostępności...';
        resTimeStatus.style.color = 'inherit';
        if(resSubmitButton) resSubmitButton.disabled = true;

        fetch(`${basePathJS}ajax_get_restaurant_availability.php?date=${selectedDate}&product_id=${restaurantReservationProductIdJS}`)
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok: ' + response.statusText); }
                return response.json();
            })
            .then(data => {
                resTimeSelect.innerHTML = '<option value="" disabled selected>-- Wybierz godzinę --</option>';
                if (data.error) {
                    console.error("Błąd pobierania dostępności restauracji:", data.error);
                    resTimeStatus.textContent = data.error;
                    resTimeStatus.style.color = 'red';
                    return;
                }

                if (data.all_possible_slots && Array.isArray(data.all_possible_slots)) {
                     if (data.all_possible_slots.length === 0) {
                        resTimeStatus.textContent = 'Brak zdefiniowanych godzin dla tego dnia.';
                        resTimeStatus.style.color = 'orange';
                        return;
                    }
                    data.all_possible_slots.forEach(slotTime => {
                        const option = document.createElement('option');
                        option.value = slotTime;
                        option.textContent = slotTime;
                        if (data.unavailable_slots && data.unavailable_slots.includes(slotTime)) {
                            option.disabled = true;
                            option.style.color = 'red';
                            option.textContent += ' (Zajęte)';
                        }
                        resTimeSelect.appendChild(option);
                    });

                    const availableOptions = Array.from(resTimeSelect.options).filter(opt => !opt.disabled && opt.value !== "").length;
                    if (availableOptions === 0 && data.all_possible_slots.length > 0) {
                         resTimeStatus.textContent = 'Wszystkie godziny w tym dniu są zajęte.';
                         resTimeStatus.style.color = 'red';
                    } else if (availableOptions > 0) {
                         resTimeStatus.textContent = '';
                    }

                } else {
                    resTimeStatus.textContent = 'Brak zdefiniowanych godzin dla tego dnia.';
                    resTimeStatus.style.color = 'orange';
                }
            })
            .catch(error => {
                console.error('Błąd Fetch przy sprawdzaniu dostępności restauracji:', error);
                resTimeStatus.textContent = 'Nie udało się załadować dostępnych godzin.';
                resTimeStatus.style.color = 'red';
                resTimeSelect.innerHTML = '<option value="" disabled selected>-- Błąd serwera --</option>';
            });
    }

    if (resDateInput && resTimeSelect) {
        resDateInput.addEventListener('change', function() {
            if (this.value) {
                updateRestaurantTimeSlots(this.value);
            } else {
                resTimeSelect.innerHTML = '<option value="" disabled selected>-- Wybierz datę --</option>';
                resTimeStatus.textContent = '';
                if(resSubmitButton) resSubmitButton.disabled = true;
            }
        });

        resTimeSelect.addEventListener('change', function() {
            if (this.value && !this.options[this.selectedIndex].disabled) {
                if(resSubmitButton) resSubmitButton.disabled = false;
                resTimeStatus.textContent = '';
            } else {
                if(resSubmitButton) resSubmitButton.disabled = true;
                if (this.value) {
                     resTimeStatus.textContent = 'Wybrana godzina jest niedostępna.';
                     resTimeStatus.style.color = 'red';
                }
            }
        });
        
        if (resDateInput.value) {
            updateRestaurantTimeSlots(resDateInput.value);
        } else {
             if(resSubmitButton) resSubmitButton.disabled = true;
        }
    }
});
</script>
</body>
</html>