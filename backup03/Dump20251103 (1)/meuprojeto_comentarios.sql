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
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios` (
  `idcomentario` int NOT NULL AUTO_INCREMENT,
  `idusuario` int NOT NULL,
  `idpostagem` int NOT NULL,
  `comentario` text COLLATE utf8mb4_general_ci NOT NULL,
  `data_comentario` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcomentario`),
  KEY `idusuario` (`idusuario`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`),
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--

LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (1,4,14,'dsa','2025-10-30 17:18:24'),(2,4,14,'dsa','2025-10-30 17:18:51'),(3,4,14,'dsa','2025-10-30 17:18:53'),(4,4,14,'dsa','2025-10-30 17:18:55'),(5,4,14,'dasdsa','2025-10-30 17:18:56'),(6,4,14,'dsa','2025-10-30 17:21:21'),(7,4,14,'dsadsa','2025-10-30 17:21:59'),(8,4,14,'dsa','2025-10-30 17:33:24'),(9,4,14,'comentrai','2025-10-30 17:33:30'),(10,9,15,'oi','2025-10-30 17:41:40'),(11,9,15,'Alo','2025-10-30 17:41:46'),(12,4,15,'dsa','2025-10-30 17:47:15'),(13,4,16,'Teste cometario','2025-10-30 22:02:31'),(14,4,17,'das','2025-11-03 01:09:41'),(15,4,17,'das','2025-11-03 01:09:44'),(16,4,17,'das','2025-11-03 01:11:07'),(17,4,17,'da','2025-11-03 01:11:08'),(18,4,17,'das','2025-11-03 01:11:09'),(19,4,7,'das','2025-11-03 01:19:04'),(20,4,7,'ds','2025-11-03 01:28:29'),(21,4,18,'das?','2025-11-03 01:37:14');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
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
