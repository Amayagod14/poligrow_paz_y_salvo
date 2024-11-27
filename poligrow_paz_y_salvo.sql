CREATE DATABASE  IF NOT EXISTS `poligrow_paz_y_salvo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `poligrow_paz_y_salvo`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: poligrow_paz_y_salvo
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
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(255) NOT NULL,
  `nit` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `logo` mediumblob DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `es_admin` tinyint(1) DEFAULT 0,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_retiro` date DEFAULT NULL,
  `motivo_retiro` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `idx_empleados_cedula` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'1','Admin ALMACEN-IDEMA','Administrador','ALMACEN-IDEMA','$2y$10$fDkTZ8Ft5aMEoCGJC6BUue7TG04USmmfh6ldHtVN1tbCjyC3bzf7y',1,NULL,NULL,NULL,'2024-11-27 21:46:17','2024-11-27 21:53:50'),(2,'2','Admin SGSST','Administrador','SGSST','$2y$10$JOhCP5AxX8sr4Wj65g5iAeEquxNqOOBoDeEDD8f03byASVxkgXTly',1,NULL,NULL,NULL,'2024-11-27 21:46:49','2024-11-27 21:53:50'),(3,'3','Admin DOTACION','Administrador','DOTACION','$2y$10$7Ky7W.lalhWBNMYlUyChQ.fLptdAs7WXtKU6PAgVZb5hsVGCKXua2',1,NULL,NULL,NULL,'2024-11-27 21:47:21','2024-11-27 21:53:50'),(4,'4','Admin CONTABILIDAD','Administrador','CONTABILIDAD','$2y$10$jyr9CX8eJJ6JJYssXhoi7.vErchhWqT8xnUJ6C4LrXrhMlU7RjFyC',1,NULL,NULL,NULL,'2024-11-27 21:47:50','2024-11-27 21:53:50'),(5,'5','Admin COORDINADOR/DIRECTOR','Administrador','COORDINADOR/DIRECTOR','$2y$10$WrJXgtipnEc0JQeNeuFAx.EmfpJ4peyhGgkPbH00SaadUxGNm3DAm',1,NULL,NULL,NULL,'2024-11-27 21:48:17','2024-11-27 21:53:50'),(6,'6','Admin ALIMENTACION','Administrador','ALIMENTACION','$2y$10$iYrsPAIjrxM.rQF/wS0CK./qkaWBbs4bM7zrgzVT/4jEbfpSMrb5y',1,NULL,NULL,NULL,'2024-11-27 21:48:38','2024-11-27 21:53:50'),(7,'7','Admin HOSPEDAJE/HERRAMIENTA','Administrador','HOSPEDAJE/HERRAMIENTA','$2y$10$YtJOtJYlBzNifn7Kd86SkOVyro/qmjYgOKqFQvRiU3qvrT5UNs2ce',1,NULL,NULL,NULL,'2024-11-27 21:49:02','2024-11-27 21:53:50'),(8,'8','Admin FUNDACION','Administrador','FUNDACION','$2y$10$onvp7sb2QidlfgFUO6kW4eVIc4pcfx/9uSs/M48uiBKK6Ff0oici6',1,NULL,NULL,NULL,'2024-11-27 21:49:20','2024-11-27 21:53:50'),(9,'9','Admin FONDO DE EMPLEADOS','Administrador','FONDO DE EMPLEADOS','$2y$10$Fk0BLXmCxJxxvmbH4e8Q9exmLopiNlEciF7Kgg6JxG6RJa9udrDfO',1,NULL,NULL,NULL,'2024-11-27 21:49:55','2024-11-27 21:53:50'),(10,'10','Admin SISTEMAS','Administrador','SISTEMAS','$2y$10$9s5nCoMVpjzp8GzdIPG3GulHEVMxZA1SE3nhxbsIrKGPE2j.nozRG',1,NULL,NULL,NULL,'2024-11-27 21:50:22','2024-11-27 21:53:50'),(11,'1121831496','sebastian amaya','auxiliar it','sistemas','$2y$10$fZsV0XPP3khJsZdTJV4Xce3NaD6AEiiPejy8sftyKS/UOo/OTnPse',0,'2024-11-01','2024-11-30','TERMINACION DE CONTRATO','2024-11-27 21:50:48','2024-11-27 21:52:53');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `firmas`
