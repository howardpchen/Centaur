CREATE DATABASE  IF NOT EXISTS `centaur` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `centaur`;
-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: howardpchen_primer
-- ------------------------------------------------------
-- Server version	5.1.56-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants` (
  `Username` varchar(45) NOT NULL,
  `PasswordHash` varchar(45) NOT NULL,
  `TrainingType` varchar(25) NOT NULL,
  `Email` varchar(100) NOT NULL,
  PRIMARY KEY (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quizzes` (
  `GameID` int(11) NOT NULL,
  `directory_name` varchar(45) NOT NULL,
  `Description` tinytext NOT NULL,
  PRIMARY KEY (`GameID`),
  UNIQUE KEY `GameID` (`GameID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `responses` (
  `UniqueID` varchar(45) NOT NULL,
  `Username` varchar(45) NOT NULL,
  `GameID` int(11) NOT NULL,
  `AttemptID` int(10) NOT NULL,
  `ResponseID` smallint(6) DEFAULT NULL,
  `QuestionID` smallint(6) DEFAULT NULL,
  `Tag` varchar(45) DEFAULT NULL,
  `QuestionText` text,
  `ResponseText` text,
  `PointValue` float DEFAULT NULL,
  `Notes` text,
  PRIMARY KEY (`UniqueID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-09-07  8:32:59
