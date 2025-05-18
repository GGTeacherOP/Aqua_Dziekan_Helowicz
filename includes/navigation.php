<?php
// navigation.php

if (!isset($pdo)) {
    // Próba załadowania init.php, jeśli nie zostało to zrobione wcześniej
    $init_path = __DIR__ . '/../config/init.php';
    if (file_exists($init_path)) {
        require_once $init_path;
    } else {
        error_log("includes/navigation.php: Nie można załadować config/init.php. " . '$pdo' . " nie jest zdefiniowane.");
        $cart_item_count = 0;
        $current_page_nav = basename($_SESSION['_PHP_SELF'] ?? $_SERVER['PHP_SELF'] ?? 'index.php');
        $user_first_name_nav = '';
        $user_role_name_nav = '';
        $is_admin_or_employee_nav = false;
    }
}

if (isset($pdo)) {
    $cart_item_count = 0;
    if (function_exists('get_current_cart_id')) {
        $current_cart_id_nav = get_current_cart_id($pdo);
    } else {
        $current_cart_id_nav = null;
        // Fallback logic if get_current_cart_id is not available
        if (isset($_SESSION['user_id'])) {
            $stmt_cart_id_nav = $pdo->prepare("SELECT cart_id FROM Carts WHERE user_id = ?");
            $stmt_cart_id_nav->execute([$_SESSION['user_id']]);
            $cart_nav = $stmt_cart_id_nav->fetch();
            if ($cart_nav) $current_cart_id_nav = $cart_nav['cart_id'];
        } elseif (isset($_SESSION['guest_cart_assigned_db_id'])) { // Changed from guest_cart_session_id to check assigned DB ID
             $stmt_cart_id_nav = $pdo->prepare("SELECT cart_id FROM Carts WHERE cart_id = ? AND user_id IS NULL AND session_id = ?");
             $stmt_cart_id_nav->execute([$_SESSION['guest_cart_assigned_db_id'], session_id()]);
             $cart_nav = $stmt_cart_id_nav->fetch();
            if ($cart_nav) $current_cart_id_nav = $cart_nav['cart_id'];
        }
    }

    if ($current_cart_id_nav) {
        try {
            $stmt_count = $pdo->prepare("SELECT SUM(quantity) as total_items FROM CartItems WHERE cart_id = :cart_id");
            $stmt_count->bindParam(':cart_id', $current_cart_id_nav, PDO::PARAM_INT);
            $stmt_count->execute();
            $count_result = $stmt_count->fetch();
            if ($count_result && $count_result['total_items']) {
                $cart_item_count = (int)$count_result['total_items'];
            }
        } catch (PDOException $e) {
            error_log("Błąd pobierania licznika koszyka w navigation.php: " . $e->getMessage());
            $cart_item_count = 0;
        }
    }
} else {
    $cart_item_count = 0;
}

$current_page_nav = basename($_SESSION['_PHP_SELF'] ?? $_SERVER['PHP_SELF'] ?? 'index.php');
$user_first_name_nav = $_SESSION['user_first_name'] ?? '';
$user_role_name_nav = $_SESSION['user_role_name'] ?? '';
$employee_admin_roles = ['Administrator', 'SuperAdmin', 'Pracownik Hotel', 'Pracownik SPA', 'Pracownik Restauracja', 'Pracownik Aquapark'];
$is_admin_or_employee_nav = isset($_SESSION['user_id']) && in_array($user_role_name_nav, $employee_admin_roles);

$base_link_path = ''; // Domyślnie dla plików w głównym katalogu.
// Można to dostosować, jeśli navigation.php jest w podfolderze i linki mają być generowane inaczej.
// Np. jeśli jest w 'includes', a strony są w głównym folderze, ta pusta wartość jest OK.
// Jeśli strony są w podfolderach, a nawigacja w 'includes', to $base_link_path musiałoby być '../'
// Ale BASE_PATH i BASE_URL powinny załatwić sprawę globalnie. Dla uproszczenia zakładamy, że linki są z głównego folderu.
?>
<nav class="main-nav">
    <div class="nav-links">
        <?php // USUNIĘTO IKONĘ UŻYTKOWNIKA (fas fa-user-circle) Z TEGO MIEJSCA ?>
        <a href="<?php echo $base_link_path; ?>index.php" class="<?php echo ($current_page_nav == 'index.php') ? 'active' : ''; ?>">Strona Główna</a>
        <a href="<?php echo $base_link_path; ?>aquapark.php" class="<?php echo ($current_page_nav == 'aquapark.php') ? 'active' : ''; ?>">Aquapark</a>
        <a href="<?php echo $base_link_path; ?>hotel.php" class="<?php echo ($current_page_nav == 'hotel.php') ? 'active' : ''; ?>">Hotel</a>
        <a href="<?php echo $base_link_path; ?>spa.php" class="<?php echo ($current_page_nav == 'spa.php') ? 'active' : ''; ?>">Spa & Wellness</a>
        <a href="<?php echo $base_link_path; ?>restaurant.php" class="<?php echo ($current_page_nav == 'restaurant.php') ? 'active' : ''; ?>">Restauracja</a>
    </div>
    <div class="nav-auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-nav-greeting"> <?php // Nowy kontener dla powitania ?>
                <i class="fas fa-user-circle user-greeting-icon"></i> <?php // Ikona wewnątrz bloku powitania ?>
                <span class="user-greeting-text">Witaj, <?php echo e($user_first_name_nav); ?></span>
            </div>
        <?php endif; ?>

        <a href="<?php echo $base_link_path; ?>cart_view.php" class="auth-button cart-button" id="cartButton" title="Koszyk">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount"><?php echo $cart_item_count; ?></span>
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($is_admin_or_employee_nav): ?>
                 <a href="<?php echo $base_link_path; ?>admin/index.php" class="auth-button register-button admin-panel-button">Panel</a>
            <?php endif; ?>
            <a href="<?php echo $base_link_path; ?>logout.php" class="auth-button logout-button" id="logoutButton">Wyloguj</a>
        <?php else: ?>
            <a href="<?php echo $base_link_path; ?>login.php" class="auth-button login-button" id="loginButton">Zaloguj się</a>
            <a href="<?php echo $base_link_path; ?>signup.php" class="auth-button register-button" id="registerButton">Zarejestruj się</a>
        <?php endif; ?>
    </div>
</nav>