<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    }
}

// Definicja BASE_URL - kluczowa dla poprawnego ładowania zasobów
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost'; // np. localhost
    $script_name_path = dirname($_SERVER['SCRIPT_NAME'] ?? 'index.php'); 
    $base_url_path = rtrim($script_name_path, '/\\'); // Poprawione dla spójności z Windows/Linux
    if ($base_url_path === '\\' || $base_url_path === '/') { 
        $base_url_path = ''; 
    }
    
    define('BASE_URL', rtrim($protocol . $host . $base_url_path, '/') . '/');
}

// Definicja BASE_PATH, jeśli nie została zdefiniowana wcześniej (np. w init.php)
if (!defined('BASE_PATH')) {
    // Zakładamy, że header.php jest w /includes/, więc __DIR__ to /includes, a .. to katalog główny
    define('BASE_PATH', rtrim(realpath(__DIR__ . '/..'), '/\\'));
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'AquaParadise'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css?v=<?php echo file_exists(BASE_PATH . '/style.css') ? filemtime(BASE_PATH . '/style.css') : time(); ?>">

    <script>
        var isLoggedInFromPHP = <?php echo (isset($_SESSION['user_id']) ? 'true' : 'false'); ?>;
        var basePathJS = '<?php echo addslashes(BASE_URL); ?>';
        var currentUserFirstNameFromPHP = '<?php echo addslashes(isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : ''); ?>';
        var currentUserLastNameFromPHP = '<?php echo addslashes(isset($_SESSION['user_last_name']) ? $_SESSION['user_last_name'] : ''); ?>';
        var currentUserEmailFromPHP = '<?php echo addslashes(isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''); ?>';
    </script>
</head>
<body>
<?php
    // Sprawdzenie, czy $pdo jest dostępne i czy funkcja istnieje, zanim zostanie wywołana
    // Zakładam, że display_flash_message() jest zdefiniowane globalnie lub w init.php
    if (function_exists('display_flash_message')) { 
        display_flash_message();
    }
?>