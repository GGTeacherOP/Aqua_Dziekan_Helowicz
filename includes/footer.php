<?php
// includes/footer.php
// Upewnij się, że ścieżki do map Google i linki mailto/tel są poprawne.
// Adres i dane kontaktowe mogą być również pobierane z konfiguracji lub bazy danych.
$contact_address = "ul. Słoneczne Wybrzeże 7, <br>00-123 Rajskie Miasto";
$contact_phone = "+48 500 100 200";
$contact_email = "kontakt@aquaparadise.pl";
$Maps_link = "https://maps.google.com/?q=Rajskie+Miasto,+Słoneczne+Wybrzeże+7"; // Przykładowy link
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
                    <span><a href="<?php echo e($Maps_link); ?>" target="_blank" rel="noopener noreferrer"><?php echo $contact_address; ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span><a href="tel:<?php echo e(str_replace(' ', '', $contact_phone)); ?>"><?php echo e($contact_phone); ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span><a href="mailto:<?php echo e($contact_email); ?>"><?php echo e($contact_email); ?></a></span>
                </div>
            </div>
        </div>
        <div class="footer-social-icons">
            <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="X (Twitter)" target="_blank" rel="noopener noreferrer"><i class="fab fa-x-twitter"></i></a>
        </div>
        <p>© <?php echo date("Y"); ?> AquaParadise. Wszelkie prawa zastrzeżone. <a href="regulamin.php" style="color: var(--primary-light-color); text-decoration: underline;">Regulamin</a></p>
    </div>
</footer>

<?php include BASE_PATH . '/includes/modals.php'; // Modal logowania/gościa (jeśli nie jest już w header) ?>
<script src="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>script.js"></script>
</body>
</html>