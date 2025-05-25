<?php
// cart_view.php
require_once __DIR__ . '/config/init.php';

$page_title = "Twój Koszyk - AquaParadise";

$cart_items_display = [];
$grand_total_display = 0;
$current_cart_id_view = null;

if (!isset($pdo)) {
    if (session_status() == PHP_SESSION_NONE) session_start();
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Krytyczny błąd konfiguracji serwera.'];
    error_log("cart_view.php: Krytyczny błąd - obiekt PDO nie jest dostępny po dołączeniu init.php.");
} else {
    $current_cart_id_view = get_current_cart_id($pdo);
}

if ($current_cart_id_view && isset($pdo)) {
    try {
        $stmt_items = $pdo->prepare(
            "SELECT ci.cart_item_id, ci.quantity, ci.price_at_addition, ci.item_details,
                    p.product_id, p.name as product_name, p.image_url, 
                    cat.name as category_name, cat.category_id
             FROM CartItems ci
             JOIN Products p ON ci.product_id = p.product_id
             LEFT JOIN Categories cat ON p.category_id = cat.category_id 
             WHERE ci.cart_id = :cart_id
             ORDER BY ci.added_at DESC"
        );
        $stmt_items->bindParam(':cart_id', $current_cart_id_view, PDO::PARAM_INT);
        $stmt_items->execute();
        $fetched_items_view = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fetched_items_view as $item) {
            $item_total = $item['quantity'] * $item['price_at_addition'];
            $item_details_parsed = $item['item_details'] ? json_decode($item['item_details'], true) : [];
            
            // --- ZAKTUALIZOWANA LOGIKA DLA CSS-LOGO ---
            $logo_wrapper_class = 'cart-item-logo-css-wrapper'; // Ta klasa jest zdefiniowana w style.css
            $logo_bg_color_class = 'default-logo-bg'; 
            $logo_icon_html = '<i class="fas fa-tag"></i>'; 
            $logo_text = 'Produkt'; 

            $product_name_lower = strtolower($item['product_name'] ?? '');
            $category_name_from_db = $item['category_name'] ?? '';
            $reservation_type = $item_details_parsed['reservation_type'] ?? null;

            // Nadpisanie na podstawie reservation_type (bardziej precyzyjne dla rezerwacji)
            if ($reservation_type === 'restaurant_table') {
                $logo_bg_color_class = 'restaurant-logo-bg';
                $logo_icon_html = '<i class="fas fa-utensils"></i>';
                $logo_text = 'RESTAURACJA';
            } elseif ($reservation_type === 'spa_booking') {
                $logo_bg_color_class = 'spa-logo-bg';
                $logo_icon_html = '<i class="fas fa-spa"></i>';
                $logo_text = 'SPA';
            } elseif ($reservation_type === 'hotel_room') {
                $logo_bg_color_class = 'hotel-logo-bg';
                $logo_icon_html = '<i class="fas fa-concierge-bell"></i>';
                $logo_text = 'HOTEL';
            } else { // Fallback na kategorię lub nazwę produktu, jeśli reservation_type nie pasuje
                if (stripos($category_name_from_db, 'Restauracja') !== false || stripos($product_name_lower, 'restauracj') !== false) {
                    $logo_bg_color_class = 'restaurant-logo-bg';
                    $logo_icon_html = '<i class="fas fa-utensils"></i>';
                    $logo_text = 'RESTAURACJA';
                } elseif (stripos($category_name_from_db, 'SPA') !== false || stripos($product_name_lower, 'spa') !== false) {
                    $logo_bg_color_class = 'spa-logo-bg';
                    $logo_icon_html = '<i class="fas fa-spa"></i>';
                    $logo_text = 'SPA';
                } elseif (stripos($category_name_from_db, 'Hotel') !== false || stripos($product_name_lower, 'hotel') !== false || stripos($product_name_lower, 'apartament') !== false) {
                    $logo_bg_color_class = 'hotel-logo-bg';
                    $logo_icon_html = '<i class="fas fa-concierge-bell"></i>';
                    $logo_text = 'HOTEL';
                } elseif (stripos($category_name_from_db, 'Aquapark') !== false || stripos($product_name_lower, 'aquapark') !== false || stripos($product_name_lower, 'bilet') !== false) {
                    $logo_bg_color_class = 'aquapark-logo-bg';
                    $logo_icon_html = '<i class="fas fa-swimmer"></i>';
                    $logo_text = 'AQUAPARK';
                }
            }
            // --- KONIEC ZAKTUALIZOWANEJ LOGIKI DLA CSS-LOGO ---

            $cart_items_display[] = [
                'cart_item_id' => $item['cart_item_id'],
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'category_name' => $category_name_from_db,
                'logo_wrapper_class' => $logo_wrapper_class,
                'logo_bg_color_class' => $logo_bg_color_class,
                'logo_icon_html' => $logo_icon_html,
                'logo_text' => $logo_text,
                'quantity' => $item['quantity'],
                'price_at_addition' => $item['price_at_addition'],
                'item_details_parsed' => $item_details_parsed,
                'total_price' => $item_total,
                'reservation_type' => $reservation_type // Dodajemy typ rezerwacji dla uproszczenia wyświetlania
            ];
            $grand_total_display += $item_total;
        }
    } catch (PDOException $e) {
        error_log("Błąd pobierania przedmiotów koszyka w cart_view.php: " . $e->getMessage());
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas ładowania zawartości koszyka.'];
    }
}

include BASE_PATH . '/includes/header.php';
?>

