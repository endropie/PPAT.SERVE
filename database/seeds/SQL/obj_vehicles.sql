/*
SQLyog Community
MySQL - 5.7.26-log : Database - ppa
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `vehicles` */

insert  into `vehicles`(`id`,`number`,`type`,`owner`,`department_id`,`description`,`created_at`,`updated_at`) values 
(1,'B 9584 FC','DELIVERY','PPA',4,'Build Up/Fuso',NULL,NULL),
(2,'B 9159 IH','DELIVERY','PPA',4,'Build Up/Fuso',NULL,NULL),
(3,'B 9496 CS','DELIVERY','PPA',4,'Build Up/Fuso',NULL,NULL),
(4,'B 9261 CP','DELIVERY','PPA',4,'Pick Up',NULL,NULL),
(5,'B 9593 EU','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(6,'B 9736 CR','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(7,'B 9405 QO','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(8,'B 9110 QB','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(9,'B 9830 BDF','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(10,'B 9334 GB','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(11,'B 9756 LC','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(12,'B 1584 CFJ','OFFICE',NULL,2,NULL,NULL,NULL),
(13,'B 1457 BRY','OFFICE',NULL,4,NULL,NULL,NULL),
(14,'B 1863 BIO','OFFICE',NULL,4,NULL,NULL,NULL),
(15,'B 1517 KJF','OFFICE',NULL,4,NULL,NULL,NULL),
(16,'B 8019 QL','OFFICE',NULL,4,NULL,NULL,NULL),
(17,'B 1949 BIO','OFFICE',NULL,2,NULL,NULL,NULL),
(18,'B 2164 TOI','OFFICE',NULL,5,NULL,NULL,NULL),
(19,'FORKLIFT','OTHERS',NULL,7,NULL,NULL,NULL),
(20,'SOLAR PRODUKSI','OTHERS',NULL,7,NULL,NULL,NULL),
(21,'B 9836 ZB','DELIVERY','PPA',4,'BOX TRUCK',NULL,NULL),
(22,'T 9052 DB','DELIVERY','RENTAL',4,'Build Up/Fuso',NULL,NULL),
(23,'B 9776 ECB','DELIVERY','RENTAL',4,'BOX TRUCK',NULL,NULL),
(24,'B 1712 SGP','OFFICE',NULL,3,NULL,NULL,NULL),
(25,'B 1871 CJD','OFFICE',NULL,3,NULL,NULL,NULL),
(26,'B 1552 FRE','OFFICE',NULL,6,NULL,NULL,NULL),
(27,'T 9402 DB','DELIVERY','RENTAL',4,'BOX TRUCK',NULL,NULL),
(28,'B 1985 COZ','OFFICE',NULL,1,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
