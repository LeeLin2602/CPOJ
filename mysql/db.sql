-- MySQL dump 10.13  Distrib 5.7.33, for Linux (x86_64)
--
-- Host: localhost    Database: oj
-- ------------------------------------------------------
-- Server version	5.7.33-0ubuntu0.18.04.1

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
-- Table structure for table `Accounts`
--

DROP DATABASE IF EXISTS `oj`;
CREATE DATABASE `oj`;
use `oj`;

GRANT ALL PRIVILEGES ON *.* TO 'judge'@'%' IDENTIFIED BY 'a84dc85b974';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS `Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Accounts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Email` varchar(256) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `Submit_Times` int(11) DEFAULT '0',
  `AC_Times` int(11) DEFAULT '0',
  `Birthday` date DEFAULT NULL,
  `auth` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Problems`
--

DROP TABLE IF EXISTS `Problems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Problems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(30) NOT NULL DEFAULT 'New Problem',
  `Source` varchar(30) NOT NULL DEFAULT '',
  `Difficulty` int(11) NOT NULL DEFAULT '0',
  `Submit_Times` int(11) DEFAULT '0',
  `AC_Times` int(11) DEFAULT '0',
  `InCompetition` tinyint(1) NOT NULL DEFAULT '0',
  `isPublic` tinyint(1) DEFAULT '1',
  `Class` varchar(40) DEFAULT 'a',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Problems`
--

LOCK TABLES `Problems` WRITE;
/*!40000 ALTER TABLE `Problems` DISABLE KEYS */;
INSERT INTO `Problems` VALUES (1,'å…©æ•¸ä¹‹å’Œ','',0,0,0,0,1,'a'),(2,'èµ°æ ¼å­','',1,0,0,0,1,'a'),(3,'èµ°æ ¼å­ II','',0,0,0,0,1,'a'),(4,'æ–æ³¢é‚£å¥‘æ•¸åˆ—','',0,0,0,0,1,'a'),(5,'æ–æ³¢é‚£å¥‘æ•¸åˆ— II','',1,0,0,0,1,'a'),(6,'æ–æ³¢é‚£å¥‘æ•¸åˆ— III','',1,0,0,0,1,'a'),(7,'å¤§å¯Œç¿','',0,0,0,0,1,'a'),(8,'å¤§å¯Œç¿ II','',1, 0,0,0,1,'a'),(9,'æ˜Ÿçƒå»ºè¨­','ä¸­å’Œè³‡ç ”-Ray',2,0,0,0,1,'a'),(10,'æ‰¾é›¶å•é¡Œ','',1,0,0,0,1,'a'),(11,'ç«æŸ´æ”¶é›†ç‹‚å°æ˜Ž','æ°´é¡Œæ¨¡æ“¬è³½ 2nd',1,0,0,0,1,'a'),(12,'æ›¸æœ¬æ”¶é›†ç‹‚å°æ˜Ž','æ°´é¡Œæ¨¡æ“¬è³½ 2nd',2,0,0,0,1,'a'),(13,'è·³æˆ¿å­','æ°´é¡Œæ¨¡æ“¬è³½ 2nd',1,0,0,0,1,'a'),(14,'pD æ°´é¡Œæ¨¡æ“¬è³½ 2nd','æ°´é¡Œæ¨¡æ“¬è³½ 2nd',0,0,0,1,0,'a'),(16,'æŽ’åˆ—çµ„åˆ','PizzaMan',1,0,0,0,1,'a');
/*!40000 ALTER TABLE `Problems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Solutions`
--

DROP TABLE IF EXISTS `Solutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Solutions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Language` varchar(20) NOT NULL,
  `ProblemID` int(11) NOT NULL,
  `Hash` varchar(128) NOT NULL,
  `Submitter` int(11) NOT NULL,
  `Status` tinyint(4) NOT NULL DEFAULT '0',
  `Upload_time` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=513 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `Static`
--

DROP TABLE IF EXISTS `Static`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Static` (
  `ProblemID` int(11) NOT NULL,
  `Submitter` int(11) NOT NULL,
  `Status` tinyint(4) NOT NULL DEFAULT '0',
  `Score` int(11) DEFAULT '0',
  UNIQUE KEY `Problem_Submitter` (`ProblemID`,`Submitter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tags` (
  `KeyID` int(11) NOT NULL,
  `ProblemID` int(11) NOT NULL,
  PRIMARY KEY (`KeyID`,`ProblemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Tags`
--

LOCK TABLES `Tags` WRITE;
/*!40000 ALTER TABLE `Tags` DISABLE KEYS */;
INSERT INTO `Tags` VALUES (1,2),(1,9),(1,10),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,10),(2,13),(3,4),(3,5),(3,6),(3,15),(3,16),(4,4),(4,5),(4,6),(5,6),(6,9),(6,10),(6,13),(7,9),(8,9),(9,9),(10,14),(12,12),(13,12),(14,12),(15,11),(15,13),(16,15),(16,16);
/*!40000 ALTER TABLE `Tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TagsName`
--

DROP TABLE IF EXISTS `TagsName`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TagsName` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `KeyName` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TagsName`
--

LOCK TABLES `TagsName` WRITE;
/*!40000 ALTER TABLE `TagsName` DISABLE KEYS */;
INSERT INTO `TagsName` VALUES (1,'DFS'),(2,'DP'),(3,'æ•¸å­¸'),(4,'å¿«é€Ÿå†ª'),(5,'å¤§æ•¸é‹ç®—'),(6,'BFS'),(7,'è¨˜æ†¶åŒ–'),(8,'Dijkstra'),(9,'A* Search'),(10,''),(11,'è²ªå¿ƒ'),(12,'Hash'),(13,'Sort'),(14,'Map'),(15,'Greedy'),(16,'å›žæº¯');
/*!40000 ALTER TABLE `TagsName` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-02-23 13:58:11
