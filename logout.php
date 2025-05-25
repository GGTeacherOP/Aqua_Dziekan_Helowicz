<?php
// logout.php
require_once __DIR__ . '/config/init.php'; // Dla session_start()

// 1. Zniszcz wszystkie zmienne sesyjne.
$_SESSION = array();

// 2. Jeśli używane są ciasteczka sesyjne (standardowo), usuń również ciasteczko sesji.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Na koniec zniszcz sesję.
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Przekieruj na stronę główną z parametrem informującym o pomyślnym wylogowaniu
header("Location: index.php?logout_status=success");
exit;
?>