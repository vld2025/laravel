-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: vld_service
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

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
-- Table structure for table `automazione_pdf`
--

DROP TABLE IF EXISTS `automazione_pdf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automazione_pdf` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attiva` tinyint(1) NOT NULL DEFAULT '0',
  `giorno_invio` int NOT NULL DEFAULT '1',
  `ora_invio` time NOT NULL DEFAULT '09:00:00',
  `mese_riferimento` enum('corrente','precedente') COLLATE utf8mb4_unicode_ci DEFAULT 'precedente',
  `email_destinatari` json NOT NULL,
  `email_oggetto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Spese Mensili - {mese} {anno}',
  `email_messaggio` text COLLATE utf8mb4_unicode_ci,
  `utenti_inclusi` json DEFAULT NULL,
  `solo_con_spese` tinyint(1) NOT NULL DEFAULT '1',
  `ultima_esecuzione` timestamp NULL DEFAULT NULL,
  `ultimo_risultato` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automazione_pdf`
--

LOCK TABLES `automazione_pdf` WRITE;
/*!40000 ALTER TABLE `automazione_pdf` DISABLE KEYS */;
INSERT INTO `automazione_pdf` VALUES (1,1,28,'01:16:00','precedente','[\"vlad@vldservice.ch\"]','Spese Mensili VLD Service - {mese} {anno}','In allegato trovate il riepilogo delle spese mensili con tutti i documenti allegati.','[]',0,'2025-06-28 01:16:02','{\"errori\": [], \"timestamp\": \"2025-06-27T23:16:02.932693Z\", \"pdf_generati\": 1, \"utenti_processati\": 4}','2025-06-24 10:53:29','2025-06-28 01:16:02');
/*!40000 ALTER TABLE `automazione_pdf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `automazione_reports`
--

