-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2023 at 07:09 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `l&fbd`
--

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_hints`
--

CREATE TABLE `chatbot_hints` (
  `id` int(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `reply` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_hints`
--

INSERT INTO `chatbot_hints` (`id`, `question`, `reply`) VALUES
(1, 'Hi||Hello', 'Hello Sir, How can I help you? '),
(2, 'What is your name?', 'My name is L&F bot. I am here to assist you in your journey to Lost & Found Website'),
(3, 'How can I contact customer support?', 'Please go to the contact & Support option below.'),
(4, 'What products/services do you offer?', 'We offer Various services.'),
(5, 'Can I track my Claim status', 'Yes, you can! Go to your user profile& click status. '),
(6, 'Is my personal information secure?', 'Hello! Ensuring the security of your personal information is our top priority. We employ advanced encryption and security measures to safeguard your data. Our systems are designed to meet industry standards for data protection. If you have specific concer'),
(7, 'Can you provide more information about lost & found website?', ' I\'d be happy to provide information about our lost and found services. Our website is dedicated to helping reunite lost items with their owners. You can report a lost item through our user-friendly interface, providing details such as the item descriptio');

-- --------------------------------------------------------

--
-- Table structure for table `founditem`
--

CREATE TABLE `founditem` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `picture` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `founditem`
--

INSERT INTO `founditem` (`id`, `name`, `address`, `address2`, `city`, `state`, `date`, `picture`) VALUES
(1, 'mobile', 'Airport', 'entry', 'dhaka', 'Dhaka', '0000-00-00', '.png'),
(2, 'mobile', 'Bus-stand', 'entry', 'dhaka', 'Dhaka', '0000-00-00', '.png'),
(3, 'mobile', 'Bus-stand', 'entry', 'dhaka', 'Chattogram', '0000-00-00', '.png'),
(4, 'mobile', 'Bus-stand', 'entry', 'dhaka', 'Chattogram', '0000-00-00', '.png'),
(5, 'mobile', 'Bus-stand', 'entry', 'dhaka', 'Chattogram', '2023-10-08', '.png'),
(6, 'laptop', 'Airport', 'exit', 'dhaka', 'Dhaka', '0000-00-00', '.png'),
(7, 'laptop', 'Bus-stand', 'exit', 'dhaka', 'Dhaka', '2023-10-31', '.png'),
(8, 'mouse', 'Hotel', 'exit', 'dhaka', 'Dhaka', '2023-10-31', '.png');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(255) NOT NULL,
  `message` text NOT NULL,
  `added_on` datetime NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `message`, `added_on`, `type`) VALUES
(13, 'hi', '2023-10-31 11:03:10', 'user'),
(14, 'Hello Sir, How can I help you? ', '2023-10-31 11:03:10', 'bot');

-- --------------------------------------------------------

--
-- Table structure for table `missingperson`
--

