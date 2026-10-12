-- schema.sql - ChroniclePress Fullstack Relational Database Schema
CREATE DATABASE IF NOT EXISTS `chroniclepress_db` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `chroniclepress_db`;

-- 1. Users Table (Supports subscriber, author, and admin roles + Password Reset Tokens)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('subscriber', 'author', 'admin') NOT NULL DEFAULT 'subscriber',
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_token_expires` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Insert Default Categories
INSERT INTO `categories` (`name`, `slug`) VALUES 
('Technology', 'technology'),
('Engineering', 'engineering'),
('Design', 'design'),
('Career', 'career')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 3. Posts Table (Supports draft, pending approval, and published statuses)
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `content` TEXT NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT 'default_cover.jpg',
  `status` ENUM('draft', 'pending', 'published') NOT NULL DEFAULT 'pending',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Comments Table
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Likes Table (Unique constraint prevents a user from liking the same post twice)
CREATE TABLE IF NOT EXISTS `likes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_post_like` (`post_id`, `user_id`),
  CONSTRAINT `fk_likes_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Insert Default Super Admin (Email: admin@chronicle.local | Password: AdminPassword123!)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES 
('super_admin', 'admin@chronicle.local', '$2y$10$e8T7O6m0Z7q.5V5hJ1kG/u3Z9bE5s8F4g2H1j0K9L8M7N6P5Q4R3S', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;