CREATE DATABASE IF NOT EXISTS casa_adelaida
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE casa_adelaida;

CREATE TABLE IF NOT EXISTS bookings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    pool_size ENUM('Big Pool', 'Small Pool') NOT NULL DEFAULT 'Big Pool',
    tour_type ENUM('Day Tour', 'Overnight Tour') NOT NULL DEFAULT 'Day Tour',
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    check_in_time TIME NOT NULL DEFAULT '08:00:00',
    check_out_time TIME NOT NULL DEFAULT '17:00:00',
    status ENUM('Pending', 'Confirmed', 'Declined', 'Checked Out') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bookings_status (status),
    KEY idx_bookings_check_in (check_in),
    KEY idx_bookings_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(64) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB;