<?php
require_once("constants.php");

try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $connection = new PDO($dsn, DB_USER, DB_PASS);

    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    die("The application is temporarily unavailable.");
}
