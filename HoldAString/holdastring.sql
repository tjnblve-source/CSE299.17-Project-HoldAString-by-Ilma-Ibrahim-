-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 05:29 PM
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
-- Database: `holdastring`
--

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `ownerID` varchar(10) DEFAULT NULL,
  `connectionID` int(11) NOT NULL,
  `Name` varchar(20) DEFAULT NULL,
  `RelationshipType` enum('Friend','Family','Partner','Colleague') DEFAULT NULL,
  `Birthday` date DEFAULT NULL,
  `Bio` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `connections`
--

INSERT INTO `connections` (`ownerID`, `connectionID`, `Name`, `RelationshipType`, `Birthday`, `Bio`) VALUES
('user2', 10, 'ryo', 'Colleague', NULL, 'annoying'),
('user1', 11, 'Mary', 'Friend', NULL, 'she\'s tolerable'),
('user1', 12, 'maria', 'Family', NULL, ''),
('user4', 13, 'lee', 'Friend', '1993-11-04', ''),
('user7', 15, 'rihanna', 'Colleague', '1999-04-30', 'she sings well'),
('user7', 16, 'Tina', 'Friend', NULL, ''),
('user2', 17, 'Han', 'Friend', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `ownerID` varchar(10) DEFAULT NULL,
  `connectionID` int(11) DEFAULT NULL,
  `Antagonist` varchar(80) DEFAULT NULL,
  `EventTitle` varchar(100) DEFAULT NULL,
  `EventDate` date DEFAULT NULL,
  `FollowUpTopic` varchar(200) DEFAULT NULL,
  `SuggestedQuestions` text DEFAULT NULL,
  `isResolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `ownerID`, `connectionID`, `Antagonist`, `EventTitle`, `EventDate`, `FollowUpTopic`, `SuggestedQuestions`, `isResolved`) VALUES
(1, 'user2', 10, 'Boss', 'Preparing for meeting regarding a potential promotion', '0000-00-00', 'The outcome of the meeting and whether the promotion was granted.', 'How did the meeting with your boss go?; Was your confidence rewarded with the promotion?', 1),
(2, 'user1', 11, 'Parents', 'Parents\' anniversary', '2024-05-11', 'Selecting a meaningful anniversary gift for her parents.', 'What are her parents\' hobbies or interests?; Is there a specific budget for the gift?', 1),
(3, 'user1', 11, 'John', 'Wedding planning meeting', '2024-05-17', 'Discussing specific wedding details and arrangements with John.', 'What specific aspects of the wedding are being discussed?; Is there a set location for this meeting?', 0),
(4, 'user4', 13, 'Wife', 'Expected Baby Delivery', '2024-06-01', 'Preparing for the newborn\'s arrival and managing prenatal anxiety.', 'How is the hospital bag preparation coming along?; Would he find it helpful to discuss some relaxation techniques for his nervousness?', 0),
(5, 'user1', 12, 'Luna Vet', 'Vet visit and potential operation', '2024-05-23', 'Luna\'s vet visit outcome and whether an operation is necessary.', 'How did Luna\'s vet visit go?; Does Luna need the operation?', 1),
(6, 'user7', 15, 'She', 'Concert Performance', '2024-05-30', 'Her concert performance and the invitation to attend.', 'What kind of music will you be performing at the concert?; How long have you been preparing for this show?; Are you feeling nervous or excited about the performance?; Would you like to grab some celebratory drinks or a bite to eat after the concert?', 0),
(7, 'user7', 16, 'Mother', 'Mother\'s Surgery', '2024-06-01', 'The recovery and health status of her mother after the surgery.', 'How did your mother\'s surgery go?; How is she feeling now that she\'s in recovery?; How are you holding up with everything going on?; Is there anything I can do to help out while you\'re caring for her?', 0),
(8, 'user7', 16, 'New Cat', 'Getting a New Cat', '2024-05-27', 'The transition and naming of the new pet.', 'How is the new cat settling into your home?; Did you decide on a name for the cat yet?; I\'d love to see photos—want to grab a coffee soon and show me some?; Is the new cat getting along with everything well?', 0),
(9, 'user1', 12, 'Maria Maria\'s', 'Maria\'s cat\'s vet appointment', '2024-05-20', 'The outcome of the cat\'s vet visit and the cat\'s health.', 'How did the vet appointment go for your cat?; Is everything okay with your cat\'s health?; I know vet visits can be stressful; would you like to grab a coffee later today to unwind?', 1),
(10, 'user2', 17, 'Concert', 'Upcoming Concert Stress', NULL, 'Han\'s upcoming concert and the stress he is experiencing regarding it.', 'How are the rehearsals for the concert going?; Are you feeling any less stressed about the performance?; Would you like to grab a drink after your concert to celebrate and unwind?', 0),
(11, 'user2', 17, 'Parents', 'Parents\' Anniversary Gift Search', '2026-05-22', 'Han\'s parents\' wedding anniversary.', 'Did you manage to find a gift for your parents\' anniversary?; Would you like to go to the mall this weekend to look for some gift ideas together?; How did your parents react to their anniversary surprise?', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminderID` int(11) NOT NULL,
  `connectionID` int(11) DEFAULT NULL,
  `reminderText` varchar(255) DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `isSent` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `sessionID` int(11) NOT NULL,
  `userID` varchar(10) DEFAULT NULL,
  `sessionToken` varchar(255) NOT NULL,
  `expiresAt` datetime NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `strings`
--

CREATE TABLE `strings` (
  `stringID` int(11) NOT NULL,
  `connectionID` int(11) DEFAULT NULL,
  `ownerID` varchar(10) DEFAULT NULL,
  `stringHealth` decimal(5,2) DEFAULT 100.00,
  `lastInteraction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strings`
--

INSERT INTO `strings` (`stringID`, `connectionID`, `ownerID`, `stringHealth`, `lastInteraction`) VALUES
(10, 10, 'user2', 80.00, '2026-05-14 12:21:11'),
(11, 11, 'user1', 80.00, '2026-05-13 05:23:07'),
(12, 12, 'user1', 72.00, '2026-05-13 08:19:43'),
(13, 13, 'user4', 42.00, '2026-05-14 12:48:05'),
(14, 15, 'user7', 100.00, '2026-04-29 04:50:40'),
(15, 16, 'user7', 100.00, '2026-04-29 04:52:48'),
(16, 17, 'user2', 100.00, '2026-05-14 12:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` varchar(10) NOT NULL,
  `DisplayName` varchar(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `isVerified` tinyint(1) DEFAULT 0,
  `verificationCode` varchar(6) DEFAULT NULL,
  `codeExpiryTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `DisplayName`, `Email`, `Password`, `CreatedAt`, `isVerified`, `verificationCode`, `codeExpiryTime`) VALUES
('user1', 'user1', 'user1@gmail.com', '$2y$10$R/cFpCFTgESDlinnXiqyFe99rFY4QHsmFTTNOYYTK7DhTbllc3VRG', '2026-02-25 06:41:09', 1, NULL, NULL),
('user2', 'user2', 'user2@gmail.com', '$2y$10$JpDYaeuf0B8SS0gkmxLcfekFag7mc1wJAYRW0/clbpmwaEK8gtE6m', '2026-02-25 06:44:06', 1, NULL, NULL),
('user3', 'user3', 'user3@gmail.com', '$2y$10$lhD5i.X4kpQHKdJpF3Ayl.90VaLxVrWkxjT8pxt7VqPc04/2M93dq', '2026-02-25 07:14:49', 1, NULL, NULL),
('user4', 'user4', 'user4@gmail.com', '$2y$10$Z6Zf7xTZ/aUAVmrl40487Ors0K1AOadII/JoA3srWHx3kBVyaVp7a', '2026-03-11 15:02:25', 1, NULL, NULL),
('user5', 'user5', 'user5@gmail.com', '$2y$10$c4T/hl2hXwI31ocLHlMmHeWNtHMMPbigkfFe1r16YiaeV/cUpdJby', '2026-03-11 15:07:00', 1, NULL, NULL),
('user6', 'user6', 'user6@gmail.com', '$2y$10$/rw7YYuNlDz9ASx/qbv8Hud75XZ21jRP9y1q82a8E3zaxjdQqVgqG', '2026-03-11 16:41:38', 1, NULL, NULL),
('user7', 'user7', 'user7@gmail.com', '$2y$10$L9poE61epvynxd4MuUXtOuy6hbwI4CKwh8ntyyKx9X64WtneX5OIS', '2026-04-29 04:11:36', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`connectionID`),
  ADD KEY `ownerID` (`ownerID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `connectionID` (`connectionID`),
  ADD KEY `ownerID` (`ownerID`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminderID`),
  ADD KEY `connectionID` (`connectionID`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sessionID`),
  ADD UNIQUE KEY `sessionToken` (`sessionToken`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `strings`
--
ALTER TABLE `strings`
  ADD PRIMARY KEY (`stringID`),
  ADD UNIQUE KEY `connectionID` (`connectionID`),
  ADD KEY `ownerID` (`ownerID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `connectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `sessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `strings`
--
ALTER TABLE `strings`
  MODIFY `stringID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `connections`
--
ALTER TABLE `connections`
  ADD CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`ownerID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`connectionID`) REFERENCES `connections` (`connectionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`ownerID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`connectionID`) REFERENCES `connections` (`connectionID`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `strings`
--
ALTER TABLE `strings`
  ADD CONSTRAINT `strings_ibfk_1` FOREIGN KEY (`connectionID`) REFERENCES `connections` (`connectionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `strings_ibfk_2` FOREIGN KEY (`ownerID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
