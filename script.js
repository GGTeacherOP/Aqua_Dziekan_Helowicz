// script.js
document.addEventListener('DOMContentLoaded', function () {
    const isLoggedIn = typeof isLoggedInFromPHP !== 'undefined' ? isLoggedInFromPHP : false;
    const baseAppPath = typeof basePathJS !== 'undefined' ? basePathJS : '/';

    const logoutButton = document.getElementById('logoutButton');
    const authGuestModalContainer = document.getElementById('authGuestModalContainer');

    function showSimpleModal(message, type = 'info', duration = 4000) {
        const existingModal = document.getElementById('simpleMessageModal');
        if (existingModal) existingModal.remove();
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
        modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) modalOverlay.remove(); });
        if (duration > 0) setTimeout(() => { if (document.getElementById('simpleMessageModal')) modalOverlay.remove(); }, duration);
    }

    if ((window.location.pathname.endsWith('index.php') || window.location.pathname === baseAppPath || window.location.pathname === baseAppPath + 'index.php')) {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout_status') === 'success') {
            showSimpleModal("Pomyślnie wylogowano", 'success', 4000);
            if (history.replaceState) {
                const cleanURL = window.location.pathname + window.location.search.replace(/[?&]logout_status=success\b&?/, '').replace(/^&/, '?').replace(/\?$/, '');
                history.replaceState({path: cleanURL}, '', cleanURL);
            }
        }
    }

    if (logoutButton) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();
            window.location.href = baseAppPath + 'logout.php';
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const hrefAttribute = this.getAttribute('href');
            if (hrefAttribute.length > 1 && hrefAttribute.startsWith('#')) {
                const targetElement = document.querySelector(hrefAttribute);
                if (targetElement) { e.preventDefault(); targetElement.scrollIntoView({ behavior: 'smooth' }); }
            }
        });
    });

    function showAuthGuestModal(options = {}) {
        const { productId, itemDetailsJsonString, formToSubmit, isButtonTrigger } = options;
        if (!authGuestModalContainer) { console.error("Brakuje kontenera #authGuestModalContainer"); return; }
        const existingModal = authGuestModalContainer.querySelector('#authGuestModal');
        if (existingModal) existingModal.remove();
        let redirectParams = [];
        if (productId) redirectParams.push(`product_id=${productId}`);
        if (itemDetailsJsonString) redirectParams.push(`details=${encodeURIComponent(itemDetailsJsonString)}`);
        const currentPath = window.location.pathname + window.location.search;
        const cleanCurrentPath = currentPath.replace(/[?&]logout_status=success\b&?/, '').replace(/^&/, '?').replace(/\?$/, '');
        redirectParams.push(`redirect=${encodeURIComponent(cleanCurrentPath)}`);
        const loginRedirectUrl = `${baseAppPath}login.php?action=add_to_cart_after_login&${redirectParams.join('&')}`;
        const signupRedirectUrl = `${baseAppPath}signup.php?action=add_to_cart_after_register&${redirectParams.join('&')}`;
        authGuestModalContainer.innerHTML = `
            <div id="authGuestModal" class="modal" style="display: block; z-index: 2000;">
                <div class="modal-content" style="max-width: 450px; text-align: center;">
                    <span class="close-button" id="closeAuthGuestModal" style="cursor:pointer;">&times;</span>
                    <h3>Aby kontynuować...</h3><p>Proszę wybrać jedną z opcji:</p>
                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                        <button id="modalLoginBtn" class="cta-button" style="width: 100%;">Zaloguj się</button>
                        <button id="modalRegisterBtn" class="cta-button register-button" style="width: 100%;">Zarejestruj się</button>
                        <button id="modalGuestBtn" class="cta-button secondary-cta" style="width: 100%;">Kontynuuj jako gość</button>
                    </div></div></div>`;
        document.getElementById('closeAuthGuestModal').onclick = () => authGuestModalContainer.innerHTML = '';
        document.getElementById('modalLoginBtn').onclick = () => window.location.href = loginRedirectUrl;
        document.getElementById('modalRegisterBtn').onclick = () => window.location.href = signupRedirectUrl;
        document.getElementById('modalGuestBtn').onclick = () => {
            if (formToSubmit) {
                let guestInput = formToSubmit.querySelector('input[name="continue_as_guest"]');
                if (!guestInput) { guestInput = document.createElement('input'); guestInput.type = 'hidden'; guestInput.name = 'continue_as_guest'; formToSubmit.appendChild(guestInput); }
                guestInput.value = '1'; formToSubmit.submit();
            } else if (isButtonTrigger && productId) {
                const guestForm = document.createElement('form'); guestForm.method = 'POST'; guestForm.action = `${baseAppPath}cart_actions.php`;
                guestForm.innerHTML = `<input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="product_id" value="${productId}"><input type="hidden" name="quantity" value="1"><input type="hidden" name="continue_as_guest" value="1">${itemDetailsJsonString ? `<input type="hidden" name="item_details_json_string" value='${itemDetailsJsonString}'>` : ''}`;
                document.body.appendChild(guestForm); guestForm.submit();
            }
            authGuestModalContainer.innerHTML = '';
        };
        const modalElement = document.getElementById('authGuestModal');
        if (modalElement) window.onclick = e => { if (e.target == modalElement) authGuestModalContainer.innerHTML = ''; };
    }

    document.querySelectorAll('form[action*="cart_actions.php"], button.add-to-cart-button, a.add-to-cart-link').forEach(element => {
        const isForm = element.tagName === 'FORM';
        element.addEventListener(isForm ? 'submit' : 'click', function(event) {
            if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
                let isGuestContinuation = isForm ? !!this.querySelector('input[name="continue_as_guest"][value="1"]') : false;
                if (!isGuestContinuation) {
                    event.preventDefault();
                    let productId = null, itemDetails = {}, itemDetailsJsonString = null;
                    if (isForm) {
                        const pidField = this.querySelector('input[name="product_id"], select[name="product_id"]'); if (pidField) productId = pidField.value;
                        this.querySelectorAll('input[name^="item_details["], select[name^="item_details["], textarea[name^="item_details["]').forEach(di => { let km = di.name.match(/item_details\[(.*?)\]/); if (km && km[1]) itemDetails[km[1]] = di.value; });
                        const existingJsonInput = this.querySelector('input[name="item_details_json_string"]'); if (existingJsonInput && existingJsonInput.value) itemDetailsJsonString = existingJsonInput.value;
                    } else { productId = this.dataset.productId; if (this.dataset.itemDetails) try { itemDetails = JSON.parse(this.dataset.itemDetails); } catch(e) { console.error("Błędny JSON w data-item-details", e); }}
                    if (!itemDetailsJsonString && Object.keys(itemDetails).length > 0) { const sd = {}; Object.keys(itemDetails).sort().forEach(k => sd[k] = itemDetails[k]); itemDetailsJsonString = JSON.stringify(sd); }
                    showAuthGuestModal({ productId, itemDetailsJsonString, formToSubmit: isForm ? this : null, isButtonTrigger: !isForm });
                }
            }
        });
    });

    const spaBookingForm = document.getElementById('spaBookingForm');
    if (spaBookingForm) {
        const modal = document.getElementById('spaPackagesModal');
        const openModalButton = document.getElementById('spaPackageButton');
        const closeModalButton = modal ? modal.querySelector('.close-button, #closeSpaModalBtn') : null;
        const confirmSelectionButton = document.getElementById('confirmSpaSelection');
        const packagesHorizontalListContainer = document.getElementById('spaPackagesHorizontalList');
        const hiddenInputSelectedPackages = document.getElementById('selectedSpaPackages');
        const selectedTreatmentsDisplay = document.getElementById('selectedTreatmentsDisplay');

        console.log("SPA Booking Form found. Initializing SPA modal logic.");
        if (!modal) console.error("SPA Modal (#spaPackagesModal) not found!");
        if (!openModalButton) console.error("SPA Open Modal Button (#spaPackageButton) not found!");
        if (!packagesHorizontalListContainer) console.error("SPA Packages List Container (#spaPackagesHorizontalList) not found!");

        function populateSpaModalDynamically(allSpaProducts) {
            console.log("populateSpaModalDynamically called with data:", JSON.parse(JSON.stringify(allSpaProducts)));
            if (!packagesHorizontalListContainer) {
                console.error("CRITICAL: packagesHorizontalListContainer is null in populateSpaModalDynamically.");
                return;
            }
            packagesHorizontalListContainer.innerHTML = '';

            if (!allSpaProducts || !Array.isArray(allSpaProducts) || allSpaProducts.length === 0) {
                console.warn("No SPA products data provided (allSpaProductsFromPHP is empty or not an array). Displaying 'no treatments' message.");
                packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding: 20px;">Obecnie brak dostępnych zabiegów do wyboru.</p>';
                return;
            }

            // Definicja naszych 4 docelowych kategorii i słów kluczowych
            // !!! DOSTOSUJ SŁOWA KLUCZOWE DO NAZW TWOICH PRODUKTÓW W BAZIE DANYCH !!!
            const targetCategories = {
                "SPA_PAKIETY":  { name: "Ekskluzywne Pakiety Wellness",  treatments: [], keywords: ["pakiet", "harmonia", "zmysłów", "królewski relaks", "dwojga"] },
                "SPA_MASAZE":   { name: "Kojące Terapie Masażu",       treatments: [], keywords: ["masaż", "lomi", "kamieniami", "klasyczny", "relaksacyjny", "wulkanicznymi"] },
                "SPA_TWARZ":    { name: "Odmładzające Zabiegi na Twarz", treatments: [], keywords: ["twarz", "hydraboost", "gold therapy", "liftingujący", "nawilżający", "oczyszczający", "anti-aging", "problematycznej"] },
                "SPA_CIALO":    { name: "Rozluźniające Zabiegi na Ciało",treatments: [], keywords: ["peeling", "okład", "rytuał z herbatą", "czekoladowy", "ciała", "detoksykujący", "cukrowy", "aromatyczny"] }
            };

            allSpaProducts.forEach(prod => {
                if (!prod || typeof prod.name !== 'string' || typeof prod.price === 'undefined' || typeof prod.product_id === 'undefined' || typeof prod.category_name_from_db !== 'string') {
                    console.error("Skipping product due to incomplete data (name, price, product_id, or category_name_from_db missing):", prod);
                    return;
                }
                const price = parseFloat(prod.price);
                if (isNaN(price)) {
                    console.error("Skipping product due to invalid price:", prod.name, prod.price);
                    return;
                }

                let assignedCategoryKey = null;
                const productNameLower = prod.name.toLowerCase();

                // 1. Jeśli produkt w bazie jest w kategorii "SPA - Pakiety Wellness" (lub "SPA - Pakiety"), przypisz go bezpośrednio
                if (prod.category_name_from_db === 'SPA - Pakiety Wellness' || prod.category_name_from_db === 'SPA - Pakiety') {
                    assignedCategoryKey = "SPA_PAKIETY";
                } 
                // 2. Jeśli produkt w bazie jest w jednej ze szczegółowych kategorii, przypisz go bezpośrednio
                else if (prod.category_name_from_db === 'SPA - Terapie Masażu') {
                    assignedCategoryKey = "SPA_MASAZE";
                } else if (prod.category_name_from_db === 'SPA - Zabiegi na Twarz') {
                    assignedCategoryKey = "SPA_TWARZ";
                } else if (prod.category_name_from_db === 'SPA - Zabiegi na Ciało') {
                    assignedCategoryKey = "SPA_CIALO";
                }
                // 3. Jeśli produkt był w ogólnej kategorii "SPA - Zabiegi" (na wypadek gdybyś nie przeniósł wszystkich), spróbuj po słowach kluczowych
                else if (prod.category_name_from_db === 'SPA - Zabiegi') {
                    if (targetCategories.SPA_TWARZ.keywords.some(keyword => productNameLower.includes(keyword.toLowerCase()))) {
                        assignedCategoryKey = "SPA_TWARZ";
                    } else if (targetCategories.SPA_CIALO.keywords.some(keyword => productNameLower.includes(keyword.toLowerCase()))) {
                        assignedCategoryKey = "SPA_CIALO";
                    } else if (targetCategories.SPA_MASAZE.keywords.some(keyword => productNameLower.includes(keyword.toLowerCase()))) {
                        assignedCategoryKey = "SPA_MASAZE";
                    }
                }


                if (assignedCategoryKey && targetCategories[assignedCategoryKey]) {
                    targetCategories[assignedCategoryKey].treatments.push({
                        product_id: prod.product_id,
                        name: `${prod.name} - ${price.toFixed(2)} PLN`,
                        price: price
                    });
                } else {
                    console.warn("Produkt nie został przypisany do żadnej z 4 docelowych kategorii:", prod.name, "(Oryginalna kategoria z DB:", prod.category_name_from_db, ")");
                }
            });

            console.log("Processed targetCategories after grouping:", targetCategories);

            const displayOrder = ["SPA_PAKIETY", "SPA_MASAZE", "SPA_TWARZ", "SPA_CIALO"];
            let productsDisplayed = false;

            displayOrder.forEach(catKey => {
                const categoryData = targetCategories[catKey];
                if (!categoryData) {
                     console.warn(`Target category key "${catKey}" not found in targetCategories object.`);
                     return;
                }

                console.log("Creating category section for:", categoryData.name);
                const categoryDiv = document.createElement('div');
                categoryDiv.classList.add('spa-package-category');

                const categoryTitle = document.createElement('h4');
                categoryTitle.textContent = categoryData.name;
                categoryDiv.appendChild(categoryTitle);

                if (categoryData.treatments.length === 0) {
                    const noTreatmentsMsg = document.createElement('p');
                    noTreatmentsMsg.textContent = 'Brak ofert w tej kategorii.';
                    noTreatmentsMsg.style.fontSize = '0.85em'; noTreatmentsMsg.style.color = 'grey'; noTreatmentsMsg.style.padding = '10px 0';
                    categoryDiv.appendChild(noTreatmentsMsg);
                } else {
                    productsDisplayed = true;
                    categoryData.treatments.forEach(treatment => {
                        const treatmentLabel = document.createElement('label');
                        treatmentLabel.classList.add('treatment-option-label');
                        const treatmentCheckbox = document.createElement('input');
                        treatmentCheckbox.type = 'checkbox'; treatmentCheckbox.name = 'spa_treatment_option';
                        treatmentCheckbox.value = treatment.product_id; treatmentCheckbox.dataset.treatmentName = treatment.name; treatmentCheckbox.dataset.price = treatment.price;
                        treatmentLabel.appendChild(treatmentCheckbox);
                        treatmentLabel.appendChild(document.createTextNode(` ${treatment.name}`));
                        categoryDiv.appendChild(treatmentLabel);
                    });
                }
                packagesHorizontalListContainer.appendChild(categoryDiv);
            });
            
            if (!productsDisplayed && allSpaProducts.length > 0) {
                 packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding: 20px;">Nie udało się dopasować zabiegów do kategorii. Sprawdź słowa kluczowe w skrypcie oraz nazwy kategorii w bazie danych.</p>';
            }
            console.log("Finished populating modal. Container content length:", packagesHorizontalListContainer.innerHTML.length);
        }
        
        if (typeof allSpaProductsFromPHP !== 'undefined') {
            console.log("Initial allSpaProductsFromPHP data:", JSON.parse(JSON.stringify(allSpaProductsFromPHP)));
        } else {
            console.warn("allSpaProductsFromPHP is not defined globally when SPA logic initializes.");
        }

        if (openModalButton && modal) {
            openModalButton.onclick = function() {
                console.log("Open SPA Modal button clicked.");
                if (typeof allSpaProductsFromPHP !== 'undefined' && Array.isArray(allSpaProductsFromPHP)) {
                    populateSpaModalDynamically(allSpaProductsFromPHP);
                } else {
                    console.error("Cannot populate SPA modal: allSpaProductsFromPHP is undefined or not an array.");
                    if(packagesHorizontalListContainer) packagesHorizontalListContainer.innerHTML = '<p style="text-align:center; color:grey; padding:20px;">Brak danych o zabiegach (problem z allSpaProductsFromPHP).</p>';
                }
                if (hiddenInputSelectedPackages && packagesHorizontalListContainer) {
                    const previouslySelectedIds = hiddenInputSelectedPackages.value.split(',').filter(id => id.trim() !== '');
                    packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"]').forEach(cb => { cb.checked = previouslySelectedIds.includes(cb.value); });
                }
                modal.style.display = 'block';
            }
        }

        if (closeModalButton && modal) closeModalButton.onclick = () => modal.style.display = 'none';
        if (modal) modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });

        if (confirmSelectionButton && modal) {
            confirmSelectionButton.onclick = function() {
                if (!hiddenInputSelectedPackages || !packagesHorizontalListContainer || !selectedTreatmentsDisplay) return;
                const selectedTreatmentIds = [], selectedTreatmentsDataForDisplay = []; let totalSelectedPrice = 0;
                packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"]:checked').forEach(cb => {
                    selectedTreatmentIds.push(cb.value); selectedTreatmentsDataForDisplay.push(cb.dataset.treatmentName); totalSelectedPrice += parseFloat(cb.dataset.price || 0);
                });
                hiddenInputSelectedPackages.value = selectedTreatmentIds.join(',');
                selectedTreatmentsDisplay.innerHTML = '';
                if (selectedTreatmentsDataForDisplay.length > 0) {
                    const titleEl = document.createElement('h5'); titleEl.textContent = 'Twoje wybrane zabiegi:'; selectedTreatmentsDisplay.appendChild(titleEl);
                    const ul = document.createElement('ul'); selectedTreatmentsDataForDisplay.forEach(n => { const li = document.createElement('li'); li.textContent = n; ul.appendChild(li); });
                    selectedTreatmentsDisplay.appendChild(ul);
                    const priceEl = document.createElement('p'); priceEl.innerHTML = `<strong>Suma za wybrane: ${totalSelectedPrice.toFixed(2)} PLN</strong>`; selectedTreatmentsDisplay.appendChild(priceEl);
                    selectedTreatmentsDisplay.style.display = 'block';
                    if(openModalButton) openModalButton.textContent = `Wybrano (${selectedTreatmentIds.length}) Zmień`;
                } else { selectedTreatmentsDisplay.style.display = 'none'; if(openModalButton) openModalButton.textContent = 'Wybierz Masaż lub Pakiet'; }
                modal.style.display = 'none';
            }
        }
        
        spaBookingForm.addEventListener('submit', function(event){
            if(!hiddenInputSelectedPackages || hiddenInputSelectedPackages.value.trim() === ''){ showSimpleModal('Proszę wybrać przynajmniej jeden zabieg lub pakiet.', 'error'); event.preventDefault(); return; }
            const spaItemDetails = {
                reservation_type: 'spa_treatment_package', 
                selected_treatments_ids_string: hiddenInputSelectedPackages.value, 
                booking_name: this.querySelector('input[name="item_details[booking_name]"]').value, 
                booking_email: this.querySelector('input[name="item_details[booking_email]"]').value,
                booking_phone: this.querySelector('input[name="item_details[booking_phone]"]').value, 
                treatment_date: this.querySelector('input[name="item_details[treatment_date]"]').value,
                treatment_time: this.querySelector('input[name="item_details[treatment_time]"]').value, 
                notes: this.querySelector('textarea[name="item_details[notes]"]').value
            };
            
            this.querySelectorAll('input[name="item_details_json_string"]').forEach(el => el.remove());

            let detailsInput = document.createElement('input');
            detailsInput.type = 'hidden';
            detailsInput.name = 'item_details_json_string'; 
            detailsInput.value = JSON.stringify(spaItemDetails);
            this.appendChild(detailsInput);
            
            console.log("Formularz SPA wysyłany z product_id:", this.querySelector('input[name="product_id"]').value, "i item_details_json_string:", detailsInput.value);
        });
    }

    const paymentRedirectForm = document.getElementById('paymentRedirectForm');
    if (paymentRedirectForm) {
        const paymentModal = document.getElementById('paymentModal'); 
        const closePaymentModalButton = paymentModal ? paymentModal.querySelector('#closePaymentModal') : null;
        if (paymentModal && closePaymentModalButton) closePaymentModalButton.onclick = () => paymentModal.style.display = 'none';
        if (paymentModal) paymentModal.addEventListener('click', e => { if (e.target === paymentModal) paymentModal.style.display = 'none'; });
    }

}); // Koniec DOMContentLoaded