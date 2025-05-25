<?php
require_once __DIR__ . '/config/init.php';

header('Content-Type: application/json'); // Zmieniamy na JSON dla lepszej obsługi danych

$opinions_per_page = 5; // Ile opinii ładować za każdym razem
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $opinions_per_page;

$opinions_html_array = [];
$has_more_opinions = false;

try {
    // Zapytanie o opinie dla bieżącej strony
    $stmt = $pdo->prepare("
        SELECT o.*, u.first_name, u.last_name, p.name as product_name
        FROM opinions o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN products p ON o.product_id = p.product_id
        WHERE o.status = 'Zaakceptowana'
        ORDER BY o.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $opinions_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $opinions_batch = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($opinions_batch as $opinion) {
        $author_name = 'Anonimowy Gość';
        if (!empty($opinion['user_id']) && !empty($opinion['first_name'])) {
            $author_name = htmlspecialchars($opinion['first_name'] . (isset($opinion['last_name'][0]) ? ' ' . $opinion['last_name'][0] . '.' : ''));
        } elseif (!empty($opinion['guest_name'])) {
            $author_name = htmlspecialchars($opinion['guest_name']);
        }

        $rating_html = '';
        if ($opinion['rating']) {
            $rating_html .= '<div class="opinion-rating-display">';
            for ($i = 1; $i <= 5; $i++) {
                $filled_class = ($i <= $opinion['rating']) ? 'filled' : '';
                $rating_html .= '<i class="fas fa-star ' . $filled_class . '"></i>';
            }
            $rating_html .= '</div>';
        }

        $product_html = '';
        if ($opinion['product_name']) {
            $product_html = '<p class="opinion-product">Dotyczy: <strong>' . htmlspecialchars($opinion['product_name']) . '</strong></p>';
        }

        $admin_reply_html = '';
        if (!empty($opinion['admin_comment'])) {
            $admin_reply_html = '
                <div class="admin-reply">
                    <p><strong><i class="fas fa-headset"></i> Odpowiedź AquaParadise:</strong></p>
                    <p>"' . nl2br(htmlspecialchars($opinion['admin_comment'])) . '"</p>
                </div>';
        }

        // Tworzenie HTML dla pojedynczej opinii - można też rozważyć zwracanie JSON i budowanie HTML po stronie klienta
        $opinions_html_array[] = '
            <div class="opinion-card-new">
                <div class="opinion-header">
                    <strong class="opinion-author"><i class="fas fa-user"></i> ' . $author_name . '</strong>
                    <span class="opinion-date">' . date("d.m.Y", strtotime($opinion['created_at'])) . '</span>
                </div>
                ' . $rating_html . '
                ' . $product_html . '
                <p class="opinion-comment-text">"' . nl2br(htmlspecialchars($opinion['comment'])) . '"</p>
                ' . $admin_reply_html . '
            </div>';
    }

    // Sprawdzenie, czy jest więcej opinii do załadowania
    $stmt_count_more = $pdo->prepare("
        SELECT COUNT(*) 
        FROM opinions 
        WHERE status = 'Zaakceptowana' 
        LIMIT 1 OFFSET :next_offset
    ");
    $next_offset = $page * $opinions_per_page;
    $stmt_count_more->bindParam(':next_offset', $next_offset, PDO::PARAM_INT);
    $stmt_count_more->execute();
    if ($stmt_count_more->fetchColumn() > 0) {
        $has_more_opinions = true;
    }

    echo json_encode(['success' => true, 'opinions_html' => $opinions_html_array, 'has_more' => $has_more_opinions]);

} catch (PDOException $e) {
    error_log("Error fetching more opinions: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Błąd podczas ładowania opinii.']);
}
?>