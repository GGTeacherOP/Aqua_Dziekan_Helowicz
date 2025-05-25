<?php
// process_payment.php
require_once __DIR__ . '/config/init.php';

if (!headers_sent()) {
    header('Content-Type: application/json');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie.']);
    exit;
}

$current_cart_id = null;
if (isset($pdo) && function_exists('get_current_cart_id')) {
    $current_cart_id = get_current_cart_id($pdo);
} else {
    error_log("process_payment.php: PDO lub get_current_cart_id nie jest dostępne.");
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Błąd konfiguracji serwera.']); }
    exit;
}

if (!$current_cart_id) {
    echo json_encode(['success' => false, 'message' => 'Nie znaleziono aktywnego koszyka.']);
    exit;
}

// Pobierz pozycje koszyka
$stmt_cart_items = $pdo->prepare(
    "SELECT ci.product_id, ci.quantity, ci.price_at_addition, ci.item_details, p.name as product_name
     FROM CartItems ci
     JOIN Products p ON ci.product_id = p.product_id
     WHERE ci.cart_id = :cart_id"
);
$stmt_cart_items->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
$stmt_cart_items->execute();
$cart_items = $stmt_cart_items->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Twój koszyk jest pusty. Nie można złożyć zamówienia.']);
    exit;
}

// Oblicz sumę zamówienia
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price_at_addition'] * $item['quantity'];
}

$payment_method = trim($_POST['payment_method'] ?? $_POST['payment_method_choice'] ?? 'Nieznana'); // payment_method_choice z modala

$user_id = $_SESSION['user_id'] ?? null;

// Inicjalizacja zmiennych dla danych billingowych/gościa
$db_guest_name = null;
$db_guest_email = null;
$db_billing_name = null;
$db_billing_email = null;
$db_billing_address_street = null;
$db_billing_address_city = null;
$db_billing_address_postal_code = null;
$db_billing_address_country = null;
$db_notes = $_POST['notes'] ?? null; // Jeśli masz pole notes w formularzu modala

