<?php
// cart_view.php
require_once __DIR__ . '/config/init.php';

$page_title = "Twój Koszyk - AquaParadise";

$cart_items_display = [];
$grand_total_display = 0;
$current_cart_id_view = get_current_cart_id($pdo); // Użyj funkcji pomocniczej

if ($current_cart_id_view) {
    $stmt_items = $pdo->prepare(
        "SELECT ci.cart_item_id, ci.quantity, ci.price_at_addition, ci.item_details,
                p.name as product_name, p.product_id, p.image_url, cat.name as category_name
         FROM CartItems ci
         JOIN Products p ON ci.product_id = p.product_id
         JOIN Categories cat ON p.category_id = cat.category_id
         WHERE ci.cart_id = :cart_id
         ORDER BY ci.added_at DESC" // Najnowsze na górze
    );
    $stmt_items->bindParam(':cart_id', $current_cart_id_view, PDO::PARAM_INT);
    $stmt_items->execute();
    $fetched_items_view = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched_items_view as $item) {
        $item_total = $item['quantity'] * $item['price_at_addition'];
        $item_details_parsed = $item['item_details'] ? json_decode($item['item_details'], true) : [];
        // Domyślny obrazek, jeśli brak
        $image_path = (!empty($item['image_url']) && file_exists(BASE_PATH . '/' . $item['image_url'])) ? $item['image_url'] : 'assets/images/placeholder.jpg';


        $cart_items_display[] = [
            'cart_item_id' => $item['cart_item_id'],
            'product_id' => $item['product_id'],
            'product_name' => $item['product_name'],
            'category_name' => $item['category_name'],
            'image_url' => $image_path,
            'quantity' => $item['quantity'],
            'price_at_addition' => $item['price_at_addition'],
            'item_details_json' => $item['item_details'], // Surowy JSON do ewentualnego przekazania dalej
            'item_details_parsed' => $item_details_parsed, // Sparsowane detale do wyświetlenia
            'total_price' => $item_total
        ];
        $grand_total_display += $item_total;
    }
}

include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>
<style>
    /* Dodatkowe style dla strony koszyka, jeśli potrzebne */
    .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .cart-table th, .cart-table td { border: 1px solid var(--border-color); padding: 12px 15px; text-align: left; vertical-align: middle; }
    .cart-table th { background-color: var(--primary-very-light-color); font-weight: 600; }
    .cart-table img.product-image-cart { max-width: 80px; height: auto; border-radius: 4px; margin-right: 10px; }
    .cart-table .product-info { display: flex; align-items: center; }
    .cart-table .item-details-list { font-size: 0.85em; color: var(--text-muted-color); list-style: none; padding-left: 0; margin-top: 5px;}
    .cart-table .item-details-list li { margin-bottom: 3px; }
    .cart-table input[type="number"] { width: 60px; padding: 5px; text-align: center; border-radius: 4px; border: 1px solid var(--border-color); }
    .cart-table .update-btn, .cart-table .remove-btn {
        padding: 6px 10px; font-size: 0.85em; border-radius: 4px; cursor: pointer;
        border: 1px solid transparent;
    }
    .cart-table .update-btn { background-color: var(--primary-light-color); color: var(--primary-dark-color); border-color: var(--primary-light-color); margin-left: 5px;}
    .cart-table .update-btn:hover { background-color: var(--primary-color); color: white; }
    .cart-table .remove-btn { background-color: #dc3545; color: white; border-color: #dc3545; }
    .cart-table .remove-btn:hover { background-color: #c82333; }
    .cart-summary { text-align: right; margin-bottom: 20px; }
    .cart-summary h3 { font-size: 1.5em; color: var(--primary-dark-color); margin-bottom: 20px; }
    .cart-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
    .empty-cart-message { padding: 20px; text-align: center; background-color: var(--primary-very-light-color); border-radius: 8px; margin-bottom: 20px;}
</style>

<?php include BASE_PATH . '/includes/navigation.php'; ?>

<div class="page-wrapper" style="text-align: left; max-width: 1200px; margin: 2em auto;">
    <section class="section-title-container">
        <h2>Twój Koszyk</h2>
    </section>

    <?php if (empty($cart_items_display)): ?>
        <div class="empty-cart-message">
            <p><i class="fas fa-shopping-cart" style="font-size: 2em; margin-bottom: 10px; color: var(--text-muted-color);"></i></p>
            <p>Twój koszyk jest pusty.</p>
            <p><a href="index.php" class="cta-button" style="margin-top:15px;">Wróć do sklepu</a></p>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th colspan="2">Produkt/Usługa</th>
                    <th>Cena jedn.</th>
                    <th>Ilość</th>
                    <th>Suma częściowa</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items_display as $item): ?>
                    <tr>
                        <td style="width: 100px;">
                            <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['product_name']); ?>" class="product-image-cart">
                        </td>
                        <td>
                            <strong><?php echo e($item['product_name']); ?></strong>
                            <?php if (!empty($item['item_details_parsed'])): ?>
                                <ul class="item-details-list">
                                    <?php foreach ($item['item_details_parsed'] as $key => $value): ?>
                                        <li><strong><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:</strong> <?php echo e($value); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format($item['price_at_addition'], 2, ',', ' '); ?> PLN</td>
                        <td>
                            <form action="cart_actions.php" method="POST" style="display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="action" value="update_quantity">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0"> <button type="submit" class="update-btn" title="Aktualizuj ilość"><i class="fas fa-sync-alt"></i></button>
                            </form>
                        </td>
                        <td><strong><?php echo number_format($item['total_price'], 2, ',', ' '); ?> PLN</strong></td>
                        <td>
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="remove_from_cart">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <button type="submit" class="remove-btn" title="Usuń z koszyka"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Suma całkowita: <?php echo number_format($grand_total_display, 2, ',', ' '); ?> PLN</h3>
        </div>
        <div class="cart-actions">
            <a href="index.php" class="cta-button secondary-cta"><i class="fas fa-arrow-left"></i> Kontynuuj zakupy</a>
            <?php if ($grand_total_display > 0): ?>
            <a href="checkout.php" class="cta-button"><i class="fas fa-credit-card"></i> Przejdź do kasy</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>