DROP TABLE IF EXISTS `automazione_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automazione_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome configurazione',
  `attivo` tinyint(1) NOT NULL DEFAULT '0',
  `ora_invio` time NOT NULL COMMENT 'Ora giornaliera di invio (es: 18:00)',
  `email_destinatari` json NOT NULL COMMENT 'Array di email destinatari',
  `lingue` json NOT NULL COMMENT 'Array di lingue da includere (it,en,de,ru)',
  `solo_giorni_lavorativi` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Invia solo nei giorni lavorativi',
  `raggruppa_per_giorno` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Raggruppa tutti i report del giorno in una singola email',
  `includi_dettagli_ore` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Includi dettagli ore e chilometri nelle email',
  `formato_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf' COMMENT 'Formato file: pdf, excel',
  `note` text COLLATE utf8mb4_unicode_ci,
  `ultimo_invio` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automazione_reports`
--

LOCK TABLES `automazione_reports` WRITE;
/*!40000 ALTER TABLE `automazione_reports` DISABLE KEYS */;
INSERT INTO `automazione_reports` VALUES (1,'rep',1,'02:23:00','[\"vlad@vldservice.ch\"]','[\"it\", \"de\", \"en\", \"ru\"]',0,1,0,'pdf',NULL,'2025-06-25 19:00:52','2025-06-25 14:32:27','2025-06-28 02:28:35');
/*!40000 ALTER TABLE `automazione_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('vld_service_gmbh_cache_356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1751145793),('vld_service_gmbh_cache_356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1751145793;',1751145793),('vld_service_gmbh_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0','i:1;',1751146655),('vld_service_gmbh_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0:timer','i:1751146655;',1751146655),('vld_service_gmbh_cache_livewire-rate-limiter:48f3b3f736091d89c7c96f1112136ffc4dc87914','i:1;',1751138539),('vld_service_gmbh_cache_livewire-rate-limiter:48f3b3f736091d89c7c96f1112136ffc4dc87914:timer','i:1751138539;',1751138539);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `clienti`
--

DROP TABLE IF EXISTS `clienti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clienti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `committente_id` bigint unsigned NOT NULL,
  `indirizzo` text COLLATE utf8mb4_unicode_ci,
  `dati_bancari` json DEFAULT NULL,
  `informazioni` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clienti_committente_id_foreign` (`committente_id`),
  CONSTRAINT `clienti_committente_id_foreign` FOREIGN KEY (`committente_id`) REFERENCES `committenti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clienti`
--

LOCK TABLES `clienti` WRITE;
/*!40000 ALTER TABLE `clienti` DISABLE KEYS */;
INSERT INTO `clienti` VALUES (2,'Bell',2,NULL,'[]',NULL,'2025-06-24 08:16:52','2025-06-24 08:16:52'),(3,'Formaggio ',4,NULL,'[]',NULL,'2025-06-26 11:48:07','2025-06-26 11:48:07'),(4,'col',2,NULL,'[]',NULL,'2025-06-26 19:26:44','2025-06-26 19:26:44'),(5,'aaaa',2,NULL,'[]',NULL,'2025-06-28 02:18:00','2025-06-28 02:18:00');
/*!40000 ALTER TABLE `clienti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commesse`
--

DROP TABLE IF EXISTS `commesse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commesse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `cliente_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commesse_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `commesse_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commesse`
--

LOCK TABLES `commesse` WRITE;
/*!40000 ALTER TABLE `commesse` DISABLE KEYS */;
INSERT INTO `commesse` VALUES (2,'i890',NULL,2,'2025-06-24 08:17:12','2025-06-24 08:17:12'),(3,'Rrrrr',NULL,3,'2025-06-26 11:48:21','2025-06-26 11:48:21'),(4,'cx vx',NULL,2,'2025-06-28 02:17:41','2025-06-28 02:17:41');
/*!40000 ALTER TABLE `commesse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committenti`
--

DROP TABLE IF EXISTS `committenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committenti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partita_iva` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` text COLLATE utf8mb4_unicode_ci,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dati_bancari` json DEFAULT NULL,
  `informazioni` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committenti`
--

LOCK TABLES `committenti` WRITE;
/*!40000 ALTER TABLE `committenti` DISABLE KEYS */;
INSERT INTO `committenti` VALUES (2,'Colussi','123456',NULL,NULL,'committenti/loghi/01JYGFN74BNXC7JXX3V3KY8Z1J.png','[]',NULL,'2025-06-24 08:06:59','2025-06-24 09:04:40'),(3,'test',NULL,NULL,NULL,NULL,'[]',NULL,'2025-06-25 00:30:40','2025-06-25 00:30:40'),(4,'Emmi',NULL,NULL,NULL,NULL,'[]',NULL,'2025-06-26 11:47:35','2025-06-26 11:47:35'),(5,'dddf',NULL,NULL,NULL,NULL,'[]',NULL,'2025-06-28 02:18:19','2025-06-28 02:18:19');
/*!40000 ALTER TABLE `committenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documenti`
--

DROP TABLE IF EXISTS `documenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documenti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `caricato_da` bigint unsigned NOT NULL,
  `tipo` enum('busta_paga','personale','aziendale') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documenti_caricato_da_foreign` (`caricato_da`),
  KEY `documenti_user_id_tipo_index` (`user_id`,`tipo`),
  KEY `documenti_tipo_created_at_index` (`tipo`,`created_at`),
  CONSTRAINT `documenti_caricato_da_foreign` FOREIGN KEY (`caricato_da`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documenti_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documenti`
--

LOCK TABLES `documenti` WRITE;
/*!40000 ALTER TABLE `documenti` DISABLE KEYS */;
INSERT INTO `documenti` VALUES (1,2,1,'aziendale','yy','documenti/01JYGMR3S6K2D38VQGQSFWRWH0.pdf','2025-06-24 09:36:45','2025-06-28 01:28:06'),(2,NULL,1,'aziendale','Vladislav','documenti/01JYGV9M5GBNQ8ZRBB70JY6EDN.pdf','2025-06-24 11:31:11','2025-06-24 11:31:11'),(3,2,1,'personale','aa','documenti/01JYNZY7EDHWKQP00QVTDK4QS4.pdf','2025-06-26 13:28:32','2025-06-26 13:28:32'),(4,NULL,1,'aziendale','ff','documenti/01JYPWMWM4P1YMQZWX3ND9789X.pdf','2025-06-26 21:50:15','2025-06-26 21:50:15'),(5,2,1,'aziendale','ii','documenti/01JYSVPVJEQVBQ2HC2CRJ971P8.pdf','2025-06-28 01:31:34','2025-06-28 01:31:34'),(6,2,1,'busta_paga','Rr','documenti/01JYTVCJTAKTVZ3Q0M7F0HZCJW.pdf','2025-06-28 10:45:12','2025-06-28 10:45:12'),(7,1,1,'aziendale','Rr','documenti/01JYVJS6GNPVFTE9S0BYNBEWKM.jpg','2025-06-28 17:34:03','2025-06-28 17:34:03'),(8,2,2,'personale','hh','documenti/01JYVSRQHSCT0EV82G437J9QYQ.pdf','2025-06-28 19:36:07','2025-06-28 19:36:07'),(9,2,2,'personale','patente','documenti/01JYVTAMRCCSDS9WRM0KDCR8NP.pdf','2025-06-28 19:45:54','2025-06-28 19:45:54'),(10,2,2,'personale','yyyy','documenti/01JYVW9Z5152XG0J6R0XTZBQHV.pdf','2025-06-28 20:20:29','2025-06-28 20:20:29'),(11,1,1,'personale','uyu','documenti/01JYVX4R3CD3G6PJVCDK342J4G.pdf','2025-06-28 20:35:07','2025-06-28 20:35:07'),(12,2,2,'personale','Patente','documenti/01JYW0C7GHH78NW178Q7M19BGK.jpg','2025-06-28 21:31:38','2025-06-28 21:31:38'),(14,2,2,'personale','Hh','documenti/01JYW300EC728XZ7Z5GV2SJ19R.jpg','2025-06-28 22:17:23','2025-06-28 22:17:23'),(15,2,2,'personale','Hh','documenti/01JYW4D3XKMZEF33ZN3XS1M370.jpg','2025-06-28 22:42:01','2025-06-28 22:42:01'),(16,2,2,'personale','ppppppp','documenti/01JYW4HJMG26CN4QSHDN8KYQNF.pdf','2025-06-28 22:44:27','2025-06-28 22:44:27'),(17,2,2,'personale','pinko','documenti/01JYW4PRTPYAPD68AXH65TWZM7.jpg','2025-06-28 22:47:17','2025-06-28 22:47:17'),(18,2,2,'personale','Hhhhhhh','documenti/01JYW4ZC8E5GXK8810E64YEF7R.jpg','2025-06-28 22:51:59','2025-06-28 22:51:59'),(19,2,2,'personale','Lettera','documenti/01JYW6CA6EVT4ADYT49Y3HMMYE.jpg','2025-06-28 23:16:32','2025-06-28 23:16:32'),(20,2,2,'personale','Ddd','documenti/01JYW6HBFH4NF10KR24BQ9G82Q.jpg','2025-06-28 23:19:17','2025-06-28 23:19:17'),(21,1,1,'personale','hjghgggh','documenti/01JYW6JBTYGS45X5N39MYTS6Y4.pdf','2025-06-28 23:19:50','2025-06-28 23:19:50'),(22,2,2,'personale','Letto ','documenti/01JYW6PG141JFW2XX3E715PKJY.jpg','2025-06-28 23:22:05','2025-06-28 23:22:05'),(23,1,1,'personale','kjghku','documenti/01JYW6PSQBS9QCNRA62B9NGVSM.pdf','2025-06-28 23:22:15','2025-06-28 23:22:15'),(24,2,2,'personale','Hshsh','documenti/01JYW6TP7P32DET7YGBJ21VTQ0.jpg','2025-06-28 23:24:23','2025-06-28 23:24:23'),(25,2,2,'personale','Prova1','documenti/01JYW6WGKG2FFSD67HSN46W7EV.jpg','2025-06-28 23:25:23','2025-06-28 23:25:23'),(26,2,2,'personale','Prova 2','documenti/01JYW78M3DFZMH3ADYNVC1BGF8.jpg','2025-06-28 23:31:59','2025-06-28 23:31:59'),(27,2,2,'personale','yy6y6','documenti/01JYW7H686RTQ50PYDYWK9PJDN.pdf','2025-06-28 23:36:40','2025-06-28 23:36:40');
/*!40000 ALTER TABLE `documenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `fatture`
--

DROP TABLE IF EXISTS `fatture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fatture` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `committente_id` bigint unsigned NOT NULL,
  `data_emissione` date NOT NULL,
  `mese_riferimento` int NOT NULL,
  `anno_riferimento` int NOT NULL,
  `stato` enum('bozza','emessa','pagata') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bozza',
  `totale_ore_lavoro` decimal(10,2) DEFAULT NULL,
  `totale_ore_viaggio` decimal(10,2) DEFAULT NULL,
  `totale_km` int DEFAULT NULL,
  `totale_pranzi` int DEFAULT NULL,
  `totale_trasferte` int DEFAULT NULL,
  `totale_spese_extra` decimal(10,2) DEFAULT NULL,
  `imponibile` decimal(10,2) DEFAULT NULL,
  `sconto` decimal(10,2) DEFAULT NULL,
  `totale` decimal(10,2) DEFAULT NULL,
  `dettagli` json DEFAULT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fatture_numero_unique` (`numero`),
  KEY `fatture_committente_id_anno_riferimento_mese_riferimento_index` (`committente_id`,`anno_riferimento`,`mese_riferimento`),
  KEY `fatture_stato_data_emissione_index` (`stato`,`data_emissione`),
  CONSTRAINT `fatture_committente_id_foreign` FOREIGN KEY (`committente_id`) REFERENCES `committenti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fatture`
--

LOCK TABLES `fatture` WRITE;
/*!40000 ALTER TABLE `fatture` DISABLE KEYS */;
INSERT INTO `fatture` VALUES (4,'2025-0001',2,'2025-06-25',6,2025,'bozza',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,150.00,NULL,NULL,'2025-06-25 00:29:37','2025-06-25 00:29:37'),(5,'2025-0002',2,'2025-06-25',6,2025,'bozza',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-25 00:30:00','2025-06-25 00:30:00'),(6,'2025-0003',3,'2025-06-25',6,2025,'bozza',8888.00,888.00,888,NULL,NULL,NULL,88.00,88.00,88.00,NULL,NULL,'2025-06-25 00:57:27','2025-06-25 00:57:27');
/*!40000 ALTER TABLE `fatture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impostazioni_fattura`
--

DROP TABLE IF EXISTS `impostazioni_fattura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impostazioni_fattura` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `committente_id` bigint unsigned NOT NULL,
  `indirizzo_fatturazione` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `partita_iva` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_creditor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_creditor_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_creditor_postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_creditor_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_creditor_country` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CH',
  `qr_additional_info` text COLLATE utf8mb4_unicode_ci,
  `qr_billing_info` text COLLATE utf8mb4_unicode_ci,
  `swiss_qr_bill` tinyint(1) NOT NULL DEFAULT '0',
  `fatturazione_automatica` tinyint(1) NOT NULL DEFAULT '0',
  `giorno_fatturazione` int DEFAULT NULL,
  `email_destinatari` json DEFAULT NULL,
  `costo_orario` decimal(10,2) NOT NULL,
  `costo_km` decimal(10,2) NOT NULL,
  `costo_pranzo` decimal(10,2) DEFAULT NULL,
  `costo_trasferta` decimal(10,2) DEFAULT NULL,
  `costo_fisso_intervento` decimal(10,2) DEFAULT NULL,
  `percentuale_notturno` decimal(5,2) NOT NULL DEFAULT '0.00',
  `percentuale_festivo` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sconto_percentuale` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `impostazioni_fattura_committente_id_unique` (`committente_id`),
  CONSTRAINT `impostazioni_fattura_committente_id_foreign` FOREIGN KEY (`committente_id`) REFERENCES `committenti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impostazioni_fattura`
--

LOCK TABLES `impostazioni_fattura` WRITE;
/*!40000 ALTER TABLE `impostazioni_fattura` DISABLE KEYS */;
INSERT INTO `impostazioni_fattura` VALUES (1,2,'via casa','1234','ch008','ouyuo','uyoouy','9000','yoy','CH',NULL,'y89',1,0,NULL,'[]',55.00,55.00,55.00,55.00,55.00,0.00,0.00,0.00,'2025-06-24 09:33:48','2025-06-25 00:19:39'),(2,3,'888','888','CH93 0076 2011 6238 5295 7','Test QR','Via Example 123','8000','Zurich','CH',NULL,NULL,1,0,NULL,'[]',88.00,88.00,NULL,NULL,NULL,890.00,887.00,887.00,'2025-06-25 00:55:28','2025-06-25 00:57:05');
/*!40000 ALTER TABLE `impostazioni_fattura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
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
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_06_24_071633_create_committentes_table',2),(5,'2025_06_24_071733_create_clientes_table',2),(6,'2025_06_24_071854_create_commessas_table',2),(7,'2025_06_24_072653_add_role_to_users_table',3),(8,'2025_06_24_075319_create_reports_table',4),(9,'2025_06_24_082352_create_spesas_table',5),(10,'2025_06_24_082517_create_spesa_extras_table',5),(11,'2025_06_24_082658_create_documentos_table',5),(12,'2025_06_24_082750_create_impostazione_fatturas_table',5),(13,'2025_06_24_082855_create_fatturas_table',5),(14,'2025_06_24_093014_fix_fatture_nullable_fields',6),(15,'2025_06_24_104430_create_automazione_pdfs_table',7),(16,'2025_06_24_220924_add_swiss_qr_bill_fields_to_impostazioni_fattura_table',8),(17,'2025_06_25_135030_add_festivo_to_report_table',9),(18,'2025_06_25_141411_create_automazione_reports_table',10),(19,'2025_06_25_143451_update_automazione_reports_table_raggruppamento',11),(20,'2025_06_25_144236_add_includi_dettagli_ore_to_automazione_reports',12),(21,'2025_06_25_175154_add_prompt_personalizzato_to_automazione_reports',13),(22,'2025_06_25_184919_create_pdf_templates_table',14),(23,'2025_06_26_173614_add_avatar_to_users_table',15);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `pdf_templates`
--

DROP TABLE IF EXISTS `pdf_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdf_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'report',
  `template_html` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_personalizzato` longtext COLLATE utf8mb4_unicode_ci,
  `variabili_disponibili` json DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT '1',
  `orientamento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'portrait',
  `formato_pagina` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A4',
  `margini` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pdf_templates_nome_unique` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdf_templates`
--

LOCK TABLES `pdf_templates` WRITE;
/*!40000 ALTER TABLE `pdf_templates` DISABLE KEYS */;
INSERT INTO `pdf_templates` VALUES (1,'report_giornalieri_default','Template predefinito per report giornalieri','report','<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n    <title>Report VLD Service - {{ $data->format(\'d/m/Y\') }}</title>\n    <style>\n        @page {\n            margin: 20mm;\n        }\n        body {\n            font-family: DejaVu Sans, sans-serif;\n            font-size: 10pt;\n            line-height: 1.5;\n            color: #333;\n        }\n        h1 {\n            color: #0066cc;\n            font-size: 18pt;\n            margin-bottom: 20px;\n            text-align: center;\n            border-bottom: 2px solid #0066cc;\n            padding-bottom: 10px;\n        }\n        h2 {\n            color: #1976d2;\n            font-size: 14pt;\n            margin-top: 20px;\n            margin-bottom: 10px;\n        }\n        h3 {\n            color: #1976d2;\n            font-size: 12pt;\n            margin-bottom: 10px;\n        }\n        .report-box {\n            border: 1px solid #e3f2fd;\n            border-radius: 5px;\n            padding: 15px;\n            margin-bottom: 20px;\n            background-color: #fafafa;\n            page-break-inside: avoid;\n        }\n        .info-grid {\n            display: table;\n            width: 100%;\n            margin-bottom: 10px;\n        }\n        .info-row {\n            display: table-row;\n        }\n        .info-cell {\n            display: table-cell;\n            width: 50%;\n            padding: 3px 0;\n        }\n        .flag {\n            display: inline-block;\n            padding: 2px 8px;\n            border-radius: 12px;\n            font-size: 9pt;\n            margin-right: 5px;\n        }\n        .flag-notturno { background: #ff9800; color: white; }\n        .flag-trasferta { background: #2196f3; color: white; }\n        .flag-festivo { background: #4caf50; color: white; }\n        .report-content {\n            background-color: #e8f5e8;\n            padding: 12px;\n            margin-top: 10px;\n            border-left: 4px solid #4caf50;\n            border-radius: 3px;\n        }\n        .descrizione-originale {\n            background-color: #fff3cd;\n            padding: 12px;\n            margin-top: 10px;\n            border-left: 4px solid #ffc107;\n            border-radius: 3px;\n        }\n        .riepilogo {\n            background-color: #e3f2fd;\n            padding: 15px;\n            border-radius: 5px;\n            margin-top: 30px;\n        }\n        .riepilogo-grid {\n            display: table;\n            width: 100%;\n        }\n        .riepilogo-item {\n            display: table-cell;\n            width: 33.33%;\n            padding: 5px;\n        }\n        hr {\n            border: none;\n            border-top: 1px solid #ddd;\n            margin: 20px 0;\n        }\n        .footer {\n            text-align: center;\n            font-size: 9pt;\n            color: #666;\n            margin-top: 30px;\n            padding-top: 20px;\n            border-top: 1px solid #ddd;\n        }\n    </style>\n</head>\n<body>\n    <h1>📋 Report VLD Service - {{ $data->format(\'d/m/Y\') }}</h1>\n    \n    @if($identificativo && $identificativo !== \'TUTTI I TECNICI\')\n        <p style=\"text-align: center; color: #666;\">Tecnico: {{ $identificativo }}</p>\n    @endif\n\n    @foreach($reports as $report)\n        <div class=\"report-box\">\n            <h3>👤 {{ $report->user->name }}</h3>\n            \n            <div class=\"info-grid\">\n                <div class=\"info-row\">\n                    <div class=\"info-cell\"><strong>🏢 Cliente:</strong> {{ $report->cliente->nome }}</div>\n                    <div class=\"info-cell\"><strong>📋 Commessa:</strong> {{ $report->commessa->nome }}</div>\n                </div>\n                @if($includi_dettagli_ore)\n                <div class=\"info-row\">\n                    <div class=\"info-cell\"><strong>⏰ Ore Lavoro:</strong> {{ $report->ore_lavorate }}h</div>\n                    <div class=\"info-cell\"><strong>🚗 Ore Viaggio:</strong> {{ $report->ore_viaggio }}h</div>\n                </div>\n                <div class=\"info-row\">\n                    <div class=\"info-cell\"><strong>📏 Chilometri:</strong> {{ $report->km_auto }} km</div>\n                    <div class=\"info-cell\"><strong>📅 Data:</strong> {{ $report->data->format(\'d/m/Y\') }}</div>\n                </div>\n                @else\n                <div class=\"info-row\">\n                    <div class=\"info-cell\"><strong>📅 Data:</strong> {{ $report->data->format(\'d/m/Y\') }}</div>\n                    <div class=\"info-cell\"></div>\n                </div>\n                @endif\n            </div>\n\n            @if($report->notturno || $report->trasferta || $report->festivo)\n                <div style=\"margin: 10px 0;\">\n                    @if($report->notturno)<span class=\"flag flag-notturno\">🌙 Notturno</span>@endif\n                    @if($report->trasferta)<span class=\"flag flag-trasferta\">🧳 Trasferta</span>@endif\n                    @if($report->festivo)<span class=\"flag flag-festivo\">🎉 Festivo</span>@endif\n                </div>\n            @endif\n\n            @if(!empty($reportContent[$report->id]))\n                <div class=\"report-content\">\n                    <strong>📝 Report Professionale:</strong><br>\n                    {!! nl2br(e($reportContent[$report->id])) !!}\n                </div>\n            @elseif(!empty($report->descrizione_lavori))\n                <div class=\"descrizione-originale\">\n                    <strong>📝 Descrizione Originale:</strong><br>\n                    {!! nl2br(e($report->descrizione_lavori)) !!}\n                </div>\n            @endif\n        </div>\n    @endforeach\n\n    <div class=\"riepilogo\">\n        <h3>📊 Riepilogo Giornaliero</h3>\n        <div class=\"riepilogo-grid\">\n            <div class=\"riepilogo-item\">\n                <strong>👥 Tecnici:</strong> {{ $reports->groupBy(\'user_id\')->count() }}\n            </div>\n            <div class=\"riepilogo-item\">\n                <strong>📋 Report:</strong> {{ $reports->count() }}\n            </div>\n            @if($includi_dettagli_ore)\n            <div class=\"riepilogo-item\">\n                <strong>⏱️ Ore Lavoro:</strong> {{ $reports->sum(\'ore_lavorate\') }}h\n            </div>\n            <div class=\"riepilogo-item\">\n                <strong>🚗 Ore Viaggio:</strong> {{ $reports->sum(\'ore_viaggio\') }}h\n            </div>\n            <div class=\"riepilogo-item\">\n                <strong>⏰ Totale:</strong> {{ $reports->sum(\'ore_lavorate\') + $reports->sum(\'ore_viaggio\') }}h\n            </div>\n            <div class=\"riepilogo-item\">\n                <strong>🛣️ Km Totali:</strong> {{ $reports->sum(\'km_auto\') }} km\n            </div>\n            @endif\n        </div>\n    </div>\n\n    <div class=\"footer\">\n        <p>Report generato automaticamente il {{ now()->format(\'d/m/Y H:i\') }}</p>\n        <p>VLD Service GmbH - {{ config(\'app.url\') }}</p>\n    </div>\n</body>\n</html>\n',NULL,'{\"data\": \"Data del report\", \"reports\": \"Collezione dei report\", \"reportContent\": \"Contenuti generati da AI\", \"identificativo\": \"Nome tecnico o TUTTI I TECNICI\", \"includi_dettagli_ore\": \"Flag per mostrare ore e km\"}',1,'portrait','A4',NULL,'2025-06-25 18:50:23','2025-06-25 18:50:23');
/*!40000 ALTER TABLE `pdf_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `data` date NOT NULL DEFAULT '2025-06-24',
  `committente_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `commessa_id` bigint unsigned NOT NULL,
  `ore_lavorate` decimal(3,1) NOT NULL,
  `ore_viaggio` decimal(3,1) NOT NULL,
  `km_auto` int NOT NULL DEFAULT '0',
  `auto_privata` tinyint(1) NOT NULL DEFAULT '0',
  `notturno` tinyint(1) NOT NULL DEFAULT '0',
  `trasferta` tinyint(1) NOT NULL DEFAULT '0',
  `festivo` tinyint(1) NOT NULL DEFAULT '0',
  `descrizione_lavori` text COLLATE utf8mb4_unicode_ci,
  `descrizione_it` text COLLATE utf8mb4_unicode_ci,
  `descrizione_en` text COLLATE utf8mb4_unicode_ci,
  `descrizione_de` text COLLATE utf8mb4_unicode_ci,
  `descrizione_ru` text COLLATE utf8mb4_unicode_ci,
  `ore_lavorate_fatturazione` decimal(3,1) DEFAULT NULL,
  `ore_viaggio_fatturazione` decimal(3,1) DEFAULT NULL,
  `fatturato` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_cliente_id_foreign` (`cliente_id`),
  KEY `report_commessa_id_foreign` (`commessa_id`),
  KEY `report_user_id_data_index` (`user_id`,`data`),
  KEY `report_committente_id_data_index` (`committente_id`,`data`),
  KEY `report_fatturato_index` (`fatturato`),
  CONSTRAINT `report_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_commessa_id_foreign` FOREIGN KEY (`commessa_id`) REFERENCES `commesse` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_committente_id_foreign` FOREIGN KEY (`committente_id`) REFERENCES `committenti` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (10,1,'2025-06-25',2,2,2,2.0,2.0,14,0,0,0,0,'montaggio','```\n----------------------------------------------------------------------\n                             REPORT DI LAVORO\n----------------------------------------------------------------------\n\nData: 25/06/2025 (Mercoledì)\n\nTecnico: Vladislav Admin\n\nCommittente: Colussi\n\nCliente: Bell\n\nCommessa: i890\n\n----------------------------------------------------------------------\n\nDETTAGLI LAVORO SVOLTO\n\nDescrizione attività: Montaggio\n\n----------------------------------------------------------------------\n\nORE E CHILOMETRI\n\nOre lavorate: 2.0 ore\n\nOre viaggio: 2.0 ore\n\nChilometri percorsi: 14 km\n\n----------------------------------------------------------------------\n\nCONDIZIONI LAVORATIVE\n\nLavoro notturno: No\n\nTrasferta: No\n\nFestivo: No\n\n----------------------------------------------------------------------\n\nNote: Nessuna nota aggiuntiva.\n\n----------------------------------------------------------------------\n\nFirma del Tecnico: ________________________\n\nData: ___/___/_____\n\n----------------------------------------------------------------------\n\nFirma del Committente: ____________________\n\nData: ___/___/_____\n\n----------------------------------------------------------------------\n```','---\n\n**Professional Work Report**\n\n**Date:** 25/06/2025 (Wednesday)  \n**Technician:** Vladislav Admin  \n**Contractor:** Colussi  \n**Client:** Bell  \n**Job Order:** i890  \n\n---\n\n**Work Details Performed:**\n\n- **Activity Description:** Assembly  \n- **Work Hours:** 2.0 hours  \n- **Travel Hours:** 2.0 hours  \n- **Distance Traveled:** 14 kilometers  \n\n**Additional Information:**\n\n- **Night Work:** No  \n- **Travel Assignment:** No  \n- **Holiday Work:** No  \n\n---\n\n**Summary:**\n\nOn the 25th of June, 2025, the technician Vladislav Admin successfully completed an assembly task for the client Bell under the contract with Colussi. The job order was identified as i890. The task involved 2.0 hours of work and required 2.0 hours of travel, covering a distance of 14 kilometers. The work was conducted during regular hours, with no night work, travel assignment, or holiday work involved.\n\n---','```\n------------------------------------------------------------\n                         ARBEITSBERICHT\n------------------------------------------------------------\n\nDatum:                25. Juni 2025 (Mittwoch)\nTechniker:            Vladislav Admin\nAuftraggeber:         Colussi\nKunde:                Bell\nAuftragsnummer:       i890\n\n------------------------------------------------------------\n                        ARBEITSDETAILS\n------------------------------------------------------------\n\nBeschreibung der Tätigkeit: \n- Montage\n\n------------------------------------------------------------\n                        STUNDEN UND KILOMETER\n------------------------------------------------------------\n\nGeleistete Arbeitsstunden:   2,0 Stunden\nReisezeit:                  2,0 Stunden\nGefahrene Kilometer:        14 km\n\n------------------------------------------------------------\n                        ZUSÄTZLICHE INFORMATIONEN\n------------------------------------------------------------\n\n- Nachtarbeit:              Nein\n- Dienstreise:              Nein\n- Feiertagsarbeit:          Nein\n\n------------------------------------------------------------\n```','# Профессиональный отчет о выполненной работе\n\n**Дата выполнения работ:** 25 июня 2025 года (среда)  \n**Техник:** Владислав Админ  \n**Заказчик:** Colussi  \n**Клиент:** Bell  \n**Номер заказа:** i890  \n\n## Детали выполненной работы\n\n**Описание деятельности:**  \nМонтаж\n\n**Часы работы:**  \n- Рабочие часы: 2.0 часа  \n- Время в пути: 2.0 часа  \n\n**Расстояние:**  \n- Пройденное расстояние: 14 км  \n\n## Дополнительная информация\n\n- Работа в ночное время: Нет  \n- Командировка: Нет  \n- Праздничный день: Нет  \n\nОтчет составлен в соответствии с предоставленными данными и отражает фактическое выполнение работ.',2.0,2.0,0,'2025-06-25 18:12:49','2025-06-26 08:32:35'),(11,2,'2025-06-26',2,2,2,16.0,12.0,100,1,0,0,0,'montaggio','```\n----------------------------------------------------------------------\n                            REPORT DI LAVORO\n----------------------------------------------------------------------\n\nData: 26/06/2025 (Giovedì)\n\nTecnico: test@test.test\n\nCommittente: Colussi\n\nCliente: Bell\n\nCommessa: i890\n\n----------------------------------------------------------------------\n\nDettagli Lavoro Svolto:\n\nDescrizione Attività: Montaggio\n\n----------------------------------------------------------------------\n\nOre Lavorate: 16.0h\n\nOre Viaggio: 12.0h\n\nChilometri Percorsi: 100 km\n\n----------------------------------------------------------------------\n\nCondizioni di Lavoro:\n\nLavoro Notturno: No\n\nTrasferta: No\n\nFestivo: No\n\n----------------------------------------------------------------------\n\nNote: Nessuna nota aggiuntiva.\n\n----------------------------------------------------------------------\n\nFirma del Tecnico: ________________________\n\nData: ________________________\n\n----------------------------------------------------------------------\n\n```','```\n-------------------------------------------------------------------\n                           WORK REPORT\n-------------------------------------------------------------------\n\nDate: June 26, 2025 (Thursday)\nTechnician: test@test.test\nClient: Bell\nContractor: Colussi\nProject Code: i890\n\n-------------------------------------------------------------------\n                        WORK DETAILS\n-------------------------------------------------------------------\n\nActivity Description: Assembly\n\n-------------------------------------------------------------------\n                          HOURS & TRAVEL\n-------------------------------------------------------------------\n\nTotal Hours Worked: 16.0 hours\nTravel Hours: 12.0 hours\nDistance Traveled: 100 kilometers\n\n-------------------------------------------------------------------\n                          ADDITIONAL INFO\n-------------------------------------------------------------------\n\nNight Work: No\nTravel Assignment: No\nHoliday Work: No\n\n-------------------------------------------------------------------\n```','**Arbeitsbericht**\n\n**Datum:** 26. Juni 2025 (Donnerstag)  \n**Techniker:** test@test.test  \n**Committent:** Colussi  \n**Kunde:** Bell  \n**Auftragsnummer:** i890  \n\n---\n\n**Arbeitsdetails:**\n\n- **Tätigkeit:** Montage  \n- **Arbeitsstunden:** 16,0 Stunden  \n- **Reisezeit:** 12,0 Stunden  \n- **Gefahrene Kilometer:** 100 km  \n- **Nachtarbeit:** Nein  \n- **Dienstreise:** Nein  \n- **Feiertagsarbeit:** Nein  \n\n---\n\n**Zusammenfassung:**\n\nAm 26. Juni 2025 wurde der Auftrag i890 für den Kunden Bell erfolgreich abgeschlossen. Der Techniker führte die Montagearbeiten gemäß den Anforderungen des Committenten Colussi durch. Die Arbeiten wurden innerhalb der regulären Arbeitszeiten durchgeführt, ohne dass Nacht-, Reise- oder Feiertagszuschläge anfielen. Die Gesamtreisezeit betrug 12 Stunden bei einer Strecke von 100 Kilometern. Alle Aufgaben wurden effizient und zur Zufriedenheit des Kunden abgeschlossen.','**Профессиональный отчет о выполненной работе**\n\n**Дата:** 26 июня 2025 года (четверг)  \n**Техник:** test@test.test  \n**Заказчик:** Colussi  \n**Клиент:** Bell  \n**Номер заказа:** i890  \n\n**Детали выполненной работы:**  \nВ рамках данного задания была проведена работа по монтажу. Все задачи выполнены в соответствии с установленными стандартами и требованиями клиента.\n\n**Часы работы и транспортные расходы:**  \n- **Отработанные часы:** 16.0 часов  \n- **Часы на дорогу:** 12.0 часов  \n- **Пробег:** 100 км  \n\n**Дополнительная информация:**  \n- Работа в ночное время: Нет  \n- Командировка: Нет  \n- Праздничные дни: Нет  \n\n**Описание деятельности:**  \nОсновной задачей было проведение монтажных работ. Все этапы работы были выполнены в срок и с соблюдением всех необходимых мер безопасности.\n\nДанный отчет подтверждает выполнение всех обязательств по заказу i890 в полном объеме.',16.0,12.0,0,'2025-06-26 07:55:37','2025-06-26 07:55:37'),(12,1,'2025-06-24',2,2,2,10.0,8.0,8,0,0,1,0,'montaggio',NULL,NULL,NULL,NULL,10.0,8.0,0,'2025-06-26 08:14:57','2025-06-26 08:14:57'),(13,2,'2025-06-23',2,2,2,12.0,12.0,1000,0,0,1,0,'montaggio',NULL,NULL,NULL,NULL,12.0,12.0,0,'2025-06-26 08:51:39','2025-06-26 08:51:39'),(14,1,'2025-06-25',4,3,3,24.0,14.0,1000,1,1,1,0,'Montaggio ',NULL,NULL,NULL,NULL,24.0,14.0,0,'2025-06-26 11:49:37','2025-06-26 11:49:37'),(15,2,'2025-06-26',2,2,2,7.0,12.0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-26 21:40:10','2025-06-26 21:40:10'),(16,2,'2025-06-26',2,2,2,2.0,1.5,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-26 21:45:28','2025-06-26 21:45:28'),(17,2,'2025-06-26',2,2,2,2.0,1.5,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-26 22:49:34','2025-06-26 22:49:34'),(18,1,'2025-06-23',2,2,2,10.0,0.0,0,0,0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-27 06:28:31','2025-06-27 06:28:31'),(19,1,'2025-06-06',2,2,2,2.0,2.0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-27 06:52:08','2025-06-27 06:52:08'),(20,1,'2025-06-10',2,2,2,2.0,1.5,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-27 09:23:52','2025-06-27 09:23:52'),(21,1,'2025-06-27',2,2,2,1.0,0.5,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-27 22:03:21','2025-06-27 22:03:21'),(23,1,'2025-06-28',2,2,2,2.0,1.5,11323132,1,1,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-28 02:15:26','2025-06-28 02:15:26'),(24,1,'2025-06-28',4,3,3,22.0,10.0,200,0,1,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-28 10:13:55','2025-06-28 10:13:55'),(25,2,'2025-06-28',2,2,2,12.0,8.0,58,1,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-06-28 17:30:27','2025-06-28 17:30:27');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('6U529R0FPVsqZTKz7fbALHZq43FH4L6qVN7qo3y0',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTzNUdlNJMklVbmNkdVZpMkFFdXhqSU91blNLUlRnemNJanhUQkVWOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL3NwZXNhLWV4dHJhcyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwczovL3ZsZC5pbnRlcm5ldC1ib3guY2gvYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1751144267),('Dfs02no1Dhqy8F1dOS5u1TeDtAMd9j67ilFJw3st',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWp2SHlDakVqaHhlNFlGN0ZEMms5RW10MU1IOFhEOUhRVXZCZlJyVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC91c2VyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1751144267),('FNohNl1XPsGQT8mtgx5UrbWqRHmh43sx8p3ac1bX',NULL,'185.247.137.30','Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM0t2UXdSRDU1RG1IcWlWNWRxOUhDQjlOb2VUaTVsUUtIZzdDOUlsMCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1751147041),('GIgqny8KIpJFNLhhq955Ro5bKnouBqlRmoJntjOG',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUHZDODJCQWIyWVF2dzJHcE0zV0RZRjZ1UFpWeW5lbFozOWphN3NObCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL3VzZXIiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozODoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL3VzZXIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1751144267),('h4DdahoieelfVScb6cH4YFkcFvA2tiF0cGyBymgM',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjhZNVpCRW4ySkZwMDlJM0xOOExxTmZsM3FtbmJCNlBSOTlwTzk5ayI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MDoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL3NwZXNhcyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwczovL3ZsZC5pbnRlcm5ldC1ib3guY2gvYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1751144267),('IvFiHjH8QMDhOZGHfDtzcWm9KEBUqy4hcEvFZY0l',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEF5TGxoS3FUa2NJdTlZU3pOQ0taS0VpQWludElXOHRVNWNHQ1RaayI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL3JlcG9ydHMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozOToiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1751144268),('nh2DLTSyPhCu2lELrEh0rFQh6F24aQsbcrXZp89q',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVDM0eTltOTR0cElObW1za2padVBMQjZSYXdmZzlVbk9qZzRTVWVTeSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL2RvY3VtZW50b3MiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozOToiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1751144267),('ofEkHGgkrKisDIDHx0hYehDTeDgv1nMhVtXdHxfr',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ2tCZ0htQ2RvalNmQjNnNk9YNmpacjhUNTdlTnBZOU1RSXplajdmUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MzoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL3VzZXIvZG9jdW1lbnRvcyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM4OiJodHRwczovL3ZsZC5pbnRlcm5ldC1ib3guY2gvdXNlci9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1751144295),('OMlW8O6CSx7FELnAKoT7dUsDoSDMSJVpGsLJvXuI',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR09YZ09MUER1UkdqNXRlbWVHSzZQSEgwMEZ2dmU4dVJoVmR5V1d5ZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL3VzZXIiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozODoiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL3VzZXIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1751144295),('qeM3rC1LMCCgXCXE8mfttprNzNot8CdU8vWYYaXm',NULL,'185.247.137.30','Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTW1sZWRxRUNmdnREQnM5VENCVkJMWlE3bG9oOGFrOWxNRGpYV1ZxQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1751147041),('qniflGdJbH4ETKi8kHdzOX551aAfIRB0Plflfw8y',NULL,'185.247.137.30','Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoid1lVT3lrSUhINlp3WG4zN0x6RjVGOE5BbEhiTThpNTEwWDdzc1JJUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1751147041),('wJ6Pe0o4yMHDIC6AzOtmysDoAqQVCEPnWV7naaIX',2,'83.76.57.94','Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiS1E1a3pxeWlpYjJoVmt6U2xxY1pPdkYxTmtKSUFjQ1JXZVhSaWIwMyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL3ZsZC5pbnRlcm5ldC1ib3guY2gvdXNlciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRVZTlEUGtDLy5QY1U1QkJYYkxWNjgub3VuV1lBSGNXMlEzdkxDemRtc1Q4SXEva3RjcDAxUyI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1751146330),('Xu3zmjHEO84yVongXLiMy8F9hJkkc5WUu20LcB4x',1,'83.76.57.94','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiZFA5cFBlenl1TVdBYXNMRU9GMExaZmdGODB6YU42MUVSUW93anQ1RiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC91c2VyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRaakZzRUVjLnJxLzg4MS5tRGlyN3FPSHNWUTlWbG9QYlV5L2ZsSFRzeW9vTjhRZ0MxaEhlSyI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1751146585),('Y6xPpN9tFn0c2g9snKPVsi2boFGpyQPIKTnsvjEL',2,'83.76.57.94','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiT3JQV2RUZU15bmdCcHg3Nm12bnpwajhWREd5MnBrUGVZQkVkcjRHbiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ0OiJodHRwczovL3ZsZC5pbnRlcm5ldC1ib3guY2gvYWRtaW4vZG9jdW1lbnRvcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRVZTlEUGtDLy5QY1U1QkJYYkxWNjgub3VuV1lBSGNXMlEzdkxDemRtc1Q4SXEva3RjcDAxUyI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1751146610),('YGD3NdN3uypG3v1k3w7MHSePPiCn04iOpzikvo3P',NULL,'83.76.57.94','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.4 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQk53Wk9neFBWZ2loYU5hZHh1YXhGWFMzcllEck43czEwN0Zzd0lHYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozOToiaHR0cHM6Ly92bGQuaW50ZXJuZXQtYm94LmNoL2FkbWluL3VzZXJzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1751144267),('zO1ofu7jSKjm2CYLyzqXyZPdAnoEGVo3twiLkbjc',1,'178.197.202.174','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Mobile Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiU2wxMkl4Z05ueHNHQnltSzhwWWttSWh2UkM1MmZpZHZtazF5Z2hVdyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vdmxkLmludGVybmV0LWJveC5jaC9hZG1pbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRaakZzRUVjLnJxLzg4MS5tRGlyN3FPSHNWUTlWbG9QYlV5L2ZsSFRzeW9vTjhRZ0MxaEhlSyI7fQ==',1751140228);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spese`
--

DROP TABLE IF EXISTS `spese`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spese` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `anno` int NOT NULL DEFAULT '2025',
  `mese` int NOT NULL DEFAULT '6',
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spese_user_id_anno_mese_file_unique` (`user_id`,`anno`,`mese`,`file`),
  KEY `spese_user_id_anno_mese_index` (`user_id`,`anno`,`mese`),
  CONSTRAINT `spese_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spese`
--

LOCK TABLES `spese` WRITE;
/*!40000 ALTER TABLE `spese` DISABLE KEYS */;
INSERT INTO `spese` VALUES (3,2,2025,6,'spese/01JYSTPGRYBWK67PSZBDZKQN5Q.pdf',NULL,'2025-06-24 10:52:18','2025-06-28 01:13:54'),(4,2,2025,6,'spese/01JYGV6YNFNPPTQHPF96HT7R35.jpg',NULL,'2025-06-24 11:29:43','2025-06-24 11:29:43'),(6,1,2025,5,'spese/01JYHCZ9VWRD8HAXZ30EHX54S2.pdf',NULL,'2025-06-24 18:40:07','2025-06-24 18:40:07'),(7,2,2025,6,'spese/01JYNTPE18GM5GBGAQ8K5NRWH3.jpg',NULL,'2025-06-26 11:56:54','2025-06-26 11:56:54'),(8,2,2025,6,'spese/01JYRRV92G4P4YG40Z6XPNET5C.jpg',NULL,'2025-06-27 15:22:19','2025-06-27 15:22:19'),(9,1,2025,6,'spese/01JYTSFPGSFQ4S8AFT5BE3F2YM.jpg',NULL,'2025-06-28 10:11:57','2025-06-28 10:11:57');
/*!40000 ALTER TABLE `spese` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spese_extra`
--

DROP TABLE IF EXISTS `spese_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spese_extra` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `committente_id` bigint unsigned NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importo` decimal(10,2) DEFAULT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `data` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spese_extra_user_id_data_index` (`user_id`,`data`),
  KEY `spese_extra_committente_id_data_index` (`committente_id`,`data`),
  CONSTRAINT `spese_extra_committente_id_foreign` FOREIGN KEY (`committente_id`) REFERENCES `committenti` (`id`) ON DELETE CASCADE,
  CONSTRAINT `spese_extra_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spese_extra`
--

LOCK TABLES `spese_extra` WRITE;
/*!40000 ALTER TABLE `spese_extra` DISABLE KEYS */;
INSERT INTO `spese_extra` VALUES (1,1,2,'spese-extra/01JYK6Z9RT99GPB4F8B6DRGZAY.pdf',12678.60,'Contributi paritetici 01.01.2024–31.12.2024: Fattura di chiusura','2025-06-24','2025-06-24 09:34:50','2025-06-25 12:19:06'),(2,1,2,'spese-extra/01JYK834T45ND27AFXHVSXAC70.jpg',37.70,'Ricevuta ristorante','2025-06-25','2025-06-25 11:53:19','2025-06-25 12:08:20'),(3,1,2,'spese-extra/01JYKASC98VYHC6Q72583DRDG8.jpg',33.50,'Ricevuta ristorante','2025-06-25','2025-06-25 12:40:24','2025-06-25 12:40:43'),(4,2,2,'spese-extra/01JYNGFQJCWG702GWEKC298VSS.pdf',197.50,'Sconto','2025-06-26','2025-06-26 08:58:29','2025-06-26 08:58:46'),(5,2,2,'spese-extra/01JYNN6197NTTDRQPHRCNE8G05.pdf',137.50,'sconto','2025-06-26','2025-06-26 10:18:39','2025-06-26 11:55:05'),(6,1,4,'spese-extra/01JYNTEEVT5EHB8N59W50ZV8BF.pdf',NULL,NULL,'2025-06-26','2025-06-26 11:52:33','2025-06-26 11:52:33'),(7,1,2,'spese-extra/01JYTSQJJXYG54WY9341TXAG00.jpg',42.60,'Acquisto generi alimentari e bevande','2025-06-28','2025-06-28 10:16:15','2025-06-28 10:16:33');
/*!40000 ALTER TABLE `spese_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','manager','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` text COLLATE utf8mb4_unicode_ci,
  `taglia_giacca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taglia_pantaloni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taglia_maglietta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taglia_scarpe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_abbigliamento` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Vladislav Admin','vlad@vldservice.ch','avatars/685fa390afabb.jpg',NULL,'$2y$12$ZjFsEEc.rq/881.mDir7qOHsVQ9VloPbUy/flHTsyooN8QgC1hHeK','RQCLCLUL4fYntyQWe8igIcoebUeGgeIIKlX85ZTtegSARSu2IufQh8acK20v','2025-06-24 07:50:24','2025-06-28 10:10:56','admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'test@test.test','test@test.test','avatars/686042f20c59d.jpg',NULL,'$2y$12$Ue9DPkC/.PcU5BBXbLV68.ounWYAHcW2Q3vLCzdmsT8Iq/ktcp01S','MCcYEgo4r3obclur2v7IysB735ZZeWwDYHfv1ro05YAWgE6d5xnxI6aRTEeU','2025-06-24 09:34:10','2025-06-28 21:30:58','user',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-28 21:44:25
