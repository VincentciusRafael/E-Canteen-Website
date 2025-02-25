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
-- Table structure for table `detail_pembelian`
--

DROP TABLE IF EXISTS `detail_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pembelian` (
  `id_detail_pembelian` int NOT NULL AUTO_INCREMENT,
  `id_pembelian` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail_pembelian`),
  KEY `detail_pembelian_pembelian_FK` (`id_pembelian`),
  KEY `detail_pembelian_produk_FK` (`id_produk`),
  CONSTRAINT `detail_pembelian_pembelian_FK` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_pembelian_produk_FK` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_pembelian`
--

LOCK TABLES `detail_pembelian` WRITE;
/*!40000 ALTER TABLE `detail_pembelian` DISABLE KEYS */;
INSERT INTO `detail_pembelian` VALUES (1,4,14,1,10000.00,10000.00),(2,5,14,1,10000.00,10000.00),(3,6,14,1,10000.00,10000.00),(4,7,9,10,1000.00,10000.00),(5,7,2,1,10000.00,10000.00),(6,8,5,1,10000.00,10000.00),(7,8,3,1,10000.00,10000.00),(8,9,14,1,10000.00,10000.00),(9,10,3,1,10000.00,10000.00),(10,10,5,1,10000.00,10000.00),(11,11,16,10,1000.00,10000.00),(12,11,15,10,1000.00,10000.00),(13,12,13,1,5000.00,5000.00),(14,13,10,2,5000.00,10000.00),(15,14,8,1,5000.00,5000.00),(16,15,4,1,12000.00,12000.00),(17,16,15,8,1000.00,8000.00),(18,17,7,5,4000.00,20000.00),(19,18,3,1,10000.00,10000.00),(20,18,5,1,10000.00,10000.00),(21,19,14,6,10000.00,60000.00),(22,20,15,2,1000.00,2000.00),(23,21,7,2,4000.00,8000.00),(24,22,5,1,10000.00,10000.00),(25,23,4,1,12000.00,12000.00),(26,24,6,2,4000.00,8000.00),(27,25,14,2,10000.00,20000.00),(28,26,10,2,5000.00,10000.00),(29,27,14,5,10000.00,50000.00),(30,28,14,3,10000.00,30000.00),(31,29,14,1,10000.00,10000.00),(32,30,10,6,5000.00,30000.00),(33,31,7,3,4000.00,12000.00),(34,32,5,6,10000.00,60000.00),(35,33,6,2,4000.00,8000.00),(36,34,16,1,1000.00,1000.00),(37,34,15,10,1000.00,10000.00),(38,35,10,1,5000.00,5000.00),(39,36,9,20,1000.00,20000.00),(40,37,4,1,12000.00,12000.00),(41,38,7,3,4000.00,12000.00),(42,39,14,1,10000.00,10000.00);
/*!40000 ALTER TABLE `detail_pembelian` ENABLE KEYS */;
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
-- Table structure for table `pembelian`
--

DROP TABLE IF EXISTS `pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembelian` (
  `id_pembelian` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `id_penjual` int NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `metode_pembayaran` varchar(100) NOT NULL,
  `tanggal_pembelian` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_selesai` timestamp NULL DEFAULT NULL,
  `catatan` text,
  `status_pesanan` enum('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  PRIMARY KEY (`id_pembelian`),
  KEY `pembelian_penjual_FK` (`id_penjual`),
  KEY `pembelian_user_FK` (`id_user`),
  CONSTRAINT `pembelian_penjual_FK` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pembelian_user_FK` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian`
--

LOCK TABLES `pembelian` WRITE;
/*!40000 ALTER TABLE `pembelian` DISABLE KEYS */;
INSERT INTO `pembelian` VALUES (4,1,14,10000.00,'completed','Saldo','2025-01-30 04:31:08',NULL,'','menunggu'),(5,1,14,10000.00,'completed','Saldo','2025-01-30 04:32:04',NULL,'','menunggu'),(6,1,14,10000.00,'completed','Saldo','2025-01-30 04:38:22',NULL,'ontol','menunggu'),(7,1,7,20000.00,'completed','Saldo','2025-01-30 04:38:22',NULL,'ontol','menunggu'),(8,1,1,20000.00,'completed','Saldo','2025-01-30 04:38:22',NULL,'ontol','menunggu'),(9,1,14,10000.00,'completed','Saldo','2025-01-30 04:54:31',NULL,'','menunggu'),(10,1,1,20000.00,'completed','Saldo','2025-01-30 04:54:31',NULL,'','menunggu'),(11,8,10,20000.00,'completed','Saldo','2025-01-30 06:14:01',NULL,'','menunggu'),(12,8,15,5000.00,'completed','Saldo','2025-01-30 06:14:01',NULL,'','menunggu'),(13,8,11,10000.00,'completed','Saldo','2025-01-30 06:14:01',NULL,'','menunggu'),(14,8,13,5000.00,'completed','Saldo','2025-01-30 06:14:01',NULL,'','menunggu'),(15,8,8,12000.00,'completed','Saldo','2025-01-30 06:15:55',NULL,'p','menunggu'),(16,8,10,8000.00,'completed','Saldo','2025-01-30 06:15:55',NULL,'p','menunggu'),(17,8,12,20000.00,'completed','Saldo','2025-01-30 06:23:52',NULL,'chilie oil pedes','menunggu'),(18,8,1,20000.00,'completed','Saldo','2025-01-30 06:23:52',NULL,'chilie oil pedes','menunggu'),(19,8,14,60000.00,'completed','Saldo','2025-01-30 06:39:25',NULL,'','menunggu'),(20,8,10,2000.00,'completed','Saldo','2025-01-30 06:39:25',NULL,'','menunggu'),(21,8,12,8000.00,'completed','Saldo','2025-01-30 06:39:25',NULL,'','menunggu'),(22,8,1,10000.00,'completed','Saldo','2025-01-31 06:42:13',NULL,'','menunggu'),(23,8,8,12000.00,'completed','Saldo','2025-01-31 06:42:13',NULL,'','menunggu'),(24,8,15,8000.00,'completed','Saldo','2025-01-31 06:42:13',NULL,'','menunggu'),(25,1,14,20000.00,'completed','Saldo','2025-02-19 05:58:46',NULL,'','menunggu'),(26,1,11,10000.00,'completed','Saldo','2025-02-19 05:58:46',NULL,'','menunggu'),(27,1,14,50000.00,'completed','Saldo','2025-02-21 05:22:34',NULL,'G pedes','menunggu'),(28,1,14,30000.00,'completed','Saldo','2025-02-21 07:49:33',NULL,'','menunggu'),(29,1,14,10000.00,'completed','Saldo','2025-02-21 08:38:39',NULL,'','menunggu'),(30,1,11,30000.00,'completed','Saldo','2025-02-21 08:38:39',NULL,'','menunggu'),(31,1,12,12000.00,'completed','Saldo','2025-02-21 08:38:39',NULL,'','menunggu'),(32,1,1,60000.00,'completed','Saldo','2025-02-21 08:38:39',NULL,'','menunggu'),(33,1,15,8000.00,'completed','Saldo','2025-02-21 08:38:39',NULL,'','menunggu'),(34,1,10,11000.00,'completed','Saldo','2025-02-21 08:57:27',NULL,'','menunggu'),(35,1,11,5000.00,'completed','Saldo','2025-02-21 08:57:27',NULL,'','menunggu'),(36,1,7,20000.00,'completed','Saldo','2025-02-21 08:57:27',NULL,'','menunggu'),(37,1,8,12000.00,'completed','Saldo','2025-02-21 08:57:27',NULL,'','menunggu'),(38,1,12,12000.00,'completed','Saldo','2025-02-21 08:57:27',NULL,'','menunggu'),(39,1,14,10000.00,'completed','Saldo','2025-02-24 07:13:36',NULL,'k','menunggu');
/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
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
  `total_pemasukan` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_penjual`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penjual`
--

LOCK TABLES `penjual` WRITE;
/*!40000 ALTER TABLE `penjual` DISABLE KEYS */;
INSERT INTO `penjual` VALUES (1,'Pak Kepo','083663654455','aktif','2025-01-04 17:00:00',60000.00,0.00,0.00),(7,'Pak Lakno','083663654455','aktif','2025-01-09 23:45:45',20000.00,0.00,0.00),(8,'Pak Doni','08366365445','aktif','2025-01-09 23:46:16',10000.00,0.00,0.00),(10,'Pak Okan','0836566423728','aktif','2025-01-10 00:28:31',10000.00,0.00,0.00),(11,'Bu Ada ','0836566423728','non-aktif','2025-01-10 14:30:33',35000.00,0.00,0.00),(12,'Bu Ming Jung','087374683234 ','aktif','2025-01-10 14:31:02',24000.00,0.00,0.00),(13,'Bu Mal Sook','083762736874','aktif','2025-01-10 14:31:16',0.00,0.00,0.00),(14,'Bu Erine','0837642764378436','aktif','2025-01-10 14:31:33',70000.00,0.00,0.00),(15,'Bu Marsha','0874366376845323','aktif','2025-01-10 14:31:47',8000.00,0.00,0.00),(21,'Pak Cepak Cepak Jeder','0836566423728','non-aktif','2025-01-10 16:18:14',0.00,0.00,0.00);
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
  `image` blob NOT NULL,
  `tanggal_dimasukan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  KEY `produk_penjual_FK` (`id_penjual`),
  CONSTRAINT `produk_penjual_FK` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (2,7,'Nasi Goreng','Nasgor',10000.00,999999,_binary '../uploads/679b1dd6b6692.jpg','2025-01-08 02:42:25'),(3,1,'Pangsit Mie','Mie Digodok',10000.00,999997,_binary '../uploads/67925f4cf1c01.png','2025-01-10 01:45:17'),(4,8,'Ayam Kentucky','Ayam Ditepungi',12000.00,9999997,_binary '../uploads/679b1e3a48586.jpg','2025-01-10 04:24:15'),(5,1,'Chilie Oil','Mie DIkasih Oil',10000.00,999990,_binary '../uploads/679ac04839b66.jpeg','2025-01-10 08:44:18'),(6,15,'Es Teh','Teh di kasih es',4000.00,9999996,'','2025-01-10 08:44:57'),(7,12,'Es Cappuccino ','Cappuccino Dikasih Es',4000.00,999987,'','2025-01-10 08:45:45'),(8,13,'Tahu Goreng','Tahu Digoreng',5000.00,999999,'','2025-01-10 08:46:22'),(9,7,'Sempol','Sempol',1000.00,999970,_binary '../uploads/67849cd95a0b6.jpg','2025-01-10 11:10:46'),(10,11,'Jus Buah','Buah diblender',5000.00,99989,_binary '../uploads/67849cc4cdf06.jpg','2025-01-10 14:33:13'),(13,15,'Batagor','Batako Digoreng',5000.00,999999,_binary '../uploads/678461eeccf13.jpg','2025-01-13 00:44:30'),(14,14,'Mie Gacoan','Mie Ga Cuan',10000.00,9999978,_binary '../uploads/67849d0850eb7.jpg','2025-01-13 04:56:40'),(15,10,'Korean Food','Panganan Korea',1000.00,999999970,_binary '../uploads/6792367b58f3f.jpg','2025-01-23 12:30:51'),(16,10,'Pisang Goreng','Pisang dikasih tepung + digoreng',1000.00,9999989,_binary '../uploads/6792386cc3de0.jpeg','2025-01-23 12:39:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `top_up`
--

LOCK TABLES `top_up` WRITE;
/*!40000 ALTER TABLE `top_up` DISABLE KEYS */;
INSERT INTO `top_up` VALUES (1,1,10000.00,'completed','2025-01-10 07:56:46',0),(2,1,10000.00,'completed','2025-01-10 08:05:30',0),(3,1,10000.00,'completed','2025-01-10 08:19:11',0),(4,1,20000.00,'completed','2025-01-10 08:23:10',0),(5,1,10000.00,'completed','2025-01-10 13:18:06',0),(6,1,10000.00,'completed','2025-01-10 13:27:29',0),(7,7,10000.00,'completed','2025-01-10 19:25:27',10000),(8,8,100000.00,'completed','2025-01-10 21:34:42',100000),(9,1,10000.00,'completed','2025-01-16 09:50:16',10000),(10,7,10000.00,'completed','2025-01-23 11:30:43',10000),(11,7,50000.00,'completed','2025-01-23 11:31:16',50000),(12,1,50000.00,'completed','2025-01-30 11:39:57',50000),(13,8,100000.00,'completed','2025-01-30 13:28:07',100000),(14,1,20000.00,'completed','2025-01-30 17:48:41',20000),(15,8,20000.00,'completed','2025-01-31 11:59:17',20000),(16,8,50000.00,'completed','2025-01-31 13:53:51',50000),(17,8,20000.00,'completed','2025-01-31 13:54:47',20000),(18,1,10000.00,'completed','2025-02-13 10:37:23',10000),(19,7,10000.00,'completed','2025-02-15 23:32:58',10000),(20,1,10000.00,'completed','2025-02-15 23:41:29',10000),(21,7,10000.00,'completed','2025-02-15 23:45:10',10000),(22,1,10000.00,'completed','2025-02-15 23:45:42',10000),(23,1,10000.00,'completed','2025-02-16 00:19:29',10000),(24,7,10000.00,'completed','2025-02-16 00:21:44',10000),(25,8,10000.00,'completed','2025-02-16 00:23:16',10000),(26,8,10000.00,'completed','2025-02-16 00:23:44',10000),(27,1,10000.00,'completed','2025-02-20 09:55:10',10000),(28,1,10000.00,'completed','2025-02-20 10:03:34',10000),(29,1,100000.00,'completed','2025-02-20 09:56:12',100000),(30,3,10000.00,'completed','2025-02-20 09:59:25',10000),(31,25,5000.00,'completed','2025-02-20 09:59:53',5000),(32,3,1000.00,'completed','2025-02-20 10:04:57',1000),(33,3,1000.00,'completed','2025-02-20 10:15:55',1000),(34,3,8000.00,'completed','2025-02-20 10:45:29',8000),(35,3,10000.00,'completed','2025-02-20 11:05:22',10000),(36,8,10000.00,'completed','2025-02-21 05:23:07',10000),(37,1,30000.00,'completed','2025-02-21 08:38:30',30000),(38,1,50000.00,'completed','2025-02-21 08:56:33',50000);
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Dony Septian R. - XII RPL 1','Asep','asep1234@gmail.com','user',0.00,'1234','2025-01-08 12:04:46'),(3,'Okan Karnivora - XII RPL 1','OkanPecintaJanda','okan1234@gmail.com','user',130000.00,'1234','2025-01-08 13:04:22'),(7,'Ridhollah Mustofa - XII RPL 1','Ribo','ribo1234@gmail.com','user',100000.00,'1234','2025-01-10 11:37:28'),(8,'Vincentcius Rafael - XII RPL 1','Kicentt.','vincentcius1234@gmail.com','user',120000.00,'1234','2025-01-10 11:38:12'),(9,'Radhit Pribadi T. - XII RPL 1','Dit','radhit1234@gmail.com','user',0.00,'1234','2025-01-10 12:33:59'),(10,'Bilal Sanayu M. - XII RPL 1','Bilal San','bilal1234@gmail.com','user',0.00,'1234','2025-01-10 12:56:14'),(11,'Bagus Setya - XII RPL 1','Bagus','bagus1234@gmail.com','user',0.00,'1234','2025-01-10 13:10:35'),(12,'Dimas Rahmanda - XII RPL 1','Rahmanda','dims1234@gmail.com','user',0.00,'1234','2025-01-10 13:30:26'),(15,'Moreno Ariel Wibowo - XII RPL 1','Reno','reno1234@gmail.com','user',0.00,'$2y$10$HmVRd4nPrKLcsL561hZziemwvBF3GHIUIWFuPE6c/rbTfhibc.xZm','2025-01-10 18:27:05'),(16,'Pak Kokok','kokok1234','kokok1234@gmail.com','guru',0.00,'$2y$10$MhfeMa2UPunsOp8W4CPCtu535CbwRZJF/AcHfD40yFngGSi7s6fLG','2025-01-15 09:30:51'),(17,'Pak Adit','Aditya','dit1234@gmail.com','guru',0.00,'$2y$10$pOjDZudR/bSJUsZ6fh3JNeVb1r3sFj0KcTbqdv4zI1NkbL768hBm.','2025-01-22 05:36:35'),(18,'Faiz - XII RPL 1','Mefiga','faiz1234@gmail.com','user',0.00,'$2y$10$UyfSVVi0zy1.nWYIEEs4tu6PXBzI1DdZ1qHjGFcSZQ/kiLqeKZi26','2025-01-22 05:37:37'),(19,'Bu Zaima','Zaima1234','zaima1234@gmail.com','guru',0.00,'$2y$10$VsanDpj6EHJjlXuMb4pt6OXbvRa4gkIfOpnv44DDEgNEk6mgm/2CW','2025-01-22 05:38:16'),(20,'Rangga - XII RPL 1','Angga','rangga1234@gmail.com','user',0.00,'$2y$10$n89MzDwpUAzBpizwoHwpcudT2tZrDz7hnZ9LlFlRvAx4SrmXQ9wJ2','2025-01-22 05:39:00'),(21,'Adji - XII RPL 1','Dji','adji1234@gmail.com','user',0.00,'$2y$10$BKZzbLAwwSEZBLVnuXtvGuW63vacgJWAtLtFFwxnVJNtOXJP5fj8i','2025-01-22 05:39:36'),(22,'Zhafran - XII RPL 1','Aza','zaf1234@gmail.com','user',0.00,'$2y$10$uoLTgHSBMhoRtLUTfC9NOuevkcdblulc.UiqA6UJhqF1nBS.0OdM6','2025-01-22 05:40:52'),(23,'Luqman - XII RPL 1','Luluq ','luqman1234@gmail.com','user',0.00,'$2y$10$gdrhs8EC81alQEk7M2AqLui3BNne3GmYzHqKROppI0yJhngSxHtC.','2025-01-22 05:41:41'),(24,'Grendy - XII RPL 1','Green Day','gren1234@gmail.com','user',0.00,'$2y$10$iliXOBV0e6FYMz1kHJWEK.oBXN8h9bWKbnETn24rttmLWoIVNQ.HW','2025-01-22 05:42:29'),(25,'Eugene - XII RPL 1','Ujin','eugene1234@gmail.com','user',5000.00,'$2y$10$y4FQQ.wBghlxWgYQHTKAnO.IK0UJ/vejeUfElIGZc7V3EnIHMSe22','2025-01-22 05:43:07'),(26,'Fitra - XII RPL 1','Monzy','fitra1234@gmail.com','user',0.00,'$2y$10$g/KN7XPqNQ3PvQfXtJ3Nb.YYVV.Uv0DSLOFRQOgcdQmJ0ljHqKvqq','2025-01-22 05:44:16'),(27,'Bryan - XII RPL 1','Yoru','bryan1234@gmail.com','user',0.00,'$2y$10$bQXgeeToEPDmX.yOvJadKuF3Ebt9PiIS2F0vNI669SsSGa7bPXGu6','2025-01-22 05:45:07'),(28,'Deva - XII RPL 1','Wibu','deva1234@gmail.com','user',0.00,'$2y$10$BwImH04XH0RhcN4QVZpPMuJ1bzw/wheZz9pJZXq9WybdWahL2o8vS','2025-01-22 05:45:39'),(29,'Yeremi - XII RPL 1','Yeri','yeremi1234@gmail.com','user',0.00,'$2y$10$1baYsOkN7lEtLWmY5PmieOqaabrXLIJpn54ga4msozyi604uL8nDu','2025-01-22 05:46:40'),(30,'Bintang - XII RPL 1','Star','bintang1234@gmail.com','user',0.00,'$2y$10$KbS9lht58noCJa2vaWLJCO/9HTQcr4z/WXE8N1ZdACLxyuIauliwy','2025-01-22 05:48:50'),(31,'Barok - XII RPL 1','Barok','barok1234@gmail.com','user',0.00,'$2y$10$QCP4yA9pZbobrOCfZCa6f.TCXdrnZLELsdG6OLDU.EFIy.mR3dwOa','2025-01-22 05:49:31'),(32,'Farel - XII RPL 1','farel','farel1234@gmail.com','user',0.00,'$2y$10$SMBw20Suu3HzmzVuoH4huO4AU7ebvcwzgJAjJxRqixHmqIydnS4fa','2025-01-22 05:50:09');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawals`
--

DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id_withdrawal` int NOT NULL AUTO_INCREMENT,
  `id_penjual` int NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `status` enum('completed','pending','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  `tanggal_withdrawal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_withdrawal` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_withdrawal`),
  KEY `withdrawals_penjual_FK` (`id_penjual`),
  CONSTRAINT `withdrawals_penjual_FK` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawals`
--

LOCK TABLES `withdrawals` WRITE;
/*!40000 ALTER TABLE `withdrawals` DISABLE KEYS */;
INSERT INTO `withdrawals` VALUES (1,7,20000.00,'completed','2025-01-31 05:45:46',20000.00),(2,12,8000.00,'completed','2025-01-31 05:50:07',8000.00),(3,10,30000.00,'completed','2025-01-31 06:21:33',30000.00),(4,14,10000.00,'completed','2025-01-31 06:44:05',10000.00),(5,8,2000.00,'completed','2025-01-31 07:07:23',2000.00),(6,15,13000.00,'completed','2025-02-13 03:25:11',13000.00),(7,14,10000.00,'completed','2025-02-21 08:00:46',10000.00),(8,10,1000.00,'completed','2025-02-24 04:45:12',1000.00),(9,8,2000.00,'completed','2025-02-24 07:14:32',2000.00);
/*!40000 ALTER TABLE `withdrawals` ENABLE KEYS */;
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

-- Dump completed on 2025-02-25 11:58:55
