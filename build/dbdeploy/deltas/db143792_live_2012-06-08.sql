# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: external-db.s143792.gridserver.com (MySQL 5.1.55-rel12.6)
# Database: db143792_live
# Generation Time: 2012-06-08 18:07:18 -0400
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

LOCK TABLES `correspondent` WRITE;
/*!40000 ALTER TABLE `correspondent` DISABLE KEYS */;

INSERT INTO `correspondent` (`id`, `fbhandle`, `twitterhandle`, `name`, `address`)
VALUES
	(1,'jpgary','jepiga','Admin','Riverside IL.'),
	(2,'','jemalexander','Jem Alex','Dunno'),
	(3,'','WesPhillips','Wes','USA'),
	(4,'pete.porch','','Peter','Sweden');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fbid`, `frequency`, `issues_selected`, `totalfriends`, `active`, `access_token`, `access_token_expiration`)
VALUES
	(1,'516040846',30,b'00000',1011,b'1','AAAEddmCtUhUBAAfyrM4aw75xvBvU2sbvQXN5a9jfLVUdGxPPUuCX11oJPVZBkV034eYvZAejE4ZB5aWIkl40ZBQRYfYVQLnMcOqM552NYgZDZD','2012-08-06 21:10:57'),
	(2,'100001743899134',15,b'11111',24,b'1','0','2012-06-08 19:23:13');

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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;

INSERT INTO `videos` (`id`, `youtube_id`, `correspondent_id`, `title`, `issues`, `posted_date`)
VALUES
	(1,'xxQSNHCbfOA',4,'Water for Cities, Woe For Villages',b'01100','2012-06-05'),
	(2,'Z4yyiz8ti1M',1,'IU Impact: Video Brings Hope and Change for Tribal Girl',b'00101','2012-06-01'),
	(3,'WHeI3-Cik1M',3,'The Death of a River',b'01001','2012-05-31'),
	(4,'sUJSu4577w8',2,'Innocent Jharkhand Tribals At the Mercy of A Violent State',b'10110','2012-05-30'),
	(5,'PRL7mknHblA',1,'No Justice for Rape Victim in Rural Jharkhand',b'01111','2012-05-29'),
	(6,'gKPvl-vBV4g',1,'Victims of Maoist Witch-Hunt Detained and Tortured in Prison',b'01101','2012-05-28'),
	(7,'XtnHcUP8u8g',1,'Healthcare Crisis in Rural Malegoan',b'11010','2012-05-25'),
	(8,'sgd0_qDvBE0',0,'UNTOUCHABILITY On The Ground promo',b'00000','2012-05-25'),
	(9,'l1uq2rSqdlw',0,'Corruption in MGNREGA,Jharkhand:100 Days of Labour Goes Unpaid',b'00000','2012-05-24'),
	(10,'5G7OR_exZ7U',0,'Exorcising The Ghosts of Blind Faith',b'00000','2012-05-23'),
	(11,'x31v_EjkbGY',0,'The Case of the Disappearing Anganwadi',b'00000','2012-05-22'),
	(12,'BeaHvdhuqIU',0,'Sansari Puja: Goddess of Sikkim Blesses Doctors Away',b'00000','2012-05-21'),
	(13,'7753L6uxEQU',0,'Dirty Water Makes Children Suffer',b'00000','2012-05-18'),
	(14,'bXAlLhNvHiA',0,'No Hygiene. No Electricity. No Teachers: The Worst School in Mumbai',b'00000','2012-05-17'),
	(15,'wWsZIKN85sA',0,'Jammu and Kashmir Dying for Health',b'00000','2012-05-16'),
	(16,'kDUPNK7Dywc',0,'Organic Farming Prospers in Karauli District',b'00000','2012-05-15'),
	(17,'HKYprKgNDKo',0,'Meter Down: A Journey With A Woman Taxi Driver in Rural Pune',b'00000','2012-05-14'),
	(18,'ucVuHapCVgY',0,'Jharkhand Govt. Has No Funds For Minority School',b'00000','2012-05-11'),
	(19,'aMAeUEDSooc',0,'The Chingari Women\'s Committee Vs. Alcohol',b'00000','2012-05-10'),
	(20,'pPMi4x_1uPA',0,'Corruption Makes The Poor Sleep Hungry',b'00000','2012-05-09'),
	(21,'UTdRksS-mAc',0,'IU Impact:Citizen Journalist Turns Symbol of Women\'s Hope',b'00000','2012-05-08'),
	(22,'b7CNNSDxd_Q',0,'All Work and No Pay for Jharkhand Teachers',b'00000','2012-05-08'),
	(23,'rPPj5NbKTYs',0,'Untouchability in Church | ARTICLE 17',b'00000','2012-05-04'),
	(24,'i6TMbE77gNs',1,'ARTICLE 17: Our First Call to the National Commission of Scheduled Castes',b'10111','2012-05-03'),
	(25,'zTKN4NjRDkg',1,'Untouchability in Housing | ARTICLE 17',b'11110','2012-04-17');

/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