--

DROP TABLE IF EXISTS `firmas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firmas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paz_y_salvo_id` int(11) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `nombre_firmante` varchar(255) DEFAULT NULL,
  `fecha_firma` date DEFAULT NULL,
  `imagen_firma` mediumblob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_firma` (`paz_y_salvo_id`,`departamento`),
  KEY `idx_firmas_paz_y_salvo` (`paz_y_salvo_id`),
  CONSTRAINT `firmas_ibfk_1` FOREIGN KEY (`paz_y_salvo_id`) REFERENCES `paz_y_salvo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `firmas`
--

LOCK TABLES `firmas` WRITE;
/*!40000 ALTER TABLE `firmas` DISABLE KEYS */;
INSERT INTO `firmas` VALUES (1,1,'SISTEMAS','sebastian amaya','2024-11-26',_binary 'âPNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0º`ÿà\0\0\0sRGB\0Æ\Œ\È\0\0\0gAMA\0\0±è¸a\0\0\0	pHYs\0\0\√\0\0\√\«o®d\0\0\n(IDATx^\Ìù;nKÜ\€w<ñ`àëx$àv`∂Ä7`\…9l\0à!F\" 	H\»`∞K\Ìo4?˙\Ôπ\’\„ôèß\«¸ütò\Ó\Í™SU\ÁU=ÜΩìû.ÑWÛ\œ¸3ÑìBBH!!§ÑzRB)!ÑÇBO\nA!Ö ÑêBB\ËI!!§ÑRB=)!ÑÇB\nA°\'Ö`	~¸¯\—=x†ªr\Â º%l|rxx\ÿ=}˙t\ﬁ\÷!Ö\‡^ø~\››æ}ª{˜\Ó]˜˚˜\Ôyk\ÿ&/^ºò˘\‰Ÿ≥g\›˚˜\Ô\Á≠av¶|˛¸yV˝9ôëΩΩΩˇ…ù;w∫á\Œ˙û\Ã«©Û\ÊÕõ\Ÿ˝˝˚˜güa;|˚ˆm\Ê˚>¸) ∑n›ö}Ü5\·7ß ßOüN¯M≈ï\‰Ú\Â\Às\r\„yÙ\Ë\—L\œ◊Ø_Oû<y2\”˚\Í’´˘\”p\ﬁ<˛¸§ò˘£/\»¸¸Û\Á\œyè∞ì,\Ó\Ï1≤øø?\◊4/ﬂøü]≥ûp˛ê\Ë\◊c˘ôŒÜ\…\ﬁH<O\ÏUeù\09>>û\È\–\ÈOPQ\ÿ&J_õ\‡ô÷â˝.\nÿú∑\0|\Ïì\"è\r\÷-ˆ\·øL™∏£´( HÇùìZp\œ8Å\"Äû1\Ë¥Q\‡Ò:\ }Mºm¿˛eá∫?}uA.\ )©¡m\Ô˚ºHo\nL™(%$7mãõ§\Â4|˚ˆ\Ìºeú>\ÃI¢Å\∆=_∂ç\nâ\·P\–.õµû\Ô\Zä/ƒÄˆ…õQ8[&U¸É\À$ 	≤n\0Ωâh\Áıwùn\Ï°B\€–õk™_SÙ7\rOú]ÖΩ≥ó\ÍWˆ(\\Ñb75&UTÒu*ü*@!EÅ˘[Ø\‡Cˆ†dDN{mEØÇ∫æ ≥=k\…\ÿ\‚ƒú\Ëf_\Ëô\‚©*\÷BG\‚kˇ\Á˝’áπy\ÎTa∆Üõ\‚<\Á™L™\»\Ÿ¢\√:TÛ©SE_î`|.[+ù.ßΩ\Õ¯\È\Ê	…ú*ÜC\"ª\–Wz\ÕG?ˆ^ı ≤¡2∏ûU\∆-ãˆ\“z´Q°\∆6-ø`ìZ|\›>¨ª¢¢?{$ÔÇøá`\Ì¨±&0ÛSCâ=fÆ≥fRÖ†\ZC\‡Lº\»iã?0π(pî\»8Éπº0o\Ît\Áπçq9\Õy\nN¡|\‹c≠\ÀEß°K\Ì\Ël¡\ÎıPai%(ò}\ÿ\∆\Á[\Á§B?∫Y∂e/≤	s∞G/bå\√6àæ:∏=U$˘t˚˘^òOq\÷*¿º\Z[e®˙:±ü£Xi˘i\Ã\\õ`RÖ\0\'aò°\‡≈†C\\Òd!áaπG?\œi\„!*>∂AV\◊\«=\ÎVÄ\r9PÛy\– *6\Ã+§KØ =ì¥\ÊÛë0Nˆm%\ÎP_Ω©¥ˆª¨*≠5ç˘âÇ§6\Ìßµ^ˆ\ÓW{8ıπ\›ÚYk\Ô5ô5¯\Z}\Ïÿπ6≈§\nÅêì[ÜBîxÙk\·F\ÊS˝p<cïT\ﬁG˝|,‚ßüY\‚\Îpá{\“\nçïs’∑\’_Å†dt|§™∫~Ñ}\ÎÙ\’IYuÛL	$[¥í™µ¶e®\…2VXè÷¶6%tkΩÏ∑µÆ\›Ó≠±$%E\÷\ÁÚxÄ\Z^\\(\‚j«¶b\Ï\\õdíÖ¿¡â1ÅÑ±™3ub:8É\Á\nh\Ã5\"\Áª¡\’\Íi≠\”\÷Œº^à\‹\·51Å±å\—\‹\‡∫\ÕC{=Èπß]c±á\√zÿãû´\0=k\È&¡ıåu\"\\#\Ã\√3%\’™]\«\Î\◊~¥óZhgo\ZC=c\ZßÇzÆ1JJpõ3÷©˛\‡π\ÏSuj\›c\Á\⁄4ì/-îX2¶OX**àQ\›Ò† ù\’IÄÉ\‘F9‹±~\Z≠ï~éÙy\’˜µ≥æä\'ì\÷\Ê–¶\Áı4Òg^Ä{á=Hz\÷\Õ3ΩE\‘=å}\Ëïπoâäyk^\Ÿ	=¨U>ê/\Â[\Ï%˚”¶ˆj\ÌO˚\ËBß\œ\Â¯\Z=é<&?\∆Œµiv≤ÄéO°ì\r\·∫ı∆†\‡†OM8w éb?-\›A\’\·^ @[OfÆi˜d>::˙\œ⁄´.üã`j°±uO^ji\ŸQé\Ì<©ú1ÅJ¢ã}.\Z\ÔoXu/ ;±N˙b÷éNm\Óe>iW?«ã\ÎPbJá#\"öjL\‡+=Û1´ÃÖNdì\Ïl!\0w¢*]¡\‡\Z#gyÄÛ\\ª%\ﬁG¢É\ƒ\“á\0ˆÄ@\‰PúÆÑ$@YÛ\·\·as?@`†K˙jr∞n/\"\n<>ôáq\“\Ìcµ◊´¢¡úå\◊3&\ÈÙ@>\r˙j}n\√\Ëó==â@\◊\Ó\„⁄èyX≥\ÊSRi_ö_˚\–\\\ÿA∞W\Ó\ÈÒ˝™¯–éhn:D\œ}ú|∞\Í\\¨Qˆ\ﬂ[/lêÕ∫\√OCy0`@ÅA’Æ†G7!ßs≠¿P\…!¥\·D	:\⁄C_w(\…\ƒ5\¬:\ËãN∆≤N=C§ì˝2}πG/h}à÷ÑN˙∞f\Ìôy¥Û\‹˜\Õ3\ÊnÌóæÃ´{Æ},¢ıh\ﬂ\Ã≤≤åø\Ë#\›\⁄˜ihN;£É6≠î0“´\”Ræ†çO}`\›\ﬁΩÿÜ}\ G\‹c\Ë#Ω≤!˚\–:∏ó˝∏óü∏W˝ó\ÏÅk\ÈZu./õb´Ö@	E†°†&à\‰@∆∏∏°\‹!-\—\\^Ω]\‰G¡\‚¢d@_}\Ê:îC¢§SWq]≤W≠EÅ\„¢˝∂Ï¶π}útÅè¡Æ¨≈ü/Ç>ÙglÀ¶Chås˚úns%;(9\Ì\rZæ&\—¿\◊)\—zΩÄ∏0ˇ\–3|§ß˙±\¬˝òπŒÉ≠ÇVçëj®V\“\"\‹O§ñ\·\Âîû,ÙS`˙	\÷\“¡5m\ﬁG˝|\Ì≠ı0ß\'\0∫jOÃ∫w;x;\‚â\‚{µ{§&\‰\"XcŸ¥Ç\Ó:ß\Ô¡Aø\ÔTjª\Ôèı\‘7ì\ÍC,≠Xrü˘°#≤Æ\—…æ)¶Ó£±smö≠Ç\÷	µäê$CÜ•üO™t+Ä\Èá\”ÇgQêì®\Ëb^ÆÖü6\Ópá˛\n\Z\Õ\Â:Ñ=CA†\”F¡V°çg~RmZg\’\ÕZx^\€\’©…µ;:ó-Bo=\ÿYI≤,ÿæ5\Ì\ÿõ˘ò˘Øœâ.\Ìøµ\Ÿ\ÕcÇyhCü\Ë¨séôk\”l≠(\ËWƒ∏Udì\‡d(qßß \Îl†!HÇv’ΩQ»êÛ\ÊMÄ\rá∏$NUVM\ËU\Ê:kˆ¯ß_¸V\‡Ç?~¸8˚ij~%∏\“aw\È“•\Ó\ÊÕõ\››ªwª{˜\ÓÕüÑ0=Æ]ª\÷ıÖv~\◊u}\Ën‹∏1øõ6[-!\\¯\≈Î£££˘]\◊ıoQ≥_\‘\ﬁRBXì_ø~u◊Ø_ˇÛ\Î\«\«\«\›\„«èg◊ªB˛Éì\÷\‰\ÂÀóä\0_gw≠@\ﬁBX˛+<\n¡˛˛~˜\ÂÀó\Ó\Í’´Û\'ªC\nAk\¬ˇ≤ªÙ\«¡Jæ\ZÑ∞&≥?\ÓjÄºÑÚFBH!!Ù§ÑRB)!ÑûÇB\nA!Ö Ñ–ìBBH!!§ÑzRB)!ÑÇBO\nA!Ö ÑêBB\ËI!·Øß\Î˛…¨¢®è\Ì$\0\0\0\0IENDÆB`Ç','2024-11-27 22:01:00');
/*!40000 ALTER TABLE `firmas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paz_y_salvo`
--

DROP TABLE IF EXISTS `paz_y_salvo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paz_y_salvo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empleado_id` int(11) NOT NULL,
  `estado` enum('pendiente','en_proceso','completado') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empleado_id` (`empleado_id`),
  CONSTRAINT `paz_y_salvo_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paz_y_salvo`
--

LOCK TABLES `paz_y_salvo` WRITE;
/*!40000 ALTER TABLE `paz_y_salvo` DISABLE KEYS */;
INSERT INTO `paz_y_salvo` VALUES (1,11,'en_proceso','2024-11-27 21:52:53','2024-11-27 22:01:00');
/*!40000 ALTER TABLE `paz_y_salvo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'poligrow_paz_y_salvo'
--

--
-- Dumping routines for database 'poligrow_paz_y_salvo'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-27 17:09:28
