<?php
// checkout.php
require_once __DIR__ . '/config/init.php';

$page_title = "Finalizacja Zamówienia - AquaParadise";

// Pobierz zawartość koszyka do podsumowania
$cart_items_checkout = [];
$grand_total_checkout = 0;
$current_cart_id_checkout = get_current_cart_id($pdo);

if ($current_cart_id_checkout) {
    $stmt_items = $pdo->prepare(
        "SELECT ci.cart_item_id, ci.quantity, ci.price_at_addition, ci.item_details, p.name as product_name
         FROM CartItems ci
         JOIN Products p ON ci.product_id = p.product_id
         WHERE ci.cart_id = :cart_id"
    );
    $stmt_items->bindParam(':cart_id', $current_cart_id_checkout, PDO::PARAM_INT);
    $stmt_items->execute();
    $fetched_items_checkout = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched_items_checkout as $item) {
        $item_total = $item['quantity'] * $item['price_at_addition'];
        $cart_items_checkout[] = [
            'product_name' => $item['product_name'],
            'quantity' => $item['quantity'],
            'price_at_addition' => $item['price_at_addition'],
            'item_details_parsed' => $item['item_details'] ? json_decode($item['item_details'], true) : [],
            'total_price' => $item_total
        ];
        $grand_total_checkout += $item_total;
    }
}

if (empty($cart_items_checkout) && $_SERVER['REQUEST_METHOD'] !== 'POST') { // Jeśli koszyk jest pusty i nie przetwarzamy formularza
    $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'Twój koszyk jest pusty. Nie można przejść do kasy.'];
    header('Location: cart_view.php');
    exit;
}

$error_message_checkout = '';

// Przetwarzanie formularza zamówienia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($cart_items_checkout)) { // Ponowne sprawdzenie
        $error_message_checkout = "Nie można złożyć zamówienia, koszyk jest pusty.";
    } else {
        $user_id_order = $_SESSION['user_id'] ?? null;
        $guest_email_order = null;
        $guest_name_order = null;

        // Jeśli użytkownik nie jest zalogowany, wymagaj danych gościa
        if (!$user_id_order) {
            $guest_email_order = trim($_POST['guest_email'] ?? '');
            $guest_name_order = trim($_POST['guest_name'] ?? '');
            if (empty($guest_email_order) || !filter_var($guest_email_order, FILTER_VALIDATE_EMAIL) || empty($guest_name_order)) {
                $error_message_checkout = "Jako gość, musisz podać poprawne imię, nazwisko i adres email.";
            }
        } else {
            // Pobierz dane zalogowanego użytkownika, jeśli potrzebne do adresu itp.
            $stmt_user = $pdo->prepare("SELECT first_name, last_name, email FROM Users WHERE user_id = ?");
            $stmt_user->execute([$user_id_order]);
            $user_data_order = $stmt_user->fetch();
            // Można użyć $user_data_order['email'] i ($user_data_order['first_name'] . ' ' . $user_data_order['last_name'])
        }

        $promo_code = trim($_POST['promo_code'] ?? null);
        // TODO: Logika walidacji kodu promocyjnego i ewentualna modyfikacja $grand_total_checkout

        $payment_method_selected = $_POST['payment_method'] ?? 'online'; // Domyślnie lub z formularza

        if (empty($error_message_checkout)) {
            try {
                $pdo->beginTransaction();

                $stmt_order = $pdo->prepare(
                    "INSERT INTO Orders (user_id, guest_email, guest_name, total_amount, order_status, payment_method, payment_status, promo_code_used, notes)
                     VALUES (:user_id, :guest_email, :guest_name, :total_amount, 'Oczekujące', :payment_method, 'Nieopłacone', :promo_code, :notes)"
                );
                $order_notes = $_POST['order_notes'] ?? null; // Dodatkowe uwagi do zamówienia
                $stmt_order->execute([
                    ':user_id' => $user_id_order,
                    ':guest_email' => $guest_email_order,
                    ':guest_name' => $guest_name_order,
                    ':total_amount' => $grand_total_checkout,
                    ':payment_method' => $payment_method_selected,
                    ':promo_code' => !empty($promo_code) ? $promo_code : null,
                    ':notes' => $order_notes
                ]);
                $new_order_id = $pdo->lastInsertId();

                // Dodaj OrderItems
                $stmt_order_item = $pdo->prepare(
                    "INSERT INTO OrderItems (order_id, product_id, quantity, price_per_item, item_details)
                     SELECT :order_id, ci.product_id, ci.quantity, ci.price_at_addition, ci.item_details
                     FROM CartItems ci WHERE ci.cart_id = :cart_id"
                );
                $stmt_order_item->execute([':order_id' => $new_order_id, ':cart_id' => $current_cart_id_checkout]);

                // Wyczyść koszyk po złożeniu zamówienia
                $stmt_clear_cart = $pdo->prepare("DELETE FROM CartItems WHERE cart_id = :cart_id");
                $stmt_clear_cart->execute([':cart_id' => $current_cart_id_checkout]);
                // Opcjonalnie: usuń sam koszyk, jeśli nie jest już potrzebny (lub oznacz jako "converted_to_order")
                // $pdo->prepare("DELETE FROM Carts WHERE cart_id = :cart_id")->execute([':cart_id' => $current_cart_id_checkout]);
                // if (isset($_SESSION['guest_cart_session_id'])) unset($_SESSION['guest_cart_session_id']);
                // if (isset($_SESSION['guest_cart_assigned_db_id'])) unset($_SESSION['guest_cart_assigned_db_id']);


                $pdo->commit();
                $_SESSION['success_message'] = "Twoje zamówienie nr #{$new_order_id} zostało złożone! Dziękujemy.";
                
                // Tutaj normalnie byłoby przekierowanie do bramki płatności lub strony potwierdzenia
                // Na razie przekierujmy na stronę potwierdzenia (którą trzeba stworzyć)
                header("Location: order_confirmation.php?order_id=" . $new_order_id);
                exit;

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Błąd składania zamówienia: " . $e->getMessage());
                $error_message_checkout = "Wystąpił błąd podczas składania zamówienia. Spróbuj ponownie.";
            }
        }
    }
}


