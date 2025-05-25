<?php
// cart_actions.php
require_once __DIR__ . '/config/init.php'; // Dołączenie konfiguracji i funkcji

// Upewnij się, że $pdo jest dostępne
if (!isset($pdo)) {
    error_log("cart_actions.php: Krytyczny błąd - obiekt PDO nie jest dostępny. Sprawdź init.php.");
    if (session_status() == PHP_SESSION_NONE) session_start(); 
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił krytyczny błąd systemu. Spróbuj ponownie później.'];
    header("Location: " . (defined('BASE_URL') ? BASE_URL : './') . "index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $current_cart_id = get_current_cart_id($pdo);

    if (!$current_cart_id && in_array($action, ['add_to_cart', 'update_quantity', 'remove_from_cart'])) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nie można zidentyfikować Twojego koszyka. Spróbuj ponownie.'];
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php"));
        exit();
    }

    if ($action === 'add_to_cart') {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $item_details_array = $_POST['item_details'] ?? []; 

        if (!$product_id || $product_id <= 0) {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowy produkt.'];
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php"));
            exit();
        }
        if (!$quantity || $quantity <= 0) {
            $quantity = 1; 
        }

        $product_price = null;
        $product_name_for_message = "Produkt";
        try {
            $stmt_product = $pdo->prepare("SELECT name, price FROM Products WHERE product_id = :product_id AND is_active = TRUE");
            $stmt_product->execute([':product_id' => $product_id]);
            $product_data = $stmt_product->fetch();

            if ($product_data) {
                $product_price = (float)$product_data['price'];
                $product_name_for_message = $product_data['name'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wybrany produkt jest niedostępny lub nie istnieje.'];
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php"));
                exit();
            }
        } catch (PDOException $e) {
            error_log("cart_actions.php (add_to_cart) - Błąd pobierania danych produktu: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas pobierania informacji o produkcie.'];
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php"));
            exit();
        }
        
        $price_at_addition = $product_price;
        if (isset($item_details_array['reservation_type']) && $item_details_array['reservation_type'] === 'spa_booking' && isset($item_details_array['total_price_for_selected_spa'])) {
            $calculated_spa_price = filter_var($item_details_array['total_price_for_selected_spa'], FILTER_VALIDATE_FLOAT);
            if ($calculated_spa_price !== false && $calculated_spa_price > 0) {
                 $price_at_addition = $calculated_spa_price;
            } else if (isset($item_details_array['placeholder_spa_product_id']) && $product_id == $item_details_array['placeholder_spa_product_id'] && $product_price == 0) {
                $price_at_addition = 0.00;
            }
        }

        $item_details_json = !empty($item_details_array) ? json_encode($item_details_array, JSON_UNESCAPED_UNICODE) : null;
        if ($item_details_json === false) { 
            error_log("cart_actions.php (add_to_cart) - Błąd kodowania item_details do JSON: " . json_last_error_msg());
            $item_details_json = null; 
        }

        try {
            $stmt_add = $pdo->prepare(
                "INSERT INTO CartItems (cart_id, product_id, quantity, price_at_addition, item_details, added_at) 
                 VALUES (:cart_id, :product_id, :quantity, :price_at_addition, :item_details, NOW())"
            );
            $stmt_add->execute([
                ':cart_id' => $current_cart_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity,
                ':price_at_addition' => $price_at_addition,
                ':item_details' => $item_details_json
            ]);

            $message_text = htmlspecialchars($product_name_for_message, ENT_QUOTES, 'UTF-8') . " został pomyślnie dodany do koszyka!";
            if (isset($item_details_array['reservation_type'])) {
                 if ($item_details_array['reservation_type'] === 'restaurant_table') {
                    $message_text = "Rezerwacja stolika została pomyślnie dodana do koszyka!";
                 } elseif ($item_details_array['reservation_type'] === 'spa_booking') {
                    $message_text = "Rezerwacja SPA została pomyślnie dodana do koszyka!";
                 } elseif ($item_details_array['reservation_type'] === 'hotel_room') {
                    $message_text = "Rezerwacja pokoju hotelowego została pomyślnie dodana do koszyka!";
                 }
            }
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => $message_text];
            header("Location: " . BASE_URL . "cart_view.php");
            exit();

        } catch (PDOException $e) {
            error_log("cart_actions.php (add_to_cart) - Błąd dodawania do CartItems: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas dodawania produktu do koszyka. Spróbuj ponownie. (' . $e->getCode() . ')'];
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php"));
            exit();
        }

    } elseif ($action === 'update_quantity') {
        $cart_item_id = filter_input(INPUT_POST, 'cart_item_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$cart_item_id || $quantity === false || $quantity < 0) { // ilość 0 oznacza usunięcie
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowe dane do aktualizacji ilości.'];
            header("Location: " . BASE_URL . "cart_view.php");
            exit();
        }
        
        try {
            if ($quantity == 0) {
                // Jeśli ilość to 0, usuń przedmiot
                $stmt_delete = $pdo->prepare("DELETE FROM CartItems WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
                $stmt_delete->execute([':cart_item_id' => $cart_item_id, ':cart_id' => $current_cart_id]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Produkt został usunięty z koszyka.'];
            } else {
                // W przeciwnym razie zaktualizuj ilość
                $stmt_update = $pdo->prepare("UPDATE CartItems SET quantity = :quantity WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
                $stmt_update->execute([':quantity' => $quantity, ':cart_item_id' => $cart_item_id, ':cart_id' => $current_cart_id]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Ilość produktu została zaktualizowana.'];
            }
        } catch (PDOException $e) {
            error_log("cart_actions.php (update_quantity) - Błąd: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas aktualizacji koszyka.'];
        }
        header("Location: " . BASE_URL . "cart_view.php");
        exit();


    } elseif ($action === 'remove_from_cart') {
        $cart_item_id = filter_input(INPUT_POST, 'cart_item_id', FILTER_VALIDATE_INT);

        if (!$cart_item_id) {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowy identyfikator produktu w koszyku.'];
            header("Location: " . BASE_URL . "cart_view.php");
            exit();
        }

        try {
            // Sprawdź, czy przedmiot należy do aktualnego koszyka (bezpieczeństwo)
            $stmt_check = $pdo->prepare("SELECT product_id FROM CartItems WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
            $stmt_check->execute([':cart_item_id' => $cart_item_id, ':cart_id' => $current_cart_id]);
            
            if ($stmt_check->fetch()) {
                $stmt_delete = $pdo->prepare("DELETE FROM CartItems WHERE cart_item_id = :cart_item_id");
                $stmt_delete->execute([':cart_item_id' => $cart_item_id]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Produkt został usunięty z koszyka.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nie znaleziono produktu w Twoim koszyku lub brak uprawnień do usunięcia.'];
            }
        } catch (PDOException $e) {
            error_log("cart_actions.php (remove_from_cart) - Błąd: " . $e->getMessage());
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas usuwania produktu z koszyka.'];
        }
        header("Location: " . BASE_URL . "cart_view.php");
        exit();

    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieznana akcja lub brak akcji.'];
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
} else {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>