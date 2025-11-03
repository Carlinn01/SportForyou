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
-- Table structure for table `notificacoes`
--

DROP TABLE IF EXISTS `notificacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mensagem` text COLLATE utf8mb4_general_ci,
  `link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lida` tinyint(1) DEFAULT '0',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacoes`
--

LOCK TABLES `notificacoes` WRITE;
/*!40000 ALTER TABLE `notificacoes` DISABLE KEYS */;
INSERT INTO `notificacoes` VALUES (1,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-28 18:03:48'),(2,5,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-28 18:04:28'),(3,6,'seguidor','@Andrezinho começou a seguir você!',NULL,1,'2025-10-28 18:04:29'),(4,7,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-28 18:04:30'),(5,4,'seguidor','@Andrezinho começou a seguir você!',NULL,1,'2025-10-28 18:04:31'),(6,3,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-28 18:04:31'),(7,1,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-28 18:04:32'),(8,2,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-28 18:04:33'),(9,8,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 16:35:12'),(10,8,'seguidor','@Andrezinho começou a seguir você!',NULL,0,'2025-10-30 16:36:46'),(11,9,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 17:37:06'),(12,2,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:23:49'),(13,1,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:23:51'),(14,3,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:23:52'),(15,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:23:53'),(16,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:28:55'),(17,1,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:29:02'),(18,2,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:29:06'),(19,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:31:26'),(20,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-10-30 22:31:53'),(21,7,'seguidor','@User começou a seguir você!',NULL,0,'2025-11-03 01:18:30'),(22,3,'seguidor','@User começou a seguir você!',NULL,0,'2025-11-03 01:22:12'),(23,2,'seguidor','@User começou a seguir você!',NULL,0,'2025-11-03 02:05:53'),(24,4,'seguidor','@Andrezinho começou a seguir você!',NULL,1,'2025-11-03 02:06:39'),(25,6,'mensagem','@User enviou uma mensagem','mensagens.php?conversa=2',1,'2025-11-03 02:55:58'),(26,6,'mensagem','@User enviou uma mensagem','mensagens.php?conversa=2',0,'2025-11-03 02:56:09'),(27,6,'mensagem','@User enviou uma mensagem','mensagens.php?conversa=2',1,'2025-11-03 02:56:13'),(28,4,'mensagem','@Andrezinho enviou uma mensagem','mensagens.php?conversa=2',1,'2025-11-03 03:13:44'),(29,4,'mensagem','@Andrezinho enviou uma mensagem','mensagens.php?conversa=2',0,'2025-11-03 03:32:49');
/*!40000 ALTER TABLE `notificacoes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-03  0:38:03