include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>
<style>
    .checkout-summary-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
    .checkout-summary-table td, .checkout-summary-table th { padding: 8px; border: 1px solid #eee; text-align: left;}
    .checkout-form .form-group { margin-bottom: 15px; }
    .checkout-form label { font-weight: bold; display: block; margin-bottom: 5px; }
    .checkout-form input[type="text"], .checkout-form input[type="email"], .checkout-form textarea {
        width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;
    }
    .payment-methods label { margin-right: 15px; }
</style>

<?php include BASE_PATH . '/includes/navigation.php'; ?>

<div class="page-wrapper" style="max-width: 900px; margin: 2em auto;">
    <section class="section-title-container">
        <h2><i class="fas fa-credit-card"></i> Finalizacja Zamówienia</h2>
    </section>

    <?php if ($error_message_checkout): ?>
        <div class="flash-message error" style="padding:10px;margin-bottom:15px;border-left:5px solid red;background-color:#fdd;"><?php echo e($error_message_checkout); ?></div>
    <?php endif; ?>

    <div class="order-summary-details" style="margin-bottom: 30px;">
        <h4>Podsumowanie Twojego Zamówienia:</h4>
        <?php if (!empty($cart_items_checkout)): ?>
            <table class="checkout-summary-table">
                <thead><tr><th>Produkt/Usługa</th><th>Ilość</th><th>Cena</th><th>Suma</th></tr></thead>
                <tbody>
                <?php foreach ($cart_items_checkout as $item): ?>
                    <tr>
                        <td>
                            <?php echo e($item['product_name']); ?>
                            <?php if (!empty($item['item_details_parsed'])): ?>
                                <ul style="font-size:0.8em; color:#555; padding-left:15px; margin-top:3px;">
                                <?php foreach ($item['item_details_parsed'] as $key => $value): ?>
                                    <li><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>: <?php echo e($value); ?></li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($item['quantity']); ?></td>
                        <td><?php echo number_format($item['price_at_addition'], 2, ',', ' '); ?> PLN</td>
                        <td><?php echo number_format($item['total_price'], 2, ',', ' '); ?> PLN</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Suma całkowita:</strong></td>
                        <td><strong><?php echo number_format($grand_total_checkout, 2, ',', ' '); ?> PLN</strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>Twój koszyk jest pusty.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($cart_items_checkout)): ?>
    <form action="checkout.php" method="POST" id="paymentRedirectForm" class="checkout-form">
        <?php if (!isset($_SESSION['user_id'])): // Formularz dla gościa ?>
            <h4>Dane Zamawiającego (Gość)</h4>
            <div class="form-group">
                <label for="guest_name"><i class="fas fa-user"></i> Imię i Nazwisko:</label>
                <input type="text" id="guest_name" name="guest_name" required>
            </div>
            <div class="form-group">
                <label for="guest_email"><i class="fas fa-envelope"></i> Adres Email:</label>
                <input type="email" id="guest_email" name="guest_email" required>
            </div>
        <?php else: ?>
            <p>Składasz zamówienie jako zalogowany użytkownik: <strong><?php echo e($_SESSION['user_first_name']); ?></strong>.</p>
        <?php endif; ?>

        <div class="form-group">
            <label for="order_notes"><i class="fas fa-sticky-note"></i> Dodatkowe uwagi do zamówienia (opcjonalnie):</label>
            <textarea id="order_notes" name="order_notes" rows="3" placeholder="Np. preferencje dotyczące rezerwacji, prośby specjalne..."></textarea>
        </div>

        <div class="form-group">
            <label for="promo_code"><i class="fas fa-tag"></i> Kod rabatowy (opcjonalnie):</label>
            <input type="text" id="promo_code" name="promo_code" placeholder="Wpisz kod rabatowy">
        </div>

        <h4>Metoda Płatności</h4>
        <div class="form-group payment-methods">
            <label><input type="radio" name="payment_method" value="online_transfer" checked> Szybki Przelew Online</label>
            <label><input type="radio" name="payment_method" value="blik"> BLIK</label>
            <label><input type="radio" name="payment_method" value="card"> Karta Płatnicza</label>
            </div>

        <p class="form-info-text" style="margin-bottom:15px;">Klikając "Złóż zamówienie i zapłać", akceptujesz <a href="regulamin.php" target="_blank">regulamin</a> i zostaniesz przekierowany do płatności.</p>
        
        <button type="submit" name="place_order" class="cta-button form-submit-button" style="width:100%;">
            <i class="fas fa-credit-card"></i> Złóż zamówienie i zapłać
        </button>
    </form>
    <?php endif; ?>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>