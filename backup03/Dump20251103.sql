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

--
-- Table structure for table `conversas`
--

DROP TABLE IF EXISTS `conversas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversas` (
  `idconversa` int NOT NULL AUTO_INCREMENT,
  `usuario1_id` int NOT NULL,
  `usuario2_id` int NOT NULL,
  `ultima_mensagem_id` int DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idconversa`),
  UNIQUE KEY `unique_conversa` (`usuario1_id`,`usuario2_id`),
  KEY `usuario1_id` (`usuario1_id`),
  KEY `usuario2_id` (`usuario2_id`),
  KEY `idx_atualizado_em` (`atualizado_em`),
  CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`usuario1_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE,
  CONSTRAINT `conversas_ibfk_2` FOREIGN KEY (`usuario2_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversas`
--

LOCK TABLES `conversas` WRITE;
/*!40000 ALTER TABLE `conversas` DISABLE KEYS */;
INSERT INTO `conversas` VALUES (1,4,5,1,'2025-11-03 02:19:21','2025-11-03 02:19:26'),(2,4,6,13,'2025-11-03 02:20:31','2025-11-03 03:32:49'),(3,1,6,4,'2025-11-03 02:36:02','2025-11-03 02:36:05'),(4,4,9,NULL,'2025-11-03 02:47:51','2025-11-03 02:47:51');
/*!40000 ALTER TABLE `conversas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curtidas`
--

DROP TABLE IF EXISTS `curtidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curtidas` (
  `idcurtida` int NOT NULL AUTO_INCREMENT,
  `idusuario` int NOT NULL,
  `idpostagem` int NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcurtida`),
  UNIQUE KEY `idusuario` (`idusuario`,`idpostagem`),
  KEY `idpostagem` (`idpostagem`),
  CONSTRAINT `curtidas_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuarios`),
  CONSTRAINT `curtidas_ibfk_2` FOREIGN KEY (`idpostagem`) REFERENCES `postagens` (`idpostagem`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curtidas`
--

LOCK TABLES `curtidas` WRITE;
/*!40000 ALTER TABLE `curtidas` DISABLE KEYS */;
INSERT INTO `curtidas` VALUES (5,4,12,'2025-10-30 14:11:16'),(6,4,11,'2025-10-30 14:13:24'),(35,9,15,'2025-10-30 14:41:51'),(41,4,14,'2025-10-30 14:57:38'),(42,4,9,'2025-10-30 14:57:45'),(43,4,15,'2025-10-30 19:00:54'),(44,4,16,'2025-10-30 19:02:23'),(49,4,17,'2025-11-02 22:28:10'),(52,4,18,'2025-11-02 22:28:45');
/*!40000 ALTER TABLE `curtidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `idevento` int NOT NULL AUTO_INCREMENT,
  `organizador_id` int NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `tipo_esporte` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_evento` date NOT NULL,
  `hora_evento` time DEFAULT NULL,
  `local` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevento`),
  KEY `organizador_id` (`organizador_id`),
  KEY `data_evento` (`data_evento`),
  KEY `tipo_esporte` (`tipo_esporte`),
  CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`organizador_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` VALUES (1,4,'das','das','Rugby','3000-03-03','11:11:00','dsa','dsa','PI',NULL,'2025-11-03 03:13:00','2025-11-03 03:13:00');
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos_interessados`
--

DROP TABLE IF EXISTS `eventos_interessados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos_interessados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_interesse` (`evento_id`,`usuario_id`),
  KEY `evento_id` (`evento_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `eventos_interessados_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`idevento`) ON DELETE CASCADE,
  CONSTRAINT `eventos_interessados_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idusuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos_interessados`
--

LOCK TABLES `eventos_interessados` WRITE;
/*!40000 ALTER TABLE `eventos_interessados` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos_interessados` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `seguidores`
--

DROP TABLE IF EXISTS `seguidores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguidores` (
  `idseguidor` int NOT NULL,
  `idusuario` int NOT NULL COMMENT 'O "idseguidor" não é gerado pelo banco, mas sim o id do usuário que está seguindo.\nE "idusuario" é o id de quem está sendo seguido.',
  PRIMARY KEY (`idseguidor`,`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguidores`
--

LOCK TABLES `seguidores` WRITE;
/*!40000 ALTER TABLE `seguidores` DISABLE KEYS */;
INSERT INTO `seguidores` VALUES (1,5),(1,7),(2,7),(3,5),(4,2),(4,3),(4,7),(5,7),(6,4),(6,6),(7,6),(8,6);
/*!40000 ALTER TABLE `seguidores` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `idusuarios` int NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `nome_usuario` varchar(45) DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `foto_perfil` varchar(45) DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `objetivos` text,
  `esportes_favoritos` text,
  PRIMARY KEY (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=armscii8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'email@email.com','$2y$12$R.OiVz0aFFkV0XGLUte0IOEuDvxcoD4UTmxW1bnWhcwjtYaLjwWTS','Carlos','Carlinn','1111-11-11','img_68dc631829b3e5.65682007.avif',NULL,NULL,NULL),(2,'email2@email.com','81dc9bdb52d04dc20036dbd8313ed055','Carlos','Carlinn','0111-11-01','img_68dc637bd3bb07.41277387.png',NULL,NULL,NULL),(3,'email@gmail.com','202cb962ac59075b964b07152d234b70','Carlos','Carlinn','2008-04-16','img_68dd7a07e95240.70326799.png',NULL,NULL,NULL),(4,'carlos@email.com','202cb962ac59075b964b07152d234b70','Carlos ','User','2008-04-16','img_68e7040ecc72a3.90917650.png','Masculino','terminar esse site','[\"Futebol\",\"Nata\\u00e7\\u00e3o\"]'),(5,'user2@user2.com','202cb962ac59075b964b07152d234b70','user2','user2','1111-11-11','',NULL,NULL,NULL),(6,'andre@email.com','202cb962ac59075b964b07152d234b70','Andre','Andrezinho','1111-11-11','img_68eceef9a34717.75077709.png',NULL,NULL,NULL),(7,'amanda@gmail.com','3f1bbc89706e4c2716e2dca536e6e277','Amanda Moresco','amandamoresco','2000-05-14','img_68f624c7b10f11.42663206.png',NULL,NULL,NULL),(8,'dsadsa@email.com','81dc9bdb52d04dc20036dbd8313ed055','dsa','user','0011-11-11','',NULL,NULL,NULL),(9,'oi@gmail.com','202cb962ac59075b964b07152d234b70','tesudinho','8===D','2025-03-12','img_6903a2146f8602.13926032.jpg',NULL,NULL,NULL);
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

-- Dump completed on 2025-11-03  0:38:10
