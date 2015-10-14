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
  `status` enum('inactive','active','in_progress','final') NOT NULL DEFAULT 'inactive',
  `actual_stage` enum('assistance','selection','classify_1','classify_2') DEFAULT NULL,
  `num_begin` int(11) NOT NULL DEFAULT '1',
  `count_competitors` int(11) NOT NULL DEFAULT '0',
  `tournament_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categories_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_categories_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`num_begin`,`count_competitors`,`tournament_id`) values (1,'categoria con jurado','wselection','final','classify_2',1,12,1),(2,'categoria sin jurado','wselection','final','classify_2',1,12,2);

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

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (4,1,'classify_2',3,2),(5,0,'classify_2',4,2),(6,0,'classify_2',5,2),(7,1,'classify_2',3,1),(8,0,'classify_2',4,1),(9,0,'classify_2',5,1);

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

insert  into `competitors`(`id`,`number`,`position`,`points`,`category_id`) values (13,1,6,17,1),(14,2,1,3,1),(15,3,2,6,1),(16,4,11,29,1),(17,5,12,35,1),(18,6,9,24,1),(19,7,7,20,1),(20,8,10,29,1),(21,9,8,20,1),(22,10,4,11,1),(23,11,5,16,1),(24,12,3,10,1),(25,1,7,18,2),(26,2,2,5,2),(27,3,12,28,2),(28,4,6,11,2),(29,5,4,13,2),(30,6,11,28,2),(31,7,1,8,2),(32,8,10,22,2),(33,9,9,22,2),(34,10,8,22,2),(35,11,5,16,2),(36,12,3,10,2);

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
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

insert  into `stages`(`id`,`position`,`stage`,`status`,`category_id`,`user_id`,`competitor_id`) values (73,1,'classify_1','final',1,3,13),(74,2,'classify_1','final',1,3,14),(75,3,'classify_1','final',1,3,15),(76,4,'classify_1','final',1,3,19),(77,5,'classify_1','final',1,3,21),(78,6,'classify_1','final',1,3,24),(79,7,'classify_1','final',1,3,23),(80,8,'classify_1','final',1,3,22),(81,9,'classify_1','final',1,3,18),(82,10,'classify_1','final',1,3,20),(83,11,'classify_1','final',1,3,16),(84,12,'classify_1','final',1,3,17),(85,1,'classify_1','final',1,4,13),(86,2,'classify_1','final',1,4,14),(87,3,'classify_1','final',1,4,15),(88,4,'classify_1','final',1,4,23),(89,5,'classify_1','final',1,4,22),(90,6,'classify_1','final',1,4,24),(91,7,'classify_1','final',1,4,21),(92,8,'classify_1','final',1,4,16),(93,9,'classify_1','final',1,4,19),(94,10,'classify_1','final',1,4,20),(95,11,'classify_1','final',1,4,17),(96,12,'classify_1','final',1,4,18),(97,1,'classify_1','final',1,5,15),(98,2,'classify_1','final',1,5,14),(99,3,'classify_1','final',1,5,18),(100,4,'classify_1','final',1,5,23),(101,5,'classify_1','final',1,5,22),(102,6,'classify_1','final',1,5,24),(103,7,'classify_1','final',1,5,19),(104,8,'classify_1','final',1,5,21),(105,9,'classify_1','final',1,5,20),(106,10,'classify_1','final',1,5,16),(107,11,'classify_1','final',1,5,13),(108,12,'classify_1','final',1,5,17),(109,1,'classify_2','final',1,5,14),(110,2,'classify_2','final',1,5,15),(111,3,'classify_2','final',1,5,24),(112,4,'classify_2','final',1,5,22),(113,5,'classify_2','final',1,5,13),(114,6,'classify_2','final',1,5,23),(115,1,'classify_2','final',1,4,14),(116,2,'classify_2','final',1,4,15),(117,3,'classify_2','final',1,4,24),(118,4,'classify_2','final',1,4,22),(119,5,'classify_2','final',1,4,23),(120,6,'classify_2','final',1,4,13),(121,1,'classify_2','final',1,3,14),(122,2,'classify_2','final',1,3,15),(123,3,'classify_2','final',1,3,22),(124,4,'classify_2','final',1,3,24),(125,5,'classify_2','final',1,3,23),(126,6,'classify_2','final',1,3,13),(127,1,'classify_1','final',2,3,31),(128,2,'classify_1','final',2,3,28),(129,3,'classify_1','final',2,3,35),(130,4,'classify_1','final',2,3,36),(131,5,'classify_1','final',2,3,30),(132,6,'classify_1','final',2,3,25),(133,7,'classify_1','final',2,3,33),(134,8,'classify_1','final',2,3,34),(135,9,'classify_1','final',2,3,32),(136,10,'classify_1','final',2,3,29),(137,11,'classify_1','final',2,3,27),(138,12,'classify_1','final',2,3,26),(139,1,'classify_1','final',2,4,31),(140,2,'classify_1','final',2,4,26),(141,3,'classify_1','final',2,4,28),(142,4,'classify_1','final',2,4,29),(143,5,'classify_1','final',2,4,36),(144,6,'classify_1','final',2,4,34),(145,7,'classify_1','final',2,4,35),(146,8,'classify_1','final',2,4,27),(147,9,'classify_1','final',2,4,33),(148,10,'classify_1','final',2,4,32),(149,11,'classify_1','final',2,4,25),(150,12,'classify_1','final',2,4,30),(151,1,'classify_1','final',2,5,25),(152,2,'classify_1','final',2,5,26),(153,3,'classify_1','final',2,5,32),(154,4,'classify_1','final',2,5,29),(155,5,'classify_1','final',2,5,35),(156,6,'classify_1','final',2,5,33),(157,7,'classify_1','final',2,5,36),(158,8,'classify_1','final',2,5,34),(159,9,'classify_1','final',2,5,27),(160,10,'classify_1','final',2,5,31),(161,11,'classify_1','final',2,5,30),(162,12,'classify_1','final',2,5,28),(163,1,'classify_2','final',2,5,31),(164,2,'classify_2','final',2,5,26),(165,3,'classify_2','final',2,5,36),(166,4,'classify_2','final',2,5,28),(167,5,'classify_2','final',2,5,29),(168,6,'classify_2','final',2,5,35),(169,1,'classify_2','final',2,4,31),(170,2,'classify_2','final',2,4,26),(171,3,'classify_2','final',2,4,29),(172,4,'classify_2','final',2,4,36),(173,5,'classify_2','final',2,4,28),(174,6,'classify_2','final',2,4,35),(175,1,'classify_2','final',2,3,26),(176,2,'classify_2','final',2,3,28),(177,3,'classify_2','final',2,3,36),(178,4,'classify_2','final',2,3,35),(179,5,'classify_2','final',2,3,29),(180,6,'classify_2','final',2,3,31);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `tournaments` */

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`type`,`status`) values (1,'Concurso 1','2015-10-14','2015-10-16','jury','inactive'),(2,'Conruso 3','2015-10-13','2015-10-14','wjury','active');

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

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`,`status`) values (1,'Miguel','Pazo Sánchez','admin','$2y$10$gg9dnnsP592CHr8SIvLMvOKxfWL4qYp65Q5AzZcrikCN6XINhBCbO',0,'0GAw60Kuz43twY3hlQiqxg3A3SxUPFNVvgzsG7ZdUg1RRAzF4K','admin','active'),(2,'comisario','comi','comisario','$2y$10$envZUJgWPO.jXz5ZuvIbbOYzISTWgLa7AamvpvfYoAKrdlhuzdwDO',0,'831rx6HCWzR5ut1ScM70Y1NQriHITC2Q0c1oMRii7AJIeuntlM','commissar','active'),(3,'jur','ado1','jurado1','$2y$10$ugcGstfrFUEdgOChlorIfOGAQiUK6hyolwi2rGZnixH5CdtTBXQcW',0,'bPMxSm9jLQEWa8dbkRxmPzA3kZcAZJ03o3IIotGVIbUspK6TTa','jury','active'),(4,'jurado','2','jurado2','$2y$10$QqUVreSqJXivG7WBkv0MkegiYI7mN8dmec18.yFlZ149ga0BLIgHK',0,'AO3ANbTzkTI4nL6KJJrwCloCIhV2Jm6myTiYOJXLrB9vyZioz0','jury','active'),(5,'jur','ado3','jurado3','$2y$10$NeoDTjL.mYFThdSngv408u8p9ECEOBbBSLHrrXtRvJ.FzNZFHLcnq',0,'J5nr0GzOgOztVONOOfsMJhq0mWUr0jWUXIL8cijQRrriwAiWbs','jury','active');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
