-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2024 at 03:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmp`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 'Testing', '1123444', '2024-10-02 15:09:12'),
(2, 2, 'Testing', '2', '2024-10-02 15:09:57'),
(3, 1, '123', 'test', '2024-10-02 16:36:55'),
(4, 1, '123', '<p><strong>test</strong></p>', '2024-10-02 16:37:13'),
(5, 2, 'testnew', '123', '2024-10-02 16:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'hi', '<p><strong>testing</strong></p>', '2024-10-01 10:19:25', '2024-10-03 06:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(219, 97, '1', 1),
(220, 97, '2', 0),
(221, 97, '2', 0),
(222, 97, '4', 0),
(223, 98, '1', 0),
(224, 98, '2', 1),
(225, 98, '3', 0),
(226, 98, '4', 0),
(227, 99, '1', 0),
(228, 99, '2', 0),
(229, 99, '3', 1),
(230, 99, '4', 0),
(235, 101, '1', 0),
(236, 101, '1', 0),
(237, 101, '1', 0),
(238, 101, '1a', 1),
(239, 102, '11', 1),
(240, 102, '2', 0),
(241, 102, '3', 0),
(242, 102, '4', 0),
(243, 103, '11', 0),
(244, 103, '22', 1),
(245, 103, '33', 0),
(246, 103, '44', 0),
(247, 104, '11', 0),
(248, 104, '22', 0),
(249, 104, '33', 1),
(250, 104, '44', 0),
(251, 105, '11', 0),
(252, 105, '22', 0),
(253, 105, '33', 0),
(254, 105, '44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`) VALUES
(97, 5, 'test1'),
(98, 5, 'test2'),
(99, 5, 'test3'),
(101, 5, 'test4'),
(102, 5, 'test5'),
(103, 5, 'test6'),
(104, 5, 'test7'),
(105, 5, 'test8');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `created_at`, `updated_at`, `duration`, `max_attempts`) VALUES
(1, 'test', 'test', '2024-10-01 10:38:14', '2024-10-01 10:38:14', 0, 2),
(2, 'hi', '2nd test\\r\\n', '2024-10-01 11:34:14', '2024-10-01 11:34:14', 0, 2),
(3, 'hi2', '2', '2024-10-01 11:55:23', '2024-10-01 11:55:23', 0, 2),
(4, 'test2', '', '2024-10-01 12:09:32', '2024-10-01 12:09:32', 0, 2),
(5, 'test3', '', '2024-10-01 12:45:13', '2024-10-01 12:45:13', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `attempt_count` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `attempt_count`, `last_attempt`) VALUES
(12, 11, 5, 2, '2024-10-03 10:08:00'),
(13, 14, 5, NULL, '2024-10-03 10:11:47'),
(14, 8, 5, 2, '2024-10-03 11:47:17'),
(15, 7, 5, 2, '2024-10-03 13:05:52'),
(16, 7, 4, 1, '2024-10-03 13:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attempt` int(11) DEFAULT 1,
  `answer` varchar(255) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `quiz_id`, `user_id`, `score`, `created_at`, `attempt`, `answer`, `question_id`) VALUES
(85, 5, 11, 8, '2024-10-03 11:33:54', 1, NULL, NULL),
(86, 5, 11, 3, '2024-10-03 11:37:50', 1, NULL, NULL),
(87, 5, 8, 5, '2024-10-03 11:47:17', 1, NULL, NULL),
(88, 5, 8, 8, '2024-10-03 11:47:29', 1, NULL, NULL),
(89, 5, 7, 5, '2024-10-03 13:05:52', 1, NULL, NULL),
(90, 5, 7, 8, '2024-10-03 13:11:21', 1, NULL, NULL),
(91, 4, 7, 0, '2024-10-03 13:11:25', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'lecturer'),
(3, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `address`) VALUES
(1, 'Chung Hwa High School', '14, Jalan Junid 84000 Muar Johor '),
(2, 'Test', ''),
(3, 'test', ''),
(4, 'test', ''),
(5, 'test', 'test'),
(6, 'school', 'tets'),
(7, 'tet', 'test'),
(8, 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `profile_desc` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `school_id`, `full_name`, `gender`, `profile_desc`, `profile_image`, `email`) VALUES
(1, 'admin', '$2y$10$OfbgQy8iLgKStxZSXDV44eXyeMWVzbExuGmsfYOUgOHkNDA.L0OB.', 1, NULL, 'admin', 'Male', NULL, NULL, ''),
(2, 'lecturer', '$2y$10$W9HpPV.9C.09BHoMUu1KIu2wDFU4gFyhPIWcK3K7sHmxVhAqjez9m', 2, NULL, 'Comlert', 'Male', 'Hi', 'assets/uploads/WhatsApp Image 2024-09-22 at 17.51.12.jpeg', 'meteor@gmail.com'),
(7, 'yau', '$2y$10$TMvlGiI.DYeBIzXRht27R.MAlAoOY0F33.Cmwf1jw2O20Lfia1wRy', 3, 1, 'Kim Hau', 'Male', 'yau', 'assets/uploads/WhatsApp Image 2024-04-30 at 15.41.56.jpeg', 'kh@mail.com'),
(8, 'vv', '$2y$10$aTX1vctYObnF1l4qx5UqneLUm3zKGkhs8q6i3KIdM8rKlfNpNOXv.', 3, 1, 'Vivian', 'Female', 'vv', 'assets/uploads/WhatsApp Image 2024-04-30 at 15.41.56.jpeg', 'vivian@gmail.com'),
(9, 'yuxuan', '$2y$10$z6KsX4izR5IjDf1nseCfseMlNJ6Nfv2MV7EteCTSDj0HEIvGDVE/G', 3, 1, 'Yu Xuan', 'Male', 'yx', 'yx', 'yx@mail.com'),
(11, 'jq', '$2y$10$asZPcoax.kbWikHTmbIqk.jeSLeOqHxUFvxmb8tur8ut/IfgwOiyi', 3, 1, 'Jie Qing', 'Male', 'Hi', 'assets/uploads/WhatsApp Image 2024-09-22 at 17.51.12.jpeg', 'jq@mail.com'),
(14, 'kw ', '$2y$10$NwhqKPzYs4avv33fl56kyOwQ1FNzlXXsXoNfTDHSPHUQGXYPjWcKy', 3, 1, 'Kang Wei', 'Male', 'HI 2', 'assets/uploads/WhatsApp Image 2024-09-22 at 17.51.12.jpeg', 'kw@mail.com'),
(15, 'ms', '$2y$10$PmrTeTQd9xJqLsnlnigW5eyRFsI3SlEu17tdGbyUxmOdGDHOqmqVC', 3, 1, 'Meteor', 'Male', 'hi', 'assets/uploads/WhatsApp Image 2024-04-30 at 15.41.56.jpeg', 'meteor@gmail.com'),
(16, 'test', '$2y$10$d2pM06U.hvfWYaFTjz2ZyuiOs3uQXNO3aW60.8Pmf3lKupbaH2jIW', 2, NULL, 'test', 'Male', '', NULL, 'test@mail');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_school` (`school_id`),
  ADD KEY `fk_roles` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
