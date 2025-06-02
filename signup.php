<?php
// signup.php
require_once __DIR__ . '/config/init.php';

$page_title = "Rejestracja - AquaParadise";

$error_message = '';
// $success_message = ''; // Komunikat sukcesu jest teraz przekazywany przez $_SESSION['flash_message'] po przekierowaniu

$redirect_url_signup = $_GET['redirect'] ?? 'index.php';
$action_after_signup = $_GET['action'] ?? null;
$product_id_after_signup = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
$details_after_signup_json = $_GET['details'] ?? null;


if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Hasło w czystym tekście z formularza
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $terms_agree = isset($_POST['terms_agree']);

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Proszę wypełnić wszystkie wymagane pola.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Nieprawidłowy format adresu email.";
    } elseif (strlen($password) < 8) { 
        $error_message = "Hasło musi mieć co najmniej 8 znaków.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Hasła nie są identyczne.";
    } elseif (!$terms_agree) {
        $error_message = "Musisz zaakceptować regulamin serwisu.";
    } else {
        try {
            $stmt_check = $pdo->prepare("SELECT user_id FROM Users WHERE email = :email");
            $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_check->execute();
            if ($stmt_check->fetch()) {
                $error_message = "Użytkownik o podanym adresie email już istnieje.";
            } else {
                $password_to_store = $password; // Zapis czystego tekstu hasła
                
                $stmt_role_id = $pdo->prepare("SELECT role_id FROM Roles WHERE role_name = 'Klient'");
                $stmt_role_id->execute();
                $role_data = $stmt_role_id->fetch();
                $default_role_id = $role_data ? $role_data['role_id'] : null;

                if (!$default_role_id) {
                    error_log("Krytyczny błąd: Domyślna rola 'Klient' nie znaleziona w bazie danych.");
                    $error_message = "Wystąpił błąd systemu podczas rejestracji. Prosimy spróbować później.";
                } else {
                    $stmt_insert = $pdo->prepare("INSERT INTO Users (first_name, last_name, email, password_hash, phone, role_id) 
                                                 VALUES (:firstname, :lastname, :email, :password_to_store, :phone, :role_id)");
                    $stmt_insert->bindParam(':firstname', $firstname, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':lastname', $lastname, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':password_to_store', $password_to_store, PDO::PARAM_STR);
                    $phone_to_insert = !empty($phone) ? $phone : null;
                    $stmt_insert->bindParam(':phone', $phone_to_insert, $phone_to_insert === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                    $stmt_insert->bindParam(':role_id', $default_role_id, PDO::PARAM_INT);
                    
                    if ($stmt_insert->execute()) {
                        $new_user_id = $pdo->lastInsertId();
                        
                        // Automatyczne zalogowanie po rejestracji
                        $guest_php_session_id_before_regenerate_signup = session_id(); // Pobierz ID sesji gościa PRZED regeneracją

                        session_regenerate_id(true); // Zregeneruj ID sesji dla nowego, zalogowanego użytkownika

                        $_SESSION['user_id'] = $new_user_id;
                        $_SESSION['user_first_name'] = $firstname;
                        $_SESSION['user_last_name'] = $lastname; 
                        $_SESSION['user_email'] = $email;       
                        $_SESSION['user_role_id'] = (int)$default_role_id; 
                        
                        $stmt_role_fetch = $pdo->prepare("SELECT role_name FROM Roles WHERE role_id = :role_id");
                        $stmt_role_fetch->execute([':role_id' => $default_role_id]);
                        $role_data_fetch = $stmt_role_fetch->fetch();
                        $_SESSION['user_role_name'] = $role_data_fetch ? $role_data_fetch['role_name'] : 'Klient';


                        // --- START: Logika scalania koszyka po rejestracji ---
                        $stmt_guest_cart_signup = $pdo->prepare("SELECT cart_id FROM Carts WHERE session_id = :session_id AND user_id IS NULL");
                        $stmt_guest_cart_signup->bindParam(':session_id', $guest_php_session_id_before_regenerate_signup, PDO::PARAM_STR);
                        $stmt_guest_cart_signup->execute();
                        $guest_cart_data_signup = $stmt_guest_cart_signup->fetch();

                        if ($guest_cart_data_signup) {
                            $guest_cart_id_signup = $guest_cart_data_signup['cart_id'];
                            // Nowo zarejestrowany użytkownik na pewno nie ma jeszcze swojego koszyka powiązanego z user_id,
                            // więc po prostu przypisujemy mu koszyk gościa.
                            $stmt_assign_signup = $pdo->prepare("UPDATE Carts SET user_id = :user_id, session_id = NULL WHERE cart_id = :guest_cart_id");
                            $stmt_assign_signup->execute([':user_id' => $new_user_id, ':guest_cart_id' => $guest_cart_id_signup]);
                        }
                        unset($_SESSION['guest_cart_id']); 
                        // --- END: Logika scalania koszyka po rejestracji ---


                        if ($action_after_signup === 'add_to_cart_after_register' && $product_id_after_signup) {
                            $params_array_signup = [
                                'action' => 'add_to_cart',
                                'product_id' => $product_id_after_signup,
                                'quantity' => 1, // Domyślna ilość, można dostosować
                            ];
                            if ($details_after_signup_json) {
                                 $params_array_signup['item_details_json_string'] = $details_after_signup_json;
                            }
                            $query_params_signup = http_build_query($params_array_signup);
                            header("Location: cart_actions.php?" . $query_params_signup);
                            exit;
                        }
                        
                        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Rejestracja zakończona sukcesem! Zostałeś automatycznie zalogowany.'];
                        header("Location: " . ($redirect_url_signup ?: 'index.php')); 
                        exit;
                    } else {
                        $error_message = "Rejestracja nie powiodła się. Spróbuj ponownie.";
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Błąd rejestracji: " . $e->getMessage());
            $error_message = "Wystąpił błąd serwera podczas rejestracji. Spróbuj ponownie później.";
        }
    }
}

$form_action_params_signup = [];
if (isset($_GET['redirect'])) $form_action_params_signup['redirect'] = $_GET['redirect'];
if (isset($_GET['action'])) $form_action_params_signup['action'] = $_GET['action'];
if (isset($_GET['product_id'])) $form_action_params_signup['product_id'] = $_GET['product_id'];
if (isset($_GET['details'])) $form_action_params_signup['details'] = $_GET['details'];
$form_action_url_signup = "signup.php" . (!empty($form_action_params_signup) ? "?" . http_build_query($form_action_params_signup) : "");
$login_link_params = http_build_query($form_action_params_signup); 

include BASE_PATH . '/includes/header.php';
?>
<title><?php echo e($page_title); ?></title>

<div class="auth-page-container">
    <div class="return-button-container">
        <a href="index.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do strony głównej</a>
    </div>

    <div class="form-container-wrapper">
        <div class="form-container auth-form">
            <h2>Rejestracja</h2>

            <?php if ($error_message): ?>
                <div class="flash-message error" style="padding: 10px; margin-bottom:15px; border: 1px solid var(--border-color); border-left-width: 5px; border-left-color: red; background-color: #fdd;"><?php echo e($error_message); ?></div>
            <?php endif; ?>

            <form action="<?php echo e($form_action_url_signup); ?>" method="POST" id="signupForm">
                <div class="form-group">
                    <label for="register-firstname"><i class="fas fa-user"></i> Imię</label>
                    <input type="text" id="register-firstname" name="firstname" value="<?php echo e($_POST['firstname'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-lastname"><i class="fas fa-user-tag"></i> Nazwisko</label>
                    <input type="text" id="register-lastname" name="lastname" value="<?php echo e($_POST['lastname'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-email"><i class="fas fa-envelope"></i> Adres Email</label>
                    <input type="email" id="register-email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-password"><i class="fas fa-lock"></i> Hasło</label>
                    <input type="password" id="register-password" name="password" required>
                    </div>
                <div class="form-group">
                    <label for="register-confirm-password"><i class="fas fa-redo-alt"></i> Potwierdź Hasło</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="register-phone"><i class="fas fa-phone"></i> Numer Telefonu (opcjonalnie)</label>
                    <input type="tel" id="register-phone" name="phone" value="<?php echo e($_POST['phone'] ?? ''); ?>">
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms_agree" required <?php echo (isset($_POST['terms_agree']) ? 'checked' : ''); ?>>
                        Akceptuję <a href="regulamin.php" target="_blank" class="form-link">regulamin serwisu</a>*
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="newsletter_agree" <?php echo (isset($_POST['newsletter_agree']) ? 'checked' : ''); ?>>
                        Chcę otrzymywać informacje o promocjach i nowościach (newsletter)
                    </label>
                </div>
                <button type="submit" class="cta-button form-submit-button">Zarejestruj się</button>
            </form>
            <p class="form-switch">Masz już konto? <a href="login.php<?php echo (!empty($login_link_params) ? "?" . $login_link_params : ""); ?>" class="form-link">Zaloguj się!</a></p>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>