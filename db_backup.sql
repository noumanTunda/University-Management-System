
sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: homestead
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-6 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sectors_id` int(10) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL COMMENT 'Account transaction amount – supports up to 1 B',
  `date` date NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_sectors_id_foreign` (`sectors_id`),
  CONSTRAINT `accounts_sectors_id_foreign` FOREIGN KEY (`sectors_id`) REFERENCES `sectors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES
(1,1,1000.00,'2025-01-01','Sample income','2026-06-04 23:09:41','2026-06-04 23:09:41',NULL),
(2,2,1000.00,'2024-01-01','Sample income','2026-06-04 23:11:13','2026-06-04 23:11:13',NULL),
(3,3,1000.00,'2026-06-05','Sample income','2026-06-04 23:19:53','2026-06-04 23:19:53',NULL),
(10,11,999999.99,'2026-06-05',' Tution Fees for CSE Department','2026-06-05 10:51:41','2026-06-05 10:51:41',NULL),
(11,14,1500000.00,'2026-06-05','From Tuition Fees','2026-06-05 12:11:25','2026-06-05 12:11:25',NULL);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `session` varchar(15) NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `students_id` int(10) unsigned NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `levelTerm` varchar(20) NOT NULL,
  `present` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_subject_id_foreign` (`subject_id`),
  KEY `attendances_students_id_foreign` (`students_id`),
  KEY `attendances_department_id_foreign` (`department_id`),
  CONSTRAINT `attendances_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
  CONSTRAINT `attendances_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`),
  CONSTRAINT `attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `author` varchar(100) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `rackNo` varchar(10) NOT NULL,
  `rowNo` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `desc` varchar(500) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_code_unique` (`code`),
  KEY `books_department_id_foreign` (`department_id`),
  CONSTRAINT `books_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES
