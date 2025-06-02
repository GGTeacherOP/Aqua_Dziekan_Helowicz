<?php
// ajax_get_hotel_availability.php
require_once __DIR__ . '/config/init.php'; // Zakładamy, że ten plik jest w głównym folderze projektu

if (!headers_sent()) {
    header('Content-Type: application/json');
}

$response = [
    'available' => false,
    'message' => 'Proszę wybrać typ pokoju oraz prawidłowe daty zameldowania i wymeldowania.',
    'checked_product_id' => null,
    'checked_dates' => null,
    'debug_info' => [] // Dodatkowe informacje do debugowania
];

if (!isset($pdo)) {
    $response['message'] = 'Błąd serwera: Brak połączenia z bazą danych.';
    $response['debug_info']['pdo_status'] = 'PDO object not available at script start.';
    error_log("ajax_get_hotel_availability.php: PDO object not available.");
    echo json_encode($response);
    exit;
}

$product_id = isset($_GET['product_id']) ? filter_var($_GET['product_id'], FILTER_VALIDATE_INT) : null;
$check_in_date_str = $_GET['check_in'] ?? null;
$check_out_date_str = $_GET['check_out'] ?? null;

$response['checked_product_id'] = $product_id;
$response['checked_dates'] = "Od: " . htmlspecialchars((string)$check_in_date_str) . " Do: " . htmlspecialchars((string)$check_out_date_str);

if (!$product_id || $product_id === false || $product_id <= 0) {
    $response['message'] = 'Nieprawidłowy lub brakujący typ pokoju.';
    $response['debug_info']['input_error'] = 'Invalid or missing product_id.';
    echo json_encode($response);
    exit;
}

if (!$check_in_date_str || !$check_out_date_str) {
    $response['message'] = 'Proszę podać datę zameldowania i wymeldowania.';
    $response['debug_info']['input_error'] = 'Missing check_in or check_out date.';
    echo json_encode($response);
    exit;
}

try {
    $check_in_dt_obj = new DateTime($check_in_date_str);
    $check_out_dt_obj = new DateTime($check_out_date_str);

    if ($check_in_dt_obj >= $check_out_dt_obj) {
        $response['message'] = 'Data wymeldowania musi być późniejsza niż data zameldowania.';
        $response['debug_info']['date_error'] = 'Check-out date must be after check-in date.';
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    $response['message'] = 'Nieprawidłowy format daty.';
    $response['debug_info']['date_format_error'] = $e->getMessage();
    error_log("ajax_get_hotel_availability.php: Invalid date format - " . $e->getMessage());
    echo json_encode($response);
    exit;
}


$max_rooms_for_type = 30; // Domyślna wartość, jeśli nie zdefiniowano w bazie
try {
    $stmt_prod = $pdo->prepare("SELECT availability_details FROM products WHERE product_id = :product_id_for_details");
    $stmt_prod->execute([':product_id_for_details' => $product_id]);
    $prod_details_json = $stmt_prod->fetchColumn();
    
    $response['debug_info']['product_availability_details_raw'] = $prod_details_json;

    if ($prod_details_json) {
        $details_arr = json_decode($prod_details_json, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($details_arr['total_units']) && is_numeric($details_arr['total_units'])) {
            $max_rooms_for_type = (int) $details_arr['total_units'];
            $response['debug_info']['max_rooms_from_db'] = $max_rooms_for_type;
        } else {
            $response['debug_info']['availability_details_warning'] = 'Could not parse total_units from availability_details or not numeric.';
            error_log("ajax_get_hotel_availability: Could not parse total_units for product_id $product_id. JSON: $prod_details_json. Error: " . json_last_error_msg());
        }
    } else {
         $response['debug_info']['availability_details_warning'] = 'No availability_details found for product.';
    }
} catch (PDOException $e) {
    error_log("ajax_get_hotel_availability: PDOException fetching total_units for product_id $product_id: " . $e->getMessage());
    $response['debug_info']['db_error_product_details'] = "Error fetching product details: " . $e->getMessage();
    // Kontynuuj z domyślną wartością $max_rooms_for_type, ale błąd został zalogowany
}


try {
    $is_available_for_all_days = true;
    $current_check_date = $check_in_dt_obj; // Użyj obiektu DateTime

    $sql_check_bookings = "SELECT SUM(quantity_booked) as total_booked_on_day
                           FROM bookings
                           WHERE product_id = :product_id
                             AND resource_type = 'hotel_room'
                             AND booking_status = 'confirmed' 
                             AND DATE(start_datetime) <= :current_day_check_end_cond 
                             AND DATE(end_datetime) > :current_day_check_start_cond";
    $stmt_check = $pdo->prepare($sql_check_bookings);

    $days_checked_debug = [];

    while ($current_check_date < $check_out_dt_obj) {
        $date_sql_format = $current_check_date->format('Y-m-d');
        $params_to_execute = [
            ':product_id' => $product_id,
            ':current_day_check_start_cond' => $date_sql_format, // Dzień, który sprawdzamy
            ':current_day_check_end_cond' => $date_sql_format   // Dzień, który sprawdzamy
        ];
        
        // error_log("Hotel Availability Check - SQL: $sql_check_bookings, Params: " . json_encode($params_to_execute)); // Bardziej szczegółowy log
        
        $stmt_check->execute($params_to_execute);
        $result = $stmt_check->fetch();
        $booked_on_this_day = $result ? (int)$result['total_booked_on_day'] : 0;
        
        $days_checked_debug[$date_sql_format] = $booked_on_this_day;

        if ($booked_on_this_day >= $max_rooms_for_type) {
            $is_available_for_all_days = false;
            $response['message'] = "Niestety, wybrany typ pokoju nie jest dostępny w dniu $date_sql_format (zarezerwowano $booked_on_this_day z $max_rooms_for_type dostępnych). Spróbuj wybrać inny termin lub typ pokoju.";
            break; 
        }
        $current_check_date->modify('+1 day');
    }
    $response['debug_info']['days_checked_counts'] = $days_checked_debug;

    if ($is_available_for_all_days) {
        $response['available'] = true;
        $response['message'] = "Wybrany typ pokoju jest dostępny w podanym terminie! (Dostępne: " . ($max_rooms_for_type - ($booked_on_this_day ?? 0)) . "/" . $max_rooms_for_type . ")";
    }

} catch (PDOException $e) { 
    $response['message'] = 'Błąd serwera podczas sprawdzania dostępności.';
    $response['debug_info']['db_error_booking_check'] = "PDOException in booking check: " . $e->getMessage();
    error_log("Błąd PDO (hotel availability loop): " . $e->getMessage() . " | Product ID: " . $product_id . " | Dates: " . $check_in_date_str . " to " . $check_out_date_str . " | Last checked day: " . ($date_sql_format ?? 'N/A'));
} catch (Exception $e) { 
    $response['message'] = 'Wystąpił nieoczekiwany błąd serwera.';
    $response['debug_info']['general_error'] = "General Exception: " . $e->getMessage();
    error_log("Ogólny błąd (hotel availability): " . $e->getMessage());
}

echo json_encode($response);
exit;
?>