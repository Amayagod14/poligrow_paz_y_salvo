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
INSERT INTO `firmas` VALUES (1,1,'ALMACEN-IDEMA','sebastian amaya',NULL,_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0�`؈\0\0\0sRGB\0�\�\�\0\0\0gAMA\0\0���a\0\0\0	pHYs\0\0\�\0\0\�\�o�d\0\0\n(IDATx^\�;nK�\�w<�`��x$�v`��7`\�9l\0�!F\" 	H\�`�K�\�o4?�\�\�\���\���t�\�\�SU\�U=����.��W�\��3���BBH!!��zRB)!��BO\nA!� ��BB\�I!!��RB=)!��B\nA�\'�`	~��\�=x�r\�ʼ%l|rxx\�=}�t\�\�!�\�^�~\�ݾ}�{�\�]���\�yk\�&/^���\�ٳg\���\�\�av�|��yV�9������ɝ;w��\���\�ǩ�\�͛\�����g�a;|��m\��>�)ʷnݚ}�5\�7�ʧO�N�Mŕ\��\�\�s\r\�y�\�\�L\�ׯ_O�<y2\��\�ի�\�p\�<������/\����\�\�y���,\�\�1���?\�4/߿�]��p��\�\�c��Ά\�\�H<O\�Ue�\09>>�\�\�\�OPQ\�&J_�\��։�.\n؜�\0|\�\"�\r\�-�\�L����( H���Zp\�8�\"��1\�Q\��:\�}M�m��e��?}uA.\�)��m\���Ho\nL�(%$7m���\�4|��\�e�>\�I��\�=_��\n�\�P\�.���\�\Z�/Ā�ɛQ8[&U��\�$ 	�n\0��h\��w�n\�B\�Лk�_S�7\rO�]����\�W�(\\�b75&UT�u*�*@!E��[�\�C���dDN{mE����ʳ=k\�\�\�Ĝ\�f_\�\�*\�BG\�k�\��Շ�y\�TaƆ�\�<\�L�\�\��\�:T�SE_�`|.[+�.��\��\�\�	ɜ*�C\"�\�Wz\�G?�^� ��2��U\�-��\�z�Q�\�6-�`�Z|\�>����?{$�`\�&0�SC�=f��fR��\ZC\�L�\�i��?0�(p�\�8���0o\�t\��q9\�y\nN�|\�c�\�E��K\�\�l�\��Pai%(�}\�\�\�[\�B?�Y�e/�	s�G/b�\�6��:�=U$�t��^�Oq\�*��\Z[e��:���Xi�i\�\\�`R�\0\'a��\�ŠC\\�d!�a�G?\�i\�!*>�AV\�\�=\�V�\r9P�y\� *6\�+�K� =��\���0N�m%\�P_������*�5�����6\�^�\�W{8��\��Yk\�5�5�\Z}\�ع6Ť\n���[�B�x�k\�F\�S�p<c�T\�G�|,⧟Y\�\�p�{\�\n��sշ\�_��dt|���~�}\��\�IYu�L	$[�����e�\�2VX�֦6%tk�췵�\�$%E\�\��x�\Z^\\(\�jǦb\�\\�d�����1����3ub:8�\�\nh\�5\"\��\�\�i�\�\�μ^�\�\�51���\�\�\�\�C{=鹧]c��\�z؋��\0=k\�&���u\"\\#\�\�3%\��]\�\�\�~��Zhgo\ZC=c\Z��z�1JJp�3֩�\�\�Suj\�c\�\�4�/-�X2�OX**�Q\��ʝ\�I��\�F9ܱ~\Z��~��y\������\'�\�\�Ц\��4�g^�{�=Hz\�\�3�E\�=�}\��o��yk^\�	=�U>�/\�[\�%�Ӧ�j\�O�\�B�\�\��\Z=�<&?\�εiv���O��\r\��Ơ\�OM8w �b?-\�A\�\�^ @[Of�i�d>::�\�ګ.��`j��uO^ji\�Q�\�<��1�J��}.\Z\�oXu/ ;�N�b֎Nm\�e>iW?ǋ\�PbJ�#\"�jL\�+=�1�̅Nd�\�l!\0w��*]�\�\Z#gy��\\�%\�G��\�\��\0��@\�P���$@Y�\�\�as?@`�K�jr�n/\"\n<>��q\�\�c�׫����\�3&\��@>\r�j}n\�\�==�@\�\�\�ڏyX�\�SRi_�_�\�\\\�A�W\�\�����Ўhn:D\�}�|�\�\\�Q�\�[/l�ͺ\�OCy0`@�Aծ�G7!�s��P\�!�\�D	:\�C_w(\�\�5\�:\�NƲN=C���2}�G/h}�քN��f\�y��\��\�3\�n헾̫{�},��h\�\�����\�#\�\��ihN;��6��0ҫ\�R���O}`\�\��؆}\�G\�c\�#��!�\�:�������W��\�k\�Zu./�b��@	E���&�\�@Ƹ��\�!-\�\\^�]\�G�\�d@_}\�:�C��SWq]�W�E�\���즹}�t�����ş/�>�gl˦Ch�s��ns%;(9\�\rZ�&\��\�)\�z���0�\�3|����\����΃��V��j�V\�\"\�O��\�\��,�S`�	\�\��5m\�G�|\��0�\'\0�jO̺w;x;\�\�{�{�&\�\"Xcٴ�\�:�\��A�\�Tj�\��\�7�\�C,�Xr���#��\�ɾ)�sm���\�	���$C���O�t+�\�\��gQ���\�b^���6\�p��\n\Z\�\�:�=CA�\�F�V��g~RmZg\�\�Zx^\�\��ɵ;:�-Bo=\�YI�,ؾ5\�\������ω.\�\�\�c�yhC�\�s��k\�l�(\�WĸUd�\�d(q�� \�l�!H�vսQȐ�\�M�\r��$NUVM\�U\�:k���_�V\��?~�8�ij~%�\�aw\�ҥ\�\�͛\�ݻw�{�\�͟�0=�]�\���v~\�u}\�nܸ1��6[-!\\�\�룣��]\��oQ�_\�\�RBX�_�~uׯ_��\�\�\�\�\�\�Ǐg׻B���\�\�\�˗�\0_gw�@\�BX�+<\n���~�\�˗\�\�ի�\'�C\nAk\�����\��J�\Z��&�?\�j����FBH!!���RB)!���B\nA!� �ГBBH!!��zRB)!��BO\nA!� ��BB\�I!ᯧ\��ɬ���\�$\0\0\0\0IEND�B`�','2024-11-13 19:16:41'),(4,1,'SGSST','sebastian amaya',NULL,_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0�`؈\0\0\0sRGB\0�\�\�\0\0\0gAMA\0\0���a\0\0\0	pHYs\0\0\�\0\0\�\�o�d\0\0\n(IDATx^\�;nK�\�w<�`��x$�v`��7`\�9l\0�!F\" 	H\�`�K�\�o4?�\�\�\���\���t�\�\�SU\�U=����.��W�\��3���BBH!!��zRB)!��BO\nA!� ��BB\�I!!��RB=)!��B\nA�\'�`	~��\�=x�r\�ʼ%l|rxx\�=}�t\�\�!�\�^�~\�ݾ}�{�\�]���\�yk\�&/^���\�ٳg\���\�\�av�|��yV�9������ɝ;w��\���\�ǩ�\�͛\�����g�a;|��m\��>�)ʷnݚ}�5\�7�ʧO�N�Mŕ\��\�\�s\r\�y�\�\�L\�ׯ_O�<y2\��\�ի�\�p\�<������/\����\�\�y���,\�\�1���?\�4/߿�]��p��\�\�c��Ά\�\�H<O\�Ue�\09>>�\�\�\�OPQ\�&J_�\��։�.\n؜�\0|\�\"�\r\�-�\�L����( H���Zp\�8�\"��1\�Q\��:\�}M�m��e��?}uA.\�)��m\���Ho\nL�(%$7m���\�4|��\�e�>\�I��\�=_��\n�\�P\�.���\�\Z�/Ā�ɛQ8[&U��\�$ 	�n\0��h\��w�n\�B\�Лk�_S�7\rO�]����\�W�(\\�b75&UT�u*�*@!E��[�\�C���dDN{mE����ʳ=k\�\�\�Ĝ\�f_\�\�*\�BG\�k�\��Շ�y\�TaƆ�\�<\�L�\�\��\�:T�SE_�`|.[+�.��\��\�\�	ɜ*�C\"�\�Wz\�G?�^� ��2��U\�-��\�z�Q�\�6-�`�Z|\�>����?{$�`\�&0�SC�=f��fR��\ZC\�L�\�i��?0�(p�\�8���0o\�t\��q9\�y\nN�|\�c�\�E��K\�\�l�\��Pai%(�}\�\�\�[\�B?�Y�e/�	s�G/b�\�6��:�=U$�t��^�Oq\�*��\Z[e��:���Xi�i\�\\�`R�\0\'a��\�ŠC\\�d!�a�G?\�i\�!*>�AV\�\�=\�V�\r9P�y\� *6\�+�K� =��\���0N�m%\�P_������*�5�����6\�^�\�W{8��\��Yk\�5�5�\Z}\�ع6Ť\n���[�B�x�k\�F\�S�p<c�T\�G�|,⧟Y\�\�p�{\�\n��sշ\�_��dt|���~�}\��\�IYu�L	$[�����e�\�2VX�֦6%tk�췵�\�$%E\�\��x�\Z^\\(\�jǦb\�\\�d�����1����3ub:8�\�\nh\�5\"\��\�\�i�\�\�μ^�\�\�51���\�\�\�\�C{=鹧]c��\�z؋��\0=k\�&���u\"\\#\�\�3%\��]\�\�\�~��Zhgo\ZC=c\Z��z�1JJp�3֩�\�\�Suj\�c\�\�4�/-�X2�OX**�Q\��ʝ\�I��\�F9ܱ~\Z��~��y\������\'�\�\�Ц\��4�g^�{�=Hz\�\�3�E\�=�}\��o��yk^\�	=�U>�/\�[\�%�Ӧ�j\�O�\�B�\�\��\Z=�<&?\�εiv���O��\r\��Ơ\�OM8w �b?-\�A\�\�^ @[Of�i�d>::�\�ګ.��`j��uO^ji\�Q�\�<��1�J��}.\Z\�oXu/ ;�N�b֎Nm\�e>iW?ǋ\�PbJ�#\"�jL\�+=�1�̅Nd�\�l!\0w��*]�\�\Z#gy��\\�%\�G��\�\��\0��@\�P���$@Y�\�\�as?@`�K�jr�n/\"\n<>��q\�\�c�׫����\�3&\��@>\r�j}n\�\�==�@\�\�\�ڏyX�\�SRi_�_�\�\\\�A�W\�\�����Ўhn:D\�}�|�\�\\�Q�\�[/l�ͺ\�OCy0`@�Aծ�G7!�s��P\�!�\�D	:\�C_w(\�\�5\�:\�NƲN=C���2}�G/h}�քN��f\�y��\��\�3\�n헾̫{�},��h\�\�����\�#\�\��ihN;��6��0ҫ\�R���O}`\�\��؆}\�G\�c\�#��!�\�:�������W��\�k\�Zu./�b��@	E���&�\�@Ƹ��\�!-\�\\^�]\�G�\�d@_}\�:�C��SWq]�W�E�\���즹}�t�����ş/�>�gl˦Ch�s��ns%;(9\�\rZ�&\��\�)\�z���0�\�3|����\����΃��V��j�V\�\"\�O��\�\��,�S`�	\�\��5m\�G�|\��0�\'\0�jO̺w;x;\�\�{�{�&\�\"Xcٴ�\�:�\��A�\�Tj�\��\�7�\�C,�Xr���#��\�ɾ)�sm���\�	���$C���O�t+�\�\��gQ���\�b^���6\�p��\n\Z\�\�:�=CA�\�F�V��g~RmZg\�\�Zx^\�\��ɵ;:�-Bo=\�YI�,ؾ5\�\������ω.\�\�\�c�yhC�\�s��k\�l�(\�WĸUd�\�d(q�� \�l�!H�vսQȐ�\�M�\r��$NUVM\�U\�:k���_�V\��?~�8�ij~%�\�aw\�ҥ\�\�͛\�ݻw�{�\�͟�0=�]�\���v~\�u}\�nܸ1��6[-!\\�\�룣��]\��oQ�_\�\�RBX�_�~uׯ_��\�\�\�\�\�\�Ǐg׻B���\�\�\�˗�\0_gw�@\�BX�+<\n���~�\�˗\�\�ի�\'�C\nAk\�����\��J�\Z��&�?\�j����FBH!!���RB)!���B\nA!� �ГBBH!!��zRB)!��BO\nA!� ��BB\�I!ᯧ\��ɬ���\�$\0\0\0\0IEND�B`�','2024-11-13 19:41:31'),(6,1,'SISTEMAS','sebastian amaya',NULL,_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0�`؈\0\0\0sRGB\0�\�\�\0\0\0gAMA\0\0���a\0\0\0	pHYs\0\0\�\0\0\�\�o�d\0\0\n(IDATx^\�;nK�\�w<�`��x$�v`��7`\�9l\0�!F\" 	H\�`�K�\�o4?�\�\�\���\���t�\�\�SU\�U=����.��W�\��3���BBH!!��zRB)!��BO\nA!� ��BB\�I!!��RB=)!��B\nA�\'�`	~��\�=x�r\�ʼ%l|rxx\�=}�t\�\�!�\�^�~\�ݾ}�{�\�]���\�yk\�&/^���\�ٳg\���\�\�av�|��yV�9������ɝ;w��\���\�ǩ�\�͛\�����g�a;|��m\��>�)ʷnݚ}�5\�7�ʧO�N�Mŕ\��\�\�s\r\�y�\�\�L\�ׯ_O�<y2\��\�ի�\�p\�<������/\����\�\�y���,\�\�1���?\�4/߿�]��p��\�\�c��Ά\�\�H<O\�Ue�\09>>�\�\�\�OPQ\�&J_�\��։�.\n؜�\0|\�\"�\r\�-�\�L����( H���Zp\�8�\"��1\�Q\��:\�}M�m��e��?}uA.\�)��m\���Ho\nL�(%$7m���\�4|��\�e�>\�I��\�=_��\n�\�P\�.���\�\Z�/Ā�ɛQ8[&U��\�$ 	�n\0��h\��w�n\�B\�Лk�_S�7\rO�]����\�W�(\\�b75&UT�u*�*@!E��[�\�C���dDN{mE����ʳ=k\�\�\�Ĝ\�f_\�\�*\�BG\�k�\��Շ�y\�TaƆ�\�<\�L�\�\��\�:T�SE_�`|.[+�.��\��\�\�	ɜ*�C\"�\�Wz\�G?�^� ��2��U\�-��\�z�Q�\�6-�`�Z|\�>����?{$�`\�&0�SC�=f��fR��\ZC\�L�\�i��?0�(p�\�8���0o\�t\��q9\�y\nN�|\�c�\�E��K\�\�l�\��Pai%(�}\�\�\�[\�B?�Y�e/�	s�G/b�\�6��:�=U$�t��^�Oq\�*��\Z[e��:���Xi�i\�\\�`R�\0\'a��\�ŠC\\�d!�a�G?\�i\�!*>�AV\�\�=\�V�\r9P�y\� *6\�+�K� =��\���0N�m%\�P_������*�5�����6\�^�\�W{8��\��Yk\�5�5�\Z}\�ع6Ť\n���[�B�x�k\�F\�S�p<c�T\�G�|,⧟Y\�\�p�{\�\n��sշ\�_��dt|���~�}\��\�IYu�L	$[�����e�\�2VX�֦6%tk�췵�\�$%E\�\��x�\Z^\\(\�jǦb\�\\�d�����1����3ub:8�\�\nh\�5\"\��\�\�i�\�\�μ^�\�\�51���\�\�\�\�C{=鹧]c��\�z؋��\0=k\�&���u\"\\#\�\�3%\��]\�\�\�~��Zhgo\ZC=c\Z��z�1JJp�3֩�\�\�Suj\�c\�\�4�/-�X2�OX**�Q\��ʝ\�I��\�F9ܱ~\Z��~��y\������\'�\�\�Ц\��4�g^�{�=Hz\�\�3�E\�=�}\��o��yk^\�	=�U>�/\�[\�%�Ӧ�j\�O�\�B�\�\��\Z=�<&?\�εiv���O��\r\��Ơ\�OM8w �b?-\�A\�\�^ @[Of�i�d>::�\�ګ.��`j��uO^ji\�Q�\�<��1�J��}.\Z\�oXu/ ;�N�b֎Nm\�e>iW?ǋ\�PbJ�#\"�jL\�+=�1�̅Nd�\�l!\0w��*]�\�\Z#gy��\\�%\�G��\�\��\0��@\�P���$@Y�\�\�as?@`�K�jr�n/\"\n<>��q\�\�c�׫����\�3&\��@>\r�j}n\�\�==�@\�\�\�ڏyX�\�SRi_�_�\�\\\�A�W\�\�����Ўhn:D\�}�|�\�\\�Q�\�[/l�ͺ\�OCy0`@�Aծ�G7!�s��P\�!�\�D	:\�C_w(\�\�5\�:\�NƲN=C���2}�G/h}�քN��f\�y��\��\�3\�n헾̫{�},��h\�\�����\�#\�\��ihN;��6��0ҫ\�R���O}`\�\��؆}\�G\�c\�#��!�\�:�������W��\�k\�Zu./�b��@	E���&�\�@Ƹ��\�!-\�\\^�]\�G�\�d@_}\�:�C��SWq]�W�E�\���즹}�t�����ş/�>�gl˦Ch�s��ns%;(9\�\rZ�&\��\�)\�z���0�\�3|����\����΃��V��j�V\�\"\�O��\�\��,�S`�	\�\��5m\�G�|\��0�\'\0�jO̺w;x;\�\�{�{�&\�\"Xcٴ�\�:�\��A�\�Tj�\��\�7�\�C,�Xr���#��\�ɾ)�sm���\�	���$C���O�t+�\�\��gQ���\�b^���6\�p��\n\Z\�\�:�=CA�\�F�V��g~RmZg\�\�Zx^\�\��ɵ;:�-Bo=\�YI�,ؾ5\�\������ω.\�\�\�c�yhC�\�s��k\�l�(\�WĸUd�\�d(q�� \�l�!H�vսQȐ�\�M�\r��$NUVM\�U\�:k���_�V\��?~�8�ij~%�\�aw\�ҥ\�\�͛\�ݻw�{�\�͟�0=�]�\���v~\�u}\�nܸ1��6[-!\\�\�룣��]\��oQ�_\�\�RBX�_�~uׯ_��\�\�\�\�\�\�Ǐg׻B���\�\�\�˗�\0_gw�@\�BX�+<\n���~�\�˗\�\�ի�\'�C\nAk\�����\��J�\Z��&�?\�j����FBH!!���RB)!���B\nA!� �ГBBH!!��zRB)!��BO\nA!� ��BB\�I!ᯧ\��ɬ���\�$\0\0\0\0IEND�B`�','2024-11-13 19:59:56'),(7,2,'ALMACEN-IDEMA','sebastian amaya',NULL,_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0�`؈\0\0\0sRGB\0�\�\�\0\0\0gAMA\0\0���a\0\0\0	pHYs\0\0\�\0\0\�\�o�d\0\0\n(IDATx^\�;nK�\�w<�`��x$�v`��7`\�9l\0�!F\" 	H\�`�K�\�o4?�\�\�\���\���t�\�\�SU\�U=����.��W�\��3���BBH!!��zRB)!��BO\nA!� ��BB\�I!!��RB=)!��B\nA�\'�`	~��\�=x�r\�ʼ%l|rxx\�=}�t\�\�!�\�^�~\�ݾ}�{�\�]���\�yk\�&/^���\�ٳg\���\�\�av�|��yV�9������ɝ;w��\���\�ǩ�\�͛\�����g�a;|��m\��>�)ʷnݚ}�5\�7�ʧO�N�Mŕ\��\�\�s\r\�y�\�\�L\�ׯ_O�<y2\��\�ի�\�p\�<������/\����\�\�y���,\�\�1���?\�4/߿�]��p��\�\�c��Ά\�\�H<O\�Ue�\09>>�\�\�\�OPQ\�&J_�\��։�.\n؜�\0|\�\"�\r\�-�\�L����( H���Zp\�8�\"��1\�Q\��:\�}M�m��e��?}uA.\�)��m\���Ho\nL�(%$7m���\�4|��\�e�>\�I��\�=_��\n�\�P\�.���\�\Z�/Ā�ɛQ8[&U��\�$ 	�n\0��h\��w�n\�B\�Лk�_S�7\rO�]����\�W�(\\�b75&UT�u*�*@!E��[�\�C���dDN{mE����ʳ=k\�\�\�Ĝ\�f_\�\�*\�BG\�k�\��Շ�y\�TaƆ�\�<\�L�\�\��\�:T�SE_�`|.[+�.��\��\�\�	ɜ*�C\"�\�Wz\�G?�^� ��2��U\�-��\�z�Q�\�6-�`�Z|\�>����?{$�`\�&0�SC�=f��fR��\ZC\�L�\�i��?0�(p�\�8���0o\�t\��q9\�y\nN�|\�c�\�E��K\�\�l�\��Pai%(�}\�\�\�[\�B?�Y�e/�	s�G/b�\�6��:�=U$�t��^�Oq\�*��\Z[e��:���Xi�i\�\\�`R�\0\'a��\�ŠC\\�d!�a�G?\�i\�!*>�AV\�\�=\�V�\r9P�y\� *6\�+�K� =��\���0N�m%\�P_������*�5�����6\�^�\�W{8��\��Yk\�5�5�\Z}\�ع6Ť\n���[�B�x�k\�F\�S�p<c�T\�G�|,⧟Y\�\�p�{\�\n��sշ\�_��dt|���~�}\��\�IYu�L	$[�����e�\�2VX�֦6%tk�췵�\�$%E\�\��x�\Z^\\(\�jǦb\�\\�d�����1����3ub:8�\�\nh\�5\"\��\�\�i�\�\�μ^�\�\�51���\�\�\�\�C{=鹧]c��\�z؋��\0=k\�&���u\"\\#\�\�3%\��]\�\�\�~��Zhgo\ZC=c\Z��z�1JJp�3֩�\�\�Suj\�c\�\�4�/-�X2�OX**�Q\��ʝ\�I��\�F9ܱ~\Z��~��y\������\'�\�\�Ц\��4�g^�{�=Hz\�\�3�E\�=�}\��o��yk^\�	=�U>�/\�[\�%�Ӧ�j\�O�\�B�\�\��\Z=�<&?\�εiv���O��\r\��Ơ\�OM8w �b?-\�A\�\�^ @[Of�i�d>::�\�ګ.��`j��uO^ji\�Q�\�<��1�J��}.\Z\�oXu/ ;�N�b֎Nm\�e>iW?ǋ\�PbJ�#\"�jL\�+=�1�̅Nd�\�l!\0w��*]�\�\Z#gy��\\�%\�G��\�\��\0��@\�P���$@Y�\�\�as?@`�K�jr�n/\"\n<>��q\�\�c�׫����\�3&\��@>\r�j}n\�\�==�@\�\�\�ڏyX�\�SRi_�_�\�\\\�A�W\�\�����Ўhn:D\�}�|�\�\\�Q�\�[/l�ͺ\�OCy0`@�Aծ�G7!�s��P\�!�\�D	:\�C_w(\�\�5\�:\�NƲN=C���2}�G/h}�քN��f\�y��\��\�3\�n헾̫{�},��h\�\�����\�#\�\��ihN;��6��0ҫ\�R���O}`\�\��؆}\�G\�c\�#��!�\�:�������W��\�k\�Zu./�b��@	E���&�\�@Ƹ��\�!-\�\\^�]\�G�\�d@_}\�:�C��SWq]�W�E�\���즹}�t�����ş/�>�gl˦Ch�s��ns%;(9\�\rZ�&\��\�)\�z���0�\�3|����\����΃��V��j�V\�\"\�O��\�\��,�S`�	\�\��5m\�G�|\��0�\'\0�jO̺w;x;\�\�{�{�&\�\"Xcٴ�\�:�\��A�\�Tj�\��\�7�\�C,�Xr���#��\�ɾ)�sm���\�	���$C���O�t+�\�\��gQ���\�b^���6\�p��\n\Z\�\�:�=CA�\�F�V��g~RmZg\�\�Zx^\�\��ɵ;:�-Bo=\�YI�,ؾ5\�\������ω.\�\�\�c�yhC�\�s��k\�l�(\�WĸUd�\�d(q�� \�l�!H�vսQȐ�\�M�\r��$NUVM\�U\�:k���_�V\��?~�8�ij~%�\�aw\�ҥ\�\�͛\�ݻw�{�\�͟�0=�]�\���v~\�u}\�nܸ1��6[-!\\�\�룣��]\��oQ�_\�\�RBX�_�~uׯ_��\�\�\�\�\�\�Ǐg׻B���\�\�\�˗�\0_gw�@\�BX�+<\n���~�\�˗\�\�ի�\'�C\nAk\�����\��J�\Z��&�?\�j����FBH!!���RB)!���B\nA!� �ГBBH!!��zRB)!��BO\nA!� ��BB\�I!ᯧ\��ɬ���\�$\0\0\0\0IEND�B`�','2024-11-13 20:03:03'),(8,2,'FUNDACION','sebastian amaya',NULL,_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0\0\0\0B\0\0\0�`؈\0\0\0sRGB\0�\�\�\0\0\0gAMA\0\0���a\0\0\0	pHYs\0\0\�\0\0\�\�o�d\0\0\n(IDATx^\�;nK�\�w<�`��x$�v`��7`\�9l\0�!F\" 	H\�`�K�\�o4?�\�\�\���\���t�\�\�SU\�U=����.��W�\��3���BBH!!��zRB)!��BO\nA!� ��BB\�I!!��RB=)!��B\nA�\'�`	~��\�=x�r\�ʼ%l|rxx\�=}�t\�\�!�\�^�~\�ݾ}�{�\�]���\�yk\�&/^���\�ٳg\���\�\�av�|��yV�9������ɝ;w��\���\�ǩ�\�͛\�����g�a;|��m\��>�)ʷnݚ}�5\�7�ʧO�N�Mŕ\��\�\�s\r\�y�\�\�L\�ׯ_O�<y2\��\�ի�\�p\�<������/\����\�\�y���,\�\�1���?\�4/߿�]��p��\�\�c��Ά\�\�H<O\�Ue�\09>>�\�\�\�OPQ\�&J_�\��։�.\n؜�\0|\�\"�\r\�-�\�L����( H���Zp\�8�\"��1\�Q\��:\�}M�m��e��?}uA.\�)��m\���Ho\nL�(%$7m���\�4|��\�e�>\�I��\�=_��\n�\�P\�.���\�\Z�/Ā�ɛQ8[&U��\�$ 	�n\0��h\��w�n\�B\�Лk�_S�7\rO�]����\�W�(\\�b75&UT�u*�*@!E��[�\�C���dDN{mE����ʳ=k\�\�\�Ĝ\�f_\�\�*\�BG\�k�\��Շ�y\�TaƆ�\�<\�L�\�\��\�:T�SE_�`|.[+�.��\��\�\�	ɜ*�C\"�\�Wz\�G?�^� ��2��U\�-��\�z�Q�\�6-�`�Z|\�>����?{$�`\�&0�SC�=f��fR��\ZC\�L�\�i��?0�(p�\�8���0o\�t\��q9\�y\nN�|\�c�\�E��K\�\�l�\��Pai%(�}\�\�\�[\�B?�Y�e/�	s�G/b�\�6��:�=U$�t��^�Oq\�*��\Z[e��:���Xi�i\�\\�`R�\0\'a��\�ŠC\\�d!�a�G?\�i\�!*>�AV\�\�=\�V�\r9P�y\� *6\�+�K� =��\���0N�m%\�P_������*�5�����6\�^�\�W{8��\��Yk\�5�5�\Z}\�ع6Ť\n���[�B�x�k\�F\�S�p<c�T\�G�|,⧟Y\�\�p�{\�\n��sշ\�_��dt|���~�}\��\�IYu�L	$[�����e�\�2VX�֦6%tk�췵�\�$%E\�\��x�\Z^\\(\�jǦb\�\\�d�����1����3ub:8�\�\nh\�5\"\��\�\�i�\�\�μ^�\�\�51���\�\�\�\�C{=鹧]c��\�z؋��\0=k\�&���u\"\\#\�\�3%\��]\�\�\�~��Zhgo\ZC=c\Z��z�1JJp�3֩�\�\�Suj\�c\�\�4�/-�X2�OX**�Q\��ʝ\�I��\�F9ܱ~\Z��~��y\������\'�\�\�Ц\��4�g^�{�=Hz\�\�3�E\�=�}\��o��yk^\�	=�U>�/\�[\�%�Ӧ�j\�O�\�B�\�\��\Z=�<&?\�εiv���O��\r\��Ơ\�OM8w �b?-\�A\�\�^ @[Of�i�d>::�\�ګ.��`j��uO^ji\�Q�\�<��1�J��}.\Z\�oXu/ ;�N�b֎Nm\�e>iW?ǋ\�PbJ�#\"�jL\�+=�1�̅Nd�\�l!\0w��*]�\�\Z#gy��\\�%\�G��\�\��\0��@\�P���$@Y�\�\�as?@`�K�jr�n/\"\n<>��q\�\�c�׫����\�3&\��@>\r�j}n\�\�==�@\�\�\�ڏyX�\�SRi_�_�\�\\\�A�W\�\�����Ўhn:D\�}�|�\�\\�Q�\�[/l�ͺ\�OCy0`@�Aծ�G7!�s��P\�!�\�D	:\�C_w(\�\�5\�:\�NƲN=C���2}�G/h}�քN��f\�y��\��\�3\�n헾̫{�},��h\�\�����\�#\�\��ihN;��6��0ҫ\�R���O}`\�\��؆}\�G\�c\�#��!�\�:�������W��\�k\�Zu./�b��@	E���&�\�@Ƹ��\�!-\�\\^�]\�G�\�d@_}\�:�C��SWq]�W�E�\���즹}�t�����ş/�>�gl˦Ch�s��ns%;(9\�\rZ�&\��\�)\�z���0�\�3|����\����΃��V��j�V\�\"\�O��\�\��,�S`�	\�\��5m\�G�|\��0�\'\0�jO̺w;x;\�\�{�{�&\�\"Xcٴ�\�:�\��A�\�Tj�\��\�7�\�C,�Xr���#��\�ɾ)�sm���\�	���$C���O�t+�\�\��gQ���\�b^���6\�p��\n\Z\�\�:�=CA�\�F�V��g~RmZg\�\�Zx^\�\��ɵ;:�-Bo=\�YI�,ؾ5\�\������ω.\�\�\�c�yhC�\�s��k\�l�(\�WĸUd�\�d(q�� \�l�!H�vսQȐ�\�M�\r��$NUVM\�U\�:k���_�V\��?~�8�ij~%�\�aw\�ҥ\�\�͛\�ݻw�{�\�͟�0=�]�\���v~\�u}\�nܸ1��6[-!\\�\�룣��]\��oQ�_\�\�RBX�_�~uׯ_��\�\�\�\�\�\�Ǐg׻B���\�\�\�˗�\0_gw�@\�BX�+<\n���~�\�˗\�\�ի�\'�C\nAk\�����\��J�\Z��&�?\�j����FBH!!���RB)!���B\nA!� �ГBBH!!��zRB)!��BO\nA!� ��BB\�I!ᯧ\��ɬ���\�$\0\0\0\0IEND�B`�','2024-11-13 20:58:48');
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
