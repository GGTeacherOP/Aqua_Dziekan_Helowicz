<?php
require_once __DIR__ . '/config/init.php'; //

$page_title = "Opinie Klientów - AquaParadise";
define('INITIAL_OPINIONS_COUNT', 5); // Ile opinii załadować na start

$opinions = [];
$has_more_initial_opinions = false;

try {
    // Zapytanie o początkową partię opinii
    $stmt = $pdo->prepare("
        SELECT o.*, u.first_name, u.last_name, p.name as product_name
        FROM opinions o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN products p ON o.product_id = p.product_id
        WHERE o.status = 'Zaakceptowana'
        ORDER BY o.created_at DESC
        LIMIT :limit 
    "); //
    $limit = INITIAL_OPINIONS_COUNT;
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $opinions = $stmt->fetchAll(PDO::FETCH_ASSOC); //

    // Sprawdzenie, czy jest więcej opinii niż początkowa partia
    $stmt_count_total = $pdo->prepare("SELECT COUNT(*) FROM opinions WHERE status = 'Zaakceptowana'");
    $stmt_count_total->execute();
    $total_opinions_count = (int)$stmt_count_total->fetchColumn();
    if ($total_opinions_count > INITIAL_OPINIONS_COUNT) {
        $has_more_initial_opinions = true;
    }

} catch (PDOException $e) {
    error_log("Error fetching opinions: " . $e->getMessage());
}

include BASE_PATH . '/includes/header.php';
?>

<header class="page-header" style="background-image: linear-gradient(135deg, rgba(0, 123, 255, 0.7) 0%, rgba(0, 86, 179, 0.85) 100%)">
    <div class="content">
        <h1>Opinie Naszych Gości</h1>
        <p>Zobacz, co mówią o nas nasi klienci i podziel się swoją opinią!</p>
    </div>
</header>

<?php include BASE_PATH . '/includes/navigation.php'; ?>

<div class="page-wrapper opinions-page-wrapper-new">
    <section class="section-title-container">
        <h2>Co Mówią Nasi Goście?</h2>
    </section>

    <?php if (function_exists('display_flash_message')) { display_flash_message(); } ?>

    <div id="opinionsListContainer" class="opinions-list-new">
        <?php if (empty($opinions)): ?>
            <p class="no-opinions-new">Nie ma jeszcze żadnych opinii. Bądź pierwszy!</p>
        <?php else: ?>
            <?php foreach ($opinions as $opinion): ?>
                <div class="opinion-card-new">
                    <div class="opinion-header">
                        <strong class="opinion-author">
                            <i class="fas fa-user"></i>
                            <?php
                            if (!empty($opinion['user_id']) && !empty($opinion['first_name'])) { //
                                echo htmlspecialchars($opinion['first_name'] . (isset($opinion['last_name'][0]) ? ' ' . $opinion['last_name'][0] . '.' : '')); //
                            } elseif (!empty($opinion['guest_name'])) { //
                                echo htmlspecialchars($opinion['guest_name']); //
                            } else {
                                echo 'Anonimowy Gość'; //
                            }
                            ?>
                        </strong>
                        <span class="opinion-date"><?php echo date("d.m.Y", strtotime($opinion['created_at'])); ?></span>
                    </div>
                    <?php if ($opinion['rating']): ?>
                        <div class="opinion-rating-display">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo ($i <= $opinion['rating']) ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($opinion['product_name']): ?>
                        <p class="opinion-product">Dotyczy: <strong><?php echo htmlspecialchars($opinion['product_name']); ?></strong></p>
                    <?php endif; ?>
                    <p class="opinion-comment-text">"<?php echo nl2br(htmlspecialchars($opinion['comment'])); ?>"</p>
                    <?php if (!empty($opinion['admin_comment'])): ?>
                        <div class="admin-reply">
                            <p><strong><i class="fas fa-headset"></i> Odpowiedź AquaParadise:</strong></p>
                            <p>"<?php echo nl2br(htmlspecialchars($opinion['admin_comment'])); ?>"</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($has_more_initial_opinions): ?>
        <div class="show-more-opinions-container">
            <button id="loadMoreOpinionsBtn" class="cta-button secondary-cta">
                <i class="fas fa-chevron-down"></i> Pokaż więcej opinii
            </button>
            <p id="loadingOpinionsIndicator" style="display:none; text-align:center; margin-top:10px;"><i class="fas fa-spinner fa-spin"></i> Ładowanie...</p>
        </div>
    <?php endif; ?>

    <div class="add-opinion-section-new">
        <button id="toggleAddOpinionFormBtn" class="cta-button">
            <i class="fas fa-plus-circle"></i> Dodaj swoją opinię
        </button>
        <div id="addOpinionFormContainer" class="form-container opinion-form-container-new" style="display:none;">
            <section class="section-title-container add-opinion-title-new">
                <h2>Podziel się wrażeniami</h2>
            </section>
            <form id="newOpinionForm" action="<?php echo BASE_URL; ?>submit_opinion.php" method="POST">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="form-group">
                    <label for="guest_name"><i class="fas fa-user-edit"></i> Twoje Imię (lub pseudonim):</label>
                    <input type="text" id="guest_name" name="guest_name" required>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="rating-form-label"><i class="fas fa-star-half-alt"></i> Twoja Ocena (1-5):</label>
                    <div class="rating-stars-form">
                        <input type="radio" id="star5-form" name="rating" value="5" required/><label class="star-label" for="star5-form" title="5 gwiazdek"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star4-form" name="rating" value="4" required/><label class="star-label" for="star4-form" title="4 gwiazdki"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star3-form" name="rating" value="3" required/><label class="star-label" for="star3-form" title="3 gwiazdki"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star2-form" name="rating" value="2" required/><label class="star-label" for="star2-form" title="2 gwiazdki"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star1-form" name="rating" value="1" required/><label class="star-label" for="star1-form" title="1 gwiazdka"><i class="fas fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment"><i class="fas fa-comment-dots"></i> Twój Komentarz:</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>
                <input type="hidden" name="product_id" value=""> <button type="submit" class="cta-button form-submit-button">Wyślij Opinię</button>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>