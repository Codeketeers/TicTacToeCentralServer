-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 06, 2013 at 05:51 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `t3`
--

-- --------------------------------------------------------

--
-- Table structure for table `challenges`
--

CREATE TABLE IF NOT EXISTS `challenges` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Challenger` varchar(10) NOT NULL,
  `Challengee` varchar(10) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE IF NOT EXISTS `game` (
  `GameID` int(11) NOT NULL,
  `PlayerA` varchar(25) NOT NULL,
  `PlayerB` varchar(25) NOT NULL,
  `Turn` varchar(25) NOT NULL,
  `Winner` varchar(25) NOT NULL,
  PRIMARY KEY (`GameID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `move`
--

CREATE TABLE IF NOT EXISTS `move` (
  `MoveID` int(11) NOT NULL AUTO_INCREMENT,
  `GameID` int(11) NOT NULL,
  `Player` varchar(25) NOT NULL,
  `X` int(11) NOT NULL DEFAULT '-1',
  `Y` int(11) NOT NULL DEFAULT '-1',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flag` varchar(25) NOT NULL,
  PRIMARY KEY (`MoveID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=99 ;

--
-- Triggers `move`
--
DROP TRIGGER IF EXISTS `switchMove`;
DELIMITER //
CREATE TRIGGER `switchMove` BEFORE INSERT ON `move`
 FOR EACH ROW BEGIN
	DECLARE pA, pB,T CHAR(25);
	DECLARE cursor1 CURSOR FOR
	SELECT PlayerA, PlayerB, Turn FROM game WHERE GameID=NEW.GameID;
        
        OPEN cursor1;
        
        FETCH cursor1 INTO pA, pB, T;
        
        IF (T = pA) THEN
        	UPDATE game SET Turn=pB WHERE GameID=NEW.GameID;
        ELSE 
        	UPDATE game SET Turn=pA WHERE GameID=NEW.GameID;
        END IF;
        
        CLOSE cursor1;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `t3match`
--

CREATE TABLE IF NOT EXISTS `t3match` (
  `MatchID` int(11) NOT NULL AUTO_INCREMENT,
  `PlayerA` varchar(25) NOT NULL,
  `PlayerB` varchar(25) NOT NULL,
  `GameID1` int(11) NOT NULL,
  `GameID2` int(11) NOT NULL,
  `GameID3` int(11) NOT NULL,
  PRIMARY KEY (`MatchID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Triggers `t3match`
--
DROP TRIGGER IF EXISTS `setGameIDs`;
DELIMITER //
CREATE TRIGGER `setGameIDs` BEFORE INSERT ON `t3match`
 FOR EACH ROW BEGIN
	DECLARE num1 FLOAT;
	
	SET NEW.GameID1 = CONCAT(NEW.MatchID, "1");
	SET NEW.GameID2 = CONCAT(NEW.MatchID, "2");
	SET NEW.GameID3 = CONCAT(NEW.MatchID, "3");
	
	INSERT INTO game SET GameID=NEW.GameID1, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = New.PlayerA;
	INSERT INTO game SET GameID=NEW.GameID2, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = New.PlayerB;
	
	SET @num1 = (SELECT RAND());
	
	if @num1 >= .5 THEN
		INSERT INTO game SET GameID=NEW.GameID3, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = NEW.PlayerA;
	ELSE
		INSERT INTO game SET GameID=NEW.GameID3, PlayerA = NEW.PlayerA, PlayerB = NEW.PlayerB, Turn = NEW.PlayerB;
	END IF;
END
//
DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
