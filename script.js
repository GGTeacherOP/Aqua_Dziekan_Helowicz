// script.js
document.addEventListener('DOMContentLoaded', function () {
    console.log("SCRIPT.JS DEBUG: DOMContentLoaded event fired.");

    const isLoggedIn = typeof isLoggedInFromPHP !== 'undefined' ? isLoggedInFromPHP : false;
    const basePath = typeof basePathJS !== 'undefined' ? basePathJS : '/';
    const currentUserFirstName = typeof currentUserFirstNameFromPHP !== 'undefined' ? currentUserFirstNameFromPHP : '';
    const currentUserLastName = typeof currentUserLastNameFromPHP !== 'undefined' ? currentUserLastNameFromPHP : ''; 
    const currentUserEmail = typeof currentUserEmailFromPHP !== 'undefined' ? currentUserEmailFromPHP : ''; 

    function showSimpleModal(message, type = 'info', duration = 4000) {
        const existingModal = document.getElementById('simpleMessageModal');
        if (existingModal) {
            existingModal.remove();
        }
        const modalOverlay = document.createElement('div');
        modalOverlay.id = 'simpleMessageModal';
        modalOverlay.className = 'modal simple-message-modal-overlay'; 
        modalOverlay.style.display = 'flex'; 
        const modalContent = document.createElement('div');
        modalContent.className = `modal-content simple-message-modal-content modal-type-${type}`; 
        const messageText = document.createElement('p');
        messageText.textContent = message;
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.className = 'close-button simple-modal-close-btn'; 
        closeButton.setAttribute('aria-label', 'Zamknij');
        closeButton.onclick = () => modalOverlay.remove();
        modalContent.appendChild(closeButton);
        modalContent.appendChild(messageText);
        modalOverlay.appendChild(modalContent);
        document.body.appendChild(modalOverlay);
        modalOverlay.addEventListener('click', e => {
            if (e.target === modalOverlay) {
                modalOverlay.remove();
            }
        });
        if (duration > 0) {
            setTimeout(() => {
                if (document.getElementById('simpleMessageModal')) { 
                    modalOverlay.remove();
                }
            }, duration);
        }
    }

    if ((window.location.pathname.endsWith('index.php') || window.location.pathname === basePath || window.location.pathname === basePath + 'index.php')) {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout_status') === 'success') {
            showSimpleModal("Pomyślnie wylogowano", 'success', 4000);
            if (history.replaceState) {
                const cleanURL = window.location.pathname + window.location.search.replace(/[?&]logout_status=success\b&?/, '').replace(/^&/, '?').replace(/\?$/, '');
                history.replaceState({ path: cleanURL }, '', cleanURL);
            }
        }
    }
    
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault(); 
            window.location.href = basePath + 'logout.php'; 
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const hrefAttribute = this.getAttribute('href');
            if (hrefAttribute.length > 1 && hrefAttribute.startsWith('#')) { 
                try { 
                    const targetElement = document.querySelector(hrefAttribute);
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                } catch (error) {
                    // console.error("Błąd w selektorze dla płynnego przewijania:", error);
                }
            }
        });
    });

    const authGuestModalContainer = document.getElementById('authGuestModalContainer');
    function showAuthGuestModal(options = {}) {
        const { productId, itemDetailsJsonString, formToSubmit, isButtonTrigger } = options;
        if (!authGuestModalContainer) {
            if (formToSubmit) formToSubmit.submit();
            return;
        }
        const existingModal = authGuestModalContainer.querySelector('#authGuestModal');
        if (existingModal) existingModal.remove();
        let redirectParams = [];
        if (productId) redirectParams.push(`product_id=${productId}`);
        if (itemDetailsJsonString) redirectParams.push(`item_details_json_string=${encodeURIComponent(itemDetailsJsonString)}`);
        const currentPath = window.location.pathname + window.location.search;
        const cleanCurrentPath = currentPath.replace(/[?&]logout_status=success\b&?/, '').replace(/^&/, '?').replace(/\?$/, '');
        redirectParams.push(`redirect=${encodeURIComponent(cleanCurrentPath)}`);
        const loginRedirectUrl = `${basePath}login.php?action=add_to_cart_after_login&${redirectParams.join('&')}`;
        const signupRedirectUrl = `${basePath}signup.php?action=add_to_cart_after_register&${redirectParams.join('&')}`;
        authGuestModalContainer.innerHTML = `
            <div id="authGuestModal" class="modal" style="display: flex; align-items:center; justify-content:center; z-index: 2000;">
                <div class="modal-content" style="max-width: 450px; text-align: center;">
                    <span class="close-button" id="closeAuthGuestModal" style="cursor:pointer;">&times;</span>
                    <h3>Aby dodać do koszyka...</h3>
                    <p>Proszę wybrać jedną z opcji:</p>
                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                        <button id="modalLoginBtn" class="cta-button" style="width: 100%;">Zaloguj się</button>
                        <button id="modalRegisterBtn" class="cta-button register-button" style="width: 100%;">Zarejestruj się</button>
                        <button id="modalGuestBtn" class="cta-button secondary-cta" style="width: 100%;">Kontynuuj jako gość</button>
                    </div></div></div>`;
        const closeAuthBtn = document.getElementById('closeAuthGuestModal');
        if(closeAuthBtn) closeAuthBtn.onclick = () => { if(authGuestModalContainer) authGuestModalContainer.innerHTML = ''; };
        const modalLoginBtn = document.getElementById('modalLoginBtn');
        if(modalLoginBtn) modalLoginBtn.onclick = () => window.location.href = loginRedirectUrl;
        const modalRegisterBtn = document.getElementById('modalRegisterBtn');
        if(modalRegisterBtn) modalRegisterBtn.onclick = () => window.location.href = signupRedirectUrl;
        const modalGuestBtn = document.getElementById('modalGuestBtn');
        if(modalGuestBtn) modalGuestBtn.onclick = () => {
            if (formToSubmit) { let guestInput = formToSubmit.querySelector('input[name="continue_as_guest"]'); if (!guestInput) { guestInput = document.createElement('input'); guestInput.type = 'hidden'; guestInput.name = 'continue_as_guest'; formToSubmit.appendChild(guestInput); } guestInput.value = '1'; formToSubmit.submit();
            } else if (isButtonTrigger && productId) { const guestForm = document.createElement('form'); guestForm.method = 'POST'; guestForm.action = `${basePath}cart_actions.php`; guestForm.innerHTML = `<input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="product_id" value="${productId}"><input type="hidden" name="quantity" value="1"> <input type="hidden" name="continue_as_guest" value="1">${itemDetailsJsonString ? `<input type="hidden" name="item_details_json_string" value='${itemDetailsJsonString}'>` : ''}`; document.body.appendChild(guestForm); guestForm.submit(); }
            if(authGuestModalContainer) authGuestModalContainer.innerHTML = '';
        };
        const authModalElement = document.getElementById('authGuestModal');
        if (authModalElement) { authModalElement.addEventListener('click', e => { if (e.target === authModalElement) { if(authGuestModalContainer) authGuestModalContainer.innerHTML = ''; } }); }
    }
    document.querySelectorAll('form[action*="cart_actions.php"], button.add-to-cart-button, a.add-to-cart-link').forEach(element => {
        const isForm = element.tagName === 'FORM';
        let requiresAuthModal = false;
        if (isForm) { const formAction = typeof element.action === 'string' ? element.action : (element.getAttribute('action') || ''); if (formAction.includes('cart_actions.php')) { const actionInput = element.querySelector('input[name="action"]'); if (actionInput && actionInput.value === 'add_to_cart') { requiresAuthModal = true; } }
        } else { if (element.classList.contains('add-to-cart-button') || element.classList.contains('add-to-cart-link')) { requiresAuthModal = true; } }
        if (requiresAuthModal) {
            element.addEventListener(isForm ? 'submit' : 'click', function(event) {
                if (!isLoggedIn) {
                    let isGuestContinuation = isForm ? !!this.querySelector('input[name="continue_as_guest"][value="1"]') : false;
                    if (!isGuestContinuation) {
                        event.preventDefault();
                        let productId = null, itemDetails = {}, itemDetailsJsonString = null;
                        if (isForm) { const pidField = this.querySelector('input[name="product_id"], select[name="product_id"]'); if (pidField) productId = pidField.value; this.querySelectorAll('input[name^="item_details["], select[name^="item_details["], textarea[name^="item_details["]').forEach(di => { let km = di.name.match(/item_details\[(.*?)\]/); if (km && km[1]) itemDetails[km[1]] = di.value; }); const existingJsonInput = this.querySelector('input[name="item_details_json_string"]'); if (existingJsonInput && existingJsonInput.value) itemDetailsJsonString = existingJsonInput.value;
                        } else { productId = this.dataset.productId; if (this.dataset.itemDetails) try { itemDetails = JSON.parse(this.dataset.itemDetails); } catch(e) {} }
                        if (!itemDetailsJsonString && Object.keys(itemDetails).length > 0) { const sd = {}; Object.keys(itemDetails).sort().forEach(k => sd[k] = itemDetails[k]); itemDetailsJsonString = JSON.stringify(sd); }
                        showAuthGuestModal({ productId, itemDetailsJsonString, formToSubmit: isForm ? this : null, isButtonTrigger: !isForm });
                    }
                }
            });
        }
    });

    // --- Payment Modal Logic ---
    const openPaymentModalBtn = document.getElementById('openPaymentModalBtn');
    const paymentCheckoutModal = document.getElementById('paymentCheckoutModal');
    const closePaymentCheckoutModalBtn = document.getElementById('closePaymentCheckoutModal');
    const checkoutForm = document.getElementById('checkoutForm'); 
    const paymentResponseMessage = document.getElementById('paymentResponseMessage');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method_choice"]');
    const cardFields = document.getElementById('cardPaymentFields');
    const blikFields = document.getElementById('blikPaymentFields');
    const transferInfo = document.getElementById('transferPaymentInfo');
    const mainPaymentSubmitButton = document.getElementById('mainPaymentSubmitButton');
    const billingDetailsSection = document.getElementById('billingDetailsSection');
    const paymentSpecificFieldsSection = document.getElementById('paymentSpecificFieldsSection');

    console.log("SCRIPT.JS DEBUG: openPaymentModalBtn:", openPaymentModalBtn);
    console.log("SCRIPT.JS DEBUG: paymentCheckoutModal:", paymentCheckoutModal);
    console.log("SCRIPT.JS DEBUG: checkoutForm (inside modal):", checkoutForm);

    if (openPaymentModalBtn && paymentCheckoutModal && checkoutForm) {
        console.log("SCRIPT.JS DEBUG: Attaching click listener to openPaymentModalBtn.");
        openPaymentModalBtn.addEventListener('click', function() {
            console.log("SCRIPT.JS DEBUG: 'Przejdź do kasy' button clicked!");
            
            checkoutForm.reset(); 
            const defaultPaymentMethodRadio = document.querySelector('input[name="payment_method_choice"][value="Karta"]');
            if (defaultPaymentMethodRadio) defaultPaymentMethodRadio.checked = true;

            const cartTotalAmountText = document.querySelector('.cart-summary h3')?.textContent;
            const modalTotalAmountSpan = document.getElementById('modalTotalAmount');
            if (cartTotalAmountText && modalTotalAmountSpan) {
                const match = cartTotalAmountText.match(/([\d,.\s]+)\s*PLN/);
                if (match && match[1]) {
                    modalTotalAmountSpan.textContent = match[1].replace(/\s/g, '');
                } else {
                    modalTotalAmountSpan.textContent = 'B/D';
                     console.log("SCRIPT.JS DEBUG: Could not parse total amount from cart summary:", cartTotalAmountText);
                }
            } else {
                if (!cartTotalAmountText) console.log("SCRIPT.JS DEBUG: Cart total amount text not found (.cart-summary h3).");
                if (!modalTotalAmountSpan) console.log("SCRIPT.JS DEBUG: Modal total amount span (#modalTotalAmount) not found.");
            }
            
            // Pre-wypełnianie danych użytkownika (jeśli zalogowany) jest teraz obsługiwane w updatePaymentFields
            
            updatePaymentFields(); 

            if(paymentResponseMessage) paymentResponseMessage.style.display = 'none';
            checkoutForm.style.display = 'block'; 
            if(mainPaymentSubmitButton) mainPaymentSubmitButton.disabled = false; 
            
            console.log("SCRIPT.JS DEBUG: Attempting to show paymentCheckoutModal.");
            paymentCheckoutModal.style.display = 'flex'; 
        });
    } else {
        console.log("SCRIPT.JS DEBUG: Could not attach payment modal listener. One or more elements are missing:");
        if (!openPaymentModalBtn) console.log("SCRIPT.JS DEBUG: - openPaymentModalBtn is missing.");
        if (!paymentCheckoutModal) console.log("SCRIPT.JS DEBUG: - paymentCheckoutModal is missing (is payment_modal.php included correctly and cart not empty?).");
        if (!checkoutForm) console.log("SCRIPT.JS DEBUG: - checkoutForm (inside modal) is missing.");
    }

    if (closePaymentCheckoutModalBtn && paymentCheckoutModal) {
        closePaymentCheckoutModalBtn.addEventListener('click', function() {
            console.log("SCRIPT.JS DEBUG: Close button clicked on payment modal.");
            paymentCheckoutModal.style.display = 'none';
        });
    }

    if (paymentCheckoutModal) { 
        paymentCheckoutModal.addEventListener('click', function(event) {
            if (event.target === paymentCheckoutModal) {
                console.log("SCRIPT.JS DEBUG: Clicked outside payment modal content.");
                paymentCheckoutModal.style.display = 'none';
            }
        });
    }

    function updatePaymentFields() {
        const selectedMethodRadio = document.querySelector('input[name="payment_method_choice"]:checked');
        if (!selectedMethodRadio || !mainPaymentSubmitButton) {
            return;
        }
        const selectedMethodValue = selectedMethodRadio.value;

        if (cardFields) cardFields.style.display = 'none';
        if (blikFields) blikFields.style.display = 'none';
        if (transferInfo) transferInfo.style.display = 'none';
        
        // *** ZMIANA TUTAJ: Logika pokazywania danych do zamówienia dla gościa ***
        if (billingDetailsSection) {
            if (!isLoggedIn) { // Jeśli użytkownik NIE jest zalogowany (jest gościem)
                billingDetailsSection.style.display = 'block'; // Pokaż sekcję danych do zamówienia
                // Ustaw pola jako wymagane
                ['billing_name', 'billing_email', 'billing_address_street', 'billing_address_city', 'billing_address_postal_code', 'billing_address_country'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.required = true;
                        // Wyczyść pola, jeśli użytkownik zmienia metodę płatności lub otwiera modal ponownie jako gość
                        // (chyba że chcesz zachować wcześniej wpisane dane gościa)
                        // field.value = ''; // Opcjonalnie: czyść pola przy każdej zmianie
                    }
                });
                // Pre-wypełnij imię i email jeśli byłyby dostępne globalnie dla gościa (rzadziej spotykane)
                const guestNameField = document.getElementById('billing_name');
                const guestEmailField = document.getElementById('billing_email');
                // if(guestNameField && typeof someGlobalGuestName !== 'undefined') guestNameField.value = someGlobalGuestName;
                // if(guestEmailField && typeof someGlobalGuestEmail !== 'undefined') guestEmailField.value = someGlobalGuestEmail;

            } else { // Jeśli użytkownik JEST zalogowany
                billingDetailsSection.style.display = 'none'; // Ukryj sekcję
                const billingInputs = billingDetailsSection.querySelectorAll('input, textarea');
                billingInputs.forEach(input => input.required = false); // Pola nie są wymagane
                // Pre-wypełnij dla zalogowanego, jeśli są widoczne (ale teraz nie będą)
                const billingNameField = document.getElementById('billing_name');
                const billingEmailField = document.getElementById('billing_email');
                if(billingNameField) billingNameField.value = currentUserFirstName + ' ' + currentUserLastName;
                if(billingEmailField) billingEmailField.value = currentUserEmail;
            }
        }
        // *** KONIEC ZMIANY ***


        if (paymentSpecificFieldsSection) {
            paymentSpecificFieldsSection.style.display = 'block';
        }

        if(cardFields) {
            if(cardFields.querySelector('#card_number')) cardFields.querySelector('#card_number').required = false;
            if(cardFields.querySelector('#card_expiry')) cardFields.querySelector('#card_expiry').required = false;
            if(cardFields.querySelector('#card_cvv')) cardFields.querySelector('#card_cvv').required = false;
        }
        if(blikFields && blikFields.querySelector('#blik_code')) blikFields.querySelector('#blik_code').required = false;


        switch (selectedMethodValue) {
            case 'Karta':
                if (cardFields) cardFields.style.display = 'block';
                if(cardFields.querySelector('#card_number')) cardFields.querySelector('#card_number').required = true;
                if(cardFields.querySelector('#card_expiry')) cardFields.querySelector('#card_expiry').required = true;
                if(cardFields.querySelector('#card_cvv')) cardFields.querySelector('#card_cvv').required = true;
                mainPaymentSubmitButton.innerHTML = '<i class="fas fa-credit-card"></i> Zapłać Kartą i Złóż Zamówienie';
                break;
            case 'Blik':
                if (blikFields) blikFields.style.display = 'block';
                if(blikFields.querySelector('#blik_code')) blikFields.querySelector('#blik_code').required = true;
                mainPaymentSubmitButton.innerHTML = '<i class="fas fa-mobile-alt"></i> Zapłać BLIKiem i Złóż Zamówienie';
                break;
            case 'Przelew': 
                if (transferInfo) transferInfo.style.display = 'block';
                mainPaymentSubmitButton.innerHTML = '<i class="fas fa-university"></i> Złóż Zamówienie (Przelew)';
                break;
        }
    }

    if (paymentMethodRadios.length > 0) {
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', updatePaymentFields);
        });
        if (document.querySelector('input[name="payment_method_choice"]:checked')) {
            updatePaymentFields();
        }
    }

    // --- START: Logika formatowania pól karty płatniczej ---
    const cardNumberInput = document.getElementById('card_number');
    const cardExpiryInput = document.getElementById('card_expiry');
    const cardCvvInput = document.getElementById('card_cvv');

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); 
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue.substring(0, 19); 
        });
        cardNumberInput.addEventListener('keydown', function(e) {
            const value = e.target.value.replace(/\s/g, '');
            if (value.length >= 16 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) && !e.metaKey && !e.ctrlKey && !e.altKey && e.key.length === 1 ) {
                e.preventDefault();
            }
        });
    }

    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); 
            let formattedValue = '';
            if (value.length > 2) {
                formattedValue = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else if (value.length === 2 && e.inputType !== 'deleteContentBackward' && e.target.value.indexOf('/') === -1) {
                formattedValue = value + '/';
            } else {
                formattedValue = value;
            }
            e.target.value = formattedValue.substring(0, 5); 
        });
        cardExpiryInput.addEventListener('keydown', function(e) {
            const value = e.target.value.replace(/\//g, '');
             if (value.length >= 4 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) && !e.metaKey && !e.ctrlKey && !e.altKey && e.key.length === 1) {
                // Zezwól na wpisanie slasha jeśli nie został jeszcze dodany, a są już 2 cyfry
                if (e.target.value.length === 2 && e.key === '/') { return; }
                e.preventDefault();
            }
        });
    }

    if (cardCvvInput) {
        cardCvvInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); 
            e.target.value = value.substring(0, 3); 
        });
        cardCvvInput.addEventListener('keydown', function(e) {
            const value = e.target.value;
            if (value.length >= 3 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) && !e.metaKey && !e.ctrlKey && !e.altKey && e.key.length === 1) {
                e.preventDefault();
            }
        });
    }
    // --- END: Logika formatowania pól karty płatniczej ---


    if (checkoutForm && mainPaymentSubmitButton) {
        checkoutForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Zawsze na początku, aby mieć kontrolę
            
            console.log("SCRIPT.JS DEBUG: Checkout form submitted.");
            const selectedMethodRadio = document.querySelector('input[name="payment_method_choice"]:checked');
            if (!selectedMethodRadio) { 
                showSimpleModal('Proszę wybrać metodę płatności.', 'error');
                console.log("SCRIPT.JS DEBUG: No payment method selected on submit.");
                return; 
            }
            const paymentMethod = selectedMethodRadio.value;
            let customValidationOk = true; 
            
            this.querySelectorAll('input[required]').forEach(input => {
                 // Reset border color only for visible and required fields
                if (input.offsetParent !== null) { // Check if element is visible
                    input.style.borderColor = 'var(--border-color)';
                }
            });

            if (billingDetailsSection && billingDetailsSection.style.display === 'block') { // Waliduj tylko jeśli widoczne
                const requiredBillingFields = ['billing_name', 'billing_email', 'billing_address_street', 'billing_address_city', 'billing_address_postal_code', 'billing_address_country'];
                for (const id of requiredBillingFields) {
                    const field = document.getElementById(id);
                    if (field && field.required && field.value.trim() === '') {
                        const labelText = field.previousElementSibling ? field.previousElementSibling.textContent.replace(':','') : id.replace('billing_', '').replace('_', ' ');
                        showSimpleModal(`Pole "${labelText}" jest wymagane.`, 'error');
                        field.style.borderColor = 'red'; customValidationOk = false; break; 
                    }
                    if (field && field.id === "billing_email" && field.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())) {
                        showSimpleModal('Proszę podać poprawny adres email w danych do zamówienia.', 'error'); field.style.borderColor = 'red'; customValidationOk = false; break;
                    }
                    if (field && field.id === "billing_address_postal_code" && field.value.trim() !== '' && !/^[0-9]{2}-[0-9]{3}$/.test(field.value.trim())) {
                        showSimpleModal('Kod pocztowy w danych do zamówienia musi być w formacie XX-XXX.', 'error'); field.style.borderColor = 'red'; customValidationOk = false; break;
                    }
                }
            }
            if (!customValidationOk) { console.log("SCRIPT.JS DEBUG: Billing validation failed."); return; }

            if (paymentMethod === 'Karta') {
                const cardNumberEl = document.getElementById('card_number');
                const cardExpiryEl = document.getElementById('card_expiry');
                const cardCvvEl = document.getElementById('card_cvv');
                
                const cardNumber = cardNumberEl?.value.replace(/\s/g, '');
                const cardExpiry = cardExpiryEl?.value;
                const cardCvv = cardCvvEl?.value;

                if (!cardNumber || cardNumber.length < 13 || cardNumber.length > 16 || !/^\d+$/.test(cardNumber)) {
                    showSimpleModal('Numer karty jest nieprawidłowy (13-16 cyfr).', 'error'); 
                    if(cardNumberEl) cardNumberEl.style.borderColor = 'red'; customValidationOk = false;
                }
                if (!cardExpiry || !/^(0[1-9]|1[0-2])\s?\/?\s?([0-9]{2})$/.test(cardExpiry.replace(/\s/g, ''))) {
                    showSimpleModal('Data ważności karty (MM/RR) jest nieprawidłowa.', 'error'); 
                    if(cardExpiryEl) cardExpiryEl.style.borderColor = 'red'; customValidationOk = false;
                } else {
                    const parts = cardExpiry.replace(/\s/g, '').split('/');
                    if (parts.length === 2) {
                        const month = parseInt(parts[0], 10);
                        const year = parseInt("20" + parts[1], 10);
                        const currentYear = new Date().getFullYear();
                        const currentMonth = new Date().getMonth() + 1;
                        if (year < currentYear || (year === currentYear && month < currentMonth)) {
                            showSimpleModal('Karta straciła ważność.', 'error');
                            if(cardExpiryEl) cardExpiryEl.style.borderColor = 'red'; customValidationOk = false;
                        }
                    }
                }
                if (!cardCvv || !/^[0-9]{3}$/.test(cardCvv)) { 
                    showSimpleModal('Kod CVV jest nieprawidłowy (dokładnie 3 cyfry).', 'error'); 
                    if(cardCvvEl) cardCvvEl.style.borderColor = 'red'; customValidationOk = false;
                }
            }
            if (paymentMethod === 'Blik') {
                const blikCodeInput = document.getElementById('blik_code');
                if (blikCodeInput && blikCodeInput.required && !/^[0-9]{6}$/.test(blikCodeInput.value)) { 
                    showSimpleModal('Kod BLIK musi składać się z 6 cyfr.', 'error'); 
                    if (blikCodeInput) blikCodeInput.style.borderColor = 'red'; customValidationOk = false;
                }
            }

            if (!customValidationOk) {
                console.log("SCRIPT.JS DEBUG: Walidacja formularza płatności nie powiodła się.");
                return; 
            }
            
            mainPaymentSubmitButton.disabled = true;
            mainPaymentSubmitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Przetwarzanie...';
            processOrder(paymentMethod); 
        });
    }

    function processOrder(paymentMethodValue) { // Zmieniono nazwę argumentu dla jasności
        const formData = new FormData(checkoutForm);
        
        formData.delete('payment_method_choice'); // Usuń pole radio
        formData.append('payment_method', paymentMethodValue); // Dodaj poprawną nazwę

        if (paymentMethodValue !== 'Karta') {
            formData.delete('card_number'); 
            formData.delete('card_expiry'); 
            formData.delete('card_cvv');
        }
        if (paymentMethodValue !== 'Blik') { formData.delete('blik_code'); }
        
        if(!isLoggedIn) { // Jeśli gość, a sekcja billingDetailsSection nie była widoczna (np. dla Karty/BLIK domyślnie)
            if (!billingDetailsSection || billingDetailsSection.style.display === 'none') {
                 ['billing_name', 'billing_email', 'billing_address_street', 'billing_address_city', 'billing_address_postal_code', 'billing_address_country'].forEach(field => formData.delete(field));
            }
        }


        console.log("SCRIPT.JS DEBUG: Processing order with method:", paymentMethodValue);
        // Wypisz FormData dla debugowania
        // for (var pair of formData.entries()) { console.log("FormData: " + pair[0]+ ': ' + pair[1]); }

        fetch(basePath + 'process_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.text().then(text => {
                console.log("SCRIPT.JS DEBUG: Raw response from process_payment.php:", text);
                if (!response.ok) {
                    try { const errorData = JSON.parse(text); throw new Error(errorData.message || `Błąd serwera: ${response.status}.`); } 
                    catch (e) { throw new Error(`Błąd serwera: ${response.status}. Odpowiedź: ${text.substring(0, 200)}...`); }
                }
                try { return JSON.parse(text); } 
                catch (e) { 
                    console.error("SCRIPT.JS DEBUG: Odpowiedź serwera nie jest poprawnym JSON-em:", text);
                    throw new Error('Błąd: Otrzymano nieprawidłową odpowiedź (nie-JSON) z serwera.');
                }
            });
        })
        .then(data => {
            console.log("SCRIPT.JS DEBUG: Parsed data from process_payment.php:", data);
            if (data.success) {
                window.location.href = basePath + 'order_confirmation.php?order_id=' + data.order_id;
            } else {
                if (paymentResponseMessage) {
                    paymentResponseMessage.style.display = 'block';
                    paymentResponseMessage.className = 'flash-message error'; 
                    paymentResponseMessage.innerHTML = `<p>${data.message || 'Wystąpił nieoczekiwany błąd.'}</p>`;
                } else {
                    showSimpleModal(data.message || 'Wystąpił nieoczekiwany błąd.', 'error');
                }
                if(mainPaymentSubmitButton) mainPaymentSubmitButton.disabled = false; 
                const currentMethod = formData.get('payment_method'); // Pobierz aktualną metodę
                if (currentMethod === 'Karta') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-credit-card"></i> Zapłać Kartą i Złóż Zamówienie';
                else if (currentMethod === 'Blik') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-mobile-alt"></i> Zapłać BLIKiem i Złóż Zamówienie';
                else if (currentMethod === 'Przelew') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-university"></i> Złóż Zamówienie (Przelew)';
                else mainPaymentSubmitButton.innerHTML = '<i class="fas fa-shield-alt"></i> Zapłać i Złóż Zamówienie';
            }
        })
        .catch(error => {
            console.error("SCRIPT.JS DEBUG: Error in processOrder fetch:", error);
            if (paymentResponseMessage) {
                paymentResponseMessage.style.display = 'block';
                paymentResponseMessage.className = 'flash-message error'; 
                paymentResponseMessage.innerHTML = `<p>${error.message || 'Wystąpił krytyczny błąd podczas przetwarzania zamówienia.'}</p>`;
            } else {
                 showSimpleModal(error.message || 'Wystąpił krytyczny błąd podczas przetwarzania zamówienia.', 'error');
            }
            if(mainPaymentSubmitButton) mainPaymentSubmitButton.disabled = false; 
            const currentMethod = formData.get('payment_method');
            if (currentMethod === 'Karta') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-credit-card"></i> Zapłać Kartą i Złóż Zamówienie';
            else if (currentMethod === 'Blik') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-mobile-alt"></i> Zapłać BLIKiem i Złóż Zamówienie';
            else if (currentMethod === 'Przelew') mainPaymentSubmitButton.innerHTML = '<i class="fas fa-university"></i> Złóż Zamówienie (Przelew)';
            else mainPaymentSubmitButton.innerHTML = '<i class="fas fa-shield-alt"></i> Zapłać i Złóż Zamówienie';
        });
    }
    
    // --- SPA Booking Modal Logic ---
    const spaBookingForm = document.getElementById('spaBookingForm');
    if (spaBookingForm) {
        const spaModal = document.getElementById('spaPackagesModal');
        const openSpaModalButton = document.getElementById('spaPackageButton');
        const closeSpaModalButton = spaModal ? spaModal.querySelector('#closeSpaModalBtn') : null;
        const confirmSpaSelectionButton = spaModal ? spaModal.querySelector('#confirmSpaSelection') : null; 
        const packagesHorizontalListContainer = document.getElementById('spaPackagesHorizontalList');
        const hiddenInputSelectedItems = document.getElementById('selectedSpaItems'); 
        const hiddenInputCalculatedPrice = document.getElementById('calculatedSpaPrice'); 
        const formProductIdInput = document.getElementById('formProductId'); 
        const placeholderSpaProductInput = spaBookingForm.querySelector('input[name="item_details[placeholder_spa_product_id]"]');
        const placeholderSpaProductId = placeholderSpaProductInput ? placeholderSpaProductInput.value : null;
        const selectedTreatmentsDisplay = document.getElementById('selectedTreatmentsDisplay');

        function populateSpaModalDynamically(spaProducts) { 
            if (!packagesHorizontalListContainer) { console.error("JS Error: Kontener #spaPackagesHorizontalList nie został znaleziony."); return; }
            packagesHorizontalListContainer.innerHTML = ''; 
            if (!spaProducts || !Array.isArray(spaProducts) || spaProducts.length === 0) {
                packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding:20px;">Obecnie brak dostępnych zabiegów lub pakietów SPA.</p>';
                return;
            }
            const categories = {};
            spaProducts.forEach(prod => {
                if (!prod || typeof prod.name !== 'string' || typeof prod.price === 'undefined' || typeof prod.product_id === 'undefined' || typeof prod.category_name_from_db !== 'string') { return; }
                const price = parseFloat(prod.price);
                if (isNaN(price)) { return; }
                const categoryName = prod.category_name_from_db;
                if (!categories[categoryName]) categories[categoryName] = { name: categoryName, treatments: [] };
                categories[categoryName].treatments.push({ product_id: prod.product_id, name: `${prod.name} - ${price.toFixed(2)} PLN`, price: price, description: prod.description || '' });
            });
            const displayOrder = ["SPA - Pakiety Wellness", "SPA - Terapie Masażu", "SPA - Zabiegi na Twarz", "SPA - Zabiegi na Ciało"];
            const sortedCategories = {};
            displayOrder.forEach(catName => { if (categories[catName]) { sortedCategories[catName] = categories[catName]; delete categories[catName]; } });
            for (const catName in categories) sortedCategories[catName] = categories[catName];

            for (const catKey in sortedCategories) {
                const categoryData = sortedCategories[catKey];
                const categoryDiv = document.createElement('div'); categoryDiv.classList.add('spa-package-category');
                const categoryTitle = document.createElement('h4'); categoryTitle.textContent = categoryData.name.replace('SPA - ', ''); categoryDiv.appendChild(categoryTitle);
                if (categoryData.treatments.length === 0) {
                    const p = document.createElement('p'); p.textContent = 'Brak ofert.'; p.style.cssText = 'font-size:0.85em; color:grey;'; categoryDiv.appendChild(p);
                } else {
                    categoryData.treatments.forEach(treatment => {
                        const lbl = document.createElement('label'); lbl.classList.add('treatment-option-label'); lbl.title = treatment.description;
                        const inp = document.createElement('input');
                        inp.type = (categoryData.name === "SPA - Pakiety Wellness") ? 'radio' : 'checkbox';
                        inp.name = (categoryData.name === "SPA - Pakiety Wellness") ? 'spa_package_option' : 'spa_treatment_option';
                        inp.value = treatment.product_id; inp.dataset.treatmentName = treatment.name; inp.dataset.price = treatment.price;
                        inp.addEventListener('change', function() {
                            if (this.type === 'radio' && this.checked) packagesHorizontalListContainer.querySelectorAll('input[type="checkbox"][name="spa_treatment_option"]').forEach(cb => cb.checked = false);
                            else if (this.type === 'checkbox' && this.checked) packagesHorizontalListContainer.querySelectorAll('input[type="radio"][name="spa_package_option"]').forEach(rb => rb.checked = false);
                        });
                        lbl.appendChild(inp); lbl.appendChild(document.createTextNode(` ${treatment.name}`)); categoryDiv.appendChild(lbl);
                    });
                }
                packagesHorizontalListContainer.appendChild(categoryDiv);
            }
        }
        if (openSpaModalButton && spaModal && packagesHorizontalListContainer) {
            openSpaModalButton.addEventListener('click', function() {
                if (typeof allSpaProductsFromPHP !== 'undefined' && Array.isArray(allSpaProductsFromPHP)) { // ZMIANA ZMIENNEJ
                    populateSpaModalDynamically(allSpaProductsFromPHP);
                } else {
                    if(packagesHorizontalListContainer) packagesHorizontalListContainer.innerHTML = '<p>Błąd ładowania danych o zabiegach.</p>';
                    console.error("script.js: Zmienna allSpaProductsFromPHP nie jest zdefiniowana lub jest niepoprawna.");
                }
                if (hiddenInputSelectedItems && packagesHorizontalListContainer) {
                    const prevSelIds = hiddenInputSelectedItems.value.split(',').filter(id => id.trim() !== '');
                    packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"], input[name="spa_package_option"]').forEach(input => input.checked = prevSelIds.includes(input.value));
                }
                spaModal.style.display = 'block';
            });
        }
        if (closeSpaModalButton && spaModal) closeSpaModalButton.onclick = () => spaModal.style.display = 'none';
        if (spaModal) spaModal.addEventListener('click', e => { if (e.target === spaModal) spaModal.style.display = 'none'; });

        if (confirmSpaSelectionButton && spaModal && hiddenInputSelectedItems && packagesHorizontalListContainer && selectedTreatmentsDisplay && hiddenInputCalculatedPrice && formProductIdInput) {
            confirmSpaSelectionButton.onclick = function() {
                let selIds = [], selDataDisp = [], totPrice = 0, isPkgSel = false;
                const selPkgRadio = packagesHorizontalListContainer.querySelector('input[type="radio"][name="spa_package_option"]:checked');
                if (selPkgRadio) {
                    isPkgSel = true; selIds.push(selPkgRadio.value); selDataDisp.push(selPkgRadio.dataset.treatmentName);
                    totPrice = parseFloat(selPkgRadio.dataset.price || 0); formProductIdInput.value = selPkgRadio.value;
                } else {
                    packagesHorizontalListContainer.querySelectorAll('input[type="checkbox"][name="spa_treatment_option"]:checked').forEach(cb => {
                        selIds.push(cb.value); selDataDisp.push(cb.dataset.treatmentName); totPrice += parseFloat(cb.dataset.price || 0);
                    });
                    formProductIdInput.value = selIds.length > 0 ? (placeholderSpaProductId || '') : '';
                }
                hiddenInputSelectedItems.value = selIds.join(','); hiddenInputCalculatedPrice.value = totPrice.toFixed(2);
                selectedTreatmentsDisplay.innerHTML = '';
                if (selDataDisp.length > 0) {
                    const h5 = document.createElement('h5'); h5.textContent = isPkgSel ? 'Wybrany pakiet SPA:' : 'Wybrane zabiegi SPA:'; selectedTreatmentsDisplay.appendChild(h5);
                    const ul = document.createElement('ul'); selDataDisp.forEach(n => { const li = document.createElement('li'); li.textContent = n; ul.appendChild(li); }); selectedTreatmentsDisplay.appendChild(ul);
                    const p = document.createElement('p'); p.innerHTML = `<strong>Łączna kwota: ${totPrice.toFixed(2)} PLN</strong>`; selectedTreatmentsDisplay.appendChild(p);
                    selectedTreatmentsDisplay.style.display = 'block';
                    if (openSpaModalButton) openSpaModalButton.textContent = `Wybrano (${selIds.length}) Zmień wybór`;
                } else {
                    selectedTreatmentsDisplay.style.display = 'none'; if (openSpaModalButton) openSpaModalButton.textContent = 'Wybierz Zabiegi lub Gotowy Pakiet';
                    formProductIdInput.value = placeholderSpaProductId || '';
                }
                spaModal.style.display = 'none';
            };
        }
        if (spaBookingForm) {
            spaBookingForm.addEventListener('submit', function(e){
                if (!hiddenInputSelectedItems || hiddenInputSelectedItems.value.trim() === '') {
                    showSimpleModal('Proszę wybrać zabieg/pakiet SPA.', 'error'); e.preventDefault(); return;
                }
                if (!formProductIdInput.value) {
                    showSimpleModal('Błąd ID produktu SPA. Wybierz ponownie.', 'error'); e.preventDefault(); return;
                }
            });
        }
    }
    // --- Pozostałe funkcje (opinie) ---
    const opinionsListCarousel = document.querySelector('.opinions-list-carousel');
    if (opinionsListCarousel) {
        const opinionCards = Array.from(opinionsListCarousel.querySelectorAll('.opinion-card-carousel'));
        const prevOpinionBtn = document.getElementById('prevOpinionBtn');
        const nextOpinionBtn = document.getElementById('nextOpinionBtn');
        let currentOpinionIndex = 0;
        function showOpinion(index) {
            opinionCards.forEach((card, i) => { card.classList.remove('active'); card.style.display = 'none'; if (i === index) { card.classList.add('active'); card.style.display = 'block'; } });
            if (prevOpinionBtn && nextOpinionBtn && opinionCards.length > 0) { prevOpinionBtn.disabled = index === 0; nextOpinionBtn.disabled = index === opinionCards.length - 1; }
            else if (prevOpinionBtn && nextOpinionBtn) { prevOpinionBtn.style.display = 'none'; nextOpinionBtn.style.display = 'none'; const nav = document.querySelector('.carousel-navigation'); if(nav) nav.style.display = 'none';}
        }
        if (prevOpinionBtn) prevOpinionBtn.addEventListener('click', () => { if (currentOpinionIndex > 0) { currentOpinionIndex--; showOpinion(currentOpinionIndex); } });
        if (nextOpinionBtn) nextOpinionBtn.addEventListener('click', () => { if (currentOpinionIndex < opinionCards.length - 1) { currentOpinionIndex++; showOpinion(currentOpinionIndex); } });
        if (opinionCards.length > 0) showOpinion(currentOpinionIndex);
        else { const nav = document.querySelector('.carousel-navigation'); if (nav) nav.style.display = 'none';}
    }
    const opinionsListContainer = document.getElementById('opinionsListContainer');
    const loadMoreOpinionsBtn = document.getElementById('loadMoreOpinionsBtn');
    const loadingOpinionsIndicator = document.getElementById('loadingOpinionsIndicator');
    let currentPage = 1;
    if (loadMoreOpinionsBtn && opinionsListContainer) {
        currentPage = 1;
        loadMoreOpinionsBtn.addEventListener('click', function() {
            currentPage++;
            if(loadingOpinionsIndicator) loadingOpinionsIndicator.style.display = 'block';
            this.disabled = true; this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ładowanie...';
            fetch(`${basePath}load_more_opinions.php?page=${currentPage}`)
                .then(response => { if (!response.ok) throw new Error(`HTTP error! ${response.status}`); return response.json(); })
                .then(data => {
                    if (data.success && data.opinions_html && Array.isArray(data.opinions_html)) {
                        if (data.opinions_html.length > 0) data.opinions_html.forEach(html => opinionsListContainer.insertAdjacentHTML('beforeend', html));
                        if (data.has_more) { this.disabled = false; this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii'; }
                        else { this.style.display = 'none'; const p = document.createElement('p'); p.textContent = 'To już wszystkie opinie.'; p.style.cssText = 'text-align:center;margin-top:20px;color:var(--text-muted-color);'; if(this.parentElement) this.parentElement.appendChild(p); }
                    } else if (data.success && (!data.opinions_html || data.opinions_html.length === 0) && !data.has_more) {
                        this.style.display = 'none'; const p = document.createElement('p'); p.textContent = 'To już wszystkie opinie.'; p.style.cssText = 'text-align:center;margin-top:20px;color:var(--text-muted-color);'; if(this.parentElement) this.parentElement.appendChild(p);
                    } else { showSimpleModal(data.message || 'Błąd ładowania opinii.', 'error'); this.disabled = false; this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii'; currentPage--; }
                })
                .catch(err => { showSimpleModal('Błąd komunikacji.', 'error'); this.disabled = false; this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii'; currentPage--; })
                .finally(() => { if(loadingOpinionsIndicator) loadingOpinionsIndicator.style.display = 'none'; });
        });
    }
    const toggleAddOpinionFormBtn = document.getElementById('toggleAddOpinionFormBtn');
    const addOpinionFormContainer = document.getElementById('addOpinionFormContainer');
    if (toggleAddOpinionFormBtn && addOpinionFormContainer) {
        toggleAddOpinionFormBtn.addEventListener('click', function() {
            const vis = addOpinionFormContainer.style.display === 'block';
            addOpinionFormContainer.style.display = vis ? 'none' : 'block';
            this.innerHTML = vis ? '<i class="fas fa-plus-circle"></i> Dodaj swoją opinię' : '<i class="fas fa-minus-circle"></i> Ukryj formularz';
            if (!vis) addOpinionFormContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    }
    const newOpinionForm = document.getElementById('newOpinionForm');
    if (newOpinionForm) {
        newOpinionForm.addEventListener('submit', function(e) {
            if (!this.querySelector('input[name="rating"]:checked')) {
                e.preventDefault(); showSimpleModal('Proszę wybrać ocenę.', 'error');
                const lbl = this.querySelector('.rating-form-label'); if(lbl) {lbl.scrollIntoView({behavior:'smooth',block:'center'});lbl.style.color='red';setTimeout(()=>lbl.style.color='',3000);}
                return false;
            }
        });
    }
}); // Koniec DOMContentLoaded