<?php
// includes/navigation.php

if (!isset($pdo)) {
    $init_path = __DIR__ . '/../config/init.php';
    if (file_exists($init_path)) {
        require_once $init_path;
    } else {
        // Fallback if init.php is not available or $pdo isn't set
        error_log("includes/navigation.php: Cannot load config/init.php. \$pdo is not defined.");
        $cart_item_count = 0;
        // Define other variables to avoid errors if $pdo is not set
        $user_first_name_nav = '';
        $user_role_name_nav = '';
        $is_admin_or_employee_nav = false;
    }
}

$cart_item_count = 0;
if (isset($pdo) && function_exists('get_current_cart_id')) {
    $current_cart_id_nav = get_current_cart_id($pdo);
    if ($current_cart_id_nav) {
        try {
            $stmt_count = $pdo->prepare("SELECT SUM(quantity) as total_items FROM CartItems WHERE cart_id = :cart_id");
            $stmt_count->bindParam(':cart_id', $current_cart_id_nav, PDO::PARAM_INT);
            $stmt_count->execute();
            $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);
            if ($count_result && $count_result['total_items']) {
                $cart_item_count = (int)$count_result['total_items'];
            }
        } catch (PDOException $e) {
            error_log("Error fetching cart count in navigation.php: " . $e->getMessage());
            $cart_item_count = 0; // Default to 0 on error
        }
    }
}

$current_page_nav = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$user_first_name_nav = $_SESSION['user_first_name'] ?? '';
$user_role_name_nav = $_SESSION['user_role_name'] ?? '';
$employee_admin_roles = ['Administrator', 'SuperAdmin', 'Pracownik Hotel', 'Pracownik SPA', 'Pracownik Restauracja', 'Pracownik Aquapark'];
$is_admin_or_employee_nav = isset($_SESSION['user_id']) && in_array($user_role_name_nav, $employee_admin_roles);

$base_url_nav = defined('BASE_URL') ? BASE_URL : './'; // Use defined BASE_URL or fallback

?>
<nav class="main-nav">
    <div class="nav-links">
        <a href="<?php echo $base_url_nav; ?>index.php" class="<?php echo ($current_page_nav == 'index.php') ? 'active' : ''; ?>">Strona Główna</a>
        <a href="<?php echo $base_url_nav; ?>aquapark.php" class="<?php echo ($current_page_nav == 'aquapark.php') ? 'active' : ''; ?>">Aquapark</a>
        <a href="<?php echo $base_url_nav; ?>hotel.php" class="<?php echo ($current_page_nav == 'hotel.php') ? 'active' : ''; ?>">Hotel</a>
        <a href="<?php echo $base_url_nav; ?>spa.php" class="<?php echo ($current_page_nav == 'spa.php') ? 'active' : ''; ?>">Spa & Wellness</a>
        <a href="<?php echo $base_url_nav; ?>restaurant.php" class="<?php echo ($current_page_nav == 'restaurant.php') ? 'active' : ''; ?>">Restauracja</a>
        <a href="<?php echo $base_url_nav; ?>opinions_view.php" class="<?php echo ($current_page_nav == 'opinions_view.php') ? 'active' : ''; ?>">Opinie</a>
    </div>
    <div class="nav-auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-nav-greeting">
                <i class="fas fa-user-circle user-greeting-icon"></i>
                <span class="user-greeting-text">Witaj, <?php echo htmlspecialchars($user_first_name_nav, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <a href="<?php echo $base_url_nav; ?>cart_view.php" class="auth-button cart-button" id="cartButton" title="Koszyk">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount"><?php echo $cart_item_count; ?></span>
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($is_admin_or_employee_nav): ?>
                 <a href="<?php echo $base_url_nav; ?>admin/index.php" class="auth-button register-button admin-panel-button">Panel</a>
            <?php endif; ?>
            <a href="<?php echo $base_url_nav; ?>logout.php" class="auth-button logout-button" id="logoutButton">Wyloguj</a>
        <?php else: ?>
            <a href="<?php echo $base_url_nav; ?>login.php" class="auth-button login-button" id="loginButton">Zaloguj się</a>
            <a href="<?php echo $base_url_nav; ?>signup.php" class="auth-button register-button" id="registerButton">Zarejestruj się</a>
        <?php endif; ?>
    </div>
</nav>