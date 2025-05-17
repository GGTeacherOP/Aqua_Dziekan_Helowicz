document.addEventListener('DOMContentLoaded', function () {
    const loginButton = document.getElementById('loginButton');
    const registerButton = document.getElementById('registerButton');
    const logoutButton = document.getElementById('logoutButton');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    const currentPage = window.location && window.location.pathname ? window.location.pathname.split('/').pop() : '';

    const mainIndexHtmlPath = 'index.html';
    const mainLoginHtmlPath = 'login.html';

    function updateNavButtons() {
        if (localStorage.getItem('isLoggedIn') === 'true') {
            if (loginButton) loginButton.style.display = 'none';
            if (registerButton) registerButton.style.display = 'none';
            if (logoutButton) logoutButton.style.display = 'inline-block';
        } else {
            if (loginButton) loginButton.style.display = 'inline-block';
            if (registerButton) registerButton.style.display = 'inline-block';
            if (logoutButton) logoutButton.style.display = 'none';
        }
    }

    function getPathToRoot() {
        if (window.location.pathname.includes('_assets/')) {
            return '../';
        }
        return '';
    }
    const pathToRoot = getPathToRoot();

    if (loginForm) {
        if (localStorage.getItem('isLoggedIn') === 'true' && currentPage === 'login.html') {
            window.location.href = mainIndexHtmlPath;
        }
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = mainIndexHtmlPath;
        });
    }

    if (signupForm) {
        if (localStorage.getItem('isLoggedIn') === 'true' && currentPage === 'signup.html') {
            window.location.href = mainIndexHtmlPath;
        }
        signupForm.addEventListener('submit', function (event) {
            event.preventDefault();
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = mainIndexHtmlPath;
        });
    }

    if (logoutButton) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();
            localStorage.removeItem('isLoggedIn');
            window.location.href = pathToRoot + mainLoginHtmlPath;
        });
    }

    if (currentPage !== 'login.html' && currentPage !== 'signup.html') {
        updateNavButtons();
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const hrefAttribute = this.getAttribute('href');
            if (hrefAttribute.length > 1 && hrefAttribute.startsWith('#')) {
                const targetElement = document.querySelector(hrefAttribute);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    const spaPageIdentifier = document.getElementById('spaBookingForm');
    if (spaPageIdentifier) {
        const modal = document.getElementById('spaPackagesModal');
        const openModalButton = document.getElementById('spaPackageButton');
        const closeModalButton = modal.querySelector('.close-button');
        const confirmSelectionButton = document.getElementById('confirmSpaSelection');
        const packagesHorizontalListContainer = document.getElementById('spaPackagesHorizontalList');
        const hiddenInputSelectedPackages = document.getElementById('selectedSpaPackages');
        const selectedTreatmentsDisplay = document.getElementById('selectedTreatmentsDisplay');

        const spaTreatmentCategories = [
            { id: 'terapie_masazu', name: 'Kojące Terapie Masażu', treatments: [ { id: 'masaz_relaksacyjny', name: 'Masaż Relaksacyjny Klasyczny - 250 PLN', price: 250 }, { id: 'masaz_lomi_lomi', name: 'Masaż Lomi Lomi Nui - 320 PLN', price: 320 }, { id: 'masaz_kamienie', name: 'Masaż Gorącymi Kamieniami Wulkanicznymi - 350 PLN', price: 350 } ] },
            { id: 'zabiegi_twarz', name: 'Odmładzające Zabiegi na Twarz', treatments: [ { id: 'zabieg_hydraboost', name: 'Intensywnie Nawilżający Zabieg HydraBoost - 280 PLN', price: 280 }, { id: 'zabieg_gold_therapy', name: 'Liftingujący Zabieg Anti-Aging Gold Therapy - 350 PLN', price: 350 }, { id: 'zabieg_oczyszczajacy', name: 'Oczyszczający Zabieg dla Skóry Problematycznej - 260 PLN', price: 260 } ] },
            { id: 'zabiegi_cialo', name: 'Rozluźniające Zabiegi na Ciało', treatments: [ { id: 'peeling_cukrowy', name: 'Aromatyczny Peeling Cukrowy Całego Ciała - 190 PLN', price: 190 }, { id: 'oklad_czekoladowy', name: 'Odżywczy Okład Czekoladowy - 330 PLN', price: 330 }, { id: 'rytual_zielona_herbata', name: 'Detoksykujący Rytuał z Zieloną Herbatą - 380 PLN', price: 380 } ] },
            { id: 'pakiety_wellness', name: 'Ekskluzywne Pakiety Wellness', treatments: [ { id: 'pakiet_harmonia', name: 'Pakiet "Harmonia Zmysłów" (Masaż + Zabieg na Twarz) - 500 PLN', price: 500 }, { id: 'pakiet_krolewski', name: 'Pakiet "Królewski Relaks" (Peeling + Okład + Masaż) - 680 PLN', price: 680 }, { id: 'pakiet_dla_dwojga', name: 'Romantyczny Rytuał dla Dwojga - 850 PLN', price: 850 } ] }
        ];

        function populateSpaModal() {
            packagesHorizontalListContainer.innerHTML = '';
            spaTreatmentCategories.forEach(category => {
                const categoryDiv = document.createElement('div');
                categoryDiv.classList.add('spa-package-category');
                categoryDiv.dataset.categoryId = category.id;
                const categoryTitle = document.createElement('h4');
                categoryTitle.textContent = category.name;
                categoryDiv.appendChild(categoryTitle);
                const selectAllLabel = document.createElement('label');
                selectAllLabel.classList.add('select-all-package-label');
                const selectAllCheckbox = document.createElement('input');
                selectAllCheckbox.type = 'checkbox';
                selectAllCheckbox.dataset.action = 'select-all';
                selectAllCheckbox.dataset.category = category.id;
                selectAllLabel.appendChild(selectAllCheckbox);
                selectAllLabel.appendChild(document.createTextNode(' Zaznacz cały pakiet'));
                categoryDiv.appendChild(selectAllLabel);
                category.treatments.forEach(treatment => {
                    const treatmentLabel = document.createElement('label');
                    treatmentLabel.classList.add('treatment-option-label');
                    const treatmentCheckbox = document.createElement('input');
                    treatmentCheckbox.type = 'checkbox';
                    treatmentCheckbox.id = `treatment_${treatment.id}`;
                    treatmentCheckbox.name = 'spa_treatment_option';
                    treatmentCheckbox.value = treatment.id;
                    treatmentCheckbox.dataset.category = category.id;
                    treatmentCheckbox.dataset.treatmentName = treatment.name.split(' - ')[0];
                    treatmentLabel.appendChild(treatmentCheckbox);
                    treatmentLabel.appendChild(document.createTextNode(` ${treatment.name}`));
                    categoryDiv.appendChild(treatmentLabel);
                });
                packagesHorizontalListContainer.appendChild(categoryDiv);
            });
            addModalEventListeners();
        }

        function addModalEventListeners() {
            const allSelectAllCheckboxes = packagesHorizontalListContainer.querySelectorAll('input[data-action="select-all"]');
            allSelectAllCheckboxes.forEach(cb => cb.addEventListener('change', handleSelectAllChange));
            const allTreatmentCheckboxes = packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"]');
            allTreatmentCheckboxes.forEach(cb => cb.addEventListener('change', handleTreatmentChange));
        }

        function handleSelectAllChange(event) {
            const masterCheckbox = event.target;
            const categoryId = masterCheckbox.dataset.category;
            const treatmentCheckboxesInPackage = packagesHorizontalListContainer.querySelectorAll(`input[name="spa_treatment_option"][data-category="${categoryId}"]`);
            treatmentCheckboxesInPackage.forEach(treatmentCb => treatmentCb.checked = masterCheckbox.checked);
        }

        function handleTreatmentChange(event) {
            const treatmentCheckbox = event.target;
            const categoryId = treatmentCheckbox.dataset.category;
            const masterCheckboxForCategory = packagesHorizontalListContainer.querySelector(`input[data-action="select-all"][data-category="${categoryId}"]`);
            const allTreatmentCheckboxesInPackage = packagesHorizontalListContainer.querySelectorAll(`input[name="spa_treatment_option"][data-category="${categoryId}"]`);
            let allChecked = true;
            allTreatmentCheckboxesInPackage.forEach(cb => { if (!cb.checked) allChecked = false; });
            masterCheckboxForCategory.checked = allChecked;
        }

        if (openModalButton) {
            openModalButton.onclick = function() {
                populateSpaModal();
                const previouslySelectedIds = hiddenInputSelectedPackages.value.split(',').filter(id => id);
                previouslySelectedIds.forEach(id => {
                    const checkboxToRestore = packagesHorizontalListContainer.querySelector(`input[value="${id}"][name="spa_treatment_option"]`);
                    if (checkboxToRestore) {
                        checkboxToRestore.checked = true;
                        handleTreatmentChange({target: checkboxToRestore});
                    }
                });
                modal.style.display = 'block';
            }
        }
        if (closeModalButton) closeModalButton.onclick = () => modal.style.display = 'none';

        if (confirmSelectionButton) {
            confirmSelectionButton.onclick = function() {
                const selectedTreatmentIds = [];
                const selectedTreatmentsData = [];
                const checkedTreatmentCheckboxes = packagesHorizontalListContainer.querySelectorAll('input[name="spa_treatment_option"]:checked');
                checkedTreatmentCheckboxes.forEach(checkbox => {
                    selectedTreatmentIds.push(checkbox.value);
                    const treatmentId = checkbox.value;
                    let treatmentFullName = '';
                    let categoryName = '';
                    for (const category of spaTreatmentCategories) {
                        const foundTreatment = category.treatments.find(t => t.id === treatmentId);
                        if (foundTreatment) {
                            treatmentFullName = foundTreatment.name;
                            categoryName = category.name;
                            break;
                        }
                    }
                    selectedTreatmentsData.push({ name: treatmentFullName, category: categoryName });
                });
                hiddenInputSelectedPackages.value = selectedTreatmentIds.join(',');
                selectedTreatmentsDisplay.innerHTML = '';
                if (selectedTreatmentsData.length > 0) {
                    const titleElement = document.createElement('h5');
                    titleElement.textContent = 'Twoje wybrane zabiegi:';
                    selectedTreatmentsDisplay.appendChild(titleElement);
                    const ul = document.createElement('ul');
                    const groupedByCategory = selectedTreatmentsData.reduce((acc, item) => {
                        acc[item.category] = acc[item.category] || [];
                        acc[item.category].push(item.name);
                        return acc;
                    }, {});
                    for (const category in groupedByCategory) {
                        if (groupedByCategory[category].length > 0) {
                            const categoryHeaderLi = document.createElement('li');
                            categoryHeaderLi.innerHTML = `<strong>${category}:</strong>`;
                            categoryHeaderLi.style.cssText = 'border-bottom: none; padding-bottom: 2px; margin-bottom: 2px;';
                            ul.appendChild(categoryHeaderLi);
                            groupedByCategory[category].forEach(name => {
                                const li = document.createElement('li');
                                li.textContent = name;
                                li.style.paddingLeft = '15px';
                                ul.appendChild(li);
                            });
                        }
                    }
                    selectedTreatmentsDisplay.appendChild(ul);
                    selectedTreatmentsDisplay.style.display = 'block';
                    openModalButton.textContent = `Wybrano (${selectedTreatmentIds.length}) Zmień`;
                } else {
                    selectedTreatmentsDisplay.style.display = 'none';
                    openModalButton.textContent = 'Wybierz Masaż lub Pakiet';
                }
                modal.style.display = 'none';
            }
        }
        window.onclick = event => { if (event.target == modal) modal.style.display = 'none'; };
    }

    const summaryPageIdentifier = document.getElementById('paymentRedirectForm');
    if (summaryPageIdentifier) {
        const goToPaymentButton = document.getElementById('goToPaymentButton');
        const paymentModal = document.getElementById('paymentModal');
        const closePaymentModalButton = document.getElementById('closePaymentModal');
        const confirmPaymentBtn = document.getElementById('confirmPaymentButton');

        if (goToPaymentButton) {
            goToPaymentButton.addEventListener('click', function(event) {
                event.preventDefault();
                if (paymentModal) paymentModal.style.display = 'block';
            });
        }
        if (paymentModal && closePaymentModalButton) {
            closePaymentModalButton.onclick = () => paymentModal.style.display = 'none';
        }
        if (confirmPaymentBtn) {
            confirmPaymentBtn.onclick = function() {
                alert('Logika przetwarzania płatności tutaj...');
                if (paymentModal) paymentModal.style.display = 'none';
            }
        }
        window.addEventListener('click', event => {
             if (event.target == paymentModal) { // Dotyczy tylko modala płatności
                if (paymentModal) paymentModal.style.display = 'none';
            }
        });
    }
});