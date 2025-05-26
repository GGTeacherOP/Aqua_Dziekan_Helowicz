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
    error_log("process_payment.php: Kluczowe funkcje lub obiekt PDO nie są dostępne.");
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Błąd konfiguracji serwera (brak PDO lub funkcji).']); }
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

$payment_method = trim($_POST['payment_method'] ?? 'Nieznana'); // Nazwa z 'payment_method_choice' jest zmieniana na 'payment_method' w JS

$user_id = $_SESSION['user_id'] ?? null;

// Inicjalizacja zmiennych
$db_billing_name = null;
$db_billing_email = null;
$db_billing_address_street = null;
$db_billing_address_city = null;
$db_billing_address_postal_code = null;
$db_billing_address_country = null;
$db_guest_name = null;             // Dla kolumny guest_name w Orders
$db_guest_email = null;            // Dla kolumny guest_email w Orders
$db_notes = $_POST['notes'] ?? null; // Jeśli pole 'notes' jest wysyłane z modala

if ($user_id) {
    // Użytkownik zalogowany
    $stmt_user = $pdo->prepare("SELECT first_name, last_name, email FROM Users WHERE user_id = :user_id");
    $stmt_user->execute([':user_id' => $user_id]);
    $user_data = $stmt_user->fetch();
    if ($user_data) {
        $db_billing_name = trim(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? ''));
        $db_billing_email = $user_data['email'];
        // Pola adresowe billingowe dla zalogowanego użytkownika mogą być puste,
        // jeśli nie masz ich w profilu użytkownika i nie zbierasz ich w modalu dla zalogowanych.
        // Tabela Orders powinna akceptować NULL dla tych kolumn w takim przypadku.
    } else {
        error_log("process_payment.php: Błąd krytyczny - user_id ({$user_id}) w sesji, ale brak użytkownika w bazie.");
        if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Błąd danych zalogowanego użytkownika.']);}
        exit;
    }
} else { 
    // Gość - dane billingowe są teraz zawsze zbierane z formularza w modalu (zgodnie z ostatnią zmianą w script.js)
    $db_billing_name = trim($_POST['billing_name'] ?? '');
    $db_billing_email = trim($_POST['billing_email'] ?? '');
    $db_billing_address_street = trim($_POST['billing_address_street'] ?? '');
    $db_billing_address_city = trim($_POST['billing_address_city'] ?? '');
    $db_billing_address_postal_code = trim($_POST['billing_address_postal_code'] ?? '');
    $db_billing_address_country = trim($_POST['billing_address_country'] ?? 'Polska');

    // Walidacja tych pól dla gości, niezależnie od metody płatności
    if (empty($db_billing_name) || empty($db_billing_email) || empty($db_billing_address_street) || empty($db_billing_address_city) || empty($db_billing_address_postal_code) || empty($db_billing_address_country)) {
        if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Jako gość, proszę wypełnić wszystkie pola danych do zamówienia.']);}
        exit;
    }
    if (!filter_var($db_billing_email, FILTER_VALIDATE_EMAIL)) {
        if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Proszę podać poprawny adres email.']);}
        exit;
    }
    if (!preg_match('/^[0-9]{2}-[0-9]{3}$/', $db_billing_address_postal_code)) {
        if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Kod pocztowy musi być w formacie XX-XXX.']);}
        exit;
    }
    // Używamy tych danych jako guest_name i guest_email dla tabeli Orders,
    // a także jako dane billingowe.
    $db_guest_name = $db_billing_name;
    $db_guest_email = $db_billing_email;
}

// Symulacja przetwarzania płatności
$payment_transaction_id = null;
$order_status = 'W trakcie realizacji'; // Domyślny status
$payment_status = 'Oczekuje';          // Domyślny status

