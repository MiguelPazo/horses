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
CREATE DATABASE /*!32312 IF NOT EXISTS*/`horses` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `horses`;

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `type` enum('selection','wselection') NOT NULL DEFAULT 'selection',
  `status` enum('inactive','active','final') NOT NULL DEFAULT 'inactive',
  `actual_stage` enum('assistance','selection','classify_1','classify_2') DEFAULT NULL,
  `count_competitors` int(11) NOT NULL DEFAULT '0',
  `tournament_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categories_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_categories_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`count_competitors`,`tournament_id`) values (2,'Categoria 1','wselection','final','classify_2',20,2),(5,'Categoria 2','wselection','inactive',NULL,12,2),(8,'Categoria 3','wselection','inactive',NULL,15,2);

/*Table structure for table `category_users` */

DROP TABLE IF EXISTS `category_users`;

CREATE TABLE `category_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dirimente` tinyint(1) NOT NULL DEFAULT '0',
  `actual_stage` enum('selection','classify_1','classify_2') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categoria_jurado_user1_idx` (`user_id`),
  KEY `fk_category_users_categories1_idx` (`category_id`),
  CONSTRAINT `fk_categoria_jurado_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_users_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

/*Data for the table `category_users` */

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (35,0,NULL,7,5),(36,0,NULL,8,5),(37,1,NULL,9,5),(38,1,NULL,7,8),(39,0,NULL,8,8),(40,0,NULL,9,8),(41,0,'classify_2',7,2),(42,1,'classify_2',8,2),(43,0,'classify_2',9,2);

/*Table structure for table `competitors` */

DROP TABLE IF EXISTS `competitors`;

CREATE TABLE `competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` tinyint(4) NOT NULL,
  `position` tinyint(4) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_competitor_categories1_idx` (`category_id`),
  CONSTRAINT `fk_competitor_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

insert  into `competitors`(`id`,`number`,`position`,`category_id`) values (32,1,3,2),(33,2,14,2),(34,3,11,2),(35,4,10,2),(36,5,9,2),(37,6,13,2),(38,7,12,2),(39,9,7,2),(40,10,4,2),(41,15,8,2),(42,16,2,2),(43,17,5,2),(44,18,6,2),(45,19,1,2),(46,20,NULL,2);

/*Table structure for table `stages` */

DROP TABLE IF EXISTS `stages`;

CREATE TABLE `stages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `stage` enum('assistance','selection','classify_1','classify_2') NOT NULL,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `user_id` int(11) NOT NULL,
  `competitor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_etapa_user1_idx` (`user_id`),
  KEY `fk_etapa_competitor1_idx` (`competitor_id`),
  CONSTRAINT `fk_etapa_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_etapa_competitor1` FOREIGN KEY (`competitor_id`) REFERENCES `competitors` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=579 DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

