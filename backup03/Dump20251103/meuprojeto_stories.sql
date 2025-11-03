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
-- Table structure for table `stories`
--

DROP TABLE IF EXISTS `stories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stories` (
  `idstory` int NOT NULL AUTO_INCREMENT,
  `idusuario` int NOT NULL,
  `midia` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('imagem','video') COLLATE utf8mb4_general_ci NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idstory`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stories`
--

LOCK TABLES `stories` WRITE;
/*!40000 ALTER TABLE `stories` DISABLE KEYS */;
INSERT INTO `stories` VALUES (1,6,'uploads/stories/68ee99a690404.png','imagem','2025-10-14 18:42:46'),(2,6,'uploads/stories/68ee9a2e42505.png','imagem','2025-10-14 18:45:02'),(3,6,'uploads/stories/68ee9a3defdc7.jpg','imagem','2025-10-14 18:45:17'),(4,4,'uploads/stories/68f61dd449d38.png','imagem','2025-10-20 11:32:36'),(5,7,'uploads/stories/68f62511f1663.png','imagem','2025-10-20 12:03:29'),(6,4,'uploads/stories/690393f28d880.png','imagem','2025-10-30 16:36:02'),(7,4,'uploads/stories/690393fc13953.png','imagem','2025-10-30 16:36:12'),(8,4,'uploads/stories/6903e1377dcf5.png','imagem','2025-10-30 22:05:43'),(9,4,'uploads/stories/6903e1396164b.png','imagem','2025-10-30 22:05:45'),(10,4,'uploads/stories/6903e13b5074d.png','imagem','2025-10-30 22:05:47'),(11,4,'uploads/stories/6903e13eb54e9.png','imagem','2025-10-30 22:05:50'),(12,4,'uploads/stories/6903e140cbd11.png','imagem','2025-10-30 22:05:52'),(13,4,'uploads/stories/6903e14339e68.png','imagem','2025-10-30 22:05:55'),(14,4,'uploads/stories/6903e1471bd53.png','imagem','2025-10-30 22:05:59'),(15,4,'uploads/stories/690800cdb125f_1762132173.png','imagem','2025-11-03 01:09:33'),(16,4,'uploads/stories/690803d52e4e3_1762132949.jpg','imagem','2025-11-03 01:22:29');
/*!40000 ALTER TABLE `stories` ENABLE KEYS */;
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
