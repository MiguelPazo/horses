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

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`num_begin`,`count_competitors`,`tournament_id`) values (1,'Potros','selection','inactive',NULL,1,25,1),(2,'Caballos 14','wselection','inactive',NULL,45,15,1);

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

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (4,0,NULL,3,2),(5,0,NULL,4,2),(6,1,NULL,5,2),(7,0,NULL,3,1),(8,1,NULL,4,1),(9,0,NULL,5,1);

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

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`type`,`status`) values (1,'Concurso Mamacona','2015-10-24','2015-10-24','jury','inactive');

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

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`,`status`) values (1,'Miguel','Pazo Sánchez','admin','$2y$10$xrV4QL2s/fe4UT/jQ2VSV.f9dk0KDa5p6Bb7YgR29tXXqSBMCeCcy',0,'us1jeMfcH7oNCTizXcbbLmrtvt7HzKg6THG0qZNCZT9czeYsTN','admin','active'),(2,'Jose','Apaza','comisario','$2y$10$ukDXWYTxkNJkqRJM3wgAAuMJC2ZclB/gMgFMk5fpg52lH960oYuWS',0,NULL,'commissar','active'),(3,'Roberto','Martinez','jurado1','$2y$10$rf0p191OeTzV5l5N8BlWeu70JYu717i/EeI9dcGJaUD6G5M9g7fRS',0,NULL,'jury','active'),(4,'Jose','Torres','jurado3','$2y$10$Vp74PIX5fjLvbYhIVNb71utgYhVENMutMAeUGQjh2rUEjJi/S5Ys2',0,NULL,'jury','active'),(5,'Alberto','Barros','jurado2','$2y$10$urS/XnNV1RrTr/Jfnf17KOOCJfZ3c7v.rbw9CbL8cdn/aFJFcXQ6q',0,NULL,'jury','active');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