insert  into `stages`(`id`,`position`,`stage`,`status`,`user_id`,`competitor_id`) values (439,1,'selection','active',7,32),(440,1,'selection','active',7,33),(441,1,'selection','active',7,34),(442,1,'selection','active',7,35),(443,1,'selection','active',7,36),(444,1,'selection','active',7,37),(445,1,'selection','active',7,38),(446,1,'selection','active',7,39),(447,1,'selection','active',7,40),(448,1,'selection','active',7,41),(449,1,'selection','active',7,42),(450,1,'selection','active',7,43),(451,1,'selection','active',8,32),(452,1,'selection','active',8,33),(453,1,'selection','active',8,34),(454,1,'selection','active',8,35),(455,1,'selection','active',8,37),(456,1,'selection','active',8,38),(457,1,'selection','active',8,39),(458,1,'selection','active',8,40),(459,1,'selection','active',8,42),(460,1,'selection','active',8,43),(461,1,'selection','active',8,44),(462,1,'selection','active',8,45),(475,1,'selection','active',9,32),(476,1,'selection','active',9,33),(477,1,'selection','active',9,34),(478,1,'selection','active',9,35),(479,1,'selection','active',9,36),(480,1,'selection','active',9,37),(481,1,'selection','active',9,39),(482,1,'selection','active',9,40),(483,1,'selection','active',9,41),(484,1,'selection','active',9,42),(485,1,'selection','active',9,43),(486,1,'selection','active',9,44),(487,1,'selection','active',9,45),(488,1,'selection','active',9,46),(489,1,'classify_1','active',9,45),(490,2,'classify_1','active',9,32),(491,3,'classify_1','active',9,40),(492,4,'classify_1','active',9,43),(493,5,'classify_1','active',9,44),(494,6,'classify_1','active',9,34),(495,7,'classify_1','active',9,42),(496,8,'classify_1','active',9,38),(497,9,'classify_1','active',9,36),(498,10,'classify_1','active',9,41),(499,11,'classify_1','active',9,39),(500,12,'classify_1','active',9,37),(501,13,'classify_1','active',9,33),(502,14,'classify_1','active',9,35),(503,1,'classify_1','active',8,45),(504,2,'classify_1','active',8,41),(505,3,'classify_1','active',8,42),(506,4,'classify_1','active',8,40),(507,5,'classify_1','active',8,43),(508,6,'classify_1','active',8,44),(509,7,'classify_1','active',8,32),(510,8,'classify_1','active',8,37),(511,9,'classify_1','active',8,39),(512,10,'classify_1','active',8,35),(513,11,'classify_1','active',8,36),(514,12,'classify_1','active',8,38),(515,13,'classify_1','active',8,33),(516,14,'classify_1','active',8,34),(517,1,'classify_1','active',7,39),(518,2,'classify_1','active',7,45),(519,3,'classify_1','active',7,44),(520,4,'classify_1','active',7,40),(521,5,'classify_1','active',7,35),(522,6,'classify_1','active',7,43),(523,7,'classify_1','active',7,36),(524,8,'classify_1','active',7,32),(525,9,'classify_1','active',7,42),(526,10,'classify_1','active',7,41),(527,11,'classify_1','active',7,34),(528,12,'classify_1','active',7,33),(529,13,'classify_1','active',7,38),(530,14,'classify_1','active',7,37),(537,2,'classify_2','active',9,45),(538,3,'classify_2','active',9,40),(539,4,'classify_2','active',9,42),(540,5,'classify_2','active',9,32),(541,6,'classify_2','active',9,43),(542,7,'classify_2','active',9,44),(549,2,'classify_2','active',8,45),(550,3,'classify_2','active',8,42),(551,4,'classify_2','active',8,32),(552,5,'classify_2','active',8,43),(553,6,'classify_2','active',8,44),(554,7,'classify_2','active',8,40),(573,2,'classify_2','active',7,45),(574,3,'classify_2','active',7,42),(575,4,'classify_2','active',7,32),(576,5,'classify_2','active',7,44),(577,6,'classify_2','active',7,40),(578,7,'classify_2','active',7,43);

/*Table structure for table `tournaments` */

DROP TABLE IF EXISTS `tournaments`;

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `date_begin` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `status` enum('inactive','active','deleted') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `tournaments` */

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`status`) values (2,'Torneo 1','2015-08-05','2015-08-29','active'),(3,'Torneo 2','1970-01-02','1970-01-24','inactive'),(4,'Torneo 3','1970-01-07','1970-01-31','inactive'),(5,'Torneo 4','2015-08-05','1970-01-01','inactive'),(6,'Torneo 5','2015-08-04','2015-08-20','inactive');

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
  `profile` enum('admin','operator','jury') NOT NULL DEFAULT 'operator',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_UNIQUE` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`) values (4,'Miguel1','Pazo','miguel','123',1,'NH2Es6hgIQyd7Tk9Ws2SbXTAFCJElHSqq4G83QdAtxFFyBcPDl','admin'),(5,'jose','alberto','operador','123',0,'mHGLdT9Hap47hErzI6qzbSk1oPzuwSiTpQsHbHxXERfa9eKisU','operator'),(6,'operador','op','optttt','123',0,NULL,'operator'),(7,'Jose','Alberto','jurado1','123',0,'v0lUks7Mrup1TiYnwV6leZKbVmbvmNDZPYwIQi66i0eP9npDjb','jury'),(8,'Lucia','Mariana','jurado2','123',0,'hNvgG4gr6Df491mIh7rj1QSUl6W0MwtplEj2tjotCtfRjrYPhP','jury'),(9,'Liliana','Alvarado','jurado3','123',0,'ly2krVJ2G8HdUjMct2DfTwsBUsOMzw9w8HG210ChEgMwL9phVJ','jury'),(10,'miguel','123','123','123',0,NULL,'operator');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
