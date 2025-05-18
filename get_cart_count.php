<?php
// get_cart_count.php
require_once __DIR__ . '/config/init.php'; // Dla $pdo i sesji

$count = 0;
$cart_id = get_current_cart_id($pdo); // Użyj funkcji pomocniczej

if ($cart_id) {
    $stmt_count = $pdo->prepare("SELECT SUM(quantity) as total_items FROM CartItems WHERE cart_id = :cart_id");
    $stmt_count->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $result = $stmt_count->fetch();
    if ($result && $result['total_items']) {
        $count = (int)$result['total_items'];
    }
}

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
exit;
?>