-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: meuprojeto
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
  `idcomentario` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `idpostagem` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `data_comentario` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcomentario`),
  KEY `idusuario` (`idusuario`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`),
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--

LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (1,4,14,'dsa','2025-10-30 17:18:24'),(2,4,14,'dsa','2025-10-30 17:18:51'),(3,4,14,'dsa','2025-10-30 17:18:53'),(4,4,14,'dsa','2025-10-30 17:18:55'),(5,4,14,'dasdsa','2025-10-30 17:18:56'),(6,4,14,'dsa','2025-10-30 17:21:21'),(7,4,14,'dsadsa','2025-10-30 17:21:59'),(8,4,14,'dsa','2025-10-30 17:33:24'),(9,4,14,'comentrai','2025-10-30 17:33:30'),(10,9,15,'oi','2025-10-30 17:41:40'),(11,9,15,'Alo','2025-10-30 17:41:46'),(12,4,15,'dsa','2025-10-30 17:47:15');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curtidas`
--

DROP TABLE IF EXISTS `curtidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curtidas` (
  `idcurtida` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `idpostagem` int(11) NOT NULL,
  `data` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idcurtida`),
  UNIQUE KEY `idusuario` (`idusuario`,`idpostagem`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `curtidas_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`),
  CONSTRAINT `curtidas_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curtidas`
--

