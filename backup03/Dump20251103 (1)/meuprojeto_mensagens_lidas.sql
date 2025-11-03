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
-- Table structure for table `mensagens_lidas`
--

DROP TABLE IF EXISTS `mensagens_lidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagens_lidas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mensagem_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `lida_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lida` (`mensagem_id`,`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `mensagens_lidas_ibfk_1` FOREIGN KEY (`mensagem_id`) REFERENCES `mensagens` (`idmensagem`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_lidas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagens_lidas`
--

LOCK TABLES `mensagens_lidas` WRITE;
/*!40000 ALTER TABLE `mensagens_lidas` DISABLE KEYS */;
INSERT INTO `mensagens_lidas` VALUES (1,1,6,'2025-11-03 02:43:09'),(3,2,4,'2025-11-03 02:43:18'),(4,3,6,'2025-11-03 02:43:34'),(5,4,6,'2025-11-03 02:43:51'),(7,5,4,'2025-11-03 02:44:00'),(9,6,4,'2025-11-03 02:44:04'),(24,7,4,'2025-11-03 02:48:51'),(26,8,4,'2025-11-03 02:49:03'),(35,9,6,'2025-11-03 02:55:59'),(38,10,6,'2025-11-03 02:56:22'),(39,11,6,'2025-11-03 02:56:22'),(44,12,4,'2025-11-03 03:13:52'),(50,13,4,'2025-11-03 03:33:01');
/*!40000 ALTER TABLE `mensagens_lidas` ENABLE KEYS */;
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
