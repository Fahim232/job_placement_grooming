-- Password Reset System Database Schema
-- Add this table to your 'projects' database

CREATE TABLE IF NOT EXISTS `password_reset_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `user_type` enum('user','company') NOT NULL DEFAULT 'user',
  `reset_code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `reset_code` (`reset_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
