-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- --------------------------------------------------------

--
-- Table structure for table `rssite_categories`
--

CREATE TABLE `rssite_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_channels`
--

CREATE TABLE `rssite_channels` (
  `id` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET latin1 NOT NULL,
  `link` varchar(250) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_channels_categories`
--

CREATE TABLE `rssite_channels_categories` (
  `channel_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_comments`
--

CREATE TABLE `rssite_comments` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_posts`
--

CREATE TABLE `rssite_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `image` varchar(250) CHARACTER SET utf8 NOT NULL,
  `link` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `channel_id` int(11) NOT NULL DEFAULT '0',
  `date_inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_posts_categories`
--

CREATE TABLE `rssite_posts_categories` (
  `post_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rssite_users`
--

CREATE TABLE `rssite_users` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rssite_categories`
--
ALTER TABLE `rssite_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rssite_channels`
--
ALTER TABLE `rssite_channels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rssite_channels_categories`
--
ALTER TABLE `rssite_channels_categories`
  ADD PRIMARY KEY (`channel_id`,`cat_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `rssite_comments`
--
ALTER TABLE `rssite_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rssite_posts`
--
ALTER TABLE `rssite_posts`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `rssite_posts_categories`
--
ALTER TABLE `rssite_posts_categories`
  ADD PRIMARY KEY (`post_id`,`cat_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `rssite_users`
--
ALTER TABLE `rssite_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rssite_categories`
--
ALTER TABLE `rssite_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rssite_channels`
--
ALTER TABLE `rssite_channels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rssite_comments`
--
ALTER TABLE `rssite_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rssite_posts`
--
ALTER TABLE `rssite_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rssite_users`
--
ALTER TABLE `rssite_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `rssite_channels_categories`
--
ALTER TABLE `rssite_channels_categories`
  ADD CONSTRAINT `rssite_channels_categories_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `rssite_categories` (`id`),
  ADD CONSTRAINT `rssite_channels_categories_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `rssite_channels` (`id`);

--
-- Constraints for table `rssite_posts_categories`
--
ALTER TABLE `rssite_posts_categories`
  ADD CONSTRAINT `rssite_posts_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `rssite_posts` (`id`),
  ADD CONSTRAINT `rssite_posts_categories_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `rssite_categories` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
