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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`num_begin`,`count_competitors`,`tournament_id`) values (1,'MULARES','selection','inactive',NULL,1,3,1),(2,'CATEGORIA DE POTRANCAS AL CABESTRO DE 1 A 1 1/2 AÑOS','selection','inactive',NULL,1,8,1),(3,'CATEGORIA DE POTRANCAS AL CABESTRO DE 1 1/2 A 2 AÑOS','selection','inactive',NULL,1,10,1),(4,'CATEGORIA DE POTRANCAS AL CABESTRO DE 2 A 2 1/2 AÑOS','selection','inactive',NULL,1,7,1),(5,'CATEGORIA DE POTRANCAS AL CABESTRO DE 2 1/2 A 3 AÑOS','selection','inactive',NULL,1,6,1),(6,'PREMIO A LA MEJOR POTRANCA PRESENTADA AL CABESTRO','selection','inactive',NULL,1,0,1),(7,'CATEGORIA DE POTRANCAS DE BOZAL DE 3 A 4 AÑOS','selection','inactive',NULL,1,16,1),(8,'CAMPEONA DE BOZAL HEMBRA','selection','inactive',NULL,1,0,1),(9,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 4 A 6 AÑOS - GRUPO 1','selection','inactive',NULL,1,13,1),(10,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 4 A 6 AÑOS - GRUPO 2','selection','inactive',NULL,1,13,1),(11,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE 6 A 8 AÑOS','selection','inactive',NULL,1,16,1),(12,'CATEGORIA DE YEGUAS DE FRENO Y ESPUELAS DE MAS DE 8 AÑOS','selection','inactive',NULL,1,17,1),(13,'PREMIO PISOS PARA YEGUAS','selection','inactive',NULL,1,0,1),(14,'PREMIO MADRE E HIJA','selection','inactive',NULL,1,0,1),(15,'PREMIO PROGENIE DE MADRE','selection','inactive',NULL,1,3,1),(16,'PREMIO CONJUNTO DE YEGUAS','selection','inactive',NULL,1,2,1),(17,'PREMIO MERITO ZOOTECNICO HEMBRA','selection','inactive',NULL,1,0,1),(18,'CATEGORIA DE CAPONES DE BOZAL DE 3 A 4 AÑOS','selection','inactive',NULL,1,1,1),(19,'CAMPEON DE BOZAL CAPON','selection','inactive',NULL,1,0,1),(20,'CATEGORIA DE CAPONES DE FRENO Y ESPUELAS DE 4 A 6 AÑOS','selection','inactive',NULL,1,19,1),(21,'CATEGORIA DE CAPONES DE FRENO Y ESPUELAS DE 6 A 8 AÑOS','selection','inactive',NULL,1,9,1),(22,'CATEGORIA DE CAPONES DE FRENO Y ESPUELAS DE 8 A 12 AÑOS','selection','inactive',NULL,1,8,1),(23,'CATEGORIA DE CAPONES DE FRENO Y ESPUELAS DE MAS DE 12 AÑOS','selection','inactive',NULL,1,2,1),(24,'PREMIO PISOS PARA CAPONES','selection','inactive',NULL,1,0,1),(25,'PREMIO CONJUNTO DE CAPONES','selection','inactive',NULL,1,2,1),(26,'CATEGORIA DE POTRILLOS AL CABESTRO DE 1 A 2 AÑOS','selection','inactive',NULL,1,13,1),(27,'CATEGORIA DE POTRILLOS AL CABESTRO DE 2 A 3 AÑOS','selection','inactive',NULL,1,9,1),(28,'PREMIO AL MEJOR POTRILLO PRESENTADO AL CABESTRO','selection','inactive',NULL,1,0,1),(29,'CATEGORIA DE POTRILLOS DE BOZAL DE 3 A 4 AÑOS','selection','inactive',NULL,1,10,1),(30,'CAMPEON DE BOZAL MACHO','selection','inactive',NULL,1,0,1),(31,'CATEGORIA DE POTROS DE FRENO Y ESPUELAS DE 4 A 6 AÑOS','selection','inactive',NULL,1,11,1),(32,'CATEGORIA DE POTROS DE FRENO Y ESPUELAS DE 6 A 8 AÑOS','selection','inactive',NULL,1,7,1),(33,'CATEGORIA DE POTROS DE FRENO Y ESPUELAS DE MAS DE 8 AÑOS','selection','inactive',NULL,1,9,1),(34,'PREMIO PROGENIE DE PADRE','selection','inactive',NULL,1,1,1),(35,'PREMIO PISOS PARA POTROS','selection','inactive',NULL,1,0,1),(36,'PREMIO CONJUNTO DE POTROS','selection','inactive',NULL,1,0,1),(37,'PREMIO MERITO ZOOTECNICO MACHO','selection','inactive',NULL,1,0,1),(38,'PRUEBA DE MENORES DE 6 A 9 AÑOS (NIÑOS Y NIÑAS)','selection','inactive',NULL,1,0,1),(39,'PRUEBA DE MENORES DE 10 A 12 AÑOS (NIÑOS Y NIÑAS)','selection','inactive',NULL,1,0,1),(40,'PRUEBA DE ENFRENADURA','selection','inactive',NULL,1,0,1),(41,'CAMPEON Y RESERVA DE CAMPEON DEL AÑO CAPON','selection','inactive',NULL,1,0,1),(42,'CAMPEONA Y RESERVA DE CAMPEONA DEL AÑO HEMBRA','selection','inactive',NULL,1,0,1),(43,'CAMPEON Y RESERVA DE CAMPEON DEL AÑO MACHO','selection','inactive',NULL,1,0,1),(44,'PREMIO AFICION','selection','inactive',NULL,1,0,1),(45,'CAMPEON DE CAMPEONES CAPON','selection','inactive',NULL,1,2,1),(46,'CAMPEONA DE CAMPEONAS HEMBRA','selection','inactive',NULL,1,1,1),(47,'CAMPEON DE CAMPEONES MACHO','selection','inactive',NULL,1,1,1),(48,'BURROS','selection','inactive',NULL,1,1,1);

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `category_users` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

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

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`type`,`status`) values (1,'XXXVII CONCURSO DEPARTAMENTAL DEL CABALLO PERUANO DE PASO','2015-11-06','2015-11-08','jury','inactive');

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

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`,`status`) values (1,'Miguel','Pazo Sánchez','admin','$2y$10$4H4J2nPfxtfujPYxdcqAROboWri0X2C2La1FJY9KEQ6nY8TwIhS2e',0,NULL,'admin','active'),(2,'Jose','Canario','Comisario','$2y$10$fOpzetUN6FjwOXR7akYDtO4uPOBAv6xjnirfdaX5y3qxVYRWvInii',0,NULL,'commissar','active'),(3,'Luis','Diaz','jurado1','$2y$10$zoLjVz96QjhNsfPZwbLZFOkgWMZJpraN/zhoiXsk4fGezYzZzQv46',0,NULL,'jury','active'),(4,'Jose','Canario','jurado2','$2y$10$PE9AJIA7vQOBfFdHimZp6.53vRGHoZ6l2MqvVNy0qktVrqjOzZpwa',0,NULL,'jury','active'),(5,'Alfredo','Benavides','jurado3','$2y$10$ZzOw4FUsHNPduiLPH.dtLuGFwjX7Rt5oCoMcv9MYBLbUcynlzdqNC',0,NULL,'jury','active');

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
