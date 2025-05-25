<?php
// restaurant_assets/restaurant_b.php
require_once dirname(__DIR__) . '/config/init.php';

$page_title = "Rezerwacja Stolika - AquaParadise";

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'AquaParadise'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <?php
   
    $css_path = "../style.css"; // Ścieżka względna z restaurant_assets do głównego katalogu
    if (defined('BASE_URL')) { // Jeśli BASE_URL jest zdefiniowane i poprawne, można go użyć
    }
    ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>?v=<?php echo time(); ?>">
    <script>
        var isLoggedInFromPHP = <?php echo (isset($_SESSION['user_id']) ? 'true' : 'false'); ?>;
        var basePathJS = '<?php echo defined('BASE_URL') ? addslashes(BASE_URL) : "/"; ?>';
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
            <h2>Rezerwacja Stolika w Restauracji</h2>
            <?php /* Reszta formularza bez zmian, jak w poprzedniej odpowiedzi */ ?>
            <form action="<?php echo BASE_URL; ?>cart_actions.php" method="POST" id="restaurantReservationForm">
                <input type="hidden" name="action" value="add_to_cart">
                <?php
                    $reservationProductId = null;
                    $reservationProductName = 'Rezerwacja Stolika w Restauracji';
                    if(isset($pdo)){
                        try {
                            $stmtResProd = $pdo->prepare("SELECT product_id FROM Products WHERE name = :name LIMIT 1");
                            $stmtResProd->bindParam(':name', $reservationProductName, PDO::PARAM_STR);
                            $stmtResProd->execute();
                            $resProd = $stmtResProd->fetch();
                            if($resProd) {
                                $reservationProductId = $resProd['product_id'];
                            } else {
                                error_log("restaurant_b.php: Produkt placeholder '" . $reservationProductName . "' nie został znaleziony w bazie danych.");
                            }
                        } catch (PDOException $e) {
                            error_log("restaurant_b.php: Błąd pobierania ID produktu rezerwacji restauracji: " . $e->getMessage());
                        }
                    } else {
                         error_log("restaurant_b.php: Krytyczny błąd - brak obiektu PDO.");
                    }
                ?>
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

                <button type="submit" class="cta-button form-submit-button" <?php if(empty($reservationProductId)) echo 'disabled title="Produkt rezerwacji nie jest dostępny"'; ?>>
                    <?php echo empty($reservationProductId) ? 'Rezerwacja chwilowo niedostępna' : 'Zarezerwuj Stolik'; ?>
                </button>
                <?php if(empty($reservationProductId)): ?>
                    <p style="color:red; text-align:center; margin-top:10px;">Formularz rezerwacji jest tymczasowo nieaktywny z powodu braku konfiguracji produktu rezerwacyjnego.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