<?php include BASE_PATH . '/includes/navigation.php'; ?>

<div class="page-wrapper" style="text-align: left; max-width: 1200px; margin: 2em auto;">
    <section class="section-title-container">
        <h2>Twój Koszyk</h2>
    </section>

    <?php if (function_exists('display_flash_message')) { display_flash_message(); } ?>

    <?php if (empty($cart_items_display)): ?>
        <div class="empty-cart-message">
            <p><i class="fas fa-shopping-cart"></i></p>
            <p>Twój koszyk jest pusty.</p>
            <p><a href="<?php echo htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>index.php" class="cta-button" style="margin-top:15px;">Wróć do sklepu</a></p>
        </div>
    <?php else: ?>
        <table class="cart-table">
             <thead>
                <tr>
                    <th class="logo-cell-header" style="width: 80px;"></th>
                    <th>Produkt/Usługa</th>
                    <th>Cena jedn.</th>
                    <th>Ilość</th>
                    <th>Suma częściowa</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items_display as $item): ?>
                    <tr>
                        <td class="logo-cell">
                            <div class="<?php echo htmlspecialchars($item['logo_wrapper_class']); ?> <?php echo htmlspecialchars($item['logo_bg_color_class']); ?>">
                                <div class="logo-icon"><?php echo $item['logo_icon_html']; ?></div>
                                <div class="logo-text"><?php echo htmlspecialchars($item['logo_text'], ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <?php if (!empty($item['item_details_parsed'])): ?>
                            <ul class="item-details-list">
                                <?php
                                // Uproszczone wyświetlanie szczegółów w zależności od typu rezerwacji
                                $details_to_show = [];
                                if ($item['reservation_type'] === 'restaurant_table') {
                                    if (isset($item['item_details_parsed']['reservation_date'])) $details_to_show['Data'] = $item['item_details_parsed']['reservation_date'];
                                    if (isset($item['item_details_parsed']['reservation_time'])) $details_to_show['Godzina'] = $item['item_details_parsed']['reservation_time'];
                                    if (isset($item_details_parsed['num_guests'])) $details_to_show['Liczba osób'] = $item['item_details_parsed']['num_guests'];
                                    if (!empty($item['item_details_parsed']['notes'])) $details_to_show['Notatki'] = $item['item_details_parsed']['notes'];
                                } elseif ($item['reservation_type'] === 'hotel_room') {
                                    if (isset($item['item_details_parsed']['check_in_date'])) $details_to_show['Zameldowanie'] = $item['item_details_parsed']['check_in_date'];
                                    if (isset($item['item_details_parsed']['check_out_date'])) $details_to_show['Wymeldowanie'] = $item['item_details_parsed']['check_out_date'];
                                    if (isset($item['item_details_parsed']['num_guests'])) $details_to_show['Liczba gości'] = $item['item_details_parsed']['num_guests'];
                                    if (!empty($item['item_details_parsed']['notes'])) $details_to_show['Notatki'] = $item['item_details_parsed']['notes'];
                                } elseif ($item['reservation_type'] === 'spa_booking') {
                                    if (isset($item['item_details_parsed']['treatment_date'])) $details_to_show['Data zabiegu'] = $item['item_details_parsed']['treatment_date'];
                                    if (isset($item['item_details_parsed']['treatment_time'])) $details_to_show['Godzina zabiegu'] = $item['item_details_parsed']['treatment_time'];
                                    if (isset($item['item_details_parsed']['selected_treatments_ids_string'])) $details_to_show['Wybrane zabiegi (ID)'] = $item['item_details_parsed']['selected_treatments_ids_string']; // Można później zamienić ID na nazwy
                                    if (!empty($item['item_details_parsed']['notes'])) $details_to_show['Notatki'] = $item['item_details_parsed']['notes'];
                                }
                                // Dla innych typów produktów można dodać kolejne warunki lub nie wyświetlać szczegółów item_details
                                ?>
                                <?php foreach ($details_to_show as $label => $value): ?>
                                <li><strong><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars((is_array($value) ? json_encode($value) : $value), ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format($item['price_at_addition'], 2, ',', ' '); ?> PLN</td>
                        <td>
                            <form action="<?php echo htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>cart_actions.php" method="POST" style="display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="action" value="update_quantity">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" style="width: 60px;" class="cart-quantity-input">
                                <button type="submit" class="action-btn update-btn" title="Aktualizuj ilość"><i class="fas fa-sync-alt"></i></button>
                            </form>
                        </td>
                        <td><strong><?php echo number_format($item['total_price'], 2, ',', ' '); ?> PLN</strong></td>
                        <td>
                            <form action="<?php echo htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="remove_from_cart">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <button type="submit" class="action-btn remove-btn" title="Usuń z koszyka" onclick="return confirm('Czy na pewno chcesz usunąć ten przedmiot z koszyka?');"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-summary"><h3>Suma całkowita: <?php echo number_format($grand_total_display, 2, ',', ' '); ?> PLN</h3></div>
        <div class="cart-actions">
            <a href="<?php echo htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8'); ?>index.php" class="cta-button secondary-cta"><i class="fas fa-arrow-left"></i> Kontynuuj zakupy</a>
            <button type="button" id="openPaymentModalBtn" class="cta-button"><i class="fas fa-credit-card"></i> Przejdź do kasy</button>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($cart_items_display) && file_exists(BASE_PATH . '/includes/payment_modal.php')): ?>
    <?php include BASE_PATH . '/includes/payment_modal.php'; ?>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>