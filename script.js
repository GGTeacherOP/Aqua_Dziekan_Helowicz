document.addEventListener('DOMContentLoaded', function () {
    const loginButton = document.getElementById('loginButton');
    const registerButton = document.getElementById('registerButton');
    const logoutButton = document.getElementById('logoutButton');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    const currentPage = window.location.pathname.split('/').pop();

    // Funkcja aktualizująca przyciski nawigacyjne
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

    // Obsługa logowania
    if (loginForm) {
        // Jeśli użytkownik jest już zalogowany i trafia na stronę logowania, przekieruj
        if (localStorage.getItem('isLoggedIn') === 'true' && currentPage === 'login.html') {
            window.location.href = 'index.html';
        }

        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            // Symulacja pomyślnego logowania
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = 'index.html';
        });
    }

    // Obsługa rejestracji
    if (signupForm) {
         // Jeśli użytkownik jest już zalogowany i trafia na stronę rejestracji, przekieruj
        if (localStorage.getItem('isLoggedIn') === 'true' && currentPage === 'signup.html') {
            window.location.href = 'index.html';
        }
        signupForm.addEventListener('submit', function (event) {
            event.preventDefault();
            // Symulacja pomyślnej rejestracji i logowania
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = 'index.html';
        });
    }

    // Obsługa wylogowania
    if (logoutButton) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();
            localStorage.removeItem('isLoggedIn');
            // W prawdziwej aplikacji tutaj byłoby przekierowanie do skryptu PHP
            // np. window.location.href = 'logout.php';
            // Po wylogowaniu przez PHP, skrypt PHP powinien przekierować do login.html
            window.location.href = 'login.html';
        });
    }

    // Inicjalizacja przycisków na wszystkich stronach (oprócz login i signup)
    if (currentPage !== 'login.html' && currentPage !== 'signup.html') {
        updateNavButtons();
    }


    // Płynne przewijanie dla linków wewnętrznych (jeśli istnieją na stronie)
    // Ten skrypt był oryginalnie w index.html
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const hrefAttribute = this.getAttribute('href');
            // Upewnij się, że element docelowy istnieje i nie jest to tylko "#"
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
});