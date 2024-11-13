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
  `nombre` varchar(255) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_retiro` date NOT NULL,
  `motivo_retiro` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `documento` (`documento`),
  KEY `idx_empleados_documento` (`documento`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'felix barraza','1121831496','auxiliar it','sistemas','1970-01-01','1970-01-01','RENUNCIA','2024-11-13 19:11:40','2024-11-13 19:17:03'),(2,'sebastian amaya','12345678','auxiliar it','sistemas','2024-11-11','1970-01-01','TERMINACION DE CONTRATO','2024-11-13 20:02:19','2024-11-13 20:02:19');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `firmas`
--

LOCK TABLES `firmas` WRITE;
/*!40000 ALTER TABLE `firmas` DISABLE KEYS */;
INSERT INTO `firmas` VALUES (1,1,'ALMACEN-IDEMA','sebastian amaya',NULL,_binary '‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0¼`Øˆ\0\0\0sRGB\0®\Î\é\0\0\0gAMA\0\0±üa\0\0\0	pHYs\0\0\Ã\0\0\Ã\Ço¨d\0\0\n(IDATx^\í;nK†\Ûw<–`ˆ‘x$ˆv`¶€7`\É9l\0ˆ!F\" 	H\È`°Kğ\ío4?ú\ï¹\Õ\ã™§\ÇüŸt˜\î\êªSU\çU=†½“.„ğWó\Ïü3„ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!!¤„RB=)!„‚B\nA¡\'…`	~üø\Ñ=xğ »r\åÊ¼%l|rxx\Ø=}út\Ş\Ö!…\à^¿~\İİ¾}»{÷\î]÷û÷\ïyk\Ø&/^¼˜ù\äÙ³g\İû÷\ï\ç­av¦|şüyVı9™‘½½½ÿÉ;wº‡\Îú\ÌÇ©ó\æÍ›\Ùııû÷gŸa;|ûöm\æû>ü)Ê·nİš}†5\á7§Ê§OŸNøMÅ•\äò\å\Ës\r\ãyô\è\ÑL\Ï×¯_O<y2\Óû\êÕ«ù\Óp\Ş<şü¤˜ù£/\Èüüó\ç\Ïy°“,\î\ì1²¿¿?\×4/ß¿Ÿ]³pş\è\×cù™Î†\É\ŞH<O\ìUe\09>>\é\Ğ\éOPQ\Ø&J_›\à™Ö‰ı.\nØœ·\0|\ì“\"\r\Ö-ö\á¿Lª¸£«( H‚“Zp\Ï8\"€1\è´Q\àñ:\Ê}M¼mÀşe‡º?}uA.\Ê)©Ám\ïû¼Ho\nLª(%$7m‹›¤\å4|ûö\í¼eœ>\ÌI¢\Æ=_¶\n‰\áP\Ğ.›µ\ï\ZŠ/Ä€öÉ›Q8[&Uüƒ\Ë$ 	²n\0½‰h\çõwn\ì¡B\ÛĞ›kª_Sô7\rOœ]…½³—\êWö(\\„b75&UTñu*Ÿ*@!Eù[¯\àCğö dDN{mE¯‚º¾Ê³=k\É\Ø\âÄœ\èf_\è™\â©*\ÖBG\âkÿ\çıÕ‡¹y\ëTaÆ†›\â<\çªLª\È\Ù¢\Ã:Tó©SE_”`|.[+.§½\Íø\é\æ	Éœ*†C\"»\ĞWz\ÍG?ö^õ ²Á2¸U\Æ-‹ö\Òz«Q¡\Æ6-¿`“Z|\İ>¬»¢¢?{$ï‚¿‡`\í¬±&0óSC‰=f®³fR… \ZC\àL¼\Èi‹ğ?0¹(p”\È8ƒ¹¼0o\ët\ç¹q9\Íy\nNÁ|\Üc­\ËE§¡K\í\èlÁ\ëõPai%(˜}\Ø\Æ\ç[\ç¤B?ºY¶e/²	s°G/bŒ\Ã6ˆ¾:¸=U$ùtûù^˜Oq\Ö*À¼\Z[e¨ú:±Ÿ£Xiùi\Ì\\›`R…\0\'a˜¡\àÅ C\\ñd!‡a¹G?\Ïi\ã!*>¶AV\×\Ç=\ëV€\r9Póy\Ğ *6\Ì+¤K¯ =“´\æó‘0Nöm%\ëP_½©´ö»¬*­5ù‰‚¤6\í§µ^ö\îW{8õ¹\İòYk\ï5™5ø\Z}\ìØ¹6Å¤\n“[†B”xôk\áF\æSıp<c•T\ŞGı|,â§ŸY\â\ëp‡{\Ò\n•sÕ·\Õ_ dt|¤ªº~„}\ëô\ÕIYuóL	$[´’ªµ¦e¨\É2VXÖ¦6%tk½ì·µ®\İî­±$%E\Ö\çòx€\Z^\\(\âjÇ¦b\ì\\›d’…ÀÁ‰1„±ª3ub:8ƒ\ç\nh\Ì5\"\ç»Á\Õ\êi­\Ó\ÖÎ¼^ˆ\Ü\á51±Œ\Ñ\Ü\àº\ÍC{=é¹§]c±‡\ÃzØ‹«\0=k\é&ÁõŒu\"\\#\Ì\Ã3%\Õª]\Ç\ë\×~´—Zhgo\ZC=c\Z§‚z®1JJp›3Ö©ş\à¹\ìSuj\İc\ç\Ú4“/-”X2¦OX**ˆQ\İñ Ê\ÕI€ƒ\ÔF9Ü±~\Z­•~ôy\Õ÷µ³¾Š\'“\Ö\æĞ¦\çõ4ñg^€{‡=Hz\Ö\Í3½E\Ô=Œ}\è•¹o‰Šyk^\Ù	=¬U>/\å[\ì%ûÓ¦öj\íOû\èB§\Ï\åø\Z=<&?\ÆÎµiv²€O¡“\r\áºõÆ \à OM8w b?-\İA\Õ\á^ @[Of®i÷d>::ú\ÏÚ«.Ÿ‹`j¡±uO^ji\ÙQ\í<©œ1J¢‹}.\Z\ïoXu/ ;±NúbÖNm\îe>iW?Ç‹\ëPbJ‡#\"šjL\à+=ó1«Ì…Nd“\ìl!\0w¢ğ*]Á\à\Z#gy€ó\\»%\ŞG¢ƒ\Ä\Ò‡\0ö€@\äPœ®„$@Yó\á\áas?@` Kújr°n/\"\n<>™‡q\Ò\ícµ×«¢ÁœŒ\×3&\éô@>\rúj}n\Ã\è—==‰@\×\î\ãÚyX³\æSRi_š_û\Ğ\\\ØA°W\î\éñıªøĞhn:D\Ï}œ|°\ê\\¬Qö\ß[/lÍº\ÃOCy0`@AÕ® G7!§s­ÀP\É!´\áD	:\ÚC_w(\É\Ä5\Â:\è‹NÆ²N=C¤“ı2}¹G/h}ˆÖ„Nú°f\í™y´ó\Ü÷\Í3\æní—¾Ì«{®},¢õh\ß\Ì²²Œ¿\è#\İ\Ú÷ihN;£ƒ6­”0Ò«\ÓR¾ O}`\İ\Ş½Ø†}\ÊG\Üc\è#½²!û\Ğ:¸—ı¸—Ÿ¸Wı—\ìk\éZu./›b«…@	E ¡ &ˆ\ä@Æ¸¸¡\Ü!-\Ñ\\^½]\äGÁ\â¢d@_}\æ:”C¢¤SWq]²W­E\ã¢ı¶ì¦¹}œtÁ®¬ÅŸ/‚>ôglË¦ChŒsûœns%;(9\í\rZ¾&\ÑÀ\×)\Ñz½€¸0ÿ\Ğ3|¤§ú±\Âı˜¹Îƒ­‚V‘j¨V\Ò\"\ÜO¤–\á\å”,ôS`ú	\Ö\ÒÁ5m\ŞGı|\í­õ0§\'\0ºjOÌºw;x;\â‰\â{ğµ{¤&\ä\"XcÙ´‚\î:§\ïÁA¿\ïTj»\ïõ\Ô7“\êC,­XrŸù¡#²®\ÑÉ¾)¦î£±smš­‚\Ö	µŠ$C†¥ŸOªt+€\é‡\Ó‚gQ“¨\èb^®…Ÿ6\îp‡ş\n\Z\Í\å:„=CA \ÓFÁV¡g~RmZg\Õ\ÍZx^\Û\Õ©Éµ;:—-Bo=\ØYI²,Ø¾5\í\Ø›ù˜ùğ¯Ï‰.\í¿µ\Ù\Íc‚yhCŸ\è¬s™k\Ól­(\èWÄ¸Ud“\àd(q§§ \ël !H‚vÕ½QÈó\æM€\r‡¸$NUVM\èU\æ:köø§_üV\à‚?~ü8ûij~%¸\Òaw\éÒ¥\î\æÍ›\İİ»w»{÷\îÍŸ„0=®]»\Öõ…v~\×u}\ènÜ¸1¿›6[-!\\ø\Åë£££ù]\×õoQ³_\Ô\ŞRBX“_¿~u×¯_ÿó\ë\Ç\Ç\Ç\İ\ãÇg×»Bşƒ“\Ö\ä\åË—Š\0_gw­@\ŞBXş+<\nÁşş~÷\åË—\î\êÕ«ó\'»C\nAk\Âÿ²»ô\ÇÁJ¾\Z„°&³?\îj€¼„òFBH!!ô¤„RB)!„‚B\nA!… „Ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!á¯§\ëşÉ¬¢¨\í$\0\0\0\0IEND®B`‚','2024-11-13 19:16:41'),(4,1,'SGSST','sebastian amaya',NULL,_binary '‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0¼`Øˆ\0\0\0sRGB\0®\Î\é\0\0\0gAMA\0\0±üa\0\0\0	pHYs\0\0\Ã\0\0\Ã\Ço¨d\0\0\n(IDATx^\í;nK†\Ûw<–`ˆ‘x$ˆv`¶€7`\É9l\0ˆ!F\" 	H\È`°Kğ\ío4?ú\ï¹\Õ\ã™§\ÇüŸt˜\î\êªSU\çU=†½“.„ğWó\Ïü3„ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!!¤„RB=)!„‚B\nA¡\'…`	~üø\Ñ=xğ »r\åÊ¼%l|rxx\Ø=}út\Ş\Ö!…\à^¿~\İİ¾}»{÷\î]÷û÷\ïyk\Ø&/^¼˜ù\äÙ³g\İû÷\ï\ç­av¦|şüyVı9™‘½½½ÿÉ;wº‡\Îú\ÌÇ©ó\æÍ›\Ùııû÷gŸa;|ûöm\æû>ü)Ê·nİš}†5\á7§Ê§OŸNøMÅ•\äò\å\Ës\r\ãyô\è\ÑL\Ï×¯_O<y2\Óû\êÕ«ù\Óp\Ş<şü¤˜ù£/\Èüüó\ç\Ïy°“,\î\ì1²¿¿?\×4/ß¿Ÿ]³pş\è\×cù™Î†\É\ŞH<O\ìUe\09>>\é\Ğ\éOPQ\Ø&J_›\à™Ö‰ı.\nØœ·\0|\ì“\"\r\Ö-ö\á¿Lª¸£«( H‚“Zp\Ï8\"€1\è´Q\àñ:\Ê}M¼mÀşe‡º?}uA.\Ê)©Ám\ïû¼Ho\nLª(%$7m‹›¤\å4|ûö\í¼eœ>\ÌI¢\Æ=_¶\n‰\áP\Ğ.›µ\ï\ZŠ/Ä€öÉ›Q8[&Uüƒ\Ë$ 	²n\0½‰h\çõwn\ì¡B\ÛĞ›kª_Sô7\rOœ]…½³—\êWö(\\„b75&UTñu*Ÿ*@!Eù[¯\àCğö dDN{mE¯‚º¾Ê³=k\É\Ø\âÄœ\èf_\è™\â©*\ÖBG\âkÿ\çıÕ‡¹y\ëTaÆ†›\â<\çªLª\È\Ù¢\Ã:Tó©SE_”`|.[+.§½\Íø\é\æ	Éœ*†C\"»\ĞWz\ÍG?ö^õ ²Á2¸U\Æ-‹ö\Òz«Q¡\Æ6-¿`“Z|\İ>¬»¢¢?{$ï‚¿‡`\í¬±&0óSC‰=f®³fR… \ZC\àL¼\Èi‹ğ?0¹(p”\È8ƒ¹¼0o\ët\ç¹q9\Íy\nNÁ|\Üc­\ËE§¡K\í\èlÁ\ëõPai%(˜}\Ø\Æ\ç[\ç¤B?ºY¶e/²	s°G/bŒ\Ã6ˆ¾:¸=U$ùtûù^˜Oq\Ö*À¼\Z[e¨ú:±Ÿ£Xiùi\Ì\\›`R…\0\'a˜¡\àÅ C\\ñd!‡a¹G?\Ïi\ã!*>¶AV\×\Ç=\ëV€\r9Póy\Ğ *6\Ì+¤K¯ =“´\æó‘0Nöm%\ëP_½©´ö»¬*­5ù‰‚¤6\í§µ^ö\îW{8õ¹\İòYk\ï5™5ø\Z}\ìØ¹6Å¤\n“[†B”xôk\áF\æSıp<c•T\ŞGı|,â§ŸY\â\ëp‡{\Ò\n•sÕ·\Õ_ dt|¤ªº~„}\ëô\ÕIYuóL	$[´’ªµ¦e¨\É2VXÖ¦6%tk½ì·µ®\İî­±$%E\Ö\çòx€\Z^\\(\âjÇ¦b\ì\\›d’…ÀÁ‰1„±ª3ub:8ƒ\ç\nh\Ì5\"\ç»Á\Õ\êi­\Ó\ÖÎ¼^ˆ\Ü\á51±Œ\Ñ\Ü\àº\ÍC{=é¹§]c±‡\ÃzØ‹«\0=k\é&ÁõŒu\"\\#\Ì\Ã3%\Õª]\Ç\ë\×~´—Zhgo\ZC=c\Z§‚z®1JJp›3Ö©ş\à¹\ìSuj\İc\ç\Ú4“/-”X2¦OX**ˆQ\İñ Ê\ÕI€ƒ\ÔF9Ü±~\Z­•~ôy\Õ÷µ³¾Š\'“\Ö\æĞ¦\çõ4ñg^€{‡=Hz\Ö\Í3½E\Ô=Œ}\è•¹o‰Šyk^\Ù	=¬U>/\å[\ì%ûÓ¦öj\íOû\èB§\Ï\åø\Z=<&?\ÆÎµiv²€O¡“\r\áºõÆ \à OM8w b?-\İA\Õ\á^ @[Of®i÷d>::ú\ÏÚ«.Ÿ‹`j¡±uO^ji\ÙQ\í<©œ1J¢‹}.\Z\ïoXu/ ;±NúbÖNm\îe>iW?Ç‹\ëPbJ‡#\"šjL\à+=ó1«Ì…Nd“\ìl!\0w¢ğ*]Á\à\Z#gy€ó\\»%\ŞG¢ƒ\Ä\Ò‡\0ö€@\äPœ®„$@Yó\á\áas?@` Kújr°n/\"\n<>™‡q\Ò\ícµ×«¢ÁœŒ\×3&\éô@>\rúj}n\Ã\è—==‰@\×\î\ãÚyX³\æSRi_š_û\Ğ\\\ØA°W\î\éñıªøĞhn:D\Ï}œ|°\ê\\¬Qö\ß[/lÍº\ÃOCy0`@AÕ® G7!§s­ÀP\É!´\áD	:\ÚC_w(\É\Ä5\Â:\è‹NÆ²N=C¤“ı2}¹G/h}ˆÖ„Nú°f\í™y´ó\Ü÷\Í3\æní—¾Ì«{®},¢õh\ß\Ì²²Œ¿\è#\İ\Ú÷ihN;£ƒ6­”0Ò«\ÓR¾ O}`\İ\Ş½Ø†}\ÊG\Üc\è#½²!û\Ğ:¸—ı¸—Ÿ¸Wı—\ìk\éZu./›b«…@	E ¡ &ˆ\ä@Æ¸¸¡\Ü!-\Ñ\\^½]\äGÁ\â¢d@_}\æ:”C¢¤SWq]²W­E\ã¢ı¶ì¦¹}œtÁ®¬ÅŸ/‚>ôglË¦ChŒsûœns%;(9\í\rZ¾&\ÑÀ\×)\Ñz½€¸0ÿ\Ğ3|¤§ú±\Âı˜¹Îƒ­‚V‘j¨V\Ò\"\ÜO¤–\á\å”,ôS`ú	\Ö\ÒÁ5m\ŞGı|\í­õ0§\'\0ºjOÌºw;x;\â‰\â{ğµ{¤&\ä\"XcÙ´‚\î:§\ïÁA¿\ïTj»\ïõ\Ô7“\êC,­XrŸù¡#²®\ÑÉ¾)¦î£±smš­‚\Ö	µŠ$C†¥ŸOªt+€\é‡\Ó‚gQ“¨\èb^®…Ÿ6\îp‡ş\n\Z\Í\å:„=CA \ÓFÁV¡g~RmZg\Õ\ÍZx^\Û\Õ©Éµ;:—-Bo=\ØYI²,Ø¾5\í\Ø›ù˜ùğ¯Ï‰.\í¿µ\Ù\Íc‚yhCŸ\è¬s™k\Ól­(\èWÄ¸Ud“\àd(q§§ \ël !H‚vÕ½QÈó\æM€\r‡¸$NUVM\èU\æ:köø§_üV\à‚?~ü8ûij~%¸\Òaw\éÒ¥\î\æÍ›\İİ»w»{÷\îÍŸ„0=®]»\Öõ…v~\×u}\ènÜ¸1¿›6[-!\\ø\Åë£££ù]\×õoQ³_\Ô\ŞRBX“_¿~u×¯_ÿó\ë\Ç\Ç\Ç\İ\ãÇg×»Bşƒ“\Ö\ä\åË—Š\0_gw­@\ŞBXş+<\nÁşş~÷\åË—\î\êÕ«ó\'»C\nAk\Âÿ²»ô\ÇÁJ¾\Z„°&³?\îj€¼„òFBH!!ô¤„RB)!„‚B\nA!… „Ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!á¯§\ëşÉ¬¢¨\í$\0\0\0\0IEND®B`‚','2024-11-13 19:41:31'),(6,1,'SISTEMAS','sebastian amaya',NULL,_binary '‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0¼`Øˆ\0\0\0sRGB\0®\Î\é\0\0\0gAMA\0\0±üa\0\0\0	pHYs\0\0\Ã\0\0\Ã\Ço¨d\0\0\n(IDATx^\í;nK†\Ûw<–`ˆ‘x$ˆv`¶€7`\É9l\0ˆ!F\" 	H\È`°Kğ\ío4?ú\ï¹\Õ\ã™§\ÇüŸt˜\î\êªSU\çU=†½“.„ğWó\Ïü3„ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!!¤„RB=)!„‚B\nA¡\'…`	~üø\Ñ=xğ »r\åÊ¼%l|rxx\Ø=}út\Ş\Ö!…\à^¿~\İİ¾}»{÷\î]÷û÷\ïyk\Ø&/^¼˜ù\äÙ³g\İû÷\ï\ç­av¦|şüyVı9™‘½½½ÿÉ;wº‡\Îú\ÌÇ©ó\æÍ›\Ùııû÷gŸa;|ûöm\æû>ü)Ê·nİš}†5\á7§Ê§OŸNøMÅ•\äò\å\Ës\r\ãyô\è\ÑL\Ï×¯_O<y2\Óû\êÕ«ù\Óp\Ş<şü¤˜ù£/\Èüüó\ç\Ïy°“,\î\ì1²¿¿?\×4/ß¿Ÿ]³pş\è\×cù™Î†\É\ŞH<O\ìUe\09>>\é\Ğ\éOPQ\Ø&J_›\à™Ö‰ı.\nØœ·\0|\ì“\"\r\Ö-ö\á¿Lª¸£«( H‚“Zp\Ï8\"€1\è´Q\àñ:\Ê}M¼mÀşe‡º?}uA.\Ê)©Ám\ïû¼Ho\nLª(%$7m‹›¤\å4|ûö\í¼eœ>\ÌI¢\Æ=_¶\n‰\áP\Ğ.›µ\ï\ZŠ/Ä€öÉ›Q8[&Uüƒ\Ë$ 	²n\0½‰h\çõwn\ì¡B\ÛĞ›kª_Sô7\rOœ]…½³—\êWö(\\„b75&UTñu*Ÿ*@!Eù[¯\àCğö dDN{mE¯‚º¾Ê³=k\É\Ø\âÄœ\èf_\è™\â©*\ÖBG\âkÿ\çıÕ‡¹y\ëTaÆ†›\â<\çªLª\È\Ù¢\Ã:Tó©SE_”`|.[+.§½\Íø\é\æ	Éœ*†C\"»\ĞWz\ÍG?ö^õ ²Á2¸U\Æ-‹ö\Òz«Q¡\Æ6-¿`“Z|\İ>¬»¢¢?{$ï‚¿‡`\í¬±&0óSC‰=f®³fR… \ZC\àL¼\Èi‹ğ?0¹(p”\È8ƒ¹¼0o\ët\ç¹q9\Íy\nNÁ|\Üc­\ËE§¡K\í\èlÁ\ëõPai%(˜}\Ø\Æ\ç[\ç¤B?ºY¶e/²	s°G/bŒ\Ã6ˆ¾:¸=U$ùtûù^˜Oq\Ö*À¼\Z[e¨ú:±Ÿ£Xiùi\Ì\\›`R…\0\'a˜¡\àÅ C\\ñd!‡a¹G?\Ïi\ã!*>¶AV\×\Ç=\ëV€\r9Póy\Ğ *6\Ì+¤K¯ =“´\æó‘0Nöm%\ëP_½©´ö»¬*­5ù‰‚¤6\í§µ^ö\îW{8õ¹\İòYk\ï5™5ø\Z}\ìØ¹6Å¤\n“[†B”xôk\áF\æSıp<c•T\ŞGı|,â§ŸY\â\ëp‡{\Ò\n•sÕ·\Õ_ dt|¤ªº~„}\ëô\ÕIYuóL	$[´’ªµ¦e¨\É2VXÖ¦6%tk½ì·µ®\İî­±$%E\Ö\çòx€\Z^\\(\âjÇ¦b\ì\\›d’…ÀÁ‰1„±ª3ub:8ƒ\ç\nh\Ì5\"\ç»Á\Õ\êi­\Ó\ÖÎ¼^ˆ\Ü\á51±Œ\Ñ\Ü\àº\ÍC{=é¹§]c±‡\ÃzØ‹«\0=k\é&ÁõŒu\"\\#\Ì\Ã3%\Õª]\Ç\ë\×~´—Zhgo\ZC=c\Z§‚z®1JJp›3Ö©ş\à¹\ìSuj\İc\ç\Ú4“/-”X2¦OX**ˆQ\İñ Ê\ÕI€ƒ\ÔF9Ü±~\Z­•~ôy\Õ÷µ³¾Š\'“\Ö\æĞ¦\çõ4ñg^€{‡=Hz\Ö\Í3½E\Ô=Œ}\è•¹o‰Šyk^\Ù	=¬U>/\å[\ì%ûÓ¦öj\íOû\èB§\Ï\åø\Z=<&?\ÆÎµiv²€O¡“\r\áºõÆ \à OM8w b?-\İA\Õ\á^ @[Of®i÷d>::ú\ÏÚ«.Ÿ‹`j¡±uO^ji\ÙQ\í<©œ1J¢‹}.\Z\ïoXu/ ;±NúbÖNm\îe>iW?Ç‹\ëPbJ‡#\"šjL\à+=ó1«Ì…Nd“\ìl!\0w¢ğ*]Á\à\Z#gy€ó\\»%\ŞG¢ƒ\Ä\Ò‡\0ö€@\äPœ®„$@Yó\á\áas?@` Kújr°n/\"\n<>™‡q\Ò\ícµ×«¢ÁœŒ\×3&\éô@>\rúj}n\Ã\è—==‰@\×\î\ãÚyX³\æSRi_š_û\Ğ\\\ØA°W\î\éñıªøĞhn:D\Ï}œ|°\ê\\¬Qö\ß[/lÍº\ÃOCy0`@AÕ® G7!§s­ÀP\É!´\áD	:\ÚC_w(\É\Ä5\Â:\è‹NÆ²N=C¤“ı2}¹G/h}ˆÖ„Nú°f\í™y´ó\Ü÷\Í3\æní—¾Ì«{®},¢õh\ß\Ì²²Œ¿\è#\İ\Ú÷ihN;£ƒ6­”0Ò«\ÓR¾ O}`\İ\Ş½Ø†}\ÊG\Üc\è#½²!û\Ğ:¸—ı¸—Ÿ¸Wı—\ìk\éZu./›b«…@	E ¡ &ˆ\ä@Æ¸¸¡\Ü!-\Ñ\\^½]\äGÁ\â¢d@_}\æ:”C¢¤SWq]²W­E\ã¢ı¶ì¦¹}œtÁ®¬ÅŸ/‚>ôglË¦ChŒsûœns%;(9\í\rZ¾&\ÑÀ\×)\Ñz½€¸0ÿ\Ğ3|¤§ú±\Âı˜¹Îƒ­‚V‘j¨V\Ò\"\ÜO¤–\á\å”,ôS`ú	\Ö\ÒÁ5m\ŞGı|\í­õ0§\'\0ºjOÌºw;x;\â‰\â{ğµ{¤&\ä\"XcÙ´‚\î:§\ïÁA¿\ïTj»\ïõ\Ô7“\êC,­XrŸù¡#²®\ÑÉ¾)¦î£±smš­‚\Ö	µŠ$C†¥ŸOªt+€\é‡\Ó‚gQ“¨\èb^®…Ÿ6\îp‡ş\n\Z\Í\å:„=CA \ÓFÁV¡g~RmZg\Õ\ÍZx^\Û\Õ©Éµ;:—-Bo=\ØYI²,Ø¾5\í\Ø›ù˜ùğ¯Ï‰.\í¿µ\Ù\Íc‚yhCŸ\è¬s™k\Ól­(\èWÄ¸Ud“\àd(q§§ \ël !H‚vÕ½QÈó\æM€\r‡¸$NUVM\èU\æ:köø§_üV\à‚?~ü8ûij~%¸\Òaw\éÒ¥\î\æÍ›\İİ»w»{÷\îÍŸ„0=®]»\Öõ…v~\×u}\ènÜ¸1¿›6[-!\\ø\Åë£££ù]\×õoQ³_\Ô\ŞRBX“_¿~u×¯_ÿó\ë\Ç\Ç\Ç\İ\ãÇg×»Bşƒ“\Ö\ä\åË—Š\0_gw­@\ŞBXş+<\nÁşş~÷\åË—\î\êÕ«ó\'»C\nAk\Âÿ²»ô\ÇÁJ¾\Z„°&³?\îj€¼„òFBH!!ô¤„RB)!„‚B\nA!… „Ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!á¯§\ëşÉ¬¢¨\í$\0\0\0\0IEND®B`‚','2024-11-13 19:59:56'),(7,2,'ALMACEN-IDEMA','sebastian amaya',NULL,_binary '‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0¼`Øˆ\0\0\0sRGB\0®\Î\é\0\0\0gAMA\0\0±üa\0\0\0	pHYs\0\0\Ã\0\0\Ã\Ço¨d\0\0\n(IDATx^\í;nK†\Ûw<–`ˆ‘x$ˆv`¶€7`\É9l\0ˆ!F\" 	H\È`°Kğ\ío4?ú\ï¹\Õ\ã™§\ÇüŸt˜\î\êªSU\çU=†½“.„ğWó\Ïü3„ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!!¤„RB=)!„‚B\nA¡\'…`	~üø\Ñ=xğ »r\åÊ¼%l|rxx\Ø=}út\Ş\Ö!…\à^¿~\İİ¾}»{÷\î]÷û÷\ïyk\Ø&/^¼˜ù\äÙ³g\İû÷\ï\ç­av¦|şüyVı9™‘½½½ÿÉ;wº‡\Îú\ÌÇ©ó\æÍ›\Ùııû÷gŸa;|ûöm\æû>ü)Ê·nİš}†5\á7§Ê§OŸNøMÅ•\äò\å\Ës\r\ãyô\è\ÑL\Ï×¯_O<y2\Óû\êÕ«ù\Óp\Ş<şü¤˜ù£/\Èüüó\ç\Ïy°“,\î\ì1²¿¿?\×4/ß¿Ÿ]³pş\è\×cù™Î†\É\ŞH<O\ìUe\09>>\é\Ğ\éOPQ\Ø&J_›\à™Ö‰ı.\nØœ·\0|\ì“\"\r\Ö-ö\á¿Lª¸£«( H‚“Zp\Ï8\"€1\è´Q\àñ:\Ê}M¼mÀşe‡º?}uA.\Ê)©Ám\ïû¼Ho\nLª(%$7m‹›¤\å4|ûö\í¼eœ>\ÌI¢\Æ=_¶\n‰\áP\Ğ.›µ\ï\ZŠ/Ä€öÉ›Q8[&Uüƒ\Ë$ 	²n\0½‰h\çõwn\ì¡B\ÛĞ›kª_Sô7\rOœ]…½³—\êWö(\\„b75&UTñu*Ÿ*@!Eù[¯\àCğö dDN{mE¯‚º¾Ê³=k\É\Ø\âÄœ\èf_\è™\â©*\ÖBG\âkÿ\çıÕ‡¹y\ëTaÆ†›\â<\çªLª\È\Ù¢\Ã:Tó©SE_”`|.[+.§½\Íø\é\æ	Éœ*†C\"»\ĞWz\ÍG?ö^õ ²Á2¸U\Æ-‹ö\Òz«Q¡\Æ6-¿`“Z|\İ>¬»¢¢?{$ï‚¿‡`\í¬±&0óSC‰=f®³fR… \ZC\àL¼\Èi‹ğ?0¹(p”\È8ƒ¹¼0o\ët\ç¹q9\Íy\nNÁ|\Üc­\ËE§¡K\í\èlÁ\ëõPai%(˜}\Ø\Æ\ç[\ç¤B?ºY¶e/²	s°G/bŒ\Ã6ˆ¾:¸=U$ùtûù^˜Oq\Ö*À¼\Z[e¨ú:±Ÿ£Xiùi\Ì\\›`R…\0\'a˜¡\àÅ C\\ñd!‡a¹G?\Ïi\ã!*>¶AV\×\Ç=\ëV€\r9Póy\Ğ *6\Ì+¤K¯ =“´\æó‘0Nöm%\ëP_½©´ö»¬*­5ù‰‚¤6\í§µ^ö\îW{8õ¹\İòYk\ï5™5ø\Z}\ìØ¹6Å¤\n“[†B”xôk\áF\æSıp<c•T\ŞGı|,â§ŸY\â\ëp‡{\Ò\n•sÕ·\Õ_ dt|¤ªº~„}\ëô\ÕIYuóL	$[´’ªµ¦e¨\É2VXÖ¦6%tk½ì·µ®\İî­±$%E\Ö\çòx€\Z^\\(\âjÇ¦b\ì\\›d’…ÀÁ‰1„±ª3ub:8ƒ\ç\nh\Ì5\"\ç»Á\Õ\êi­\Ó\ÖÎ¼^ˆ\Ü\á51±Œ\Ñ\Ü\àº\ÍC{=é¹§]c±‡\ÃzØ‹«\0=k\é&ÁõŒu\"\\#\Ì\Ã3%\Õª]\Ç\ë\×~´—Zhgo\ZC=c\Z§‚z®1JJp›3Ö©ş\à¹\ìSuj\İc\ç\Ú4“/-”X2¦OX**ˆQ\İñ Ê\ÕI€ƒ\ÔF9Ü±~\Z­•~ôy\Õ÷µ³¾Š\'“\Ö\æĞ¦\çõ4ñg^€{‡=Hz\Ö\Í3½E\Ô=Œ}\è•¹o‰Šyk^\Ù	=¬U>/\å[\ì%ûÓ¦öj\íOû\èB§\Ï\åø\Z=<&?\ÆÎµiv²€O¡“\r\áºõÆ \à OM8w b?-\İA\Õ\á^ @[Of®i÷d>::ú\ÏÚ«.Ÿ‹`j¡±uO^ji\ÙQ\í<©œ1J¢‹}.\Z\ïoXu/ ;±NúbÖNm\îe>iW?Ç‹\ëPbJ‡#\"šjL\à+=ó1«Ì…Nd“\ìl!\0w¢ğ*]Á\à\Z#gy€ó\\»%\ŞG¢ƒ\Ä\Ò‡\0ö€@\äPœ®„$@Yó\á\áas?@` Kújr°n/\"\n<>™‡q\Ò\ícµ×«¢ÁœŒ\×3&\éô@>\rúj}n\Ã\è—==‰@\×\î\ãÚyX³\æSRi_š_û\Ğ\\\ØA°W\î\éñıªøĞhn:D\Ï}œ|°\ê\\¬Qö\ß[/lÍº\ÃOCy0`@AÕ® G7!§s­ÀP\É!´\áD	:\ÚC_w(\É\Ä5\Â:\è‹NÆ²N=C¤“ı2}¹G/h}ˆÖ„Nú°f\í™y´ó\Ü÷\Í3\æní—¾Ì«{®},¢õh\ß\Ì²²Œ¿\è#\İ\Ú÷ihN;£ƒ6­”0Ò«\ÓR¾ O}`\İ\Ş½Ø†}\ÊG\Üc\è#½²!û\Ğ:¸—ı¸—Ÿ¸Wı—\ìk\éZu./›b«…@	E ¡ &ˆ\ä@Æ¸¸¡\Ü!-\Ñ\\^½]\äGÁ\â¢d@_}\æ:”C¢¤SWq]²W­E\ã¢ı¶ì¦¹}œtÁ®¬ÅŸ/‚>ôglË¦ChŒsûœns%;(9\í\rZ¾&\ÑÀ\×)\Ñz½€¸0ÿ\Ğ3|¤§ú±\Âı˜¹Îƒ­‚V‘j¨V\Ò\"\ÜO¤–\á\å”,ôS`ú	\Ö\ÒÁ5m\ŞGı|\í­õ0§\'\0ºjOÌºw;x;\â‰\â{ğµ{¤&\ä\"XcÙ´‚\î:§\ïÁA¿\ïTj»\ïõ\Ô7“\êC,­XrŸù¡#²®\ÑÉ¾)¦î£±smš­‚\Ö	µŠ$C†¥ŸOªt+€\é‡\Ó‚gQ“¨\èb^®…Ÿ6\îp‡ş\n\Z\Í\å:„=CA \ÓFÁV¡g~RmZg\Õ\ÍZx^\Û\Õ©Éµ;:—-Bo=\ØYI²,Ø¾5\í\Ø›ù˜ùğ¯Ï‰.\í¿µ\Ù\Íc‚yhCŸ\è¬s™k\Ól­(\èWÄ¸Ud“\àd(q§§ \ël !H‚vÕ½QÈó\æM€\r‡¸$NUVM\èU\æ:köø§_üV\à‚?~ü8ûij~%¸\Òaw\éÒ¥\î\æÍ›\İİ»w»{÷\îÍŸ„0=®]»\Öõ…v~\×u}\ènÜ¸1¿›6[-!\\ø\Åë£££ù]\×õoQ³_\Ô\ŞRBX“_¿~u×¯_ÿó\ë\Ç\Ç\Ç\İ\ãÇg×»Bşƒ“\Ö\ä\åË—Š\0_gw­@\ŞBXş+<\nÁşş~÷\åË—\î\êÕ«ó\'»C\nAk\Âÿ²»ô\ÇÁJ¾\Z„°&³?\îj€¼„òFBH!!ô¤„RB)!„‚B\nA!… „Ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!á¯§\ëşÉ¬¢¨\í$\0\0\0\0IEND®B`‚','2024-11-13 20:03:03'),(8,2,'FUNDACION','sebastian amaya',NULL,_binary '‰PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0¼`Øˆ\0\0\0sRGB\0®\Î\é\0\0\0gAMA\0\0±üa\0\0\0	pHYs\0\0\Ã\0\0\Ã\Ço¨d\0\0\n(IDATx^\í;nK†\Ûw<–`ˆ‘x$ˆv`¶€7`\É9l\0ˆ!F\" 	H\È`°Kğ\ío4?ú\ï¹\Õ\ã™§\ÇüŸt˜\î\êªSU\çU=†½“.„ğWó\Ïü3„ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!!¤„RB=)!„‚B\nA¡\'…`	~üø\Ñ=xğ »r\åÊ¼%l|rxx\Ø=}út\Ş\Ö!…\à^¿~\İİ¾}»{÷\î]÷û÷\ïyk\Ø&/^¼˜ù\äÙ³g\İû÷\ï\ç­av¦|şüyVı9™‘½½½ÿÉ;wº‡\Îú\ÌÇ©ó\æÍ›\Ùııû÷gŸa;|ûöm\æû>ü)Ê·nİš}†5\á7§Ê§OŸNøMÅ•\äò\å\Ës\r\ãyô\è\ÑL\Ï×¯_O<y2\Óû\êÕ«ù\Óp\Ş<şü¤˜ù£/\Èüüó\ç\Ïy°“,\î\ì1²¿¿?\×4/ß¿Ÿ]³pş\è\×cù™Î†\É\ŞH<O\ìUe\09>>\é\Ğ\éOPQ\Ø&J_›\à™Ö‰ı.\nØœ·\0|\ì“\"\r\Ö-ö\á¿Lª¸£«( H‚“Zp\Ï8\"€1\è´Q\àñ:\Ê}M¼mÀşe‡º?}uA.\Ê)©Ám\ïû¼Ho\nLª(%$7m‹›¤\å4|ûö\í¼eœ>\ÌI¢\Æ=_¶\n‰\áP\Ğ.›µ\ï\ZŠ/Ä€öÉ›Q8[&Uüƒ\Ë$ 	²n\0½‰h\çõwn\ì¡B\ÛĞ›kª_Sô7\rOœ]…½³—\êWö(\\„b75&UTñu*Ÿ*@!Eù[¯\àCğö dDN{mE¯‚º¾Ê³=k\É\Ø\âÄœ\èf_\è™\â©*\ÖBG\âkÿ\çıÕ‡¹y\ëTaÆ†›\â<\çªLª\È\Ù¢\Ã:Tó©SE_”`|.[+.§½\Íø\é\æ	Éœ*†C\"»\ĞWz\ÍG?ö^õ ²Á2¸U\Æ-‹ö\Òz«Q¡\Æ6-¿`“Z|\İ>¬»¢¢?{$ï‚¿‡`\í¬±&0óSC‰=f®³fR… \ZC\àL¼\Èi‹ğ?0¹(p”\È8ƒ¹¼0o\ët\ç¹q9\Íy\nNÁ|\Üc­\ËE§¡K\í\èlÁ\ëõPai%(˜}\Ø\Æ\ç[\ç¤B?ºY¶e/²	s°G/bŒ\Ã6ˆ¾:¸=U$ùtûù^˜Oq\Ö*À¼\Z[e¨ú:±Ÿ£Xiùi\Ì\\›`R…\0\'a˜¡\àÅ C\\ñd!‡a¹G?\Ïi\ã!*>¶AV\×\Ç=\ëV€\r9Póy\Ğ *6\Ì+¤K¯ =“´\æó‘0Nöm%\ëP_½©´ö»¬*­5ù‰‚¤6\í§µ^ö\îW{8õ¹\İòYk\ï5™5ø\Z}\ìØ¹6Å¤\n“[†B”xôk\áF\æSıp<c•T\ŞGı|,â§ŸY\â\ëp‡{\Ò\n•sÕ·\Õ_ dt|¤ªº~„}\ëô\ÕIYuóL	$[´’ªµ¦e¨\É2VXÖ¦6%tk½ì·µ®\İî­±$%E\Ö\çòx€\Z^\\(\âjÇ¦b\ì\\›d’…ÀÁ‰1„±ª3ub:8ƒ\ç\nh\Ì5\"\ç»Á\Õ\êi­\Ó\ÖÎ¼^ˆ\Ü\á51±Œ\Ñ\Ü\àº\ÍC{=é¹§]c±‡\ÃzØ‹«\0=k\é&ÁõŒu\"\\#\Ì\Ã3%\Õª]\Ç\ë\×~´—Zhgo\ZC=c\Z§‚z®1JJp›3Ö©ş\à¹\ìSuj\İc\ç\Ú4“/-”X2¦OX**ˆQ\İñ Ê\ÕI€ƒ\ÔF9Ü±~\Z­•~ôy\Õ÷µ³¾Š\'“\Ö\æĞ¦\çõ4ñg^€{‡=Hz\Ö\Í3½E\Ô=Œ}\è•¹o‰Šyk^\Ù	=¬U>/\å[\ì%ûÓ¦öj\íOû\èB§\Ï\åø\Z=<&?\ÆÎµiv²€O¡“\r\áºõÆ \à OM8w b?-\İA\Õ\á^ @[Of®i÷d>::ú\ÏÚ«.Ÿ‹`j¡±uO^ji\ÙQ\í<©œ1J¢‹}.\Z\ïoXu/ ;±NúbÖNm\îe>iW?Ç‹\ëPbJ‡#\"šjL\à+=ó1«Ì…Nd“\ìl!\0w¢ğ*]Á\à\Z#gy€ó\\»%\ŞG¢ƒ\Ä\Ò‡\0ö€@\äPœ®„$@Yó\á\áas?@` Kújr°n/\"\n<>™‡q\Ò\ícµ×«¢ÁœŒ\×3&\éô@>\rúj}n\Ã\è—==‰@\×\î\ãÚyX³\æSRi_š_û\Ğ\\\ØA°W\î\éñıªøĞhn:D\Ï}œ|°\ê\\¬Qö\ß[/lÍº\ÃOCy0`@AÕ® G7!§s­ÀP\É!´\áD	:\ÚC_w(\É\Ä5\Â:\è‹NÆ²N=C¤“ı2}¹G/h}ˆÖ„Nú°f\í™y´ó\Ü÷\Í3\æní—¾Ì«{®},¢õh\ß\Ì²²Œ¿\è#\İ\Ú÷ihN;£ƒ6­”0Ò«\ÓR¾ O}`\İ\Ş½Ø†}\ÊG\Üc\è#½²!û\Ğ:¸—ı¸—Ÿ¸Wı—\ìk\éZu./›b«…@	E ¡ &ˆ\ä@Æ¸¸¡\Ü!-\Ñ\\^½]\äGÁ\â¢d@_}\æ:”C¢¤SWq]²W­E\ã¢ı¶ì¦¹}œtÁ®¬ÅŸ/‚>ôglË¦ChŒsûœns%;(9\í\rZ¾&\ÑÀ\×)\Ñz½€¸0ÿ\Ğ3|¤§ú±\Âı˜¹Îƒ­‚V‘j¨V\Ò\"\ÜO¤–\á\å”,ôS`ú	\Ö\ÒÁ5m\ŞGı|\í­õ0§\'\0ºjOÌºw;x;\â‰\â{ğµ{¤&\ä\"XcÙ´‚\î:§\ïÁA¿\ïTj»\ïõ\Ô7“\êC,­XrŸù¡#²®\ÑÉ¾)¦î£±smš­‚\Ö	µŠ$C†¥ŸOªt+€\é‡\Ó‚gQ“¨\èb^®…Ÿ6\îp‡ş\n\Z\Í\å:„=CA \ÓFÁV¡g~RmZg\Õ\ÍZx^\Û\Õ©Éµ;:—-Bo=\ØYI²,Ø¾5\í\Ø›ù˜ùğ¯Ï‰.\í¿µ\Ù\Íc‚yhCŸ\è¬s™k\Ól­(\èWÄ¸Ud“\àd(q§§ \ël !H‚vÕ½QÈó\æM€\r‡¸$NUVM\èU\æ:köø§_üV\à‚?~ü8ûij~%¸\Òaw\éÒ¥\î\æÍ›\İİ»w»{÷\îÍŸ„0=®]»\Öõ…v~\×u}\ènÜ¸1¿›6[-!\\ø\Åë£££ù]\×õoQ³_\Ô\ŞRBX“_¿~u×¯_ÿó\ë\Ç\Ç\Ç\İ\ãÇg×»Bşƒ“\Ö\ä\åË—Š\0_gw­@\ŞBXş+<\nÁşş~÷\åË—\î\êÕ«ó\'»C\nAk\Âÿ²»ô\ÇÁJ¾\Z„°&³?\îj€¼„òFBH!!ô¤„RB)!„‚B\nA!… „Ğ“BBH!!¤„zRB)!„‚BO\nA!… „BB\èI!á¯§\ëşÉ¬¢¨\í$\0\0\0\0IEND®B`‚','2024-11-13 20:58:48');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paz_y_salvo`
--

LOCK TABLES `paz_y_salvo` WRITE;
/*!40000 ALTER TABLE `paz_y_salvo` DISABLE KEYS */;
INSERT INTO `paz_y_salvo` VALUES (1,1,'en_proceso','2024-11-13 19:11:40','2024-11-13 19:16:41'),(2,2,'en_proceso','2024-11-13 20:02:19','2024-11-13 20:03:03');
/*!40000 ALTER TABLE `paz_y_salvo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'sebastian amaya','sebascata2005@gmail.com','$2y$10$i3BJIH60ciWhtsPYztcOJevVwtIABThljg2u.94ZCUnZq/aV8IbzW','2024-11-13 20:01:08');
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

-- Dump completed on 2024-11-13 16:31:42
