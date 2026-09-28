-- =====================================================================
-- University BCA Student Management System - Database Schema & Seed Data
-- Database Engine: MySQL / MariaDB (InnoDB)
-- Character Set: utf8mb4 (Full Unicode Support)
-- =====================================================================

-- Step 1: Create Database if not exists
CREATE DATABASE IF NOT EXISTS `bca_student_management`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `bca_student_management`;

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
('Head Administrator', 'admin@bca.edu', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm');

-- Step 6: Insert Seed Student Records for Testing Search & Pagination
INSERT INTO `students` (`name`, `email`, `phone`, `gender`, `dob`, `course`, `semester`, `address`, `photo`) VALUES
('Amanpreet Singh', 'amanpreet@ptu.ac.in', '9876543210', 'Male', '2005-04-12', 'BCA', 1, 'Model Town, Jalandhar, Punjab', NULL),
('Simran Kaur', 'simran@ptu.ac.in', '9812345678', 'Female', '2004-11-23', 'BCA', 1, 'Urban Estate Phase 2, Patiala, Punjab', NULL),
('Rajesh Kumar', 'rajesh@ptu.ac.in', '9723456789', 'Male', '2003-08-15', 'BCA', 3, 'Civil Lines, Ludhiana, Punjab', NULL),
('Pooja Sharma', 'pooja@ptu.ac.in', '9834567890', 'Female', '2005-01-30', 'BCA', 1, 'GT Road, Amritsar, Punjab', NULL),
('Gurpreet Singh', 'gurpreet@ptu.ac.in', '9845678901', 'Male', '2004-06-18', 'MCA', 1, 'Sector 70, Mohali, Punjab', NULL),
('Harleen Deol', 'harleen@ptu.ac.in', '9856789012', 'Female', '2003-12-05', 'BCA', 5, 'Mall Road, Bathinda, Punjab', NULL),
('Navdeep Gill', 'navdeep@ptu.ac.in', '9867890123', 'Male', '2005-09-14', 'B.Tech IT', 1, 'Near University Campus, Kapurthala, Punjab', NULL),
('Kiran Bala', 'kiran@ptu.ac.in', '9878901234', 'Female', '2004-03-22', 'BCA', 3, 'Hoshiarpur Road, Phagwara, Punjab', NULL),
('Vikramaditya', 'vikram@ptu.ac.in', '9889012345', 'Male', '2004-07-09', 'BCA', 3, 'Chheharta, Amritsar, Punjab', NULL),
('Tanya Verma', 'tanya@ptu.ac.in', '9890123456', 'Female', '2005-02-17', 'BCA', 1, 'Ranjit Avenue, Amritsar, Punjab', NULL);
