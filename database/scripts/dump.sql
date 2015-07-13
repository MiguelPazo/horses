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

/*Table structure for table `animal` */

DROP TABLE IF EXISTS `animal`;

CREATE TABLE `animal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `concurso_id` bigint(20) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `prefijo` varchar(20) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `fec_nac` date DEFAULT NULL,
  `padre` varchar(50) DEFAULT NULL,
  `madre` varchar(50) DEFAULT NULL,
  `criador` varchar(100) DEFAULT NULL,
  `propietario` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_Relationship4` (`concurso_id`),
  CONSTRAINT `concurso_animal` FOREIGN KEY (`concurso_id`) REFERENCES `concurso` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `animal` */

insert  into `animal`(`id`,`concurso_id`,`numero`,`prefijo`,`nombre`,`codigo`,`fec_nac`,`padre`,`madre`,`criador`,`propietario`) values (1,1,580,'RG ','EL GUAPO','CN-02352','0000-00-00','AV ANDALUZ','HH FLOR DE ROCIO','ROCIO GUTIERREZ VILLENA','ROCIO GUTIERREZ VILLENA'),(2,1,581,'KRA ','IMPETUOSO','CN-02508','0000-00-00','JEB IRREVERENTE','AHT LINDA CAMPERA','KARL WILHELM REUSCHE ARAMBULO','KARL WILHELM REUSCHE ARAMBULO'),(3,1,582,'OSP ','ARCANGEL','CN-02538','0000-00-00','AEV SOL REAL','MSP AROMA','ORLANDO SANCHEZ PAREDES','ORLANDO SANCHEZ PAREDES'),(4,1,583,'FyEP ','EMPRESARIO','CN-02381','0000-00-00','FyEP ACIERTO','MEAA APURIMAC','FERNANDO Y ELSA PUGA','FERNANDO Y ELSA PUGA'),(5,1,584,'AZF ','CAMPESINO','CN-02518','0000-00-00','ERM ELEGIDO','AZF CALLECITA','ABRAHAM ZAVALA FALCON','ABRAHAM ZAVALA FALCON'),(6,1,585,'GLR','ARREBATO','CN-02537','0000-00-00','GLR RABICAN','GLR DO?A PEPA','JAVIER A.LA ROSA MUSANTE/CRIADERO DO?A IRMA','JAVIER A.LA ROSA MUSANTE/CRIADERO DO?A IRMA'),(7,1,586,'JVDZ ','GRAN SE?OR I','CN-02534','0000-00-00','JVDZ LAZCANO I','JVDZ CONQUISTA','JORGE VILLACORTA DIAZ / CRIADERO LAZCAN','JORGE VILLACORTA DIAZ / CRIADERO LAZCAN'),(8,1,587,'MSCRL ','ODISEO-TE','CN-02343','0000-00-00','AEV DIGNATARIO','JFRN ALIESKA','CRIADERO LA MAESTRANZA SCRL','CRIADERO LA MAESTRANZA SCRL'),(9,1,588,'SSM','PINTORESCO','CN-02482','0000-00-00','LV EXPRESO','MOH PINTORESCA','SILVIA ISABEL SANCHEZ MIRANDA','SILVIA ISABEL SANCHEZ MIRANDA'),(10,1,589,'ACZ','DOCTOR','CN-02504','0000-00-00','*JAI PRESTIGIO','CLVL ACTRIZ','ARTURO CAVERO ZAVALA','ARTURO CAVERO ZAVALA'),(11,1,590,'GAN','SOLERO','CN-02483','0000-00-00','LB FABULOSO','BC SUSPIRO','GONZALO ANDRADE NICOLI','GONZALO ANDRADE NICOLI'),(12,1,591,'FESA ','CANCILLER-TE','CN-02479','0000-00-00','LV ACTOR','FESA EMBAJADORA-TE','FIDEL ERNESTO SANCHEZ ALAYO','FIDEL ERNESTO SANCHEZ ALAYO'),(13,1,592,'MRA','MALAMBITO','CN-02365','0000-00-00','ERM JUSTICIERO','MRA MALASIA','MIGUEL ARBULU ALVA','ARMANDO LUZA SALAZAR'),(14,1,593,'AZ ','SO?ADOR-TE','CN-02447','0000-00-00','CMV REHEN','PK SO?ADA','ALBERTO DE AZAMBUJA PASARA','ALBERTO DE AZAMBUJA PASARA'),(15,1,594,'JMLL ','BENDITO','CN-02532','0000-00-00','CTR MOSQUETERO','JMLL JUSTICIA','JUAN MANUEL LLANOS RODRIGUEZ','JUAN MANUEL LLANOS RODRIGUEZ'),(16,1,595,'MRA ','BUJAMERO','CN-02503','0000-00-00','ERM JUSTICIERO','MRA TORMENTA','MIGUEL ARBULU ALVA','CESAR BENITES MENDOZA'),(17,1,596,'PZ ','EJECUTOR','CN-02496','0000-00-00','AV EJECUTIVO','PZ ABA PEDRO ','ZAMBRANO CHAVARRI','PEDRO ZAMBRANO CHAVARRI');

/*Table structure for table `categoria` */

DROP TABLE IF EXISTS `categoria`;