if ($user_id) {
    // Zalogowany użytkownik - pobierz jego dane jako domyślne billingowe
    $stmt_user = $pdo->prepare("SELECT first_name, last_name, email FROM Users WHERE user_id = :user_id");
    $stmt_user->execute([':user_id' => $user_id]);
    $user_data = $stmt_user->fetch();
    if ($user_data) {
        $db_billing_name = trim(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? ''));
        $db_billing_email = $user_data['email'];
        // Jeśli masz zapisane adresy dla zalogowanych użytkowników, możesz je tu pobrać.
        // Na razie zakładamy, że dla Blik/Karta nie są one wymagane do zapisu w Orders.
    } else {
        // To nie powinno się zdarzyć, jeśli user_id jest w sesji
        error_log("process_payment.php: Błąd krytyczny - user_id w sesji, ale brak użytkownika w bazie.");
        if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Błąd danych użytkownika.']);}
        exit;
    }
} else {
    // Gość - dane billingowe są pobierane z formularza, jeśli metoda płatności tego wymaga (np. Przelew)
    // Dla BLIK i Karta, dane adresowe mogą nie być wymagane bezpośrednio dla płatności,
    // ale mogą być potrzebne do zamówienia.
    // W modalu payment_modal.php sekcja billingDetailsSection jest pokazywana dla gości przy przelewie.
    // Jeśli dla BLIK/Karta też chcesz zbierać te dane od gościa, musisz dostosować logikę pokazywania tej sekcji w script.js
    // oraz upewnić się, że pola mają odpowiednie name="" w HTML.
    // Obecnie Twój script.js pokazuje billingDetailsSection tylko dla gości płacących przelewem.
    // Załóżmy, że dla BLIK/Karta gość nie podaje pełnych danych adresowych w tym kroku,
    // chyba że je dodasz do modala i script.js.

    if ($payment_method === 'Przelew') { // Jeśli jednak opcja Przelew zostanie dodana i wybrana
        $db_billing_name = trim($_POST['billing_name'] ?? '');
        $db_billing_email = trim($_POST['billing_email'] ?? '');
        $db_billing_address_street = trim($_POST['billing_address_street'] ?? '');
        $db_billing_address_city = trim($_POST['billing_address_city'] ?? '');
        $db_billing_address_postal_code = trim($_POST['billing_address_postal_code'] ?? '');
        $db_billing_address_country = trim($_POST['billing_address_country'] ?? 'Polska');

        if (empty($db_billing_name) || empty($db_billing_email) || empty($db_billing_address_street) || empty($db_billing_address_city) || empty($db_billing_address_postal_code) || empty($db_billing_address_country)) {
            if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Dla płatności przelewem jako gość, proszę wypełnić wszystkie pola danych do zamówienia.']);}
            exit;
        }
        if (!filter_var($db_billing_email, FILTER_VALIDATE_EMAIL)) {
            if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Proszę podać poprawny adres email.']);}
            exit;
        }
        // Walidacja kodu pocztowego (opcjonalnie, script.js już to robi)
        // if (!preg_match('/^[0-9]{2}-[0-9]{3}$/', $db_billing_address_postal_code)) { ... }

        $db_guest_name = $db_billing_name;   // Używamy billing_name jako guest_name
        $db_guest_email = $db_billing_email; // Używamy billing_email jako guest_email
    } else {
        // Dla BLIK/Karta gościa, jeśli nie zbierasz pełnych danych adresowych w modalu:
        // Możesz spróbować pobrać podstawowe dane, jeśli są, lub ustawić wartości domyślne.
        // Załóżmy, że modal NIE zbiera pełnych danych adresowych dla gościa przy BLIK/Karta.
        // Jeśli chcesz je zbierać, dodaj odpowiednie pola do modala i do tej sekcji.
        $db_guest_name = $_SESSION['guest_checkout_name'] ?? $_POST['guest_name_from_previous_step'] ?? "Gość"; // Przykładowo
        $db_guest_email = $_SESSION['guest_checkout_email'] ?? $_POST['guest_email_from_previous_step'] ?? null; // Przykładowo

        // W tym uproszczonym scenariuszu, dane billingowe mogą pozostać null dla BLIK/Karta gościa
        // lub możesz ustawić $db_billing_name i $db_billing_email na to samo co $db_guest_name i $db_guest_email
        $db_billing_name = $db_guest_name;
        $db_billing_email = $db_guest_email;

    }
}

// Symulacja przetwarzania płatności BLIK/Karta
$payment_transaction_id = null;
if ($payment_method === 'Karta' || $payment_method === 'Blik') {
    // Tu normalnie byłaby integracja z bramką płatniczą
    // Dla symulacji, uznajemy płatność za udaną
    $payment_status = 'Zakończona'; // Lub 'Opłacone'
    $order_status = 'Zrealizowane'; // Lub 'W trakcie realizacji', 'Nowe'
    $payment_transaction_id = 'SIMULATED_' . strtoupper($payment_method) . '_' . time(); // Przykładowy ID transakcji
} elseif ($payment_method === 'Przelew') {
    $payment_status = 'Oczekuje na wpłatę';
    $order_status = 'Oczekuje na płatność';
} else {
    $payment_status = 'Nieznana';
    $order_status = 'Błąd płatności';
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Nieznana lub nieobsługiwana metoda płatności.']);}
    exit;
}


