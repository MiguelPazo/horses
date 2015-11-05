/*
SQLyog Ultimate v9.63 
MySQL - 5.6.16 : Database - horses
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `type` enum('selection','wselection') NOT NULL DEFAULT 'selection',
  `status` enum('inactive','active','in_progress','final','deleted') NOT NULL DEFAULT 'inactive',
  `actual_stage` enum('assistance','selection','classify_1','classify_2') DEFAULT NULL,
  `num_begin` int(11) NOT NULL DEFAULT '1',
  `count_competitors` int(11) NOT NULL DEFAULT '0',
  `tournament_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categories_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_categories_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`num_begin`,`count_competitors`,`tournament_id`) values (1,'MULARES','selection','deleted','classify_1',1,3,1),(2,'CATEGORIA DE POTRANCAS DE BOZAL DE 3 A 4 AÑOS','selection','final','classify_2',1,16,1),(3,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 4 A 6 AÑOS - GRUPO 1','selection','final','classify_2',1,13,1),(4,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 4 A 6 AÑOS - GRUPO 2','selection','inactive',NULL,1,13,1),(5,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 6 A 8 AÑOS','selection','inactive',NULL,1,16,1),(6,'MULARES DELETE','selection','deleted','assistance',1,12,1);

/*Table structure for table `category_users` */

DROP TABLE IF EXISTS `category_users`;

CREATE TABLE `category_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dirimente` tinyint(1) NOT NULL DEFAULT '0',
  `actual_stage` enum('assistance','selection','classify_1','classify_2') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categoria_jurado_user1_idx` (`user_id`),
  KEY `fk_category_users_categories1_idx` (`category_id`),
  CONSTRAINT `fk_categoria_jurado_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_users_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `category_users` */

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (1,1,'classify_2',3,1),(2,0,'classify_1',4,1),(3,0,'classify_1',5,1),(4,1,'classify_2',3,2),(5,0,'classify_2',4,2),(6,0,'classify_2',5,2),(7,0,'classify_2',3,3),(8,1,'classify_2',4,3),(9,0,'classify_2',5,3);

/*Table structure for table `competitors` */

DROP TABLE IF EXISTS `competitors`;

CREATE TABLE `competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` tinyint(3) unsigned NOT NULL,
  `position` tinyint(3) unsigned DEFAULT NULL,
  `points` smallint(5) unsigned DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_competitor_categories1_idx` (`category_id`),
  CONSTRAINT `fk_competitor_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

insert  into `competitors`(`id`,`number`,`position`,`points`,`category_id`) values (1,1,NULL,NULL,6),(2,2,NULL,NULL,6),(3,3,NULL,NULL,6),(4,4,NULL,NULL,6),(5,5,NULL,NULL,6),(6,6,NULL,NULL,6),(7,7,NULL,NULL,6),(8,8,NULL,NULL,6),(9,9,NULL,NULL,6),(10,10,NULL,NULL,6),(11,11,NULL,NULL,6),(12,12,NULL,NULL,6),(13,1,1,3,1),(14,2,2,6,1),(15,3,3,9,1),(16,1,2,8,2),(17,2,13,36,2),(18,3,1,6,2),(19,4,6,15,2),(20,5,12,36,2),(21,6,NULL,NULL,2),(22,7,8,23,2),(23,8,11,30,2),(24,9,10,28,2),(25,10,NULL,NULL,2),(26,11,9,24,2),(27,12,4,12,2),(28,13,7,18,2),(29,14,3,9,2),(30,15,5,13,2),(31,16,NULL,NULL,2),(32,1,1,3,3),(33,2,10,25,3),(34,3,13,39,3),(35,4,11,26,3),(36,5,9,25,3),(37,6,7,21,3),(38,7,12,29,3),(39,8,8,25,3),(40,9,6,14,3),(41,10,3,10,3),(42,11,4,13,3),(43,12,2,9,3),(44,13,5,14,3);

/*Table structure for table `stages` */

DROP TABLE IF EXISTS `stages`;

