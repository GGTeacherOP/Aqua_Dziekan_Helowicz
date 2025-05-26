<?php
// ajax_get_restaurant_availability.php
require_once __DIR__ . '/config/init.php';

if (!headers_sent()) {
    header('Content-Type: application/json');
}

$response = [
    'unavailable_slots' => [],
    'all_possible_slots' => [],
    'max_capacity_per_slot' => 30, 
    'slot_duration_hours' => 1,
    'debug_info' => []
];

if (!isset($pdo)) {
    $response['error'] = 'Błąd serwera: Brak połączenia z bazą danych.';
    error_log("ajax_get_restaurant_availability.php: PDO object not available.");
    echo json_encode($response);
    exit;
}

$selected_date_str = $_GET['date'] ?? null;
$restaurant_reservation_product_id_get = isset($_GET['product_id']) ? filter_var($_GET['product_id'], FILTER_VALIDATE_INT) : null;

if (!$selected_date_str) {
    $response['error'] = 'Brak wybranej daty.';
    echo json_encode($response);
    exit;
}
// Pobierz ID produktu "Rezerwacja Stolika w Restauracji", jeśli nie przekazano go w GET
if (!$restaurant_reservation_product_id_get) {
    $reservationProductName = 'Rezerwacja Stolika w Restauracji'; // Upewnij się, że produkt istnieje
    $stmtResProdId = $pdo->prepare("SELECT product_id, availability_details FROM Products WHERE name = :name AND is_active = TRUE LIMIT 1");
    $stmtResProdId->bindParam(':name', $reservationProductName, PDO::PARAM_STR);
    $stmtResProdId->execute();
    $resProdRow = $stmtResProdId->fetch();
    if ($resProdRow) {
        $restaurant_reservation_product_id = $resProdRow['product_id'];
        // Odczytaj pojemność i czas trwania z availability_details, jeśli są
        if ($resProdRow['availability_details']) {
            $avail_details = json_decode($resProdRow['availability_details'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($avail_details['total_units']) && is_numeric($avail_details['total_units'])) {
                    $response['max_capacity_per_slot'] = (int)$avail_details['total_units'];
                }
                if (isset($avail_details['slot_duration_hours']) && is_numeric($avail_details['slot_duration_hours'])) {
                    $response['slot_duration_hours'] = (int)$avail_details['slot_duration_hours'];
                }
            }
        }
    } else {
        $response['error'] = "Nie znaleziono produktu dla rezerwacji stolika ('$reservationProductName').";
        error_log("ajax_get_restaurant_availability: Produkt '$reservationProductName' nie znaleziony.");
        echo json_encode($response);
        exit;
    }
} else {
    $restaurant_reservation_product_id = $restaurant_reservation_product_id_get;
    // Jeśli ID produktu jest przekazane, również można by zaktualizować max_capacity/slot_duration z jego availability_details
}
$response['debug_info']['restaurant_product_id_used'] = $restaurant_reservation_product_id;


try {
    new DateTime($selected_date_str);
} catch (Exception $e) {
    $response['error'] = 'Nieprawidłowy format daty.';
    error_log("ajax_get_restaurant_availability.php: Invalid date format - " . $e->getMessage());
    echo json_encode($response);
    exit;
}


try {
    $start_dt_obj = new DateTime($selected_date_str . ' 09:00:00');
    $end_dt_limit_obj = new DateTime($selected_date_str . ' 00:00:00');
    $end_dt_limit_obj->modify('+1 day')->setTime(2,0,0); // Do 02:00 następnego dnia

    while($start_dt_obj < $end_dt_limit_obj) {
        $response['all_possible_slots'][] = $start_dt_obj->format('H:i');
        $start_dt_obj->modify('+30 minutes');
    }
    
    $stmt = $pdo->prepare(
        "SELECT start_datetime, end_datetime, quantity_booked 
         FROM bookings 
         WHERE product_id = :product_id 
           AND resource_type = 'restaurant_table'
           AND DATE(start_datetime) = :selected_date 
           AND booking_status = 'confirmed'"
    );
    $stmt->execute([
        ':product_id' => $restaurant_reservation_product_id, 
        ':selected_date' => $selected_date_str
    ]);
    $day_bookings_restaurant = $stmt->fetchAll();
    $response['debug_info']['day_bookings_count'] = count($day_bookings_restaurant);

    foreach ($response['all_possible_slots'] as $slot_time_str) {
        $slot_start_dt_restaurant = new DateTime($selected_date_str . ' ' . $slot_time_str);
        $slot_end_dt_restaurant = (clone $slot_start_dt_restaurant)->modify('+' . $response['slot_duration_hours'] . ' hours'); 
        
        $booked_tables_in_this_slot = 0;
        foreach ($day_bookings_restaurant as $booking) {
            $booking_start_dt_restaurant = new DateTime($booking['start_datetime']);
            $booking_end_dt_restaurant = new DateTime($booking['end_datetime']);

            if ($slot_start_dt_restaurant < $booking_end_dt_restaurant && $slot_end_dt_restaurant > $booking_start_dt_restaurant) {
                $booked_tables_in_this_slot += (int)$booking['quantity_booked'];
            }
        }
        $response['debug_info']['slot_check'][$slot_time_str] = $booked_tables_in_this_slot;

        if ($booked_tables_in_this_slot >= $response['max_capacity_per_slot']) {
            $response['unavailable_slots'][] = $slot_time_str;
        }
    }

} catch (Exception $e) {
    $response['error'] = 'Błąd serwera podczas generowania lub sprawdzania slotów restauracji: ' . $e->getMessage();
    error_log("Błąd w ajax_get_restaurant_availability.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
?>