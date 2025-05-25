<?php
require_once __DIR__ . '/config/init.php';
$page_title = "Ustaw Nowe Hasło - AquaParadise";
$error_message_rp = '';
$success_message_rp = '';
$token_valid = false;
$token_from_url = $_GET['token'] ?? null;
$email_from_url = $_GET['email'] ?? null;

if ($token_from_url && $email_from_url) {
    try {
        $stmt_check_token = $pdo->prepare("SELECT * FROM password_resets WHERE email = :email AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt_check_token->execute([':email' => $email_from_url]);
        $reset_request = $stmt_check_token->fetch();

        if ($reset_request && password_verify($token_from_url, $reset_request['token'])) {
            $token_valid = true;
        } else {
            $error_message_rp = "Link do resetowania hasła jest nieprawidłowy, wygasł lub został już użyty.";
        }
    } catch (PDOException $e) {
        error_log("Reset Password Token Check Error: " . $e->getMessage());
        $error_message_rp = "Wystąpił błąd serwera podczas weryfikacji tokenu.";
    }
} else {
    $error_message_rp = "Brak tokenu lub adresu email w żądaniu.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'], $_POST['token_rp'], $_POST['email_rp'])) {
    if (!$token_valid && $token_from_url !== $_POST['token_rp']) { // Additional check if previous validation failed
        $error_message_rp = "Sesja resetowania hasła wygasła lub token jest nieprawidłowy. Spróbuj ponownie.";
    } else {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $email_rp = $_POST['email_rp'];

        if (empty($new_password) || strlen($new_password) < 8) {
            $error_message_rp = "Hasło musi mieć co najmniej 8 znaków.";
        } elseif ($new_password !== $confirm_password) {
            $error_message_rp = "Hasła nie są identyczne.";
        } else {
            try {
                // Update user's password (plain text as per signup.php logic)
                $new_password_to_store = $new_password; 

                $stmt_update_pass = $pdo->prepare("UPDATE Users SET password_hash = :password_hash WHERE email = :email");
                $stmt_update_pass->execute([':password_hash' => $new_password_to_store, ':email' => $email_rp]);

                // Invalidate the token (delete it)
                $stmt_delete_token = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
                $stmt_delete_token->execute([':email' => $email_rp]);

                $success_message_rp = "Twoje hasło zostało pomyślnie zmienione! Możesz się teraz zalogować.";
                $token_valid = false; // Prevent form resubmission by hiding it
            } catch (PDOException $e) {
                error_log("Reset Password Update Error: " . $e->getMessage());
                $error_message_rp = "Wystąpił błąd serwera podczas aktualizacji hasła.";
            }
        }
    }
}


include BASE_PATH . '/includes/header.php';
?>

<div class="auth-page-container">
     <div class="return-button-container">
        <a href="<?php echo BASE_URL; ?>login.php" class="return-button"><i class="fas fa-arrow-left"></i> Powrót do logowania</a>
    </div>
    <div class="form-container-wrapper">
        <div class="form-container auth-form">
            <h2>Ustaw Nowe Hasło</h2>

            <?php if ($error_message_rp): ?>
                <div class="flash-message error"><?php echo e($error_message_rp); ?></div>
            <?php endif; ?>
            <?php if ($success_message_rp): ?>
                <div class="flash-message success"><?php echo e($success_message_rp); ?></div>
            <?php endif; ?>

            <?php if ($token_valid && empty($success_message_rp)): ?>
            <form action="reset_password.php?token=<?php echo e($token_from_url); ?>&email=<?php echo e($email_from_url); ?>" method="POST">
                <input type="hidden" name="token_rp" value="<?php echo e($token_from_url); ?>">
                <input type="hidden" name="email_rp" value="<?php echo e($email_from_url); ?>">
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> Nowe Hasło:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-redo-alt"></i> Potwierdź Nowe Hasło:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="cta-button form-submit-button">Zmień Hasło</button>
            </form>
            <?php elseif (empty($success_message_rp) && !$token_valid && empty($error_message_rp)): ?>
                 <p class="flash-message info">Weryfikowanie linku...</p> 
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>