CREATE TABLE `stages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `stage` enum('assistance','selection','classify_1','classify_2') NOT NULL,
  `status` enum('active','final') NOT NULL DEFAULT 'active',
  `category_id` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `competitor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_etapa_user1_idx` (`user_id`),
  KEY `fk_etapa_competitor1_idx` (`competitor_id`),
  CONSTRAINT `fk_etapa_competitor1` FOREIGN KEY (`competitor_id`) REFERENCES `competitors` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etapa_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

insert  into `stages`(`id`,`position`,`stage`,`status`,`category_id`,`user_id`,`competitor_id`) values (1,1,'selection','active',1,3,13),(2,1,'selection','active',1,3,14),(3,1,'selection','active',1,3,15),(4,1,'selection','active',1,4,13),(5,1,'selection','active',1,4,14),(6,1,'selection','active',1,4,15),(7,1,'selection','active',1,5,13),(8,1,'selection','active',1,5,14),(9,1,'selection','active',1,5,15),(10,1,'classify_1','active',1,5,13),(11,2,'classify_1','active',1,5,14),(12,3,'classify_1','active',1,5,15),(13,1,'classify_1','active',1,4,13),(14,2,'classify_1','active',1,4,14),(15,3,'classify_1','active',1,4,15),(16,1,'classify_1','active',1,3,13),(17,2,'classify_1','active',1,3,14),(18,3,'classify_1','active',1,3,15),(19,1,'classify_2','active',1,3,13),(20,2,'classify_2','active',1,3,14),(21,3,'classify_2','active',1,3,15),(22,1,'selection','final',2,3,16),(23,1,'selection','final',2,3,17),(24,1,'selection','final',2,3,18),(25,1,'selection','final',2,3,20),(26,1,'selection','final',2,3,22),(27,1,'selection','final',2,3,23),(28,1,'selection','final',2,3,24),(29,1,'selection','final',2,3,26),(30,1,'selection','final',2,3,28),(31,1,'selection','final',2,3,29),(32,1,'selection','final',2,3,30),(33,1,'selection','final',2,3,31),(34,1,'selection','final',2,4,16),(35,1,'selection','final',2,4,18),(36,1,'selection','final',2,4,19),(37,1,'selection','final',2,4,20),(38,1,'selection','final',2,4,22),(39,1,'selection','final',2,4,23),(40,1,'selection','final',2,4,24),(41,1,'selection','final',2,4,25),(42,1,'selection','final',2,4,26),(43,1,'selection','final',2,4,27),(44,1,'selection','final',2,4,29),(45,1,'selection','final',2,4,30),(46,1,'selection','final',2,5,16),(47,1,'selection','final',2,5,17),(48,1,'selection','final',2,5,18),(49,1,'selection','final',2,5,19),(50,1,'selection','final',2,5,21),(51,1,'selection','final',2,5,22),(52,1,'selection','final',2,5,23),(53,1,'selection','final',2,5,24),(54,1,'selection','final',2,5,26),(55,1,'selection','final',2,5,27),(56,1,'selection','final',2,5,28),(57,1,'selection','final',2,5,30),(58,1,'classify_1','final',2,5,18),(59,2,'classify_1','final',2,5,22),(60,3,'classify_1','final',2,5,27),(61,4,'classify_1','final',2,5,30),(62,5,'classify_1','final',2,5,29),(63,6,'classify_1','final',2,5,28),(64,7,'classify_1','final',2,5,16),(65,8,'classify_1','final',2,5,19),(66,9,'classify_1','final',2,5,26),(67,10,'classify_1','final',2,5,24),(68,11,'classify_1','final',2,5,17),(69,12,'classify_1','final',2,5,23),(70,13,'classify_1','final',2,5,20),(71,1,'classify_1','final',2,4,18),(72,2,'classify_1','final',2,4,16),(73,3,'classify_1','final',2,4,19),(74,4,'classify_1','final',2,4,29),(75,5,'classify_1','final',2,4,30),(76,6,'classify_1','final',2,4,27),(77,7,'classify_1','final',2,4,28),(78,8,'classify_1','final',2,4,26),(79,9,'classify_1','final',2,4,23),(80,10,'classify_1','final',2,4,24),(81,11,'classify_1','final',2,4,22),(82,12,'classify_1','final',2,4,20),(83,13,'classify_1','final',2,4,17),(84,1,'classify_1','final',2,3,16),(85,2,'classify_1','final',2,3,19),(86,3,'classify_1','final',2,3,29),(87,4,'classify_1','final',2,3,30),(88,5,'classify_1','final',2,3,28),(89,6,'classify_1','final',2,3,27),(90,7,'classify_1','final',2,3,26),(91,8,'classify_1','final',2,3,24),(92,9,'classify_1','final',2,3,23),(93,10,'classify_1','final',2,3,22),(94,11,'classify_1','final',2,3,20),(95,12,'classify_1','final',2,3,17),(96,13,'classify_1','final',2,3,18),(97,1,'classify_2','final',2,3,16),(98,2,'classify_2','final',2,3,18),(99,3,'classify_2','final',2,3,19),(100,4,'classify_2','final',2,3,27),(101,5,'classify_2','final',2,3,30),(102,6,'classify_2','final',2,3,29),(103,1,'classify_2','final',2,4,18),(104,2,'classify_2','final',2,4,29),(105,3,'classify_2','final',2,4,30),(106,4,'classify_2','final',2,4,27),(107,5,'classify_2','final',2,4,16),(108,6,'classify_2','final',2,4,19),(109,1,'classify_2','final',2,5,29),(110,2,'classify_2','final',2,5,16),(111,3,'classify_2','final',2,5,18),(112,4,'classify_2','final',2,5,27),(113,5,'classify_2','final',2,5,30),(114,6,'classify_2','final',2,5,19),(115,1,'selection','final',3,3,32),(116,1,'selection','final',3,3,33),(117,1,'selection','final',3,3,35),(118,1,'selection','final',3,3,36),(119,1,'selection','final',3,3,37),(120,1,'selection','final',3,3,38),(121,1,'selection','final',3,3,39),(122,1,'selection','final',3,3,40),(123,1,'selection','final',3,3,41),(124,1,'selection','final',3,3,42),(125,1,'selection','final',3,3,43),(126,1,'selection','final',3,3,44),(127,1,'selection','final',3,4,32),(128,1,'selection','final',3,4,33),(129,1,'selection','final',3,4,34),(130,1,'selection','final',3,4,35),(131,1,'selection','final',3,4,36),(132,1,'selection','final',3,4,37),(133,1,'selection','final',3,4,38),(134,1,'selection','final',3,4,39),(135,1,'selection','final',3,4,40),(136,1,'selection','final',3,4,41),(137,1,'selection','final',3,4,42),(138,1,'selection','final',3,4,43),(139,1,'selection','final',3,5,33),(140,1,'selection','final',3,5,34),(141,1,'selection','final',3,5,35),(142,1,'selection','final',3,5,36),(143,1,'selection','final',3,5,37),(144,1,'selection','final',3,5,38),(145,1,'selection','final',3,5,39),(146,1,'selection','final',3,5,40),(147,1,'selection','final',3,5,41),(148,1,'selection','final',3,5,42),(149,1,'selection','final',3,5,43),(150,1,'selection','final',3,5,44),(151,1,'classify_1','final',3,5,32),(152,2,'classify_1','final',3,5,42),(153,3,'classify_1','final',3,5,35),(154,4,'classify_1','final',3,5,41),(155,5,'classify_1','final',3,5,40),(156,6,'classify_1','final',3,5,44),(157,7,'classify_1','final',3,5,39),(158,8,'classify_1','final',3,5,38),(159,9,'classify_1','final',3,5,43),(160,10,'classify_1','final',3,5,37),(161,11,'classify_1','final',3,5,36),(162,12,'classify_1','final',3,5,33),(163,13,'classify_1','final',3,5,34),(164,1,'classify_1','final',3,4,32),(165,2,'classify_1','final',3,4,42),(166,3,'classify_1','final',3,4,44),(167,4,'classify_1','final',3,4,40),(168,5,'classify_1','final',3,4,43),(169,6,'classify_1','final',3,4,37),(170,7,'classify_1','final',3,4,41),(171,8,'classify_1','final',3,4,39),(172,9,'classify_1','final',3,4,38),(173,10,'classify_1','final',3,4,36),(174,11,'classify_1','final',3,4,33),(175,12,'classify_1','final',3,4,35),(176,13,'classify_1','final',3,4,34),(177,1,'classify_1','final',3,3,32),(178,2,'classify_1','final',3,3,33),(179,3,'classify_1','final',3,3,44),(180,4,'classify_1','final',3,3,36),(181,5,'classify_1','final',3,3,37),(182,6,'classify_1','final',3,3,43),(183,7,'classify_1','final',3,3,41),(184,8,'classify_1','final',3,3,42),(185,9,'classify_1','final',3,3,40),(186,10,'classify_1','final',3,3,39),(187,11,'classify_1','final',3,3,35),(188,12,'classify_1','final',3,3,38),(189,13,'classify_1','final',3,3,34),(190,1,'classify_2','final',3,3,32),(191,2,'classify_2','final',3,3,42),(192,3,'classify_2','final',3,3,43),(193,4,'classify_2','final',3,3,41),(194,5,'classify_2','final',3,3,40),(195,6,'classify_2','final',3,3,44),(196,1,'classify_2','final',3,4,32),(197,2,'classify_2','final',3,4,44),(198,3,'classify_2','final',3,4,43),(199,4,'classify_2','final',3,4,41),(200,5,'classify_2','final',3,4,40),(201,6,'classify_2','final',3,4,42),(202,1,'classify_2','final',3,5,32),(203,2,'classify_2','final',3,5,41),(204,3,'classify_2','final',3,5,43),(205,4,'classify_2','final',3,5,40),(206,5,'classify_2','final',3,5,42),(207,6,'classify_2','final',3,5,44);

/*Table structure for table `tournaments` */

DROP TABLE IF EXISTS `tournaments`;

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `date_begin` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `type` enum('jury','wjury') NOT NULL DEFAULT 'jury',
  `status` enum('inactive','active','deleted') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `tournaments` */

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`type`,`status`) values (1,'XXXVII CONCURSO DEPARTAMENTAL DEL CABALLO PERUANO DE PASO','2015-11-06','2015-11-08','jury','active');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `names` varchar(50) NOT NULL,
  `lastname` varchar(80) NOT NULL,
  `user` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `login` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(50) DEFAULT NULL,
  `profile` enum('admin','commissar','jury') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`,`status`) values (1,'Miguel','Pazo Sánchez','admin','$2y$10$yZBeKKrI73VEZZroUVqNNuva4jMg7rDWvwhfk16bf7EjIM9h3MNYu',0,'QnXCMvsQXqQmsTAPZEioIbQapmZYdkdfYrZ0dzXcG4EjPuWGtS','admin','active'),(2,'Jose','Canario','comisario','$2y$10$cgoHnNd1jLEnBI0cFeb11OP2eRFe0BGCzeGzV7.hWgywu3yt/Z/s.',0,'Vwh6QxUeemak0wGLbGAhcmNpREEGwYlJ7FchSR8xw0Z5OAWlB4','commissar','active'),(3,'Luis','Carrillo','jurado1','$2y$10$EXix7r2OkaTvdBsb9lxJEO7a42rjltm8fGpVykicZXhck4y5lhAs6',0,'SDZfCTq6RNkFePVXaaOeOUAqdv1QKxmpErWro2yKHQ5W43AULd','jury','active'),(4,'Pablo','Escobar','jurado2','$2y$10$lw9VdINGvzIHJuOb3bn8yumgj/I1MKPcjGDOBTLSi4d4F6k6/a9Wa',0,'uzGbugVdJn2Wx1jSimfG4Tn4mFDmvkgotSFkqkNmQ4U186Z2Px','jury','active'),(5,'Andre','Wise','jurado3','$2y$10$9nEoqRmulxwYUTTIjXOgVumTxuEt2XDy63NGCr9agXufoywxIRR3K',0,'XIwzgkjkIOpOHVIuACOP1QFwvJE0pVzfx2WnWwIaUE2cFWJIfb','jury','active');

/*Table structure for table `resume` */

DROP TABLE IF EXISTS `resume`;

/*!50001 DROP VIEW IF EXISTS `resume` */;
/*!50001 DROP TABLE IF EXISTS `resume` */;

/*!50001 CREATE TABLE  `resume`(
 `category_id` int(11) ,
 `category` varchar(200) ,
 `position` tinyint(3) unsigned ,
 `num_camp` tinyint(3) unsigned ,
 `points` smallint(5) unsigned 
)*/;

/*View structure for view resume */

/*!50001 DROP TABLE IF EXISTS `resume` */;
/*!50001 DROP VIEW IF EXISTS `resume` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `resume` AS select `ca`.`id` AS `category_id`,`ca`.`description` AS `category`,`c`.`position` AS `position`,`c`.`number` AS `num_camp`,`c`.`points` AS `points` from (`competitors` `c` join `categories` `ca` on((`ca`.`id` = `c`.`category_id`))) where ((`ca`.`status` = 'final') and (`c`.`position` is not null)) order by `ca`.`description`,`c`.`position` */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