CREATE TABLE `missingperson` (
  `Fname` varchar(255) NOT NULL,
  `NID` int(255) NOT NULL,
  `Phone` int(255) NOT NULL,
  `MissingDate` date NOT NULL,
  `Age` int(255) NOT NULL,
  `Haircolor` varchar(255) NOT NULL,
  `Eyecolor` varchar(255) NOT NULL,
  `Cloths` varchar(255) NOT NULL,
  `Sid` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `coursename` text NOT NULL,
  `fees` text NOT NULL,
  `card_number` text NOT NULL,
  `card_expirymonth` text NOT NULL,
  `card_expiryyear` text NOT NULL,
  `status` text NOT NULL,
  `paymentid` text NOT NULL,
  `added_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `name`, `email`, `coursename`, `fees`, `card_number`, `card_expirymonth`, `card_expiryyear`, `status`, `paymentid`, `added_date`) VALUES
(1, 'Rakesh kumar', 'krrakesh014@gmail.com', 'Web Development Expert', '200', '4242424242424242', '08', '28', 'succeeded', 'txn_3NlQKbL3hyAk7Kt217i16cS8', '2023-09-01 07:55:47'),
(2, 'Rahul Goyal', 'rahul@gmail.com', 'Web Development Expert', '300', '4242424242424242', '08', '30', 'succeeded', 'ch_3NlQNFL3hyAk7Kt200722MD3', '2023-09-01 07:58:30'),
(3, 'Tamim Shadman', 'tamim7553@gmail.com', 'cse331', '12000', '4242424242424242', '10', '26', 'succeeded', 'ch_3O2UU8CD1vWrjtcw0fNXJ4IP', '2023-10-18 09:48:07'),
(4, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '10', '23', 'succeeded', 'ch_3O2UYBCD1vWrjtcw1h5fopHl', '2023-10-18 09:52:18'),
(5, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '10', '25', 'succeeded', 'ch_3O2UiPCD1vWrjtcw11d9zuYw', '2023-10-18 10:02:52'),
(6, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '10', '23', 'succeeded', 'ch_3O2UjhCD1vWrjtcw04Wryn5y', '2023-10-18 10:04:12'),
(7, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '03', '24', 'succeeded', 'ch_3O2UycCD1vWrjtcw1WZE7IEN', '2023-10-18 10:19:37'),
(8, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4000056655665556', '10', '25', 'succeeded', 'ch_3O2VtcCD1vWrjtcw06OqAkEs', '2023-10-18 11:18:31'),
(9, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '04', '25', 'succeeded', 'ch_3O2W4lCD1vWrjtcw1Ggz9R74', '2023-10-18 11:30:02'),
(10, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '11', '23', 'succeeded', 'ch_3O2W8WCD1vWrjtcw1BAZtFE4', '2023-10-18 11:33:55'),
(11, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '11', '23', 'succeeded', 'ch_3O2WBJCD1vWrjtcw1KCMIrIg', '2023-10-18 11:36:48'),
(12, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '11', '23', 'succeeded', 'ch_3O2WCNCD1vWrjtcw0UN0mkYW', '2023-10-18 11:37:53'),
(13, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '12', '23', 'succeeded', 'ch_3O7Al3CD1vWrjtcw1jSWTCoG', '2023-10-31 06:44:58'),
(14, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4242424242424242', '12', '23', 'succeeded', 'ch_3O7AnFCD1vWrjtcw0Wu4ruf3', '2023-10-31 06:47:13'),
(15, 'Tamim Shadman', 'tamim7553@gmail.com', '', '12000', '4000056655665556', '12', '23', 'succeeded', 'ch_3O7B0yCD1vWrjtcw12GJIuNN', '2023-10-31 07:01:24'),
(16, 'Tamim Shadman', 'tamimshadman2000@gmail.com', '', '12000', '5555555555554444', '12', '25', 'succeeded', 'ch_3O7B4QCD1vWrjtcw0Es6TNNj', '2023-10-31 07:04:58');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Mobile` int(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Time` date NOT NULL DEFAULT current_timestamp(),
  `sid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`Fname`, `Lname`, `Mobile`, `Email`, `Password`, `Time`, `sid`) VALUES
('Ratul', 'rinku', 1915286868, 'ratul.bhattarcharjee@northsouth.edu', '123456789', '2023-10-01', 1),
('Ratul', 'Bhatt', 1715157500, 'ratulbhattacharjee00@gmail.com', '123456789', '2023-10-04', 2),
('', '', 0, 'ratul.bhattarcharjee@northsouth.edu', '123456789', '2023-10-30', 3),
('Tamim', 'Shadman', 987654321, 'tamimshadman@gmail.com', '12345678', '2023-10-31', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chatbot_hints`
--
ALTER TABLE `chatbot_hints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `founditem`
--
ALTER TABLE `founditem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `missingperson`
--
ALTER TABLE `missingperson`
  ADD PRIMARY KEY (`Sid`,`NID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`sid`,`Email`,`Mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chatbot_hints`
--
ALTER TABLE `chatbot_hints`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `founditem`
--
ALTER TABLE `founditem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `missingperson`
--
ALTER TABLE `missingperson`
  MODIFY `Sid` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
