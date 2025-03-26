-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: nextshop_whatsapp_demo
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.24.04.1

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
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,'ZsnNnvf7ACJvw0k0CtLVsIyj0UO8o4ytbE0QMWEa.png',NULL,'Category',1),(2,'YwsM6FW3hBAkUXrg9L9w4uLdPwfWtEMKNTWv8FwOf.png','featured','Product',1),(3,'tZe4ZP1OjAig1q9El5tMUOjuUFPFTAOX5H4qlGPFX.png','featured','Product',2),(4,'OSQJzK9NoT01iLwpr3tyK0SUl2Ogd7RiZxX9thKWB.png','featured','Product',3);
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` VALUES (1,1,'flA9xApyjFKAH3j3glWbmFb8aXesKaukWbSbKO.jpg'),(2,2,'K5dH5Aw1bL3aq7crKbFyaj9q7Q6BR7bSixV74C.jpg');
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blocks`
--

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;
INSERT INTO `blocks` VALUES (1,'Original','Curabitur aliquet quam id dui posuere blandit.','AK0HiQhzBPORfCczBf5xLu4Rk9QiMFXFQhn4UJFoj3.png',1,'Featured',NULL,'2023-10-03 14:27:48','2023-10-03 14:27:48'),(2,'Terpercaya','Curabitur aliquet quam id dui posuere blandit.','Q9SFlhcSs3Dxq0WdW3EgvjCHC0DjvvunvIF5K2tpWO.png',2,'Featured',NULL,'2023-10-03 14:28:05','2023-10-03 14:28:05'),(3,'Terbaik','Curabitur aliquet quam id dui posuere blandit.','q2AnUxEdJhZ5h7ZVni6tYhxKD5l221gxNQk0i3tIWN.png',3,'Featured',NULL,'2023-10-03 14:28:20','2023-10-03 14:28:20');
/*!40000 ALTER TABLE `blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Makanan & Minuman','makanan-minuman',NULL,'Makanan & Minuman',1,1,'2023-10-03 14:28:56','2023-10-03 14:28:56');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'Cara Berbelanja','cara-berbelanja',NULL,'j3nCZsu0ZqDjoXXCWJ9r8ileJukTTYycUCeTeLYdfY.jpg','<div>Nulla quis lorem ut libero malesuada feugiat. Sed porttitor lectus nibh. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Nulla porttitor accumsan tincidunt.</div><div><br></div><div>Sed porttitor lectus nibh. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Pellentesque in ipsum id orci porta dapibus. Sed porttitor lectus nibh.</div><div><br></div><div>Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Proin eget tortor risus. Nulla porttitor accumsan tincidunt. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Nulla porttitor accumsan tincidunt. Proin eget tortor risus. Nulla porttitor accumsan tincidunt.</div>',1,1,NULL,'2023-10-03 14:36:20','2023-10-03 14:36:20'),(2,'Cara Pembayaran','cara-pembayaran',NULL,'DfLkLGSY00oxO0xpFUNrUyM5LWkchgjZXbBRICghOj.jpg','<div>Nulla quis lorem ut libero malesuada feugiat. Sed porttitor lectus nibh. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Nulla porttitor accumsan tincidunt.</div><div><br></div><div>Sed porttitor lectus nibh. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Pellentesque in ipsum id orci porta dapibus. Sed porttitor lectus nibh.</div><div><br></div><div>Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Proin eget tortor risus. Nulla porttitor accumsan tincidunt. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Nulla porttitor accumsan tincidunt. Proin eget tortor risus. Nulla porttitor accumsan tincidunt.</div>',1,1,NULL,'2023-10-03 14:36:38','2023-10-03 14:36:38');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Kripik Talas','kripik-talas','a72b26fb-ab78-456d-b2d0-4d23ce54e870','<div>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Curabitur aliquet quam id dui posuere blandit. Cras ultricies ligula sed magna dictum porta. Pellentesque in ipsum id orci porta dapibus.</div><div><br></div><div>Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Nulla porttitor accumsan tincidunt. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus suscipit tortor eget felis porttitor volutpat.</div><div><br></div><div>Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Cras ultricies ligula sed magna dictum porta. Nulla porttitor accumsan tincidunt. Cras ultricies ligula sed magna dictum porta.</div>',100,20000,1,1,500,'2023-10-03 14:29:57','2023-10-03 14:29:57',NULL),(2,'Kripik Pare','kripik-pare','f960054d-4448-4353-838f-b93592fbf1f6','<div>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Curabitur aliquet quam id dui posuere blandit. Cras ultricies ligula sed magna dictum porta. Pellentesque in ipsum id orci porta dapibus.</div><div><br></div><div>Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Nulla porttitor accumsan tincidunt. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus suscipit tortor eget felis porttitor volutpat.</div><div><br></div><div>Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Cras ultricies ligula sed magna dictum porta. Nulla porttitor accumsan tincidunt. Cras ultricies ligula sed magna dictum porta.</div>',0,0,1,1,0,'2023-10-03 14:31:37','2023-10-03 14:31:37',NULL),(3,'Kripik Rumput Laut','kripik-rumput-laut','01c197ce-fab7-4061-be55-4802d0388def','<div>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Curabitur aliquet quam id dui posuere blandit. Cras ultricies ligula sed magna dictum porta. Pellentesque in ipsum id orci porta dapibus.</div><div><br></div><div>Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Nulla porttitor accumsan tincidunt. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus suscipit tortor eget felis porttitor volutpat.</div><div><br></div><div>Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Cras ultricies ligula sed magna dictum porta. Nulla porttitor accumsan tincidunt. Cras ultricies ligula sed magna dictum porta.</div>',0,0,1,1,0,'2023-10-03 14:34:06','2023-10-03 14:34:06',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_varians`
--

LOCK TABLES `product_varians` WRITE;
/*!40000 ALTER TABLE `product_varians` DISABLE KEYS */;
INSERT INTO `product_varians` VALUES (1,2,'Ukuran','200 Gram','29dbd84c-7dfa-4534-bc14-5d88346ae0c7',20000,100,600,NULL,0,'2023-10-03 14:31:38','2023-10-03 14:31:38'),(2,2,'Ukuran','500 Gram','2a0ba7f5-ba0a-40ab-80f5-d1d2f5492285',35000,100,600,NULL,0,'2023-10-03 14:31:38','2023-10-03 14:31:38'),(3,2,'Ukuran','1000 Gram','cf4b4ecd-2b01-4c80-8884-29d16e24cb02',70000,100,600,NULL,0,'2023-10-03 14:31:38','2023-10-03 14:31:38'),(4,3,'Rasa','Original','2a951e3e-b48e-454d-92ed-51684131c977',0,0,100,NULL,1,'2023-10-03 14:34:06','2023-10-03 14:34:06'),(5,3,'Ukuran','200 Gram','6aa7f3ca-f422-46da-9dec-a5345f50b5f6',20000,100,600,4,0,'2023-10-03 14:34:06','2023-10-03 14:34:06'),(6,3,'Ukuran','500 Gram','e9bc27a5-51e2-466a-9467-4c0f93c8857d',35000,100,600,4,0,'2023-10-03 14:34:06','2023-10-03 14:34:06'),(7,3,'Rasa','Barbeque','7e7770ae-46c9-4f63-b311-3a450ae513d5',0,0,100,NULL,1,'2023-10-03 14:34:06','2023-10-03 14:34:06'),(8,3,'Ukuran','200 Gram','eb91be15-41ac-480e-af80-37cc9ba1eb99',20000,100,600,7,0,'2023-10-03 14:34:06','2023-10-03 14:34:06'),(9,3,'Ukuran','500 Gram','81b823d5-fcd3-4314-89c3-18b9a93a0227',35000,100,600,7,0,'2023-10-03 14:34:06','2023-10-03 14:34:06');
/*!40000 ALTER TABLE `product_varians` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-27  3:35:40
