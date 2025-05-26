<?php
// login.php
require_once __DIR__ . '/config/init.php'; 

$page_title = "Logowanie - AquaParadise"; 

$error_message = '';
$success_message = ''; 

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$redirect_url = $_GET['redirect'] ?? 'index.php'; 
$action_after_login = $_GET['action'] ?? null;
$product_id_after_login = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$details_after_login_json = $_GET['details'] ?? null; 

if (isset($_SESSION['user_id'])) {
    header("Location: " . $redirect_url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_login = trim($_POST['email_login'] ?? '');
    $password_input = $_POST['password'] ?? ''; 

    if (empty($email_login) || empty($password_input)) {
        $error_message = "Proszę wypełnić wszystkie pola.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT u.user_id, u.first_name, u.last_name, u.email, u.password_hash, r.role_name, r.role_id
                                   FROM Users u
                                   JOIN Roles r ON u.role_id = r.role_id
                                   WHERE u.email = :email_login");
            $stmt->bindParam(':email_login', $email_login, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && $password_input === $user['password_hash']) { // Porównanie hasła w czystym tekście
                
                $guest_php_session_id_before_regenerate = session_id(); // Pobierz ID sesji gościa PRZED regeneracją

                session_regenerate_id(true); 

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name']; 
                $_SESSION['user_email'] = $user['email'];       
                $_SESSION['user_role_id'] = (int)$user['role_id']; 
                $_SESSION['user_role_name'] = $user['role_name'];

                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Pomyślnie zalogowano. Witaj, ' . htmlspecialchars($user['first_name']) . '!'];
                
                // Przeniesienie koszyka gościa (jeśli istnieje)
                // Używamy $guest_php_session_id_before_regenerate
                $stmt_guest_cart = $pdo->prepare("SELECT cart_id FROM Carts WHERE session_id = :session_id AND user_id IS NULL");
                $stmt_guest_cart->bindParam(':session_id', $guest_php_session_id_before_regenerate, PDO::PARAM_STR);
                $stmt_guest_cart->execute();
                $guest_cart_data = $stmt_guest_cart->fetch();

                if ($guest_cart_data) {
                    $guest_cart_id = $guest_cart_data['cart_id'];
                    $stmt_user_cart = $pdo->prepare("SELECT cart_id FROM Carts WHERE user_id = :user_id");
                    $stmt_user_cart->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                    $stmt_user_cart->execute();
                    $user_cart_data = $stmt_user_cart->fetch();

                    if ($user_cart_data) { 
                        $user_db_cart_id = $user_cart_data['cart_id'];
                        if ($user_db_cart_id != $guest_cart_id) { 
                            $stmt_merge = $pdo->prepare("UPDATE CartItems SET cart_id = :user_cart_id WHERE cart_id = :guest_cart_id");
                            $stmt_merge->execute([':user_cart_id' => $user_db_cart_id, ':guest_cart_id' => $guest_cart_id]);
                            $pdo->prepare("DELETE FROM Carts WHERE cart_id = :guest_cart_id")->execute([':guest_cart_id' => $guest_cart_id]);
                        }
                    } else { 
                        $stmt_assign = $pdo->prepare("UPDATE Carts SET user_id = :user_id, session_id = NULL WHERE cart_id = :guest_cart_id");
                        $stmt_assign->execute([':user_id' => $user['user_id'], ':guest_cart_id' => $guest_cart_id]);
                    }
                }
                // Usunięcie specyficznych zmiennych sesyjnych, jeśli były używane do śledzenia koszyka gościa
                unset($_SESSION['guest_cart_id']); // Jeśli używałeś tej zmiennej
                // unset($_SESSION['guest_cart_session_id']); // Jeśli używałeś tej zmiennej
                // unset($_SESSION['guest_cart_assigned_db_id']); // Jeśli używałeś tej zmiennej


                $pdo->prepare("UPDATE Users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?")->execute([$user['user_id']]);

                if ($action_after_login === 'add_to_cart_after_login' && $product_id_after_login) {
                    $params_array = [
                        'action' => 'add_to_cart',
                        'product_id' => $product_id_after_login,
                        'quantity' => 1,
                    ];
                    if ($details_after_login_json) {
                        $params_array['item_details_json_string'] = $details_after_login_json;
                    }
                    $query_params = http_build_query($params_array);
                    header("Location: cart_actions.php?" . $query_params);
                    exit;
                }

                if (in_array($user['role_name'], ['Administrator', 'SuperAdmin', 'Pracownik Hotel', 'Pracownik SPA', 'Pracownik Restauracja', 'Pracownik Aquapark'])) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: " . $redirect_url);
                }
                exit;

            } else {
                $error_message = "Nieprawidłowy email lub hasło.";
            }
        } catch (PDOException $e) {
            error_log("Błąd logowania: " . $e->getMessage());
            $error_message = "Wystąpił błąd serwera. Spróbuj ponownie później.";
        }
    }
}

$form_action_params = [];
if (isset($_GET['redirect'])) $form_action_params['redirect'] = $_GET['redirect'];
if (isset($_GET['action'])) $form_action_params['action'] = $_GET['action'];
if (isset($_GET['product_id'])) $form_action_params['product_id'] = $_GET['product_id'];
if (isset($_GET['details'])) $form_action_params['details'] = $_GET['details'];
$form_action_url = "login.php" . (!empty($form_action_params) ? "?" . http_build_query($form_action_params) : "");

include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title> 
<div class="auth-page-container">
    <div class="return-button-container">
        <a href="index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form">
            <h2>Logowanie</h2>

            <?php if ($error_message): ?>
                <div class="flash-message error" style="padding: 10px; margin-bottom:15px; border: 1px solid var(--border-color); border-left-width: 5px; border-left-color: red; background-color: #fdd;"><?php echo e($error_message); ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="flash-message success" style="padding: 10px; margin-bottom:15px; border: 1px solid var(--border-color); border-left-width: 5px; border-left-color: green; background-color: #dfd;"><?php echo e($success_message); ?></div>
            <?php endif; ?>

            <form action="<?php echo e($form_action_url); ?>" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="login-email"><i class="fas fa-envelope"></i> Adres Email</label>
                    <input type="text" id="login-email" name="email_login" value="<?php echo e($_POST['email_login'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="login-password"><i class="fas fa-lock"></i> Hasło</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <div class="form-options">
                    <span></span> 
                    <a href="forgot_password.php" class="form-link">Nie pamiętam hasła</a>
                </div>
                <button type="submit" class="cta-button form-submit-button">Zaloguj się</button>
            </form>
            <p class="form-switch">Nie masz jeszcze konta? <a href="signup.php<?php echo (!empty($form_action_params) ? "?" . http_build_query($form_action_params) : ""); ?>" class="form-link">Zarejestruj się tutaj!</a></p>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>