CREATE TABLE `categoria` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `concurso_id` bigint(20) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `seleccion` varchar(1) DEFAULT NULL,
  `ganadores` int(11) DEFAULT NULL,
  `destino` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_Relationship1` (`concurso_id`),
  CONSTRAINT `concurso_categoria` FOREIGN KEY (`concurso_id`) REFERENCES `concurso` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `categoria` */

insert  into `categoria`(`id`,`concurso_id`,`nombre`,`seleccion`,`ganadores`,`destino`) values (1,1,'CATEGORIA DE CAPONES DE BOZAL DE 3 A 4 AÑOS','1',1,NULL);

/*Table structure for table `categoria_jurado` */

DROP TABLE IF EXISTS `categoria_jurado`;

CREATE TABLE `categoria_jurado` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `jurado_id` bigint(20) DEFAULT NULL,
  `categoria_id` bigint(20) DEFAULT NULL,
  `dirimente` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_Relationship2` (`jurado_id`),
  KEY `IX_Relationship3` (`categoria_id`),
  CONSTRAINT `categoria_jurado` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jurado_categoria` FOREIGN KEY (`jurado_id`) REFERENCES `jurado` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `categoria_jurado` */

insert  into `categoria_jurado`(`id`,`jurado_id`,`categoria_id`,`dirimente`) values (1,1,1,NULL),(2,2,1,NULL),(3,3,1,'1');

/*Table structure for table `concurso` */

DROP TABLE IF EXISTS `concurso`;

CREATE TABLE `concurso` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) DEFAULT NULL,
  `fecha_ini` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `concurso` */

insert  into `concurso`(`id`,`nombre`,`fecha_ini`,`fecha_fin`,`activo`) values (1,'Primero Concurso de Caballos de Paso XYZ - 2015','2015-07-10','2015-07-31','1');

/*Table structure for table `etapa` */

DROP TABLE IF EXISTS `etapa`;

CREATE TABLE `etapa` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `participante_id` bigint(20) DEFAULT NULL,
  `jurado_id` bigint(20) DEFAULT NULL,
  `posicion` int(11) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `cerrado` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IX_Relationship7` (`participante_id`),
  KEY `IX_Relationship8` (`jurado_id`),
  CONSTRAINT `jurado_seleccion` FOREIGN KEY (`jurado_id`) REFERENCES `jurado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participante_seleccion` FOREIGN KEY (`participante_id`) REFERENCES `participante` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

/*Data for the table `etapa` */

insert  into `etapa`(`id`,`participante_id`,`jurado_id`,`posicion`,`descripcion`,`cerrado`) values (70,1,1,1,'selection',1),(71,2,1,1,'selection',1),(72,3,1,1,'selection',1),(73,4,1,1,'selection',1),(74,5,1,1,'selection',1),(75,6,1,1,'selection',1),(76,7,1,1,'selection',1),(77,8,1,1,'selection',1),(78,9,1,1,'selection',1),(79,10,1,1,'selection',1),(80,11,1,1,'selection',1),(81,12,1,1,'selection',1),(82,1,2,1,'selection',1),(83,2,2,1,'selection',1),(84,3,2,1,'selection',1),(85,4,2,1,'selection',1),(86,5,2,1,'selection',1),(87,6,2,1,'selection',1),(88,7,2,1,'selection',1),(113,1,3,1,'selection',1),(114,2,3,1,'selection',1),(115,3,3,1,'selection',1),(116,4,3,1,'selection',1),(117,5,3,1,'selection',1),(118,6,3,1,'selection',1),(119,7,3,1,'selection',1),(120,8,3,1,'selection',1),(121,12,3,1,'selection',1),(122,13,3,1,'selection',1),(123,14,3,1,'selection',1),(124,15,3,1,'selection',1);

/*Table structure for table `jurado` */

DROP TABLE IF EXISTS `jurado`;

CREATE TABLE `jurado` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(200) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `estado` varchar(1) DEFAULT NULL,
  `remember_token` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `jurado` */

insert  into `jurado`(`id`,`nombres`,`usuario`,`password`,`estado`,`remember_token`) values (1,'Jurado 1','jurado1','123','1','4MS6ZvqQHAoniWLhjQgdDh1GOhQFx3VxD7XrCKJ0P1972KxLog'),(2,'Jurado 2','jurado2','123','1','s4Jn1qwOnHKyza63BUuXIeGmGC7XOFp7jpKSdjss0qgStYKPjl'),(3,'Jurado 3','jurado3','123','1',NULL);

/*Table structure for table `participante` */

DROP TABLE IF EXISTS `participante`;

CREATE TABLE `participante` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `animal_id` bigint(20) DEFAULT NULL,
  `categoria_id` bigint(20) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `eliminado` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_Relationship5` (`animal_id`),
  KEY `IX_Relationship6` (`categoria_id`),
  CONSTRAINT `categoria_participante` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `animal_participante` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `participante` */

insert  into `participante`(`id`,`animal_id`,`categoria_id`,`numero`,`eliminado`) values (1,1,1,1,NULL),(2,2,1,2,NULL),(3,3,1,3,NULL),(4,4,1,4,NULL),(5,5,1,5,NULL),(6,6,1,6,NULL),(7,7,1,7,NULL),(8,8,1,8,NULL),(9,9,1,9,NULL),(10,10,1,10,NULL),(11,11,1,11,NULL),(12,12,1,12,NULL),(13,13,1,13,NULL),(14,14,1,14,NULL),(15,15,1,15,NULL),(16,16,1,16,NULL),(17,17,1,17,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;