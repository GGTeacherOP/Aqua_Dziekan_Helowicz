<?php
// order_confirmation.php
require_once __DIR__ . '/config/init.php'; // Ładuje $pdo, session_start(), funkcje

$page_title = "Potwierdzenie Zamówienia - AquaParadise";
$order_id_from_url = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
$order_details = null;
$order_items_confirmation = [];

if ($order_id_from_url) {
    try {
        // Pobierz główne informacje o zamówieniu
        $stmt_order = $pdo->prepare(
            "SELECT o.order_id, o.total_amount, o.order_status, o.payment_method, o.created_at,
                    u.email as user_email, u.first_name as user_first_name,
                    o.guest_email, o.guest_name
             FROM Orders o
             LEFT JOIN Users u ON o.user_id = u.user_id
             WHERE o.order_id = :order_id"
        );
        $stmt_order->bindParam(':order_id', $order_id_from_url, PDO::PARAM_INT);
        $stmt_order->execute();
        $order_details = $stmt_order->fetch();

        if ($order_details) {
            // Pobierz pozycje zamówienia
            $stmt_items = $pdo->prepare(
                "SELECT oi.quantity, oi.price_per_item, oi.item_details, p.name as product_name
                 FROM OrderItems oi
                 JOIN Products p ON oi.product_id = p.product_id
                 WHERE oi.order_id = :order_id"
            );
            $stmt_items->bindParam(':order_id', $order_id_from_url, PDO::PARAM_INT);
            $stmt_items->execute();
            $order_items_confirmation = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("Błąd pobierania potwierdzenia zamówienia: " . $e->getMessage());
        // Można ustawić komunikat błędu, ale strona potwierdzenia powinna być prosta
    }
}

// Komunikat sukcesu z sesji (jeśli ustawiony w checkout.php)
$success_message_confirmation = $_SESSION['success_message'] ?? "Twoje zamówienie zostało pomyślnie złożone!";
if (isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']); // Wyświetl tylko raz
}


include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>
<style>
    .confirmation-container {
        padding: 30px;
        background-color: #f9f9f9;
        border-radius: 8px;
        margin-top: 20px;
        text-align: center;
    }
    .confirmation-container h3 {
        color: var(--primary-color);
        font-size: 1.8em;
        margin-bottom: 15px;
    }
    .confirmation-container p {
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    .order-summary-confirmation {
        margin-top: 30px;
        text-align: left;
        border: 1px solid var(--border-color);
        padding: 20px;
        border-radius: 8px;
        background-color: #fff;
    }
    .order-summary-confirmation h4 {
        margin-top: 0;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .order-summary-confirmation ul {
        list-style: none;
        padding-left: 0;
    }
    .order-summary-confirmation li {
        padding: 8px 0;
        border-bottom: 1px dashed #eee;
    }
    .order-summary-confirmation li:last-child {
        border-bottom: none;
    }
    .order-summary-confirmation .item-details-conf {
        font-size: 0.85em;
        color: #555;
        display: block;
        margin-left: 15px;
    }
</style>

<?php include BASE_PATH . '/includes/navigation.php'; ?>

<div class="page-wrapper" style="max-width: 800px; margin: 2em auto;">
    <section class="section-title-container">
        <h2><i class="fas fa-check-circle" style="color: green;"></i> Potwierdzenie Zamówienia</h2>
    </section>

    <div class="confirmation-container">
        <?php if ($order_details): ?>
            <h3><?php echo e($success_message_confirmation); ?></h3>
            <p>Dziękujemy za złożenie zamówienia w AquaParadise!</p>
            <p>Numer Twojego zamówienia to: <strong>#<?php echo e($order_details['order_id']); ?></strong></p>
            <p>Status zamówienia: <strong><?php echo e($order_details['order_status']); ?></strong></p>
            <p>Wybrana metoda płatności: <strong><?php echo e(ucfirst(str_replace('_', ' ', $order_details['payment_method']))); ?></strong></p>
            <?php
            $customer_name_conf = $order_details['user_first_name'] ?? $order_details['guest_name'];
            $customer_email_conf = $order_details['user_email'] ?? $order_details['guest_email'];
            if ($customer_email_conf) {
                echo "<p>Potwierdzenie zostało również wysłane na adres email: <strong>" . e($customer_email_conf) . "</strong> (funkcjonalność wysyłki maili do zaimplementowania).</p>";
            }
            ?>

            <div class="order-summary-confirmation">
                <h4>Szczegóły zamówienia:</h4>
                <ul>
                    <?php foreach ($order_items_confirmation as $item_conf): ?>
                        <li>
                            <strong><?php echo e($item_conf['product_name']); ?></strong> (<?php echo e($item_conf['quantity']); ?> x <?php echo number_format($item_conf['price_per_item'], 2, ',', ' '); ?> PLN)
                            = <?php echo number_format($item_conf['quantity'] * $item_conf['price_per_item'], 2, ',', ' '); ?> PLN
                            <?php
                            $details_conf = $item_conf['item_details'] ? json_decode($item_conf['item_details'], true) : [];
                            if (!empty($details_conf)):
                            ?>
                                <span class="item-details-conf">
                                <?php foreach ($details_conf as $key_conf => $value_conf): ?>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $key_conf))); ?>: <?php echo e($value_conf); ?><br>
                                <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p style="text-align: right; font-size: 1.2em; margin-top: 15px;">
                    <strong>Suma całkowita: <?php echo number_format($order_details['total_amount'], 2, ',', ' '); ?> PLN</strong>
                </p>
            </div>

            <p style="margin-top: 25px;">
                <a href="index.php" class="cta-button"><i class="fas fa-home"></i> Wróć na stronę główną</a>
                <?php if (isset($_SESSION['user_id'])): // Jeśli użytkownik jest zalogowany, może przejść do historii zamówień ?>
                    <a href="account/orders.php" class="cta-button secondary-cta" style="margin-left:10px;"><i class="fas fa-history"></i> Moje Zamówienia</a>
                    <?php // Będziesz musiał stworzyć folder 'account' i plik 'orders.php' ?>
                <?php endif; ?>
            </p>

        <?php else: ?>
            <h3><i class="fas fa-exclamation-triangle" style="color: orange;"></i> Nie znaleziono zamówienia</h3>
            <p>Nie mogliśmy znaleźć informacji o podanym zamówieniu. Skontaktuj się z obsługą, jeśli uważasz, że to błąd.</p>
            <p style="margin-top: 25px;">
                <a href="index.php" class="cta-button"><i class="fas fa-home"></i> Wróć na stronę główną</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>