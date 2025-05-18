<?php
// config/init.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definiowanie stałej dla ścieżki głównej projektu, jeśli potrzebne
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); // Zakłada, że config jest o jeden poziom niżej niż główny folder projektu
}

require_once BASE_PATH . '/config/db_connection.php';
require_once BASE_PATH . '/src/functions.php'; // Ta linia DOŁĄCZA plik, gdzie funkcja POWINNA być zdefiniowana

// Ustawienie strefy czasowej
date_default_timezone_set('Europe/Warsaw');

$isLoggedInFromPHP = isset($_SESSION['user_id']);
?>