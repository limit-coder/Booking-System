<?php
declare(strict_types=1);

function get_database_connection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('CASA_DB_HOST') ?: '127.0.0.1';
    $database = getenv('CASA_DB_NAME') ?: 'casa_adelaida';
    $username = getenv('CASA_DB_USER') ?: 'root';
    $password = getenv('CASA_DB_PASSWORD') ?: '';
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

    $connection = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $columns = $connection->query(
        "SELECT COLUMN_NAME FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings'"
    )->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('tour_type', $columns, true)) {
        $connection->exec(
            "ALTER TABLE bookings
             ADD COLUMN tour_type ENUM('Day Tour', 'Overnight Tour') NOT NULL DEFAULT 'Day Tour'
             AFTER pool_size"
        );
    }
    if (!in_array('check_in_time', $columns, true)) {
        $connection->exec(
            "ALTER TABLE bookings
             ADD COLUMN check_in_time TIME NOT NULL DEFAULT '08:00:00'
             AFTER check_in"
        );
    }
    if (!in_array('check_out_time', $columns, true)) {
        $connection->exec(
            "ALTER TABLE bookings
             ADD COLUMN check_out_time TIME NOT NULL DEFAULT '17:00:00'
             AFTER check_out"
        );
    }

    $statusColumn = $connection->query(
        "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'status'"
    )->fetchColumn();
    if (is_string($statusColumn) && !str_contains($statusColumn, 'Checked Out')) {
        $connection->exec(
            "ALTER TABLE bookings
             MODIFY status ENUM('Pending', 'Confirmed', 'Declined', 'Checked Out') NOT NULL DEFAULT 'Pending'"
        );
    }

    return $connection;
}