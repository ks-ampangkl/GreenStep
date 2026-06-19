-- sql/greenstep.sql
CREATE DATABASE IF NOT EXISTS greenstep_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE greenstep_api;

DROP TABLE IF EXISTS eco_photos;
DROP TABLE IF EXISTS goals;
DROP TABLE IF EXISTS friend_requests;
DROP TABLE IF EXISTS friendships;
DROP TABLE IF EXISTS challenge_members;
DROP TABLE IF EXISTS challenges;
DROP TABLE IF EXISTS tips;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS activity_types;
DROP TABLE IF EXISTS users;

-- 1. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    eco_points INT DEFAULT 0,
    gained_today INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Activity Types Table
CREATE TABLE activity_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    kg_co2_per_unit DECIMAL(10,4) NOT NULL,
    info VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 3. Activity Logs Table
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    emissions_kg DECIMAL(10,2) NOT NULL,
    logged_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    logged_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Tips Table
CREATE TABLE tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    category VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 5. Challenges Table
CREATE TABLE challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    group_progress_percent DECIMAL(5,2) DEFAULT 0.00,
    is_active INT DEFAULT 1,
    is_completed INT DEFAULT 0
) ENGINE=InnoDB;

-- 6. Challenge Members (Junction Table)
CREATE TABLE challenge_members (
    challenge_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (challenge_id, user_id),
    FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Friendships Table
CREATE TABLE friendships (
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    PRIMARY KEY (user_id, friend_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Friend Requests Table
CREATE TABLE friend_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    requested_at VARCHAR(50) NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Performance Goals Table
CREATE TABLE goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    target_to_reduce_kg DECIMAL(10,2) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    emissions_reduced_so_far_kg DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Eco Photos Table
CREATE TABLE eco_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    achievement TEXT NOT NULL,
    uploaded_on DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;