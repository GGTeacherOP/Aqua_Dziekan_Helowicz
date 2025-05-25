<?php
require_once __DIR__ . '/config/init.php';
$page_title = "Resetowanie Hasła - AquaParadise";
$error_message_fp = '';
$success_message_fp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_fp'])) {
    $email = trim($_POST['email_fp']);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message_fp = "Proszę podać prawidłowy adres email.";
    } else {
        try {
            $stmt_user_exists = $pdo->prepare("SELECT user_id FROM Users WHERE email = :email");
            $stmt_user_exists->execute([':email' => $email]);
            $user = $stmt_user_exists->fetch();

            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

                // Store token in the database
                $stmt_insert_token = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                $stmt_insert_token->execute([':email' => $email, ':token' => password_hash($token, PASSWORD_DEFAULT), ':expires_at' => $expires_at]);

                // Simulate sending email
                $reset_link = BASE_URL . "reset_password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
                $success_message_fp = "Jeśli konto z podanym adresem email istnieje, wysłaliśmy link do resetowania hasła. Sprawdź swoją skrzynkę odbiorczą (również folder spam).";
                // For development, you can display the link:
                // $success_message_fp .= "<br><small>Link deweloperski: <a href='$reset_link'>$reset_link</a></small>";
                 error_log("Password reset link for $email: $reset_link");


            } else {
                // Show generic message even if user does not exist to prevent email enumeration
                $success_message_fp = "Jeśli konto z podanym adresem email istnieje, wysłaliśmy link do resetowania hasła. Sprawdź swoją skrzynkę odbiorczą (również folder spam).";
            }
        } catch (PDOException $e) {
            error_log("Forgot Password Error: " . $e->getMessage());
            $error_message_fp = "Wystąpił błąd serwera. Spróbuj ponownie później.";
        } catch (Exception $e) {
            error_log("Token Generation Error: " . $e->getMessage());
            $error_message_fp = "Wystąpił błąd podczas generowania tokenu. Spróbuj ponownie później.";
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
            <h2>Nie Pamiętam Hasła</h2>
            <p style="text-align: center; margin-bottom: 15px; font-size:0.9em; color: var(--text-muted-color);">Podaj adres email powiązany z Twoim kontem, a wyślemy Ci instrukcję resetowania hasła.</p>

            <?php if ($error_message_fp): ?>
                <div class="flash-message error"><?php echo e($error_message_fp); ?></div>
            <?php endif; ?>
            <?php if ($success_message_fp): ?>
                <div class="flash-message success"><?php echo $success_message_fp; /* Already e-scaped or contains HTML */ ?></div>
            <?php endif; ?>

            <?php if (empty($success_message_fp)): // Hide form after success message ?>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email_fp"><i class="fas fa-envelope"></i> Adres Email:</label>
                    <input type="email" id="email_fp" name="email_fp" required>
                </div>
                <button type="submit" class="cta-button form-submit-button">Wyślij Link Resetujący</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>