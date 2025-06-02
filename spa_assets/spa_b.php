<?php
// Plik: spa_assets/spa_b.php
require_once dirname(__DIR__) . '/config/init.php';

$page_title = "Rezerwacja Spa - AquaParadise";

// Definicja $form_product_id_for_custom_spa
$form_product_id_for_custom_spa = null;
$placeholderProductName = 'Pakiet SPA Indywidualny'; // ID 15 w Twojej bazie
if (isset($pdo)) {
    try {
        $stmtFormPkg = $pdo->prepare("SELECT product_id FROM Products WHERE name = :placeholderName AND is_active = TRUE LIMIT 1");
        $stmtFormPkg->bindParam(':placeholderName', $placeholderProductName, PDO::PARAM_STR);
        $stmtFormPkg->execute();
        $formPkgProduct = $stmtFormPkg->fetch();
        if ($formPkgProduct) {
            $form_product_id_for_custom_spa = $formPkgProduct['product_id'];
        } else {
            error_log("spa_b.php: Produkt placeholder SPA ('" . e($placeholderProductName) . "') nie został znaleziony lub jest nieaktywny.");
        }
    } catch (PDOException $e) {
        error_log("spa_b.php: Błąd pobierania ID produktu placeholder SPA: " . $e->getMessage());
    }
}