if ($payment_method === 'Karta' || $payment_method === 'Blik') {
    $payment_status = 'Zakończona'; 
    $order_status = 'Zrealizowane'; 
    $payment_transaction_id = 'SIMULATED_' . strtoupper($payment_method) . '_' . time();
} elseif ($payment_method === 'Przelew') {
    $payment_status = 'Oczekuje na wpłatę';
    $order_status = 'Oczekuje na płatność';
} else {
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Nieznana lub nieobsługiwana metoda płatności.']);}
    exit;
}


try {
    $pdo->beginTransaction();

    // Wstaw zamówienie do tabeli Orders
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

    // Wstaw pozycje zamówienia i utwórz rezerwacje
    $stmt_order_item = $pdo->prepare(
        "INSERT INTO OrderItems (order_id, product_id, quantity, price_per_item, item_details)
         VALUES (:order_id, :product_id, :quantity, :price_per_item, :item_details)"
    );
    $stmt_booking = $pdo->prepare(
        "INSERT INTO bookings (order_item_id, product_id, user_id, guest_name, guest_email, resource_type, start_datetime, end_datetime, quantity_booked, booking_status)
         VALUES (:order_item_id, :product_id, :user_id, :guest_name, :guest_email, :resource_type, :start_datetime, :end_datetime, :quantity_booked, :booking_status)"
    );

    foreach ($cart_items as $item) {
        $stmt_order_item->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price_per_item' => $item['price_at_addition'],
            ':item_details' => ($item['item_details'] === null ? null : $item['item_details'])
        ]);
        $order_item_id = $pdo->lastInsertId();

        $details = $item['item_details'] ? json_decode($item['item_details'], true) : [];
        $resource_type = null;
        $start_datetime_str = null;
        $end_datetime_str = null;
        $quantity_booked = $item['quantity']; 

        if (isset($details['reservation_type'])) {
            $booking_start_date = $details['reservation_date'] ?? $details['treatment_date'] ?? $details['check_in_date'] ?? null;
            $booking_start_time_raw = $details['reservation_time'] ?? $details['treatment_time'] ?? null;
            
            // Ustalanie godziny rozpoczęcia
            if ($details['reservation_type'] === 'hotel_room') {
                 $booking_start_time = '14:00:00'; // Domyślna godzina zameldowania dla hotelu
            } elseif ($booking_start_time_raw) {
                 $booking_start_time = date('H:i:s', strtotime($booking_start_time_raw)); // Upewnij się, że format jest poprawny
            } else {
                 $booking_start_time = '00:00:00'; // Fallback, jeśli godzina nie jest podana dla innych typów
            }


            if ($booking_start_date) {
                $start_datetime_str = $booking_start_date . ' ' . $booking_start_time;
            }

            switch ($details['reservation_type']) {
                case 'restaurant_table':
                    $resource_type = 'restaurant_table';
                    if ($start_datetime_str) {
                        $end_datetime_str = date('Y-m-d H:i:s', strtotime($start_datetime_str . ' +2 hours'));
                    }
                    break;
                case 'spa_booking':
                    $resource_type = 'spa_slot';
                    if ($start_datetime_str) {
                        $duration_minutes = 60; // Domyślnie
                        if (isset($details['placeholder_spa_product_id']) && $item['product_id'] == $details['placeholder_spa_product_id'] && isset($details['selected_treatments_ids_string'])) {
                            // Sumowanie czasów trwania indywidualnych zabiegów - bardziej skomplikowane, wymaga dostępu do danych produktów
                            // Na razie użyjemy domyślnego lub pobierzemy z głównego produktu SPA, jeśli to pakiet
                             error_log("SPA Booking: product_id {$item['product_id']} / placeholder {$details['placeholder_spa_product_id']}");
                             // Jeśli to pakiet (nie placeholder), spróbuj odczytać jego duration
                             if ($item['product_id'] != ($details['placeholder_spa_product_id'] ?? -1) ) { // -1 by uniknąć warninga jeśli nie ma placeholder
                                $stmt_prod_info_spa = $pdo->prepare("SELECT availability_details FROM products WHERE product_id = ?");
                                $stmt_prod_info_spa->execute([$item['product_id']]);
                                $prod_avail_json_spa = $stmt_prod_info_spa->fetchColumn();
                                if ($prod_avail_json_spa) {
                                    $avail_arr_spa = json_decode($prod_avail_json_spa, true);
                                    if (isset($avail_arr_spa['duration_minutes'])) {
                                        $duration_minutes = (int)$avail_arr_spa['duration_minutes'];
                                    }
                                }
                             } else {
                                // Dla indywidualnych, można by zsumować czasy z `selected_treatments_ids_string`
                                // Dla uproszczenia, przyjmujemy teraz 90 minut
                                $duration_minutes = 90;
                             }
                        } else { // Pojedynczy zabieg/pakiet
                             $stmt_prod_info_spa = $pdo->prepare("SELECT availability_details FROM products WHERE product_id = ?");
                             $stmt_prod_info_spa->execute([$item['product_id']]);
                             $prod_avail_json_spa = $stmt_prod_info_spa->fetchColumn();
                             if ($prod_avail_json_spa) {
                                 $avail_arr_spa = json_decode($prod_avail_json_spa, true);
                                 if (isset($avail_arr_spa['duration_minutes'])) {
                                     $duration_minutes = (int)$avail_arr_spa['duration_minutes'];
                                 }
                             }
                        }
                        $end_datetime_str = date('Y-m-d H:i:s', strtotime($start_datetime_str . " +{$duration_minutes} minutes"));
                    }
                    break;
                case 'hotel_room':
                    $resource_type = 'hotel_room';
                    $end_date_str = $details['check_out_date'] ?? null;
                    if ($start_datetime_str && $end_date_str) {
                        $end_datetime_str = $end_date_str . ' 12:00:00'; // Domyślna godzina wymeldowania
                    }
                    break;
            }

            if ($resource_type && $start_datetime_str && $end_datetime_str) {
                $current_booking_guest_name = $user_id ? null : ($db_guest_name ?? ($details['booking_name'] ?? null));
                $current_booking_guest_email = $user_id ? null : ($db_guest_email ?? ($details['booking_email'] ?? null));

                $stmt_booking->execute([
                    ':order_item_id' => $order_item_id,
                    ':product_id' => $item['product_id'],
                    ':user_id' => $user_id,
                    ':guest_name' => $current_booking_guest_name,
                    ':guest_email' => $current_booking_guest_email,
                    ':resource_type' => $resource_type,
                    ':start_datetime' => $start_datetime_str,
                    ':end_datetime' => $end_datetime_str,
                    ':quantity_booked' => $quantity_booked,
                    ':booking_status' => ($payment_status === 'Zakończona' || $payment_status === 'Opłacone') ? 'confirmed' : 'pending_payment'
                ]);
            }
        }
    }

    $stmt_clear_cart_items = $pdo->prepare("DELETE FROM CartItems WHERE cart_id = :cart_id");
    $stmt_clear_cart_items->bindParam(':cart_id', $current_cart_id, PDO::PARAM_INT);
    $stmt_clear_cart_items->execute();
    
    $pdo->commit();

    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Zamówienie nr #' . $order_id . ' zostało pomyślnie złożone.'];
    if (!headers_sent()) {
        echo json_encode(['success' => true, 'order_id' => $order_id, 'payment_method' => $payment_method]);
    }
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("Błąd PDO przy przetwarzaniu zamówienia: " . $e->getMessage() . " | Kod: " . $e->getCode() . " | POST: " . json_encode($_POST) . " | CartID: " . $current_cart_id);
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Wystąpił błąd serwera podczas przetwarzania zamówienia. (DB_ERR)']);}
    exit;
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("Ogólny błąd przy przetwarzaniu zamówienia: " . $e->getMessage());
    if (!headers_sent()) { echo json_encode(['success' => false, 'message' => 'Wystąpił nieoczekiwany błąd. Spróbuj ponownie. (GEN_ERR)']);}
    exit;
}
?>