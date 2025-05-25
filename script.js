// script.js
document.addEventListener('DOMContentLoaded', function () {
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

    function updatePaymentFields() {
        const selectedMethodRadio = document.querySelector('input[name="payment_method_choice"]:checked');
        if (!selectedMethodRadio || !mainPaymentSubmitButton) return;
        const selectedMethodValue = selectedMethodRadio.value;
        if (cardFields) cardFields.style.display = 'none';
        if (blikFields) blikFields.style.display = 'none';
        if (transferInfo) transferInfo.style.display = 'none';
        if (billingDetailsSection) billingDetailsSection.style.display = 'none';
        const billingInputs = billingDetailsSection ? billingDetailsSection.querySelectorAll('input') : [];
        billingInputs.forEach(input => input.required = false);
        if (cardFields) {
            if(cardFields.querySelector('#card_number')) cardFields.querySelector('#card_number').required = false;
            if(cardFields.querySelector('#card_expiry')) cardFields.querySelector('#card_expiry').required = false;
            if(cardFields.querySelector('#card_cvv')) cardFields.querySelector('#card_cvv').required = false;
        }
        if (blikFields) {
            const blikInput = blikFields.querySelector('#blik_code');
            if (blikInput) blikInput.required = false;
        }
        if (selectedMethodValue === 'Przelew' && !isLoggedIn) {
            if (billingDetailsSection) {
                billingDetailsSection.style.display = 'block';
                billingInputs.forEach(input => input.required = true);
            }
        }
        if (paymentSpecificFieldsSection) {
            paymentSpecificFieldsSection.style.display = 'block';
        }
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
                const blikInput = blikFields.querySelector('#blik_code');
                if(blikInput) blikInput.required = true;
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
    }

    if (openPaymentModalBtn && paymentCheckoutModal && checkoutForm) {
        openPaymentModalBtn.addEventListener('click', function() {
            checkoutForm.reset(); 
            const defaultPaymentMethodRadio = document.querySelector('input[name="payment_method_choice"][value="Karta"]');
            if (defaultPaymentMethodRadio) defaultPaymentMethodRadio.checked = true;
            if (isLoggedIn) {
                const billingNameField = document.getElementById('billing_name');
                const billingEmailField = document.getElementById('billing_email');
                if (billingNameField && billingDetailsSection && billingDetailsSection.style.display === 'block') {
                    billingNameField.value = currentUserFirstName + ' ' + currentUserLastName;
                }
                if (billingEmailField && billingDetailsSection && billingDetailsSection.style.display === 'block') {
                    billingEmailField.value = currentUserEmail;
                }
            }
            if(paymentSpecificFieldsSection) paymentSpecificFieldsSection.style.display = 'block';
            if(mainPaymentSubmitButton) mainPaymentSubmitButton.style.display = 'block';
            checkoutForm.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]').forEach(input => { input.style.borderColor = 'var(--border-color)'; });
            updatePaymentFields(); 
            if(paymentResponseMessage) paymentResponseMessage.style.display = 'none';
            checkoutForm.style.display = 'block'; 
            if(mainPaymentSubmitButton) mainPaymentSubmitButton.disabled = false; 
            paymentCheckoutModal.style.display = 'flex'; 
        });
    }

    if (closePaymentCheckoutModalBtn && paymentCheckoutModal) {
        closePaymentCheckoutModalBtn.addEventListener('click', function() {
            paymentCheckoutModal.style.display = 'none';
        });
    }

    if (paymentCheckoutModal) { 
        paymentCheckoutModal.addEventListener('click', function(event) {
            if (event.target === paymentCheckoutModal) {
                paymentCheckoutModal.style.display = 'none';
            }
        });
    }

    if (checkoutForm && mainPaymentSubmitButton) {
        checkoutForm.addEventListener('submit', function(event) {
            event.preventDefault(); 
            const selectedMethodRadio = document.querySelector('input[name="payment_method_choice"]:checked');
            if (!selectedMethodRadio) { showSimpleModal('Proszę wybrać metodę płatności.', 'error'); return; }
            const paymentMethod = selectedMethodRadio.value;
            let customValidationOk = true;
            
            if (billingDetailsSection && billingDetailsSection.style.display === 'block') {
                const requiredBillingFields = ['billing_name', 'billing_email', 'billing_address_street', 'billing_address_city', 'billing_address_postal_code', 'billing_address_country'];
                for (const id of requiredBillingFields) {
                    const field = document.getElementById(id);
                    if (field && field.required && field.value.trim() === '') {
                        const labelText = field.previousElementSibling ? field.previousElementSibling.textContent.replace(':','') : id.replace('billing_', '').replace('_', ' ');
                        showSimpleModal(`Pole "${labelText}" jest wymagane.`, 'error');
                        field.style.borderColor = 'red'; customValidationOk = false; break; 
                    }
                    if (field && field.name === "billing_email" && field.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())) {
                        showSimpleModal('Proszę podać poprawny adres email.', 'error'); field.style.borderColor = 'red'; customValidationOk = false; break;
                    }
                    if (field && field.name === "billing_address_postal_code" && field.value.trim() !== '' && !/^[0-9]{2}-[0-9]{3}$/.test(field.value.trim())) {
                        showSimpleModal('Kod pocztowy musi być w formacie XX-XXX.', 'error'); field.style.borderColor = 'red'; customValidationOk = false; break;
                    }
                }
            }
            if (!customValidationOk) return; 

            if (paymentMethod === 'Karta') {
                const cardNumber = document.getElementById('card_number');
                const cardExpiry = document.getElementById('card_expiry');
                const cardCvv = document.getElementById('card_cvv');
                if (cardNumber && cardNumber.required && cardNumber.value.trim().length < 15) {showSimpleModal('Numer karty jest nieprawidłowy.', 'error'); cardNumber.style.borderColor = 'red'; customValidationOk = false;}
                if (cardExpiry && cardExpiry.required && !/^(0[1-9]|1[0-2])\s?\/?\s?([0-9]{2})$/.test(cardExpiry.value.replace(' ',''))) {showSimpleModal('Data ważności karty (MM/RR) jest nieprawidłowa.', 'error'); cardExpiry.style.borderColor = 'red'; customValidationOk = false;}
                if (cardCvv && cardCvv.required && !/^[0-9]{3,4}$/.test(cardCvv.value)) {showSimpleModal('Kod CVV jest nieprawidłowy (3 lub 4 cyfry).', 'error'); cardCvv.style.borderColor = 'red'; customValidationOk = false;}
            }
            if (paymentMethod === 'Blik') {
                const blikCodeInput = document.getElementById('blik_code');
                if (blikCodeInput && blikCodeInput.required && !/^[0-9]{6}$/.test(blikCodeInput.value)) { showSimpleModal('Kod BLIK musi składać się z 6 cyfr.', 'error'); if (blikCodeInput) blikCodeInput.style.borderColor = 'red'; customValidationOk = false;}
            }
            if (!customValidationOk) return; 

            mainPaymentSubmitButton.disabled = true;
            mainPaymentSubmitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Przetwarzanie...';
            processOrder(paymentMethod);
        });
    }

    function processOrder(paymentMethod) {
        const formData = new FormData(checkoutForm);
        formData.append('payment_method', paymentMethod); 
        if (paymentMethod !== 'Karta') {
            formData.delete('card_number'); 
            formData.delete('card_expiry'); 
            formData.delete('card_cvv');
        }
        if (paymentMethod !== 'Blik') { formData.delete('blik_code'); }
        fetch(basePath + 'process_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.text().then(text => {
                if (!response.ok) {
                    try { const errorData = JSON.parse(text); throw new Error(errorData.message || `Błąd serwera: ${response.status}.`); } 
                    catch (e) { throw new Error(`Błąd serwera: ${response.status}. Odpowiedź: ${text.substring(0, 200)}...`); }
                }
                try { return JSON.parse(text); } 
                catch (e) { 
                    throw new Error('Błąd: Otrzymano nieprawidłową odpowiedź (nie-JSON) z serwera.');
                }
            });
        })
        .then(data => {
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
                updatePaymentFields(); 
            }
        })
        .catch(error => {
            if (paymentResponseMessage) {
                paymentResponseMessage.style.display = 'block';
                paymentResponseMessage.className = 'flash-message error'; 
                paymentResponseMessage.innerHTML = `<p>${error.message || 'Wystąpił krytyczny błąd podczas przetwarzania zamówienia.'}</p>`;
            } else {
                 showSimpleModal(error.message || 'Wystąpił krytyczny błąd podczas przetwarzania zamówienia.', 'error');
            }
            if(mainPaymentSubmitButton) mainPaymentSubmitButton.disabled = false; 
            updatePaymentFields();
        });
    }

    function updateCartCountNavbar(newCount) {
        const cartCountElements = document.querySelectorAll('.cart-count'); 
        cartCountElements.forEach(el => { 
            if (el) { 
                el.textContent = newCount; 
                el.style.display = newCount > 0 ? 'inline-block' : 'none'; 
            } 
        });
    }
    
    if (checkoutForm) {
        updatePaymentFields(); 
    }

    // --- START: SPA Booking Modal Logic ---
    const spaBookingForm = document.getElementById('spaBookingForm');
    if (spaBookingForm) {
        const spaModal = document.getElementById('spaPackagesModal');
        const openSpaModalButton = document.getElementById('spaPackageButton');
        const closeSpaModalButton = spaModal ? spaModal.querySelector('#closeSpaModalBtn') : null; // Poprawiony selektor
        const confirmSpaSelectionButton = spaModal ? spaModal.querySelector('#confirmSpaSelection') : null;
        const packagesHorizontalListContainer = document.getElementById('spaPackagesHorizontalList');
        
        const hiddenInputSelectedItems = document.getElementById('selectedSpaItems'); 
        const hiddenInputCalculatedPrice = document.getElementById('calculatedSpaPrice'); 
        const formProductIdInput = document.getElementById('formProductId'); 
        
        const placeholderSpaProductInput = spaBookingForm.querySelector('input[name="item_details[placeholder_spa_product_id]"]');
        const placeholderSpaProductId = placeholderSpaProductInput ? placeholderSpaProductInput.value : null;

        const selectedTreatmentsDisplay = document.getElementById('selectedTreatmentsDisplay');

        function populateSpaModalDynamically(spaProducts) { // Zmieniono nazwę argumentu
            if (!packagesHorizontalListContainer) { 
                console.error("JS Error: Kontener #spaPackagesHorizontalList nie został znaleziony.");
                return; 
            }
            packagesHorizontalListContainer.innerHTML = ''; 
            if (!spaProducts || !Array.isArray(spaProducts) || spaProducts.length === 0) {
                packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding:20px;">Obecnie brak dostępnych zabiegów lub pakietów SPA do wyboru.</p>';
                return;
            }

            const categories = {};
            spaProducts.forEach(prod => { // Użycie argumentu funkcji
                if (!prod || typeof prod.name !== 'string' || typeof prod.price === 'undefined' || typeof prod.product_id === 'undefined' || typeof prod.category_name_from_db !== 'string') { return; }
                const price = parseFloat(prod.price);
                if (isNaN(price)) { return; }

                const categoryName = prod.category_name_from_db;
                if (!categories[categoryName]) {
                    categories[categoryName] = { name: categoryName, treatments: [] };
                }
                categories[categoryName].treatments.push({
                    product_id: prod.product_id,
                    name: `${prod.name} - ${price.toFixed(2)} PLN`,
                    price: price,
                    description: prod.description || ''
                });
            });
            
            const displayOrder = ["SPA - Pakiety Wellness", "SPA - Terapie Masażu", "SPA - Zabiegi na Twarz", "SPA - Zabiegi na Ciało"];
            const sortedCategories = {};
            displayOrder.forEach(catName => {
                if (categories[catName]) {
                    sortedCategories[catName] = categories[catName];
                    delete categories[catName];
                }
            });
            for (const catName in categories) {
                sortedCategories[catName] = categories[catName];
            }

            for (const catKey in sortedCategories) {
                const categoryData = sortedCategories[catKey];
                const categoryDiv = document.createElement('div');
                categoryDiv.classList.add('spa-package-category');
                
                const categoryTitle = document.createElement('h4');
                categoryTitle.textContent = categoryData.name.replace('SPA - ', '');
                categoryDiv.appendChild(categoryTitle);

                if (categoryData.treatments.length === 0) {
                    const noTreatmentsMsg = document.createElement('p');
                    noTreatmentsMsg.textContent = 'Brak ofert w tej kategorii.';
                    noTreatmentsMsg.style.cssText = 'font-size:0.85em; color:grey; padding:10px 0;';
                    categoryDiv.appendChild(noTreatmentsMsg);
                } else {
                    categoryData.treatments.forEach(treatment => {
                        const treatmentLabel = document.createElement('label');
                        treatmentLabel.classList.add('treatment-option-label');
                        treatmentLabel.title = treatment.description;

                        const treatmentInput = document.createElement('input');
                        if (categoryData.name === "SPA - Pakiety Wellness") {
                            treatmentInput.type = 'radio';
                            treatmentInput.name = 'spa_package_option';
                        } else {
                            treatmentInput.type = 'checkbox';
                            treatmentInput.name = 'spa_treatment_option';
                        }
                        treatmentInput.value = treatment.product_id;
                        treatmentInput.dataset.treatmentName = treatment.name;
                        treatmentInput.dataset.price = treatment.price;
                        
                        treatmentInput.addEventListener('change', function() {
                            if (this.type === 'radio' && this.checked) {
                                packagesHorizontalListContainer.querySelectorAll('input[type="checkbox"][name="spa_treatment_option"]').forEach(cb => cb.checked = false);
                            } else if (this.type === 'checkbox' && this.checked) {
                                packagesHorizontalListContainer.querySelectorAll('input[type="radio"][name="spa_package_option"]').forEach(rb => rb.checked = false);
                            }
                        });

                        treatmentLabel.appendChild(treatmentInput);
                        treatmentLabel.appendChild(document.createTextNode(` ${treatment.name}`));
                        categoryDiv.appendChild(treatmentLabel);
                    });
                }
                packagesHorizontalListContainer.appendChild(categoryDiv);
            }
        }

        if (openSpaModalButton && spaModal && packagesHorizontalListContainer) {
            openSpaModalButton.addEventListener('click', function() {
                // Używamy allSpaProductsFromPHP zamiast allSpaProductsFromPHPGlobal
                if (typeof allSpaProductsFromPHP !== 'undefined' && Array.isArray(allSpaProductsFromPHP)) {
                    populateSpaModalDynamically(allSpaProductsFromPHP); // Przekazujemy dane do funkcji
                } else {
                    if(packagesHorizontalListContainer) packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding:20px;">Błąd ładowania danych o zabiegach. Spróbuj odświeżyć stronę.</p>';
                    console.error("script.js: Zmienna allSpaProductsFromPHP nie jest zdefiniowana lub nie jest tablicą. Sprawdź czy jest poprawnie przekazywana z PHP w pliku spa_b.php.");
                }
                
                if (hiddenInputSelectedItems && packagesHorizontalListContainer) {
                    const previouslySelectedIds = hiddenInputSelectedItems.value.split(',').filter(id => id.trim() !== '');
                    if (packagesHorizontalListContainer) { 
                        packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"], input[name="spa_package_option"]').forEach(input => {
                            input.checked = previouslySelectedIds.includes(input.value);
                        });
                    }
                }
                spaModal.style.display = 'block';
            });
        } else {
            if(!openSpaModalButton) console.error("script.js: Nie znaleziono #spaPackageButton");
            if(!spaModal) console.error("script.js: Nie znaleziono #spaPackagesModal");
            if(!packagesHorizontalListContainer) console.error("script.js: Nie znaleziono #spaPackagesHorizontalList");
        }

        if (closeSpaModalButton && spaModal) { 
            closeSpaModalButton.onclick = () => { spaModal.style.display = 'none'; };
        }
        if (spaModal) { 
            spaModal.addEventListener('click', e => { if (e.target === spaModal) { spaModal.style.display = 'none'; } });
        }

        if (confirmSpaSelectionButton && spaModal && hiddenInputSelectedItems && packagesHorizontalListContainer && selectedTreatmentsDisplay && hiddenInputCalculatedPrice && formProductIdInput) {
            confirmSpaSelectionButton.onclick = function() {
                const selectedTreatmentIds = [];
                const selectedTreatmentsDataForDisplay = [];
                let totalSelectedPrice = 0;
                let isPackageSelected = false;

                const selectedPackageRadio = packagesHorizontalListContainer.querySelector('input[type="radio"][name="spa_package_option"]:checked');
                
                if (selectedPackageRadio) {
                    isPackageSelected = true;
                    selectedTreatmentIds.push(selectedPackageRadio.value);
                    selectedTreatmentsDataForDisplay.push(selectedPackageRadio.dataset.treatmentName);
                    totalSelectedPrice = parseFloat(selectedPackageRadio.dataset.price || 0);
                    formProductIdInput.value = selectedPackageRadio.value; 
                } else {
                    packagesHorizontalListContainer.querySelectorAll('input[type="checkbox"][name="spa_treatment_option"]:checked').forEach(cb => {
                        selectedTreatmentIds.push(cb.value);
                        selectedTreatmentsDataForDisplay.push(cb.dataset.treatmentName);
                        totalSelectedPrice += parseFloat(cb.dataset.price || 0);
                    });
                    if (selectedTreatmentIds.length > 0) {
                        formProductIdInput.value = placeholderSpaProductId || ''; 
                    } else {
                        formProductIdInput.value = ''; 
                    }
                }

                hiddenInputSelectedItems.value = selectedTreatmentIds.join(',');
                hiddenInputCalculatedPrice.value = totalSelectedPrice.toFixed(2);

                selectedTreatmentsDisplay.innerHTML = ''; 
                if (selectedTreatmentsDataForDisplay.length > 0) {
                    const titleEl = document.createElement('h5');
                    titleEl.textContent = isPackageSelected ? 'Twój wybrany pakiet SPA:' : 'Twoje wybrane zabiegi SPA:';
                    selectedTreatmentsDisplay.appendChild(titleEl);
                    const ul = document.createElement('ul');
                    selectedTreatmentsDataForDisplay.forEach(nameWithPrice => {
                        const li = document.createElement('li');
                        li.textContent = nameWithPrice;
                        ul.appendChild(li);
                    });
                    selectedTreatmentsDisplay.appendChild(ul);
                    const priceEl = document.createElement('p');
                    priceEl.innerHTML = `<strong>Łączna kwota: ${totalSelectedPrice.toFixed(2)} PLN</strong>`;
                    selectedTreatmentsDisplay.appendChild(priceEl);
                    selectedTreatmentsDisplay.style.display = 'block';
                    if (openSpaModalButton) openSpaModalButton.textContent = `Wybrano (${selectedTreatmentIds.length}) Zmień wybór`;
                } else {
                    selectedTreatmentsDisplay.style.display = 'none';
                    if (openSpaModalButton) openSpaModalButton.textContent = 'Wybierz Zabiegi lub Gotowy Pakiet';
                    // Jeśli nic nie wybrano, a formularz mógłby być wysłany, przywróć domyślne ID produktu (placeholder), jeśli istnieje.
                    // W przeciwnym razie zostaw puste, aby walidacja formularza zadziałała.
                    formProductIdInput.value = (placeholderSpaProductId && selectedTreatmentIds.length === 0) ? placeholderSpaProductId : ''; 
                }
                spaModal.style.display = 'none'; 
            };
        }
        
        if (spaBookingForm) { 
            spaBookingForm.addEventListener('submit', function(event){
                if (!hiddenInputSelectedItems || hiddenInputSelectedItems.value.trim() === '') {
                    if (typeof showSimpleModal === 'function') showSimpleModal('Proszę wybrać przynajmniej jeden zabieg lub pakiet SPA z listy.', 'error');
                    else alert('Proszę wybrać przynajmniej jeden zabieg lub pakiet SPA z listy.');
                    event.preventDefault(); return;
                }
                if (!formProductIdInput.value) {
                    // Ten warunek jest ważny, jeśli placeholderSpaProductId nie został znaleziony i nic nie wybrano
                    if (typeof showSimpleModal === 'function') showSimpleModal('Błąd: Brak ID produktu do dodania do koszyka. Wybierz ponownie zabiegi/pakiet lub skontaktuj się z obsługą, jeśli problem się powtarza.', 'error');
                    else alert('Błąd: Brak ID produktu do dodania do koszyka.');
                    event.preventDefault(); return;
                }
            });
        }
    } 
    // --- END: SPA Booking Modal Logic ---

    const opinionsListCarousel = document.querySelector('.opinions-list-carousel');
    if (opinionsListCarousel) {
        const opinionCards = Array.from(opinionsListCarousel.querySelectorAll('.opinion-card-carousel'));
        const prevOpinionBtn = document.getElementById('prevOpinionBtn');
        const nextOpinionBtn = document.getElementById('nextOpinionBtn');
        let currentOpinionIndex = 0;

        function showOpinion(index) {
            opinionCards.forEach((card, i) => {
                card.classList.remove('active'); 
                card.style.display = 'none'; 
                if (i === index) {
                    card.classList.add('active');
                    card.style.display = 'block'; 
                }
            });

            if (prevOpinionBtn && nextOpinionBtn && opinionCards.length > 0) {
                prevOpinionBtn.disabled = index === 0;
                nextOpinionBtn.disabled = index === opinionCards.length - 1;
            } else if (prevOpinionBtn && nextOpinionBtn) { 
                prevOpinionBtn.style.display = 'none';
                nextOpinionBtn.style.display = 'none';
                 const carouselNavContainer = document.querySelector('.carousel-navigation');
                 if(carouselNavContainer) carouselNavContainer.style.display = 'none';
            }
        }

        if (prevOpinionBtn) {
            prevOpinionBtn.addEventListener('click', () => {
                if (currentOpinionIndex > 0) {
                    currentOpinionIndex--;
                    showOpinion(currentOpinionIndex);
                }
            });
        }

        if (nextOpinionBtn) {
            nextOpinionBtn.addEventListener('click', () => {
                if (currentOpinionIndex < opinionCards.length - 1) {
                    currentOpinionIndex++;
                    showOpinion(currentOpinionIndex);
                }
            });
        }
        
        if (opinionCards.length > 0) {
            showOpinion(currentOpinionIndex); 
        } else {
            const carouselNavContainer = document.querySelector('.carousel-navigation');
            if (carouselNavContainer) {
                carouselNavContainer.style.display = 'none';
            }
        }
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
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ładowanie...';

            fetch(`${basePath}load_more_opinions.php?page=${currentPage}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.opinions_html && Array.isArray(data.opinions_html)) {
                        if (data.opinions_html.length > 0) {
                            data.opinions_html.forEach(opinionHTML => {
                                opinionsListContainer.insertAdjacentHTML('beforeend', opinionHTML);
                            });
                        }
                        
                        if (data.has_more) {
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii';
                        } else {
                            this.style.display = 'none'; 
                            const noMoreMsg = document.createElement('p');
                            noMoreMsg.textContent = 'To już wszystkie opinie.';
                            noMoreMsg.style.textAlign = 'center';
                            noMoreMsg.style.marginTop = '20px';
                            noMoreMsg.style.color = 'var(--text-muted-color)';
                            if (this.parentElement) this.parentElement.appendChild(noMoreMsg);
                        }
                    } else if (data.success && (!data.opinions_html || data.opinions_html.length === 0) && !data.has_more) { // Poprawiony warunek
                        this.style.display = 'none';
                        const noMoreMsg = document.createElement('p');
                        noMoreMsg.textContent = 'To już wszystkie opinie.';
                        noMoreMsg.style.textAlign = 'center';
                        noMoreMsg.style.marginTop = '20px';
                        noMoreMsg.style.color = 'var(--text-muted-color)';
                        if (this.parentElement) this.parentElement.appendChild(noMoreMsg);
                    }
                    else {
                        showSimpleModal(data.message || 'Nie udało się załadować więcej opinii.', 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii';
                        currentPage--; 
                    }
                })
                .catch(error => {
                    showSimpleModal('Wystąpił błąd podczas komunikacji z serwerem.', 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-chevron-down"></i> Pokaż więcej opinii';
                    currentPage--; 
                })
                .finally(() => {
                    if(loadingOpinionsIndicator) loadingOpinionsIndicator.style.display = 'none';
                });
        });
    }

    const toggleAddOpinionFormBtn = document.getElementById('toggleAddOpinionFormBtn');
    const addOpinionFormContainer = document.getElementById('addOpinionFormContainer');

    if (toggleAddOpinionFormBtn && addOpinionFormContainer) {
        toggleAddOpinionFormBtn.addEventListener('click', function() {
            const isVisible = addOpinionFormContainer.style.display === 'block';
            addOpinionFormContainer.style.display = isVisible ? 'none' : 'block';
            this.innerHTML = isVisible ? '<i class="fas fa-plus-circle"></i> Dodaj swoją opinię' : '<i class="fas fa-minus-circle"></i> Ukryj formularz';
            if (!isVisible) {
                 addOpinionFormContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    const newOpinionForm = document.getElementById('newOpinionForm');
    if (newOpinionForm) {
        newOpinionForm.addEventListener('submit', function(event) {
            const ratingChecked = this.querySelector('input[name="rating"]:checked');
            if (!ratingChecked) {
                event.preventDefault();
                showSimpleModal('Proszę wybrać ocenę (ilość gwiazdek).', 'error');
                const ratingLabel = this.querySelector('.rating-form-label');
                if (ratingLabel) {
                    ratingLabel.scrollIntoView({behavior: 'smooth', block: 'center'});
                    ratingLabel.style.color = 'red'; 
                    setTimeout(() => { ratingLabel.style.color = ''; }, 3000);
                }
                return false;
            }
        });
    }
}); // Koniec DOMContentLoaded