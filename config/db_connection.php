<?php
// config/db_connection.php

$db_host = 'localhost'; // lub adres Twojego serwera DB
$db_name = 'aquaparadise_db'; // Nazwa Twojej bazy danych (upewnij się, że istnieje)
$db_user = 'root'; // Twój użytkownik DB
$db_pass = ''; // Twoje hasło DB (jeśli ustawiłeś)

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // W środowisku produkcyjnym nie wyświetlaj szczegółów błędu
    error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
    die("Wystąpił problem z połączeniem z systemem. Prosimy spróbować później.");
}
?>