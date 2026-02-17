
CREATE DATABASE IF NOT EXISTS car_rental
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE car_rental;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  full_name VARCHAR(100) NULL,
  address VARCHAR(255) NULL,
  phone_number VARCHAR(50) NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL DEFAULT 2,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  profile_image VARCHAR(255) NULL,
  remember_token VARCHAR(128) NULL,
  blocked_until DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  successful TINYINT(1) NOT NULL,
  attempt_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_login_attempts_user_id (user_id),
  CONSTRAINT fk_login_attempts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  price_per_day DECIMAL(10,2) NOT NULL,
  fuel VARCHAR(50) NOT NULL,
  seating_capacity INT NOT NULL,
  engine VARCHAR(100) NOT NULL,
  transmission VARCHAR(50) NOT NULL,
  year INT NOT NULL,
  bluetooth TINYINT(1) NOT NULL DEFAULT 0,
  gps TINYINT(1) NOT NULL DEFAULT 0,
  color VARCHAR(50) NOT NULL,
  type VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS car_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  image_order INT NOT NULL,
  KEY idx_car_images_car_id (car_id),
  CONSTRAINT fk_car_images_car
    FOREIGN KEY (car_id) REFERENCES cars(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  price_per_day DECIMAL(10,2) NOT NULL,
  fuel VARCHAR(50) NOT NULL,
  engine_cc INT NOT NULL,
  transmission VARCHAR(50) NOT NULL,
  year INT NOT NULL,
  abs TINYINT(1) NOT NULL DEFAULT 0,
  color VARCHAR(50) NOT NULL,
  type VARCHAR(50) NOT NULL,
  weight_kg INT NOT NULL,
  seat_height_mm INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycle_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  motorcycle_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  image_order INT NOT NULL,
  KEY idx_motorcycle_images_motorcycle_id (motorcycle_id),
  CONSTRAINT fk_motorcycle_images_motorcycle
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  car_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_cart_user_id (user_id),
  KEY idx_cart_car_id (car_id),
  CONSTRAINT fk_cart_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_cart_car
    FOREIGN KEY (car_id) REFERENCES cars(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycle_cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  motorcycle_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_motorcycle_cart_user_id (user_id),
  KEY idx_motorcycle_cart_motorcycle_id (motorcycle_id),
  CONSTRAINT fk_motorcycle_cart_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_motorcycle_cart_motorcycle
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  user_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  total_price DECIMAL(10,2) NOT NULL,
  booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_bookings_user_id (user_id),
  KEY idx_bookings_car_id (car_id),
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_bookings_car
    FOREIGN KEY (car_id) REFERENCES cars(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycle_bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  motorcycle_id INT NOT NULL,
  user_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  total_price DECIMAL(10,2) NOT NULL,
  booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_motorcycle_bookings_user_id (user_id),
  KEY idx_motorcycle_bookings_motorcycle_id (motorcycle_id),
  CONSTRAINT fk_motorcycle_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_motorcycle_bookings_motorcycle
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL,
  review_text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_reviews_car_user (car_id, user_id),
  KEY idx_reviews_car_id (car_id),
  KEY idx_reviews_user_id (user_id),
  CONSTRAINT fk_reviews_car
    FOREIGN KEY (car_id) REFERENCES cars(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycle_reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  motorcycle_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL,
  review_text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_motorcycle_reviews_motorcycle_user (motorcycle_id, user_id),
  KEY idx_motorcycle_reviews_motorcycle_id (motorcycle_id),
  KEY idx_motorcycle_reviews_user_id (user_id),
  CONSTRAINT fk_motorcycle_reviews_motorcycle
    FOREIGN KEY (motorcycle_id) REFERENCES motorcycles(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_motorcycle_reviews_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  provider VARCHAR(50) NOT NULL,
  provider_transaction_id VARCHAR(128) NOT NULL,
  checkout_session_id VARCHAR(128) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_transactions_booking_id (booking_id),
  CONSTRAINT fk_transactions_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS motorcycle_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  provider VARCHAR(50) NOT NULL,
  provider_transaction_id VARCHAR(128) NOT NULL,
  checkout_session_id VARCHAR(128) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_motorcycle_transactions_booking_id (booking_id),
  CONSTRAINT fk_motorcycle_transactions_booking
    FOREIGN KEY (booking_id) REFERENCES motorcycle_bookings(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
