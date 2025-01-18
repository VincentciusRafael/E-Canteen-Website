-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: db_e-canteen
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `tanggal_bergabung` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Ribo','qw@gmail.com','1234','2025-01-01 17:00:00'),(7,'Okan','okan1234@gmail.com','7890','2025-01-08 13:34:45'),(8,'Vinz','vincen@gmail.com','0000','2025-01-08 13:38:30'),(13,'Asep','asep1234@gmail.com','2222','2025-01-10 16:13:26'),(15,'Admin','admin@gmail.com','1234','2025-01-10 16:26:42');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_transaksi`
--

DROP TABLE IF EXISTS `detail_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL,
  `id_topup` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `detail_transaksi_top_up_FK` (`id_topup`),
  KEY `detail_transaksi_produk_FK` (`id_produk`),
  CONSTRAINT `detail_transaksi_produk_FK` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_transaksi_top_up_FK` FOREIGN KEY (`id_topup`) REFERENCES `top_up` (`id_topup`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_transaksi`
--

LOCK TABLES `detail_transaksi` WRITE;
/*!40000 ALTER TABLE `detail_transaksi` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penjual`
--

DROP TABLE IF EXISTS `penjual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penjual` (
  `id_penjual` int NOT NULL AUTO_INCREMENT,
  `nama_penjual` varchar(100) NOT NULL,
  `kontak` varchar(100) NOT NULL,
  `status` enum('aktif','non-aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_bergabung` timestamp NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `withdrawable_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_penjual`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penjual`
--

LOCK TABLES `penjual` WRITE;
/*!40000 ALTER TABLE `penjual` DISABLE KEYS */;
INSERT INTO `penjual` VALUES (1,'Pak Kepo','083663654455','non-aktif','2025-01-04 17:00:00',0.00,0.00),(7,'Pak Lakno','083663654455','aktif','2025-01-09 23:45:45',0.00,0.00),(8,'Pak Doni','08366365445','aktif','2025-01-09 23:46:16',0.00,0.00),(10,'Pak Okan','0836566423728','aktif','2025-01-10 00:28:31',0.00,0.00),(11,'Bu Ada ','0836566423728','non-aktif','2025-01-10 14:30:33',0.00,0.00),(12,'Bu Ming Jung','087374683234 ','aktif','2025-01-10 14:31:02',0.00,0.00),(13,'Bu Mal Sook','083762736874','aktif','2025-01-10 14:31:16',0.00,0.00),(14,'Bu Erine','0837642764378436','aktif','2025-01-10 14:31:33',0.00,0.00),(15,'Bu Marsha','0874366376845323','aktif','2025-01-10 14:31:47',0.00,0.00),(21,'Pak Cepak Cepak Jeder','0836566423728','non-aktif','2025-01-10 16:18:14',0.00,0.00);
/*!40000 ALTER TABLE `penjual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk` (
  `id_produk` int NOT NULL AUTO_INCREMENT,
  `id_penjual` int NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi_produk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int NOT NULL,
  `tanggal_dimasukan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  KEY `produk_penjual_FK` (`id_penjual`),
  CONSTRAINT `produk_penjual_FK` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (2,7,'Nasi Goreng','Nasgor',10000.00,1000000,'2025-01-08 02:42:25'),(3,1,'Pangsit Mie','Mie Digodok',10000.00,1000000,'2025-01-10 01:45:17'),(4,8,'Ayam Kentucky','Ayam DiKentu',12000.00,10000000,'2025-01-10 04:24:15'),(5,1,'Chilie Oil','Mie DIkasih Oil',10000.00,1000000,'2025-01-10 08:44:18'),(6,15,'Es Teh','Teh di kasih es',4000.00,10000000,'2025-01-10 08:44:57'),(7,12,'Es Cappuccino ','Cappuccino Dikasih Es',4000.00,1000000,'2025-01-10 08:45:45'),(8,13,'Tahu Goreng','Tahu Digoreng',5000.00,1000000,'2025-01-10 08:46:22'),(9,7,'Sempol','Sempongen',1000.00,1000000,'2025-01-10 11:10:46'),(10,11,'Jus Buah','Buah diblender',5000.00,100000,'2025-01-10 14:33:13'),(12,15,'Batagor','Batako Digoreng',5000.00,1000000,'2025-01-11 03:12:49');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `top_up`
--

DROP TABLE IF EXISTS `top_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `top_up` (
  `id_topup` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  `tanggal_transaksi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_harga` int NOT NULL,
  PRIMARY KEY (`id_topup`),
  KEY `transaksi_user_FK` (`id_user`),
  CONSTRAINT `transaksi_user_FK` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `top_up`
--

LOCK TABLES `top_up` WRITE;
/*!40000 ALTER TABLE `top_up` DISABLE KEYS */;
INSERT INTO `top_up` VALUES (1,1,10000.00,'completed','2025-01-10 07:56:46',0),(2,1,10000.00,'completed','2025-01-10 08:05:30',0),(3,1,10000.00,'completed','2025-01-10 08:19:11',0),(4,1,20000.00,'completed','2025-01-10 08:23:10',0),(5,1,10000.00,'completed','2025-01-10 13:18:06',0),(6,1,10000.00,'completed','2025-01-10 13:27:29',0),(7,7,10000.00,'completed','2025-01-10 19:25:27',10000),(8,8,100000.00,'completed','2025-01-10 21:34:42',100000);
/*!40000 ALTER TABLE `top_up` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_bergabung` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Dony Septian R. - XII RPL 1','Asep','asep1234@gmail.com','user',80000.00,'11111111','2025-01-08 12:04:46'),(3,'Okan Karnivora - XII RPL 1','OkanPecintaJanda','okan1234@gmail.com','user',100000.00,'123455678','2025-01-08 13:04:22'),(7,'Ridhollah Mustofa - XII RPL 1','Ribo','ribo1234@gmail.com','user',10000.00,'1234','2025-01-10 11:37:28'),(8,'Vincentcius Rafael - XII RPL 1','Kicentt.','vincentcius1234@gmail.com','user',100000.00,'12345678','2025-01-10 11:38:12'),(9,'Radhit Pribadi T. - XII RPL 1','Dit','radhit1234@gmail.com','user',0.00,'1234','2025-01-10 12:33:59'),(10,'Bilal Sanayu M. - XII RPL 1','Bilal San','bilal1234@gmail.com','user',0.00,'1234','2025-01-10 12:56:14'),(11,'Bagus Setya - XII RPL 1','Bagus','bagus1234@gmail.com','user',0.00,'1234','2025-01-10 13:10:35'),(12,'Dimas Rahmanda - XII RPL 1','Rahmanda','dims1234@gmail.com','user',0.00,'1234','2025-01-10 13:30:26'),(15,'Moreno Ariel Wibowo - XII RPL 1','Reno','reno1234@gmail.com','user',0.00,'$2y$10$HmVRd4nPrKLcsL561hZziemwvBF3GHIUIWFuPE6c/rbTfhibc.xZm','2025-01-10 18:27:05');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_e-canteen'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-11 10:38:56