try {
    $pdo->beginTransaction();

    // Dodaj zamówienie do tabeli Orders
    // Upewnij się, że tabela Orders ma kolumny: billing_name, billing_email, billing_address_street, itd.
    // Jeśli nie, usuń te pola z zapytania lub dodaj je do tabeli.
    $stmt_order = $pdo->prepare(
        "INSERT INTO Orders (user_id, guest_email, guest_name, 
                             billing_name, billing_email, billing_address_street, billing_address_city, 
                             billing_address_postal_code, billing_address_country,
                             total_amount, order_status, payment_method, payment_status, payment_transaction_id, notes, created_at, updated_at)
         VALUES (:user_id, :guest_email, :guest_name, 
                 :billing_name, :billing_email, :billing_address_street, :billing_address_city, 
                 :billing_address_postal_code, :billing_address_country,
                 :total_amount, :order_status, :payment_method, :payment_status, :payment_transaction_id, :notes, NOW(), NOW())"
    );

    $stmt_order->bindParam(':user_id', $user_id, $user_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
    $stmt_order->bindParam(':guest_email', $db_guest_email, $db_guest_email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_order->bindParam(':guest_name', $db_guest_name, $db_guest_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    // Dane billingowe
    $stmt_order->bindParam(':billing_name', $db_billing_name, $db_billing_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR); 
    $stmt_order->bindParam(':billing_email', $db_billing_email, $db_billing_email === null ? PDO::PARAM_NULL : PDO::PARAM_STR); 
    $stmt_order->bindParam(':billing_address_street', $db_billing_address_street, $db_billing_address_street === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_order->bindParam(':billing_address_city', $db_billing_address_city, $db_billing_address_city === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_order->bindParam(':billing_address_postal_code', $db_billing_address_postal_code, $db_billing_address_postal_code === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_order->bindParam(':billing_address_country', $db_billing_address_country, $db_billing_address_country === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    $stmt_order->bindParam(':total_amount', $total_amount);
    $stmt_order->bindParam(':order_status', $order_status, PDO::PARAM_STR);
    $stmt_order->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
    $stmt_order->bindParam(':payment_status', $payment_status, PDO::PARAM_STR);
    $stmt_order->bindParam(':payment_transaction_id', $payment_transaction_id, $payment_transaction_id === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt_order->bindParam(':notes', $db_notes, $db_notes === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    $stmt_order->execute();
    $order_id = $pdo->lastInsertId();

    // Dodaj pozycje zamówienia do OrderItems
    $stmt_order_item = $pdo->prepare(
        "INSERT INTO OrderItems (order_id, product_id, quantity, price_per_item, item_details)
         VALUES (:order_id, :product_id, :quantity, :price_per_item, :item_details)"
    );
    foreach ($cart_items as $item) {
        $stmt_order_item->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price_per_item' => $item['price_at_addition'],
            ':item_details' => ($item['item_details'] === null ? PDO::PARAM_NULL : $item['item_details'])
        ]);
    }

    // Wyczyść koszyk (CartItems)
    $stmt_clear_cart_items = $pdo->prepare("DELETE FROM CartItems WHERE cart_id = :cart_id");
    $stmt_clear_cart_items->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
    $stmt_clear_cart_items->execute();

    // Opcjonalnie: Usuń sam koszyk z tabeli Carts, jeśli nie jest już potrzebny
    // $stmt_clear_cart = $pdo->prepare("DELETE FROM Carts WHERE cart_id = :cart_id");
    // $stmt_clear_cart->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
    // $stmt_clear_cart->execute();
    // if (isset($_SESSION['guest_cart_id'])) unset($_SESSION['guest_cart_id']);


    $pdo->commit();

    // Ustaw komunikat flash dla strony potwierdzenia (jeśli używasz)
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Zamówienie nr #' . $order_id . ' zostało pomyślnie złożone i opłacone.'];
    
    if (!headers_sent()) {
        echo json_encode(['success' => true, 'order_id' => $order_id, 'payment_method' => $payment_method]);
    }
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("Błąd PDO przy przetwarzaniu zamówienia: " . $e->getMessage() . " | Kod: " . $e->getCode() . " | POST: " . json_encode($_POST) . " | CartID: " . $current_cart_id);
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Wystąpił błąd serwera podczas przetwarzania zamówienia. (DB_ERR)']);}
    exit;
} catch (Exception $e) { // Ogólny błąd
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("Ogólny błąd przy przetwarzaniu zamówienia: " . $e->getMessage());
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Wystąpił nieoczekiwany błąd. Spróbuj ponownie. (GEN_ERR)']);}
    exit;
}
?>