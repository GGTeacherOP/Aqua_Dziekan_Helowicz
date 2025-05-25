<?php
// config/init.php

if (session_status() == PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    } else {
        error_log("CRITICAL: init.php - Session not started, headers already sent. Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . " from " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A'));
    }
}

// Logika "Zapamiętaj mnie" została usunięta.

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $script_name_for_base_url = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $script_directory_raw = dirname($script_name_for_base_url);
    $script_directory = str_replace('\\', '/', $script_directory_raw);

    $project_root_on_server = str_replace('\\', '/', BASE_PATH);
    $document_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? $project_root_on_server);
    
    $base_url_path = '';
    if (strpos($project_root_on_server, $document_root) === 0) {
        $base_url_path = substr($project_root_on_server, strlen($document_root));
    } else {
        $path_parts = explode('/', trim($script_directory, '/'));
        // Usunięto bardziej skomplikowaną logikę, zakładając prostszą strukturę lub poprawne ustawienie document_root
        if (count($path_parts) > 0 && !empty($path_parts[0]) && $path_parts[0] !== basename(BASE_PATH) && $script_directory !== '/') {
             // Jeśli skrypt jest w podfolderze projektu, a BASE_PATH to katalog nadrzędny
             $base_url_path = $script_directory; 
        } else if ($script_directory === '/' || $script_directory === '') {
            $base_url_path = ''; // Główny katalog serwera
        }
        else { // Zakładamy, że BASE_PATH to bezpośredni podfolder document_root
            $base_url_path = '/' . basename(BASE_PATH);
        }
    }
    
    $base_url_path = rtrim(str_replace('\\', '/', $base_url_path), '/');
    // Dodatkowe sprawdzenie, aby uniknąć podwójnego slasha, jeśli $base_url_path jest pusty
    define('BASE_URL', $protocol . $host . ($base_url_path ? $base_url_path . '/' : '/'));

}


if (file_exists(BASE_PATH . '/config/db_connection.php')) {
    if (!isset($pdo)) { // Dołącz tylko jeśli $pdo jeszcze nie istnieje
        require_once BASE_PATH . '/config/db_connection.php'; 
    }
} else {
    error_log("CRITICAL: init.php - Missing config/db_connection.php. Path: " . BASE_PATH . '/config/db_connection.php');
    die("Krytyczny błąd: Brak pliku konfiguracyjnego bazy danych.");
}

if (file_exists(BASE_PATH . '/src/functions.php')) {
    require_once BASE_PATH . '/src/functions.php';
} else {
    error_log("CRITICAL: init.php - Missing src/functions.php. Path: " . BASE_PATH . '/src/functions.php');
    die("Krytyczny błąd: Brak pliku z funkcjami pomocniczymi.");
}

date_default_timezone_set('Europe/Warsaw');

if (isset($_SESSION['user_id']) && !isset($_SESSION['user_role_name']) && isset($pdo)) { 
    try {
        $stmt_user_session = $pdo->prepare(
            "SELECT u.first_name, u.last_name, u.email, u.role_id, r.role_name
             FROM Users u
             JOIN Roles r ON u.role_id = r.role_id
             WHERE u.user_id = :user_id"
        );
        $stmt_user_session->execute([':user_id' => $_SESSION['user_id']]);
        $user_session_data_refresh = $stmt_user_session->fetch(); 
        if ($user_session_data_refresh) {
            $_SESSION['user_first_name'] = $user_session_data_refresh['first_name'];
            $_SESSION['user_last_name'] = $user_session_data_refresh['last_name'];
            $_SESSION['user_email'] = $user_session_data_refresh['email'];
            $_SESSION['user_role_id'] = (int)$user_session_data_refresh['role_id'];
            $_SESSION['user_role_name'] = $user_session_data_refresh['role_name'];
        } else {
            error_log("User ID " . ($_SESSION['user_id'] ?? 'N/A') . " from session not found in database during data refresh. Clearing session.");
            $_SESSION = array(); // Wyczyść wszystkie dane sesyjne
            if (ini_get("session.use_cookies")) { // Usuń ciasteczko sesyjne
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            if (session_status() === PHP_SESSION_ACTIVE) { // Zniszcz sesję tylko jeśli jest aktywna
                 session_destroy();
            }
            // Nie ma potrzeby usuwania ciasteczka remember_me, bo funkcja jest usuwana
        }
    } catch (PDOException $e) {
        error_log("Error fetching user session data refresh in init.php: " . $e->getMessage());
    }
}
global $pdo; 
?>