-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-02-13 07:03:25
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `seisaku`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `birth_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `login`
--

INSERT INTO `login` (`id`, `name`, `password`, `image`, `birth_date`, `created_at`) VALUES
(1, 'bob', '$2y$10$deJb4xNzP9Q6qpur8h1BKOult.cpN7ggVr4XGM0KMumvycfA9IeVu', 'default.jpg', '2004-10-30', '2025-01-09 16:32:14'),
(2, 'aaa', '$2y$10$uKeYnU/Re80rlQbgN/b30uXeWYz2FyjHdL6zjyqACnNafy8pWdSDS', 'default.jpg', '2025-01-01', '2025-01-09 16:34:36'),
(3, '山田', '$2y$10$vJDTDlCpyuyfbty0VCCYR.GYWPcjPdH2OIox.EZGL9rM4g2As4whG', 'default.jpg', '2006-02-21', '2025-02-13 13:36:12');

-- --------------------------------------------------------

--
-- テーブルの構造 `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `evaluation` varchar(255) NOT NULL,
  `restaurantname` varchar(255) NOT NULL,
  `dishname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `review` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `published` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `review`
--

INSERT INTO `review` (`id`, `user_id`, `evaluation`, `restaurantname`, `dishname`, `username`, `review`, `image`, `tags`, `published`) VALUES
(25, 2, '⭐⭐⭐', '藤岡', '串カツ', 'aaa', 'うまい', '', '串カツ', '2025-02-13'),
(27, 2, '⭐⭐⭐⭐', '吉野家', '牛丼', 'aaa', 'とてもおいしい', '', '牛丼', '2025-02-13');

-- --------------------------------------------------------

--
-- テーブルの構造 `review_tags`
--

CREATE TABLE `review_tags` (
  `review_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `review_tags`
--

INSERT INTO `review_tags` (`review_id`, `tag_id`) VALUES
(25, 24),
(27, 25);

-- --------------------------------------------------------

--
-- テーブルの構造 `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(26, 'ステーキ'),
(24, '串カツ'),
(25, '牛丼');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- テーブルのインデックス `review_tags`
--
ALTER TABLE `review_tags`
  ADD PRIMARY KEY (`review_id`,`tag_id`),
  ADD KEY `fk_tag` (`tag_id`);

--
-- テーブルのインデックス `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- テーブルの AUTO_INCREMENT `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `review_tags`
--
ALTER TABLE `review_tags`
  ADD CONSTRAINT `fk_review` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
