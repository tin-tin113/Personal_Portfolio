-- ============================================================
-- Personal Portfolio & Private Admin CMS - Database Schema
-- Database Target: MySQL 5.7+ / MariaDB 10.2+
-- Environment: Laragon (PHP 8.x + MySQL)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `personal_portfolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `personal_portfolio`;

-- ------------------------------------------------------------
-- Table: admin_users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `failed_logins` INT DEFAULT 0,
  `last_failed_login` DATETIME NULL,
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: site_settings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: profile
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profile` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `professional_title` VARCHAR(150) NOT NULL,
  `tagline` VARCHAR(255) NULL,
  `short_summary` TEXT NULL,
  `about_me` LONGTEXT NULL,
  `email` VARCHAR(100) NULL,
  `phone` VARCHAR(30) NULL,
  `location` VARCHAR(100) NULL,
  `profile_image` VARCHAR(255) NULL,
  `resume_file` VARCHAR(255) NULL,
  `availability_status` VARCHAR(100) DEFAULT 'Available for Full-Time & Freelance Projects',
  `github_url` VARCHAR(255) NULL,
  `linkedin_url` VARCHAR(255) NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: skill_categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `skill_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: skills
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NULL,
  `name` VARCHAR(100) NOT NULL,
  `proficiency_level` INT DEFAULT 80,
  `level_name` VARCHAR(50) DEFAULT 'Advanced',
  `icon_class` VARCHAR(100) NULL,
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `skill_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: projects
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `short_description` VARCHAR(255) NOT NULL,
  `detailed_description` LONGTEXT NULL,
  `project_image` VARCHAR(255) NULL,
  `my_role` VARCHAR(150) NULL,
  `key_features` TEXT NULL,
  `challenges` TEXT NULL,
  `solutions` TEXT NULL,
  `development_methodology` VARCHAR(150) NULL,
  `project_year` VARCHAR(20) NULL,
  `github_url` VARCHAR(255) NULL,
  `live_demo_url` VARCHAR(255) NULL,
  `documentation_url` VARCHAR(255) NULL,
  `technologies_used` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: project_images
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: certifications
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `certifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `issuing_organization` VARCHAR(150) NOT NULL,
  `issue_date` DATE NULL,
  `expiration_date` DATE NULL,
  `credential_id` VARCHAR(100) NULL,
  `credential_url` VARCHAR(255) NULL,
  `certificate_file` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: licenses
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `licenses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `issuing_organization` VARCHAR(150) NOT NULL,
  `issue_date` DATE NULL,
  `license_number` VARCHAR(100) NULL,
  `verification_url` VARCHAR(255) NULL,
  `document_file` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `is_published` TINYINT(1) DEFAULT 1,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: education
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `institution` VARCHAR(150) NOT NULL,
  `degree_program` VARCHAR(150) NOT NULL,
  `start_year` VARCHAR(20) NULL,
  `end_year` VARCHAR(20) NULL,
  `description` TEXT NULL,
  `achievements` TEXT NULL,
  `relevant_coursework` TEXT NULL,
  `credential_file` VARCHAR(255) NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: experience
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `experience` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `position` VARCHAR(150) NOT NULL,
  `organization` VARCHAR(150) NOT NULL,
  `employment_type` VARCHAR(50) DEFAULT 'Full-time',
  `start_date` DATE NULL,
  `end_date` DATE NULL,
  `is_current` TINYINT(1) DEFAULT 0,
  `description` TEXT NULL,
  `responsibilities` TEXT NULL,
  `achievements` TEXT NULL,
  `technologies_used` VARCHAR(255) NULL,
  `display_order` INT DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: external_links
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `external_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(100) NULL,
  `category` VARCHAR(50) DEFAULT 'Social',
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: media
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_filename` VARCHAR(255) NOT NULL UNIQUE,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `file_size` INT NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `associated_module` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
