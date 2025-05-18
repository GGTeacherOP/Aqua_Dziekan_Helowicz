<?php
// src/functions.php

// Funkcja pomocnicza do wyświetlania komunikatów flash
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message_type = htmlspecialchars($_SESSION['flash_message']['type'], ENT_QUOTES, 'UTF-8');
        $message_text = htmlspecialchars($_SESSION['flash_message']['text'], ENT_QUOTES, 'UTF-8');
        // Style inline są tutaj dla przykładu, lepiej użyć dedykowanych klas CSS
        $style = "padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align:center; border: 1px solid transparent;";
        if ($message_type === 'success') {
            $style .= "color: #155724; background-color: #d4edda; border-color: #c3e6cb;";
        } elseif ($message_type === 'error') {
            $style .= "color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;";
        } elseif ($message_type === 'warning') {
            $style .= "color: #856404; background-color: #fff3cd; border-color: #ffeeba;";
        } else { // Domyślnie np. info
             $style .= "color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb;";
        }
        echo "<div class=\"flash-message {$message_type}\" style=\"{$style}\">{$message_text}</div>";
        unset($_SESSION['flash_message']);
    }
}

// Funkcja do bezpiecznego wyświetlania danych (uniknięcie XSS)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Funkcja sprawdzania uprawnień
function has_permission($pdo_conn, $user_id_check, $permission_name_check) {
    if (!$user_id_check) return false;

    $stmt_perm = $pdo_conn->prepare(
        "SELECT COUNT(*)
         FROM RolePermissions rp
         JOIN Users u ON rp.role_id = u.role_id
         JOIN Permissions p ON rp.permission_id = p.permission_id
         WHERE u.user_id = :user_id AND p.permission_name = :permission_name"
    );
    $stmt_perm->bindParam(':user_id', $user_id_check, PDO::PARAM_INT);
    $stmt_perm->bindParam(':permission_name', $permission_name_check, PDO::PARAM_STR);
    $stmt_perm->execute();
    return $stmt_perm->fetchColumn() > 0;
}

// Funkcja do pobierania cart_id
function get_current_cart_id($pdo) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT cart_id FROM Carts WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart = $stmt->fetch();
        if ($cart) {
            return $cart['cart_id'];
        } else {
            $stmt_insert = $pdo->prepare("INSERT INTO Carts (user_id) VALUES (?)");
            $stmt_insert->execute([$_SESSION['user_id']]);
            return $pdo->lastInsertId();
        }
    } elseif (isset($_SESSION['guest_cart_assigned_db_id'])) { // Sprawdź, czy mamy już ID koszyka z DB dla tej sesji PHP
        $stmt = $pdo->prepare("SELECT cart_id FROM Carts WHERE cart_id = ? AND user_id IS NULL AND session_id = ?");
        $stmt->execute([$_SESSION['guest_cart_assigned_db_id'], session_id()]);
        if ($stmt->fetch()) {
            return $_SESSION['guest_cart_assigned_db_id'];
        } else {
            unset($_SESSION['guest_cart_assigned_db_id']);
        }
    }
    // Jeśli powyższe nie zwróciło, lub dla nowego gościa
    $current_php_session_id = session_id();
    $stmt = $pdo->prepare("SELECT cart_id FROM Carts WHERE session_id = ? AND user_id IS NULL");
    $stmt->execute([$current_php_session_id]);
    $cart = $stmt->fetch();
    if ($cart) {
        $_SESSION['guest_cart_assigned_db_id'] = $cart['cart_id'];
        return $cart['cart_id'];
    } else {
        $stmt_insert = $pdo->prepare("INSERT INTO Carts (session_id) VALUES (?)");
        $stmt_insert->execute([$current_php_session_id]);
        $new_cart_id = $pdo->lastInsertId();
        $_SESSION['guest_cart_assigned_db_id'] = $new_cart_id;
        return $new_cart_id;
    }
    // return null; // Teoretycznie nie powinno się tu dostać, bo zawsze tworzymy koszyk
}
?>