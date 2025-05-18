<?php
// logout.php
require_once __DIR__ . '/config/init.php'; // Dla session_start()

// Zniszcz wszystkie zmienne sesyjne.
$_SESSION = array();

// Jeśli używane są ciasteczka sesyjne, usuń je (zalecane).
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Na koniec zniszcz sesję.
session_destroy();

// Przekieruj na stronę główną z parametrem informującym o pomyślnym wylogowaniu
header("Location: index.php?logout_status=success");
exit;
?>