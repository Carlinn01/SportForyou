-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: meuprojeto
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postagens` (
  `idpostagem` int NOT NULL AUTO_INCREMENT,
  `texto` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` datetime DEFAULT NULL,
  `idusuario` int DEFAULT NULL,
  `publico` enum('público','privado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postagens`
--

LOCK TABLES `postagens` WRITE;
/*!40000 ALTER TABLE `postagens` DISABLE KEYS */;
INSERT INTO `postagens` VALUES (1,'fotos','1760467547_Ellen.jpg','2025-10-14 15:45:47',6,NULL),(2,'teste','1760468669_Ellen.jpg','2025-10-14 16:04:29',6,NULL),(3,'olá, eu sou o André Nascimento','1760468783_images.webp','2025-10-14 16:06:23',6,NULL),(4,' dadasda',NULL,'2025-10-14 16:47:09',6,NULL),(5,'ontem eu  bati bola',NULL,'2025-10-14 16:47:25',6,NULL),(6,'ddsa',NULL,'2025-10-14 16:54:06',6,NULL),(7,'bom dia, volto de meio dia','1760961765_Rectangle 17.png','2025-10-20 09:02:45',7,NULL),(8,'das',NULL,'2025-10-28 14:44:50',4,NULL),(9,'dsa',NULL,'2025-10-28 14:44:52',4,NULL),(10,'das',NULL,'2025-10-28 14:44:54',4,NULL),(11,'da',NULL,'2025-10-28 14:44:55',4,NULL),(12,'dsa',NULL,'2025-10-30 13:37:00',6,NULL),(13,'das',NULL,'2025-10-30 13:38:45',6,NULL),(14,'da',NULL,'2025-10-30 13:48:28',4,NULL),(15,'so o bernardo',NULL,'2025-10-30 14:36:52',9,NULL),(16,'teste','1761861739_img_registro.png','2025-10-30 19:02:19',4,NULL),(17,'oi',NULL,'2025-10-30 19:24:48',4,NULL),(18,'das','1762133236_690804f474a72_ewqddasdas.PNG','2025-11-02 22:27:16',4,NULL);
/*!40000 ALTER TABLE `postagens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-03  0:38:04
