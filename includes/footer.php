<?php
// includes/footer.php
$contact_address_display = "ul. Słoneczne Wybrzeże 7, <br>00-123 Rajskie Miasto";
$contact_address_map = "ul. Słoneczne Wybrzeże 7, 00-123 Rajskie Miasto"; // For map query
$contact_phone = "+48 500 100 200";
$contact_email = "kontakt@aquaparadise.pl"; //

$terms_link = (defined('BASE_URL') ? BASE_URL : '') . 'regulamin.php';
// Corrected Google Maps link
$maps_link = "https://www.google.com/maps/search/?api=1&query=" . urlencode(strip_tags($contact_address_map));

?>
<footer class="main-footer">
    <div class="footer-inner-content">
        <div class="footer-content-columns">
            <div>
                <h4>O AquaParadise</h4>
                <p>Twoja wymarzona destynacja pełna wodnych przygód i luksusowego relaksu. Stwórz niezapomniane wspomnienia z nami.</p>
            </div>
            <div>
                <h4>Godziny Otwarcia Kompleksu</h4>
                <p><strong>Poniedziałek - Piątek:</strong> 09:00 - 22:00</p>
                <p><strong>Sobota - Niedziela:</strong> 08:00 - 23:00</p>
            </div>
            <div>
                <h4>Skontaktuj się z Nami</h4>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><a href="<?php echo htmlspecialchars($maps_link, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo $contact_address_display; ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span><a href="tel:<?php echo htmlspecialchars(str_replace([' ', '(', ')', '-'], '', $contact_phone), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($contact_phone, ENT_QUOTES, 'UTF-8'); ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span><a href="mailto:<?php echo htmlspecialchars($contact_email, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($contact_email, ENT_QUOTES, 'UTF-8'); ?></a></span>
                </div>
            </div>
        </div>
        <div class="footer-social-icons">
            <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="X (Twitter)" target="_blank" rel="noopener noreferrer"><i class="fab fa-x-twitter"></i></a>
        </div>
        <p>© <?php echo date("Y"); ?> AquaParadise. Wszelkie prawa zastrzeżone. <a href="<?php echo htmlspecialchars($terms_link, ENT_QUOTES, 'UTF-8'); ?>" style="color: var(--primary-light-color); text-decoration: underline;">Regulamin</a></p>
    </div>
</footer>

<?php
// Modal logowania/gościa (authGuestModalContainer)
if (file_exists(__DIR__ . '/modals.php')) {
    include __DIR__ . '/modals.php';
}
?>
<script src="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>script.js?v=<?php echo time(); ?>"></script>
</body>
</html>