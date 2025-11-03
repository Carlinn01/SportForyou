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
-- Table structure for table `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagens` (
  `idmensagem` int NOT NULL AUTO_INCREMENT,
  `conversa_id` int NOT NULL,
  `remetente_id` int NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enviada','entregue','lida') COLLATE utf8mb4_unicode_ci DEFAULT 'enviada',
  `anexo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idmensagem`),
  KEY `conversa_id` (`conversa_id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `criado_em` (`criado_em`),
  CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`idconversa`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagens`
--

LOCK TABLES `mensagens` WRITE;
/*!40000 ALTER TABLE `mensagens` DISABLE KEYS */;
INSERT INTO `mensagens` VALUES (1,2,4,'oi','lida',NULL,'2025-11-03 02:43:02'),(2,2,6,'oi','lida',NULL,'2025-11-03 02:43:17'),(3,2,4,'das','lida','anexos/anexo_690816d4b916f5.09866840.PNG','2025-11-03 02:43:32'),(4,2,4,'da','lida','anexos/anexo_690816e616cfa0.75090191.PNG','2025-11-03 02:43:50'),(5,2,6,'?','lida',NULL,'2025-11-03 02:43:59'),(6,2,6,'?','lida',NULL,'2025-11-03 02:44:03'),(7,2,6,'oi','lida',NULL,'2025-11-03 02:48:50'),(8,2,6,'oi','lida',NULL,'2025-11-03 02:49:01'),(9,2,4,'oi','lida',NULL,'2025-11-03 02:55:58'),(10,2,4,'oi','lida',NULL,'2025-11-03 02:56:09'),(11,2,4,'UsuarioDAO::adicionarNotificacao(): Implicitly marking parameter $link as nullable is deprecated, the explicit nullable type must be used instead in <b>C:\\Users\\Carlinn\\Desktop\\SportForyou-1\\login\\src\\UsuarioDAO.php</b> on line <b>112</b><br />','lida',NULL,'2025-11-03 02:56:13'),(12,2,6,'oi','lida',NULL,'2025-11-03 03:13:44'),(13,2,6,'da','lida','anexos/anexo_69082261e7a7a5.12591821.PNG','2025-11-03 03:32:49');
/*!40000 ALTER TABLE `mensagens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-03  0:38:00
