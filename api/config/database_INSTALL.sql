SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `letterpress_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player1` varchar(32) NOT NULL,
  `player2` varchar(32) NOT NULL,
  `current_turn` varchar(32) NOT NULL,
  `board` text NOT NULL,
  `word_list` text,
  `game_status` varchar(32) NOT NULL,
  `skip_count` tinyint(1) NOT NULL,
  `winner` varchar(32),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `games`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--