(1,'BK001','Sample Book','Author Name',5,'A1','1','Textbook','Sample description',1,'2026-06-04 23:20:20','2026-06-04 23:20:20',NULL),
(4,'CS-101-INTRO','Introduction to Algorithms','Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein',15,'R-01','W-02','Academic','A fundamental textbook providing a comprehensive introduction to the modern study of computer algorithms. ',1,'2026-06-05 09:58:37','2026-06-05 12:59:43',NULL),
(5,'CS-202-DSAL','Data Structures and Algorithm Analysis in C++','Mark Allen Weiss',10,'R-01','W-03','Academic','Core textbook focusing on data structures, algorithm analysis, and advanced programming concepts using C++.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(6,'CS-301-DBMS','Database System Concepts','Abraham Silberschatz, Henry F. Korth, S. Sudarshan',12,'R-02','W-01','Academic','Covers fundamental concepts of database management systems including SQL, relational design, and transaction management.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(7,'CS-204-OSYS','Operating System Concepts','Abraham Silberschatz, Peter B. Galvin, Greg Gagne',8,'R-02','W-04','Academic','The standard reference for understanding operating system architectures, process management, memory, and storage virtualization.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(8,'CS-401-NETW','Computer Networking: A Top-Down Approach','James F. Kurose, Keith W. Ross',20,'R-03','W-01','Academic','An layered approach focusing on the Internet and upper-layer application environments down to the physical layer.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(9,'CS-305-SENG','Software Engineering: A Practitioner\'s Approach','Roger S. Pressman, Bruce Maxim',7,'R-04','W-02','Academic','A complete guide to software processes, agile development methodologies, architectural design, and software testing strategies.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(10,'CS-410-CYBER','Computer Security: Principles and Practice','William Stallings, Lawrie Brown',5,'R-05','W-03','Academic','Comprehensive textbook detailing technical security fundamentals, cryptography, network security, and digital forensics principles.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL),
(11,'CS-102-JAVA','Introduction to Java Programming and Data Structures','Y. Daniel Liang',25,'R-01','W-01','Academic','A fundamentals-first textbook covering core object-oriented programming concepts and language building blocks.',1,'2026-06-05 09:58:37','2026-06-05 09:58:37',NULL);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBookAdd` AFTER INSERT ON `books` FOR EACH ROW
      BEGIN
      insert into stock_books
      set
      books_id = new.id,
      quantity = new.quantity;
      END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBookUpdate` AFTER UPDATE ON `books` FOR EACH ROW
      BEGIN
      UPDATE stock_books
      set
      quantity = new.quantity-(old.quantity-quantity)
      WHERE books_id=old.id;
      END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBookDelete` AFTER DELETE ON `books` FOR EACH ROW
      BEGIN
      delete from borrow_books where books_id = old.id;
      delete from stock_books where books_id = old.id;
      END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `borrow_books`
--

DROP TABLE IF EXISTS `borrow_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrow_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `students_id` int(10) unsigned NOT NULL,
  `books_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `issueDate` date NOT NULL,
  `returnDate` date NOT NULL,
  `fine` decimal(18,2) NOT NULL DEFAULT 0.00,
  `Status` varchar(10) NOT NULL DEFAULT 'Borrowed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrow_books_books_id_foreign` (`books_id`),
  KEY `borrow_books_students_id_foreign` (`students_id`),
  CONSTRAINT `borrow_books_books_id_foreign` FOREIGN KEY (`books_id`) REFERENCES `books` (`id`),
  CONSTRAINT `borrow_books_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_books`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `borrow_books` WRITE;
/*!40000 ALTER TABLE `borrow_books` DISABLE KEYS */;
/*!40000 ALTER TABLE `borrow_books` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBorrowBookAdd` AFTER INSERT ON `borrow_books` FOR EACH ROW
      BEGIN
      UPDATE stock_books
      set quantity = quantity-new.quantity
      where books_id=new.books_id;
      END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBorrowBookUpdate` AFTER UPDATE ON `borrow_books` FOR EACH ROW
      IF (new.Status='Returned') THEN
      UPDATE stock_books
      set quantity = quantity+new.quantity
      WHERE books_id=new.books_id;
      END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `afterBorrowBookDelete` AFTER DELETE ON `borrow_books` FOR EACH ROW
      IF (old.Status='Borrowed') THEN
      UPDATE stock_books
      set quantity = quantity+old.quantity
      WHERE books_id=old.books_id;
      END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `expiration` int(11) NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `course_subject`
--

DROP TABLE IF EXISTS `course_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `semester` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_course_subject` (`course_id`,`subject_id`,`semester`),
  KEY `fk_course_subject_subject` (`subject_id`),
  CONSTRAINT `fk_course_subject_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_course_subject_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_subject`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `course_subject` WRITE;
/*!40000 ALTER TABLE `course_subject` DISABLE KEYS */;
INSERT INTO `course_subject` VALUES
(36,4,9,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(37,4,4,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(38,4,5,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(39,4,7,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(40,4,3,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(41,4,8,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(42,4,6,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(43,4,1,1,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(44,4,11,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(45,4,2,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(46,4,12,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(47,4,14,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(48,4,13,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(49,4,15,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(50,4,10,2,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(51,4,21,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(52,4,17,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(53,4,23,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(54,4,20,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(55,4,18,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(56,4,22,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(57,4,19,3,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(58,4,28,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(59,4,58,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(60,4,31,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(61,4,25,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(62,4,27,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(63,4,26,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(64,4,29,4,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(65,4,33,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(66,4,51,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(67,4,56,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(68,4,38,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(69,4,35,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(70,4,37,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(71,4,54,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(72,4,32,5,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(73,4,41,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(74,4,67,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(75,4,48,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(76,4,44,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(77,4,66,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(78,4,42,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(79,4,47,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(80,4,45,6,'2026-06-07 02:08:50','2026-06-07 02:08:50'),
(81,5,1,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(82,5,3,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(83,5,4,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(84,5,5,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(85,5,6,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(86,5,7,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(87,5,8,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(88,5,9,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(89,5,2,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(90,5,11,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(91,5,12,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(92,5,13,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(93,5,16,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(94,5,24,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(95,5,17,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(96,5,18,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(97,5,19,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(98,5,20,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(99,5,22,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(100,5,23,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(101,5,25,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(102,5,28,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(103,5,30,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(104,5,31,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(105,5,32,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(106,5,35,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(107,5,36,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(108,5,38,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(109,5,46,6,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(110,5,47,6,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(111,5,49,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(112,5,55,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(113,5,62,8,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(114,5,63,8,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(115,6,1,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(116,6,3,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(117,6,4,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(118,6,5,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(119,6,7,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(120,6,8,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(121,6,2,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(122,6,11,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(123,6,12,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(124,6,14,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(125,6,16,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(126,6,19,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(127,6,20,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(128,6,22,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(129,6,23,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(130,6,28,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(131,6,30,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(132,6,51,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(133,6,38,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(134,6,39,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(135,6,55,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(136,6,59,8,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(137,6,62,8,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(138,6,65,8,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(139,7,1,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(140,7,3,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(141,7,4,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(142,7,5,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(143,7,6,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(144,7,8,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(145,7,9,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(146,7,2,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(147,7,11,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(148,7,12,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(149,7,13,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(150,7,16,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(151,7,24,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(152,7,17,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(153,7,18,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(154,7,19,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(155,7,20,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(156,7,22,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(157,7,23,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(158,7,25,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(159,7,26,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(160,7,28,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(161,7,30,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(162,7,31,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(163,7,32,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(164,7,35,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(165,7,43,6,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(166,7,46,6,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(167,7,47,6,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(168,7,49,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(169,7,55,7,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(170,8,1,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(171,8,3,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(172,8,4,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(173,8,5,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(174,8,7,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(175,8,2,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(176,8,11,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(177,8,12,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(178,8,13,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(179,8,14,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(180,8,18,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(181,8,19,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(182,8,22,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(183,8,25,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(184,8,28,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(185,8,30,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(186,8,31,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(187,8,35,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(188,8,38,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(189,8,52,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(190,8,55,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(191,8,56,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(192,10,1,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(193,10,3,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(194,10,4,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(195,10,5,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(196,10,7,1,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(197,10,2,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(198,10,11,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(199,10,12,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(200,10,13,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(201,10,14,2,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(202,10,18,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(203,10,19,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(204,10,22,3,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(205,10,25,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(206,10,28,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(207,10,30,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(208,10,31,4,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(209,10,35,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(210,10,37,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(211,10,38,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(212,10,55,5,'2026-06-06 23:34:52','2026-06-06 23:34:52'),
(213,18,9,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(214,18,4,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(215,18,5,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(216,18,7,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(217,18,3,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(218,18,8,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(219,18,6,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(220,18,1,1,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(221,18,11,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(222,18,2,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(223,18,12,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(224,18,14,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(225,18,13,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(226,18,15,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(227,18,16,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(228,18,10,2,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(229,18,21,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(230,18,33,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(231,18,17,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(232,18,23,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(233,18,20,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(234,18,18,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(235,18,22,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(236,18,19,3,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(237,18,28,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(238,18,58,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(239,18,31,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(240,18,25,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(241,18,27,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(242,18,26,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(243,18,30,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(244,18,29,4,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(245,18,51,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(246,18,56,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(247,18,35,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(248,18,34,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(249,18,37,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(250,18,54,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(251,18,49,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(252,18,36,5,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(253,18,41,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(254,18,48,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(255,18,44,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(256,18,66,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(257,18,42,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(258,18,43,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(259,18,45,6,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(260,18,53,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(261,18,39,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(262,18,52,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(263,18,38,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(264,18,55,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(265,18,57,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(266,18,50,7,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(267,18,62,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(268,18,64,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(269,18,59,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(270,18,65,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(271,18,60,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(272,18,61,8,'2026-06-07 12:29:18','2026-06-07 12:29:18'),
(273,18,63,8,'2026-06-07 12:29:18','2026-06-07 12:29:18');
/*!40000 ALTER TABLE `course_subject` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `duration_years` int(11) NOT NULL DEFAULT 4,
  `min_credits` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_courses_department` (`department_id`),
  CONSTRAINT `fk_courses_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES
(4,'Bachelor of Science in Computer Science','BSc CS',1,3,366.00,'2026-06-07 02:08:50','2026-06-07 12:16:51','2026-06-07 12:16:51'),
(5,'Bachelor of Science in Computer Networks and Information Security Engineering','BSc CNISE',2,4,495.30,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(6,'Bachelor of Science in Computer Engineering','BSc CE',2,4,489.30,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(7,'Bachelor of Science in Cyber Security and Digital Forensics Engineering','BSc CSDFE',1,4,501.30,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(8,'Bachelor of Science in Business Information Systems','BSc BIS',3,3,383.20,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(9,'Bachelor of Science in Health Information Systems','BSc HIS',3,3,366.60,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(10,'Bachelor of Science in Information Systems','BSc IS',3,3,373.70,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(11,'Bachelor of Science in Instructional Design and Information Technology','BSc IDIT',1,3,368.20,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(12,'Bachelor of Science in Multimedia Technology and Animation','BSc MTA',2,3,368.20,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(13,'Bachelor of Science in Telecommunication Engineering','BSc TE',2,4,488.80,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(14,'Bachelor of Science in Digital Content and Broadcasting Engineering','BSc DCBE',2,4,488.80,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(15,'Diploma in Cyber Security and Digital Forensics','Dip. CSDF',1,2,254.70,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(16,'Diploma in Educational Technology','Dip. ET',1,2,308.00,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(17,'Diploma in Information and Communication Technology','Dip. ICT',1,2,285.00,'2026-06-06 23:33:38','2026-06-06 23:33:38',NULL),
(18,'Bachelor of Science in Software Engineering','BSc SE',1,4,498.00,'2026-06-07 12:29:18','2026-06-07 12:29:18',NULL);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `credit` varchar(20) NOT NULL,
  `years` varchar(20) NOT NULL,
  `description` varchar(250) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES
(1,'Computer Science and Engineering','CSE','3','4','CSE Department','2026-06-04 23:02:08','2026-06-05 11:10:45',NULL),
(2,'Electronics and Telecommunication Engineering','ETE','3','4','ETE Department','2026-06-04 23:03:34','2026-06-04 23:03:34',NULL),
(3,'Information Systems Technology','IST','3','3','IST Department','2026-06-04 23:03:50','2026-06-05 10:39:07',NULL);
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `dormitories`
--

DROP TABLE IF EXISTS `dormitories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dormitories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `numOfRoom` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `dormitories` WRITE;
/*!40000 ALTER TABLE `dormitories` DISABLE KEYS */;
INSERT INTO `dormitories` VALUES
(1,'BLOCK 1',256,'CIVE','MALE ONLY','2026-06-04 23:24:45','2026-06-05 11:37:10',NULL),
(2,'BLOCK 2',256,'CIVE','MIXTURE','2026-06-04 23:47:08','2026-06-05 11:37:32',NULL),
(3,'BLOCK 3',256,'CIVE','MALE ONLY','2026-06-04 23:50:11','2026-06-05 11:37:55',NULL),
(4,'BLOCK 4',256,'CIVE','MALE ONLY','2026-06-05 11:38:11','2026-06-05 11:38:11',NULL),
(5,'BLOCK 5',256,'CIVE','MIXTURE','2026-06-05 11:38:23','2026-06-05 11:38:23',NULL),
(6,'BLOCK 6',256,'CIVE','FEMALE ONLY','2026-06-05 11:38:40','2026-06-05 11:38:40',NULL);
/*!40000 ALTER TABLE `dormitories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `dormitory_fees`
--

DROP TABLE IF EXISTS `dormitory_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dormitory_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `students_id` int(10) unsigned NOT NULL,
  `dormitory_students_id` int(10) unsigned NOT NULL,
  `feeMonth` date NOT NULL,
  `feeAmount` decimal(12,2) NOT NULL COMMENT 'Dormitory fee amount – supports up to 1 B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dormitory_fees_students_id_foreign` (`students_id`),
  KEY `dormitory_fees_dormitory_students_id_foreign` (`dormitory_students_id`),
  CONSTRAINT `dormitory_fees_dormitory_students_id_foreign` FOREIGN KEY (`dormitory_students_id`) REFERENCES `dormitory_students` (`id`),
  CONSTRAINT `dormitory_fees_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitory_fees`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `dormitory_fees` WRITE;
/*!40000 ALTER TABLE `dormitory_fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `dormitory_fees` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `dormitory_students`
--

DROP TABLE IF EXISTS `dormitory_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dormitory_students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `students_id` int(10) unsigned NOT NULL,
  `dormitories_id` int(10) unsigned NOT NULL,
  `joinDate` date NOT NULL,
  `leaveDate` date DEFAULT NULL,
  `roomNo` varchar(255) NOT NULL,
  `monthlyFee` decimal(10,2) NOT NULL,
  `isActive` varchar(3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dormitory_students_dormitories_id_foreign` (`dormitories_id`),
  KEY `dormitory_students_students_id_foreign` (`students_id`),
  CONSTRAINT `dormitory_students_dormitories_id_foreign` FOREIGN KEY (`dormitories_id`) REFERENCES `dormitories` (`id`),
  CONSTRAINT `dormitory_students_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dormitory_students`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `dormitory_students` WRITE;
/*!40000 ALTER TABLE `dormitory_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `dormitory_students` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(10) unsigned NOT NULL,
  `session` varchar(15) NOT NULL,
  `levelTerm` varchar(20) NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `students_id` int(10) unsigned NOT NULL,
  `exam` varchar(255) NOT NULL,
  `raw_score` decimal(6,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(6,2) NOT NULL DEFAULT 0.00,
  `weight` decimal(6,2) NOT NULL DEFAULT 0.00,
  `percentage_x_weight` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_subject_id_foreign` (`subject_id`),
  KEY `exams_students_id_foreign` (`students_id`),
  KEY `exams_department_id_foreign` (`department_id`),
  CONSTRAINT `exams_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
  CONSTRAINT `exams_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`),
  CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES
(17,1,'2024-2025','L1T1',1,3,'Midterm Exam',20.00,20.00,20.00,20.00,'2026-06-11 16:26:08',NULL,NULL),
(18,1,'2024-2025','L1T1',1,3,'Final Exam',20.00,20.00,20.00,20.00,'2026-06-11 16:28:36',NULL,NULL);
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `fee_collection_items`
--

DROP TABLE IF EXISTS `fee_collection_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_collection_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fee_collections_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_collection_items_fee_collections_id_foreign` (`fee_collections_id`),
  CONSTRAINT `fee_collection_items_fee_collections_id_foreign` FOREIGN KEY (`fee_collections_id`) REFERENCES `fee_collections` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_collection_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `fee_collection_items` WRITE;
/*!40000 ALTER TABLE `fee_collection_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_collection_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `fee_collections`
--

DROP TABLE IF EXISTS `fee_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_collections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `students_id` int(10) unsigned NOT NULL,
  `payableAmount` decimal(18,2) NOT NULL,
  `lateFee` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paidAmount` decimal(18,2) NOT NULL,
  `dueAmount` decimal(18,2) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payDate` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_collections_students_id_foreign` (`students_id`),
  CONSTRAINT `fee_collections_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_collections`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `fee_collections` WRITE;
/*!40000 ALTER TABLE `fee_collections` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_collections` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `fees`
--

DROP TABLE IF EXISTS `fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL COMMENT 'Fee amount – supports up to 1 B',
  `description` text DEFAULT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fees_department_id_foreign` (`department_id`),
  CONSTRAINT `fees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `fees` WRITE;
/*!40000 ALTER TABLE `fees` DISABLE KEYS */;
INSERT INTO `fees` VALUES
(10,'Registration Fee',60000.00,'Registration fee for CSE',1,'2026-06-05 11:48:57','2026-06-05 11:48:57',NULL),
(11,'Contribution to UDOSO',10000.00,' Contribution to UDOSO',1,'2026-06-05 11:50:03','2026-06-05 11:50:03',NULL),
(12,'NHIF Collection',50400.00,' NHIF Collection',1,'2026-06-05 11:50:29','2026-06-05 11:50:29',NULL),
(14,'Registration Fee',60000.00,' Registration Fee for ETE',2,'2026-06-05 11:52:00','2026-06-05 11:52:00',NULL),
(15,'Registration Fee',60000.00,' Registration Fee for IST',3,'2026-06-05 11:52:28','2026-06-05 11:52:28',NULL),
(16,'Contribution to UDOSO',10000.00,' Contribution to UDOSO',2,'2026-06-05 11:52:51','2026-06-05 11:52:51',NULL),
(17,'Contribution to UDOSO',10000.00,' Contribution to UDOSO',3,'2026-06-05 11:53:01','2026-06-05 11:53:01',NULL),
(20,'NHIF Collection',50400.00,'NHIF Collection',2,'2026-06-05 11:55:16','2026-06-05 11:55:16',NULL),
(21,'NHIF Collection',50400.00,'NHIF Collection ',3,'2026-06-05 11:55:28','2026-06-05 11:55:28',NULL),
(23,'Acomodation Fee',183750.00,'Acomodation Fee',1,'2026-06-05 14:11:30','2026-06-05 14:11:30',NULL),
(24,'Acomodation Fee',183750.00,'Acomodation Fee',2,'2026-06-05 14:11:59','2026-06-05 14:11:59',NULL),
(25,'Acomodation Fee',183750.00,' Acomodation Fee',3,'2026-06-05 14:12:22','2026-06-05 14:12:22',NULL),
(30,'Tuition Fee',1500000.00,' Tuition Fee for CSE Department',1,'2026-06-07 12:50:13','2026-06-07 12:50:13',NULL),
(31,'Tuition Fee',1500000.00,'  Tuition Fee for ETE Department',2,'2026-06-07 12:50:37','2026-06-07 12:50:37',NULL),
(32,'Tuition Fee',1200000.00,' Tuition Fee for IST Department',3,'2026-06-07 12:51:13','2026-06-07 12:51:13',NULL);
/*!40000 ALTER TABLE `fees` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `institute`
--

DROP TABLE IF EXISTS `institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `institute` (
  `name` varchar(250) NOT NULL,
  `establish` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `web` varchar(80) NOT NULL,
  `phoneNo` varchar(15) NOT NULL,
  `address` varchar(250) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institute`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `institute` WRITE;
/*!40000 ALTER TABLE `institute` DISABLE KEYS */;
INSERT INTO `institute` VALUES
('Testing CIVE','2026','testinginstitute@example.com','www.cive.com','+255000000000','Tanzania','2026-06-05 11:33:04','2026-06-05 11:33:04');
/*!40000 ALTER TABLE `institute` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
('2016_06_30_100938_CreateTableUsers',1),
('2016_07_01_064012_CreateTableInstitute',1),
('2016_07_01_165014_CreateTableDepartment',1),
('2016_07_01_195232_CreateTableSubject',1),
('2016_07_25_072232_createTableStudents',1),
('2016_08_31_172636_create_registrations_table',1),
('2016_09_07_114437_create_attendances_table',1),
('2016_09_08_112048_create_sectors_table',1),
('2016_09_08_112154_create_accounts_table',1),
('2016_09_08_161127_create_exams_table',1),
('2016_09_19_112343_create_fees_table',1),
('2016_09_19_120719_create_fee_collections_table',1),
('2016_12_25_131633_create_books_table',1),
('2016_12_25_141150_create_borrow_books_table',1),
('2016_12_25_142435_create_tables_book_stock_and_triggers',1),
('2017_01_19_210413_create_dormitories_table',1),
('2017_01_19_210618_create_dormitory_students_table',1),
('2017_01_19_210640_create_dormitory_fees_table',1),
('2017_01_20_132715_create_cache_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `levelTerm` varchar(20) NOT NULL,
  `session` varchar(15) NOT NULL,
  `students_id` int(10) unsigned NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registrations_students_id_foreign` (`students_id`),
  KEY `registrations_department_id_foreign` (`department_id`),
  CONSTRAINT `registrations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
  CONSTRAINT `registrations_students_id_foreign` FOREIGN KEY (`students_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
INSERT INTO `registrations` VALUES
(65,'L1T1','2024-2025',3,1,'2026-06-11 16:19:49','2026-06-11 16:19:49',NULL),
(66,'L1T2','2024-2025',3,1,'2026-06-11 16:20:04','2026-06-11 16:20:04',NULL);
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sectors`
--

DROP TABLE IF EXISTS `sectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sectors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('Income','Expence') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sectors`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sectors` WRITE;
/*!40000 ALTER TABLE `sectors` DISABLE KEYS */;
INSERT INTO `sectors` VALUES
(1,'Income','Income','2026-06-04 23:09:41','2026-06-05 10:50:05','2026-06-05 10:50:05'),
(2,'Income','Income','2026-06-04 23:11:13','2026-06-05 10:50:10','2026-06-05 10:50:10'),
(3,'Income','Income','2026-06-04 23:19:53','2026-06-05 12:10:44','2026-06-05 12:10:44'),
(10,'Information Systems Technology','Income','2026-06-05 10:50:00','2026-06-05 12:10:32','2026-06-05 12:10:32'),
(11,'Computer Science Department','Income','2026-06-05 10:50:29','2026-06-05 12:09:54','2026-06-05 12:09:54'),
(12,'Electronics and Telecommunication Engineering Department','Income','2026-06-05 10:50:59','2026-06-05 12:10:19','2026-06-05 12:10:19'),
(13,'Fees','Income','2026-06-05 11:57:14','2026-06-05 12:10:40','2026-06-05 12:10:40'),
(14,'Computer Science and Engineering','Income','2026-06-05 12:09:45','2026-06-05 12:09:45',NULL),
(15,'Electronics and Telecommunication Engineering ','Income','2026-06-05 12:10:14','2026-06-05 12:10:14',NULL),
(16,'Information Systems Technology','Income','2026-06-05 12:10:28','2026-06-05 12:10:28',NULL),
(17,'Fees','Income','2026-06-05 12:12:05','2026-06-05 12:12:05',NULL);
/*!40000 ALTER TABLE `sectors` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `stock_books`
--

DROP TABLE IF EXISTS `stock_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_books` (
  `books_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  KEY `stock_books_books_id_foreign` (`books_id`),
  CONSTRAINT `stock_books_books_id_foreign` FOREIGN KEY (`books_id`) REFERENCES `books` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_books`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `stock_books` WRITE;
/*!40000 ALTER TABLE `stock_books` DISABLE KEYS */;
INSERT INTO `stock_books` VALUES
(1,5),
(4,15),
(5,10),
(6,12),
(7,8),
(8,20),
(9,7),
(10,5),
(11,25);
/*!40000 ALTER TABLE `stock_books` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idNo` varchar(20) NOT NULL,
  `batchNo` varchar(20) DEFAULT NULL,
  `session` varchar(15) NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `bncReg` varchar(50) NOT NULL,
  `firstName` varchar(60) NOT NULL,
  `middleName` varchar(60) NOT NULL,
  `lastName` varchar(60) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `religion` varchar(15) NOT NULL,
  `bloodgroup` varchar(10) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `photo` varchar(30) NOT NULL,
  `mobileNo` varchar(15) NOT NULL,
  `fatherName` varchar(180) NOT NULL,
  `fatherMobileNo` varchar(15) NOT NULL,
  `motherName` varchar(180) NOT NULL,
  `motherMobileNo` varchar(15) NOT NULL,
  `localGuardian` varchar(180) NOT NULL,
  `localGuardianMobileNo` varchar(15) NOT NULL,
  `presentAddress` varchar(500) NOT NULL,
  `parmanentAddress` varchar(500) NOT NULL,
  `isActive` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_idno_unique` (`idNo`),
  KEY `students_department_id_foreign` (`department_id`),
  KEY `fk_students_course` (`course_id`),
  CONSTRAINT `fk_students_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES
(3,'T24-03-00000','2024-2025','2024-2025',1,18,'T24-03-00000','Nouman','Abdallah','Tunda','Male','Cristian','O-','Tanzanian','2004-03-30','T24-03-00000.jpeg','+255 7000000000','Original Father','+255 7111111111','Original Mother','+255 7222222222','','','DODOMA','DODOMA\r\n									\r\n									\r\n									','1','2026-06-05 12:45:30','2026-06-11 15:52:09',NULL),
(4,'T21-03-12111','2021-2022','2021-2022',1,NULL,'T21-03-12111','John','A.','Doe','Male','Islam','O+','American','2000-01-01','default.png','+255 123456789_','Father Name','+255 123456789_','Mother Name','+255 123456789_','','','Address 1','Address 2\r\n									','1','2026-06-05 13:03:32','2026-06-07 02:11:26',NULL),
(5,'T24-03-11111','2024-2025','2024-2025',1,NULL,'T24-03-11111','New','Student','One','Male','Islam','O+','Tanzanian','2005-01-01','default.png','+255 987654321_','Father Two','+255 123456789_','Mother Name','+255 123456789_','','','Dodoma','Dodoma\r\n									\r\n									','1','2026-06-05 13:03:32','2026-06-07 02:11:39',NULL),
(6,'T22-03-10001','2022-2023','2022-2023',1,NULL,'T22-03-10001','Chebet','Ali','Mbowe','Female','Muslim','O+','Kenyan','2001-04-03','default.png','0783197857','Baraka Kenyatta','0721668732','Wanjiku Karume','0789254563','','','Bagamoyo, Tanzania','Bagamoyo, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(7,'T23-03-10002','2023-2024','2023-2024',3,NULL,'T23-03-10002','Chebet','Kinuthia','Omwamba','Female','Islam','A+','Kenyan','2000-06-25','T23-03-10002.png','+255 0731429110','Njoroge Kibaki','+255 0766722344','Jelagat Kikwete','+255 0755667651','','','Arusha, Tanzania','Arusha, Tanzania\r\n									','1','2026-06-05 13:15:20','2026-06-05 20:43:54',NULL),
(8,'T24-03-10003','2024-2025','2024-2025',2,NULL,'T24-03-10003','Rehema','Mbugua','Mvurya','Female','Other','B-','Kenyan','2000-02-23','default.png','0720576383','Odhiambo Waiguru','0784093639','Subira Murkomen','0749349722','','','Geita, Tanzania','Geita, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(9,'T24-03-10004','2024-2025','2024-2025',1,NULL,'T24-03-10004','Mutua','Mwendwa','Samia','Male','Christian','A-','Tanzanian','2005-11-23','default.png','0758966946','Otieno Mvurya','0731831063','Jelagat Waiguru','0759684848','','','Arusha, Tanzania','Arusha, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(10,'T23-03-10005','2023-2024','2023-2024',3,NULL,'T23-03-10005','Omondi','Mwendwa','Shein','Male','Christian','A-','Kenyan','2003-06-26','default.png','0784752529','Njoroge Mvurya','0739476249','Jelagat Lissu','0753524491','','','Iringa, Tanzania','Njombe, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(11,'T24-03-10006','2024-2025','2024-2025',1,NULL,'T24-03-10006','Nekesa','Odhiambo','Kenyatta','Female','Christian','AB+','Tanzanian','2001-04-12','default.png','0743101783','Musyoka Lissu','0785345555','Njeri Kenyatta','0782340307','','','Bagamoyo, Tanzania','Bagamoyo, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(12,'T22-03-10007','2022-2023','2022-2023',1,NULL,'T22-03-10007','Simiyu','Onyango','Mvurya','Male','Muslim','B+','Kenyan','2000-12-22','default.png','0761220073','Omondi Kikwete','0789978790','Nafula Magufuli','0772820592','','','Geita, Tanzania','Geita, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(13,'T24-03-10008','2024-2025','2024-2025',3,NULL,'T24-03-10008','Wanjiku','Kariuki','Mwinyi','Female','Muslim','O-','Tanzanian','2004-11-27','default.png','0745351479','Bakari Ngilu','0777187530','Shani Waiguru','0733978249','','','Lindi, Tanzania','Songea, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(14,'T23-03-10009','2023-2024','2023-2024',3,NULL,'T23-03-10009','Wafula','Mbugua','Kingi','Male','Christian','O-','Kenyan','2006-08-24','default.png','0751273847','Faraji Kihika','0742138745','Neema Murkomen','0717774584','','','Arusha, Tanzania','Arusha, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(15,'T23-03-10010','2023-2024','2023-2024',1,NULL,'T23-03-10010','Subira','Musyoka','Ngilu','Female','Other','B+','Tanzanian','2002-11-22','default.png','0738427073','Omondi Wetangula','0782383095','Zawadi Majaliwa','0736998038','','','Shinyanga, Tanzania','Shinyanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(16,'T25-03-10011','2025-2026','2025-2026',1,NULL,'T25-03-10011','Baraka','Cheruiyot','Mudavadi','Male','Christian','O+','Kenyan','2006-01-03','default.png','0788979095','Mwangi Joho','0739557077','Neema Kibaki','0710965138','','','Moshi, Tanzania','Mbeya, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(17,'T24-03-10012','2024-2025','2024-2025',3,NULL,'T24-03-10012','Chepngetich','Chebet','Mudavadi','Female','Christian','O-','Tanzanian','2003-07-12','default.png','0773481353','Kipkorir Kenyatta','0764634663','Pendo Kibaki','0735556386','','','Shinyanga, Tanzania','Shinyanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(18,'T22-03-10013','2022-2023','2022-2023',3,NULL,'T22-03-10013','Kwame','Kinuthia','Murkomen','Male','Muslim','AB-','Kenyan','2002-03-14','default.png','0743374088','Maina Mwinyi','0735714784','Anyango Kikwete','0735529407','','','Shinyanga, Tanzania','Shinyanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(19,'T22-03-10014','2022-2023','2022-2023',2,NULL,'T22-03-10014','Kwame','Mbugua','Mkapa','Male','Muslim','AB-','Kenyan','2000-07-28','default.png','0741726318','Simiyu Moi','0732321899','Neema Matiang\'i','0764547971','','','Tanga, Tanzania','Tanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(20,'T25-03-10015','2025-2026','2025-2026',2,NULL,'T25-03-10015','Achieng','Abdi','Kihika','Female','Christian','O+','Kenyan','2000-06-24','default.png','0730776478','Kimani Shein','0735487660','Subira Karume','0749823450','','','Dodoma, Tanzania','Dodoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(21,'T23-03-10016','2023-2024','2023-2024',1,NULL,'T23-03-10016','Mutua','Kilonzo','Majaliwa','Male','Muslim','A-','Kenyan','2003-02-20','default.png','0741568532','Omondi Matiang\'i','0764193837','Halima Matiang\'i','0726090908','','','Geita, Tanzania','Geita, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(22,'T24-03-10017','2024-2025','2024-2025',2,NULL,'T24-03-10017','Wekesa','Simiyu','Kenyatta','Male','Christian','O-','Kenyan','2004-10-15','default.png','0796637649','Onyango Joho','0750264926','Chebet Wetangula','0771367643','','','Mwanza, Tanzania','Morogoro, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(23,'T24-03-10018','2024-2025','2024-2025',1,NULL,'T24-03-10018','Asha','Maina','Waiguru','Female','Other','O+','Tanzanian','2001-02-22','default.png','0782909480','Musyoka Mudavadi','0750603163','Atieno Kalonzo','0792097999','','','Iringa, Tanzania','Mwanza, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(24,'T23-03-10019','2023-2024','2023-2024',2,NULL,'T23-03-10019','Halima','Kilonzo','Nyerere','Female','Christian','O+','Kenyan','2006-07-21','default.png','0745431319','Chacha Shein','0777834855','Anyango Ruto','0775569635','','','Iringa, Tanzania','Moshi, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(25,'T23-03-10020','2023-2024','2023-2024',3,NULL,'T23-03-10020','Shani','Barasa','Moi','Female','Other','A+','Kenyan','2004-06-27','default.png','0711297845','Odhiambo Waiguru','0725015458','Subira Shein','0720099059','','','Kibaha, Tanzania','Zanzibar, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(26,'T22-03-10021','2022-2023','2022-2023',2,NULL,'T22-03-10021','Rehema','Ochieng','Karume','Female','Muslim','A+','Kenyan','2006-03-28','default.png','0723796726','Musyoka Kingi','0757469942','Chepngetich Karume','0785146293','','','Mpanda, Tanzania','Musoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(27,'T22-03-10022','2022-2023','2022-2023',1,NULL,'T22-03-10022','Nafula','Maina','Kikwete','Female','Muslim','A-','Kenyan','2002-08-27','default.png','0724508349','Kipkirui Karume','0761343880','Chebet Wetangula','0715197528','','','Tabora, Tanzania','Tabora, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(28,'T23-03-10023','2023-2024','2023-2024',1,NULL,'T23-03-10023','Wekesa','Kinuthia','Matiang\'i','Male','Muslim','AB+','Tanzanian','2002-02-01','default.png','0747463522','Chacha Mvurya','0757130035','Anyango Wetangula','0796098221','','','Tanga, Tanzania','Tanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(29,'T24-03-10024','2024-2025','2024-2025',1,NULL,'T24-03-10024','Wafula','Musyoka','Nyang\'au','Male','Christian','B+','Kenyan','2000-07-23','default.png','0725521714','Kimani Kingi','0761700055','Anyango Ngilu','0787388337','','','Bagamoyo, Tanzania','Bagamoyo, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(30,'T23-03-10025','2023-2024','2023-2024',2,NULL,'T23-03-10025','Asha','Wafula','Karume','Female','Christian','AB+','Tanzanian','2002-12-13','default.png','0726726926','Wekesa Lissu','0750308572','Anyango Magufuli','0778064830','','','Kibaha, Tanzania','Musoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(31,'T25-03-10026','2025-2026','2025-2026',3,NULL,'T25-03-10026','Wafula','Karanja','Omwamba','Male','Muslim','B-','Tanzanian','2005-08-15','default.png','0750785405','Ochieng Kalonzo','0748508907','Nekesa Moi','0738210242','','','Morogoro, Tanzania','Morogoro, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(32,'T23-03-10027','2023-2024','2023-2024',3,NULL,'T23-03-10027','Bakari','Onyango','Samia','Male','Christian','A+','Kenyan','2003-04-02','default.png','0741523529','Kiprop Karume','0751663795','Halima Joho','0740150759','','','Lindi, Tanzania','Lindi, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(33,'T22-03-10028','2022-2023','2022-2023',2,NULL,'T22-03-10028','Pendo','Simiyu','Mudavadi','Female','Christian','AB+','Kenyan','2004-12-06','default.png','0729806690','Chacha Shein','0798054615','Nekesa Kihika','0710744212','','','Shinyanga, Tanzania','Mbeya, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(34,'T22-03-10029','2022-2023','2022-2023',2,NULL,'T22-03-10029','Kipkirui','Musyoka','Joho','Male','Muslim','AB-','Kenyan','2003-08-24','default.png','0769401199','Njoroge Karume','0792228802','Zawadi Moi','0777750178','','','Iringa, Tanzania','Iringa, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(35,'T24-03-10030','2024-2025','2024-2025',3,NULL,'T24-03-10030','Chebet','Otieno','Mudavadi','Female','Muslim','O-','Kenyan','2003-04-05','default.png','0746468984','Zuberi Waiguru','0755076693','Asha Shein','0752910691','','','Bariadi, Tanzania','Morogoro, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(36,'T22-03-10031','2022-2023','2022-2023',2,NULL,'T22-03-10031','Anyango','Njeri','Samia','Female','Other','O+','Tanzanian','2002-01-16','default.png','0766390708','Kipkorir Waiguru','0762274692','Nafula Maalim','0788393811','','','Tabora, Tanzania','Tabora, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(37,'T25-03-10032','2025-2026','2025-2026',3,NULL,'T25-03-10032','Githinji','Kariuki','Mvurya','Male','Other','A+','Tanzanian','2004-10-05','default.png','0755114543','Baraka Wetangula','0799774697','Nafula Kihika','0764266464','','','Dar es Salaam, Tanzania','Dar es Salaam, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(38,'T22-03-10033','2022-2023','2022-2023',1,NULL,'T22-03-10033','Amina','Odhiambo','Mvurya','Female','Christian','B-','Tanzanian','2002-07-09','default.png','0753936531','Mutua Waiguru','0738408562','Shani Maalim','0771028710','','','Arusha, Tanzania','Arusha, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(39,'T22-03-10034','2022-2023','2022-2023',2,NULL,'T22-03-10034','Simiyu','Kariuki','Mudavadi','Male','Christian','A-','Kenyan','2000-11-16','default.png','0736757737','Mwangi Mkapa','0712735359','Rehema Mkapa','0793394260','','','Njombe, Tanzania','Njombe, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(40,'T25-03-10035','2025-2026','2025-2026',3,NULL,'T25-03-10035','Atieno','Abdi','Murkomen','Female','Muslim','O+','Tanzanian','2003-11-19','default.png','0731980274','Omondi Magufuli','0751746937','Halima Mwinyi','0724508768','','','Kigoma, Tanzania','Kigoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(41,'T23-03-10036','2023-2024','2023-2024',1,NULL,'T23-03-10036','Otieno','Kilonzo','Magufuli','Male','Christian','AB+','Tanzanian','2005-09-03','default.png','0726247735','Onyango Mkapa','0785958181','Achieng Majaliwa','0715512205','','','Moshi, Tanzania','Musoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(42,'T25-03-10037','2025-2026','2025-2026',1,NULL,'T25-03-10037','Atieno','Mwangi','Ngilu','Female','Christian','AB-','Tanzanian','2004-12-14','default.png','0733640702','Simiyu Mbowe','0780027662','Njeri Shein','0797302916','','','Iringa, Tanzania','Iringa, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(43,'T23-03-10038','2023-2024','2023-2024',1,NULL,'T23-03-10038','Njeri','Musyoka','Karume','Female','Muslim','B-','Kenyan','2002-04-16','default.png','0760867083','Baraka Mkapa','0755150390','Njeri Kibaki','0713852048','','','Mpanda, Tanzania','Mpanda, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(44,'T24-03-10039','2024-2025','2024-2025',3,NULL,'T24-03-10039','Subira','Ochieng','Mwinyi','Female','Christian','AB-','Tanzanian','2000-01-15','default.png','0764549859','Juma Nyang\'au','0775575808','Chepngetich Matiang\'i','0784514489','','','Tabora, Tanzania','Tabora, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(45,'T23-03-10040','2023-2024','2023-2024',2,NULL,'T23-03-10040','Mwendwa','Ali','Moi','Female','Christian','B+','Tanzanian','2005-02-08','default.png','0754398056','Kipkorir Nyang\'au','0757219209','Atieno Ngilu','0770900658','','','Morogoro, Tanzania','Morogoro, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(46,'T23-03-10041','2023-2024','2023-2024',1,NULL,'T23-03-10041','Odhiambo','Kipkirui','Magufuli','Male','Christian','A-','Tanzanian','2000-05-06','default.png','0747983442','Githinji Wetangula','0723492385','Pendo Mkapa','0736054020','','','Bariadi, Tanzania','Bariadi, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(47,'T24-03-10042','2024-2025','2024-2025',1,NULL,'T24-03-10042','Kipkorir','Kinuthia','Mkapa','Male','Muslim','B+','Tanzanian','2002-08-16','default.png','0775884623','Bakari Shein','0723769080','Amina Nyerere','0711646234','','','Tanga, Tanzania','Tanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(48,'T24-03-10043','2024-2025','2024-2025',2,NULL,'T24-03-10043','Cheruiyot','Mutua','Nyerere','Male','Muslim','B+','Kenyan','2004-02-08','default.png','0717195288','Mwangi Mvurya','0730364454','Mwendwa Matiang\'i','0730024960','','','Sumbawanga, Tanzania','Sumbawanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(49,'T23-03-10044','2023-2024','2023-2024',3,NULL,'T23-03-10044','Njeri','Mutua','Ngilu','Female','Christian','O-','Kenyan','2002-04-21','default.png','0750987442','Kilonzo Waiguru','0786320155','Achieng Majaliwa','0793352876','','','Geita, Tanzania','Mbeya, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(50,'T23-03-10045','2023-2024','2023-2024',3,NULL,'T23-03-10045','Omondi','Musyoka','Kihika','Male','Christian','B+','Tanzanian','2001-11-03','default.png','0749092160','Juma Ngilu','0714380847','Njeri Shein','0741068214','','','Iringa, Tanzania','Iringa, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(51,'T23-03-10046','2023-2024','2023-2024',2,NULL,'T23-03-10046','Kipkorir','Odhiambo','Samia','Male','Christian','B+','Tanzanian','2003-02-15','default.png','0729090631','Baraka Nyerere','0719584466','Amina Lissu','0718004482','','','Bariadi, Tanzania','Bariadi, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(52,'T25-03-10047','2025-2026','2025-2026',2,NULL,'T25-03-10047','Njeri','Barasa','Mwinyi','Female','Christian','O-','Kenyan','2004-10-22','default.png','0753261270','Mwangi Magufuli','0791029073','Rehema Mbowe','0743603811','','','Dar es Salaam, Tanzania','Dar es Salaam, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(53,'T24-03-10048','2024-2025','2024-2025',3,NULL,'T24-03-10048','Barasa','Mwendwa','Waiguru','Male','Muslim','AB-','Kenyan','2003-06-16','default.png','0747333543','Barasa Odinga','0734359060','Mwendwa Nyang\'au','0788561612','','','Moshi, Tanzania','Moshi, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(54,'T22-03-10049','2022-2023','2022-2023',1,NULL,'T22-03-10049','Nafula','Simiyu','Samia','Female','Christian','B-','Tanzanian','2006-02-11','default.png','0783832717','Onyango Kihika','0714925405','Achieng Karume','0771045700','','','Kigoma, Tanzania','Kigoma, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL),
(55,'T22-03-10050','2022-2023','2022-2023',3,NULL,'T22-03-10050','Nafula','Musyoka','Mkapa','Female','Christian','A-','Tanzanian','2002-03-18','default.png','0776906611','Kwame Ruto','0793940940','Zawadi Kingi','0769329732','','','Shinyanga, Tanzania','Shinyanga, Tanzania','1','2026-06-05 13:15:20','2026-06-05 13:15:20',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `credit` varchar(20) NOT NULL,
  `description` varchar(250) NOT NULL,
  `levelTerm` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `department_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_department_id_foreign` (`department_id`),
  CONSTRAINT `subject_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES
(1,'Principles of Programming Languages','CP 111','9','Principles of Programming Languages','L1T1','2026-06-04 23:02:08','2026-06-05 11:13:15',NULL,1),
(2,'Introduction to Database Systems','CP 121','9','Introduction to Database Systems','L1T2','2026-06-04 23:03:34','2026-06-05 11:20:36',NULL,1),
(3,'Introduction to Information Technology','IT 111','7.5','Introduction to Information Technology','L1T1','2026-06-04 23:03:50','2026-06-05 14:35:43',NULL,3),
(4,'Communication Skills','LG 102','7.5','Communication Skills','L1T1','2026-06-04 23:05:52','2026-06-05 14:35:25',NULL,3),
(5,'Development Perspectives','DS 102','7.5','Development Perspectives','L1T1','2026-06-04 23:08:03','2026-06-05 11:15:04',NULL,1),
(6,'Mathematical Foundations of Information Security','IA 112','7.5','Mathematical Foundations of Information Security','L1T1','2026-06-04 23:09:41','2026-06-05 11:15:51',NULL,1),
(7,'Discrete Mathematics for ICT','TN 111','7.5','Discrete Mathematics for ICT','L1T1','2026-06-04 23:11:13','2026-06-05 11:17:53',NULL,2),
(8,'Linear Algebra for ICT','TN 112','7.5','Linear Algebra for ICT','L1T1','2026-06-04 23:19:53','2026-06-05 11:17:40',NULL,1),
(9,'Calculus','TN 113','7.5','Calculus','L1T1','2026-06-04 23:20:20','2026-06-05 11:17:27',NULL,2),
(10,'Wearable Computing','CG 121','7.5','Wearable Computing','L1T2','2026-06-04 23:20:24','2026-06-05 11:19:22',NULL,1),
(11,'Introduction to Computer Networking','CN 121','7.5','Introduction to Computer Networking','L1T2','2026-06-04 23:21:15','2026-06-05 11:19:52',NULL,1),
(12,'Introduction to High Level Programming','CS123','9','Introduction to High Level Programming','L1T2','2026-06-04 23:24:45','2026-06-05 11:21:14',NULL,1),
(13,'Introduction to Software Engineering','CS 123','6','Introduction to Software Engineering','L1T2','2026-06-04 23:47:07','2026-06-05 11:21:46',NULL,1),
(14,'Introduction to IT Security','IA 124','7.5','Introduction to IT Security','L1T2','2026-06-04 23:50:11','2026-06-05 11:22:19',NULL,1),
(15,'Numerical Analysis for ICT','TN 121','7.5','Numerical Analysis for ICT','L1T2','2026-06-05 11:23:19','2026-06-05 11:23:19',NULL,2),
(16,'Probability and Statistics','TN 122','9','Probability and Statistics','L1T2','2026-06-05 11:23:46','2026-06-05 11:23:46',NULL,2),
(17,'Computer Networking Protocols','CN 211','9','Computer Networking Protocols','L2T1','2026-06-05 11:24:23','2026-06-05 11:24:23',NULL,1),
(18,'Introduction to Linux/Unix Systems','CP 211','9','Introduction to Linux/Unix Systems','L2T1','2026-06-05 11:24:50','2026-06-05 11:24:50',NULL,1),
(19,'System Analysis and Design','CP 212','7.5','System Analysis and Design','L2T1','2026-06-05 11:25:10','2026-06-05 11:25:10',NULL,1),
(20,'Data Structure and Algorithms Analysis','CP 213','10.5','Data Structure and Algorithms Analysis','L2T1','2026-06-05 11:25:46','2026-06-05 11:25:46',NULL,1),
(21,'Computational Theory','CP 214','7.5','Computational Theory','L2T1','2026-06-05 11:26:07','2026-06-05 11:26:07',NULL,1),
(22,'Object Oriented Programming in JAVA','CP 215','9','Object Oriented Programming in JAVA','L2T1','2026-06-05 11:26:29','2026-06-05 11:26:29',NULL,1),
(23,'Computer Organization and Architecture I','CT 211','9','Computer Organization and Architecture I','L2T1','2026-06-05 11:26:52','2026-06-05 11:26:52',NULL,1),
(24,'Industrial Practical Training I','CS 131','9.6','Industrial Practical Training I','L2T1','2026-06-05 11:27:19','2026-06-05 11:27:19',NULL,1),
(25,'Internet Programming and Application I','CP 221','7.5','Internet Programming and Application I','L2T2','2026-06-05 11:27:50','2026-06-05 11:27:50',NULL,1),
(26,'Open Source Technologies','CP 222','7.5','Open Source Technologies','L2T2','2026-06-05 11:28:12','2026-06-05 11:28:12',NULL,1),
(27,'Object-Oriented Systems Design','CP 223','7.5','Object-Oriented Systems Design','L2T2','2026-06-05 11:28:34','2026-06-05 11:28:34',NULL,1),
(28,'Database Management Systems','CP 224','9','Database Management Systems','L2T2','2026-06-05 11:28:54','2026-06-05 11:28:54',NULL,1),
(29,'Software Testing and Quality Assurance','CP 225','7.5','Software Testing and Quality Assurance\r\n','L2T2','2026-06-05 11:29:12','2026-06-05 11:29:12',NULL,1),
(30,'Operating Systems','CP 226','9','Operating Systems\r\n','L2T2','2026-06-05 11:29:35','2026-06-05 11:29:35',NULL,1),
(31,'ICT Research Methods','IS 221','7.5','ICT Research Methods\r\n','L2T2','2026-06-05 11:29:56','2026-06-05 11:29:56',NULL,3),
(32,'Operating Systems Security','IA 313','7.5','Operating Systems Security','L3T1','2026-06-05 14:00:26','2026-06-05 14:00:26',NULL,1),
(33,'Computer Graphics','CP 318','9.0','Computer Graphics','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(34,'Mathematical Logic and Formal Semantics','MT 3111','7.5','Mathematical Logic and Formal Semantics','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(35,'Internet Programming and Applications II','CP 311','9.0','Internet Programming and Applications II','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(36,'Python Programming','CP 312','9.0','Python Programming','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(37,'Mobile Applications Development','CP 313','9.0','Mobile Applications Development','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(38,'ICT Entrepreneurship','EME 314','7.5','ICT Entrepreneurship','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(39,'Embedded Systems I','CT 411','9.0','Embedded Systems I','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,2),
(40,'Selected Topics in Software Engineering','CP 316','9.0','Selected Topics in Software Engineering','L3T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(41,'Advanced Java Programming','CS 321','9.0','Advanced Java Programming','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(42,'Distributed Database Systems','CP 321','9.0','Distributed Database Systems','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(43,'Information and Communication Systems Security','IA 321','9.0','Information and Communication Systems Security','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(44,'Data Mining and Warehousing','CP 322','9.0','Data Mining and Warehousing','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(45,'Web Framework Development Using Javascript','CP 323','9.0','Web Framework Development Using Javascript','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(46,'Industrial Practical Training III','CS 331','9.6','Industrial Practical Training III','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(47,'Secure System Development','IA 326','7.5','Secure System Development','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(48,'Compiler Technology','CP 324','7.5','Compiler Technology','L3T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(49,'Professional Ethics and Conduct','SI 311','7.5','Professional Ethics and Conduct','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(50,'Software Engineering Project I','CS 431','6.0','Software Engineering Project I','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(51,'Computer Maintenance','CT 312','9.0','Computer Maintenance','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,2),
(52,'Human-Computer Interaction','IM 411','7.5','Human-Computer Interaction','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(53,'C# Programming','CP 412','9.0','C# Programming','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(54,'Multimedia Content Development','CD 312','7.5','Multimedia Content Development','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(55,'ICT Project Management','BT 413','6.0','ICT Project Management','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(56,'Electronic and Mobile Commerce','BT 312','7.5','Electronic and Mobile Commerce','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(57,'Reverse Engineering','CS 411','7.5','Reverse Engineering','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(58,'Foundations of Data Science','CG 222','7.5','Foundations of Data Science','L4T1','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(59,'Digital Image Processing','CP 421','7.5','Digital Image Processing','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(60,'Software Deployment and Management','CS 421','7.5','Software Deployment and Management','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(61,'Software Engineering Project II','CS 432','9.0','Software Engineering Project II','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(62,'Artificial Intelligence','CP 422','9.0','Artificial Intelligence','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(63,'System Administration and Management','CP 423','9.0','System Administration and Management','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(64,'Cloud Computing','CP 424','9.0','Cloud Computing','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1),
(65,'Embedded Systems II','CT 421','9.0','Embedded Systems II','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,2),
(66,'Digital Creative Advertising and Production','CD 322','9.0','Digital Creative Advertising and Production','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,3),
(67,'Big Data Analysis','CS 329','9.0','Big Data Analysis','L4T2','2026-06-05 14:06:13','2026-06-05 14:06:13',NULL,1);
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `login` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `group` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(32,'Admin','User','System Admin','admin','admin@test.com','Admin','$2y$10$JlCBhXAePbObT.iezPW.nuClJzHYFd8MX5YI7ITJFS/iNoEs.v032','gHtxDqqY4zMAMY56Ti0x4h5ozVQOqcUg4nkNMee0X8jpBtK5ibJ8j99A7SPn','2026-06-04 23:50:11','2026-06-05 23:33:12',NULL),
(33,'John','Doe',NULL,'johndoe','john@example.com','user','$2y$10$6K9gBNJzztD1wrI8R1Q1huSYqcBWZmifYyT35LOar.AEeQRgd1dWa','wgW4h4UlqXkGgdJjfovFZeOV50YZVSuHZsIYmLrWFlUTNCY8mwds4RPTz1pq','2026-06-04 23:50:11','2026-06-05 11:34:24',NULL),
(34,'Accountant','User','System Accountant','accountant','accountant@test.com','Account','$2y$10$z81MaeYsgw1.rAUW3ZkVQOCJTK4mH38sXb4KkZpAnMVuBtvMHM0AS','R1tvB1pOrGWIRgg23lFufebJPyX5Muwy5emPJ1mUsu3Ip5dxxfCNUpSUqKMf','2026-06-05 11:08:29','2026-06-05 16:41:52',NULL),
(35,'Teacher','User','System Teacher','teacher','teacher@test.com','Teacher','$2y$10$/8cV5Ph4AFg0SlS1BkqhDuS3EQUhebfmNQ73yznP2/C2ddTrBb1dS','n9hQqVKAaTydZ4JZfABriJqbOE8HyFgmqdOZgMUYhOQnXZyQ4kEffmvebX97','2026-06-05 11:09:30','2026-06-11 16:19:06',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-11 16:53:12
