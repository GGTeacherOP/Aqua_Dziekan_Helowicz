<?php
// admin/process_opinion.php
require_once __DIR__ . '/../config/init.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/../login.php"); // Przekieruj, jeśli nie jest zalogowany
    exit;
}

// --- [ Kod do sprawdzania uprawnień (user_has_permission) - POWINIEN BYĆ W PLIKU FUNKCYJNYM ] ---
if (!function_exists('user_has_permission')) {
    function user_has_permission($pdo_conn, $user_id_check, $permission_name_check) {
        $stmt_role = $pdo_conn->prepare("SELECT role_id FROM users WHERE user_id = :user_id");
        $stmt_role->execute(['user_id' => $user_id_check]);
        $user_role = $stmt_role->fetch();
        if (!$user_role) return false;

        $stmt_perm = $pdo_conn->prepare("
            SELECT COUNT(*)
            FROM RolePermissions rp
            JOIN Permissions p ON rp.permission_id = p.permission_id
            WHERE rp.role_id = :role_id AND p.permission_name = :permission_name
        ");
        $stmt_perm->execute(['role_id' => $user_role['role_id'], 'permission_name' => $permission_name_check]);
        return $stmt_perm->fetchColumn() > 0;
    }
}
// --- [ Koniec kodu do sprawdzania uprawnień ] ---

$user_id = $_SESSION['user_id'];
$stmt_role_check = $pdo->prepare("SELECT role_id FROM users WHERE user_id = :user_id");
$stmt_role_check->execute(['user_id' => $user_id]);
$current_user_role = $stmt_role_check->fetch();

if (!$current_user_role || (!user_has_permission($pdo, $user_id, 'manage_opinions') && $current_user_role['role_id'] != 7) ) {
    $_SESSION['flash_message'] = "Nie masz uprawnień do wykonania tej akcji.";
    $_SESSION['flash_message_type'] = "error";
    header("Location: " . BASE_URL . "admin/index.php?table=opinions");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opinion_id'], $_POST['action'])) {
    $opinion_id = (int)$_POST['opinion_id'];
    $action = $_POST['action'];
    $admin_comment = isset($_POST['admin_comment']) ? trim($_POST['admin_comment']) : null;

    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE opinions SET status = 'Zaakceptowana', admin_comment = :admin_comment WHERE opinion_id = :opinion_id");
            $stmt->execute(['admin_comment' => $admin_comment, 'opinion_id' => $opinion_id]);
            $_SESSION['flash_message'] = "Opinia (ID: $opinion_id) została zaakceptowana.";
            $_SESSION['flash_message_type'] = "success";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE opinions SET status = 'Odrzucona', admin_comment = :admin_comment WHERE opinion_id = :opinion_id");
            $stmt->execute(['admin_comment' => $admin_comment, 'opinion_id' => $opinion_id]);
            $_SESSION['flash_message'] = "Opinia (ID: $opinion_id) została odrzucona.";
            $_SESSION['flash_message_type'] = "success";
        } elseif ($action === 'update_comment') {
            $stmt = $pdo->prepare("UPDATE opinions SET admin_comment = :admin_comment WHERE opinion_id = :opinion_id");
            $stmt->execute(['admin_comment' => $admin_comment, 'opinion_id' => $opinion_id]);
            $_SESSION['flash_message'] = "Komentarz administratora dla opinii (ID: $opinion_id) został zaktualizowany.";
            $_SESSION['flash_message_type'] = "success";
        } elseif ($action === 'delete_opinion') {
            // Tylko SuperAdmin (rola 7) może trwale usuwać opinie przez ten mechanizm
             if ($current_user_role['role_id'] == 7) {
                $stmt = $pdo->prepare("DELETE FROM opinions WHERE opinion_id = :opinion_id");
                $stmt->execute(['opinion_id' => $opinion_id]);
                $_SESSION['flash_message'] = "Opinia (ID: $opinion_id) została trwale usunięta.";
                $_SESSION['flash_message_type'] = "success";
             } else {
                $_SESSION['flash_message'] = "Nie masz uprawnień do trwałego usuwania opinii.";
                $_SESSION['flash_message_type'] = "error";
             }
        } else {
            $_SESSION['flash_message'] = "Nieznana akcja.";
            $_SESSION['flash_message_type'] = "error";
        }
    } catch (PDOException $e) {
        error_log("Admin Panel Opinion Action Error: " . $e->getMessage());
        $_SESSION['flash_message'] = "Wystąpił błąd bazy danych podczas przetwarzania akcji dla opinii ID: $opinion_id. Szczegóły: " . $e->getMessage();
        $_SESSION['flash_message_type'] = "error";
    }
    header("Location: index.php?table=opinions&opinion_status_filter=" . urlencode($_POST['current_filter_status'] ?? 'Oczekująca'));
    exit();
} else {
    $_SESSION['flash_message'] = "Nieprawidłowe żądanie.";
    $_SESSION['flash_message_type'] = "error";
    header("Location: index.php?table=opinions");
    exit();
}
?>