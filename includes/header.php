<?php
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Usuń nazwę pliku skryptu, aby uzyskać ścieżkę do folderu
    $script_dir = str_replace(basename($_SERVER['SCRIPT_NAME'] ?? 'index.php'), '', $_SERVER['SCRIPT_NAME'] ?? '/');
    // Usuń podwójne slashe i zapewnij jeden na końcu
    define('BASE_URL', rtrim($protocol . $host . $script_dir, '/') . '/');
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title ?? 'AquaParadise'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css"> 

    <script>
        var isLoggedInFromPHP = <?php echo (isset($_SESSION['user_id']) ? 'true' : 'false'); ?>;
        var basePathJS = '<?php echo addslashes(BASE_URL); ?>'; {/* Użycie BASE_URL */}
    </script>
    <?php
        // Wszelkie inne globalne skrypty lub meta tagi, które chcesz mieć w <head> na każdej stronie
    ?>
</head>
<body>
<?php
    // Wywołanie funkcji display_flash_message(), jeśli istnieje (zdefiniowana w functions.php)
    if (function_exists('display_flash_message')) {
        display_flash_message();
    }
?>