LOCK TABLES `curtidas` WRITE;
/*!40000 ALTER TABLE `curtidas` DISABLE KEYS */;
INSERT INTO `curtidas` VALUES (5,4,12,'2025-10-30 14:11:16'),(6,4,11,'2025-10-30 14:13:24'),(17,4,7,'2025-10-30 14:24:04'),(35,9,15,'2025-10-30 14:41:51'),(41,4,14,'2025-10-30 14:57:38'),(42,4,9,'2025-10-30 14:57:45');
/*!40000 ALTER TABLE `curtidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacoes`
--

DROP TABLE IF EXISTS `notificacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacoes`
--

LOCK TABLES `notificacoes` WRITE;
/*!40000 ALTER TABLE `notificacoes` DISABLE KEYS */;
INSERT INTO `notificacoes` VALUES (1,7,'seguidor','@User começou a seguir você!',0,'2025-10-28 18:03:48'),(2,5,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:28'),(3,6,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:29'),(4,7,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:30'),(5,4,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:31'),(6,3,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:31'),(7,1,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:32'),(8,2,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-28 18:04:33'),(9,8,'seguidor','@User começou a seguir você!',0,'2025-10-30 16:35:12'),(10,8,'seguidor','@Andrezinho começou a seguir você!',0,'2025-10-30 16:36:46'),(11,9,'seguidor','@User começou a seguir você!',0,'2025-10-30 17:37:06');
/*!40000 ALTER TABLE `notificacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postagens`
--

DROP TABLE IF EXISTS `postagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postagens` (
  `idpostagem` int(11) NOT NULL AUTO_INCREMENT,
  `texto` text DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `criado_em` datetime DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `publico` enum('público','privado') DEFAULT NULL,
  PRIMARY KEY (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postagens`
--

LOCK TABLES `postagens` WRITE;
/*!40000 ALTER TABLE `postagens` DISABLE KEYS */;
INSERT INTO `postagens` VALUES (1,'fotos','1760467547_Ellen.jpg','2025-10-14 15:45:47',6,NULL),(2,'teste','1760468669_Ellen.jpg','2025-10-14 16:04:29',6,NULL),(3,'olá, eu sou o André Nascimento','1760468783_images.webp','2025-10-14 16:06:23',6,NULL),(4,' dadasda',NULL,'2025-10-14 16:47:09',6,NULL),(5,'ontem eu  bati bola',NULL,'2025-10-14 16:47:25',6,NULL),(6,'ddsa',NULL,'2025-10-14 16:54:06',6,NULL),(7,'bom dia, volto de meio dia','1760961765_Rectangle 17.png','2025-10-20 09:02:45',7,NULL),(8,'das',NULL,'2025-10-28 14:44:50',4,NULL),(9,'dsa',NULL,'2025-10-28 14:44:52',4,NULL),(10,'das',NULL,'2025-10-28 14:44:54',4,NULL),(11,'da',NULL,'2025-10-28 14:44:55',4,NULL),(12,'dsa',NULL,'2025-10-30 13:37:00',6,NULL),(13,'das',NULL,'2025-10-30 13:38:45',6,NULL),(14,'da',NULL,'2025-10-30 13:48:28',4,NULL),(15,'so o bernardo',NULL,'2025-10-30 14:36:52',9,NULL);
/*!40000 ALTER TABLE `postagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguidores`
--

DROP TABLE IF EXISTS `seguidores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguidores` (
  `idseguidor` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL COMMENT 'O "idseguidor" não é gerado pelo banco, mas sim o id do usuário que está seguindo.\nE "idusuario" é o id de quem está sendo seguido.',
  PRIMARY KEY (`idseguidor`,`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguidores`
--

LOCK TABLES `seguidores` WRITE;
/*!40000 ALTER TABLE `seguidores` DISABLE KEYS */;
INSERT INTO `seguidores` VALUES (1,5),(1,7),(2,7),(3,5),(4,4),(4,5),(4,7),(5,4),(5,7),(6,4),(6,6),(7,4),(7,6),(8,4),(8,6),(9,4);
/*!40000 ALTER TABLE `seguidores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stories`
--

DROP TABLE IF EXISTS `stories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stories` (
  `idstory` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `midia` varchar(255) NOT NULL,
  `tipo` enum('imagem','video') NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idstory`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stories`
--

LOCK TABLES `stories` WRITE;
/*!40000 ALTER TABLE `stories` DISABLE KEYS */;
INSERT INTO `stories` VALUES (1,6,'uploads/stories/68ee99a690404.png','imagem','2025-10-14 18:42:46'),(2,6,'uploads/stories/68ee9a2e42505.png','imagem','2025-10-14 18:45:02'),(3,6,'uploads/stories/68ee9a3defdc7.jpg','imagem','2025-10-14 18:45:17'),(4,4,'uploads/stories/68f61dd449d38.png','imagem','2025-10-20 11:32:36'),(5,7,'uploads/stories/68f62511f1663.png','imagem','2025-10-20 12:03:29'),(6,4,'uploads/stories/690393f28d880.png','imagem','2025-10-30 16:36:02'),(7,4,'uploads/stories/690393fc13953.png','imagem','2025-10-30 16:36:12');
/*!40000 ALTER TABLE `stories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `idusuarios` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `nome_usuario` varchar(45) DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `foto_perfil` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=armscii8 COLLATE=armscii8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'email@email.com','$2y$12$R.OiVz0aFFkV0XGLUte0IOEuDvxcoD4UTmxW1bnWhcwjtYaLjwWTS','Carlos','Carlinn','1111-11-11','img_68dc631829b3e5.65682007.avif'),(2,'email2@email.com','81dc9bdb52d04dc20036dbd8313ed055','Carlos','Carlinn','0111-11-01','img_68dc637bd3bb07.41277387.png'),(3,'email@gmail.com','202cb962ac59075b964b07152d234b70','Carlos','Carlinn','2008-04-16','img_68dd7a07e95240.70326799.png'),(4,'carlos@email.com','202cb962ac59075b964b07152d234b70','Carlos ','User','2008-04-16','img_68e7040ecc72a3.90917650.png'),(5,'user2@user2.com','202cb962ac59075b964b07152d234b70','user2','user2','1111-11-11',''),(6,'andre@email.com','202cb962ac59075b964b07152d234b70','Andre','Andrezinho','1111-11-11','img_68eceef9a34717.75077709.png'),(7,'amanda@gmail.com','3f1bbc89706e4c2716e2dca536e6e277','Amanda Moresco','amandamoresco','2000-05-14','img_68f624c7b10f11.42663206.png'),(8,'dsadsa@email.com','81dc9bdb52d04dc20036dbd8313ed055','dsa','user','0011-11-11',''),(9,'oi@gmail.com','202cb962ac59075b964b07152d234b70','tesudinho','8===D','2025-03-12','img_6903a2146f8602.13926032.jpg');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-30 14:59:52
