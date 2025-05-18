<?php
// cart_actions.php
require_once __DIR__ . '/config/init.php'; // Ładuje $pdo, session_start(), funkcje

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') { // Akceptujemy GET dla prostego dodawania po przekierowaniu
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowe żądanie.'];
    header('Location: index.php');
    exit;
}

$action = $_REQUEST['action'] ?? null; // Używamy $_REQUEST, aby obsłużyć GET i POST
$product_id = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : null;
$quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

// item_details może przyjść jako tablica z POST lub jako string JSON z GET
$item_details_array = [];
if (isset($_POST['item_details']) && is_array($_POST['item_details'])) {
    $item_details_array = $_POST['item_details'];
} elseif (isset($_REQUEST['item_details_json_string'])) { // Dla przekierowań GET
    $decoded_details = json_decode($_REQUEST['item_details_json_string'], true);
    if (is_array($decoded_details)) {
        $item_details_array = $decoded_details;
    }
}
// Sortowanie kluczy w item_details, aby zapewnić spójność JSON dla porównań
if (!empty($item_details_array)) {
    ksort($item_details_array);
}
$item_details_json = !empty($item_details_array) ? json_encode($item_details_array) : null;


$price_at_addition = 0;
if ($product_id) {
    $product_stmt = $pdo->prepare("SELECT price, name FROM Products WHERE product_id = :product_id");
    $product_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $product_stmt->execute();
    $product_data = $product_stmt->fetch();
    if ($product_data) {
        $price_at_addition = $product_data['price'];
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Produkt nie został znaleziony.'];
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }
}

$current_cart_id = get_current_cart_id($pdo); // Użyj funkcji pomocniczej

if (!$current_cart_id && $action === 'add_to_cart') { // Jeśli get_current_cart_id zwrócił null, ale próbujemy dodać
    // Ta sytuacja nie powinna wystąpić, bo get_current_cart_id tworzy koszyk, jeśli go nie ma
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nie udało się zainicjować koszyka. Spróbuj ponownie.'];
     header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}


try {
    if ($action === 'add_to_cart' && $product_id && $quantity > 0) {
        $stmt_check = $pdo->prepare(
            "SELECT cart_item_id, quantity FROM CartItems
             WHERE cart_id = :cart_id AND product_id = :product_id
             AND (item_details = :item_details OR (item_details IS NULL AND :item_details_is_null))"
        );
        $stmt_check->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':item_details', $item_details_json, PDO::PARAM_STR);
        $is_null = ($item_details_json === null);
        $stmt_check->bindParam(':item_details_is_null', $is_null, PDO::PARAM_BOOL); // To może nie działać zgodnie z oczekiwaniami we wszystkich SQL
        // Lepsze podejście do sprawdzania NULL:
        // $sql_check = "SELECT cart_item_id, quantity FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id";
        // if ($item_details_json === null) {
        //    $sql_check .= " AND item_details IS NULL";
        // } else {
        //    $sql_check .= " AND item_details = :item_details";
        // }
        // $stmt_check = $pdo->prepare($sql_check);
        // ... bindowanie ...

        $stmt_check->execute();
        $existing_item = $stmt_check->fetch();

        // Dla rezerwacji (gdzie item_details nie jest null i jest unikalny dla rezerwacji np. daty)
        // zawsze dodajemy nowy wpis. Dla produktów (item_details jest null), aktualizujemy ilość.
        if ($existing_item && $item_details_json === null) {
            $new_quantity = $existing_item['quantity'] + $quantity;
            $stmt_update = $pdo->prepare("UPDATE CartItems SET quantity = :quantity WHERE cart_item_id = :cart_item_id");
            $stmt_update->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $stmt_update->bindParam(':cart_item_id', $existing_item['cart_item_id'], PDO::PARAM_INT);
            $stmt_update->execute();
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => e($product_data['name']) . ' - ilość zaktualizowana w koszyku!'];
        } else {
            $stmt_insert = $pdo->prepare(
                "INSERT INTO CartItems (cart_id, product_id, quantity, price_at_addition, item_details)
                 VALUES (:cart_id, :product_id, :quantity, :price_at_addition, :item_details)"
            );
            $stmt_insert->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt_insert->bindParam(':price_at_addition', $price_at_addition);
            $stmt_insert->bindParam(':item_details', $item_details_json, PDO::PARAM_STR);
            $stmt_insert->execute();
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => e($product_data['name']) . ' dodany do koszyka!'];
        }

    } elseif ($action === 'remove_from_cart' && isset($_POST['cart_item_id'])) {
        $cart_item_id_to_remove = (int)$_POST['cart_item_id'];
        if ($current_cart_id) {
            $stmt_delete = $pdo->prepare("DELETE FROM CartItems WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
            $stmt_delete->bindParam(':cart_item_id', $cart_item_id_to_remove, PDO::PARAM_INT);
            $stmt_delete->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
            $stmt_delete->execute();
            if ($stmt_delete->rowCount() > 0) {
                 $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Produkt usunięty z koszyka.'];
            } else {
                 $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'Nie można usunąć produktu.'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Brak aktywnego koszyka.'];
        }

    } elseif ($action === 'update_quantity' && isset($_POST['cart_item_id'])) {
        $cart_item_id_to_update = (int)$_POST['cart_item_id'];
        if ($quantity <= 0) { // Jeśli ilość jest 0 lub mniej, usuwamy przedmiot
             if ($current_cart_id) {
                $stmt_delete = $pdo->prepare("DELETE FROM CartItems WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
                $stmt_delete->bindParam(':cart_item_id', $cart_item_id_to_update, PDO::PARAM_INT);
                $stmt_delete->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
                $stmt_delete->execute();
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Produkt usunięty z koszyka (ilość <= 0).'];
             }
        } elseif ($current_cart_id) {
            $stmt_update_qty = $pdo->prepare("UPDATE CartItems SET quantity = :quantity WHERE cart_item_id = :cart_item_id AND cart_id = :cart_id");
            $stmt_update_qty->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt_update_qty->bindParam(':cart_item_id', $cart_item_id_to_update, PDO::PARAM_INT);
            $stmt_update_qty->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
            $stmt_update_qty->execute();
            if ($stmt_update_qty->rowCount() > 0) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Ilość zaktualizowana.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'warning', 'text' => 'Nie można zaktualizować ilości.'];
            }
        } else {
             $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Brak aktywnego koszyka.'];
        }
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieznana akcja lub brak wymaganych danych.'];
    }

} catch (PDOException $e) {
    error_log("Błąd operacji na koszyku: " . $e->getMessage() . " | Dane: product_id=$product_id, action=$action, item_details=$item_details_json");
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd serwera podczas operacji na koszyku. Spróbuj ponownie.'];
}

// Przekierowanie
$referer = $_SERVER['HTTP_REFERER'] ?? 'cart_view.php';
// Unikaj przekierowania z powrotem do samego cart_actions.php
if (strpos($referer, 'cart_actions.php') !== false) {
    $referer = 'cart_view.php';
}

if ($action === 'add_to_cart') {
    header('Location: ' . $referer);
} else {
    header('Location: cart_view.php');
}
exit;
?>