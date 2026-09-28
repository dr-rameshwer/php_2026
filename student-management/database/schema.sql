-- =====================================================================
-- University Student Management System - Database Schema & Seed Data
-- Database Engine: MySQL / MariaDB (InnoDB)
-- Character Set: utf8mb4 (Full Unicode Support)
-- =====================================================================

-- Step 1: Create Database if not exists
CREATE DATABASE IF NOT EXISTS `student_management`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `student_management`;

-- Step 2: Drop existing tables to ensure a clean slate if re-importing
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

-- Step 3: Create Users Table (For Admin Authentication)
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create Students Table
CREATE TABLE `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
    `dob` DATE NOT NULL,
    `course` VARCHAR(50) NOT NULL,
    `semester` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `address` TEXT NOT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 5: Insert Seed Admin User
-- Plaintext Password is: AdminPassword123
-- Hashed using: password_hash('AdminPassword123', PASSWORD_BCRYPT)
INSERT INTO `users` (`name`, `email`, `password`) VALUES
('Head Administrator', 'admin@portal.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm');

-- Step 6: Insert Seed Student Records for Testing Search & Pagination
INSERT INTO `students` (`name`, `email`, `phone`, `gender`, `dob`, `course`, `semester`, `address`, `photo`) VALUES
('Amanpreet Singh', 'amanpreet@example.edu', '9876543210', 'Male', '2005-04-12', 'Computer Science', 1, 'Model Town, City Center', NULL),
('Simran Kaur', 'simran@example.edu', '9812345678', 'Female', '2004-11-23', 'Computer Science', 1, 'Urban Estate Phase 2', NULL),
('Rajesh Kumar', 'rajesh@example.edu', '9723456789', 'Male', '2003-08-15', 'Information Technology', 3, 'Civil Lines', NULL),
('Pooja Sharma', 'pooja@example.edu', '9834567890', 'Female', '2005-01-30', 'Computer Science', 1, 'Grand Trunk Road', NULL),
('Gurpreet Singh', 'gurpreet@example.edu', '9845678901', 'Male', '2004-06-18', 'Software Engineering', 1, 'Sector 70', NULL),
('Harleen Deol', 'harleen@example.edu', '9856789012', 'Female', '2003-12-05', 'Data Science', 5, 'Mall Road', NULL),
('Navdeep Gill', 'navdeep@example.edu', '9867890123', 'Male', '2005-09-14', 'Information Technology', 1, 'University Campus Avenue', NULL),
('Kiran Bala', 'kiran@example.edu', '9878901234', 'Female', '2004-03-22', 'Computer Science', 3, 'North Avenue Road', NULL),
('Vikramaditya', 'vikram@example.edu', '9889012345', 'Male', '2004-07-09', 'Software Engineering', 3, 'Heritage Enclave', NULL),
('Tanya Verma', 'tanya@example.edu', '9890123456', 'Female', '2005-02-17', 'Computer Science', 1, 'South Park Boulevard', NULL);