$allSpaProductsForJS = [];
if (isset($pdo)) {
    try {
        $sql_modal = "SELECT p.product_id, p.name, p.description, p.price, c.name as category_name_from_db
                      FROM Products p
                      JOIN Categories c ON p.category_id = c.category_id
                      WHERE c.name LIKE 'SPA - %' AND p.is_active = TRUE";
        $excluded_id_for_modal = $form_product_id_for_custom_spa;
        if (!empty($excluded_id_for_modal) && is_numeric($excluded_id_for_modal) && $excluded_id_for_modal > 0) {
             $sql_modal .= " AND p.product_id != :excluded_id_for_modal";
        }
        $sql_modal .= " ORDER BY c.name ASC, p.price ASC, p.name ASC"; // Sortowanie
        $stmtSpaProductsModal = $pdo->prepare($sql_modal);
        if (!empty($excluded_id_for_modal) && is_numeric($excluded_id_for_modal) && $excluded_id_for_modal > 0) {
            $stmtSpaProductsModal->bindParam(':excluded_id_for_modal', $excluded_id_for_modal, PDO::PARAM_INT);
        }
        $stmtSpaProductsModal->execute();
        $allSpaProductsForJS = $stmtSpaProductsModal->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { error_log("spa_b.php: Błąd pobierania produktów SPA dla modala: " . $e->getMessage());}
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
        var allSpaProductsFromPHP = <?php echo json_encode($allSpaProductsForJS); ?>;
    </script>
</head>
<body>

<div class="auth-page-container">
    <div class="return-button-container">
        <a href="<?php echo BASE_URL; ?>index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form booking-form">
            <h2>Zarezerwuj Wizytę w Spa</h2>
            <?php if (function_exists('display_flash_message')) { display_flash_message(); } ?>

            <form action="<?php echo BASE_URL; ?>cart_actions.php" method="POST" id="spaBookingForm">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" id="formProductId" name="product_id" value="<?php echo e($form_product_id_for_custom_spa ?? ''); ?>">
                <input type="hidden" name="item_details[reservation_type]" value="spa_booking">
                <?php if($form_product_id_for_custom_spa): ?>
                    <input type="hidden" name="item_details[placeholder_spa_product_id]" value="<?php echo e($form_product_id_for_custom_spa); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="spa_booking_name"><i class="fas fa-user"></i> Imię i Nazwisko:</label>
                    <input type="text" id="spa_booking_name" name="item_details[booking_name]" placeholder="Wpisz swoje imię i nazwisko" required>
                </div>
                <div class="form-group">
                    <label for="spa_booking_email"><i class="fas fa-envelope"></i> Adres Email:</label>
                    <input type="email" id="spa_booking_email" name="item_details[booking_email]" placeholder="Wpisz swój adres email" required>
                </div>
                <div class="form-group">
                    <label for="spa_booking_phone"><i class="fas fa-phone"></i> Numer Telefonu:</label>
                    <input type="tel" id="spa_booking_phone" name="item_details[booking_phone]" placeholder="Wpisz swój numer telefonu" required>
                </div>
                <div class="form-group">
                    <label for="spa_booking_date"><i class="fas fa-calendar-alt"></i> Preferowana Data:</label>
                    <input type="date" id="spa_booking_date" name="item_details[treatment_date]" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                 <div class="form-group">
                    <label for="spa_booking_time"><i class="fas fa-clock"></i> Preferowana Godzina:</label>
                    <select id="spa_booking_time" name="item_details[treatment_time]" required>
                        <option value="" disabled selected>-- Wybierz datę, aby załadować godziny --</option>
                    </select>
                    <small id="spa_time_status" style="display:block; margin-top:5px;"></small>
                </div>
                <div class="form-group">
                    <label for="spaPackageButton"><i class="fas fa-spa"></i> Wybór Zabiegów/Pakietu:</label>
                    <button type="button" id="spaPackageButton" class="cta-button secondary-cta">Wybierz Zabiegi lub Gotowy Pakiet</button>
                    <input type="hidden" id="selectedSpaItems" name="item_details[selected_treatments_ids_string]">
                    <div id="selectedTreatmentsDisplay" class="selected-treatments-display"></div>
                    <input type="hidden" id="calculatedSpaPrice" name="item_details[total_price_for_selected_spa]" value="0.00">
                </div>
                <div class="form-group">
                    <label for="spa_booking_notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                    <textarea id="spa_booking_notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, preferencje"></textarea>
                </div>
                <button type="submit" id="spaSubmitButton" class="cta-button form-submit-button" <?php if(empty($form_product_id_for_custom_spa)) echo 'disabled title="Funkcja rezerwacji indywidualnych pakietów SPA jest tymczasowo niedostępna."'; ?>>
                     <?php echo empty($form_product_id_for_custom_spa) ? 'Funkcja niedostępna' : 'Dodaj do koszyka i rezerwuj'; ?>
                </button>
                 <?php if(empty($form_product_id_for_custom_spa)): ?>
                    <p style="color:red; text-align:center; margin-top:10px;">Rezerwacja indywidualnych pakietów SPA jest chwilowo niemożliwa. Prosimy wybrać gotowy pakiet lub skontaktować się z obsługą.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div id="spaPackagesModal" class="modal">
    <div class="modal-content wide-modal">
        <span class="close-button" id="closeSpaModalBtn">&times;</span>
        <h3>Wybierz Zabiegi lub Gotowe Pakiety</h3>
        <div id="spaPackagesHorizontalList" class="modal-horizontal-options-list">
            {/* Wypełniane przez JS używając allSpaProductsFromPHP */}
        </div>
        <button id="confirmSpaSelection" class="cta-button">Potwierdź Wybór</button>
    </div>
</div>
<div id="authGuestModalContainer" style="position: relative; z-index: 2000;"></div>

<script src="<?php echo BASE_URL; ?>script.js?v=<?php echo file_exists(BASE_PATH . '/script.js') ? filemtime(BASE_PATH . '/script.js') : time(); ?>"></script>
<script>
// Ten skrypt jest specyficzny dla spa_b.php i powinien być tutaj lub w osobnym pliku dołączanym tylko na tej stronie.
document.addEventListener('DOMContentLoaded', function() {
    const spaDateInput = document.getElementById('spa_booking_date');
    const spaTimeSelect = document.getElementById('spa_booking_time');
    const spaTimeStatus = document.getElementById('spa_time_status');
    const spaSubmitButton = document.getElementById('spaSubmitButton'); // ID dodane do przycisku submit

    function updateSpaTimeSlots(selectedDate) {
        if (!spaTimeSelect || !spaTimeStatus || !basePathJS) return;

        spaTimeSelect.innerHTML = '<option value="" disabled selected>Ładowanie godzin...</option>';
        spaTimeStatus.textContent = 'Sprawdzanie dostępności...';
        spaTimeStatus.style.color = 'inherit';
        if (spaSubmitButton) spaSubmitButton.disabled = true;

        fetch(`${basePathJS}ajax_get_spa_availability.php?date=${selectedDate}`)
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok: ' + response.statusText); }
                return response.json();
            })
            .then(data => {
                spaTimeSelect.innerHTML = '<option value="" disabled selected>-- Wybierz godzinę --</option>';
                if (data.error) {
                    console.error("Błąd pobierania dostępności SPA:", data.error);
                    spaTimeStatus.textContent = data.error;
                    spaTimeStatus.style.color = 'red';
                    return;
                }

                if (data.all_possible_slots && Array.isArray(data.all_possible_slots)) {
                    if (data.all_possible_slots.length === 0) {
                        spaTimeStatus.textContent = 'Brak zdefiniowanych godzin dla tego dnia.';
                        spaTimeStatus.style.color = 'orange';
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
                        spaTimeSelect.appendChild(option);
                    });
                    
                    const availableOptions = Array.from(spaTimeSelect.options).filter(opt => !opt.disabled && opt.value !== "").length;
                    if (availableOptions === 0 && data.all_possible_slots.length > 0) {
                         spaTimeStatus.textContent = 'Wszystkie godziny w tym dniu są zajęte.';
                         spaTimeStatus.style.color = 'red';
                    } else if (availableOptions > 0) {
                         spaTimeStatus.textContent = ''; // Są dostępne godziny
                    }

                } else {
                    spaTimeStatus.textContent = 'Brak zdefiniowanych godzin dla tego dnia.';
                    spaTimeStatus.style.color = 'orange';
                }
            })
            .catch(error => {
                console.error('Błąd Fetch przy sprawdzaniu dostępności SPA:', error);
                spaTimeStatus.textContent = 'Nie udało się załadować dostępnych godzin.';
                spaTimeStatus.style.color = 'red';
                spaTimeSelect.innerHTML = '<option value="" disabled selected>-- Błąd serwera --</option>';
            });
    }

    if (spaDateInput && spaTimeSelect) {
        spaDateInput.addEventListener('change', function() {
            if (this.value) {
                updateSpaTimeSlots(this.value);
            } else {
                spaTimeSelect.innerHTML = '<option value="" disabled selected>-- Wybierz datę --</option>';
                spaTimeStatus.textContent = '';
                if(spaSubmitButton) spaSubmitButton.disabled = true;
            }
        });
        
        spaTimeSelect.addEventListener('change', function() {
            if (this.value && !this.options[this.selectedIndex].disabled) {
                if(spaSubmitButton) spaSubmitButton.disabled = false;
                spaTimeStatus.textContent = '';
            } else {
                if(spaSubmitButton) spaSubmitButton.disabled = true;
                if (this.value) { 
                     spaTimeStatus.textContent = 'Wybrana godzina jest niedostępna.';
                     spaTimeStatus.style.color = 'red';
                }
            }
        });

        if (spaDateInput.value) {
            updateSpaTimeSlots(spaDateInput.value);
        } else {
            if(spaSubmitButton) spaSubmitButton.disabled = true;
        }
    }
});
</script>
</body>
</html>