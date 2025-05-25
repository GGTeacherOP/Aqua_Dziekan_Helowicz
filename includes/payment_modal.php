<?php
// includes/payment_modal.php
// Ten plik jest dołączany w cart_view.php

// Dostęp do $current_user_first_name, $current_user_last_name, $current_user_email z sesji dla zalogowanych
// Te zmienne są przekazywane do JavaScript w header.php jako currentUserFirstNameFromPHP etc.
// i używane w script.js do pre-wypełnienia pól, jeśli użytkownik jest zalogowany.
$user_full_name_modal = '';
$user_email_modal = '';
if (isset($_SESSION['user_id']) && isset($_SESSION['user_first_name'])) {
    $user_full_name_modal = trim(($_SESSION['user_first_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? ''));
    $user_email_modal = $_SESSION['user_email'] ?? '';
}
?>
<div id="paymentCheckoutModal" class="modal payment-checkout-modal">
    <div class="modal-content payment-modal-content">
        <span class="close-button" id="closePaymentCheckoutModal">&times;</span>
        <h3><i class="fas fa-credit-card"></i> Podsumowanie i Płatność</h3>
        
        <div id="paymentResponseMessage" class="flash-message" style="display:none;"></div>

        <form id="checkoutForm" method="POST">
            <h4>Wybierz metodę płatności:</h4>
            <div class="form-group payment-method-selection">
                <label>
                    <input type="radio" name="payment_method_choice" value="Karta" checked>
                    <i class="fas fa-credit-card"></i> Karta Płatnicza
                </label>
                <label>
                    <input type="radio" name="payment_method_choice" value="Blik">
                    <i class="fas fa-mobile-alt"></i> BLIK
                </label>
                <label>
                    <input type="radio" name="payment_method_choice" value="Przelew">
                    <i class="fas fa-university"></i> Przelew Bankowy
                </label>
                
            </div>

            <div id="paymentSpecificFieldsSection" style="display:none;">
                <div id="cardPaymentFields" class="payment-details-fields" style="display:none;">
                    <h4>Dane Karty Płatniczej</h4>
                    <div class="form-group">
                        <label for="card_number">Numer Karty:</label>
                        <input type="text" id="card_number" name="card_number" placeholder="---- ---- ---- ----" autocomplete="cc-number" required>
                    </div>
                    <div class="form-group" style="display: flex; gap: 10px;">
                        <div style="flex-grow: 1;">
                            <label for="card_expiry">Data Ważności (MM/RR):</label>
                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/RR" autocomplete="cc-exp" required>
                        </div>
                        <div style="flex-grow: 1;">
                            <label for="card_cvv">Kod CVV/CVC:</label>
                            <input type="text" id="card_cvv" name="card_cvv" placeholder="---" autocomplete="cc-csc" required>
                        </div>
                    </div>
                </div>

                <div id="blikPaymentFields" class="payment-details-fields" style="display:none;">
                    <h4>Płatność BLIK</h4>
                    <div class="form-group">
                        <label for="blik_code">Kod BLIK (6 cyfr):</label>
                        <input type="text" id="blik_code" name="blik_code" placeholder="------" pattern="[0-9]{6}" inputmode="numeric" maxlength="6" required>
                    </div>
                </div>
                <div id="transferPaymentInfo" class="payment-details-fields payment-transfer-info" style="display:none;">
                    <h4>Płatność Przelewem Bankowym</h4>
                    <p>Prosimy o dokonanie przelewu na poniższe dane. W tytule przelewu prosimy podać numer zamówienia, który zostanie wygenerowany po jego złożeniu.</p>
                    <p><strong>Numer konta:</strong> PL 12 3456 7890 1234 5678 9012 3456</p>
                    <p><strong>Odbiorca:</strong> AquaParadise Sp. z o.o.</p>
                    <p><strong>Tytuł przelewu:</strong> Zamówienie nr <span id="orderIdForTransfer">[Numer zamówienia]</span></p>
                </div>
            </div>

            <div id="billingDetailsSection" style="display:none;">
                <h4>Dane do Zamówienia (wymagane dla gości przy przelewie)</h4>
                <div class="form-group">
                    <label for="billing_name">Imię i Nazwisko / Nazwa Firmy:</label>
                    <input type="text" id="billing_name" name="billing_name" value="<?php echo e($user_full_name_modal); ?>" required>
                </div>
                <div class="form-group">
                    <label for="billing_email">Adres Email:</label>
                    <input type="email" id="billing_email" name="billing_email" value="<?php echo e($user_email_modal); ?>" required>
                </div>
                <div class="form-group">
                    <label for="billing_address_street">Ulica i numer:</label>
                    <input type="text" id="billing_address_street" name="billing_address_street" required>
                </div>
                <div class="form-group" style="display: flex; gap: 10px;">
                    <div style="flex-grow: 1;">
                        <label for="billing_address_postal_code">Kod Pocztowy:</label>
                        <input type="text" id="billing_address_postal_code" name="billing_address_postal_code" placeholder="XX-XXX" required>
                    </div>
                    <div style="flex-grow: 1;">
                        <label for="billing_address_city">Miejscowość:</label>
                        <input type="text" id="billing_address_city" name="billing_address_city" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="billing_address_country">Kraj:</label>
                    <input type="text" id="billing_address_country" name="billing_address_country" value="Polska" required>
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px; margin-bottom:20px; font-size: 1.2em;">
                <strong>Do zapłaty: <span id="modalTotalAmount"></span> PLN</strong>
            </div>

            <button type="submit" id="mainPaymentSubmitButton" class="cta-button form-submit-button" style="width:100%;">
                <i class="fas fa-shield-alt"></i> Zapłać i Złóż Zamówienie
            </button>
        </form>
    </div>
</div>