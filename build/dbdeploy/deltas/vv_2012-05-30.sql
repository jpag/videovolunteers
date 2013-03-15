# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: local.local (MySQL 5.1.44)
# Database: vv
# Generation Time: 2012-05-30 11:45:11 -0400
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table changelog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `changelog`;

CREATE TABLE `changelog` (
  `change_number` bigint(20) NOT NULL,
  `delta_set` varchar(10) NOT NULL,
  `start_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `complete_dt` timestamp NULL DEFAULT NULL,
  `applied_by` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`change_number`,`delta_set`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table correspondent
# ------------------------------------------------------------

DROP TABLE IF EXISTS `correspondent`;

CREATE TABLE `correspondent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fbhandle` varchar(255) DEFAULT NULL,
  `twitterhandle` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

LOCK TABLES `correspondent` WRITE;
/*!40000 ALTER TABLE `correspondent` DISABLE KEYS */;

INSERT INTO `correspondent` (`id`, `fbhandle`, `twitterhandle`, `name`, `address`)
VALUES
	(5,'18902949','jill_e_23','Akiko Rokube',NULL),
	(30,'18369','briangossett','Carrie Bradshaw','Boston MA'),
	(7,'22003403','kelstoclose','Kelly Close','Chicago IL.'),
	(8,NULL,'schneidertobias','To Schr',NULL),
	(9,'911','seth_weisfeld','Bob Greenberg','Venice Beach'),
	(31,'20305888',NULL,'Lamont Bongo','Chicago IL.'),
	(11,NULL,'schneidertobias','Beyonce West',NULL),
	(13,'20305888',NULL,'Ellyse Lamont Watson Zinger','Chicago IL.'),
	(29,'fbid','twid','Amanda Peet',''),
	(15,'117010','partypooper','Ali Salem',''),
	(16,'18902949','jill_e_23','Romulan Prize',NULL),
	(18,'18369','briangossett','Brandy Bob','Boston MA'),
	(19,'28240','guiseiz','Abigail Wright','New York NY'),
	(20,NULL,'seth_weisfeld','Seth McMaster','Brooklyn Ny'),
	(21,'tobitobs','schneidertobias','Harry Frank',''),
	(28,'joelsoul','soulyjo','Johnny Appleseed','california'),
	(22,'bobsuth','','Bob Sutherland','Turkey Island'),
	(23,'deano','beanodean','James Dean','LA Cali.'),
	(24,'fbid','twid','Kayne East',''),
	(25,'mcgravy','mcgravygogo','Jacob','lomant'),
	(26,'joelsoul','soulyjo','Billy Joel','california'),
	(32,NULL,'schneidertobias','Tobias Schneider',NULL),
	(33,'18369','briangossett','Ryan Schilling','Boston MA'),
	(34,'18369','briangossett','Zach Warren','Boston MA'),
	(35,NULL,'schneidertobias','Paul Frank',NULL),
	(36,NULL,'schneidertobias','Kate Spade',NULL),
	(37,'18902949','jill_e_23','Diane Firstenburg',NULL),
	(38,'allyourbase','bobdole','Boby Murptatty','westchester');

/*!40000 ALTER TABLE `correspondent` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `friends` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique-post-per-user-video` (`video_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;

INSERT INTO `posts` (`id`, `video_id`, `user_id`, `date`, `friends`)
VALUES
	(1,4,1,'2012-05-22',20),
	(2,1,2,'2012-03-25',200),
	(3,1,3,'2012-03-25',13),
	(4,2,1,'2012-03-21',350),
	(5,2,3,'2012-03-21',436),
	(6,3,1,'2012-03-22',239),
	(7,3,2,'2012-03-22',1000),
	(8,4,3,'2012-03-16',3226),
	(9,4,4,'2012-03-23',352),
	(10,4,5,'2012-03-24',167),
	(11,5,6,'2012-03-27',210),
	(12,2,7,'2012-03-27',2),
	(13,1,15,'2012-03-27',543),
	(14,5,14,'2012-03-27',240),
	(15,4,13,'2012-03-27',25),
	(16,3,12,'2012-03-27',1250),
	(17,4,11,'2012-03-27',1500),
	(18,3,10,'2012-03-27',150),
	(19,1,12,'2012-03-27',25),
	(20,3,9,'2012-03-27',987),
	(21,2,13,'2012-03-20',10),
	(22,1,4,'2010-02-01',11),
	(23,1,5,'2010-02-01',1),
	(24,1,6,'2010-02-01',1),
	(25,1,7,'2010-02-01',3),
	(26,1,8,'2010-02-01',5),
	(27,1,9,'2010-02-01',1),
	(28,1,10,'2010-02-01',1),
	(29,1,11,'2010-02-01',1),
	(30,1,13,'2010-02-01',1),
	(31,1,14,'2010-02-01',1),
	(32,1,1,'2012-05-29',591);

/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fbid` varchar(100) NOT NULL DEFAULT '',
  `frequency` float DEFAULT NULL COMMENT 'Frequency of posts - HOW MANY DAYS BETWEEN POSTS',
  `issues_selected` bit(5) DEFAULT NULL COMMENT 'An ordered sequece to save the selection of issues for the user (Education, Corruption,Justic,Woman''sRights, Environment)',
  `totalfriends` int(4) DEFAULT '0' COMMENT 'Total of Number of Friends user has',
  `active` bit(1) NOT NULL DEFAULT b'1',
  `access_token` varchar(255) DEFAULT NULL COMMENT 'Store an extended access token to manage auto posting when the user is not logged in.',
  `access_token_expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fbid`, `frequency`, `issues_selected`, `totalfriends`, `active`, `access_token`, `access_token_expiration`)
VALUES
	(1,'64900125',18,b'01011',591,b'1','AAAEZClYif3qEBADbu8WxKoEeJc7RrcsxCEg8j2AsBZBD1b945wpA4BiemWvZCQCCtkrL8zmYyM1r4ZApQp2nnJlhX3Ql0xFnUFtiQoNLFgZDZD','2012-07-29 16:26:33'),
	(2,'1107594382',30,b'00110',1025,b'1',NULL,NULL),
	(3,'1109649779',30,b'11111',756,b'1',NULL,NULL),
	(4,'1069145404',6,b'11110',157,b'1',NULL,NULL),
	(9,'169600046',15,b'10011',250,b'1',NULL,NULL),
	(5,'508885295',2,b'10101',300,b'1',NULL,NULL),
	(6,'500457979',6,b'01010',350,b'1',NULL,NULL),
	(7,'500043161',3,b'11111',458,b'1',NULL,NULL),
	(8,'500022770',15,b'10001',350,b'1',NULL,NULL),
	(10,'169600013',3,b'11110',200,b'1',NULL,NULL),
	(11,'64901577',30,b'11111',15,b'1',NULL,NULL),
	(12,'64901212',30,b'11111',200,b'1',NULL,NULL),
	(13,'64901127',30,b'11111',500,b'1',NULL,NULL),
	(14,'100002592880536',30,b'00110',315,b'1',NULL,NULL),
	(15,'1404965892',30,b'11111',250,b'1',NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table videos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `videos`;

CREATE TABLE `videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `youtube_id` varchar(50) DEFAULT NULL,
  `correspondent_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issues` bit(5) DEFAULT NULL COMMENT 'Bitfield - matches issues in user table',
  `posted_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posted` (`posted_date`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;

INSERT INTO `videos` (`id`, `youtube_id`, `correspondent_id`, `title`, `issues`, `posted_date`)
VALUES
	(1,'YnBvfVJu8-U',31,'Bow Shot',b'11111','2012-03-20'),
	(2,'QrgTtZXuj4w',38,'Giant Vortex Cannon',b'10000','2012-03-21'),
	(3,'fLREkg4Cufk',21,'Fast Hands',b'10100','2012-03-22'),
	(4,'3zoTKXXNQIU',22,'Non Newtonian Fluid',b'11110','2012-03-23'),
	(5,'aMS0O3kknvk',5,'Grape Fall',b'11001','2012-03-24'),
	(21,'jRMHie8dj9Y',21,'Minko - iPad/iPhone 3D Data Visualization',b'11101','2015-10-11'),
	(20,'U5k25-hHNrc',36,'The Copenhagen Wheel - Data Visualization',b'10000','2015-10-01'),
	(19,'RgA4aaEfgPQ',38,'Google\'s Public Data Explorer',b'10000','1111-01-11'),
	(18,'cd2-vsTzd9E',26,'Automatic Bicycle Transmission (IVT)',b'11111','1920-01-01'),
	(17,'Jfkyp89LLTE',30,'Boeing KLM',b'11111','1920-01-01'),
	(16,'zUyR0eezwns',32,'RC c-17 globemaster',b'11111','1920-05-02'),
	(15,'Fe751kMBwms',29,'Yes We Can',b'10001','2005-10-12'),
	(22,'pLqjQ55tz-U',35,'David McCandless',b'10000','2041-10-10'),
	(23,'1jqFxZI_2XY',36,'NASA | Aqua MODIS',b'11000','1223-10-12'),
	(24,'7E-0j90Cwpk',38,'NASA afterschool',b'10000','2530-10-01'),
	(25,'OsW8zctD7CM',11,'Liquid Iron',b'11100','2015-10-25'),
	(26,'_d6KuiuteIA',7,'The leap',b'11111','1982-01-01'),
	(27,'3CgKmfInv_k',5,'Ferrofluid',b'10010','1920-01-04'),
	(28,'KqRAnpJu8jQ',28,'Forest Recovering From Mt St Helens',b'10000','2011-10-20'),
	(29,'bx_LWm6_6tA',22,'Crisis Of Credit',b'11000','2011-08-08');

/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
