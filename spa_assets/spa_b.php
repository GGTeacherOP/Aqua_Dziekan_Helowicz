<?php
require_once __DIR__ . '/../config/init.php';
$page_title = "Rezerwacja Spa - AquaParadise";

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
            <h2>Zarezerwuj Wizytę w Spa</h2>
            <?php
            if (function_exists('display_flash_message')) {
                display_flash_message();
            }
            ?>

            <form action="../cart_actions.php" method="POST" id="spaBookingForm">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="quantity" value="1">

                <?php
                    // Definicja $customSpaPackageProductId DLA FORMULARZA
                    // To ID produktu będzie używane, gdy użytkownik rezerwuje pakiet indywidualny z modala
                    $form_product_id_for_custom_spa = null;
                    $placeholderProductName = 'Pakiet SPA Indywidualny'; // Upewnij się, że taki produkt istnieje
                    if(isset($pdo)) {
                        $stmtFormPkg = $pdo->prepare("SELECT product_id FROM Products WHERE name = :placeholderName LIMIT 1");
                        $stmtFormPkg->bindParam(':placeholderName', $placeholderProductName, PDO::PARAM_STR);
                        $stmtFormPkg->execute();
                        $formPkgProduct = $stmtFormPkg->fetch();
                        if ($formPkgProduct) {
                            $form_product_id_for_custom_spa = $formPkgProduct['product_id'];
                        } else {
                            error_log("Produkt placeholder dla formularza SPA ('" . $placeholderProductName . "') nie został znaleziony. Używam fallback ID.");
                            $form_product_id_for_custom_spa = '99999'; // Lub inne stałe ID dla "niestandardowego pakietu"
                        }
                    } else {
                        error_log("Brak obiektu PDO w spa_b.php przy pobieraniu ID produktu placeholder dla formularza.");
                        $form_product_id_for_custom_spa = '99999';
                    }
                ?>
                <input type="hidden" id="formProductIdCustomSpa" name="product_id" value="<?php echo e($form_product_id_for_custom_spa); ?>">

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
                    <input type="time" id="spa_booking_time" name="item_details[treatment_time]" required>
                </div>
                <div class="form-group">
                    <label for="spaPackageButton"><i class="fas fa-spa"></i> Wybór Masażu/Pakietu:</label>
                    <button type="button" id="spaPackageButton" class="cta-button secondary-cta">Wybierz Masaż lub Pakiet</button>
                    <input type="hidden" id="selectedSpaPackages" name="selected_spa_treatments_ids_temp_holder">
                    <div id="selectedTreatmentsDisplay" class="selected-treatments-display"></div>
                </div>
                <div class="form-group">
                    <label for="spa_booking_notes"><i class="fas fa-sticky-note"></i> Dodatkowe Uwagi:</label>
                    <textarea id="spa_booking_notes" name="item_details[notes]" rows="2" placeholder="Specjalne życzenia, preferencje"></textarea>
                </div>
                <button type="submit" class="cta-button form-submit-button">Dodaj do koszyka i rezerwuj</button>
            </form>
        </div>
    </div>
</div>

<div id="spaPackagesModal" class="modal">
    <div class="modal-content wide-modal">
        <span class="close-button" id="closeSpaModalBtn">&times;</span>
        <h3>Wybierz Masaże i Pakiety</h3>
        <div id="spaPackagesHorizontalList" class="modal-horizontal-options-list">
           <?php
// --- POCZĄTEK BLOKU POBIERANIA DANYCH SPA DLA MODALA ---
$allSpaProducts = [];
$debugMessages = [];
// ID produktu "Pakiet SPA Indywidualny" (placeholder) jest już zdefiniowane wyżej jako $form_product_id_for_custom_spa
// Będziemy go używać do wykluczenia z listy w modalu.
$excluded_id_for_modal = $form_product_id_for_custom_spa; // Powinno być zdefiniowane wcześniej w tym pliku

if (isset($pdo)) {
    try {
        $debugMessages[] = "Pobieranie produktów SPA dla modala (wykluczając produkt placeholder o ID: " . htmlspecialchars((string)$excluded_id_for_modal) . ")...";
        
        $sql = "SELECT p.product_id, p.name, p.description, p.price, 
               c.name as category_name_from_db, -- Ważne dla JS!
               c.category_id as cat_id_debug 
                FROM Products p
                JOIN Categories c ON p.category_id = c.category_id
                WHERE c.name LIKE 'SPA - %' AND p.is_active = TRUE"; // Ten warunek jest kluczowy

        if (!empty($excluded_id_for_modal) && ($excluded_id_for_modal != '99999' && $excluded_id_for_modal != 0)) {
            $sql .= " AND p.product_id != :excluded_id_for_modal";
        }
        $sql .= " ORDER BY c.category_id ASC, p.name ASC";

        $stmtSpaProducts = $pdo->prepare($sql);
        if (!empty($excluded_id_for_modal) && ($excluded_id_for_modal != '99999' && $excluded_id_for_modal != 0)) {
            $stmtSpaProducts->bindParam(':excluded_id_for_modal', $excluded_id_for_modal, PDO::PARAM_INT);
        }
        $stmtSpaProducts->execute();
        $allSpaProducts = $stmtSpaProducts->fetchAll(PDO::FETCH_ASSOC);

        $debugMessages[] = "  -> Użyte zapytanie SQL: " . htmlspecialchars($sql);
        $debugMessages[] = "  -> Liczba wszystkich pobranych produktów SPA (przed grupowaniem w JS): " . count($allSpaProducts);

        if (count($allSpaProducts) > 0) {
            $uniqueCategoriesFetched = array_unique(array_column($allSpaProducts, 'category_name_from_db'));
            $debugMessages[] = "  -> Unikalne nazwy kategorii ('category_name_from_db') pobrane z bazy: " . htmlspecialchars(implode(' | ', $uniqueCategoriesFetched));
            // ... reszta logów debugujących
        } else {
             $debugMessages[] = "  -> KRYTYCZNE OSTRZEŻENIE: Nie pobrano żadnych produktów SPA do wyświetlenia w modalu. Sprawdź warunki w zapytaniu SQL (np. `c.name LIKE 'SPA - %'`, `p.is_active = TRUE`) oraz czy produkty są poprawnie przypisane do tych kategorii w tabeli 'Products'.";
        }

    } catch (PDOException $e) {
        // ... obsługa błędu ...
    }
} else {
    // ... obsługa braku PDO ...
}

// Wyświetl logi debugujące jako komentarz HTML (widoczne w źródle strony Ctrl+U)
echo "\n\n";

$jsonSpaProducts = json_encode($allSpaProducts);
if ($jsonSpaProducts === false) {
    // ... obsługa błędu json_encode ...
    echo "<script>var allSpaProductsFromPHP = [];</script>\n";
} else {
    echo "<script>var allSpaProductsFromPHP = " . $jsonSpaProducts . ";</script>\n";
}
// --- KONIEC BLOKU POBIERANIA DANYCH SPA ---
?>
        </div>
        <button id="confirmSpaSelection" class="cta-button">Potwierdź Wybór</button>
    </div>
</div>

<script src="../script.js"></script>
</body>
</html>