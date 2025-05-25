<?php
require_once __DIR__ . '/config/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $guest_name = trim($_POST['guest_name'] ?? null);
    // Product ID is now optional / general opinion if empty
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
    $comment = trim($_POST['comment'] ?? '');

    // Basic validation
    if (empty($comment)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Komentarz nie może być pusty.'];
        header('Location: opinions_view.php');
        exit;
    }
    if ($rating !== null && ($rating < 1 || $rating > 5)) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowa ocena.'];
        header('Location: opinions_view.php');
        exit;
    }
    if (!$user_id && empty($guest_name)) {
         $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Musisz podać imię, jeśli nie jesteś zalogowany.'];
        header('Location: opinions_view.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO opinions (user_id, guest_name, product_id, rating, comment, status, created_at)
            VALUES (:user_id, :guest_name, :product_id, :rating, :comment, 'Oczekująca', NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':guest_name' => $user_id ? null : $guest_name,
            ':product_id' => $product_id, 
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Dziękujemy za Twoją opinię! Zostanie opublikowana po weryfikacji.'];
    } catch (PDOException $e) {
        error_log("Error submitting opinion: " . $e->getMessage());
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Wystąpił błąd podczas przesyłania opinii. Spróbuj ponownie.'];
    }
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Nieprawidłowe żądanie.'];
}

header('Location: opinions_view.php');
exit;
?>