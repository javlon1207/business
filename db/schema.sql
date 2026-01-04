-- Create database manually first:
-- CREATE DATABASE business_calendar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE business_calendar;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS event_participants;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  last_name VARCHAR(100) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  position VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(255) NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME NULL,
  location VARCHAR(255),
  agenda TEXT,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (start_time),
  INDEX (end_time),
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE event_participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  user_id INT NOT NULL,
  status ENUM('pending','confirmed','rejected') DEFAULT 'pending',
  notified TINYINT DEFAULT 0,
  UNIQUE KEY uniq_event_user (event_id, user_id),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NULL,
  message TEXT NOT NULL,
  is_read TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed accounts (password = 123)
INSERT INTO users(last_name, first_name, position, email, password, role) VALUES
('Admin', 'Super', 'Administrator', 'admin@local', '$2y$10$B1Jd0nww0y2vV5dQm1m5dOrZb7Rj3wYH5qkI0dI19yqjH9v3bR0qC', 'admin'),
('User', 'One', 'Engineer', 'user1@local', '$2y$10$B1Jd0nww0y2vV5dQm1m5dOrZb7Rj3wYH5qkI0dI19yqjH9v3bR0qC', 'user');

-- Note: the hash above corresponds to password "123" for PHP password_verify (bcrypt)
