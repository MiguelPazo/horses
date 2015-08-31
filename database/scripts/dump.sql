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
  `status` enum('inactive','active','in_progress','final') NOT NULL DEFAULT 'inactive',
  `actual_stage` enum('assistance','selection','classify_1','classify_2') DEFAULT NULL,
  `num_begin` int(11) NOT NULL DEFAULT '1',
  `count_competitors` int(11) NOT NULL DEFAULT '0',
  `tournament_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categories_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_categories_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`num_begin`,`count_competitors`,`tournament_id`) values (8,'Categoria 1','selection','in_progress','classify_1',123,23,4),(9,'Categoria 2','selection','inactive',NULL,200,23,4),(10,'Categoria 3','selection','inactive',NULL,1,25,4);

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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

/*Data for the table `category_users` */

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (52,1,'classify_1',11,8),(53,0,'classify_1',12,8),(54,0,'classify_1',13,8),(55,0,NULL,11,9),(56,1,NULL,12,9),(57,0,NULL,13,9),(58,0,NULL,11,10),(59,0,NULL,12,10),(60,1,NULL,13,10);

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
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

insert  into `competitors`(`id`,`number`,`position`,`points`,`category_id`) values (139,123,NULL,NULL,8),(140,124,5,14,8),(141,125,9,22,8),(142,126,13,39,8),(143,127,12,35,8),(144,128,11,33,8),(145,129,6,19,8),(146,130,10,27,8),(147,131,NULL,NULL,8),(148,132,NULL,NULL,8),(149,133,7,21,8),(150,134,8,21,8),(151,136,4,12,8),(152,137,NULL,NULL,8),(153,138,3,12,8),(154,139,1,9,8),(155,140,2,9,8),(156,141,NULL,NULL,8),(157,143,NULL,NULL,8),(158,144,NULL,NULL,8),(159,145,NULL,NULL,8);

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
  CONSTRAINT `fk_etapa_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_etapa_competitor1` FOREIGN KEY (`competitor_id`) REFERENCES `competitors` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1057 DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

insert  into `stages`(`id`,`position`,`stage`,`status`,`category_id`,`user_id`,`competitor_id`) values (865,1,'selection','active',8,11,139),(866,1,'selection','active',8,11,140),(867,1,'selection','active',8,11,142),(868,1,'selection','active',8,11,143),(869,1,'selection','active',8,11,144),(870,1,'selection','active',8,11,145),(871,1,'selection','active',8,11,149),(872,1,'selection','active',8,11,150),(873,1,'selection','active',8,11,151),(874,1,'selection','active',8,11,153),(875,1,'selection','active',8,11,154),(876,1,'selection','active',8,11,155),(877,1,'selection','active',8,12,140),(878,1,'selection','active',8,12,141),(879,1,'selection','active',8,12,142),(880,1,'selection','active',8,12,143),(881,1,'selection','active',8,12,144),(882,1,'selection','active',8,12,145),(883,1,'selection','active',8,12,146),(884,1,'selection','active',8,12,149),(885,1,'selection','active',8,12,150),(886,1,'selection','active',8,12,151),(887,1,'selection','active',8,12,152),(888,1,'selection','active',8,12,153),(889,1,'selection','active',8,13,141),(890,1,'selection','active',8,13,142),(891,1,'selection','active',8,13,143),(892,1,'selection','active',8,13,144),(893,1,'selection','active',8,13,145),(894,1,'selection','active',8,13,146),(895,1,'selection','active',8,13,147),(896,1,'selection','active',8,13,153),(897,1,'selection','active',8,13,154),(898,1,'selection','active',8,13,155),(899,1,'selection','active',8,13,156),(900,1,'selection','active',8,13,157),(901,1,'classify_1','active',8,13,151),(902,2,'classify_1','active',8,13,155),(903,3,'classify_1','active',8,13,154),(904,4,'classify_1','active',8,13,153),(905,5,'classify_1','active',8,13,140),(906,6,'classify_1','active',8,13,149),(907,7,'classify_1','active',8,13,145),(908,8,'classify_1','active',8,13,150),(909,9,'classify_1','active',8,13,146),(910,10,'classify_1','active',8,13,141),(911,11,'classify_1','active',8,13,144),(912,12,'classify_1','active',8,13,143),(913,13,'classify_1','active',8,13,142),(914,1,'classify_1','active',8,12,141),(915,2,'classify_1','active',8,12,155),(916,3,'classify_1','active',8,12,154),(917,4,'classify_1','active',8,12,153),(918,5,'classify_1','active',8,12,151),(919,6,'classify_1','active',8,12,150),(920,7,'classify_1','active',8,12,149),(921,8,'classify_1','active',8,12,140),(922,9,'classify_1','active',8,12,146),(923,10,'classify_1','active',8,12,145),(924,11,'classify_1','active',8,12,143),(925,12,'classify_1','active',8,12,144),(926,13,'classify_1','active',8,12,142),(1044,1,'classify_1','active',8,11,140),(1045,2,'classify_1','active',8,11,145),(1046,3,'classify_1','active',8,11,154),(1047,4,'classify_1','active',8,11,153),(1048,5,'classify_1','active',8,11,155),(1049,6,'classify_1','active',8,11,151),(1050,7,'classify_1','active',8,11,150),(1051,8,'classify_1','active',8,11,149),(1052,9,'classify_1','active',8,11,146),(1053,10,'classify_1','active',8,11,144),(1054,11,'classify_1','active',8,11,141),(1055,12,'classify_1','active',8,11,143),(1056,13,'classify_1','active',8,11,142);

/*Table structure for table `tournaments` */

DROP TABLE IF EXISTS `tournaments`;

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `date_begin` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `status` enum('inactive','active','deleted') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `tournaments` */

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`status`) values (4,'Concurso 1','2015-09-02','2015-08-06','active');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_UNIQUE` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`) values (9,'Miguel','Pazo Sánchez','mpazo','123',0,'pCMf6xPYMaWdDDksQF35ZTaUqqAJeUGuWlyvVCmDBrWJnlGXAS','admin'),(10,'Comisario','Comisario','comisario','123',0,'7wUkmRyGW381FJKOVFYQlQ9A1NfnQbPLpCm9m8mRq8JBzGon6S','commissar'),(11,'Jurado 1','Jurado 1','jurado1','123',1,'ohOKJR75hBfYJp1Z1qYAbE4tpOgeRgKCQm7yOHuP4TAm8Lk8aL','jury'),(12,'Jurado 2','Jurado 2','jurado2','123',0,'NxVlCwlHvl6DnyZ29xwB1ymPbFWTGuQsi0GDilSJUEhKlDPClF','jury'),(13,'Jurado 3','Jurado 3','jurado3','123',0,'6FJwfqbMryrEccgeSSDMzXDvbEYwB5g1Z1wGkRYYlP6gwNvgXz','jury');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
