SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- DROP DATABASE IF EXISTS leave_system_db;
-- CREATE DATABASE leave_system_db;
-- USE leave_system_db;

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` (`id`, `user_id`, `action_text`, `created_at`) VALUES
(1, 1, 'Jayson logged in', '2025-11-07 16:00:00'),
(2, 1, 'Submitted vacation request #1', '2025-11-07 16:00:00'),
(3, 4, 'Manager approved leave request #1', '2025-11-08 16:00:00'),
(4, 2, 'Kevin logged in', '2025-11-19 16:00:00'),
(5, 2, 'Submitted sick leave request #2', '2025-11-19 16:00:00'),
(6, 4, 'Manager approved leave request #2', '2025-11-20 16:00:00'),
(7, 2, 'Submitted vacation request #3', '2025-11-22 16:00:00'),
(8, 4, 'Manager rejected leave request #3 (Reason: Critical Deadline)', '2025-11-23 16:00:00'),
(9, 3, 'Lance logged in', '2025-11-28 07:38:35'),
(10, 3, 'Submitted emergency leave request #4', '2025-11-28 07:38:35'),
(11, 1, 'Submitted vacation request #5', '2025-11-28 07:38:35'),
(12, 4, 'User logged in', '2025-11-28 07:38:55'),
(13, 3, 'User logged in', '2025-11-28 07:41:44'),
(14, 4, 'User logged in', '2025-11-28 07:42:09'),
(15, 3, 'Applied for 2 days (Emergency)', '2025-11-28 07:42:39'),
(16, 4, 'Approved request #6', '2025-11-28 07:42:58'),
(17, 5, 'User logged in', '2025-11-28 07:43:32'),
(18, 4, 'User logged in', '2025-11-28 07:44:40'),
(19, 3, 'User logged in', '2025-11-28 07:44:54');

CREATE TABLE `calendar_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `color` varchar(20) DEFAULT '#8b5cf6',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `calendar_events` (`id`, `title`, `start_date`, `end_date`, `color`, `created_by`, `created_at`) VALUES
(1, 'Company Anniversary', '2025-12-08', '2025-12-08', '#f59e0b', 5, '2025-11-28 07:38:35'),
(2, 'Team Building', '2025-12-23', '2025-12-24', '#10b981', 5, '2025-11-28 07:38:35'),
(3, 'Public Holiday', '2026-01-07', '2026-01-07', '#ef4444', 5, '2025-11-28 07:38:35'),
(4, 'Creation of Project', '2025-11-01', '2025-11-05', '#ef4444', 4, '2025-11-28 07:39:55'),
(5, 'No Working Hours', '2025-11-29', '2025-11-30', '#f59e0b', 4, '2025-11-28 07:40:34'),
(6, 'Session Fair', '2025-11-10', '2025-11-11', '#a855f7', 4, '2025-11-28 07:41:25');

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type` enum('Vacation','Sick','Emergency') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `leave_requests` (`id`, `user_id`, `leave_type`, `start_date`, `end_date`, `days_count`, `reason`, `status`, `created_at`) VALUES
(1, 1, 'Vacation', '2025-11-14', '2025-11-17', 3, 'Family outing to Baguio', 'Approved', '2025-11-07 16:00:00'),
(2, 2, 'Sick', '2025-11-21', '2025-11-22', 2, 'High fever and flu', 'Approved', '2025-11-19 16:00:00'),
(3, 2, 'Vacation', '2025-11-26', '2025-11-27', 2, 'Urgent personal errand', 'Rejected', '2025-11-22 16:00:00'),
(4, 3, 'Emergency', '2025-11-29', '2025-11-29', 1, 'Car broke down, need to visit mechanic', 'Pending', '2025-11-28 07:38:34'),
(5, 1, 'Vacation', '2025-12-28', '2026-01-02', 5, 'International travel', 'Pending', '2025-11-28 07:38:34'),
(6, 3, 'Emergency', '2025-11-25', '2025-11-26', 2, 'Emergency Purposes', 'Approved', '2025-11-28 07:42:39');

CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL,
  `data` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `stamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sessions` (`session_id`, `data`, `ip`, `agent`, `stamp`) VALUES
('2s7uvjuisooevn3smbtb2n7uel', 'user_id|i:4;full_name|s:15:\"Project Manager\";role|s:7:\"manager\";', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 1764315780),
('9qt4bn2d6kn86838ldplpuu3pv', '', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 1764315961);

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','manager','admin') NOT NULL,
  `vacation_balance` int(11) DEFAULT 15,
  `sick_balance` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `role`, `vacation_balance`, `sick_balance`, `created_at`) VALUES
(1, 'Jayson Abelong', 'Jayson', '12345', 'employee', 12, 8, '2025-11-28 07:38:34'),
(2, 'Kevin Malabanan', 'Kevin', '12345', 'employee', 12, 8, '2025-11-28 07:38:34'),
(3, 'Lance David', 'Lance', '12345', 'employee', 12, 8, '2025-11-28 07:38:34'),
(4, 'Project Manager', 'manager', '12345', 'manager', 20, 20, '2025-11-28 07:38:34'),
(5, 'Admin HR', 'admin', '12345', 'admin', 30, 30, '2025-11-28 07:38:34');

ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `calendar_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;