-- MySQL dump 10.13  Distrib 5.7.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: fmt
-- ------------------------------------------------------
-- Server version	5.7.37

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
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `address1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (1,'US','AL','sadf','12312','asdfs',NULL,'2022-01-26 19:48:04','2022-01-26 19:48:04'),(2,'US','AL','sdfg','123123','dhgd',NULL,'2022-02-24 21:33:43','2022-02-24 21:33:43'),(3,'US','AL','asdfas','123123','sdfasdf',NULL,'2022-02-24 22:08:27','2022-02-24 22:08:27'),(4,'US','AL','sdf','12345','sdf',NULL,'2022-03-30 21:12:04','2022-03-30 21:12:04');
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookstore_transfer`
--

DROP TABLE IF EXISTS `bookstore_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookstore_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `net` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  PRIMARY KEY (`id`),
  KEY `IDX_B4FAA418727ACA70` (`parent_id`),
  CONSTRAINT `FK_B4FAA418727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `bookstore_transfer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookstore_transfer`
--

LOCK TABLES `bookstore_transfer` WRITE;
/*!40000 ALTER TABLE `bookstore_transfer` DISABLE KEYS */;
INSERT INTO `bookstore_transfer` VALUES (1,NULL,0,'processed','2022-03-14 13:25:37','2022-03-14 13:25:37'),(2,NULL,0,'processed','2022-03-16 12:06:31','2022-03-16 12:06:31'),(3,NULL,0,'processed','2022-03-16 12:30:31','2022-03-16 12:30:31'),(10,NULL,1195,'pending','2022-03-16 12:48:52','2022-03-16 12:48:52'),(11,NULL,0,'processed','2022-03-16 12:50:24','2022-03-16 12:50:24'),(12,NULL,16590,'pending','2022-03-16 13:00:28','2022-03-16 13:00:28'),(13,NULL,1600,'pending','2022-03-25 06:55:29','2022-03-25 06:55:29');
/*!40000 ALTER TABLE `bookstore_transfer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `estimated_shipping` int(11) DEFAULT NULL,
  `estimated_tax` int(11) DEFAULT NULL,
  `estimated_cost` int(11) DEFAULT NULL,
  `funded_total` int(11) NOT NULL,
  `purchased_total` int(11) NOT NULL DEFAULT '0',
  `donations_from_previous` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `shipping_option` int(11) NOT NULL DEFAULT '0',
  `is_paused` smallint(6) NOT NULL DEFAULT '0',
  `paused_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `mass_mailing_called` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IX_campaign_status` (`status`),
  KEY `FK_campaign_user` (`user_id`),
  CONSTRAINT `FK_1F1512DDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign`
--

LOCK TABLES `campaign` WRITE;
/*!40000 ALTER TABLE `campaign` DISABLE KEYS */;
INSERT INTO `campaign` VALUES (17,2,'2022-03-08 00:00:00','2022-04-13 00:00:00',0,NULL,48330,400,47330,0,0,'2022-04-08 10:45:35','2022-04-13 06:41:16',203,0,NULL,0);
/*!40000 ALTER TABLE `campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_book`
--

DROP TABLE IF EXISTS `campaign_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `product_family_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isbn` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  `state` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `sku` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_book_status` (`status`),
  KEY `IX_book_campaign` (`campaign_id`),
  CONSTRAINT `FK_CA0298D3F639F774` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_book`
--

LOCK TABLES `campaign_book` WRITE;
/*!40000 ALTER TABLE `campaign_book` DISABLE KEYS */;
INSERT INTO `campaign_book` VALUES (37,17,'11127475','COLLEGE ACCOUNTING LL W/ WILEYPLUS','WEYGANDT','ACCOUNTING 110','9781119502067',18000,1,2,1,'2022-04-08 10:45:36','2022-04-12 11:17:36','11127475'),(38,17,'11098874','COLLEGE ACCOUNTING (CH.1-15) LL 23RD W/ ACCESS CODE','HEINTZ','ACCOUNTING 110','9780357252260',10530,1,2,1,'2022-04-08 10:45:36','2022-04-12 11:25:22','11098874'),(39,17,'11138136','PAYROLL ACCOUNTING 2022','BIEG','ACCOUNTING 130','9780357518755',18800,1,2,1,'2022-04-08 10:45:36','2022-04-12 11:25:22','11138136');
/*!40000 ALTER TABLE `campaign_book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_contact`
--

DROP TABLE IF EXISTS `campaign_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `status` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_campaign` (`campaign_id`),
  KEY `FK_campaign_contact` (`contact_id`),
  CONSTRAINT `FK_E4D87A14E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `user_contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E4D87A14F639F774` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_contact`
--

LOCK TABLES `campaign_contact` WRITE;
/*!40000 ALTER TABLE `campaign_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dwolla_event`
--

DROP TABLE IF EXISTS `dwolla_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dwolla_event` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `received` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `topic` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dwolla_event`
--

LOCK TABLES `dwolla_event` WRITE;
/*!40000 ALTER TABLE `dwolla_event` DISABLE KEYS */;
INSERT INTO `dwolla_event` VALUES ('d0cac5ae-b12b-4d9e-8adc-378da32a55ef','2022-03-25 06:55:21','2022-03-25 07:11:04','transfer_created','53c91c8b-08ac-ec11-813f-d8831e3bd798');
/*!40000 ALTER TABLE `dwolla_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ext_log_entries`
--

DROP TABLE IF EXISTS `ext_log_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ext_log_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `object_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_class_lookup_idx` (`object_class`),
  KEY `log_date_lookup_idx` (`logged_at`),
  KEY `log_user_lookup_idx` (`username`),
  KEY `log_version_lookup_idx` (`object_id`,`object_class`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ext_log_entries`
--

LOCK TABLES `ext_log_entries` WRITE;
/*!40000 ALTER TABLE `ext_log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `ext_log_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_order_item`
--

DROP TABLE IF EXISTS `log_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_item_id` int(11) DEFAULT NULL,
  `action` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
  `object_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_58FCB3B2E415FB15` (`order_item_id`),
  CONSTRAINT `FK_58FCB3B2E415FB15` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_order_item`
--

LOCK TABLES `log_order_item` WRITE;
/*!40000 ALTER TABLE `log_order_item` DISABLE KEYS */;
INSERT INTO `log_order_item` VALUES (83,89,'create','2022-04-12 11:17:05','89','FMT\\DataBundle\\Entity\\OrderItem',1,'a:1:{s:6:\"status\";s:4:\"cart\";}','shini1@mail.com','cart'),(84,89,'update','2022-04-12 11:17:36','89','FMT\\DataBundle\\Entity\\OrderItem',2,'a:1:{s:6:\"status\";s:9:\"submitted\";}','shini1@mail.com','submitted'),(85,90,'create','2022-04-12 11:24:47','90','FMT\\DataBundle\\Entity\\OrderItem',1,'a:1:{s:6:\"status\";s:4:\"cart\";}','shini1@mail.com','cart'),(86,91,'create','2022-04-12 11:24:54','91','FMT\\DataBundle\\Entity\\OrderItem',1,'a:1:{s:6:\"status\";s:4:\"cart\";}','shini1@mail.com','cart'),(87,90,'update','2022-04-12 11:25:22','90','FMT\\DataBundle\\Entity\\OrderItem',2,'a:1:{s:6:\"status\";s:9:\"submitted\";}','shini1@mail.com','submitted'),(88,91,'update','2022-04-12 11:25:22','91','FMT\\DataBundle\\Entity\\OrderItem',2,'a:1:{s:6:\"status\";s:9:\"submitted\";}','shini1@mail.com','submitted');
/*!40000 ALTER TABLE `log_order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` VALUES ('20180316094146'),('20180319071305'),('20180329150746'),('20180410111440'),('20180412091704'),('20180510163524'),('20180511080242'),('20180514153438'),('20180529094624'),('20200116110352'),('20200128090531'),('20200203133243'),('20200207064055'),('20200403135605'),('20200405124142'),('20200429114459'),('20200504131455'),('20200506150437'),('20200507150731'),('20200512102447'),('20200518092047'),('20200603080704'),('20200604074336'),('20200612092016'),('20200626152602'),('20200710102432'),('20200716091417'),('20211119121358'),('20211215111238'),('20220120161014'),('20220312111238'),('20220408131142'),('20220519140851'),('20220526123729');
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `anonymous_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `external_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) NOT NULL,
  `shipping` int(11) NOT NULL,
  `tax` int(11) NOT NULL,
  `transaction_fee` int(11) NOT NULL,
  `fmt_fee` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `submitted` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `unprocessed_amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_order_user` (`user_id`),
  KEY `FK_order_address` (`address_id`),
  KEY `FK_order_campaign` (`campaign_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_F5299398F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`),
  CONSTRAINT `FK_F5299398F639F774` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (85,2,NULL,17,NULL,'4548909709464075943',18000,2000,0,658,1000,21658,'completed',NULL,'2022-04-12 11:17:05','2022-04-12 11:17:33',20000),(86,2,NULL,17,NULL,'8029549813895850979',29330,2000,0,1013,1567,33910,'completed',NULL,'2022-04-12 11:24:47','2022-04-12 11:25:21',31330);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `unprocessed_amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_item_order` (`order_id`),
  KEY `FK_item_book` (`book_id`),
  CONSTRAINT `FK_52EA1F0916A2B381` FOREIGN KEY (`book_id`) REFERENCES `campaign_book` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
INSERT INTO `order_item` VALUES (89,85,37,'11127475','COLLEGE ACCOUNTING LL W/ WILEYPLUS',18000,1,'submitted','2022-04-12 11:17:05','2022-04-12 11:17:36',0),(90,86,38,'11098874','COLLEGE ACCOUNTING (CH.1-15) LL 23RD W/ ACCESS CODE',10530,1,'submitted','2022-04-12 11:24:47','2022-04-12 11:25:22',0),(91,86,39,'11138136','PAYROLL ACCOUNTING 2022',18800,1,'submitted','2022-04-12 11:24:54','2022-04-12 11:25:22',0);
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) DEFAULT NULL,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `nebook_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statistic_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_unique_login` (`login`),
  UNIQUE KEY `UNIQ_8D93D64992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_8D93D649A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_8D93D649C05FB297` (`confirmation_token`),
  UNIQUE KEY `UNIQ_8D93D649CCFA12B8` (`profile_id`),
  UNIQUE KEY `UNIQ_8D93D64953B6268F` (`statistic_id`),
  KEY `FK_user_profile` (`profile_id`),
  CONSTRAINT `FK_8D93D64953B6268F` FOREIGN KEY (`statistic_id`) REFERENCES `user_statistic` (`id`),
  CONSTRAINT `FK_8D93D649CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `user_profile` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'shini@mail.com','$2y$13$MtdTOKJkRbnD.oJGUax3Qurqpg.y/6JBK8hYqnS444B6ppFe7Oqoa','2022-01-26 19:05:08','2022-03-30 21:02:54','shini@mail.com','shini@mail.com','shini@mail.com','shini@mail.com',1,NULL,'2022-03-30 21:02:54',NULL,NULL,'a:1:{i:0;s:10:\"ROLE_DONOR\";}',NULL,1),(2,2,'shini1@mail.com','$2y$13$H0u/Dlfyz6vwKij9ko3vJOxf8bA6iovNurteCaw3k6wcdTc5qyhPG','2022-01-26 19:25:39','2022-05-27 10:52:19','shini1@mail.com','shini1@mail.com','shini1@mail.com','shini1@mail.com',1,NULL,'2022-05-27 10:52:19',NULL,NULL,'a:1:{i:0;s:12:\"ROLE_STUDENT\";}','8e9a4bdc19e64e689330a6f0156370bf',2),(3,3,'donor@mail.com','$2y$13$eiOyYWr40or9YaoyzJTVbOZg4BGvPl1aFogfv49u6ZyKHqxAQn6pi','2022-02-10 08:21:10','2022-02-10 08:36:02','donor@mail.com','donor@mail.com','donor@mail.com','donor@mail.com',1,NULL,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_DONOR\";}',NULL,3),(4,4,'shini2@mail.com','$2y$13$4vTZ1qQQ3MY/9hGUOs2ace.anhuiBm.gxhD6zyWqg9jUE9Zl20bHO','2022-02-21 12:45:25','2022-03-25 10:32:13','shini2@mail.com','shini2@mail.com','shini2@mail.com','shini2@mail.com',1,NULL,'2022-03-25 10:32:13',NULL,NULL,'a:1:{i:0;s:10:\"ROLE_DONOR\";}',NULL,4),(5,5,'shini3@mail.com','$2y$13$cEJ.s9yAOR6hGJohHvSeJ.sykpMwH/7qdtemz1BYMnmVNhl6.WMX2','2022-02-24 21:31:57','2022-03-30 21:04:34','shini3@mail.com','shini3@mail.com','shini3@mail.com','shini3@mail.com',1,NULL,'2022-03-30 21:04:34',NULL,NULL,'a:1:{i:0;s:12:\"ROLE_STUDENT\";}','628bb6a0f08441bdb72fa0e394ded74b',5),(6,6,'shini4@mail.com','$2y$13$GhzXOn7GoPWEsSMzp/eCs.1nWsKwVSQV1Ggyb2Nrow55kx9/Hz78O','2022-02-24 22:07:18','2022-03-30 21:04:57','shini4@mail.com','shini4@mail.com','shini4@mail.com','shini4@mail.com',1,NULL,'2022-03-30 21:04:57',NULL,NULL,'a:1:{i:0;s:12:\"ROLE_STUDENT\";}','67782026720348d7b9b7f41cd55eaaf8',6),(7,7,'shiniii@mail.com','$2y$13$lYb2QWMf267pcIv1UrvcveFba/4U8TRTLkB2DeAum3DdkjyUFJq.u','2022-02-28 16:48:52','2022-02-28 17:45:49','shiniii@mail.com','shiniii@mail.com','shiniii@mail.com','shiniii@mail.com',1,NULL,'2022-02-28 17:45:49',NULL,NULL,'a:1:{i:0;s:10:\"ROLE_DONOR\";}',NULL,7),(8,8,'sdfg@sdfg.com','NjIzNDVkYjg4ZGMxZTAuNDYzNTcyOTE=','2022-03-18 10:23:52','2022-03-18 10:23:52','sdfg@sdfg.com','sdfg@sdfg.com','sdfg@sdfg.com','sdfg@sdfg.com',0,NULL,NULL,'qif_FgQNWHM4OL-g4Q0t9twjDjMOXMPQf8MuUjPKUdE',NULL,'a:1:{i:0;s:21:\"ROLE_INCOMPLETE_DONOR\";}',NULL,8),(9,9,'132123@mail.com','NjIzZGExNDhlZGY1NzMuMzgxNTc1MzY=','2022-03-25 11:02:32','2022-03-25 11:02:32','132123@mail.com','132123@mail.com','132123@mail.com','132123@mail.com',0,NULL,NULL,'S8nps68rMFiGJpYOyUvkbulsC79abIgA3ofcpOgv_Ts',NULL,'a:1:{i:0;s:21:\"ROLE_INCOMPLETE_DONOR\";}',NULL,9),(10,10,'shini-1@mail.com','$2y$13$Uio5mF./cSfSBw9wpF1GrO6n4u/.HzQO8XG59qGX1XnV3zIWVsIku','2022-03-30 21:08:04','2022-03-30 21:09:28','shini-1@mail.com','shini-1@mail.com','shini-1@mail.com','shini-1@mail.com',1,NULL,NULL,NULL,NULL,'a:1:{i:0;s:21:\"ROLE_INCOMPLETE_DONOR\";}',NULL,10),(11,11,'shini-2@mail.com','$2y$13$9f80mjDU6tU18htf/U6yRuE5u5acaCGLnATv750xJE0eJ3M8mCdSK','2022-03-30 21:10:56','2022-03-30 21:14:36','shini-2@mail.com','shini-2@mail.com','shini-2@mail.com','shini-2@mail.com',1,NULL,'2022-03-30 21:14:36',NULL,NULL,'a:1:{i:0;s:12:\"ROLE_STUDENT\";}','aa6845d557a3452c96d75c2850e429e5',11),(12,12,'test1@mail.com','NjI0NGM3Yzg2NmM4OTUuMjU5ODAxNzE=','2022-03-30 21:12:40','2022-03-30 21:12:40','test1@mail.com','test1@mail.com','test1@mail.com','test1@mail.com',0,NULL,NULL,'eph2Kc-60qiP4T6MeqKqBsbTeSY126i8qB4d4NjZVpI',NULL,'a:1:{i:0;s:21:\"ROLE_INCOMPLETE_DONOR\";}',NULL,12),(13,13,'test2@mail.com','NjI0NGM4MTFjN2FhYTAuMDAyNTc3NTE=','2022-03-30 21:13:53','2022-03-30 21:13:53','test2@mail.com','test2@mail.com','test2@mail.com','test2@mail.com',0,NULL,NULL,'OX985fJCAUGevDz0ZXrAXR9b2n0Ph3KmGMAb4TZCCj8',NULL,'a:1:{i:0;s:21:\"ROLE_INCOMPLETE_DONOR\";}',NULL,13);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_avatar`
--

DROP TABLE IF EXISTS `user_avatar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_avatar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `status` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_avatar`
--

LOCK TABLES `user_avatar` WRITE;
/*!40000 ALTER TABLE `user_avatar` DISABLE KEYS */;
INSERT INTO `user_avatar` VALUES (1,'d036bbbb5844fcdaa7b1f1220316d89c.png',NULL,1,'2022-01-26 19:48:04','2022-04-06 11:57:09'),(2,'67a75bcc14fc8004add40350cd2a5162.jpg',NULL,1,'2022-02-24 21:33:43','2022-02-24 21:33:43'),(3,'a7266fa1969ab6e8c57246a990c963be.jpg',NULL,1,'2022-02-24 22:08:27','2022-02-24 22:08:27'),(4,'b24eb43818f84ac7e00b7fcb2659a3a2.jpg',NULL,1,'2022-03-30 21:12:04','2022-03-30 21:12:04');
/*!40000 ALTER TABLE `user_avatar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_contacts`
--

DROP TABLE IF EXISTS `user_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_contact_was_deleted` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_contact_user` (`student_id`),
  KEY `FK_contact_donor` (`donor_id`),
  CONSTRAINT `FK_D3CDF1733DD7B7A7` FOREIGN KEY (`donor_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_D3CDF173CB944F1A` FOREIGN KEY (`student_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contacts`
--

LOCK TABLES `user_contacts` WRITE;
/*!40000 ALTER TABLE `user_contacts` DISABLE KEYS */;
INSERT INTO `user_contacts` VALUES (1,2,7,'sdfsd','sdfsdf','2022-02-28 16:48:55','2022-02-28 16:48:55',0),(2,2,8,'asdfg','sdfg','2022-03-18 10:23:55','2022-03-18 10:23:55',0),(4,2,4,'shini2','shini2','2022-03-25 10:49:17','2022-03-25 10:50:25',0),(5,11,12,'123','123','2022-03-30 21:12:40','2022-03-30 21:12:40',0),(6,11,13,'123','123','2022-03-30 21:13:53','2022-03-30 21:13:53',0);
/*!40000 ALTER TABLE `user_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_major`
--

DROP TABLE IF EXISTS `user_major`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_major` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `campus_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_major`
--

LOCK TABLES `user_major` WRITE;
/*!40000 ALTER TABLE `user_major` DISABLE KEYS */;
INSERT INTO `user_major` VALUES (1,'ACCOUNTING',1,'2022-01-26 22:36:35','2022-05-17 18:52:26',41,3444),(2,'BIOMEDICAL SCIENCES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3445),(3,'CNA',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3446),(4,'HNRS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3447),(5,'NURS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3448),(6,'VN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3449),(7,'WEXP',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3450),(8,'BLACK STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3451),(9,'SS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3452),(10,'HSBI',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3453),(11,'HSCA',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3454),(12,'HSEA',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3455),(13,'BOTANY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3456),(14,'HSEC',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3457),(15,'HSEN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3458),(16,'HSHE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3459),(17,'HSHI',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3460),(18,'HSMA',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3461),(19,'HSPD',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3462),(20,'HSPS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3463),(21,'HSVA',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3464),(22,'HSWH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3465),(23,'MISCELLANEOUS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3466),(24,'BUSINESS LAW',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3467),(25,'HSII',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3468),(26,'TRANSLATION & INTERPRETATION SERVICE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3469),(27,'NON-CREDIT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3470),(28,'BUSINESS ADMINISTRATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3471),(29,'CHEMISTRY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3472),(30,'CHINESE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3473),(31,'CHICANO STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3474),(32,'CANCER INFORMATION MANAGEMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3475),(33,'COMPUTER INFORMATION SYSTEMS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3476),(34,'ALCOHOL & DRUG COUNSELING',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3477),(35,'COMPUTER NETWORK ENGINEERING & ELECTRONICS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3478),(36,'COMPUTER APPLICATIONS & OFFICE MANAGEMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3479),(37,'COMMUNICATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3480),(38,'COMPUTER SCIENCE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3481),(39,'COSMETOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3482),(40,'DRAFTING/CAD',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3483),(41,'DISABLED STUDENT PROGRAMS & SERVICES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3484),(42,'EARTH & PLANETARY SCIENCES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3485),(43,'EARLY CHILDHOOD EDUCATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3486),(44,'ECONOMICS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3487),(45,'ADMINISTRATION OF JUSTICE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3488),(46,'EDUCATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3489),(47,'ENVIRONMENTAL HORTICULTURE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3490),(48,'ENGLISH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3491),(49,'ENGINEERING',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3492),(50,'ENVIRONMENTAL STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3493),(51,'ENGLISH AS A SECOND LANGUAGE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3494),(52,'ETHNIC STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3495),(53,'FILM STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3496),(54,'FINANCE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3497),(55,'FRENCH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3498),(56,'ANTHROPOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3499),(57,'GRAPHIC DESIGN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3500),(58,'GEOGRAPHY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3501),(59,'GERMAN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3502),(60,'HEALTH EDUCATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3503),(61,'HISTORY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3504),(62,'HEALTH INFORMATION TECHNOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3505),(63,'INTERIOR DESIGN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3506),(64,'ART',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3507),(65,'ITALIAN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3508),(66,'JOURNALISM',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3509),(67,'JAPANESE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3510),(68,'MULTIMEDIA ARTS & TECHNOLOGIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3511),(69,'MATHEMATICS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3512),(70,'MARINE DIVING TECHNOLOGIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3513),(71,'MANAGEMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3514),(72,'MARKETING',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3515),(73,'MUSIC',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3516),(74,'NATIVE AMERICAN STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3517),(75,'ASIAN-AMERICAN STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3518),(76,'PHYSICAL EDUCATION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3519),(77,'PERSONAL DEVELOPMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3520),(78,'PHILOSOPHY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3521),(79,'PHYSICS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3522),(80,'PHYSICAL SCIENCE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3523),(81,'POLITICAL SCIENCE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3524),(82,'PSYCHOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3525),(83,'AMERICAN SIGN LANGUAGE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3526),(84,'REAL ESTATE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3527),(85,'RADIOGRAPHY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3528),(86,'SOCIOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3529),(87,'SPANISH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3530),(88,'THEATRE ARTS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3531),(89,'WATER SCIENCE',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3532),(90,'ZOOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3533),(91,'HEBREW',0,'2022-01-26 23:36:42','2022-05-17 18:52:21',41,3534),(92,'AUTOMOTIVE SERVICE/TECHNOLOGY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3535),(93,'ARABIC',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3536),(94,'PHOTOGRAPHY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3537),(95,'FILM & TELEVISION PRODUCTION',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3538),(96,'GLOBAL & INTERNATIONAL STUDIES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3539),(97,'CONSTRUCTION TRADES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3540),(98,'CULINARY ARTS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3541),(99,'HOTEL MANAGEMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3542),(100,'DIAGNOSTIC MEDICAL SONOGRAPHY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3543),(101,'ALLIED HEALTH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3544),(102,'BIOLOGICAL SCIENCES',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3545),(103,'LIBRARY',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3546),(104,'PROFESSIONAL DEVELOPEMENT',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3547),(105,'KOREAN',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3548),(106,'EMERGENCY MEDICAL TECH',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3549),(107,'INTERNATIONAL BUSINESS',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3550),(108,'ENTREPRENEURSHIP',1,'2022-01-26 23:36:42','2022-05-17 18:52:26',41,3551),(109,'SCIENCE TECHNOLOGY ENGINEERING MATH',1,'2022-03-17 19:36:24','2022-05-17 18:52:26',41,3552),(110,'MANAGEMENT INFO SYSTEMS',1,'2022-03-17 19:36:24','2022-05-17 18:52:26',41,3553),(111,'IS',1,'2022-03-17 19:36:24','2022-05-17 18:52:26',41,3554),(112,'RECREATION EDUCATION',1,'2022-03-17 19:36:24','2022-05-17 18:52:26',41,3555),(113,'LATIN',1,'2022-03-17 19:36:24','2022-05-17 18:52:26',41,3556),(114,'HEBREW',1,'2022-05-17 18:52:26','2022-05-17 18:52:26',41,3557);
/*!40000 ALTER TABLE `user_major` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grad_year` int(11) DEFAULT NULL,
  `student_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8_unicode_ci,
  `visible` smallint(6) NOT NULL,
  `facebook` smallint(6) NOT NULL,
  `twitter` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_profile_school` (`school_id`),
  KEY `FK_profile_major` (`major_id`),
  KEY `FK_profile_address` (`address_id`),
  KEY `FK_profile_avatar` (`avatar_id`),
  CONSTRAINT `FK_D95AB40586383B10` FOREIGN KEY (`avatar_id`) REFERENCES `user_avatar` (`id`),
  CONSTRAINT `FK_D95AB405C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `user_school` (`id`),
  CONSTRAINT `FK_D95AB405E93695C7` FOREIGN KEY (`major_id`) REFERENCES `user_major` (`id`),
  CONSTRAINT `FK_D95AB405F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profile`
--

LOCK TABLES `user_profile` WRITE;
/*!40000 ALTER TABLE `user_profile` DISABLE KEYS */;
INSERT INTO `user_profile` VALUES (1,NULL,NULL,NULL,NULL,'shini@mail.com','shini','shini',NULL,NULL,NULL,2,0,0,'2022-01-26 19:05:08','2022-01-26 19:05:08'),(2,1,1,1,1,'shini1@mail.com','shini1','shini11',2020,'1','sd fgsdfg sdfgsd fsd g',0,0,0,'2022-01-26 19:25:39','2022-05-17 18:53:04'),(3,NULL,NULL,NULL,NULL,'donor@mail.com','asdfasd','asdfasd',NULL,NULL,NULL,2,0,0,'2022-02-10 08:21:10','2022-02-10 08:21:10'),(4,NULL,NULL,NULL,NULL,'shini2@mail.com','shini2','shini2',NULL,NULL,NULL,2,0,0,'2022-02-21 12:45:25','2022-02-21 12:45:25'),(5,1,1,2,2,'shini3@mail.com','123','1132',2000,'1234',NULL,0,0,0,'2022-02-24 21:31:57','2022-02-24 21:33:43'),(6,1,1,3,3,'shini4@mail.com','123','123',2000,'12345',NULL,0,0,0,'2022-02-24 22:07:18','2022-02-24 22:08:27'),(7,NULL,NULL,NULL,NULL,'shiniii@mail.com','sdfsd','sdfsdf',NULL,NULL,NULL,2,0,0,'2022-02-28 16:48:52','2022-02-28 16:48:52'),(8,NULL,NULL,NULL,NULL,'sdfg@sdfg.com','asdfg','sdfg',NULL,NULL,NULL,2,0,0,'2022-03-18 10:23:52','2022-03-18 10:23:52'),(9,NULL,NULL,NULL,NULL,'132123@mail.com','123123','12312312',NULL,NULL,NULL,2,0,0,'2022-03-25 11:02:32','2022-03-25 11:02:32'),(10,NULL,NULL,NULL,NULL,'shini-1@mail.com','123','12312',NULL,NULL,NULL,0,0,0,'2022-03-30 21:08:04','2022-03-30 21:08:04'),(11,1,1,4,4,'shini-2@mail.com','123','123',2018,'1245',NULL,0,0,0,'2022-03-30 21:10:56','2022-03-30 21:12:04'),(12,NULL,NULL,NULL,NULL,'test1@mail.com','123','123',NULL,NULL,NULL,2,0,0,'2022-03-30 21:12:40','2022-03-30 21:12:40'),(13,NULL,NULL,NULL,NULL,'test2@mail.com','123','123',NULL,NULL,NULL,2,0,0,'2022-03-30 21:13:53','2022-03-30 21:13:53');
/*!40000 ALTER TABLE `user_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_school`
--

DROP TABLE IF EXISTS `user_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_school` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_school`
--

LOCK TABLES `user_school` WRITE;
/*!40000 ALTER TABLE `user_school` DISABLE KEYS */;
INSERT INTO `user_school` VALUES (1,'Santa Barbara City College (SBCC)',1,'2022-01-26 18:05:16','2022-01-26 18:05:16'),(2,'My School Isn`t Shown',2,'2022-01-26 18:05:20','2022-01-26 18:05:20');
/*!40000 ALTER TABLE `user_school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_statistic`
--

DROP TABLE IF EXISTS `user_statistic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `students_founded` int(11) NOT NULL DEFAULT '0',
  `books_purchased_for` int(11) NOT NULL DEFAULT '0',
  `amount_founded` int(11) NOT NULL DEFAULT '0',
  `donated_to_me` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_statistic`
--

LOCK TABLES `user_statistic` WRITE;
/*!40000 ALTER TABLE `user_statistic` DISABLE KEYS */;
INSERT INTO `user_statistic` VALUES (1,1,0,0,0),(2,1,0,55968,47730),(3,1,0,0,0),(4,1,0,0,0),(5,0,0,0,0),(6,0,0,0,0),(7,1,0,0,0),(8,0,0,0,0),(9,0,0,0,0),(10,0,0,0,0),(11,0,0,0,0),(12,0,0,0,0),(13,0,0,0,0);
/*!40000 ALTER TABLE `user_statistic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_transaction`
--

DROP TABLE IF EXISTS `user_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `external_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) NOT NULL,
  `amount` int(11) NOT NULL,
  `fee` int(11) NOT NULL,
  `payment_system_fee` int(11) DEFAULT NULL,
  `net` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `anonymous` smallint(6) NOT NULL,
  `thanks` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unregistered_sender` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `stripe_charge_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_transaction_type` (`type`),
  KEY `FK_transaction_order` (`order_id`),
  KEY `FK_transaction_campaign` (`campaign_id`),
  KEY `FK_transaction_sender` (`sender_id`),
  KEY `FK_transaction_recipient` (`recipient_id`),
  CONSTRAINT `FK_DB2CCC448D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  CONSTRAINT `FK_DB2CCC44E92F8F78` FOREIGN KEY (`recipient_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_DB2CCC44F624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_DB2CCC44F639F774` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_transaction`
--

LOCK TABLES `user_transaction` WRITE;
/*!40000 ALTER TABLE `user_transaction` DISABLE KEYS */;
INSERT INTO `user_transaction` VALUES (107,2,17,85,2,'txn_3Knhj1ItFolWlQLk07jlPEtm',3,0,1083,710,21658,NULL,'2022-04-12 11:17:33',1,NULL,NULL,NULL),(108,2,17,86,2,'txn_3KnhqYItFolWlQLk0KDUe0PP',3,0,1696,1094,33910,NULL,'2022-04-12 11:25:21',1,NULL,NULL,NULL),(109,2,17,NULL,2,'txn_3KnzlwItFolWlQLk1b2HapKi',1,0,5,34,100,NULL,'2022-04-13 06:33:46',1,NULL,NULL,NULL),(110,2,17,NULL,2,'txn_3KnznzItFolWlQLk1fSq9fTv',1,0,5,34,100,NULL,'2022-04-13 06:35:53',1,NULL,NULL,NULL),(111,2,17,NULL,2,'txn_3KnzouItFolWlQLk0AIBefVA',1,0,5,34,100,NULL,'2022-04-13 06:36:49',1,NULL,NULL,NULL),(112,2,17,NULL,2,'txn_3KnztBItFolWlQLk2xuaEb8w',1,0,5,34,100,NULL,'2022-04-13 06:41:15',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user_transaction` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-31 13:53:05
