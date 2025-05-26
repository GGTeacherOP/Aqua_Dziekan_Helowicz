<?php
// ajax_get_spa_availability.php
require_once __DIR__ . '/config/init.php';

if (!headers_sent()) {
    header('Content-Type: application/json');
}

$response = [
    'unavailable_slots' => [],
    'all_possible_slots' => [],
    'max_capacity_per_slot' => 10, // Maks. 10 jednoczesnych sesji/stanowisk SPA
    'slot_duration_minutes' => 45, // Przyjmujemy średni czas trwania rezerwacji blokującej jedno stanowisko
    'debug_info' => []
];

if (!isset($pdo)) {
    $response['error'] = 'Błąd serwera: Brak połączenia z bazą danych.';
    error_log("ajax_get_spa_availability.php: PDO object not available.");
    echo json_encode($response);
    exit;
}

$selected_date_str = $_GET['date'] ?? null;

if (!$selected_date_str) {
    $response['error'] = 'Brak wybranej daty.';
    echo json_encode($response);
    exit;
}

try {
    new DateTime($selected_date_str); // Walidacja formatu daty
} catch (Exception $e) {
    $response['error'] = 'Nieprawidłowy format daty.';
    error_log("ajax_get_spa_availability.php: Invalid date format - " . $e->getMessage());
    echo json_encode($response);
    exit;
}

// Możesz pobrać max_capacity i slot_duration z produktu 'Pakiet SPA Indywidualny' (product_id 15 w Twojej bazie)
$spaPlaceholderProductId = 15; // ID dla "Pakiet SPA Indywidualny"
try {
    $stmt_prod_spa = $pdo->prepare("SELECT availability_details FROM products WHERE product_id = :product_id");
    $stmt_prod_spa->execute([':product_id' => $spaPlaceholderProductId]);
    $spa_prod_details_json = $stmt_prod_spa->fetchColumn();
    if ($spa_prod_details_json) {
        $spa_details_arr = json_decode($spa_prod_details_json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($spa_details_arr['total_units']) && is_numeric($spa_details_arr['total_units'])) {
                $response['max_capacity_per_slot'] = (int)$spa_details_arr['total_units'];
            }
            if (isset($spa_details_arr['default_slot_duration_minutes']) && is_numeric($spa_details_arr['default_slot_duration_minutes'])) {
                $response['slot_duration_minutes'] = (int)$spa_details_arr['default_slot_duration_minutes'];
            }
        }
    }
} catch (PDOException $e) {
    error_log("ajax_get_spa_availability.php: Błąd pobierania danych produktu SPA placeholder: " . $e->getMessage());
}


try {
    // Generuj sloty czasowe dla SPA (od 13:30 do 01:00 co 30 minut)
    $start_hour = 13; $start_minute = 30;
    $end_hour_limit = 1; // Do 01:00 (następnego dnia)

    $current_dt_spa = new DateTime($selected_date_str);
    $current_dt_spa->setTime($start_hour, $start_minute);

    $end_limit_dt_spa = new DateTime($selected_date_str);
    if ($end_hour_limit <= $start_hour) { 
        $end_limit_dt_spa->modify('+1 day');
    }
    $end_limit_dt_spa->setTime($end_hour_limit, 0);

    while($current_dt_spa < $end_limit_dt_spa) {
        $response['all_possible_slots'][] = $current_dt_spa->format('H:i');
        $current_dt_spa->modify('+30 minutes');
    }
    
    $stmt = $pdo->prepare(
        "SELECT start_datetime, end_datetime, quantity_booked 
         FROM bookings 
         WHERE resource_type = 'spa_slot'
           AND DATE(start_datetime) = :selected_date 
           AND booking_status = 'confirmed'"
    );
    $stmt->execute([':selected_date' => $selected_date_str]);
    $day_bookings_spa = $stmt->fetchAll();
    $response['debug_info']['day_bookings_count'] = count($day_bookings_spa);

    foreach ($response['all_possible_slots'] as $slot_time_str) {
        $slot_start_dt_spa = new DateTime($selected_date_str . ' ' . $slot_time_str);
        $slot_end_dt_spa = (clone $slot_start_dt_spa)->modify('+' . $response['slot_duration_minutes'] . ' minutes'); 
        
        $occupied_spa_rooms_this_slot = 0;
        foreach ($day_bookings_spa as $booking) {
            $booking_start_dt_spa = new DateTime($booking['start_datetime']);
            $booking_end_dt_spa = new DateTime($booking['end_datetime']);

            if ($slot_start_dt_spa < $booking_end_dt_spa && $slot_end_dt_spa > $booking_start_dt_spa) {
                $occupied_spa_rooms_this_slot += (int)$booking['quantity_booked'];
            }
        }
        $response['debug_info']['slot_check'][$slot_time_str] = $occupied_spa_rooms_this_slot;

        if ($occupied_spa_rooms_this_slot >= $response['max_capacity_per_slot']) {
            $response['unavailable_slots'][] = $slot_time_str;
        }
    }

} catch (Exception $e) {
    $response['error'] = 'Błąd serwera podczas generowania lub sprawdzania slotów SPA: ' . $e->getMessage();
    error_log("Błąd w ajax_get_spa_availability.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
?>