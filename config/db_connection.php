<?php
// config/db_connection.php

$db_host = 'localhost';
$db_name = 'aquaparadise_db';
$db_user = 'root';
$db_pass = ''; // Upewnij się, że hasło jest poprawne

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
    // Na produkcji nie wyświetlaj szczegółów błędu użytkownikowi
    die("Wystąpił problem z połączeniem z systemem. Prosimy spróbować później. Szczegóły błędu zostały zapisane w logach serwera.");
}
?>