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
  CONSTRAINT `fk_categories_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*Data for the table `categories` */

insert  into `categories`(`id`,`description`,`type`,`status`,`actual_stage`,`count_competitors`,`tournament_id`) values (22,'Categoria 1','selection','final','classify_2',22,7),(23,'Categoria 2','selection','final','classify_2',23,7),(24,'Categoria 3','selection','active',NULL,20,7);

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

/*Data for the table `category_users` */

insert  into `category_users`(`id`,`dirimente`,`actual_stage`,`user_id`,`category_id`) values (68,1,'classify_2',14,22),(69,0,'classify_2',15,22),(70,0,'classify_2',16,22),(71,1,'classify_2',14,23),(72,0,'classify_2',15,23),(73,0,'classify_2',16,23),(74,1,NULL,14,24),(75,0,NULL,15,24),(76,0,NULL,16,24);

/*Table structure for table `competitors` */

DROP TABLE IF EXISTS `competitors`;

CREATE TABLE `competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` tinyint(4) NOT NULL,
  `position` tinyint(4) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_competitor_categories1_idx` (`category_id`),
  CONSTRAINT `fk_competitor_categories1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;

/*Data for the table `competitors` */

insert  into `competitors`(`id`,`number`,`position`,`category_id`) values (193,1,1,22),(194,2,2,22),(195,3,12,22),(196,4,11,22),(197,5,10,22),(198,6,9,22),(199,7,8,22),(200,8,7,22),(201,9,6,22),(202,10,5,22),(203,11,4,22),(204,12,3,22),(205,13,NULL,22),(206,14,NULL,22),(207,15,NULL,22),(208,16,NULL,22),(209,17,NULL,22),(210,18,NULL,22),(211,19,NULL,22),(212,20,NULL,22),(213,21,NULL,22),(214,22,NULL,22),(215,1,1,23),(216,2,7,23),(217,3,13,23),(218,4,NULL,23),(219,5,12,23),(220,6,11,23),(221,7,9,23),(222,8,10,23),(223,9,8,23),(224,10,6,23),(225,11,5,23),(226,12,2,23),(227,13,NULL,23),(228,14,NULL,23),(229,15,NULL,23),(230,16,NULL,23),(231,17,NULL,23),(232,18,NULL,23),(233,19,NULL,23),(234,20,4,23),(235,21,3,23),(236,22,NULL,23),(237,23,NULL,23);

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
) ENGINE=InnoDB AUTO_INCREMENT=1207 DEFAULT CHARSET=utf8;

/*Data for the table `stages` */

insert  into `stages`(`id`,`position`,`stage`,`status`,`category_id`,`user_id`,`competitor_id`) values (976,1,'selection','final',22,14,193),(977,1,'selection','final',22,14,194),(978,1,'selection','final',22,14,195),(979,1,'selection','final',22,14,197),(980,1,'selection','final',22,14,199),(981,1,'selection','final',22,14,200),(982,1,'selection','final',22,14,201),(983,1,'selection','final',22,14,205),(984,1,'selection','final',22,14,206),(985,1,'selection','final',22,14,207),(986,1,'selection','final',22,14,208),(987,1,'selection','final',22,14,210),(988,1,'selection','final',22,15,193),(989,1,'selection','final',22,15,194),(990,1,'selection','final',22,15,195),(991,1,'selection','final',22,15,196),(992,1,'selection','final',22,15,197),(993,1,'selection','final',22,15,198),(994,1,'selection','final',22,15,199),(995,1,'selection','final',22,15,200),(996,1,'selection','final',22,15,201),(997,1,'selection','final',22,15,202),(998,1,'selection','final',22,15,203),(999,1,'selection','final',22,15,204),(1000,1,'selection','final',22,16,193),(1001,1,'selection','final',22,16,194),(1002,1,'selection','final',22,16,195),(1003,1,'selection','final',22,16,196),(1004,1,'selection','final',22,16,197),(1005,1,'selection','final',22,16,198),(1006,1,'selection','final',22,16,199),(1007,1,'selection','final',22,16,200),(1008,1,'selection','final',22,16,201),(1009,1,'selection','final',22,16,202),(1010,1,'selection','final',22,16,203),(1011,1,'selection','final',22,16,204),(1012,2,'classify_1','final',22,16,193),(1013,3,'classify_1','final',22,16,194),(1014,4,'classify_1','final',22,16,204),(1015,5,'classify_1','final',22,16,203),(1016,6,'classify_1','final',22,16,202),(1017,7,'classify_1','final',22,16,201),(1018,8,'classify_1','final',22,16,200),(1019,9,'classify_1','final',22,16,199),(1020,10,'classify_1','final',22,16,198),(1021,11,'classify_1','final',22,16,196),(1022,12,'classify_1','final',22,16,195),(1023,13,'classify_1','final',22,16,197),(1024,2,'classify_1','final',22,15,193),(1025,3,'classify_1','final',22,15,194),(1026,4,'classify_1','final',22,15,204),(1027,5,'classify_1','final',22,15,202),(1028,6,'classify_1','final',22,15,203),(1029,7,'classify_1','final',22,15,201),(1030,8,'classify_1','final',22,15,200),(1031,9,'classify_1','final',22,15,199),(1032,10,'classify_1','final',22,15,198),(1033,11,'classify_1','final',22,15,197),(1034,12,'classify_1','final',22,15,196),(1035,13,'classify_1','final',22,15,195),(1036,2,'classify_1','final',22,14,193),(1037,3,'classify_1','final',22,14,194),(1038,4,'classify_1','final',22,14,204),(1039,5,'classify_1','final',22,14,203),(1040,6,'classify_1','final',22,14,202),(1041,7,'classify_1','final',22,14,201),(1042,8,'classify_1','final',22,14,200),(1043,9,'classify_1','final',22,14,199),(1044,10,'classify_1','final',22,14,198),(1045,11,'classify_1','final',22,14,197),(1046,12,'classify_1','final',22,14,196),(1047,13,'classify_1','final',22,14,195),(1048,2,'classify_2','final',22,14,193),(1049,3,'classify_2','final',22,14,194),(1050,4,'classify_2','final',22,14,204),(1051,5,'classify_2','final',22,14,203),(1052,6,'classify_2','final',22,14,202),(1053,7,'classify_2','final',22,14,201),(1054,2,'classify_2','final',22,15,193),(1055,3,'classify_2','final',22,15,194),(1056,4,'classify_2','final',22,15,204),(1057,5,'classify_2','final',22,15,203),(1058,6,'classify_2','final',22,15,202),(1059,7,'classify_2','final',22,15,201),(1072,2,'classify_2','final',22,16,194),(1073,3,'classify_2','final',22,16,193),(1074,4,'classify_2','final',22,16,204),(1075,5,'classify_2','final',22,16,203),(1076,6,'classify_2','final',22,16,202),(1077,7,'classify_2','final',22,16,201),(1078,1,'selection','final',23,14,215),(1079,1,'selection','final',23,14,216),(1080,1,'selection','final',23,14,217),(1081,1,'selection','final',23,14,218),(1082,1,'selection','final',23,14,219),(1083,1,'selection','final',23,14,220),(1084,1,'selection','final',23,14,221),(1085,1,'selection','final',23,14,222),(1086,1,'selection','final',23,14,223),(1087,1,'selection','final',23,14,224),(1088,1,'selection','final',23,14,225),(1089,1,'selection','final',23,14,226),(1090,1,'selection','final',23,15,215),(1091,1,'selection','final',23,15,216),(1092,1,'selection','final',23,15,217),(1093,1,'selection','final',23,15,220),(1094,1,'selection','final',23,15,222),(1095,1,'selection','final',23,15,224),(1096,1,'selection','final',23,15,225),(1097,1,'selection','final',23,15,230),(1098,1,'selection','final',23,15,232),(1099,1,'selection','final',23,15,233),(1100,1,'selection','final',23,15,234),(1101,1,'selection','final',23,15,235),(1102,1,'selection','final',23,16,215),(1103,1,'selection','final',23,16,219),(1104,1,'selection','final',23,16,220),(1105,1,'selection','final',23,16,221),(1106,1,'selection','final',23,16,223),(1107,1,'selection','final',23,16,225),(1108,1,'selection','final',23,16,226),(1109,1,'selection','final',23,16,228),(1110,1,'selection','final',23,16,231),(1111,1,'selection','final',23,16,234),(1112,1,'selection','final',23,16,235),(1113,1,'selection','final',23,16,236),(1114,2,'classify_1','final',23,16,215),(1115,3,'classify_1','final',23,16,225),(1116,4,'classify_1','final',23,16,234),(1117,5,'classify_1','final',23,16,235),(1118,6,'classify_1','final',23,16,226),(1119,7,'classify_1','final',23,16,224),(1120,8,'classify_1','final',23,16,216),(1121,9,'classify_1','final',23,16,223),(1122,10,'classify_1','final',23,16,222),(1123,11,'classify_1','final',23,16,221),(1124,12,'classify_1','final',23,16,220),(1125,13,'classify_1','final',23,16,219),(1126,14,'classify_1','final',23,16,217),(1127,2,'classify_1','final',23,15,215),(1128,3,'classify_1','final',23,15,224),(1129,4,'classify_1','final',23,15,235),(1130,5,'classify_1','final',23,15,226),(1131,6,'classify_1','final',23,15,225),(1132,7,'classify_1','final',23,15,234),(1133,8,'classify_1','final',23,15,221),(1134,9,'classify_1','final',23,15,223),(1135,10,'classify_1','final',23,15,216),(1136,11,'classify_1','final',23,15,220),(1137,12,'classify_1','final',23,15,222),(1138,13,'classify_1','final',23,15,219),(1139,14,'classify_1','final',23,15,217),(1140,2,'classify_1','final',23,14,215),(1141,3,'classify_1','final',23,14,216),(1142,4,'classify_1','final',23,14,234),(1143,5,'classify_1','final',23,14,235),(1144,6,'classify_1','final',23,14,226),(1145,7,'classify_1','final',23,14,225),(1146,8,'classify_1','final',23,14,224),(1147,9,'classify_1','final',23,14,223),(1148,10,'classify_1','final',23,14,222),(1149,11,'classify_1','final',23,14,221),(1150,12,'classify_1','final',23,14,220),(1151,13,'classify_1','final',23,14,219),(1152,14,'classify_1','final',23,14,217),(1153,2,'classify_2','final',23,14,215),(1154,3,'classify_2','final',23,14,226),(1155,4,'classify_2','final',23,14,235),(1156,5,'classify_2','final',23,14,234),(1157,6,'classify_2','final',23,14,225),(1158,7,'classify_2','final',23,14,224),(1159,2,'classify_2','final',23,15,235),(1160,3,'classify_2','final',23,15,215),(1161,4,'classify_2','final',23,15,234),(1162,5,'classify_2','final',23,15,226),(1163,6,'classify_2','final',23,15,225),(1164,7,'classify_2','final',23,15,224),(1201,1,'classify_2','final',23,16,226),(1202,2,'classify_2','final',23,16,225),(1203,3,'classify_2','final',23,16,215),(1204,4,'classify_2','final',23,16,235),(1205,5,'classify_2','final',23,16,234),(1206,6,'classify_2','final',23,16,224);

/*Table structure for table `tournaments` */

DROP TABLE IF EXISTS `tournaments`;

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `date_begin` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `status` enum('inactive','active','deleted') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tournaments` */

insert  into `tournaments`(`id`,`description`,`date_begin`,`date_end`,`status`) values (7,'Torneo1','2015-08-12','2015-08-28','active');

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`names`,`lastname`,`user`,`password`,`login`,`remember_token`,`profile`) values (13,'Miguel','Pazo Sánchez','mpazo','123',0,'IETc92Q45E8ylXZIH6PAChj9mPmgkQsAhI3IEflFpeYOnQmbWn','admin'),(14,'jurado1','jurado1','jurado1','123',0,'AStrKJvXuUxGf4tOd9mrAePDBc6S8HjaUiEdbW0JKu8c9IpEh2','jury'),(15,'jurado2','jurado2','jurado2','123',0,'onZL9yF0jSJt3Kt121SsJcm7Fuhy8HYoMe3OHGViZ5WTBY7ejm','jury'),(16,'jurado3','jurado3','jurado3','123',0,'Nf26tvA4ZgnlDFzhFuNVQUtJDAUaHP0kWJXexcF4JfOpDRxrSq','jury'),(17,'operador','operador','operador','123',0,'6YsGvzR7KvZwmA5rDpHbESUchBTaKIA8scrbobanKKTbhBsQv5','operator');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
