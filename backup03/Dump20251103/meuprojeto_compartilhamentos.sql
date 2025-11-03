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
-- Table structure for table `compartilhamentos`
--

DROP TABLE IF EXISTS `compartilhamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compartilhamentos` (
  `idcompartilhamento` int NOT NULL AUTO_INCREMENT,
  `idusuario` int NOT NULL,
  `idpostagem` int NOT NULL,
  `data_compartilhamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcompartilhamento`),
  KEY `idusuario` (`idusuario`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `compartilhamentos_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE,
  CONSTRAINT `compartilhamentos_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compartilhamentos`
--

LOCK TABLES `compartilhamentos` WRITE;
/*!40000 ALTER TABLE `compartilhamentos` DISABLE KEYS */;
INSERT INTO `compartilhamentos` VALUES (1,4,18,'2025-11-03 01:37:03'),(2,4,18,'2025-11-03 01:37:04'),(3,4,18,'2025-11-03 01:37:05'),(4,4,18,'2025-11-03 01:37:05'),(5,4,18,'2025-11-03 01:37:06'),(6,4,18,'2025-11-03 01:37:06'),(7,4,18,'2025-11-03 01:37:06'),(8,4,18,'2025-11-03 01:37:06'),(9,4,18,'2025-11-03 01:37:06'),(10,4,18,'2025-11-03 01:37:06'),(11,4,18,'2025-11-03 01:37:07'),(12,4,18,'2025-11-03 01:37:07'),(13,4,18,'2025-11-03 01:37:07'),(14,4,18,'2025-11-03 01:37:07'),(15,4,18,'2025-11-03 01:37:07'),(16,4,18,'2025-11-03 01:37:07'),(17,4,18,'2025-11-03 01:37:08'),(18,4,18,'2025-11-03 01:37:08'),(19,4,18,'2025-11-03 01:37:08'),(20,4,18,'2025-11-03 01:37:08'),(21,4,18,'2025-11-03 01:37:08'),(22,4,18,'2025-11-03 01:37:08'),(23,4,18,'2025-11-03 01:37:09'),(24,4,17,'2025-11-03 01:37:17'),(25,4,17,'2025-11-03 01:37:17'),(26,4,17,'2025-11-03 01:37:17'),(27,4,17,'2025-11-03 01:37:18'),(28,4,17,'2025-11-03 01:37:18'),(29,4,17,'2025-11-03 01:37:18'),(30,4,17,'2025-11-03 01:37:18'),(31,4,17,'2025-11-03 01:37:18'),(32,4,17,'2025-11-03 01:37:18'),(33,4,17,'2025-11-03 01:37:19'),(34,4,17,'2025-11-03 01:37:19'),(35,4,17,'2025-11-03 01:37:19');
/*!40000 ALTER TABLE `compartilhamentos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-03  0:38:01
