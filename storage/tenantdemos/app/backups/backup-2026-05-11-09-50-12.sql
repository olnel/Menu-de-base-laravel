-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: transmada
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acces`
--

DROP TABLE IF EXISTS `acces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_groupe_users` int NOT NULL DEFAULT '0',
  `id_module_enfant_access` int DEFAULT '0',
  `etat` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_module_enfant_access` (`id_module_enfant_access`),
  KEY `id_groupe_users` (`id_groupe_users`)
) ENGINE=InnoDB AUTO_INCREMENT=737 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acces`
--

LOCK TABLES `acces` WRITE;
/*!40000 ALTER TABLE `acces` DISABLE KEYS */;
INSERT INTO `acces` VALUES (1,2,1,0),(2,2,2,0),(3,2,3,0),(4,2,4,0),(5,2,5,0),(6,2,6,0),(7,2,7,0),(8,2,8,0),(9,2,9,0),(10,2,10,0),(11,2,134,0),(12,2,11,0),(13,2,12,0),(14,2,13,0),(15,2,14,0),(16,2,15,0),(17,2,16,0),(18,2,17,0),(19,2,18,0),(20,2,19,0),(21,2,22,1),(22,2,23,1),(23,2,24,1),(24,2,25,1),(25,2,26,1),(26,2,27,0),(27,2,28,0),(28,2,29,0),(29,2,30,0),(30,2,154,0),(31,2,31,0),(32,2,32,0),(33,2,33,0),(34,2,34,0),(35,2,35,0),(36,2,36,0),(37,2,37,0),(38,2,38,0),(39,2,39,0),(40,2,40,0),(41,2,41,0),(42,2,42,0),(43,2,43,0),(44,2,44,0),(45,2,45,0),(46,2,46,0),(47,2,47,0),(48,2,48,0),(49,2,49,1),(50,2,50,1),(51,2,51,1),(52,2,52,1),(53,2,53,1),(54,2,125,1),(55,2,20,0),(56,2,21,0),(57,2,86,1),(58,2,87,1),(59,2,88,1),(60,2,89,1),(61,2,90,1),(62,2,91,1),(63,2,100,1),(64,2,101,1),(65,2,102,1),(66,2,103,1),(67,2,104,1),(68,2,105,1),(69,2,106,1),(70,2,107,1),(71,2,108,1),(72,2,109,1),(73,2,110,1),(74,2,111,1),(75,2,112,1),(76,2,113,1),(77,2,114,1),(78,2,115,1),(79,2,116,1),(80,2,117,1),(81,2,118,1),(82,2,119,1),(83,2,155,1),(84,2,120,0),(85,2,121,0),(86,2,122,0),(87,2,123,0),(88,2,92,1),(89,2,93,1),(90,2,94,1),(91,2,95,1),(92,2,96,1),(93,2,97,1),(94,2,98,1),(95,2,99,1),(96,2,126,1),(97,2,127,1),(98,2,128,1),(99,2,129,1),(100,2,130,1),(101,2,131,0),(102,2,132,0),(103,2,133,0),(104,2,135,1),(105,2,136,1),(106,2,137,1),(107,2,138,1),(108,2,153,1),(109,2,139,1),(110,2,140,1),(111,2,141,1),(112,2,142,1),(113,2,143,1),(114,2,144,1),(115,2,175,1),(116,2,176,1),(117,2,145,1),(118,2,146,1),(119,2,147,1),(120,2,148,1),(121,2,149,1),(122,2,150,1),(123,2,151,1),(124,2,152,1),(125,2,156,1),(126,2,157,1),(127,2,158,1),(128,2,159,1),(129,2,165,1),(130,2,166,1),(131,2,167,1),(132,2,168,1),(133,2,160,1),(134,2,161,1),(135,2,162,1),(136,2,163,1),(137,2,169,1),(138,2,170,1),(139,2,171,1),(140,2,172,1),(141,2,164,1),(142,2,173,1),(143,2,174,1),(144,2,177,0),(145,2,178,0),(146,2,179,0),(147,2,180,0),(148,2,181,0),(149,2,182,0),(150,2,183,0),(151,2,184,0),(152,2,185,0),(153,2,186,0),(154,2,187,0),(155,2,188,0),(156,2,189,0),(157,2,190,0),(158,2,191,0),(159,2,192,0),(160,2,193,0),(161,2,194,0),(162,2,195,0),(163,2,196,0),(164,2,197,0),(165,2,198,0),(166,2,199,0),(167,2,200,0),(168,2,201,0),(169,2,202,0),(170,2,203,0),(171,2,204,0),(172,2,205,0),(173,2,206,0),(174,2,207,0),(175,2,208,0),(176,2,209,0),(177,2,210,0),(178,2,211,0),(179,2,212,0),(180,2,213,0),(181,2,214,0),(182,2,215,0),(183,2,216,0),(184,2,217,0),(553,3,11,0),(554,3,12,0),(555,3,13,0),(556,3,14,0),(557,3,15,0),(558,3,16,0),(559,3,17,0),(560,3,18,0),(561,3,19,0),(562,3,1,0),(563,3,2,0),(564,3,3,0),(565,3,4,0),(566,3,5,0),(567,3,6,0),(568,3,7,0),(569,3,8,0),(570,3,9,0),(571,3,10,0),(572,3,134,0),(573,3,131,0),(574,3,132,0),(575,3,133,0),(576,3,20,0),(577,3,21,0),(578,3,22,0),(579,3,23,0),(580,3,24,0),(581,3,25,0),(582,3,26,0),(583,3,160,0),(584,3,161,0),(585,3,162,0),(586,3,163,0),(587,3,169,0),(588,3,170,0),(589,3,171,0),(590,3,172,0),(591,3,45,0),(592,3,46,0),(593,3,47,0),(594,3,48,0),(595,3,44,0),(596,3,42,0),(597,3,43,0),(598,3,40,0),(599,3,41,0),(600,3,35,0),(601,3,36,0),(602,3,37,0),(603,3,38,0),(604,3,39,0),(605,3,31,0),(606,3,32,0),(607,3,33,0),(608,3,34,0),(609,3,27,0),(610,3,28,0),(611,3,29,0),(612,3,30,0),(613,3,154,0),(614,3,49,0),(615,3,50,0),(616,3,51,0),(617,3,52,0),(618,3,53,0),(619,3,125,0),(620,3,96,0),(621,3,97,0),(622,3,98,0),(623,3,99,0),(624,3,92,0),(625,3,93,0),(626,3,94,0),(627,3,95,0),(628,3,174,0),(629,3,104,0),(630,3,100,0),(631,3,101,0),(632,3,102,0),(633,3,103,0),(634,3,90,0),(635,3,91,0),(636,3,86,0),(637,3,87,0),(638,3,88,0),(639,3,89,0),(640,3,105,1),(641,3,106,1),(642,3,107,1),(643,3,108,1),(644,3,109,1),(645,3,110,1),(646,3,111,1),(647,3,112,1),(648,3,113,1),(649,3,114,1),(650,3,115,1),(651,3,156,1),(652,3,157,1),(653,3,158,1),(654,3,159,1),(655,3,165,1),(656,3,166,1),(657,3,167,1),(658,3,168,1),(659,3,164,0),(660,3,173,0),(661,3,120,0),(662,3,121,0),(663,3,122,0),(664,3,123,0),(665,3,126,0),(666,3,127,0),(667,3,128,0),(668,3,129,0),(669,3,116,0),(670,3,117,0),(671,3,118,0),(672,3,119,0),(673,3,155,0),(674,3,130,0),(675,3,143,0),(676,3,135,1),(677,3,136,1),(678,3,137,1),(679,3,138,1),(680,3,153,1),(681,3,139,1),(682,3,140,1),(683,3,141,1),(684,3,142,1),(685,3,144,1),(686,3,175,1),(687,3,176,1),(688,3,145,1),(689,3,146,1),(690,3,147,1),(691,3,148,1),(692,3,149,1),(693,3,150,1),(694,3,151,1),(695,3,152,1),(696,3,177,0),(697,3,178,0),(698,3,179,0),(699,3,180,0),(700,3,181,0),(701,3,209,0),(702,3,210,0),(703,3,211,0),(704,3,212,0),(705,3,201,0),(706,3,202,0),(707,3,189,0),(708,3,190,0),(709,3,187,0),(710,3,188,0),(711,3,182,0),(712,3,183,0),(713,3,184,0),(714,3,185,0),(715,3,186,0),(716,3,191,0),(717,3,192,0),(718,3,193,0),(719,3,194,0),(720,3,195,0),(721,3,196,0),(722,3,197,0),(723,3,198,0),(724,3,199,0),(725,3,200,0),(726,3,213,0),(727,3,214,0),(728,3,215,0),(729,3,216,0),(730,3,217,0),(731,3,203,0),(732,3,204,0),(733,3,205,0),(734,3,206,0),(735,3,207,0),(736,3,208,0);
/*!40000 ALTER TABLE `acces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_categorie_article` int DEFAULT NULL,
  `type_article` varchar(255) DEFAULT NULL,
  `designation_article` varchar(255) DEFAULT NULL,
  `reference_article` varchar(255) DEFAULT NULL,
  `numero_article` double DEFAULT '0',
  `stock` double DEFAULT '0',
  `stock_securite` double DEFAULT '0',
  `total_article` double DEFAULT '0',
  `prix_unitaire` double DEFAULT '0',
  `estArticle_chambre` int DEFAULT '0',
  `etat_suppression` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_famille_article` (`id_categorie_article`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_appro_details`
--

DROP TABLE IF EXISTS `article_appro_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_appro_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_approvisionnement_id` bigint unsigned NOT NULL,
  `article_id` bigint unsigned NOT NULL,
  `magasin_id` bigint unsigned NOT NULL,
  `prix_unitaire` double NOT NULL DEFAULT '0',
  `quantite` double NOT NULL DEFAULT '0',
  `montant` double NOT NULL DEFAULT '0',
  `remise` double NOT NULL DEFAULT '0',
  `remise_ariary` double NOT NULL DEFAULT '0',
  `tva_detail` double NOT NULL DEFAULT '0',
  `montant_tva` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `valeur_remise` double DEFAULT '0',
  `valeur_ht` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `article_appro_details_article_approvisionnement_id_foreign` (`article_approvisionnement_id`),
  KEY `article_appro_details_article_id_foreign` (`article_id`),
  KEY `article_appro_details_magasin_id_foreign` (`magasin_id`),
  CONSTRAINT `article_appro_details_article_approvisionnement_id_foreign` FOREIGN KEY (`article_approvisionnement_id`) REFERENCES `article_approvisionnements` (`id`),
  CONSTRAINT `article_appro_details_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `article_appro_details_magasin_id_foreign` FOREIGN KEY (`magasin_id`) REFERENCES `magasins` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_appro_details`
--

LOCK TABLES `article_appro_details` WRITE;
/*!40000 ALTER TABLE `article_appro_details` DISABLE KEYS */;
INSERT INTO `article_appro_details` VALUES (1,1,1,2,450000,2,882000,2,18000,0,0,'2025-06-24 06:50:20','2025-06-24 10:18:40','2025-06-24 10:18:40',882000,0),(2,2,1,2,450000,1,361800,20,88200,20,72360,'2025-06-24 06:51:01','2025-06-24 09:45:54','2025-06-24 09:45:54',361800,0),(3,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 06:51:01','2025-06-24 09:45:54','2025-06-24 09:45:54',301500,0),(5,2,1,2,450000,1,361800,20,88200,20,72360,'2025-06-24 09:45:54','2025-06-24 09:46:04','2025-06-24 09:46:04',361800,0),(6,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 09:45:54','2025-06-24 09:46:04','2025-06-24 09:46:04',301500,0),(7,2,1,2,450000,1,361800,20,88200,20,72360,'2025-06-24 09:46:04','2025-06-24 09:46:20','2025-06-24 09:46:20',361800,0),(8,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 09:46:04','2025-06-24 09:46:20','2025-06-24 09:46:20',301500,0),(9,2,1,2,450000,5,2161800,20,88200,20,432360,'2025-06-24 09:46:20','2025-06-24 10:03:59','2025-06-24 10:03:59',2161800,0),(10,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 09:46:20','2025-06-24 10:03:59','2025-06-24 10:03:59',301500,0),(11,2,1,2,450000,5,2161800,20,88200,20,432360,'2025-06-24 10:04:36','2025-06-24 10:18:17','2025-06-24 10:18:17',2161800,0),(12,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 10:04:36','2025-06-24 10:18:17','2025-06-24 10:18:17',301500,0),(13,2,1,2,450000,5,2161800,20,88200,20,432360,'2025-06-24 10:18:17','2025-06-24 10:19:07','2025-06-24 10:19:07',2161800,0),(14,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 10:18:17','2025-06-24 10:19:07','2025-06-24 10:19:07',301500,0),(15,1,1,2,450000,10,4500000,0,0,0,0,'2025-06-24 10:18:40','2025-06-24 10:18:40',NULL,4500000,0),(16,2,1,2,450000,5,2161800,20,88200,20,432360,'2025-06-24 10:19:07','2025-06-24 10:19:48','2025-06-24 10:19:48',2161800,0),(17,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 10:19:07','2025-06-24 10:19:48','2025-06-24 10:19:48',301500,0),(18,2,1,2,450000,5,2161800,20,88200,20,432360,'2025-06-24 10:19:48','2025-06-24 10:19:48',NULL,2161800,0),(19,2,2,2,75000,5,301500,20,73500,20,60300,'2025-06-24 10:19:48','2025-06-24 10:19:48',NULL,301500,0),(20,4,5,1,2500000,7,17500000,0,0,20,3500000,'2025-07-01 10:38:57','2025-07-01 10:38:57',NULL,17500000,0),(21,5,1,2,450000,5,2250000,0,0,0,0,'2025-07-01 10:53:17','2025-07-01 10:53:17',NULL,2250000,0),(22,5,2,2,75000,5,375000,0,0,0,0,'2025-07-01 10:53:17','2025-07-01 10:53:17',NULL,375000,0),(25,8,1,2,450000,5,2250000,0,0,0,0,'2025-07-01 10:58:13','2025-07-01 10:58:13',NULL,2250000,0),(26,8,2,2,75000,5,375000,0,0,0,0,'2025-07-01 10:58:13','2025-07-01 10:58:13',NULL,375000,0),(28,10,1,4,450000,5,2250000,0,0,0,0,'2025-07-01 11:00:54','2025-07-03 08:57:36','2025-07-03 08:57:36',2250000,0),(29,11,2,1,75000,2,150000,0,0,0,0,'2025-07-03 06:42:49','2025-07-03 06:42:49',NULL,150000,0),(30,11,6,1,15000,2,30000,0,0,0,0,'2025-07-03 06:42:49','2025-07-03 06:42:49',NULL,30000,0),(31,10,1,4,450000,5,2250000,0,0,0,0,'2025-07-03 08:57:36','2025-07-03 08:57:36',NULL,2250000,0),(32,12,3,1,3200000,2,6400000,0,0,0,0,'2025-07-04 05:59:18','2025-07-04 05:59:18',NULL,6400000,0),(33,12,2,1,75000,2,150000,0,0,0,0,'2025-07-04 05:59:18','2025-07-04 05:59:18',NULL,150000,0),(34,12,1,1,450000,2,900000,0,0,0,0,'2025-07-04 05:59:18','2025-07-04 05:59:18',NULL,900000,0),(35,12,5,1,2500000,2,5000000,0,0,0,0,'2025-07-04 05:59:18','2025-07-04 05:59:18',NULL,5000000,0);
/*!40000 ALTER TABLE `article_appro_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_approvisionnement`
--

DROP TABLE IF EXISTS `article_approvisionnement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_approvisionnement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_users` int NOT NULL DEFAULT '0',
  `id_res_magasin` int NOT NULL DEFAULT '0',
  `id_fournisseur` int unsigned NOT NULL DEFAULT '0',
  `date_appro` date DEFAULT NULL,
  `num_bon_livraison` varchar(255) DEFAULT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `montant_ht_appro` double DEFAULT '0',
  `montant_tva_appro` double DEFAULT '0',
  `montant_ttc_appro` double DEFAULT '0',
  `montant_a_payer_appro` double DEFAULT '0',
  `montant_payer_appro` double DEFAULT '0',
  `montant_reste_a_payer_appro` double DEFAULT '0',
  `remise_general` double DEFAULT '0',
  `remise_general_ariary` double DEFAULT '0',
  `taux_tva` double DEFAULT '0',
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `type_approvisionnement` varchar(255) DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `is_regler_echeanche` int DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_users` (`id_users`) USING BTREE,
  KEY `id_fournisseur` (`id_fournisseur`) USING BTREE,
  KEY `id_res_magasin` (`id_res_magasin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_approvisionnement`
--

LOCK TABLES `article_approvisionnement` WRITE;
/*!40000 ALTER TABLE `article_approvisionnement` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_approvisionnement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_approvisionnement_detail`
--

DROP TABLE IF EXISTS `article_approvisionnement_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_approvisionnement_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article_approvisionnement` int NOT NULL DEFAULT '0',
  `id_article` int NOT NULL DEFAULT '0',
  `id_res_magasin` int NOT NULL DEFAULT '0',
  `prix_unitaire` double NOT NULL DEFAULT '0',
  `quantite` double NOT NULL DEFAULT '0',
  `montant` double NOT NULL DEFAULT '0',
  `remise` double NOT NULL DEFAULT '0',
  `remise_ariary` double NOT NULL DEFAULT '0',
  `tva_detail` double NOT NULL DEFAULT '0',
  `montant_tva` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_article_approvisionnement` (`id_article_approvisionnement`) USING BTREE,
  KEY `id_article` (`id_article`) USING BTREE,
  KEY `id_res_magasin` (`id_res_magasin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_approvisionnement_detail`
--

LOCK TABLES `article_approvisionnement_detail` WRITE;
/*!40000 ALTER TABLE `article_approvisionnement_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_approvisionnement_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_approvisionnement_reglement`
--

DROP TABLE IF EXISTS `article_approvisionnement_reglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_approvisionnement_reglement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_users` int DEFAULT NULL,
  `id_article_approvisionnement` int DEFAULT NULL,
  `tresorerie_mvt_id` int DEFAULT NULL,
  `date_reglement` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `montant_reglement_payer` double DEFAULT '0',
  `mode_regement` varchar(255) DEFAULT NULL,
  `reference_reglement` varchar(255) DEFAULT NULL,
  `commentaire_reglemet` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_users` (`id_users`) USING BTREE,
  KEY `id_approvisionnement` (`id_article_approvisionnement`) USING BTREE,
  KEY `tresorerie_mvt_id` (`tresorerie_mvt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_approvisionnement_reglement`
--

LOCK TABLES `article_approvisionnement_reglement` WRITE;
/*!40000 ALTER TABLE `article_approvisionnement_reglement` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_approvisionnement_reglement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_approvisionnements`
--

DROP TABLE IF EXISTS `article_approvisionnements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_approvisionnements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_appro` date DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `magasin_id` bigint unsigned NOT NULL,
  `fournisseur_id` bigint unsigned NOT NULL,
  `montant_ht_appro` double NOT NULL DEFAULT '0',
  `montant_tva_appro` double NOT NULL DEFAULT '0',
  `montant_ttc_appro` double NOT NULL DEFAULT '0',
  `montant_a_payer_appro` double NOT NULL DEFAULT '0',
  `montant_payer_appro` double NOT NULL DEFAULT '0',
  `montant_reste_a_payer_appro` double NOT NULL DEFAULT '0',
  `remise_general` double NOT NULL DEFAULT '0',
  `remise_general_ariary` double NOT NULL DEFAULT '0',
  `numero_bon_commande` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taux_tva` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_approvisionnement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_regler_echeanche` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `boncommande_fournisseur_id` bigint unsigned DEFAULT NULL,
  `numero_bon_livraison` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_approvisionnements_user_id_foreign` (`user_id`),
  KEY `article_approvisionnements_magasin_id_foreign` (`magasin_id`),
  KEY `article_approvisionnements_fournisseur_id_foreign` (`fournisseur_id`),
  CONSTRAINT `article_approvisionnements_fournisseur_id_foreign` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`),
  CONSTRAINT `article_approvisionnements_magasin_id_foreign` FOREIGN KEY (`magasin_id`) REFERENCES `magasins` (`id`),
  CONSTRAINT `article_approvisionnements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_approvisionnements`
--

LOCK TABLES `article_approvisionnements` WRITE;
/*!40000 ALTER TABLE `article_approvisionnements` DISABLE KEYS */;
INSERT INTO `article_approvisionnements` VALUES (1,'2025-06-26',NULL,NULL,8,2,5,4500000,900000,4950000,0,0,0,10,450000,'dfdf','20',NULL,0,'2025-06-24 06:50:20','2025-06-24 10:18:40',NULL,NULL,NULL),(2,'2025-06-18',NULL,NULL,8,2,7,2463300,492660,2955960,0,0,0,0,0,'xxxx','0',NULL,0,'2025-06-24 06:51:01','2025-06-24 10:19:48',NULL,NULL,NULL),(4,'2025-07-01',NULL,'2025-07-01 12:38:57',1,1,1,17500000,3500000,21000000,0,0,0,0,0,'xx','0',NULL,0,'2025-07-01 10:38:57','2025-07-01 10:38:57',NULL,NULL,NULL),(5,'2025-07-01',NULL,'2025-07-01 12:53:17',1,2,2,2625000,0,2625000,0,0,0,0,0,'dfq','0',NULL,0,'2025-07-01 10:53:17','2025-07-01 10:53:17',NULL,NULL,NULL),(8,'2025-07-01',NULL,'2025-07-01 12:58:13',1,2,2,2625000,0,2625000,0,0,0,0,0,'dfq','0',NULL,0,'2025-07-01 10:58:13','2025-07-01 10:58:13',NULL,NULL,NULL),(10,'2025-07-01',NULL,'2025-07-01 13:00:54',1,4,2,2250000,0,2250000,0,0,0,0,0,'dfdsf','0',NULL,0,'2025-07-01 11:00:54','2025-07-01 11:00:54',NULL,NULL,NULL),(11,'2025-07-03',NULL,'2025-07-03 08:42:49',1,1,1,180000,0,180000,0,0,0,0,0,'BCF-006/07-2025','0',NULL,0,'2025-07-03 06:42:49','2025-07-03 06:42:49',NULL,10,'00111dddd'),(12,'2025-07-12',NULL,'2025-07-04 07:59:17',1,1,2,12450000,0,12450000,0,0,0,0,0,NULL,'0',NULL,0,'2025-07-04 05:59:18','2025-07-04 05:59:18',NULL,NULL,'xxxx1425');
/*!40000 ALTER TABLE `article_approvisionnements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_categorie`
--

DROP TABLE IF EXISTS `article_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libeller_categorie` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_categorie`
--

LOCK TABLES `article_categorie` WRITE;
/*!40000 ALTER TABLE `article_categorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_familles`
--

DROP TABLE IF EXISTS `article_familles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_familles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_famille_article` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_familles`
--

LOCK TABLES `article_familles` WRITE;
/*!40000 ALTER TABLE `article_familles` DISABLE KEYS */;
INSERT INTO `article_familles` VALUES (1,'Roues & Pneus','2025-06-18 04:50:20','2025-06-18 04:50:20',NULL),(2,'Moteur & Entretien','2025-06-18 04:51:42','2025-06-18 04:51:42',NULL),(3,'Transmission','2025-06-18 06:17:25','2025-06-18 06:17:25',NULL),(4,'teste','2025-06-18 06:20:29','2025-06-18 06:20:29',NULL),(5,'nouveau famillle','2025-07-01 05:44:59','2025-07-01 05:44:59',NULL);
/*!40000 ALTER TABLE `article_familles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_inventaire`
--

DROP TABLE IF EXISTS `article_inventaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_inventaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article` int DEFAULT '0',
  `id_users` int DEFAULT '0',
  `date_inventaire` date DEFAULT NULL,
  `stock_theorique` double DEFAULT '0',
  `stock_physique` double DEFAULT '0',
  `ecart_stock` double DEFAULT '0',
  `commentaire` varchar(255) DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id_article` (`id_article`) USING BTREE,
  KEY `id_users` (`id_users`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_inventaire`
--

LOCK TABLES `article_inventaire` WRITE;
/*!40000 ALTER TABLE `article_inventaire` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_inventaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_inventaire_par_chambre`
--

DROP TABLE IF EXISTS `article_inventaire_par_chambre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_inventaire_par_chambre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chambre` int DEFAULT NULL,
  `date_inventaire_par_chambre` date DEFAULT NULL,
  `date_heure_enregistrement_par_chambre` datetime DEFAULT NULL,
  `libeller_inventaire_par_chambre` varchar(255) DEFAULT NULL,
  `commentaire_par_chambre` text,
  PRIMARY KEY (`id`),
  KEY `id_chambre` (`id_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_inventaire_par_chambre`
--

LOCK TABLES `article_inventaire_par_chambre` WRITE;
/*!40000 ALTER TABLE `article_inventaire_par_chambre` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_inventaire_par_chambre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_inventaire_par_chambre_detail`
--

DROP TABLE IF EXISTS `article_inventaire_par_chambre_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_inventaire_par_chambre_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article_par_chambre` int DEFAULT NULL,
  `quantite_reel` float DEFAULT NULL,
  `quantite_theorique` double DEFAULT NULL,
  `ecart_quantite` double DEFAULT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `id_article_inventaire_par_chambre` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article_par_chambre`) USING BTREE,
  KEY `id_article_inventaire_par_chambre` (`id_article_inventaire_par_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_inventaire_par_chambre_detail`
--

LOCK TABLES `article_inventaire_par_chambre_detail` WRITE;
/*!40000 ALTER TABLE `article_inventaire_par_chambre_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_inventaire_par_chambre_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_inventaires`
--

DROP TABLE IF EXISTS `article_inventaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_inventaires` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_inventaire` date DEFAULT NULL,
  `article_id` bigint unsigned DEFAULT NULL,
  `magasin_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `stock_theorique` double NOT NULL DEFAULT '0',
  `stock_reel` double NOT NULL DEFAULT '0',
  `ecart_stock` double NOT NULL DEFAULT '0',
  `remarque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `date_heure_enregistrment` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_inventaires_article_id_foreign` (`article_id`),
  KEY `article_inventaires_magasin_id_foreign` (`magasin_id`),
  KEY `article_inventaires_user_id_foreign` (`user_id`),
  CONSTRAINT `article_inventaires_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `article_inventaires_magasin_id_foreign` FOREIGN KEY (`magasin_id`) REFERENCES `magasins` (`id`),
  CONSTRAINT `article_inventaires_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_inventaires`
--

LOCK TABLES `article_inventaires` WRITE;
/*!40000 ALTER TABLE `article_inventaires` DISABLE KEYS */;
INSERT INTO `article_inventaires` VALUES (1,'2025-06-13',1,4,8,0,50,0,'ttt','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL,NULL),(2,'2025-06-13',2,4,8,0,50,0,'ssf','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL,NULL),(3,'2025-06-13',3,4,8,0,50,0,'hrt  fdsf','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL,NULL),(4,'2025-06-13',2,4,8,0,1,0,NULL,'2025-06-19 12:53:44','2025-06-19 12:53:44',NULL,NULL),(5,'2025-06-13',3,4,8,0,1,0,NULL,'2025-06-19 12:53:44','2025-06-19 12:53:44',NULL,NULL),(6,'2025-06-20',1,2,8,0,5,5,'teste','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL,NULL),(7,'2025-06-20',2,2,8,1,6,5,'bla','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL,NULL),(8,'2025-06-20',3,2,8,1,7,6,'blabla','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL,NULL),(9,'2025-06-20',2,2,8,7,0,-7,'d','2025-06-20 06:54:53','2025-06-20 06:54:53',NULL,NULL),(10,'2025-06-20',3,2,8,8,0,-8,'d','2025-06-20 06:54:54','2025-06-20 06:54:54',NULL,NULL),(11,'2025-07-24',5,4,1,0,10,10,NULL,'2025-07-01 05:46:17','2025-07-01 05:46:17',NULL,NULL),(12,'2025-07-24',3,4,1,5,15,10,NULL,'2025-07-01 05:46:17','2025-07-01 05:46:17',NULL,NULL),(13,'2025-07-24',2,4,1,20,7,-13,NULL,'2025-07-01 05:46:17','2025-07-01 05:46:17',NULL,NULL),(14,'2025-07-01',3,4,1,117,10,-107,NULL,'2025-07-01 11:54:49','2025-07-01 11:54:49',NULL,'2025-07-01 13:54:49'),(15,'2025-07-01',3,4,1,10,30,20,'tes','2025-07-01 11:59:03','2025-07-01 11:59:03',NULL,'2025-07-01 13:59:03'),(16,'2025-07-01',2,4,1,7,12,5,'ff','2025-07-01 11:59:03','2025-07-01 11:59:03',NULL,'2025-07-01 13:59:03'),(17,'2025-07-01',6,4,1,0,18,18,'qqqq','2025-07-01 11:59:03','2025-07-01 11:59:03',NULL,'2025-07-01 13:59:03');
/*!40000 ALTER TABLE `article_inventaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_magasin`
--

DROP TABLE IF EXISTS `article_magasin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_magasin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `magasin_id` bigint unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_magasin_article_id_magasin_id_unique` (`article_id`,`magasin_id`),
  KEY `article_magasin_magasin_id_foreign` (`magasin_id`),
  CONSTRAINT `article_magasin_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `article_magasin_magasin_id_foreign` FOREIGN KEY (`magasin_id`) REFERENCES `magasins` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_magasin`
--

LOCK TABLES `article_magasin` WRITE;
/*!40000 ALTER TABLE `article_magasin` DISABLE KEYS */;
INSERT INTO `article_magasin` VALUES (1,2,4,12,'2025-06-19 12:53:44','2025-07-01 11:59:03',NULL),(2,3,4,30,'2025-06-19 12:53:44','2025-07-01 11:59:03',NULL),(3,1,2,42,'2025-06-20 05:25:20','2025-07-01 10:58:13',NULL),(4,2,2,100,'2025-06-20 05:25:20','2025-07-01 11:52:03',NULL),(5,3,2,100,'2025-06-20 05:25:20','2025-07-01 11:52:03',NULL),(6,1,1,25,'2025-06-23 11:57:32','2025-07-07 06:47:38',NULL),(7,2,1,20,'2025-06-23 11:57:32','2025-07-04 12:18:13',NULL),(8,3,1,10,'2025-06-23 11:57:32','2025-07-04 12:18:13',NULL),(9,5,4,10,'2025-07-01 05:46:17','2025-07-01 05:46:17',NULL),(10,5,1,30,'2025-07-01 10:38:57','2025-07-07 06:47:38',NULL),(11,1,4,5,'2025-07-01 11:00:54','2025-07-03 08:57:36',NULL),(12,6,1,50,'2025-07-01 11:51:33','2025-07-07 05:39:50',NULL),(13,6,2,100,'2025-07-01 11:52:03','2025-07-01 11:52:03',NULL),(14,6,4,18,'2025-07-01 11:59:03','2025-07-01 11:59:03',NULL),(15,6,5,2,'2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(16,5,5,2,'2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(17,1,5,12,'2025-07-07 05:52:18','2025-07-07 06:38:40',NULL);
/*!40000 ALTER TABLE `article_magasin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_mouvement`
--

DROP TABLE IF EXISTS `article_mouvement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_mouvement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article` int DEFAULT NULL,
  `id_res_magasin` int DEFAULT NULL,
  `id_chambre` int DEFAULT NULL,
  `id_users` int DEFAULT NULL,
  `type_mvt` varchar(255) DEFAULT NULL,
  `operation_mvt` varchar(255) DEFAULT NULL,
  `stock_avant_mvt` double DEFAULT '0',
  `stock_apres_mvt` double DEFAULT '0',
  `quantite_article_mvt` double DEFAULT '0',
  `quantiter_article_totale_mvt` double DEFAULT '0',
  `date_mouvement` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `type_stockage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`),
  KEY `id_chambre` (`id_chambre`),
  KEY `id_users` (`id_users`),
  KEY `id_res_magasin` (`id_res_magasin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_mouvement`
--

LOCK TABLES `article_mouvement` WRITE;
/*!40000 ALTER TABLE `article_mouvement` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_mouvement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_mouvements`
--

DROP TABLE IF EXISTS `article_mouvements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_mouvements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned DEFAULT NULL,
  `magasin_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `qte_initial` double NOT NULL DEFAULT '0',
  `qte_mvt` double NOT NULL DEFAULT '0',
  `qte_final` double NOT NULL DEFAULT '0',
  `reference_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mvt` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `commentaire_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_mouvements_article_id_foreign` (`article_id`),
  KEY `article_mouvements_magasin_id_foreign` (`magasin_id`),
  KEY `article_mouvements_user_id_foreign` (`user_id`),
  CONSTRAINT `article_mouvements_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `article_mouvements_magasin_id_foreign` FOREIGN KEY (`magasin_id`) REFERENCES `magasins` (`id`),
  CONSTRAINT `article_mouvements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_mouvements`
--

LOCK TABLES `article_mouvements` WRITE;
/*!40000 ALTER TABLE `article_mouvements` DISABLE KEYS */;
INSERT INTO `article_mouvements` VALUES (12,1,2,8,0,0,0,'','2025-06-12','2025-06-19 00:00:00',NULL,'INVENTAIRE','2025-06-19 12:28:01','2025-06-19 12:28:01',NULL),(15,1,2,8,0,5,5,'','2025-06-14','2025-06-19 00:00:00',NULL,'INVENTAIRE','2025-06-19 12:31:38','2025-06-19 12:31:38',NULL),(16,1,4,8,0,50,50,'','2025-06-13','2025-06-19 00:00:00','ttt','INVENTAIRE','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL),(17,2,4,8,0,50,50,'','2025-06-13','2025-06-19 00:00:00','ssf','INVENTAIRE','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL),(18,3,4,8,0,50,50,'','2025-06-13','2025-06-19 00:00:00','hrt  fdsf','INVENTAIRE','2025-06-19 12:50:16','2025-06-19 12:50:16',NULL),(19,2,4,8,0,1,1,'','2025-06-13','2025-06-19 00:00:00',NULL,'INVENTAIRE','2025-06-19 12:53:44','2025-06-19 12:53:44',NULL),(20,3,4,8,0,1,1,'','2025-06-13','2025-06-19 00:00:00',NULL,'INVENTAIRE','2025-06-19 12:53:44','2025-06-19 12:53:44',NULL),(21,1,2,8,0,5,5,'','2025-06-20','2025-06-20 00:00:00','teste','INVENTAIRE','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL),(22,2,2,8,1,6,6,'','2025-06-20','2025-06-20 00:00:00','bla','INVENTAIRE','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL),(23,3,2,8,1,7,7,'','2025-06-20','2025-06-20 00:00:00','blabla','INVENTAIRE','2025-06-20 05:25:20','2025-06-20 05:25:20',NULL),(24,2,2,8,7,0,0,'','2025-06-20','2025-06-20 00:00:00','d','INVENTAIRE','2025-06-20 06:54:54','2025-06-20 06:54:54',NULL),(25,3,2,8,8,0,0,'','2025-06-20','2025-06-20 00:00:00','d','INVENTAIRE','2025-06-20 06:54:54','2025-06-20 06:54:54',NULL),(26,1,2,8,5,1,6,NULL,NULL,'2025-06-23 13:51:32',NULL,'APPROVISIONNEMENT','2025-06-23 11:51:32','2025-06-23 11:51:32',NULL),(27,1,2,8,5,1,6,NULL,NULL,'2025-06-23 13:52:02',NULL,'APPROVISIONNEMENT','2025-06-23 11:52:02','2025-06-23 11:52:02',NULL),(28,1,2,8,5,10,15,NULL,NULL,'2025-06-23 13:52:23',NULL,'APPROVISIONNEMENT','2025-06-23 11:52:23','2025-06-23 11:52:23',NULL),(29,2,2,8,0,10,10,NULL,NULL,'2025-06-23 13:52:23',NULL,'APPROVISIONNEMENT','2025-06-23 11:52:23','2025-06-23 11:52:23',NULL),(30,1,2,8,15,1,16,NULL,NULL,'2025-06-23 13:54:10',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:10','2025-06-23 11:54:10',NULL),(31,2,2,8,10,1,11,NULL,NULL,'2025-06-23 13:54:10',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:10','2025-06-23 11:54:10',NULL),(32,3,2,8,0,1,1,NULL,NULL,'2025-06-23 13:54:10',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:10','2025-06-23 11:54:10',NULL),(33,1,2,8,16,1,17,NULL,NULL,'2025-06-23 13:54:30',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:30','2025-06-23 11:54:30',NULL),(34,2,2,8,11,1,12,NULL,NULL,'2025-06-23 13:54:30',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:30','2025-06-23 11:54:30',NULL),(35,3,2,8,1,1,2,NULL,NULL,'2025-06-23 13:54:30',NULL,'APPROVISIONNEMENT','2025-06-23 11:54:30','2025-06-23 11:54:30',NULL),(36,1,1,8,0,2,2,NULL,NULL,'2025-06-23 13:57:31',NULL,'APPROVISIONNEMENT','2025-06-23 11:57:32','2025-06-23 11:57:32',NULL),(37,2,1,8,0,2,2,NULL,NULL,'2025-06-23 13:57:31',NULL,'APPROVISIONNEMENT','2025-06-23 11:57:32','2025-06-23 11:57:32',NULL),(38,3,1,8,0,2,2,NULL,NULL,'2025-06-23 13:57:31',NULL,'APPROVISIONNEMENT','2025-06-23 11:57:32','2025-06-23 11:57:32',NULL),(39,1,2,8,17,2,19,NULL,NULL,'2025-06-24 08:50:20',NULL,'APPROVISIONNEMENT','2025-06-24 06:50:20','2025-06-24 06:50:20',NULL),(40,1,2,8,19,1,20,NULL,NULL,'2025-06-24 08:51:01',NULL,'APPROVISIONNEMENT','2025-06-24 06:51:01','2025-06-24 06:51:01',NULL),(41,2,2,8,12,5,17,NULL,NULL,'2025-06-24 08:51:01',NULL,'APPROVISIONNEMENT','2025-06-24 06:51:01','2025-06-24 06:51:01',NULL),(42,1,2,8,20,1,19,NULL,NULL,'2025-06-24 11:45:54',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:45:54','2025-06-24 09:45:54',NULL),(43,2,2,8,17,5,12,NULL,NULL,'2025-06-24 11:45:54',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:45:54','2025-06-24 09:45:54',NULL),(44,1,2,8,19,1,20,NULL,NULL,'2025-06-24 11:45:54',NULL,'APPROVISIONNEMENT','2025-06-24 09:45:54','2025-06-24 09:45:54',NULL),(45,2,2,8,12,5,17,NULL,NULL,'2025-06-24 11:45:54',NULL,'APPROVISIONNEMENT','2025-06-24 09:45:54','2025-06-24 09:45:54',NULL),(46,1,2,8,20,1,19,NULL,NULL,'2025-06-24 11:46:04',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:46:04','2025-06-24 09:46:04',NULL),(47,2,2,8,17,5,12,NULL,NULL,'2025-06-24 11:46:04',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:46:04','2025-06-24 09:46:04',NULL),(48,1,2,8,19,1,20,NULL,NULL,'2025-06-24 11:46:04',NULL,'APPROVISIONNEMENT','2025-06-24 09:46:04','2025-06-24 09:46:04',NULL),(49,2,2,8,12,5,17,NULL,NULL,'2025-06-24 11:46:04',NULL,'APPROVISIONNEMENT','2025-06-24 09:46:04','2025-06-24 09:46:04',NULL),(50,1,2,8,20,1,19,NULL,NULL,'2025-06-24 11:46:20',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:46:20','2025-06-24 09:46:20',NULL),(51,2,2,8,17,5,12,NULL,NULL,'2025-06-24 11:46:20',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 09:46:20','2025-06-24 09:46:20',NULL),(52,1,2,8,19,5,24,NULL,NULL,'2025-06-24 11:46:20',NULL,'APPROVISIONNEMENT','2025-06-24 09:46:20','2025-06-24 09:46:20',NULL),(53,2,2,8,12,5,17,NULL,NULL,'2025-06-24 11:46:20',NULL,'APPROVISIONNEMENT','2025-06-24 09:46:20','2025-06-24 09:46:20',NULL),(54,1,2,8,24,5,19,NULL,NULL,'2025-06-24 12:03:59',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:03:59','2025-06-24 10:03:59',NULL),(55,2,2,8,17,5,12,NULL,NULL,'2025-06-24 12:03:59',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:03:59','2025-06-24 10:03:59',NULL),(56,1,2,8,19,5,24,NULL,NULL,'2025-06-24 12:04:36',NULL,'APPROVISIONNEMENT','2025-06-24 10:04:36','2025-06-24 10:04:36',NULL),(57,2,2,8,12,5,17,NULL,NULL,'2025-06-24 12:04:36',NULL,'APPROVISIONNEMENT','2025-06-24 10:04:36','2025-06-24 10:04:36',NULL),(58,1,2,8,24,5,19,NULL,NULL,'2025-06-24 12:18:17',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:18:17','2025-06-24 10:18:17',NULL),(59,2,2,8,17,5,12,NULL,NULL,'2025-06-24 12:18:17',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:18:17','2025-06-24 10:18:17',NULL),(60,1,2,8,19,5,24,NULL,NULL,'2025-06-24 12:18:17',NULL,'APPROVISIONNEMENT','2025-06-24 10:18:17','2025-06-24 10:18:17',NULL),(61,2,2,8,12,5,17,NULL,NULL,'2025-06-24 12:18:17',NULL,'APPROVISIONNEMENT','2025-06-24 10:18:17','2025-06-24 10:18:17',NULL),(62,1,2,8,24,2,22,NULL,NULL,'2025-06-24 12:18:40',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:18:40','2025-06-24 10:18:40',NULL),(63,1,2,8,22,10,32,NULL,NULL,'2025-06-24 12:18:40',NULL,'APPROVISIONNEMENT','2025-06-24 10:18:40','2025-06-24 10:18:40',NULL),(64,1,2,8,32,5,27,NULL,NULL,'2025-06-24 12:19:07',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:19:07','2025-06-24 10:19:07',NULL),(65,2,2,8,17,5,12,NULL,NULL,'2025-06-24 12:19:07',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:19:07','2025-06-24 10:19:07',NULL),(66,1,2,8,27,5,32,NULL,NULL,'2025-06-24 12:19:07',NULL,'APPROVISIONNEMENT','2025-06-24 10:19:07','2025-06-24 10:19:07',NULL),(67,2,2,8,12,5,17,NULL,NULL,'2025-06-24 12:19:07',NULL,'APPROVISIONNEMENT','2025-06-24 10:19:07','2025-06-24 10:19:07',NULL),(68,1,2,8,32,5,27,NULL,NULL,'2025-06-24 12:19:48',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:19:48','2025-06-24 10:19:48',NULL),(69,2,2,8,17,5,12,NULL,NULL,'2025-06-24 12:19:48',NULL,'ANNULATION APPROVISIONNEMENT','2025-06-24 10:19:48','2025-06-24 10:19:48',NULL),(70,1,2,8,27,5,32,NULL,NULL,'2025-06-24 12:19:48',NULL,'APPROVISIONNEMENT','2025-06-24 10:19:48','2025-06-24 10:19:48',NULL),(71,2,2,8,12,5,17,NULL,NULL,'2025-06-24 12:19:48',NULL,'APPROVISIONNEMENT','2025-06-24 10:19:48','2025-06-24 10:19:48',NULL),(72,5,4,1,0,10,10,'','2025-07-24','2025-07-01 07:46:17',NULL,'INVENTAIRE','2025-07-01 05:46:17','2025-07-01 05:46:17',NULL),(73,3,4,1,5,15,15,'','2025-07-24','2025-07-01 07:46:17',NULL,'INVENTAIRE','2025-07-01 05:46:17','2025-07-01 05:46:17',NULL),(74,2,4,1,20,7,7,'','2025-07-24','2025-07-01 07:46:17',NULL,'INVENTAIRE','2025-07-01 05:46:17','2025-07-01 05:46:17',NULL),(75,5,1,1,0,7,7,NULL,NULL,'2025-07-01 12:38:57',NULL,'APPROVISIONNEMENT','2025-07-01 10:38:57','2025-07-01 10:38:57',NULL),(76,1,2,1,32,5,37,NULL,NULL,'2025-07-01 12:53:17',NULL,'APPROVISIONNEMENT','2025-07-01 10:53:17','2025-07-01 10:53:17',NULL),(77,2,2,1,17,5,22,NULL,NULL,'2025-07-01 12:53:17',NULL,'APPROVISIONNEMENT','2025-07-01 10:53:17','2025-07-01 10:53:17',NULL),(78,1,2,1,37,5,42,NULL,NULL,'2025-07-01 12:58:13',NULL,'APPROVISIONNEMENT','2025-07-01 10:58:13','2025-07-01 10:58:13',NULL),(79,2,2,1,22,5,27,NULL,NULL,'2025-07-01 12:58:13',NULL,'APPROVISIONNEMENT','2025-07-01 10:58:13','2025-07-01 10:58:13',NULL),(80,1,4,1,0,5,5,NULL,'2025-07-01','2025-07-01 13:00:54',NULL,'APPROVISIONNEMENT','2025-07-01 11:00:54','2025-07-01 11:00:54',NULL),(81,2,1,1,2,2,4,NULL,'2025-07-03','2025-07-03 08:42:49',NULL,'APPROVISIONNEMENT','2025-07-03 06:42:49','2025-07-03 06:42:49',NULL),(82,6,1,1,50,2,52,NULL,'2025-07-03','2025-07-03 08:42:49',NULL,'APPROVISIONNEMENT','2025-07-03 06:42:49','2025-07-03 06:42:49',NULL),(83,1,4,1,5,5,0,NULL,NULL,'2025-07-03 10:57:36',NULL,'ANNULATION APPROVISIONNEMENT','2025-07-03 08:57:36','2025-07-03 08:57:36',NULL),(84,1,4,1,0,5,5,NULL,'2025-07-01','2025-07-03 10:57:36',NULL,'APPROVISIONNEMENT','2025-07-03 08:57:36','2025-07-03 08:57:36',NULL),(85,3,1,1,2,2,4,NULL,'2025-07-12','2025-07-04 07:59:17',NULL,'APPROVISIONNEMENT','2025-07-04 05:59:18','2025-07-04 05:59:18',NULL),(86,2,1,1,4,2,6,NULL,'2025-07-12','2025-07-04 07:59:17',NULL,'APPROVISIONNEMENT','2025-07-04 05:59:18','2025-07-04 05:59:18',NULL),(87,1,1,1,35,2,37,NULL,'2025-07-12','2025-07-04 07:59:17',NULL,'APPROVISIONNEMENT','2025-07-04 05:59:18','2025-07-04 05:59:18',NULL),(88,5,1,1,30,2,32,NULL,'2025-07-12','2025-07-04 07:59:17',NULL,'APPROVISIONNEMENT','2025-07-04 05:59:18','2025-07-04 05:59:18',NULL),(89,3,1,1,4,6,10,'AREF-001/07-2025','2025-07-04','2025-07-04 14:18:13',NULL,'MOUVEMENT Entrée','2025-07-04 12:18:13','2025-07-04 12:18:13',NULL),(90,2,1,1,6,14,20,'AREF-001/07-2025','2025-07-04','2025-07-04 14:18:13',NULL,'MOUVEMENT Entrée','2025-07-04 12:18:13','2025-07-04 12:18:13',NULL),(91,6,1,1,52,2,54,'RECEPTION AREF-001/07-2025','2025-07-07','2025-07-07 07:35:44',NULL,'Entrée','2025-07-07 05:35:44','2025-07-07 05:35:44',NULL),(92,6,1,1,54,2,52,'TRANSFERT AREF-001/07-2025','2025-07-07','2025-07-07 07:35:44',NULL,'Sortie','2025-07-07 05:35:44','2025-07-07 05:35:44',NULL),(93,5,1,1,32,2,34,'RECEPTION AREF-001/07-2025','2025-07-07','2025-07-07 07:35:44',NULL,'Entrée','2025-07-07 05:35:44','2025-07-07 05:35:44',NULL),(94,5,1,1,34,2,32,'TRANSFERT AREF-001/07-2025','2025-07-07','2025-07-07 07:35:44',NULL,'Sortie','2025-07-07 05:35:44','2025-07-07 05:35:44',NULL),(95,6,5,1,0,2,2,'RECEPTION AREF-001/07-2025','2025-07-07','2025-07-07 07:39:50',NULL,'Entrée','2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(96,6,1,1,52,2,50,'TRANSFERT AREF-001/07-2025','2025-07-07','2025-07-07 07:39:50',NULL,'Sortie','2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(97,5,5,1,0,2,2,'RECEPTION AREF-001/07-2025','2025-07-07','2025-07-07 07:39:50',NULL,'Entrée','2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(98,5,1,1,32,2,30,'TRANSFERT AREF-001/07-2025','2025-07-07','2025-07-07 07:39:50',NULL,'Sortie','2025-07-07 05:39:50','2025-07-07 05:39:50',NULL),(99,1,5,1,0,7,7,'RECEPTION AREF-002/07-2025','2025-07-07','2025-07-07 07:52:17',NULL,'TRANSFERT (Entrée)','2025-07-07 05:52:18','2025-07-07 05:52:18',NULL),(100,1,1,1,37,7,30,'TRANSFERT AREF-002/07-2025','2025-07-07','2025-07-07 07:52:17',NULL,'TRANSFERT (Sortie)','2025-07-07 05:52:18','2025-07-07 05:52:18',NULL),(101,1,1,1,30,5,25,'AREF-003/07-2025','2025-07-07','2025-07-07 08:35:34',NULL,'MOUVEMENT Sortie','2025-07-07 06:35:34','2025-07-07 06:35:34',NULL),(102,5,1,1,30,5,25,'AREF-003/07-2025','2025-07-07','2025-07-07 08:35:34',NULL,'MOUVEMENT Sortie','2025-07-07 06:35:34','2025-07-07 06:35:34',NULL),(103,1,1,1,25,5,30,'ANNULLATION AREF-003/07-2025','2025-07-07','2025-07-07 08:36:40',NULL,'ANNULATION MOUVEMENT Sortie','2025-07-07 06:36:40','2025-07-07 06:36:40',NULL),(104,5,1,1,25,5,30,'ANNULLATION AREF-003/07-2025','2025-07-07','2025-07-07 08:36:40',NULL,'ANNULATION MOUVEMENT Sortie','2025-07-07 06:36:40','2025-07-07 06:36:40',NULL),(105,1,1,1,30,5,25,'AREF-003/07-2025','2025-07-07','2025-07-07 08:36:40',NULL,'MOUVEMENT Sortie','2025-07-07 06:36:40','2025-07-07 06:36:40',NULL),(106,5,1,1,30,5,25,'AREF-003/07-2025','2025-07-07','2025-07-07 08:36:40',NULL,'MOUVEMENT Sortie','2025-07-07 06:36:40','2025-07-07 06:36:40',NULL),(107,1,5,1,7,5,12,'RECEPTION AREF-002/07-2025','2025-07-07','2025-07-07 08:37:28',NULL,'TRANSFERT (Entrée)','2025-07-07 06:37:28','2025-07-07 06:37:28',NULL),(108,1,1,1,25,5,20,'TRANSFERT AREF-002/07-2025','2025-07-07','2025-07-07 08:37:28',NULL,'TRANSFERT (Sortie)','2025-07-07 06:37:28','2025-07-07 06:37:28',NULL),(109,1,5,1,12,5,7,'ANNULLATION AREF-002/07-2025','2025-07-07','2025-07-07 08:38:40',NULL,'ANNULATION TRANSFERT(Sortie)','2025-07-07 06:38:40','2025-07-07 06:38:40',NULL),(110,1,1,1,20,5,25,'ANNULLATION AREF-002/07-2025','2025-07-07','2025-07-07 08:38:40',NULL,'ANNULATION TRANSFERT(Entrée)','2025-07-07 06:38:40','2025-07-07 06:38:40',NULL),(111,1,5,1,7,5,12,'RECEPTION AREF-002/07-2025','2025-07-07','2025-07-07 08:38:40',NULL,'TRANSFERT (Entrée)','2025-07-07 06:38:40','2025-07-07 06:38:40',NULL),(112,1,1,1,25,5,20,'TRANSFERT AREF-002/07-2025','2025-07-07','2025-07-07 08:38:40',NULL,'TRANSFERT (Sortie)','2025-07-07 06:38:40','2025-07-07 06:38:40',NULL),(113,1,1,1,20,5,25,'ANNULLATION AREF-003/07-2025','2025-07-07','2025-07-07 08:47:38',NULL,'ANNULATION MOUVEMENT Sortie','2025-07-07 06:47:38','2025-07-07 06:47:38',NULL),(114,5,1,1,25,5,30,'ANNULLATION AREF-003/07-2025','2025-07-07','2025-07-07 08:47:38',NULL,'ANNULATION MOUVEMENT Sortie','2025-07-07 06:47:38','2025-07-07 06:47:38',NULL);
/*!40000 ALTER TABLE `article_mouvements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_par_chambre`
--

DROP TABLE IF EXISTS `article_par_chambre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_par_chambre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article` int DEFAULT NULL,
  `id_chambre` int DEFAULT NULL,
  `quantiter` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`),
  KEY `id_chambre` (`id_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_par_chambre`
--

LOCK TABLES `article_par_chambre` WRITE;
/*!40000 ALTER TABLE `article_par_chambre` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_par_chambre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_sortie`
--

DROP TABLE IF EXISTS `article_sortie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_sortie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chambre` int DEFAULT NULL,
  `libeller_sortie` varchar(255) DEFAULT NULL,
  `type_sortie` varchar(255) DEFAULT NULL,
  `id_res_magasin` int DEFAULT NULL,
  `id_res_magasin_cible` int DEFAULT NULL,
  `date_sortie` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `id_users` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_chambre` (`id_chambre`),
  KEY `id_users` (`id_users`),
  KEY `id_res_magasin` (`id_res_magasin`),
  KEY `id_res_magasin_cible` (`id_res_magasin_cible`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_sortie`
--

LOCK TABLES `article_sortie` WRITE;
/*!40000 ALTER TABLE `article_sortie` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_sortie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_sortie_detail`
--

DROP TABLE IF EXISTS `article_sortie_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_sortie_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article_sortie` int DEFAULT NULL,
  `id_article` int DEFAULT NULL,
  `id_res_magasin` int DEFAULT NULL,
  `quantite_sortie` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_article_sortie` (`id_article_sortie`),
  KEY `id_article` (`id_article`),
  KEY `id_res_magasin` (`id_res_magasin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_sortie_detail`
--

LOCK TABLES `article_sortie_detail` WRITE;
/*!40000 ALTER TABLE `article_sortie_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_sortie_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_transaction_details`
--

DROP TABLE IF EXISTS `article_transaction_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_transaction_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned DEFAULT NULL,
  `magasin_id` bigint unsigned DEFAULT NULL,
  `qte_mouvement` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `article_transaction_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_transaction_details`
--

LOCK TABLES `article_transaction_details` WRITE;
/*!40000 ALTER TABLE `article_transaction_details` DISABLE KEYS */;
INSERT INTO `article_transaction_details` VALUES (1,3,1,6,'2025-07-04 12:18:13','2025-07-04 12:18:13',NULL,1),(2,2,1,14,'2025-07-04 12:18:13','2025-07-04 12:18:13',NULL,1),(3,6,1,2,'2025-07-07 05:32:37','2025-07-07 05:32:37',NULL,2),(4,5,1,2,'2025-07-07 05:32:37','2025-07-07 05:32:37',NULL,2),(5,6,1,2,'2025-07-07 05:35:44','2025-07-07 05:35:44',NULL,3),(6,5,1,2,'2025-07-07 05:35:44','2025-07-07 05:35:44',NULL,3),(8,6,1,2,'2025-07-07 05:39:50','2025-07-07 05:39:50',NULL,5),(9,5,1,2,'2025-07-07 05:39:50','2025-07-07 05:39:50',NULL,5),(10,1,1,7,'2025-07-07 05:52:18','2025-07-07 06:37:28','2025-07-07 06:37:28',6),(11,1,1,5,'2025-07-07 06:35:34','2025-07-07 06:36:40','2025-07-07 06:36:40',7),(12,5,1,5,'2025-07-07 06:35:34','2025-07-07 06:36:40','2025-07-07 06:36:40',7),(13,1,1,5,'2025-07-07 06:36:40','2025-07-07 06:47:38','2025-07-07 06:47:38',7),(14,5,1,5,'2025-07-07 06:36:40','2025-07-07 06:47:38','2025-07-07 06:47:38',7),(15,1,1,5,'2025-07-07 06:37:28','2025-07-07 06:38:40','2025-07-07 06:38:40',6),(16,1,1,5,'2025-07-07 06:38:40','2025-07-07 06:38:40',NULL,6);
/*!40000 ALTER TABLE `article_transaction_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_transactions`
--

DROP TABLE IF EXISTS `article_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_transaction` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `magasin_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `type_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `reference_mouvement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_mouvement` int DEFAULT (0),
  `magasin_cible` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_transactions`
--

LOCK TABLES `article_transactions` WRITE;
/*!40000 ALTER TABLE `article_transactions` DISABLE KEYS */;
INSERT INTO `article_transactions` VALUES (1,'2025-07-04','2025-07-04 14:18:13',1,1,4,'Entrée','teeste','2025-07-04 12:18:13','2025-07-04 12:18:13',NULL,'AREF-001/07-2025',1,NULL),(2,'2025-07-07','2025-07-07 07:32:36',1,1,NULL,'Transfert',NULL,'2025-07-07 05:32:37','2025-07-07 05:32:37',NULL,'AREF-001/07-2025',1,5),(3,'2025-07-07','2025-07-07 07:35:44',1,1,NULL,'Transfert',NULL,'2025-07-07 05:35:44','2025-07-07 05:35:44',NULL,'AREF-001/07-2025',1,5),(5,'2025-07-07','2025-07-07 07:39:50',1,1,NULL,'Transfert','dfsfqfqs','2025-07-07 05:39:50','2025-07-07 05:39:50',NULL,'AREF-001/07-2025',1,5),(6,'2025-07-07','2025-07-07 08:38:40',1,1,NULL,'Transfert','dfsfqf','2025-07-07 05:52:17','2025-07-07 06:38:40',NULL,'AREF-002/07-2025',2,5),(7,'2025-07-07','2025-07-07 08:36:40',1,1,5,'Sortie','dfsqfqdfqf','2025-07-07 06:35:34','2025-07-07 06:47:38','2025-07-07 06:47:38','AREF-003/07-2025',3,NULL);
/*!40000 ALTER TABLE `article_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_famille_id` bigint unsigned DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_article` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valeur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `articles_article_famille_id_foreign` (`article_famille_id`),
  CONSTRAINT `articles_article_famille_id_foreign` FOREIGN KEY (`article_famille_id`) REFERENCES `article_familles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (1,1,'MIC-XZY3-295','Pneu radial 295/80 R22.5 Michelin XZY3','Pneu','Michelin','450000','2025-06-18 04:50:20','2025-06-18 04:50:20',NULL),(2,2,'MANN-W940','Filtre à huile Mann W940/25 pour moteur diesel','Consommmable','Manns','75000','2025-06-18 04:51:42','2025-06-18 06:21:16',NULL),(3,3,'ZF-AS-Tronic-16AS2630','Boîte de vitesse automatique ZF 16AS2630 pour camion poids lourd','Pièce majeure','ZFZ','3200000','2025-06-18 06:17:25','2025-06-18 06:17:44',NULL),(4,4,'teste','teste','Pneu','teste','1020000','2025-06-18 06:20:29','2025-06-18 06:21:58','2025-06-18 06:21:58'),(5,5,'xxufycx','pneu','Pièce majeure','marque 1','2500000','2025-07-01 05:44:59','2025-07-01 05:44:59',NULL),(6,2,'mljkj','iofkjsj','Pièce majeure','teste',NULL,'2025-07-01 06:57:59','2025-07-01 06:57:59',NULL);
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avantage`
--

DROP TABLE IF EXISTS `avantage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avantage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `montant_avantage` double NOT NULL DEFAULT '0',
  `libeller_avantage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avantage`
--

LOCK TABLES `avantage` WRITE;
/*!40000 ALTER TABLE `avantage` DISABLE KEYS */;
/*!40000 ALTER TABLE `avantage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blanchisserie`
--

DROP TABLE IF EXISTS `blanchisserie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blanchisserie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_reservation_client` int DEFAULT NULL,
  `date_reception` date DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `statut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `montant_total` double DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `count_blanchisserie` int DEFAULT '0',
  `numero_blanchisserie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `est_traiter` int DEFAULT '0',
  `est_regler` int DEFAULT '0',
  `vente_facture_id` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_reservation_client` (`id_reservation_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blanchisserie`
--

LOCK TABLES `blanchisserie` WRITE;
/*!40000 ALTER TABLE `blanchisserie` DISABLE KEYS */;
/*!40000 ALTER TABLE `blanchisserie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blanchisserie_article`
--

DROP TABLE IF EXISTS `blanchisserie_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blanchisserie_article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_blanchisserie` int DEFAULT NULL,
  `nom_article` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qte_article` double DEFAULT NULL,
  `prix_unitaire` double DEFAULT NULL,
  `montant` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_blanchisserie` (`id_blanchisserie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blanchisserie_article`
--

LOCK TABLES `blanchisserie_article` WRITE;
/*!40000 ALTER TABLE `blanchisserie_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `blanchisserie_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blanchisserie_article_sortie`
--

DROP TABLE IF EXISTS `blanchisserie_article_sortie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blanchisserie_article_sortie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_blanchisserie` int DEFAULT NULL,
  `id_res_magasin` int DEFAULT NULL,
  `id_article` int DEFAULT NULL,
  `qte_sortie` double DEFAULT NULL,
  `id_users` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_blanchisserie` (`id_blanchisserie`),
  KEY `id_res_magasin` (`id_res_magasin`),
  KEY `id_article` (`id_article`),
  KEY `qte_sortie` (`qte_sortie`),
  KEY `id_users` (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blanchisserie_article_sortie`
--

LOCK TABLES `blanchisserie_article_sortie` WRITE;
/*!40000 ALTER TABLE `blanchisserie_article_sortie` DISABLE KEYS */;
/*!40000 ALTER TABLE `blanchisserie_article_sortie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boncommande_fournisseur_details`
--

DROP TABLE IF EXISTS `boncommande_fournisseur_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boncommande_fournisseur_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `boncommande_fournisseur_id` bigint unsigned DEFAULT NULL,
  `article_id` bigint unsigned DEFAULT NULL,
  `qte_commander` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `prix_unitaire` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boncommande_fournisseur_details`
--

LOCK TABLES `boncommande_fournisseur_details` WRITE;
/*!40000 ALTER TABLE `boncommande_fournisseur_details` DISABLE KEYS */;
INSERT INTO `boncommande_fournisseur_details` VALUES (1,1,1,56,'2025-06-27 07:12:33','2025-06-27 10:09:26','2025-06-27 10:09:26',NULL),(2,1,2,10,'2025-06-27 07:12:33','2025-06-27 10:09:26','2025-06-27 10:09:26',NULL),(3,1,1,56,'2025-06-27 10:09:26','2025-06-27 10:09:38','2025-06-27 10:09:38',NULL),(4,1,2,10,'2025-06-27 10:09:26','2025-06-27 10:09:38','2025-06-27 10:09:38',NULL),(5,1,1,56,'2025-06-27 10:09:39','2025-06-27 10:10:07','2025-06-27 10:10:07',NULL),(6,1,2,510,'2025-06-27 10:09:39','2025-06-27 10:10:07','2025-06-27 10:10:07',NULL),(7,1,1,3,'2025-06-27 10:10:07','2025-06-27 10:15:31','2025-06-27 10:15:31',NULL),(8,1,2,5,'2025-06-27 10:10:07','2025-06-27 10:15:31','2025-06-27 10:15:31',NULL),(9,2,1,5,'2025-06-27 10:51:43','2025-06-27 10:53:05','2025-06-27 10:53:05',NULL),(10,2,1,4,'2025-06-27 10:53:05','2025-06-27 10:53:05',NULL,NULL),(11,3,1,1,'2025-06-27 10:53:33','2025-07-14 04:27:53','2025-07-14 04:27:53',NULL),(12,3,2,2,'2025-06-27 10:53:33','2025-07-14 04:27:53','2025-07-14 04:27:53',NULL),(13,3,3,3,'2025-06-27 10:53:33','2025-07-14 04:27:53','2025-07-14 04:27:53',NULL),(14,4,1,5,'2025-06-27 11:14:29','2025-07-02 09:29:47','2025-07-02 09:29:47',NULL),(15,4,2,2,'2025-06-27 11:14:29','2025-07-02 09:29:47','2025-07-02 09:29:47',NULL),(16,4,3,2,'2025-06-27 11:14:29','2025-07-02 09:29:47','2025-07-02 09:29:47',NULL),(17,5,1,4,'2025-07-01 05:54:58','2025-07-02 09:29:38','2025-07-02 09:29:38',NULL),(18,5,2,4,'2025-07-01 05:54:58','2025-07-02 09:29:38','2025-07-02 09:29:38',NULL),(19,5,1,4,'2025-07-02 09:29:39','2025-07-07 10:47:22','2025-07-07 10:47:22',NULL),(20,5,2,4,'2025-07-02 09:29:39','2025-07-07 10:47:22','2025-07-07 10:47:22',NULL),(21,4,1,5,'2025-07-02 09:29:47','2025-07-02 09:29:47',NULL,NULL),(22,4,2,2,'2025-07-02 09:29:47','2025-07-02 09:29:47',NULL,NULL),(23,4,3,2,'2025-07-02 09:29:47','2025-07-02 09:29:47',NULL,NULL),(24,6,3,4,'2025-07-03 06:03:32','2025-07-03 06:03:32',NULL,3200000),(25,6,2,5,'2025-07-03 06:03:32','2025-07-03 06:03:32',NULL,75000),(26,7,3,10,'2025-07-03 06:04:18','2025-07-03 06:04:18',NULL,0),(27,7,2,1,'2025-07-03 06:04:18','2025-07-03 06:04:18',NULL,0),(28,7,6,1,'2025-07-03 06:04:18','2025-07-03 06:04:18',NULL,0),(29,8,3,5,'2025-07-03 06:04:43','2025-07-03 06:04:43',NULL,3200000),(30,8,2,5,'2025-07-03 06:04:43','2025-07-03 06:04:43',NULL,75000),(31,9,3,7,'2025-07-03 06:05:02','2025-07-07 10:19:59','2025-07-07 10:19:59',0),(32,9,2,7,'2025-07-03 06:05:02','2025-07-07 10:19:59','2025-07-07 10:19:59',0),(33,10,2,2,'2025-07-03 06:11:42','2025-07-03 06:11:42',NULL,75000),(34,10,6,2,'2025-07-03 06:11:42','2025-07-03 06:11:42',NULL,NULL),(35,9,3,7,'2025-07-07 10:19:59','2025-07-07 10:19:59',NULL,NULL),(36,9,2,7,'2025-07-07 10:19:59','2025-07-07 10:19:59',NULL,NULL),(37,5,1,4,'2025-07-07 10:47:22','2025-07-07 10:47:22',NULL,NULL),(38,5,2,4,'2025-07-07 10:47:22','2025-07-07 10:47:22',NULL,NULL),(39,3,1,1,'2025-07-14 04:27:53','2025-07-14 04:27:53',NULL,NULL),(40,3,2,2,'2025-07-14 04:27:53','2025-07-14 04:27:53',NULL,NULL),(41,3,3,3,'2025-07-14 04:27:53','2025-07-14 04:27:53',NULL,NULL);
/*!40000 ALTER TABLE `boncommande_fournisseur_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boncommande_fournisseurs`
--

DROP TABLE IF EXISTS `boncommande_fournisseurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boncommande_fournisseurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_boncommande` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `numero_bon_commande` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_numero_commande` int DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `fournisseur_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `show_prix_unitaire` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boncommande_fournisseurs`
--

LOCK TABLES `boncommande_fournisseurs` WRITE;
/*!40000 ALTER TABLE `boncommande_fournisseurs` DISABLE KEYS */;
INSERT INTO `boncommande_fournisseurs` VALUES (1,'2025-06-27','2025-06-27 09:12:33','BCF-001/06-2025',1,8,1,'2025-06-27 07:12:33','2025-06-27 10:15:31','2025-06-27 10:15:31',0),(2,'2025-06-27','2025-06-27 12:53:05','BCF-001/06-2025',1,8,1,'2025-06-27 10:51:43','2025-06-27 10:53:05',NULL,0),(3,'2025-06-27','2025-07-14 07:27:53','BCF-002/06-2025',2,1,1,'2025-06-27 10:53:33','2025-07-14 04:27:53',NULL,0),(4,'2025-06-30','2025-07-02 11:29:47','BCF-003/06-2025',3,1,8,'2025-06-27 11:14:29','2025-07-02 09:29:47',NULL,0),(5,'2025-07-01','2025-07-07 12:47:22','BCF-001/07-2025',1,1,9,'2025-07-01 05:54:58','2025-07-07 10:47:22',NULL,0),(6,'2025-07-03','2025-07-03 08:03:32','BCF-002/07-2025',2,1,7,'2025-07-03 06:03:32','2025-07-03 06:03:32',NULL,0),(7,'2025-07-03','2025-07-03 08:04:18','BCF-003/07-2025',3,1,6,'2025-07-03 06:04:18','2025-07-03 06:04:18',NULL,0),(8,'2025-07-03','2025-07-03 08:04:43','BCF-004/07-2025',4,1,3,'2025-07-03 06:04:43','2025-07-03 06:04:43',NULL,1),(9,'2025-07-03','2025-07-07 12:19:59','BCF-005/07-2025',5,1,9,'2025-07-03 06:05:02','2025-07-07 10:19:59',NULL,0),(10,'2025-07-04','2025-07-03 08:11:42','BCF-006/07-2025',6,1,1,'2025-07-03 06:11:42','2025-07-03 06:11:42',NULL,0);
/*!40000 ALTER TABLE `boncommande_fournisseurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('contact@dna.mg|127.0.0.1','i:2;',1777988892),('contact@dna.mg|127.0.0.1:timer','i:1777988892;',1777988892),('mbola@gmail.com|127.0.0.1','i:1;',1756281303),('mbola@gmail.com|127.0.0.1:timer','i:1756281303;',1756281303);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carburant_cards`
--

DROP TABLE IF EXISTS `carburant_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carburant_cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_carte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `chauffeur_id` bigint unsigned DEFAULT NULL,
  `plafond_mensuel` decimal(10,2) NOT NULL,
  `consommation_courante` decimal(10,2) NOT NULL DEFAULT '0.00',
  `station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `solde` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `carburant_cards_numero_carte_unique` (`numero_carte`),
  KEY `carburant_cards_vehicule_id_foreign` (`vehicule_id`),
  KEY `carburant_cards_chauffeur_id_foreign` (`chauffeur_id`),
  CONSTRAINT `carburant_cards_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`),
  CONSTRAINT `carburant_cards_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carburant_cards`
--

LOCK TABLES `carburant_cards` WRITE;
/*!40000 ALTER TABLE `carburant_cards` DISABLE KEYS */;
INSERT INTO `carburant_cards` VALUES (1,'1000222d',5,1,1500000.00,0.00,'galana',0,NULL,'2025-06-19 04:26:16','2025-06-27 12:03:32',0),(2,'CX151-245D',NULL,NULL,100000.00,0.00,NULL,1,'2025-06-27 12:03:56','2025-06-27 12:02:18','2025-06-27 12:03:56',0),(3,'00002',NULL,NULL,100000.00,0.00,NULL,1,NULL,'2025-07-01 06:00:38','2025-07-01 06:10:06',1900);
/*!40000 ALTER TABLE `carburant_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carburant_mouvements`
--

DROP TABLE IF EXISTS `carburant_mouvements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carburant_mouvements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carburant_card_id` bigint unsigned NOT NULL,
  `type` enum('achat_carte','achat_espece','recharge','ajustement','annulation_transaction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_mvmt` date NOT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `chauffeur_id` bigint unsigned DEFAULT NULL,
  `quantite_mvmt` decimal(8,2) DEFAULT NULL,
  `montant_initiale` double DEFAULT NULL,
  `montant` double DEFAULT NULL,
  `piece_jointe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `montant_finale` double DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `montant_mvmt` double DEFAULT NULL,
  `reference_mvmt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carburant_mouvements_vehicule_id_foreign` (`vehicule_id`),
  KEY `carburant_mouvements_chauffeur_id_foreign` (`chauffeur_id`),
  KEY `carburant_mouvements_type_index` (`type`),
  KEY `carburant_mouvements_date_mvmt_index` (`date_mvmt`),
  KEY `carburant_mouvements_user_id_foreign` (`user_id`),
  CONSTRAINT `carburant_mouvements_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `carburant_mouvements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `carburant_mouvements_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carburant_mouvements`
--

LOCK TABLES `carburant_mouvements` WRITE;
/*!40000 ALTER TABLE `carburant_mouvements` DISABLE KEYS */;
INSERT INTO `carburant_mouvements` VALUES (1,3,'recharge','2025-07-01',NULL,NULL,NULL,NULL,2000,NULL,NULL,'2025-07-01 06:01:47','2025-07-01 06:01:47',2000,1,NULL,NULL,NULL,NULL),(2,3,'recharge','2025-07-01',NULL,NULL,NULL,0,2000,NULL,NULL,'2025-07-01 06:03:01','2025-07-01 06:03:01',2000,1,NULL,'2025-07-01 08:03:01',NULL,NULL),(3,3,'achat_carte','2025-07-01',5,4,NULL,2000,100,'[\"img\\/transaction\\/documents\\/achat_carte674470.webp\"]',NULL,'2025-07-01 06:10:06','2025-07-01 06:10:06',1900,1,NULL,'2025-07-01 08:10:06',NULL,NULL);
/*!40000 ALTER TABLE `carburant_mouvements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carburant_transactions`
--

DROP TABLE IF EXISTS `carburant_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carburant_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carburant_card_id` bigint unsigned DEFAULT NULL,
  `type` enum('achat_carte','achat_espece') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_transaction` date NOT NULL,
  `date_heure_enregistrement` datetime NOT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `chauffeur_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `montant` double DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `piece_jointe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `voyage_id` bigint unsigned DEFAULT NULL,
  `carburant_litre` decimal(8,2) DEFAULT NULL,
  `prix_unitaire` double DEFAULT NULL,
  `reservation_detail_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carburant_transactions_vehicule_id_foreign` (`vehicule_id`),
  KEY `carburant_transactions_chauffeur_id_foreign` (`chauffeur_id`),
  KEY `carburant_transactions_user_id_foreign` (`user_id`),
  KEY `carburant_transactions_type_index` (`type`),
  KEY `carburant_transactions_date_transaction_index` (`date_transaction`),
  KEY `carburant_transactions_reservation_detail_id_foreign` (`reservation_detail_id`),
  CONSTRAINT `carburant_transactions_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `carburant_transactions_reservation_detail_id_foreign` FOREIGN KEY (`reservation_detail_id`) REFERENCES `reservation_details` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carburant_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `carburant_transactions_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carburant_transactions`
--

LOCK TABLES `carburant_transactions` WRITE;
/*!40000 ALTER TABLE `carburant_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `carburant_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `card_ajustements`
--

DROP TABLE IF EXISTS `card_ajustements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `card_ajustements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carburant_mouvement_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci,
  `montant` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `card_ajustements_carburant_mouvement_id_foreign` (`carburant_mouvement_id`),
  KEY `card_ajustements_user_id_foreign` (`user_id`),
  CONSTRAINT `card_ajustements_carburant_mouvement_id_foreign` FOREIGN KEY (`carburant_mouvement_id`) REFERENCES `carburant_mouvements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `card_ajustements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card_ajustements`
--

LOCK TABLES `card_ajustements` WRITE;
/*!40000 ALTER TABLE `card_ajustements` DISABLE KEYS */;
INSERT INTO `card_ajustements` VALUES (1,2,1,'fde',2000,'2025-07-01 06:03:01','2025-07-01 06:03:01','2025-07-01');
/*!40000 ALTER TABLE `card_ajustements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `card_transactions`
--

DROP TABLE IF EXISTS `card_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `card_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quantite_carbu` decimal(10,2) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carburant_card_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `card_transactions_carburant_card_id_foreign` (`carburant_card_id`),
  CONSTRAINT `card_transactions_carburant_card_id_foreign` FOREIGN KEY (`carburant_card_id`) REFERENCES `carburant_cards` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card_transactions`
--

LOCK TABLES `card_transactions` WRITE;
/*!40000 ALTER TABLE `card_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `card_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chambre`
--

DROP TABLE IF EXISTS `chambre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chambre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_type_chambre` int DEFAULT '0',
  `numero_chambre` varchar(255) DEFAULT NULL,
  `disponibiliter` varchar(255) DEFAULT NULL,
  `caracteristique_chambre` text,
  `prix_chambre` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_type_chambre` (`id_type_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chambre`
--

LOCK TABLES `chambre` WRITE;
/*!40000 ALTER TABLE `chambre` DISABLE KEYS */;
/*!40000 ALTER TABLE `chambre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chambre_photos`
--

DROP TABLE IF EXISTS `chambre_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chambre_photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chambre` int NOT NULL,
  `image_photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_chambre` (`id_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chambre_photos`
--

LOCK TABLES `chambre_photos` WRITE;
/*!40000 ALTER TABLE `chambre_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `chambre_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chambre_tarif`
--

DROP TABLE IF EXISTS `chambre_tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chambre_tarif` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_tarif` varchar(255) DEFAULT NULL,
  `id_chambre` int DEFAULT '0',
  `montant_tarif` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_chambre` (`id_chambre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chambre_tarif`
--

LOCK TABLES `chambre_tarif` WRITE;
/*!40000 ALTER TABLE `chambre_tarif` DISABLE KEYS */;
/*!40000 ALTER TABLE `chambre_tarif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chambre_tarif_detail`
--

DROP TABLE IF EXISTS `chambre_tarif_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chambre_tarif_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libeller_chambre_tarif` varchar(255) DEFAULT NULL,
  `prix_chambre_tarif` double DEFAULT NULL,
  `id_chambre_tarif` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_chambre_tarif` (`id_chambre_tarif`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chambre_tarif_detail`
--

LOCK TABLES `chambre_tarif_detail` WRITE;
/*!40000 ALTER TABLE `chambre_tarif_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `chambre_tarif_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chauffeur_vehicule`
--

DROP TABLE IF EXISTS `chauffeur_vehicule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chauffeur_vehicule` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chauffeur_id` bigint unsigned NOT NULL,
  `vehicule_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chauffeur_vehicule_chauffeur_id_foreign` (`chauffeur_id`),
  KEY `chauffeur_vehicule_vehicule_id_foreign` (`vehicule_id`),
  CONSTRAINT `chauffeur_vehicule_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chauffeur_vehicule_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chauffeur_vehicule`
--

LOCK TABLES `chauffeur_vehicule` WRITE;
/*!40000 ALTER TABLE `chauffeur_vehicule` DISABLE KEYS */;
INSERT INTO `chauffeur_vehicule` VALUES (1,4,4,'2025-05-28 03:10:01','2025-05-28 03:10:01'),(2,4,5,'2025-05-28 03:10:01','2025-05-28 03:10:01'),(3,6,4,'2025-06-07 02:37:27','2025-06-07 02:37:27'),(5,7,5,'2025-06-10 04:56:09','2025-06-10 04:56:09'),(6,8,4,'2025-06-10 06:19:37','2025-06-10 06:19:37'),(7,9,5,'2025-07-01 05:32:01','2025-07-01 05:32:01'),(8,9,9,'2025-07-01 05:32:01','2025-07-01 05:32:01');
/*!40000 ALTER TABLE `chauffeur_vehicule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chauffeurs`
--

DROP TABLE IF EXISTS `chauffeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chauffeurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `matricule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `CIN` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chauffeurs_matricule_unique` (`matricule`),
  UNIQUE KEY `chauffeurs_cin_unique` (`CIN`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chauffeurs`
--

LOCK TABLES `chauffeurs` WRITE;
/*!40000 ALTER TABLE `chauffeurs` DISABLE KEYS */;
INSERT INTO `chauffeurs` VALUES (1,'2025-05-26 08:11:36','2025-05-28 03:10:29','1251','img/chauffeur/chauffeur_1.webp','img/chauffeur/chauffeur_1_thumb.webp','fsqdf','fqdfsqdf','2025-05-08','dfqs','dd','Mangarano',NULL),(4,'2025-05-28 03:09:54','2025-05-28 03:10:01','0001','img/chauffeur/chauffeur_4.webp','img/chauffeur/chauffeur_4_thumb.webp','ratsitobaina','mbolatiana','2025-05-28','dfq','0341384290','lot 12 p/lle 13-41-12 Ambolomadinika',NULL),(5,'2025-05-28 03:11:25','2025-05-28 03:11:26','00025','img/chauffeur/chauffeur_5.webp','img/chauffeur/chauffeur_5_thumb.webp','koto','tsiry','2025-05-28','xxfdfsf','dfqfqdfqs','dfqfsq',NULL),(6,'2025-06-07 02:37:21','2025-06-07 02:37:27','xxxx1','img/chauffeur/chauffeur_6_20250607043721hq8Fs36.webp','img/chauffeur/chauffeur_6_20250607043721hq8Fs36_thumb.webp','ratsitobain','mbolatiaan','2025-06-07','xxxx','xxxxx','xxx',NULL),(7,'2025-06-10 04:56:06','2025-06-10 04:56:40','00001xddsd','img/chauffeur/chauffeur_7_202506100656408xxcB2193.webp','img/chauffeur/chauffeur_7_202506100656408xxcB2193_thumb.webp','Andriantsiry Mbolatianaxx','RATSITOxxBAINA','2025-06-25','0114412xxx45747','xxx','lot 12 p/lle 13-41-12 Ambolomadinikagfgfg',NULL),(8,'2025-06-10 06:19:37','2025-06-10 06:20:19','COD00014XXdfsdfsd','img/chauffeur/chauffeur_8_20250610082018ttzVJ2301.webp','img/chauffeur/chauffeur_8_20250610082018ttzVJ2301_thumb.webp','Tsiryfsdfsdfd','Niainadfsdf','2025-06-10','xx11454xxfdsdfd','01355565df','AMPASIMAZAVAfqdfqsdf',NULL),(9,'2025-07-01 05:32:01','2025-07-01 05:32:01','MAT0001',NULL,NULL,'Dieu','donné','2025-07-01','xxfd2323','032545421551','Ambolomadinika',NULL);
/*!40000 ALTER TABLE `chauffeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_client` varchar(255) DEFAULT NULL,
  `validation_visa` date DEFAULT NULL,
  `nom_prenom_client` varchar(255) DEFAULT NULL,
  `sexe_client` varchar(255) DEFAULT NULL,
  `adresse_client` varchar(255) DEFAULT NULL,
  `contact_client` varchar(255) DEFAULT NULL,
  `mail_client` varchar(255) DEFAULT NULL,
  `photos_client` varchar(255) DEFAULT NULL,
  `prenom_client` varchar(255) DEFAULT NULL,
  `cin_client` varchar(255) DEFAULT NULL,
  `piece_identite` varchar(255) DEFAULT NULL,
  `nationalite_client` varchar(255) DEFAULT NULL,
  `age_client` int DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(255) DEFAULT '',
  `nom_pere` varchar(255) DEFAULT '',
  `nom_mere` varchar(255) DEFAULT '',
  `profession` varchar(255) DEFAULT '',
  `date_insertion` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1,'AU COMPTANT',NULL,'AU COMPTANT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','','','',NULL);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_pj`
--

DROP TABLE IF EXISTS `client_pj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_pj` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL DEFAULT '0',
  `libeller_pj` varchar(255) DEFAULT NULL,
  `link_pj` varchar(255) DEFAULT NULL,
  `pj` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_pj`
--

LOCK TABLES `client_pj` WRITE;
/*!40000 ALTER TABLE `client_pj` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_pj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nif_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcs_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_numero_unique` (`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'xxxx-xxxx012','Ratsitobaina Mbolatiana','Mangarano','mbolatianaratsitobaina1712@gmail.com',NULL,NULL,NULL,NULL,NULL,'2025-08-04 05:31:05','2025-08-04 05:43:47'),(2,NULL,'tsiry',NULL,'tsiriniainartn@gmail.com',NULL,NULL,NULL,NULL,NULL,'2025-08-04 05:31:32','2025-08-04 05:31:32'),(3,NULL,'teste',NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-04 05:43:14','2025-08-04 05:43:10','2025-08-04 05:43:14'),(4,NULL,'teste',NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-04 05:43:24','2025-08-04 05:43:19','2025-08-04 05:43:24');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotisation_sociale`
--

DROP TABLE IF EXISTS `cotisation_sociale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotisation_sociale` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libeller_cotisation_sociale` varchar(255) DEFAULT NULL,
  `taux_cotisation_sociale` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotisation_sociale`
--

LOCK TABLES `cotisation_sociale` WRITE;
/*!40000 ALTER TABLE `cotisation_sociale` DISABLE KEYS */;
/*!40000 ALTER TABLE `cotisation_sociale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devis_client_details`
--

DROP TABLE IF EXISTS `devis_client_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devis_client_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `devis_client_id` bigint unsigned DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantite` double DEFAULT NULL,
  `prix_unitaire` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `montant` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `devis_client_details_devis_client_id_foreign` (`devis_client_id`),
  CONSTRAINT `devis_client_details_devis_client_id_foreign` FOREIGN KEY (`devis_client_id`) REFERENCES `devis_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devis_client_details`
--

LOCK TABLES `devis_client_details` WRITE;
/*!40000 ALTER TABLE `devis_client_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `devis_client_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devis_clients`
--

DROP TABLE IF EXISTS `devis_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devis_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `date_devis` date DEFAULT NULL,
  `validite_devis` date DEFAULT NULL,
  `condition_delais` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `montant_total` double NOT NULL DEFAULT '0',
  `numero_devis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_devis` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `devis_clients_user_id_foreign` (`user_id`),
  KEY `devis_clients_client_id_foreign` (`client_id`),
  CONSTRAINT `devis_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `devis_clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devis_clients`
--

LOCK TABLES `devis_clients` WRITE;
/*!40000 ALTER TABLE `devis_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `devis_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents_chauffeurs`
--

DROP TABLE IF EXISTS `documents_chauffeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents_chauffeurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chauffeur_id` bigint unsigned NOT NULL,
  `nom_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `fichier_jointe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_chauffeurs_chauffeur_id_foreign` (`chauffeur_id`),
  CONSTRAINT `documents_chauffeurs_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents_chauffeurs`
--

LOCK TABLES `documents_chauffeurs` WRITE;
/*!40000 ALTER TABLE `documents_chauffeurs` DISABLE KEYS */;
INSERT INTO `documents_chauffeurs` VALUES (1,1,'dfsd','2025-06-18','[\"img\\/chauffeur\\/documents\\/1_327411.png\",\"img\\/chauffeur\\/documents\\/1_106656.jpg\",\"img\\/chauffeur\\/documents\\/1_773209.jpg\",\"img\\/chauffeur\\/documents\\/1_645114.jpg\"]','2025-06-12 10:15:18','2025-06-12 10:15:18',NULL),(2,9,'dddd','2025-07-09','[\"img\\/chauffeur\\/documents\\/9_274027.jpeg\"]','2025-07-01 05:35:09','2025-07-01 05:36:05',NULL);
/*!40000 ALTER TABLE `documents_chauffeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (17,'demos.transmada.localhost','demos','2025-08-11 04:38:53','2025-08-11 04:38:53'),(23,'domaine_teste.transmada.localhost','domaine_teste','2025-08-27 10:03:10','2025-08-27 10:03:10'),(24,'everest_trans.transmada.localhost','everest_trans','2025-08-27 10:21:30','2025-08-27 10:21:30'),(25,'aina_trans.transmada.localhost','Aina_trans','2025-08-27 10:26:40','2025-08-27 10:26:40');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emplois_du_temp_travail_par_semaine`
--

DROP TABLE IF EXISTS `emplois_du_temp_travail_par_semaine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emplois_du_temp_travail_par_semaine` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libeller` varchar(255) DEFAULT NULL,
  `date_emploi_du_temp` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `id_users` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emplois_du_temp_travail_par_semaine`
--

LOCK TABLES `emplois_du_temp_travail_par_semaine` WRITE;
/*!40000 ALTER TABLE `emplois_du_temp_travail_par_semaine` DISABLE KEYS */;
/*!40000 ALTER TABLE `emplois_du_temp_travail_par_semaine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facture_client_reglements`
--

DROP TABLE IF EXISTS `facture_client_reglements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facture_client_reglements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_reglement` date DEFAULT NULL,
  `facture_client_id` bigint unsigned DEFAULT NULL,
  `tresorerie_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `montant_reglement` double NOT NULL DEFAULT '0',
  `mode_reglement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facture_client_reglements_facture_client_id_foreign` (`facture_client_id`),
  KEY `facture_client_reglements_tresorerie_id_foreign` (`tresorerie_id`),
  KEY `facture_client_reglements_user_id_foreign` (`user_id`),
  CONSTRAINT `facture_client_reglements_facture_client_id_foreign` FOREIGN KEY (`facture_client_id`) REFERENCES `facture_clients` (`id`),
  CONSTRAINT `facture_client_reglements_tresorerie_id_foreign` FOREIGN KEY (`tresorerie_id`) REFERENCES `tresoreries` (`id`),
  CONSTRAINT `facture_client_reglements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facture_client_reglements`
--

LOCK TABLES `facture_client_reglements` WRITE;
/*!40000 ALTER TABLE `facture_client_reglements` DISABLE KEYS */;
/*!40000 ALTER TABLE `facture_client_reglements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facture_clients`
--

DROP TABLE IF EXISTS `facture_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facture_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_facture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_facture` int DEFAULT NULL,
  `date_facture` date DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `montant_ht` double DEFAULT NULL,
  `montant_tva` double DEFAULT NULL,
  `montant_ttc` double DEFAULT NULL,
  `mode_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut_facture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voyage_ids` json DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `remise` double DEFAULT NULL,
  `montant_remise` double DEFAULT NULL,
  `montant_payer` double DEFAULT NULL,
  `montant_reste_a_payer` double DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `taux_tva` double DEFAULT '0',
  `montant_voyage` double DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `facture_clients_numero_facture_unique` (`numero_facture`),
  KEY `facture_clients_client_id_foreign` (`client_id`),
  KEY `facture_clients_user_id_foreign` (`user_id`),
  CONSTRAINT `facture_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `facture_clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facture_clients`
--

LOCK TABLES `facture_clients` WRITE;
/*!40000 ALTER TABLE `facture_clients` DISABLE KEYS */;
INSERT INTO `facture_clients` VALUES (35,'FACT-001/08-2025',1,'2025-08-06',NULL,4000000,800000,4800000,NULL,'Brouillon','[\"18\"]',1,NULL,300000,NULL,4800000,1,'2025-08-06 09:36:13','2025-08-06 09:36:13',NULL,20,4300000),(37,'FACT-002/08-2025',2,'2025-08-06',NULL,6775000,1355000,8130000,NULL,'Brouillon','[\"19\"]',1,NULL,0,NULL,8130000,1,'2025-08-06 09:54:31','2025-08-06 09:54:31',NULL,20,6775000),(38,'FACT-003/08-2025',3,'2025-08-06',NULL,41000000,8200000,49200000,NULL,'Brouillon','[\"20\", \"21\", \"22\"]',1,NULL,255000,NULL,49200000,1,'2025-08-06 10:10:49','2025-08-06 10:10:49',NULL,20,41255000);
/*!40000 ALTER TABLE `facture_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facture_proforma`
--

DROP TABLE IF EXISTS `facture_proforma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facture_proforma` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_facture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `count_facture` int DEFAULT '0',
  `date_facture` date DEFAULT NULL,
  `date_heure_enregistrement` datetime DEFAULT NULL,
  `id_users` int DEFAULT NULL,
  `id_client` int DEFAULT NULL,
  `total_montant` double DEFAULT '0',
  `total_montant_ht` double DEFAULT '0',
  `montant_tva` double DEFAULT '0',
  `montant_remise_general` double DEFAULT '0',
  `montant_ht` double DEFAULT '0',
  `tva` double DEFAULT '0',
  `total_montant_ttc` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_users` (`id_users`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facture_proforma`
--

LOCK TABLES `facture_proforma` WRITE;
/*!40000 ALTER TABLE `facture_proforma` DISABLE KEYS */;
/*!40000 ALTER TABLE `facture_proforma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facture_proforma_detail`
--

DROP TABLE IF EXISTS `facture_proforma_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facture_proforma_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_facture_proforma` int DEFAULT NULL,
  `designation_produit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_res_produit` int DEFAULT NULL,
  `id_res_magasin` int DEFAULT NULL,
  `qte` double DEFAULT NULL,
  `pu` double DEFAULT NULL,
  `montant` double DEFAULT NULL,
  `montant_remise` double DEFAULT NULL,
  `type_produit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_facture_proforma` (`id_facture_proforma`),
  KEY `id_article` (`id_res_produit`) USING BTREE,
  KEY `id_res_magasin` (`id_res_magasin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facture_proforma_detail`
--

LOCK TABLES `facture_proforma_detail` WRITE;
/*!40000 ALTER TABLE `facture_proforma_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `facture_proforma_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `famille_article`
--

DROP TABLE IF EXISTS `famille_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `famille_article` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_famille_article` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `famille_article`
--

LOCK TABLES `famille_article` WRITE;
/*!40000 ALTER TABLE `famille_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `famille_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonction`
--

DROP TABLE IF EXISTS `fonction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_fonction` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonction`
--

LOCK TABLES `fonction` WRITE;
/*!40000 ALTER TABLE `fonction` DISABLE KEYS */;
/*!40000 ALTER TABLE `fonction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupe_utilisateurs`
--

DROP TABLE IF EXISTS `groupe_utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groupe_utilisateurs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupe_utilisateurs`
--

LOCK TABLES `groupe_utilisateurs` WRITE;
/*!40000 ALTER TABLE `groupe_utilisateurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `groupe_utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `immobilisations`
--

DROP TABLE IF EXISTS `immobilisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `immobilisations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Libellé de l''immobilisation',
  `valeur` double NOT NULL COMMENT 'Valeur de l''immobilisation',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `immobilisations`
--

LOCK TABLES `immobilisations` WRITE;
/*!40000 ALTER TABLE `immobilisations` DISABLE KEYS */;
INSERT INTO `immobilisations` VALUES (1,'immobilisation',155000,'2025-07-21 09:22:45','2025-07-21 09:38:10',NULL);
/*!40000 ALTER TABLE `immobilisations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info_societes`
--

DROP TABLE IF EXISTS `info_societes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `info_societes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nif_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stat_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcs_societe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_societes`
--

LOCK TABLES `info_societes` WRITE;
/*!40000 ALTER TABLE `info_societes` DISABLE KEYS */;
INSERT INTO `info_societes` VALUES (1,'TransMada','d','0123456789','societe@gmail.com','123456789','s','123456789123','xxxx xxxxx','2025-07-25 04:19:01','2025-07-25 04:54:13',NULL);
/*!40000 ALTER TABLE `info_societes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"e96e3948-c928-4480-bf5f-2d7a01fef79a\",\"displayName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"command\":\"O:32:\\\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\\\":1:{s:9:\\\"\\u0000*\\u0000tenant\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:17:\\\"App\\\\Models\\\\Tenant\\\";s:2:\\\"id\\\";s:5:\\\"tsiry\\\";s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"}}',0,NULL,1754660481,1754660481),(2,'default','{\"uuid\":\"e50788de-dc21-4072-ab4f-ec521c65f200\",\"displayName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"command\":\"O:32:\\\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\\\":1:{s:9:\\\"\\u0000*\\u0000tenant\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:17:\\\"App\\\\Models\\\\Tenant\\\";s:2:\\\"id\\\";s:4:\\\"djas\\\";s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"}}',0,NULL,1754661037,1754661037),(3,'default','{\"uuid\":\"fee26af5-daa0-4649-89e2-488eb0f12a1b\",\"displayName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\",\"command\":\"O:32:\\\"Stancl\\\\Tenancy\\\\Jobs\\\\SeedDatabase\\\":1:{s:9:\\\"\\u0000*\\u0000tenant\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:17:\\\"App\\\\Models\\\\Tenant\\\";s:2:\\\"id\\\";s:3:\\\"dna\\\";s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\"}}',0,NULL,1754661198,1754661198);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libelle_maintenances`
--

DROP TABLE IF EXISTS `libelle_maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libelle_maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification` int NOT NULL DEFAULT '0',
  `background` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libelle_maintenances`
--

LOCK TABLES `libelle_maintenances` WRITE;
/*!40000 ALTER TABLE `libelle_maintenances` DISABLE KEYS */;
INSERT INTO `libelle_maintenances` VALUES (1,'Assurance',7,'#48b5f9','#000000','2025-05-28 10:02:35','2025-06-11 08:22:45',NULL),(2,'teste',5,'#42c21e','#ffffff','2025-05-28 10:19:49','2025-06-11 08:22:21',NULL),(3,'vinette',5,'#2c72e2','#f5f3f7','2025-05-29 09:54:25','2025-06-11 08:21:59',NULL),(11,'dfqfqsf',3,'#d34a4a','#000000','2025-05-30 10:06:32','2025-06-11 08:21:40',NULL),(12,'dfdsfssfsdf',10,'#fa0000','#781717','2025-06-10 04:00:54','2025-06-11 08:17:35',NULL);
/*!40000 ALTER TABLE `libelle_maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `magasins`
--

DROP TABLE IF EXISTS `magasins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `magasins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_magasin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `magasins`
--

LOCK TABLES `magasins` WRITE;
/*!40000 ALTER TABLE `magasins` DISABLE KEYS */;
INSERT INTO `magasins` VALUES (1,'magasin de stockage',NULL,'2025-06-17 09:51:26','2025-06-17 09:56:16'),(2,'magasin stockage des Pneu',NULL,'2025-06-17 09:55:04','2025-06-17 09:56:37'),(3,'teste magasin','2025-06-17 09:58:13','2025-06-17 09:58:08','2025-06-17 09:58:13'),(4,'teste magasin',NULL,'2025-06-17 10:02:45','2025-06-17 10:02:45'),(5,'teste magasin 1s',NULL,'2025-06-17 10:08:01','2025-06-17 10:25:24');
/*!40000 ALTER TABLE `magasins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marchandises`
--

DROP TABLE IF EXISTS `marchandises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marchandises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marchandises`
--

LOCK TABLES `marchandises` WRITE;
/*!40000 ALTER TABLE `marchandises` DISABLE KEYS */;
/*!40000 ALTER TABLE `marchandises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_12_09_062516_create_permission_tables',1),(5,'2025_01_13_133110_add_somme_culumn_on_user_table',1),(11,'2025_05_22_074046_create_vehicule_photos_table',2),(13,'2025_05_22_084231_create_param_elements_table',3),(14,'2025_05_22_084315_create_param_element_details_table',4),(15,'2025_05_22_184130_create_vehicule_elements_table',5),(17,'2025_05_21_082930_create_vehicules_table',6),(18,'2025_05_22_184701_add_column_to_vehicules',6),(19,'2025_05_23_061701_create_chauffeurs_table',7),(20,'2025_05_23_115212_create_vehicule_photo_listes_table',7),(22,'2025_05_27_042355_create_vehicule_documents_table',8),(23,'2025_05_26_135803_create_documents_chauffeurs_table',9),(24,'2025_05_26_142214_create_chauffeur_vehicule_table',9),(25,'2025_05_28_081813_create_groupe_utilisateurs_table',10),(27,'2025_05_28_104845_add_column_to_users',11),(28,'2025_05_28_114534_create_libelle_maintenances_table',12),(29,'2025_05_29_130228_create_planning_calendars_table',13),(30,'0000_01_01_000000_create_user_groups_table',14),(31,'2025_06_11_112400_add_column_to_users_table',15),(32,'2025_06_11_115002_add_column_to_users_table',16),(33,'2025_06_12_040031_add_column_is_dna_to_users_table',17),(34,'2025_06_12_121336_rename_fichier_to_fichier_jointe_in_documents_chauffeurs',18),(35,'2025_06_17_112311_create_magasins_table',19),(36,'2025_06_17_125152_create_famille_article_table',20),(37,'2025_06_17_125614_create_article_familles_table',21),(38,'2025_06_17_125705_create_articles_table',22),(40,'2025_06_18_103838_create_article_magasin_table',23),(41,'2025_06_18_110602_create_article_inventaires_table',24),(42,'2025_06_17_114043_create_carburant_card_table',25),(43,'2025_06_19_055035_create_card_transaction_table',26),(44,'2025_06_19_063828_create_article_mouvements_table',27),(45,'2025_06_20_120107_create_fournisseurs_table',28),(46,'2025_06_20_125634_create_article_approvisionnements_table',28),(47,'2025_06_20_130626_create_article_appro_details_table',29),(48,'2025_06_23_080723_add_column_to_article_appro_details_table',30),(50,'2025_06_19_081642_create_carburant_mouvement_table',31),(51,'2025_06_23_055535_rename_quantite_carburant_to_qte_initiale_in_carburant_mouvements',31),(52,'2025_06_23_060152_add_collumn_qte_finale_to_carburant_mouvements_table',31),(53,'2025_06_23_063117_add_collumn_user_id_to_carburant_mouvements_table',31),(55,'2025_06_24_064907_rename_motant_to_montant_in_carburant_mouvements_table',32),(56,'2025_06_24_134226_create_boncommande_fournisseurs_table',33),(57,'2025_06_24_134922_create_boncommande_fournisseur_details_table',33),(58,'2025_06_24_134428_add_collumn_commentaire_to_carburant_mouvements_table',34),(59,'2025_06_24_140150_add_collumn_date_heure_enregistrement_to_carburant_mouvements_table',34),(60,'2025_06_24_140709_add_collumn_solde_to_carburant_cards_table',34),(61,'2025_06_25_081118_create_card_ajustements_table',34),(62,'2025_06_27_083821_add_collumn_date_to_card_ajustements_table',34),(63,'2025_07_01_134340_add_column_date_heure_enregistrement_to_table_article_inventaires',35),(64,'2025_07_03_073546_add_column_prix_unitare_table_boncommande_fournisseur_details',36),(65,'2025_07_03_074228_add_column_show_prix_unitaire_to_boncommande_fournisseurs_table',37),(66,'2025_07_03_081812_add_column_boncommande_fournisseur_id_to_article_approvisionnements_table',38),(67,'2025_07_03_082449_add_column_numero_bon_livraison_to_article_approvisionnements_table',39),(68,'2025_07_03_110232_add_column_to_fournisseurs_table',40),(69,'2025_07_01_082425_create_carburant_transactions_table',41),(70,'2025_07_01_085734_rename_qte_to_montant_in_carburant_mouvements',42),(71,'2025_07_01_131728_update_type_collumn_in_carburant_mouvements',42),(72,'2025_07_02_083752_add_collumn_reference_to_carburant_mouvements_table',42),(73,'2025_07_02_114659_update_type_collumn_with_new_type_in_carburant_mouvements',42),(74,'2025_07_04_083617_create_article_transactions_table',42),(75,'2025_07_04_112012_add_column_reference_mouvement_to_article_transactions_table',43),(76,'2025_07_04_112145_create_article_transaction_details_table',44),(77,'2025_07_04_112528_add_column_magasin_cible_to_article_transactions_table',44),(78,'2025_07_04_124257_add_column_article_transation_id_to_article_transaction_details',45),(80,'2025_07_07_124934_create_marchandise_table',46),(81,'2025_07_07_130339_create_client_table',46),(82,'2025_07_08_070548_create_remorques_table',46),(84,'2025_07_08_071134_create_remorque_elements_table',47),(85,'2025_07_08_071609_create_remorque_documents_table',47),(86,'2025_07_08_071620_create_remorque_photos_table',47),(87,'2025_07_09_075111_add_column_param_element_id_to_remorques_table',48),(89,'2025_07_09_091332_add_column_remoque_id_to_vehicules_table',49),(90,'2025_07_09_120419_create_devis_clients_table',50),(91,'2025_07_09_122651_create_devis_client_details_table',51),(92,'2025_07_11_074108_add_column_montant_total_to_devis_clients_table',52),(93,'2025_07_11_083659_add_column_montant_total_to_devis_client_details_table',53),(94,'2025_07_11_120429_add_column_to_devis_client_deetails_table',54),(95,'2025_07_08_055552_create_voyage_prime_table',55),(97,'2025_07_10_101813_add_collumn_voyage_id_to_carburant_transaction_table',55),(98,'2025_07_14_132953_create_tarifs_table',56),(99,'2025_07_14_133100_create_tarif_details_table',56),(101,'2025_07_15_073354_create_tresoreries_table',57),(102,'2025_07_15_073512_create_tresorerie_mouvements_table',57),(103,'2025_07_15_111745_create_tresorerie_fluxes_table',58),(104,'2025_07_15_130310_add_column_commentaire_to_tresoreries_table',59),(105,'2025_07_15_130407_add_column_commentaire_to_tresorerie_flux_table',60),(106,'2025_07_11_120503_add_collumn_to_carburant_transactions_table',61),(107,'2025_07_11_140639_drop_unique_attribut_matricule_to_chauffeur_table',61),(108,'2025_07_14_131214_create_reservation_table',61),(109,'2025_07_15_081852_create_reservation_marchandises_table',62),(110,'2025_07_15_083830_create_reservation_charge_table',63),(111,'2025_07_15_100000_create_reservation_details_table',63),(112,'2025_07_21_062336_add_column_deleted_at_to_tresorerie_fluxes_table',63),(113,'2025_07_21_062956_add_reservation_detail_to_reservation_marchandises',63),(114,'2025_07_21_063301_add_reservation_detail_to_reservation_charges',63),(115,'2025_07_21_063353_add_reservation_detail_id_to_carburant_transactions_table',63),(118,'2025_07_15_081000_create_voyage_table',64),(119,'2025_07_15_081852_create_voyage_marchandises_table',65),(120,'2025_07_15_083830_create_voyage_charge_table',65),(121,'2025_07_15_091423_add_voyage_id_to_carburant_transactions_table',65),(122,'2025_07_19_034114_change_allprice_type_double',66),(123,'2025_07_21_082533_add_column_voyage_id_to_voyage_marchandises_table',67),(124,'2025_07_21_090402_create_immobilisations_table',68),(125,'2025_07_21_130641_create_facture_clients_table',69),(126,'2025_07_22_114330_alter_facture_clients_table_for_voyage_ids_json',70),(127,'2025_07_22_115905_nullable_champ_in_facture_cllients_table',71),(128,'2025_07_22_130602_add_column_stat_voyage_champ_in_reservations_table',72),(129,'2025_07_22_130704_dropcolumn_reservation_id_in_facture_cllients_table',73),(130,'2025_07_23_072804_add_column_to_tresoreries_table',74),(131,'2025_07_21_132622_change_date_reservation_column_type_in_reservations_table',75),(132,'2025_07_21_133239_add_remoque_to_voyages_table',75),(133,'2025_07_21_135056_add_numero_voyage_to_voyages_table',75),(134,'2025_07_22_054846_add_foreign_keys_to_voyages_table',75),(135,'2025_07_22_070848_add_collumn_count_numero_reservation_to_reservations_table',75),(136,'2025_07_22_112322_add_tresorerie_mouvement_id_to_voyage_charges_table_final',75),(137,'2025_07_22_142046_add_tarif_id_to_voyages_table',75),(138,'2025_07_22_145936_rename_tarif_to_tarif_ttc_and_add_ht_tva_to_voyages_table',75),(139,'2025_07_23_135311_add_column_to_voyages_table',76),(140,'2025_07_23_075749_add_nbr_vehicule_to_reservations_table',77),(141,'2025_07_23_130952_add_tarif_ttc_ht_tva_to_voyages_table',78),(142,'2025_07_24_123624_add_lieu_depart_et_description_to_voyages_table',78),(143,'2025_07_24_142248_add_kilometrage_pour_tarif_ht_to_voyages_table',79),(144,'2025_07_25_061741_create_info_societes_table',80),(145,'2025_07_25_111049_create_facture_client_reglements_table',81),(146,'2025_07_28_072529_add_column_numero_telephone_to_tresoreries_table',82),(147,'2025_07_28_124944_add_column_titulaire_compte_to_tresoreries_table',83),(150,'2025_07_25_140052_add_numero_to_clients',85),(151,'2025_07_31_072002_create_poste_depenses_table',86),(152,'2025_07_31_085741_add_column_poste_depense_to_tresoreries_table',87),(153,'2025_07_31_124525_add_column_poste_depense_to_tresorerie_mouvements_table',88),(154,'2025_08_04_091836_add_column_tresorerie_id_to_voyage_charges_table',89),(155,'2025_08_04_092137_add_column_mode_paiement_to_voyage_charges_table',90),(156,'2025_07_31_083934_change_numero_to_clients',91),(157,'2025_08_05_105919_add_montant_ht_to_voyages_table',91),(158,'2025_08_06_085018_add_column_taux_tva_in_facture_clients_table',91),(159,'2025_08_06_091305_add_column_montant_voyage_in_facture_clients_table',92),(160,'2025_08_06_082813_add_remise_to_voyages_tables',93),(161,'2019_09_15_000010_create_tenants_table',94),(162,'2019_09_15_000020_create_domains_table',94),(163,'2025_08_08_141923_add_column_is_active_to_tenants_table',95),(164,'2025_08_27_125907_add_nom_client_to_tenant_table',96);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',2),(1,'App\\Models\\User',3),(1,'App\\Models\\User',4),(1,'App\\Models\\User',5);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `param_element_details`
--

DROP TABLE IF EXISTS `param_element_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `param_element_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `param_element_id` bigint unsigned DEFAULT NULL,
  `emplacement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `param_element_details_param_element_id_foreign` (`param_element_id`),
  CONSTRAINT `param_element_details_param_element_id_foreign` FOREIGN KEY (`param_element_id`) REFERENCES `param_elements` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param_element_details`
--

LOCK TABLES `param_element_details` WRITE;
/*!40000 ALTER TABLE `param_element_details` DISABLE KEYS */;
INSERT INTO `param_element_details` VALUES (1,5,'t','t','t','2025-05-22 10:49:12','2025-05-22 08:59:18','2025-05-22 10:49:12'),(2,5,'a','a','a','2025-05-22 10:49:12','2025-05-22 08:59:18','2025-05-22 10:49:12'),(3,5,'t','t','t','2025-05-22 10:50:00','2025-05-22 10:49:13','2025-05-22 10:50:00'),(4,5,'a','a','a','2025-05-22 10:50:00','2025-05-22 10:49:13','2025-05-22 10:50:00'),(5,5,'element 1','tite','tdfsd',NULL,'2025-05-22 10:50:00','2025-05-22 10:50:00'),(6,5,'element 2','adfs','adfsq',NULL,'2025-05-22 10:50:00','2025-05-22 10:50:00'),(7,6,'t','t','t','2025-05-22 10:55:32','2025-05-22 10:55:18','2025-05-22 10:55:32'),(8,7,'xx','xx','xxx','2025-06-04 08:47:50','2025-06-04 08:44:24','2025-06-04 08:47:50'),(9,7,'xx','xx','xxx',NULL,'2025-06-04 08:47:50','2025-06-04 08:47:50'),(10,7,'1','1','1',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(11,7,'2','2','2',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(12,7,'3','3','3',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(13,7,'4','4','4',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(14,7,'5','5','5',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(15,7,'6','6','6',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51'),(16,7,'7','7','7',NULL,'2025-06-04 08:47:51','2025-06-04 08:47:51');
/*!40000 ALTER TABLE `param_element_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `param_elements`
--

DROP TABLE IF EXISTS `param_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `param_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `param_elements`
--

LOCK TABLES `param_elements` WRITE;
/*!40000 ALTER TABLE `param_elements` DISABLE KEYS */;
INSERT INTO `param_elements` VALUES (5,'Modèle 1','2025-05-22 08:59:18','2025-05-22 08:59:18',NULL),(6,'t','2025-05-22 10:55:18','2025-05-22 10:55:32','2025-05-22 10:55:32'),(7,'modèle 2','2025-06-04 08:44:23','2025-06-04 08:44:23',NULL);
/*!40000 ALTER TABLE `param_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user-view','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(2,'user-create','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(3,'user-edit','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(4,'user-delete','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(5,'chauffeur-view','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(6,'chauffeur-create','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(7,'chauffeur-edit','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(8,'chauffeur-delete','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(9,'vehicule-view','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(10,'vehicule-create','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(11,'vehicule-edit','web','2025-06-12 02:05:10','2025-06-12 02:05:10'),(12,'vehicule-delete','web','2025-06-12 02:05:10','2025-06-12 02:05:10');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planning_calendars`
--

DROP TABLE IF EXISTS `planning_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planning_calendars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `libelle_maintenance_id` bigint unsigned DEFAULT NULL,
  `date_maintenance` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planning_calendars_vehicule_id_foreign` (`vehicule_id`),
  KEY `planning_calendars_libelle_maintenance_id_foreign` (`libelle_maintenance_id`),
  CONSTRAINT `planning_calendars_libelle_maintenance_id_foreign` FOREIGN KEY (`libelle_maintenance_id`) REFERENCES `libelle_maintenances` (`id`),
  CONSTRAINT `planning_calendars_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planning_calendars`
--

LOCK TABLES `planning_calendars` WRITE;
/*!40000 ALTER TABLE `planning_calendars` DISABLE KEYS */;
INSERT INTO `planning_calendars` VALUES (1,4,1,'2025-06-11','2025-05-30 09:43:34','2025-05-30 09:43:34',NULL),(2,4,11,'2025-06-11','2025-05-30 09:45:48','2025-05-30 09:45:48',NULL),(3,4,1,'2025-06-17','2025-05-30 09:47:45','2025-05-30 09:47:45',NULL),(4,5,11,'2027-06-16','2025-05-30 10:06:32','2025-05-30 11:06:23',NULL),(5,4,2,'2025-07-01','2025-06-10 03:59:24','2025-06-10 03:59:24',NULL),(6,5,1,'2025-07-24','2025-06-10 04:00:14','2025-06-10 04:00:14',NULL),(7,4,12,'2026-07-30','2025-06-10 04:00:54','2025-06-10 04:00:54',NULL),(8,18,3,'2025-07-17','2025-07-01 05:40:00','2025-07-01 05:40:00',NULL),(9,12,3,'2024-07-01','2025-07-02 04:34:13','2025-07-02 04:38:53',NULL),(10,5,1,'2025-07-05','2025-07-02 05:22:34','2025-07-02 05:22:34',NULL),(11,12,3,'2025-07-06','2025-07-02 05:22:53','2025-07-02 05:22:53',NULL),(12,5,3,'2025-07-02','2025-07-02 07:27:34','2025-07-02 07:27:34',NULL),(13,12,11,'2025-07-02','2025-07-02 07:29:55','2025-07-02 07:29:55',NULL);
/*!40000 ALTER TABLE `planning_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poste_depenses`
--

DROP TABLE IF EXISTS `poste_depenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `poste_depenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poste_depenses`
--

LOCK TABLES `poste_depenses` WRITE;
/*!40000 ALTER TABLE `poste_depenses` DISABLE KEYS */;
INSERT INTO `poste_depenses` VALUES (1,'achat 1sdfsddf','2025-07-31 05:46:03','2025-07-31 05:50:38',NULL),(2,'achat 2','2025-07-31 05:46:10','2025-07-31 05:46:10',NULL),(3,'teste ets','2025-07-31 05:51:57','2025-07-31 05:52:01','2025-07-31 05:52:01'),(4,'teste','2025-07-31 08:17:32','2025-07-31 08:17:32',NULL),(5,NULL,'2025-07-31 09:04:53','2025-07-31 09:04:53',NULL),(6,'tsiry','2025-07-31 09:31:53','2025-07-31 09:31:53',NULL),(7,'10','2025-07-31 09:33:46','2025-07-31 09:33:46',NULL),(8,'decaissement','2025-07-31 09:48:35','2025-07-31 09:48:35',NULL);
/*!40000 ALTER TABLE `poste_depenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remorque_documents`
--

DROP TABLE IF EXISTS `remorque_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remorque_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `remorque_id` bigint unsigned DEFAULT NULL,
  `nom_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_expiration` date DEFAULT NULL,
  `fichier_jointe` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remorque_documents_remorque_id_foreign` (`remorque_id`),
  CONSTRAINT `remorque_documents_remorque_id_foreign` FOREIGN KEY (`remorque_id`) REFERENCES `remorques` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remorque_documents`
--

LOCK TABLES `remorque_documents` WRITE;
/*!40000 ALTER TABLE `remorque_documents` DISABLE KEYS */;
INSERT INTO `remorque_documents` VALUES (1,1,'teste','fqsf','2025-07-09','[\"/img/remorque/documents/1_200430.jpeg\", \"/img/remorque/documents/1_890730.jpeg\"]','2025-07-09 06:36:43','2025-07-09 06:36:58',NULL);
/*!40000 ALTER TABLE `remorque_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remorque_elements`
--

DROP TABLE IF EXISTS `remorque_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remorque_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `remorque_id` bigint unsigned DEFAULT NULL,
  `emplacement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_piece` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remorque_elements_remorque_id_foreign` (`remorque_id`),
  CONSTRAINT `remorque_elements_remorque_id_foreign` FOREIGN KEY (`remorque_id`) REFERENCES `remorques` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remorque_elements`
--

LOCK TABLES `remorque_elements` WRITE;
/*!40000 ALTER TABLE `remorque_elements` DISABLE KEYS */;
INSERT INTO `remorque_elements` VALUES (1,NULL,'element 1','tite','tdfsd',NULL,NULL,'2025-07-09 06:44:39','2025-07-09 06:44:39',NULL),(2,NULL,'element 2','adfs','adfsq',NULL,NULL,'2025-07-09 06:44:39','2025-07-09 06:44:39',NULL),(3,4,'xx','xx','xxx',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(4,4,'1','1','1',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(5,4,'2','2','2',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(6,4,'3','3','3',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(7,4,'4','4','4',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(8,4,'5','5','5',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(9,4,'6','6','6',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(10,4,'7','7','7',NULL,NULL,'2025-07-09 06:45:36','2025-07-09 06:45:36',NULL),(11,5,'xx','xx','xxx',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(12,5,'1','1','1',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(13,5,'2','2','2',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(14,5,'3','3','3',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(15,5,'4','4','4',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(16,5,'5','5','5',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(17,5,'6','6','6',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(18,5,'7','7','7',NULL,NULL,'2025-07-09 07:09:36','2025-07-09 07:09:58','2025-07-09 07:09:58'),(19,5,'xx','xx','xxx','fdsfqf',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(20,5,'1','1','1','ddd',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(21,5,'2','2','2','ffq',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(22,5,'3','3','3','d',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(23,5,'4','4','4','fqfd',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(24,5,'5','5','5','qfq',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(25,5,'6','6','6','fdf',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL),(26,5,'7','7','7','dfqf',NULL,'2025-07-09 07:09:58','2025-07-09 07:09:58',NULL);
/*!40000 ALTER TABLE `remorque_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remorque_photos`
--

DROP TABLE IF EXISTS `remorque_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remorque_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `remorque_id` bigint unsigned DEFAULT NULL,
  `type_element` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_prise_photo` date DEFAULT NULL,
  `liste_image` json DEFAULT NULL,
  `etat_vehicule` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remorque_photos`
--

LOCK TABLES `remorque_photos` WRITE;
/*!40000 ALTER TABLE `remorque_photos` DISABLE KEYS */;
INSERT INTO `remorque_photos` VALUES (1,1,'Photo origine','2025-07-09','[{\"main\": \"img/remorque/remorque_1_202507090817025oKa31322.webp\"}]','teste remoqure','2025-07-09 06:17:02','2025-07-09 06:37:14',NULL);
/*!40000 ALTER TABLE `remorque_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remorques`
--

DROP TABLE IF EXISTS `remorques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remorques` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_remorque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modele_remorque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marque_remorque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `param_element_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remorques_param_element_id_foreign` (`param_element_id`),
  CONSTRAINT `remorques_param_element_id_foreign` FOREIGN KEY (`param_element_id`) REFERENCES `param_elements` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remorques`
--

LOCK TABLES `remorques` WRITE;
/*!40000 ALTER TABLE `remorques` DISABLE KEYS */;
INSERT INTO `remorques` VALUES (1,'REM-001','TR500','Schmitz','2025-07-08 07:12:13','2025-07-09 05:55:22',NULL,NULL),(2,'dfsfs','testes','teste','2025-07-09 06:42:33','2025-07-09 06:42:33',NULL,5),(3,'sfdddd','TR500','teste','2025-07-09 06:44:39','2025-07-09 06:44:39',NULL,5),(4,'fqf dfq','qgrez','fff','2025-07-09 06:45:36','2025-07-09 06:45:36',NULL,7),(5,'ssss','f','fqfqsf','2025-07-09 07:09:36','2025-07-09 07:09:36',NULL,7);
/*!40000 ALTER TABLE `remorques` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_charges`
--

DROP TABLE IF EXISTS `reservation_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_charges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reservation_detail_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_charges_reservation_detail_id_foreign` (`reservation_detail_id`),
  CONSTRAINT `reservation_charges_reservation_detail_id_foreign` FOREIGN KEY (`reservation_detail_id`) REFERENCES `reservation_details` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_charges`
--

LOCK TABLES `reservation_charges` WRITE;
/*!40000 ALTER TABLE `reservation_charges` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_details`
--

DROP TABLE IF EXISTS `reservation_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint unsigned DEFAULT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `chauffeur_id` bigint unsigned DEFAULT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_trajet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_reception` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobilisation` int DEFAULT NULL,
  `etat_vehicule_avant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_vehicule_apres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_details`
--

LOCK TABLES `reservation_details` WRITE;
/*!40000 ALTER TABLE `reservation_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_marchandises`
--

DROP TABLE IF EXISTS `reservation_marchandises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_marchandises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reservation_detail_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_marchandises_reservation_detail_id_foreign` (`reservation_detail_id`),
  CONSTRAINT `reservation_marchandises_reservation_detail_id_foreign` FOREIGN KEY (`reservation_detail_id`) REFERENCES `reservation_details` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_marchandises`
--

LOCK TABLES `reservation_marchandises` WRITE;
/*!40000 ALTER TABLE `reservation_marchandises` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_marchandises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_reservation` date NOT NULL,
  `numero_reservation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `lieu_chargement` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_livraison` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_facture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `facture_client_id` bigint unsigned DEFAULT NULL,
  `count_numero_reservation` int NOT NULL DEFAULT '0',
  `nbr_vehicule` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_user_id_foreign` (`user_id`),
  KEY `reservations_client_id_foreign` (`client_id`),
  KEY `reservations_facture_client_id_foreign` (`facture_client_id`),
  CONSTRAINT `reservations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `reservations_facture_client_id_foreign` FOREIGN KEY (`facture_client_id`) REFERENCES `facture_clients` (`id`),
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (12,'2025-08-06','RES-001/08-2025',1,1,'Toamasina','Tana','valide',NULL,'2025-08-06 09:34:16','2025-08-06 09:34:16',NULL,1,2),(13,'2025-08-06','RES-002/08-2025',1,1,'Tana','Tana','valide',NULL,'2025-08-06 10:05:44','2025-08-06 10:05:44',NULL,2,3);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','web','2025-05-21 05:44:40','2025-05-21 05:44:40',NULL),(2,'ADMIN2','web','2025-06-12 02:05:09','2025-06-12 02:05:09',NULL),(3,'ADMIN3','web','2025-06-12 02:05:10','2025-06-12 02:05:10',NULL),(4,'ADMIN4','web','2025-06-12 02:05:10','2025-06-12 02:05:10',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('zcwYUGVislUIYP6cwpCUh0Kk7sPKsb4ZSm15VyeL',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVFBPMnBHeXk1dXFsaUt4WWtuMjRSZG8wcWNFMkdybkNUWHNEMFk3aSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=',1777989331);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarif_details`
--

DROP TABLE IF EXISTS `tarif_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarif_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tarif_id` bigint unsigned NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Libellé du tarif',
  `prix_ht` double NOT NULL COMMENT 'Prix HT du tarif',
  `tva` double NOT NULL DEFAULT '20' COMMENT 'Taux de TVA appliqué',
  `prix_ttc` double NOT NULL COMMENT 'Prix TTC du tarif, calculé à partir du prix HT et de la TVA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarif_details_tarif_id_foreign` (`tarif_id`),
  CONSTRAINT `tarif_details_tarif_id_foreign` FOREIGN KEY (`tarif_id`) REFERENCES `tarifs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarif_details`
--

LOCK TABLES `tarif_details` WRITE;
/*!40000 ALTER TABLE `tarif_details` DISABLE KEYS */;
INSERT INTO `tarif_details` VALUES (1,1,'1',1000000,20,1200000,'2025-07-14 11:29:05','2025-07-15 03:22:19','2025-07-15 03:22:19'),(2,1,'1',1000000,20,1200000,'2025-07-15 03:22:19','2025-07-15 03:33:39','2025-07-15 03:33:39'),(3,1,'2',50000,20,60000,'2025-07-15 03:22:19','2025-07-15 03:33:39','2025-07-15 03:33:39'),(4,2,'TC de 20\' < 25T',4300000,20,5160000,'2025-07-15 03:36:21','2025-07-15 03:36:21',NULL),(5,2,'TC de 40\' > 25T',4500000,20,5400000,'2025-07-15 03:36:21','2025-07-15 03:36:21',NULL),(6,2,'TC REFER',6000000,20,7200000,'2025-07-15 03:36:21','2025-07-15 03:36:21',NULL),(7,2,'ISOTANK',6500000,20,7800000,'2025-07-15 03:36:21','2025-07-15 03:36:21',NULL);
/*!40000 ALTER TABLE `tarif_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarifs`
--

DROP TABLE IF EXISTS `tarifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarifs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_tarif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Libellé du tarif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarifs`
--

LOCK TABLES `tarifs` WRITE;
/*!40000 ALTER TABLE `tarifs` DISABLE KEYS */;
INSERT INTO `tarifs` VALUES (1,'TRANSPORT TC Pour Tamatave - Antananaivo','2025-07-14 11:29:05','2025-07-15 03:33:39','2025-07-15 03:33:39'),(2,'TRANSPORT TC Pour Tamatave-Antananarivo','2025-07-15 03:36:21','2025-07-15 03:36:21',NULL);
/*!40000 ALTER TABLE `tarifs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` json DEFAULT NULL,
  `is_active` int DEFAULT (1),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('Aina_trans',NULL,'2025-08-27 10:26:27','2025-08-27 10:26:27','{\"is_active\": 1, \"created_at\": \"2025-08-27 13:26:27\", \"nom_client\": \"transport Aina\", \"updated_at\": \"2025-08-27 13:26:27\", \"tenancy_db_name\": \"tms_Aina_trans\"}',1),('demos',NULL,'2025-08-11 04:38:19','2025-08-11 04:38:19','{\"is_active\": 1, \"created_at\": \"2025-08-11 07:38:19\", \"updated_at\": \"2025-08-11 07:38:19\", \"tenancy_db_name\": \"tms_demos\"}',1),('domaine_teste',NULL,'2025-08-27 10:02:57','2025-08-27 10:02:57','{\"is_active\": 1, \"created_at\": \"2025-08-27 13:02:57\", \"updated_at\": \"2025-08-27 13:02:57\", \"tenancy_db_name\": \"tms_domaine_teste\"}',1),('everest_trans',NULL,'2025-08-27 10:21:16','2025-08-27 10:21:16','{\"is_active\": 1, \"created_at\": \"2025-08-27 13:21:16\", \"nom_client\": \"transport everest\", \"updated_at\": \"2025-08-27 13:21:16\", \"tenancy_db_name\": \"tms_everest_trans\"}',1),('transmdata',NULL,'2025-08-27 04:57:18','2025-08-27 04:57:19','{\"is_active\": 1, \"created_at\": \"2025-08-27 07:57:18\", \"updated_at\": \"2025-08-27 07:57:18\", \"tenancy_db_name\": \"tms_transmdata\"}',1);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tresorerie_fluxes`
--

DROP TABLE IF EXISTS `tresorerie_fluxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tresorerie_fluxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tresorerie_id` bigint unsigned NOT NULL,
  `tresorerie_id_cible` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `date_mvt` date NOT NULL,
  `libelle_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solde_avant` double NOT NULL DEFAULT '0',
  `montant` double NOT NULL DEFAULT '0',
  `solde_final` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `poste_depense` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tresorerie_fluxes_tresorerie_id_foreign` (`tresorerie_id`),
  KEY `tresorerie_fluxes_tresorerie_id_cible_foreign` (`tresorerie_id_cible`),
  KEY `tresorerie_fluxes_user_id_foreign` (`user_id`),
  CONSTRAINT `tresorerie_fluxes_tresorerie_id_cible_foreign` FOREIGN KEY (`tresorerie_id_cible`) REFERENCES `tresoreries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tresorerie_fluxes_tresorerie_id_foreign` FOREIGN KEY (`tresorerie_id`) REFERENCES `tresoreries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tresorerie_fluxes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tresorerie_fluxes`
--

LOCK TABLES `tresorerie_fluxes` WRITE;
/*!40000 ALTER TABLE `tresorerie_fluxes` DISABLE KEYS */;
INSERT INTO `tresorerie_fluxes` VALUES (50,12,NULL,1,'2025-07-28','a',NULL,'Ajustement vers 500000','Espèces','ENTREE',0,500000,500000,'2025-07-28 06:51:51','2025-07-28 06:51:51','ajustement',NULL,NULL),(51,12,NULL,1,'2025-07-28','Transfert vers ',NULL,'Transfert à ','Espèces','SORTIE',500000,200000,300000,'2025-07-28 06:52:20','2025-07-28 06:52:20',NULL,NULL,NULL),(52,13,NULL,1,'2025-07-28','Transfert de ',NULL,'Transfert de ','Espèces','ENTREE',0,200000,200000,'2025-07-28 06:52:20','2025-07-28 06:52:20',NULL,NULL,NULL),(53,12,NULL,1,'2025-07-28','efs',NULL,'efs','Espèces','ENTREE',300000,100000,400000,'2025-07-28 08:59:53','2025-07-28 08:59:53','t',NULL,NULL),(54,12,NULL,1,'2025-07-28','Annulation efs',NULL,'Annulation mouvement','Annulation','SORTIE',400000,100000,300000,'2025-07-28 08:59:58','2025-07-28 08:59:58','Annulation: t',NULL,NULL),(55,12,NULL,1,'2025-07-28','Transfert vers banque',NULL,'Transfert à banque','Espèces','SORTIE',300000,20,299980,'2025-07-28 09:02:08','2025-07-28 09:02:08',NULL,NULL,NULL),(56,13,NULL,1,'2025-07-28','Transfert de caisse',NULL,'Transfert de caisse','Espèces','ENTREE',200000,20,200020,'2025-07-28 09:02:08','2025-07-28 09:02:08',NULL,NULL,NULL),(57,12,NULL,1,'2025-07-28','Annulation transfert',NULL,'Annulation transfert','Annulation','ENTREE',299980,20,300000,'2025-07-28 09:02:37','2025-07-28 09:02:37','Annulation transfert vers banque',NULL,NULL),(58,13,NULL,1,'2025-07-28','Annulation transfert',NULL,'Annulation transfert','Annulation','SORTIE',200020,20,200000,'2025-07-28 09:02:37','2025-07-28 09:02:37','Annulation transfert de caisse',NULL,NULL),(60,12,NULL,1,'2025-07-29','Réglement du facture client: Mbolatiana - FACT-001/07-2025',NULL,'REGLEMENT FACTURE','Espèces','ENTREE',300000,15720000,16020000,'2025-07-29 05:05:05','2025-07-29 05:05:05','reglement',NULL,NULL),(61,15,NULL,1,'2025-07-29','Réglement du facture client: encore client - FACT-002/07-2025',NULL,'REGLEMENT FACTURE','Espèces','ENTREE',0,160000,160000,'2025-07-29 05:15:13','2025-07-29 05:15:13','paiement partielle',NULL,NULL),(62,12,NULL,1,'2025-07-29','Réglement du facture client: encore client - FACT-002/07-2025',NULL,'REGLEMENT FACTURE','Espèces','ENTREE',16020000,30000,16050000,'2025-07-29 05:54:45','2025-07-29 05:54:45','gff',NULL,NULL),(63,12,NULL,1,'2025-07-29','Réglement de la facture client: Mbolatiana - FACT-001/07-2025',NULL,'ANNULATION REGLEMENT FACTURE','Espèces','SORTIE',16050000,15720000,330000,'2025-07-29 06:36:02','2025-07-29 06:36:02','Annulation règlement client: 2',NULL,NULL),(64,12,NULL,1,'2025-07-29','Réglement de la facture client: encore client - FACT-002/07-2025',NULL,'ANNULATION REGLEMENT FACTURE','Espèces','SORTIE',330000,30000,300000,'2025-07-29 08:32:19','2025-07-29 08:32:19','Annulation règlement client: 4',NULL,NULL),(65,12,NULL,1,'2025-07-31','test',NULL,NULL,'Carte bancaire','SORTIE',300000,1000,299000,'2025-07-31 08:17:30','2025-07-31 08:17:30','dfdf',NULL,NULL),(66,12,NULL,1,'2025-07-31','fs',NULL,NULL,'Espèces','SORTIE',299000,100,298900,'2025-07-31 08:25:37','2025-07-31 08:25:37','ds',NULL,NULL),(67,12,NULL,1,'2025-07-31','fs',NULL,NULL,'Espèces','SORTIE',298900,100,298800,'2025-07-31 08:25:48','2025-07-31 08:25:48','ds',NULL,NULL),(68,12,NULL,1,'2025-07-31','fs',NULL,NULL,'Espèces','SORTIE',298800,100,298700,'2025-07-31 08:32:18','2025-07-31 08:32:18','ds',NULL,NULL),(69,12,NULL,1,'2025-07-31','dfs',NULL,NULL,'Espèces','SORTIE',298700,20,298680,'2025-07-31 08:50:30','2025-07-31 08:50:30','d',NULL,NULL),(70,12,NULL,1,'2025-07-31','dfs',NULL,NULL,'Espèces','SORTIE',298680,20,298660,'2025-07-31 08:50:44','2025-07-31 08:50:44','d',NULL,NULL),(71,12,NULL,1,'2025-07-31','dfs',NULL,NULL,'Espèces','SORTIE',298660,20,298640,'2025-07-31 08:51:04','2025-07-31 08:51:04','d',NULL,NULL),(72,12,NULL,1,'2025-07-31','f',NULL,NULL,'Espèces','SORTIE',298640,1000,297640,'2025-07-31 08:57:56','2025-07-31 08:57:56',NULL,NULL,NULL),(73,12,NULL,1,'2025-07-31','f',NULL,NULL,'Espèces','SORTIE',297640,100,297540,'2025-07-31 08:58:36','2025-07-31 08:58:36',NULL,NULL,NULL),(74,12,NULL,1,'2025-07-31','df',NULL,NULL,'Espèces','SORTIE',297540,200,297340,'2025-07-31 08:59:37','2025-07-31 08:59:37',NULL,NULL,NULL),(75,13,NULL,1,'2025-07-31','Réglement de la facture client:  - FACT-002/07-2025',NULL,'REGLEMENT FACTURE','Espèces','ENTREE',200000,5000000,5200000,'2025-07-31 09:02:56','2025-07-31 09:02:56','d',NULL,NULL),(76,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',297340,1000,296340,'2025-07-31 09:04:53','2025-07-31 09:04:53','dfs',NULL,NULL),(77,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',296340,10,296330,'2025-07-31 09:14:49','2025-07-31 09:14:49',NULL,NULL,NULL),(78,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',296330,10,296320,'2025-07-31 09:15:03','2025-07-31 09:15:03',NULL,NULL,NULL),(79,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296320,10,296310,'2025-07-31 09:17:04','2025-07-31 09:17:04',NULL,NULL,NULL),(80,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296310,10,296300,'2025-07-31 09:27:04','2025-07-31 09:27:04',NULL,NULL,NULL),(81,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296300,10,296290,'2025-07-31 09:27:47','2025-07-31 09:27:47',NULL,NULL,NULL),(82,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296290,10,296280,'2025-07-31 09:28:14','2025-07-31 09:28:14',NULL,NULL,NULL),(83,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296280,10,296270,'2025-07-31 09:28:58','2025-07-31 09:28:58',NULL,NULL,NULL),(84,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296270,10,296260,'2025-07-31 09:29:18','2025-07-31 09:29:18',NULL,NULL,NULL),(85,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296260,10,296250,'2025-07-31 09:29:28','2025-07-31 09:29:28',NULL,NULL,NULL),(86,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296250,10,296240,'2025-07-31 09:29:46','2025-07-31 09:29:46',NULL,NULL,NULL),(87,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296240,10,296230,'2025-07-31 09:30:01','2025-07-31 09:30:01',NULL,NULL,NULL),(88,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296230,10,296220,'2025-07-31 09:30:18','2025-07-31 09:30:18',NULL,NULL,NULL),(89,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296220,10,296210,'2025-07-31 09:30:31','2025-07-31 09:30:31',NULL,NULL,NULL),(90,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296210,10,296200,'2025-07-31 09:31:53','2025-07-31 09:31:53',NULL,NULL,NULL),(91,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296200,10,296190,'2025-07-31 09:32:10','2025-07-31 09:32:10',NULL,NULL,NULL),(92,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296190,10,296180,'2025-07-31 09:32:16','2025-07-31 09:32:16',NULL,NULL,NULL),(93,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296180,10,296170,'2025-07-31 09:32:18','2025-07-31 09:32:18',NULL,NULL,NULL),(94,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',296170,10,296160,'2025-07-31 09:32:21','2025-07-31 09:32:21',NULL,NULL,NULL),(95,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',296160,11000,285160,'2025-07-31 09:33:18','2025-07-31 09:33:18',NULL,NULL,NULL),(96,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',285160,11000,274160,'2025-07-31 09:33:24','2025-07-31 09:33:24',NULL,NULL,NULL),(97,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',274160,11000,263160,'2025-07-31 09:33:30','2025-07-31 09:33:30',NULL,NULL,NULL),(98,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',263160,11000,252160,'2025-07-31 09:33:46','2025-07-31 09:33:46',NULL,NULL,NULL),(99,12,NULL,1,'2025-07-31','d',NULL,NULL,'Carte bancaire','SORTIE',252160,1000,251160,'2025-07-31 09:48:35','2025-07-31 09:48:35',NULL,NULL,'decaissement'),(100,12,NULL,1,'2025-08-04','Charge Voyage: charge 1',NULL,'Paiement Charge Voyage','espèce','SORTIE',251160,20000,231160,'2025-08-04 06:24:03','2025-08-04 06:24:03','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(101,12,NULL,1,'2025-08-04','Charge Voyage: chage 2',NULL,'Paiement Charge Voyage','espèce','SORTIE',231160,2000,229160,'2025-08-04 06:24:03','2025-08-04 06:24:03','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(102,12,NULL,1,'2025-08-04','Charge Voyage: chage 3',NULL,'Paiement Charge Voyage','espèce','SORTIE',229160,2000,227160,'2025-08-04 06:24:03','2025-08-04 06:24:03','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(103,12,NULL,1,'2025-08-04','Charge Voyage: charge 1',NULL,'Paiement Charge Voyage','espèce','SORTIE',227160,20000,207160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(104,12,NULL,1,'2025-08-04','Charge Voyage: chage 2',NULL,'Paiement Charge Voyage','espèce','SORTIE',207160,2000,205160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(105,12,NULL,1,'2025-08-04','Charge Voyage: chage 3',NULL,'Paiement Charge Voyage','espèce','SORTIE',205160,2000,203160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Charge liée au voyage N°VOY-005/08-2025',NULL,NULL),(106,12,NULL,1,'2025-08-04','Annulation Suppression Charge Voyage: charge 1',NULL,'Annulation mouvement','Annulation','ENTREE',203160,20000,223160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Annulation: Annulation de la charge Voyage N°VOY-005/08-2025',NULL,NULL),(107,12,NULL,1,'2025-08-04','Annulation Suppression Charge Voyage: chage 2',NULL,'Annulation mouvement','Annulation','ENTREE',223160,2000,225160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Annulation: Annulation de la charge Voyage N°VOY-005/08-2025',NULL,NULL),(108,12,NULL,1,'2025-08-04','Annulation Suppression Charge Voyage: chage 3',NULL,'Annulation mouvement','Annulation','ENTREE',225160,2000,227160,'2025-08-04 06:25:00','2025-08-04 06:25:00','Annulation: Annulation de la charge Voyage N°VOY-005/08-2025',NULL,NULL),(109,12,NULL,1,'2025-08-04','fgsg',NULL,NULL,'Espèces','SORTIE',227160,50000,177160,'2025-08-04 06:27:19','2025-08-04 06:27:19','teset',NULL,'achat 2'),(110,12,NULL,1,'2025-08-04','Annulation fgsg',NULL,'Annulation mouvement','Annulation','ENTREE',177160,50000,227160,'2025-08-04 06:27:42','2025-08-04 06:27:42','Annulation: teset',NULL,NULL),(111,12,NULL,1,'2025-08-04','fgsg',NULL,NULL,'Espèces','SORTIE',227160,50000,177160,'2025-08-04 06:27:42','2025-08-04 06:27:42','teset',NULL,'achat 2');
/*!40000 ALTER TABLE `tresorerie_fluxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tresorerie_mouvements`
--

DROP TABLE IF EXISTS `tresorerie_mouvements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tresorerie_mouvements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tresorerie_id` bigint unsigned NOT NULL,
  `tresorerie_id_cible` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `date_mvt` date NOT NULL,
  `libelle_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_mvt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solde_avant` double DEFAULT '0',
  `montant` double NOT NULL DEFAULT '0',
  `solde_final` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poste_depense` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tresorerie_mouvements_tresorerie_id_foreign` (`tresorerie_id`),
  KEY `tresorerie_mouvements_tresorerie_id_cible_foreign` (`tresorerie_id_cible`),
  KEY `tresorerie_mouvements_user_id_foreign` (`user_id`),
  CONSTRAINT `tresorerie_mouvements_tresorerie_id_cible_foreign` FOREIGN KEY (`tresorerie_id_cible`) REFERENCES `tresoreries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tresorerie_mouvements_tresorerie_id_foreign` FOREIGN KEY (`tresorerie_id`) REFERENCES `tresoreries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tresorerie_mouvements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tresorerie_mouvements`
--

LOCK TABLES `tresorerie_mouvements` WRITE;
/*!40000 ALTER TABLE `tresorerie_mouvements` DISABLE KEYS */;
INSERT INTO `tresorerie_mouvements` VALUES (18,12,NULL,1,'2025-07-28','a',NULL,NULL,'Espèces','AJUSTEMENT',0,500000,0,'2025-07-28 06:51:51','2025-07-28 06:51:51',NULL,'ajustement',NULL),(19,12,13,1,'2025-07-28','t',NULL,NULL,'Espèces','TRANSFERT',0,200000,0,'2025-07-28 06:52:20','2025-07-28 06:52:20',NULL,NULL,NULL),(20,12,NULL,1,'2025-07-28','efs',NULL,NULL,'Espèces','ENTREE',0,100000,0,'2025-07-28 08:59:53','2025-07-28 08:59:58','2025-07-28 08:59:58','t',NULL),(21,12,13,1,'2025-07-28','d',NULL,NULL,'Espèces','TRANSFERT',0,20,0,'2025-07-28 09:02:08','2025-07-28 09:02:37','2025-07-28 09:02:37',NULL,NULL),(22,12,NULL,1,'2025-07-31','test',NULL,NULL,'Carte bancaire','SORTIE',0,1000,0,'2025-07-31 08:17:32','2025-07-31 08:17:32',NULL,'dfdf',NULL),(23,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',0,1000,0,'2025-07-31 09:04:53','2025-07-31 09:04:53',NULL,'dfs',NULL),(24,12,NULL,1,'2025-07-31','r',NULL,NULL,'Espèces','SORTIE',0,10,0,'2025-07-31 09:31:53','2025-07-31 09:31:53',NULL,NULL,NULL),(25,12,NULL,1,'2025-07-31','d',NULL,NULL,'Espèces','SORTIE',0,11000,0,'2025-07-31 09:33:46','2025-07-31 09:33:46',NULL,NULL,NULL),(26,12,NULL,1,'2025-07-31','d',NULL,NULL,'Carte bancaire','SORTIE',0,1000,0,'2025-07-31 09:48:35','2025-07-31 09:48:35',NULL,NULL,'decaissement'),(27,12,NULL,1,'2025-08-04','fgsg',NULL,NULL,'Espèces','SORTIE',0,50000,0,'2025-08-04 06:27:19','2025-08-04 06:27:19',NULL,'teset','achat 2');
/*!40000 ALTER TABLE `tresorerie_mouvements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tresoreries`
--

DROP TABLE IF EXISTS `tresoreries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tresoreries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_tresorerie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solde` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_tresorerie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_compte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banque_correspondante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_swift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulaire_compte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poste_depense` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tresoreries`
--

LOCK TABLES `tresoreries` WRITE;
/*!40000 ALTER TABLE `tresoreries` DISABLE KEYS */;
INSERT INTO `tresoreries` VALUES (12,'caisse',177160,'2025-07-28 06:49:58','2025-08-04 06:27:42',NULL,NULL,'Caisse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'banque',5200000,'2025-07-28 06:50:16','2025-07-31 09:02:56',NULL,NULL,'Caisse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'caisse 1',0,'2025-07-28 06:50:31','2025-07-28 06:51:08','2025-07-28 06:51:08',NULL,'Caisse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'d',160000,'2025-07-28 09:53:10','2025-07-29 05:15:13',NULL,NULL,'Mobile Money',NULL,NULL,NULL,NULL,NULL,'0341384290','MBOla',NULL);
/*!40000 ALTER TABLE `tresoreries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `privileges` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (1,'teste','[\"user\",\"user.index\",\"user.store\",\"user.update\",\"user.delete\"]','2025-06-10 05:37:49','2025-06-10 05:37:49',NULL),(2,'Groupe 1','','2025-06-11 09:38:35','2025-06-13 04:49:12',NULL),(3,'ADMINISTRATEUR','[\"calendrier\",\"planning_calendar\",\"planning_calendar.index\",\"planning_calendar.store\",\"planning_calendar.update\",\"planning_calendar.delete\",\"planning_calendar.show\",\"user\",\"user.index\",\"user.store\",\"user.update\",\"user.delete\",\"group_user\",\"group_user.index\",\"group_user.store\",\"group_user.update\",\"group_user.delete\",\"paramelement\",\"paramelement.index\",\"paramelement.store\",\"paramelement.update\",\"paramelement.destroy\",\"libelle_maintenance\",\"libelle_maintenance.index\",\"libelle_maintenance.store\",\"libelle_maintenance.update\",\"libelle_maintenance.destroy\",\"dashboard\",\"chauffeur.index\",\"chauffeur.store\",\"chauffeur.delete\",\"chauffeur.informations\",\"chauffeur.index_chauffeur_documennt\",\"chauffeur.store_chauffeur_documennt\",\"chauffeur.update_chauffeur_documennt\",\"chauffeur.destroy_chauffeur_documennt\",\"chauffeur.update\",\"chauffeur\",\"vehicule\",\"vehicule.index\",\"vehicule.store\",\"vehicule.update\",\"vehicule.delete\",\"vehicule.show\",\"vehicule.index_element_vehicule\",\"vehicule.store_element_vehicule\",\"vehicule.index_vehicule_photo\",\"vehicule.store_vehicule_photo\",\"vehicule.update_vehicule_photo\",\"vehicule.destroy_vehicule_photo\",\"vehicule.index_vehicule_document\",\"vehicule.store_vehicule_document\",\"vehicule.update_vehicule_document\",\"vehicule.destroy_vehicule_document\"]','2025-06-13 04:52:24','2025-06-13 11:40:37',NULL),(4,'chauffeur_groupe','[\"dashboard\",\"calendrier\",\"chauffeur\",\"chauffeur.index\",\"chauffeur.store\",\"chauffeur.update\",\"chauffeur.delete\",\"chauffeur.informations\",\"chauffeur.index_chauffeur_documennt\",\"chauffeur.store_chauffeur_documennt\",\"chauffeur.update_chauffeur_documennt\",\"chauffeur.destroy_chauffeur_documennt\",\"vehicule.index\"]','2025-06-30 04:13:40','2025-06-30 05:45:38',NULL);
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_group_id` bigint unsigned DEFAULT NULL,
  `type` enum('admin','admin2','admin3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_dna` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_user_group_id_foreign` (`user_group_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'admin',1,'mbola','mbola@dna.mg','2025-05-21 05:44:40','$2y$12$JyOEha4p.XTPiyXPpb9zLO4U92UQIgHLfPvgN3V0pPyJPSJ9rSYIW',1,'rjAAn86LoFfqpVMxAdECUnNA8Ek51hwk5Sa3uN8dEBMQGPYef8Osa510iRpv','2025-05-21 05:44:41','2025-05-21 05:44:41',NULL,NULL,NULL,NULL,NULL),(2,NULL,'admin',1,'admin','admin@dna.mg','2025-05-21 05:44:41','$2y$12$YPSdgNWJynmpWBG.y2VTF.ZUMxfZCAMp8py2.tXVViAinSCwVmzSG',0,NULL,'2025-05-21 05:44:41','2025-05-21 05:44:41',NULL,NULL,NULL,NULL,NULL),(3,NULL,'admin',1,'djasnive','djasnive@dna.mg','2025-05-21 05:44:41','$2y$12$NvL/1lJ9ZAGxURpf.r2tXOCPqyDVpjgWQ7PO2X72PWNG7Y2ehfW.S',0,NULL,'2025-05-21 05:44:41','2025-05-21 05:44:41',NULL,NULL,NULL,NULL,NULL),(4,NULL,'admin',1,'donnat','donnat@dna.mg','2025-05-21 05:44:41','$2y$12$6EGdrnn1JAA.pI1eVRkoHOPFrz8UjZ3PY3kxh8uqa7r.bYnJdPSyq',0,NULL,'2025-05-21 05:44:42','2025-05-21 05:44:42',NULL,NULL,NULL,NULL,NULL),(5,NULL,'admin',1,'tsiry','tsiry@gmail.com',NULL,'$2y$12$r.gZYIPS/g.thxpBZkQKa.Le8Jx4X.ouRRYuYUgle9tP3J1qPIIva',0,NULL,'2025-05-28 05:55:11','2025-05-28 05:55:11',NULL,NULL,NULL,NULL,NULL),(6,NULL,'admin',NULL,'df','d@gmail.com','2025-06-10 05:39:07','$2y$12$7Tm1fj5IuQkoc6qf.gmD1eCi5BW/g99u1HExG3rI/Hfjzodag994S',0,NULL,'2025-06-10 05:39:07','2025-06-11 09:37:04',NULL,NULL,NULL,'img/chauffeur/chauffeur_6_20250611113703uaR9K2082.webp','img/chauffeur/chauffeur_6_20250611113703uaR9K2082_thumb.webp'),(7,NULL,'admin',NULL,'fdfssss','dsds@gmail.com','2025-06-11 09:15:08','$2y$12$g/QxsNSc3AFWmh7cTRkwLOOgmKGBYIL3xcM9S6gDNDDynPc28dKAu',0,NULL,'2025-06-11 09:15:08','2025-06-11 09:15:08',NULL,NULL,NULL,NULL,NULL),(8,2,'admin',NULL,'tsiry','tsiry1@gmail.com','2025-06-11 09:39:51','$2y$12$mv7JNBUged.P1a2WR4msc.LrJqbj0stmEI4d6M2a6EYgvQHnxwWQm',1,'abCUoDz3xqQmIIAmlbitZ1giLN8gMzqVnnOpiVbnSY5pLxUJrL8t4BPZSXvU','2025-06-11 09:39:51','2025-06-11 09:55:56',NULL,NULL,NULL,'img/chauffeur/chauffeur_8_202506111139510awAK2398.webp','img/chauffeur/chauffeur_8_202506111139510awAK2398_thumb.webp'),(10,NULL,'admin',NULL,'DNA','contact@dna.mg','2025-06-12 02:05:33','$2y$12$Fz61MCjmOaO2PzVfc7AJCOZjaPCdCI/97fZJMSywJifhQ7uKMZP6S',1,NULL,'2025-06-12 02:05:33','2025-06-12 02:05:33',NULL,NULL,NULL,NULL,NULL),(11,3,'admin',NULL,'nel','nel@dna.mg','2025-06-13 04:35:44','$2y$12$BIoO.P7SBKXQT/uvVefBKe/aIOIhp99Do8dmHCoVOJMBLRbahW1zm',0,NULL,'2025-06-13 04:35:44','2025-06-13 04:56:56',NULL,NULL,NULL,NULL,NULL),(12,4,'admin',NULL,'koto','koto@gmail.com','2025-06-30 04:14:13','$2y$12$6VSLBRACmy2MQltUp9vgFekEQ59/My41FnAnBSolx94A9oipEchpS',0,NULL,'2025-06-30 04:14:13','2025-06-30 04:14:13',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicule_documents`
--

DROP TABLE IF EXISTS `vehicule_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicule_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `nom_document` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_expiration` date DEFAULT NULL,
  `fichier_jointe` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicule_documents_vehicule_id_foreign` (`vehicule_id`),
  CONSTRAINT `vehicule_documents_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicule_documents`
--

LOCK TABLES `vehicule_documents` WRITE;
/*!40000 ALTER TABLE `vehicule_documents` DISABLE KEYS */;
INSERT INTO `vehicule_documents` VALUES (1,4,'image','dfsq','2025-05-14','[{\"src\": \"img/vehicule/documents/4_805031.pdf\"}, {\"src\": \"img/vehicule/documents/4_601121.png\"}, {\"src\": \"img/vehicule/documents/4_889720.png\"}, \"img/vehicule/documents/4_422559.png\"]','2025-05-27 08:50:03','2025-06-06 15:55:21','2025-06-06 15:55:21'),(2,4,'dfsq','dqfds','2025-05-20','[\"img/vehicule/documents/4_906984.png\", \"img/vehicule/documents/4_838642.png\"]','2025-05-27 10:00:11','2025-05-27 10:00:33','2025-05-27 10:00:33'),(3,4,'dfqdfq','dqsfqsdf','2025-05-15','[{\"src\": \"img/vehicule/documents/4_955142.jpg\"}, \"img/vehicule/documents/4_384804.pdf\"]','2025-05-28 04:05:43','2025-06-06 15:57:43','2025-06-06 15:57:43'),(4,4,'tstes','teste testes testes tsete','2025-04-09','[{\"src\": \"img/vehicule/documents/4_924162.png\"}, {\"src\": \"img/vehicule/documents/4_883764.png\"}, \"img/vehicule/documents/4_814744.pdf\"]','2025-05-28 04:38:21','2025-06-06 16:11:46','2025-06-06 16:11:46'),(5,4,'nom du document','fdfsddfd','2025-06-06','[\"/img/vehicule/documents/4_833813.jpg\", \"/img/vehicule/documents/4_317763.jpg\", \"/img/vehicule/documents/4_956907.txt\", \"img/vehicule/documents/4_353656.jpeg\"]','2025-06-06 13:57:13','2025-07-17 08:56:36',NULL),(6,4,'ancien document','enocren un nouveau teste pour le dfsif','2025-06-13','[\"img/vehicule/documents/4_837693.jpeg\", \"img/vehicule/documents/4_141296.jpeg\", \"img/vehicule/documents/4_261817.jpg\", \"img/vehicule/documents/4_879484.jpg\", \"img/vehicule/documents/4_304279.jpeg\"]','2025-06-06 13:59:35','2025-07-17 08:55:10',NULL),(7,4,'dfgdg','dsffsq','2025-06-25','[\"/img/vehicule/documents/4_982174.png\", \"/img/vehicule/documents/4_311126.png\", \"/img/vehicule/documents/4_434641.jpg\", \"/img/vehicule/documents/4_762463.png\", \"/img/vehicule/documents/4_403888.jpg\", \"img/vehicule/documents/4_606947.png\"]','2025-06-06 15:46:18','2025-06-06 16:11:53','2025-06-06 16:11:53'),(8,19,'document 1','testes','2025-07-09','[\"/img/vehicule/documents/1_268060.jpeg\", \"/img/vehicule/documents/1_201251.jpeg\", \"/img/vehicule/documents/1_672218.png\"]','2025-07-09 06:28:08','2025-07-17 05:36:29',NULL),(9,9,'df','qsfq','2025-07-08','[\"/img/vehicule/documents/1_783856.png\"]','2025-07-09 06:29:00','2025-07-17 05:36:19',NULL),(10,12,'dfqfq','fqfdqfq','2025-07-09','[\"/img/vehicule/documents/1_136219.jpeg\"]','2025-07-09 06:30:41','2025-07-17 05:36:11',NULL),(11,4,'d','teste','2025-07-31','[\"img/vehicule/documents/4_800498.jpeg\"]','2025-07-17 05:22:25','2025-07-17 05:22:25',NULL),(12,13,'teste teste','teste','2025-08-30','[\"img/vehicule/documents/13_251904.jpeg\", \"img/vehicule/documents/13_822222.jpeg\", \"img/vehicule/documents/13_311521.png\", \"img/vehicule/documents/13_172408.jpg\", \"img/vehicule/documents/13_119633.jpeg\"]','2025-07-17 05:22:51','2025-07-17 05:22:51',NULL),(13,19,'nouveau','test teste teste teste test teste','2025-07-17','[\"/img/vehicule/documents/19_345124.jpg\", \"/img/vehicule/documents/19_742921.jpg\", \"/img/vehicule/documents/19_472799.jpg\", \"/img/vehicule/documents/19_880545.jpg\", \"/img/vehicule/documents/19_619037.jpg\", \"/img/vehicule/documents/19_459359.jpg\", \"img/vehicule/documents/19_946879.jpeg\"]','2025-07-17 08:57:24','2025-07-17 08:57:49',NULL);
/*!40000 ALTER TABLE `vehicule_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicule_elements`
--

DROP TABLE IF EXISTS `vehicule_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicule_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `emplacement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_piece` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicule_elements_vehicule_id_foreign` (`vehicule_id`),
  CONSTRAINT `vehicule_elements_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicule_elements`
--

LOCK TABLES `vehicule_elements` WRITE;
/*!40000 ALTER TABLE `vehicule_elements` DISABLE KEYS */;
INSERT INTO `vehicule_elements` VALUES (1,4,'element 1','tite','tdfsd','xx','xx','2025-05-23 05:57:19','2025-05-23 06:04:55','2025-05-23 06:04:55'),(2,4,'element 2','adfs','adfsq','xx','xxx','2025-05-23 05:57:19','2025-05-23 06:04:55','2025-05-23 06:04:55'),(3,4,'element 1','tite','tdfsd','xx','xx','2025-05-23 06:04:55','2025-05-23 06:06:58','2025-05-23 06:06:58'),(4,4,'element 2','adfs','adfsq','xx','xxx','2025-05-23 06:04:55','2025-05-23 06:06:58','2025-05-23 06:06:58'),(5,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-23 06:06:58','2025-05-26 05:52:47','2025-05-26 05:52:47'),(6,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-23 06:06:58','2025-05-26 05:52:47','2025-05-26 05:52:47'),(7,5,'element 1','tite','tdfsd','xdfq','fqsd','2025-05-26 02:10:19','2025-05-26 06:19:54','2025-05-26 06:19:54'),(8,5,'element 2','adfs','adfsq','ffere','fqsdf','2025-05-26 02:10:19','2025-05-26 06:19:54','2025-05-26 06:19:54'),(9,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 05:52:50','2025-05-26 05:53:10','2025-05-26 05:53:10'),(10,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 05:52:51','2025-05-26 05:53:10','2025-05-26 05:53:10'),(11,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 05:53:11','2025-05-26 07:06:04','2025-05-26 07:06:04'),(12,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 05:53:11','2025-05-26 07:06:04','2025-05-26 07:06:04'),(13,5,'element 1','tite','tdfsd','xdfq','fqsd','2025-05-26 06:19:56','2025-05-26 08:30:12','2025-05-26 08:30:12'),(14,5,'element 2','adfs','adfsq','ffere','fqsdf','2025-05-26 06:19:56','2025-05-26 08:30:12','2025-05-26 08:30:12'),(15,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 07:06:05','2025-05-26 07:34:49','2025-05-26 07:34:49'),(16,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 07:06:05','2025-05-26 07:34:49','2025-05-26 07:34:49'),(17,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 07:34:49','2025-05-26 08:28:52','2025-05-26 08:28:52'),(18,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 07:34:49','2025-05-26 08:28:52','2025-05-26 08:28:52'),(19,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 08:28:52','2025-05-26 08:37:14','2025-05-26 08:37:14'),(20,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 08:28:52','2025-05-26 08:37:14','2025-05-26 08:37:14'),(21,5,'element 1','tite','tdfsd','xdfq','fqsd','2025-05-26 08:30:12','2025-05-26 08:30:32','2025-05-26 08:30:32'),(22,5,'element 2','adfs','adfsq','ffere','fqsdf','2025-05-26 08:30:12','2025-05-26 08:30:32','2025-05-26 08:30:32'),(23,5,'element 1','tite','tdfsd','xdfq','fqsd','2025-05-26 08:30:32','2025-05-26 08:31:23','2025-05-26 08:31:23'),(24,5,'element 2','adfs','adfsq','ffere','fqsdf','2025-05-26 08:30:32','2025-05-26 08:31:23','2025-05-26 08:31:23'),(25,5,'element 1','tite','tdfsd','xdfq','fqsd','2025-05-26 08:31:24','2025-05-26 08:31:24',NULL),(26,5,'element 2','adfs','adfsq','ffere','fqsdf','2025-05-26 08:31:24','2025-05-26 08:31:24',NULL),(27,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 08:37:14','2025-05-26 16:13:04','2025-05-26 16:13:04'),(28,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 08:37:14','2025-05-26 16:13:04','2025-05-26 16:13:04'),(29,4,'arrière','tgeste','teste','1050','neuve','2025-05-26 08:37:14','2025-05-26 16:13:04','2025-05-26 16:13:04'),(30,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-26 16:13:09','2025-05-27 10:19:56','2025-05-27 10:19:56'),(31,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-26 16:13:09','2025-05-27 10:19:56','2025-05-27 10:19:56'),(32,4,'arrière','tgeste','teste','1050','neuve','2025-05-26 16:13:09','2025-05-27 10:19:56','2025-05-27 10:19:56'),(33,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-05-27 10:19:57','2025-06-04 06:01:26','2025-06-04 06:01:26'),(34,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-05-27 10:19:58','2025-06-04 06:01:26','2025-06-04 06:01:26'),(35,4,'arrière','tgeste','teste','1050','neuve','2025-05-27 10:19:58','2025-06-04 06:01:26','2025-06-04 06:01:26'),(36,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-06-04 06:01:32','2025-06-04 06:08:26','2025-06-04 06:08:26'),(37,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-06-04 06:01:33','2025-06-04 06:08:26','2025-06-04 06:08:26'),(38,4,'arrière','tgeste','teste','1050','neuve','2025-06-04 06:01:33','2025-06-04 06:08:26','2025-06-04 06:08:26'),(39,4,'Avant','Autopilot','AP3.0',NULL,'neuve','2025-06-04 06:08:26','2025-06-04 09:02:22','2025-06-04 09:02:22'),(40,4,'Avant','Jantes 21\\','Jantes 21\\','01252x55','neuve','2025-06-04 06:08:26','2025-06-04 09:02:22','2025-06-04 09:02:22'),(41,4,'arrière','tgeste','teste','1050','neuve','2025-06-04 06:08:26','2025-06-04 09:02:22','2025-06-04 09:02:22'),(42,4,'xx','xx','xxx',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(43,4,'1','1','1',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(44,4,'2','2','2',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(45,4,'3','3','3',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(46,4,'4','4','4',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(47,4,'5','5','5',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(48,4,'6','6','6',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(49,4,'7','7','7',NULL,NULL,'2025-06-04 09:02:23','2025-06-05 09:45:09','2025-06-05 09:45:09'),(50,4,'xx','xx','xxx',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(51,4,'1','1','1',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(52,4,'2','2','2',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(53,4,'3','3','3',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(54,4,'4','4','4',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(55,4,'5','5','5',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(56,4,'6','6','6',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(57,4,'7','7','7',NULL,NULL,'2025-06-05 09:45:09','2025-06-05 16:31:33','2025-06-05 16:31:33'),(58,4,'xx','xx','xxx',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(59,4,'1','1','1',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(60,4,'2','2','2',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(61,4,'3','3','3',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(62,4,'4','4','4',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(63,4,'5','5','5',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(64,4,'6','6','6',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(65,4,'7','7','7',NULL,NULL,'2025-06-05 16:31:34','2025-06-16 09:56:06','2025-06-16 09:56:06'),(67,16,'xx','xx','xxx',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(68,16,'1','1','1',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(69,16,'2','2','2',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(70,16,'3','3','3',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(71,16,'4','4','4',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(72,16,'5','5','5',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(73,16,'6','6','6',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(74,16,'7','7','7',NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL),(75,15,'element 1','tite','tdfsd',NULL,NULL,'2025-06-13 09:49:16','2025-06-13 09:49:16',NULL),(76,15,'element 2','adfs','adfsq',NULL,NULL,'2025-06-13 09:49:16','2025-06-13 09:49:16',NULL),(77,4,'ffdfs','qfqd','fdq','dfqdfq','fqdfq','2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(78,4,'xx','xx','xxx',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(79,4,'1','1','1',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(80,4,'2','2','2',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(81,4,'3','3','3',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(82,4,'4','4','4',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(83,4,'5','5','5',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(84,4,'6','6','6',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(85,4,'7','7','7',NULL,NULL,'2025-06-16 09:56:07','2025-07-01 05:14:49','2025-07-01 05:14:49'),(86,19,'element 1','tite','tdfsd',NULL,NULL,'2025-07-01 05:13:54','2025-07-01 05:13:54',NULL),(87,19,'element 2','adfs','adfsq',NULL,NULL,'2025-07-01 05:13:54','2025-07-01 05:13:54',NULL),(88,4,'ffdfs','qfqd','fdq','dfqdfq','fqdfq','2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(89,4,'xx','xx','xxx',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(90,4,'1','1','1',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(91,4,'2','2','2',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(92,4,'3','3','3',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(93,4,'4','4','4',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(94,4,'5','5','5',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(95,4,'6','6','6',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(96,4,'7','7','7',NULL,NULL,'2025-07-01 05:14:49','2025-07-01 05:15:41','2025-07-01 05:15:41'),(97,4,'ffdfs','qfqd','fdq','dfqdfq','fqdfq','2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(98,4,'xx','xx','xxx','ggd','neuf','2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(99,4,'1','1','1',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(100,4,'2','2','2',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(101,4,'3','3','3',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(102,4,'4','4','4',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(103,4,'5','5','5',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(104,4,'6','6','6',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL),(105,4,'7','7','7',NULL,NULL,'2025-07-01 05:15:41','2025-07-01 05:15:41',NULL);
/*!40000 ALTER TABLE `vehicule_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicule_photo_listes`
--

DROP TABLE IF EXISTS `vehicule_photo_listes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicule_photo_listes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicule_photo_listes`
--

LOCK TABLES `vehicule_photo_listes` WRITE;
/*!40000 ALTER TABLE `vehicule_photo_listes` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicule_photo_listes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicule_photos`
--

DROP TABLE IF EXISTS `vehicule_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicule_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `type_element` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_prise_photo` date DEFAULT NULL,
  `liste_image` json DEFAULT NULL,
  `etat_vehicule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicule_photos`
--

LOCK TABLES `vehicule_photos` WRITE;
/*!40000 ALTER TABLE `vehicule_photos` DISABLE KEYS */;
INSERT INTO `vehicule_photos` VALUES (1,4,'Controle Technique','2025-05-15','[{\"main\": \"img/vehicule/vehicule_1_202505261931567riQq992.webp\"}, {\"main\": \"img/vehicule/vehicule_1_20250526193158TUGA02252.webp\"}, {\"main\": \"img/vehicule/vehicule_1_20250526193159HRZuO849.webp\"}, {\"main\": \"img/vehicule/vehicule_1_20250526193200xT3Ep816.webp\"}]','cf','2025-05-26 15:37:18','2025-06-06 16:00:42','2025-06-06 16:00:42'),(2,4,'Controle Technique','2025-05-27','[{\"main\": \"img/vehicule/vehicule_2_20250527125704.webp\"}, {\"main\": \"img/vehicule/vehicule_2_20250527125730.webp\"}]','dsfqfdq','2025-05-27 09:57:04','2025-05-27 09:58:25','2025-05-27 09:58:25'),(13,4,'Controle Technique','2025-05-15','[{\"main\": \"img/vehicule/vehicule_13_20250605191203aw8D01578.webp\"}]','mimoza','2025-05-28 04:05:21','2025-06-05 17:12:03',NULL),(14,4,'Entretien','2025-05-06','[{\"main\": \"img/vehicule/vehicule_14_20250528071236.webp\"}, {\"main\": \"img/vehicule/vehicule_14_20250528071237.webp\"}]','dfsqfqsdf','2025-05-28 04:12:36','2025-06-06 16:12:00','2025-06-06 16:12:00'),(15,4,'Controle Technique','2025-05-21','[{\"main\": \"img/vehicule/vehicule_15_202505300653233b2ff2136.webp\"}, {\"main\": \"img/vehicule/vehicule_15_20250530065337TWFxg1814.webp\"}]','fgkkfsdq df \r\nflkjsdkfjljds','2025-05-28 04:19:41','2025-05-30 03:53:40',NULL),(16,4,'Constat','2025-06-05','[{\"main\": \"img/vehicule/vehicule_16_20250605114544OU5xn277.webp\"}, {\"main\": \"img/vehicule/vehicule_16_20250605114547b9xTn511.webp\"}, {\"main\": \"img/vehicule/vehicule_16_20250605114547LnEJd261.webp\"}]','teste','2025-06-05 09:45:44','2025-06-05 09:45:47',NULL),(17,4,'Photo origine','2025-06-07','[{\"main\": \"img/vehicule/vehicule_17_20250605114633eDmEI360.webp\"}, {\"main\": \"img/vehicule/vehicule_17_20250605114633AskyM1535.webp\"}, {\"main\": \"img/vehicule/vehicule_17_20250605114633e7z0P1088.webp\"}, {\"main\": \"img/vehicule/vehicule_17_20250605114633t19Ar1925.webp\"}, {\"main\": \"img/vehicule/vehicule_17_20250605114633GyNAk408.webp\"}, {\"main\": \"img/vehicule/vehicule_17_20250605114633171Tq2045.webp\"}]','image d\'origine du véhicule','2025-06-05 09:46:33','2025-06-05 09:46:33',NULL),(18,4,'Controle Technique','2025-06-14','[{\"main\": \"img/vehicule/vehicule_18_20250605183342sJbOL2159.webp\"}, {\"main\": \"img/vehicule/vehicule_18_202506051833447XNUP1493.webp\"}, {\"main\": \"img/vehicule/vehicule_18_20250605183344Tb4b6284.webp\"}, {\"main\": \"img/vehicule/vehicule_18_20250605183344Je5ZM103.webp\"}]','dds','2025-06-05 16:33:42','2025-06-05 16:33:44',NULL),(19,4,'Controle Technique','2025-06-14','[{\"main\": \"img/vehicule/vehicule_19_20250605183535QU0iX516.webp\"}, {\"main\": \"img/vehicule/vehicule_19_20250605183535Gg2Gc1268.webp\"}, {\"main\": \"img/vehicule/vehicule_19_20250605183535iZ6I81598.webp\"}, {\"main\": \"img/vehicule/vehicule_19_20250605183535IT8VS773.webp\"}]','dds','2025-06-05 16:35:35','2025-06-05 16:35:35',NULL),(20,4,'Entretien','2025-06-12','[{\"main\": \"img/vehicule/vehicule_20_20250605183736677JA2408.webp\"}, {\"main\": \"img/vehicule/vehicule_20_20250605183736R3SwT855.webp\"}, {\"main\": \"img/vehicule/vehicule_20_20250605183736k48Lu1718.webp\"}]','dfsdfsd','2025-06-05 16:37:36','2025-06-05 16:37:36',NULL),(21,4,'Entretien','2025-06-11','[{\"main\": \"img/vehicule/vehicule_21_20250605183908cuQpw300.webp\"}, {\"main\": \"img/vehicule/vehicule_21_20250605183908JqEzN991.webp\"}, {\"main\": \"img/vehicule/vehicule_21_20250605183908aC7CO345.webp\"}]','dfs','2025-06-05 16:39:08','2025-06-05 16:39:08',NULL),(22,4,'Entretien','2025-06-11','[{\"main\": \"img/vehicule/vehicule_22_20250605183946v1nCX1958.webp\"}, {\"main\": \"img/vehicule/vehicule_22_20250605183946PP4Zp361.webp\"}, {\"main\": \"img/vehicule/vehicule_22_20250605183946QqPCa1511.webp\"}, {\"main\": \"img/vehicule/vehicule_22_20250605183946Jmwoh1364.webp\"}]','dfsff','2025-06-05 16:39:46','2025-06-05 16:39:46',NULL),(23,4,'Photo origine','2025-06-28','[{\"main\": \"img/vehicule/vehicule_23_20250606122226vpGeA312.webp\"}, {\"main\": \"img/vehicule/vehicule_23_202506061222291dhh92354.webp\"}, {\"main\": \"img/vehicule/vehicule_23_202506061222299SBJ433.webp\"}, {\"main\": \"img/vehicule/vehicule_23_20250606122229dTZ7h1141.webp\"}, {\"main\": \"img/vehicule/vehicule_23_20250606122229sZ6hs1083.webp\"}, {\"main\": \"img/vehicule/vehicule_23_20250606122229LYMMI750.webp\"}, {\"main\": \"img/vehicule/vehicule_23_20250606122229s3I9z1221.webp\"}]','mbolatiana andriantisy','2025-06-05 16:40:24','2025-06-06 10:22:29',NULL),(24,4,'Constat','2025-07-01','[{\"main\": \"img/vehicule/vehicule_24_20250701071924KDamU1804.webp\"}, {\"main\": \"img/vehicule/vehicule_24_20250701071928MGqii1046.webp\"}, {\"main\": \"img/vehicule/vehicule_24_20250701071928aNzty1849.webp\"}, {\"main\": \"img/vehicule/vehicule_24_20250701071928fPP65142.webp\"}, {\"main\": \"img/vehicule/vehicule_24_20250701071928aCBAL1295.webp\"}, {\"main\": \"img/vehicule/vehicule_24_20250701071929cEKGZ1220.webp\"}]',NULL,'2025-07-01 05:19:24','2025-07-01 05:19:29',NULL);
/*!40000 ALTER TABLE `vehicule_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `param_element_id` bigint unsigned DEFAULT NULL,
  `immatriculation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marque` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modele` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_chassis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `couleur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_carte_grise` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nbre_porte` double NOT NULL DEFAULT '0',
  `valeur_initial` double NOT NULL DEFAULT '0',
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumg_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remorque_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicules_param_element_id_foreign` (`param_element_id`),
  KEY `vehicules_remorque_id_foreign` (`remorque_id`),
  CONSTRAINT `vehicules_param_element_id_foreign` FOREIGN KEY (`param_element_id`) REFERENCES `param_elements` (`id`),
  CONSTRAINT `vehicules_remorque_id_foreign` FOREIGN KEY (`remorque_id`) REFERENCES `remorques` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicules`
--

LOCK TABLES `vehicules` WRITE;
/*!40000 ALTER TABLE `vehicules` DISABLE KEYS */;
INSERT INTO `vehicules` VALUES (1,5,'d','d','d','d','d','d',2,1,NULL,NULL,NULL,'2025-05-23 05:49:36','2025-05-23 06:51:55','2025-05-23 06:51:55',NULL),(4,7,'AB-123-CD','Model S','marque 1','5YJSA1E20HF123456','Rouge','AA123BB456',0,1250000,NULL,NULL,NULL,'2025-05-23 05:57:19','2025-07-01 05:15:41',NULL,NULL),(5,5,'145XD-VD','teste','fqdfqs','dfqfdqsfq','red','11512',2,14250000,NULL,NULL,NULL,'2025-05-26 02:10:19','2025-05-26 08:31:24',NULL,NULL),(6,6,'ds','df','dsfs','df','sdf','4545445',2,12,NULL,NULL,NULL,'2025-06-06 16:03:05','2025-06-06 16:03:12','2025-06-06 16:03:12',NULL),(9,NULL,'testeste','dfs','dfs','qd','f','dfqsd',5,1000000,NULL,NULL,NULL,'2025-06-13 09:41:14','2025-06-13 09:41:14',NULL,NULL),(10,5,'etests','dfq','qfe','dfq','dfsdf','dfsd',2,1400000,NULL,NULL,NULL,'2025-06-13 09:41:39','2025-06-13 09:41:39',NULL,NULL),(11,5,'1215²','1ddf','gjk','415986','5745','879245',2,150000,NULL,NULL,NULL,'2025-06-13 09:44:19','2025-06-13 09:44:19',NULL,NULL),(12,5,'mbolaik','o','d7','8','448','484',5,82852000,NULL,NULL,NULL,'2025-06-13 09:45:52','2025-06-13 09:45:52',NULL,NULL),(13,NULL,'165x565','sd','df','00236..3','d','115545',2,145650,NULL,NULL,NULL,'2025-06-13 09:46:38','2025-06-13 09:46:38',NULL,NULL),(14,7,'1252555','1414','144','4114455','14','14',4,4525,NULL,NULL,NULL,'2025-06-13 09:47:00','2025-06-13 09:47:00',NULL,NULL),(15,5,'7787','48959','5987','95+9','595','9',5,5555,NULL,NULL,NULL,'2025-06-13 09:48:42','2025-06-13 09:49:16',NULL,NULL),(16,7,'ddffqtz','hdhh','fgds','sgs','fs','gsfg',0,0,NULL,NULL,NULL,'2025-06-13 09:48:57','2025-06-13 09:48:57',NULL,NULL),(17,NULL,'xxx--11','marque teste 11','modele teste','chassis ----111','red','xxxxx',2,10000000,NULL,NULL,NULL,'2025-06-16 06:48:10','2025-06-16 06:48:10',NULL,NULL),(18,NULL,'01525DF','marque teste 11','modele teste','xfdftrtfgf','teste couleur','xxxx',2,2600000,NULL,NULL,NULL,'2025-06-16 06:49:12','2025-06-16 07:55:28',NULL,NULL),(19,5,'xxxxoooxx','nouveau marque','modele teste','xx-xxx-xxkjkjxx','rouge','teste-xxxxx',2,10000000,NULL,NULL,NULL,'2025-07-01 05:13:54','2025-07-01 05:13:54',NULL,NULL),(20,NULL,'111111111','marque teste 11','fqdfqs','fsfs','5745','estestes',2,100000,NULL,NULL,NULL,'2025-07-09 09:34:18','2025-07-09 09:34:18',NULL,NULL),(21,NULL,'ttr','48959','d7','rrr','5745','rrrrrr',2,0,NULL,NULL,NULL,'2025-07-09 09:43:14','2025-07-09 09:52:02',NULL,4);
/*!40000 ALTER TABLE `vehicules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voyage_charges`
--

DROP TABLE IF EXISTS `voyage_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyage_charges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` double DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `voyage_id` bigint unsigned DEFAULT NULL,
  `tresorerie_mouvement_id` bigint unsigned DEFAULT NULL,
  `tresorerie_id` bigint unsigned DEFAULT NULL,
  `mode_paiement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voyage_charges_voyage_id_foreign` (`voyage_id`),
  KEY `voyage_charges_tresorerie_mouvement_id_foreign` (`tresorerie_mouvement_id`),
  KEY `voyage_charges_tresorerie_id_foreign` (`tresorerie_id`),
  CONSTRAINT `voyage_charges_tresorerie_id_foreign` FOREIGN KEY (`tresorerie_id`) REFERENCES `tresoreries` (`id`),
  CONSTRAINT `voyage_charges_tresorerie_mouvement_id_foreign` FOREIGN KEY (`tresorerie_mouvement_id`) REFERENCES `tresorerie_mouvements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `voyage_charges_voyage_id_foreign` FOREIGN KEY (`voyage_id`) REFERENCES `voyages` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyage_charges`
--

LOCK TABLES `voyage_charges` WRITE;
/*!40000 ALTER TABLE `voyage_charges` DISABLE KEYS */;
/*!40000 ALTER TABLE `voyage_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voyage_marchandises`
--

DROP TABLE IF EXISTS `voyage_marchandises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyage_marchandises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `voyage_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voyage_marchandises_voyage_id_foreign` (`voyage_id`),
  CONSTRAINT `voyage_marchandises_voyage_id_foreign` FOREIGN KEY (`voyage_id`) REFERENCES `voyages` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyage_marchandises`
--

LOCK TABLES `voyage_marchandises` WRITE;
/*!40000 ALTER TABLE `voyage_marchandises` DISABLE KEYS */;
/*!40000 ALTER TABLE `voyage_marchandises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voyage_primes`
--

DROP TABLE IF EXISTS `voyage_primes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyage_primes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voyage_id` bigint unsigned NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voyage_primes_voyage_id_foreign` (`voyage_id`),
  CONSTRAINT `voyage_primes_voyage_id_foreign` FOREIGN KEY (`voyage_id`) REFERENCES `voyages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyage_primes`
--

LOCK TABLES `voyage_primes` WRITE;
/*!40000 ALTER TABLE `voyage_primes` DISABLE KEYS */;
/*!40000 ALTER TABLE `voyage_primes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voyages`
--

DROP TABLE IF EXISTS `voyages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voyages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint unsigned DEFAULT NULL,
  `vehicule_id` bigint unsigned DEFAULT NULL,
  `chauffeur_id` bigint unsigned DEFAULT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_trajet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_reception` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarif_ttc` double DEFAULT NULL,
  `tarif_ht` double DEFAULT NULL,
  `tarif_tva` double DEFAULT NULL,
  `nbr_jour` int DEFAULT NULL,
  `mobilisation` double DEFAULT NULL,
  `etat_vehicule_avant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_vehicule_apres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant` double DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_voyage` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remorque_id` bigint unsigned DEFAULT NULL,
  `numero_voyage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_numero_voyage` int NOT NULL,
  `facture_client_id` bigint unsigned DEFAULT NULL,
  `valeur_tva` double DEFAULT NULL,
  `montant_tva` double DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `depart` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kilometrage` int DEFAULT NULL,
  `tarif_ht_total` double DEFAULT NULL,
  `montant_ht` double DEFAULT NULL,
  `remise` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voyages_date_voyage_index` (`date_voyage`),
  KEY `voyages_reservation_id_foreign` (`reservation_id`),
  KEY `voyages_vehicule_id_foreign` (`vehicule_id`),
  KEY `voyages_chauffeur_id_foreign` (`chauffeur_id`),
  KEY `voyages_remorque_id_foreign` (`remorque_id`),
  KEY `voyages_facture_client_id_foreign` (`facture_client_id`),
  CONSTRAINT `voyages_chauffeur_id_foreign` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voyages_facture_client_id_foreign` FOREIGN KEY (`facture_client_id`) REFERENCES `facture_clients` (`id`),
  CONSTRAINT `voyages_remorque_id_foreign` FOREIGN KEY (`remorque_id`) REFERENCES `remorques` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voyages_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voyages_vehicule_id_foreign` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voyages`
--

LOCK TABLES `voyages` WRITE;
/*!40000 ALTER TABLE `voyages` DISABLE KEYS */;
INSERT INTO `voyages` VALUES (18,12,4,4,'Tana','local',NULL,5275000,4500000,NULL,5,155000,NULL,NULL,5275000,'sfs','2025-08-05',NULL,'2025-08-06 09:34:43','2025-08-06 09:48:46',1,'VOY-001/08-2025',1,35,0,0,'Voyage prévu pour le 06/08/2025. qui partira de Toamasina vers Tana, avec le camion Model S marque 1 (AB-123-CD), conduit par Mr ratsitobaina mbolatiana.','Toamasina',NULL,4500000,5275000,NULL),(19,12,5,4,'Tana','regional',NULL,6775000,6000000,NULL,5,155000,NULL,NULL,6775000,NULL,'2025-08-06',NULL,'2025-08-06 09:50:23','2025-08-06 09:54:31',2,'VOY-002/08-2025',2,37,NULL,0,'Voyage prévu pour le 06/08/2025. qui partira de Toamasina vers Tana, avec le camion teste fqdfqs (145XD-VD), conduit par Mr ratsitobaina mbolatiana.','Toamasina',NULL,6000000,6775000,NULL),(20,13,9,9,'Tana','local','complet',32500000,6500000,NULL,NULL,155000,NULL,NULL,32500000,NULL,'2025-08-08',NULL,'2025-08-06 10:06:38','2025-08-06 10:10:50',1,'VOY-003/08-2025',3,38,NULL,0,'Voyage prévu pour le 08/08/2025. qui partira de Tana vers Tana, avec le camion dfs dfs (testeste), conduit par Mr Dieu donné.','Tana',5,32500000,32500000,NULL),(21,13,9,9,'Toamasina','regional',NULL,4455000,4300000,NULL,1,155000,NULL,NULL,4455000,NULL,'2025-08-07',NULL,'2025-08-06 10:07:16','2025-08-06 10:10:50',3,'VOY-004/08-2025',4,38,NULL,0,'Voyage prévu pour le 08/08/2025. qui partira de Tana vers Toamasina, avec le camion dfs dfs (testeste), conduit par Mr Dieu donné.','Tana',NULL,4300000,4455000,NULL),(22,13,9,9,'Tana','local',NULL,4300000,4300000,NULL,NULL,155000,NULL,NULL,4300000,NULL,'2025-08-07',NULL,'2025-08-06 10:10:19','2025-08-06 10:10:50',3,'VOY-005/08-2025',5,38,NULL,0,'Voyage prévu pour le 08/08/2025. qui partira de Tana vers Tana, avec le camion dfs dfs (testeste), conduit par Mr Dieu donné.','Tana',NULL,4300000,4300000,NULL);
/*!40000 ALTER TABLE `voyages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 12:50:18
