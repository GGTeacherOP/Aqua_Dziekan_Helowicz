<?php
// src/functions.php

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('display_flash_message')) {
    function display_flash_message() {
        if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) { // Dodano is_array
            $message_type = e($_SESSION['flash_message']['type'] ?? 'info'); // Domyślnie info
            $message_text = e($_SESSION['flash_message']['text'] ?? '');
            echo "<div class=\"flash-message " . $message_type . "\">" . $message_text . "</div>";
            unset($_SESSION['flash_message']);
        } elseif (isset($_SESSION['flash_message']) && is_string($_SESSION['flash_message'])) { // Obsługa starszego formatu
            echo "<div class=\"flash-message info\">" . e($_SESSION['flash_message']) . "</div>";
            unset($_SESSION['flash_message']);
        }
    }
}


// Zmieniona nazwa na user_has_permission_by_name dla spójności
if (!function_exists('user_has_permission')) { // Zmieniono nazwę na user_has_permission
    function user_has_permission(PDO $pdo_conn, $user_id_check, $permission_name_check) {
        if (!$user_id_check || !$pdo_conn) { // Dodano sprawdzenie $pdo_conn
            error_log("user_has_permission: Brak user_id lub pdo_conn.");
            return false;
        }
        try {
            // Najpierw pobierz role_id użytkownika
            $stmt_user_role = $pdo_conn->prepare("SELECT role_id FROM Users WHERE user_id = :user_id");
            $stmt_user_role->execute([':user_id' => $user_id_check]);
            $user_role_data = $stmt_user_role->fetch();

            if (!$user_role_data) {
                error_log("user_has_permission: Nie znaleziono roli dla user_id: $user_id_check");
                return false; // Użytkownik nie ma roli lub nie istnieje
            }
            $current_user_role_id = $user_role_data['role_id'];

            // Następnie sprawdź uprawnienia dla tej roli
            $stmt_perm = $pdo_conn->prepare(
                "SELECT COUNT(*) FROM RolePermissions rp
                 JOIN Permissions p ON rp.permission_id = p.permission_id
                 WHERE rp.role_id = :role_id AND p.permission_name = :permission_name"
            );
            $stmt_perm->execute([':role_id' => $current_user_role_id, ':permission_name' => $permission_name_check]);
            return $stmt_perm->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Błąd sprawdzania uprawnień dla user_id $user_id_check, permission $permission_name_check: " . $e->getMessage());
            return false;
        }
    }
}


if (!function_exists('is_column_nullable')) {
    function is_column_nullable($pdo_conn, $table_name, $column_name) {
        if (!$pdo_conn || empty($table_name) || empty($column_name)) return false;
        try {
            // Użyj SHOW FULL COLUMNS, aby uzyskać więcej informacji, w tym o NULL
            $stmt = $pdo_conn->prepare("SHOW FULL COLUMNS FROM `$table_name` WHERE Field = :column_name");
            $stmt->execute([':column_name' => $column_name]);
            $column_info = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($column_info && isset($column_info['Null']) && $column_info['Null'] === 'YES');
        } catch (PDOException $e) {
            error_log("is_column_nullable error for table $table_name, column $column_name: " . $e->getMessage());
            return false; 
        }
    }
}

// Funkcja get_current_cart_id (z Twojego pliku)
if (!function_exists('get_current_cart_id')) {
    function get_current_cart_id(PDO $pdo): ?int {
        if (session_status() == PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            } else {
                error_log("get_current_cart_id: Krytyczny błąd - Sesja nie została uruchomiona, a nagłówki zostały już wysłane.");
                return null;
            }
        }
        $cart_id_to_return = null;
        if (isset($_SESSION['user_id'])) {
            $session_cart_var = 'user_cart_id_' . $_SESSION['user_id'];
            if (isset($_SESSION[$session_cart_var])) {
                try {
                    $stmt_verify = $pdo->prepare("SELECT cart_id FROM Carts WHERE cart_id = :cart_id AND user_id = :user_id");
                    $stmt_verify->execute([':cart_id' => $_SESSION[$session_cart_var], ':user_id' => $_SESSION['user_id']]);
                    if ($stmt_verify->fetch()) {
                        $cart_id_to_return = (int)$_SESSION[$session_cart_var];
                    } else { unset($_SESSION[$session_cart_var]); }
                } catch (PDOException $e) { error_log("Błąd weryfikacji user_cart_id w get_current_cart_id: " . $e->getMessage()); unset($_SESSION[$session_cart_var]); }
            }
            if ($cart_id_to_return === null) {
                try {
                    $stmt_find = $pdo->prepare("SELECT cart_id FROM Carts WHERE user_id = :user_id");
                    $stmt_find->execute([':user_id' => $_SESSION['user_id']]);
                    $cart = $stmt_find->fetch();
                    if ($cart) { $cart_id_to_return = (int)$cart['cart_id'];
                    } else {
                        $stmt_create = $pdo->prepare("INSERT INTO Carts (user_id, created_at, updated_at) VALUES (:user_id, NOW(), NOW())");
                        $stmt_create->execute([':user_id' => $_SESSION['user_id']]);
                        $cart_id_to_return = (int)$pdo->lastInsertId();
                    }
                    $_SESSION[$session_cart_var] = $cart_id_to_return;
                } catch (PDOException $e) { error_log("Błąd znajdowania/tworzenia koszyka dla użytkownika w get_current_cart_id: " . $e->getMessage()); return null; }
            }
        } else {
            $php_session_id = session_id();
            if (!$php_session_id) { error_log("get_current_cart_id: Brak session_id() dla gościa."); return null; }
            if (isset($_SESSION['guest_cart_id'])) {
                try {
                    $stmt_verify_guest = $pdo->prepare("SELECT cart_id FROM Carts WHERE cart_id = :cart_id AND user_id IS NULL AND session_id = :session_id");
                    $stmt_verify_guest->execute([':cart_id' => $_SESSION['guest_cart_id'], ':session_id' => $php_session_id]);
                    if ($stmt_verify_guest->fetch()) {
                        $cart_id_to_return = (int)$_SESSION['guest_cart_id'];
                    } else { unset($_SESSION['guest_cart_id']); }
                } catch (PDOException $e) { error_log("Błąd weryfikacji guest_cart_id w get_current_cart_id: " . $e->getMessage()); unset($_SESSION['guest_cart_id']); }
            }
            if ($cart_id_to_return === null) {
                try {
                    $stmt_find_guest_db = $pdo->prepare("SELECT cart_id FROM Carts WHERE session_id = :session_id AND user_id IS NULL");
                    $stmt_find_guest_db->execute([':session_id' => $php_session_id]);
                    $cart_db = $stmt_find_guest_db->fetch();
                    if ($cart_db) { $cart_id_to_return = (int)$cart_db['cart_id'];
                    } else {
                        $stmt_create_guest = $pdo->prepare("INSERT INTO Carts (session_id, created_at, updated_at) VALUES (:session_id, NOW(), NOW())");
                        $stmt_create_guest->execute([':session_id' => $php_session_id]);
                        $cart_id_to_return = (int)$pdo->lastInsertId();
                    }
                    $_SESSION['guest_cart_id'] = $cart_id_to_return;
                } catch (PDOException $e) { error_log("Błąd znajdowania/tworzenia koszyka dla gościa w get_current_cart_id: " . $e->getMessage()); return null; }
            }
        }
        return $cart_id_to_return;
    }
}
?>