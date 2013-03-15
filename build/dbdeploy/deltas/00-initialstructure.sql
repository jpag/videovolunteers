# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: local.local (MySQL 5.1.44)
# Database: vv
# Generation Time: 2012-03-29 15:30:25 -0400
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

CREATE TABLE `correspondent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fbhandle` varchar(255) DEFAULT NULL,
  `twitterhandle` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table posts
# ------------------------------------------------------------

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `friends` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique-post-per-user-video` (`video_id`,`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;

INSERT INTO `posts` (`id`, `video_id`, `user_id`, `date`, `friends`)
VALUES
	(1,1,1,'2012-03-25',20),
	(2,1,2,'2012-03-25',200),
	(3,1,3,'2012-03-25',13),
	(4,2,1,'2012-03-21',350),
	(5,2,3,'2012-03-21',436),
	(6,3,1,'2012-03-22',239),
	(7,3,2,'2012-03-22',1000),
	(8,4,3,'2012-03-16',3226),
	(9,4,4,'2012-03-23',352),
	(10,4,5,'2012-03-24',167);

/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fbid` varchar(100) NOT NULL DEFAULT '',
  `frequency` float DEFAULT NULL COMMENT 'Frequency of posts - HOW MANY DAYS BETWEEN POSTS',
  `issues_selected` bit(5) DEFAULT NULL COMMENT 'An ordered sequece to save the selection of issues for the user (Education, Corruption,Justic,Woman''sRights, Environment)',
  `totalfriends` int(4) DEFAULT '0' COMMENT 'Total of Number of Friends user has',
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fbid`, `frequency`, `issues_selected`, `totalfriends`, `active`)
VALUES
	(1,'64900125',1,b'00110',591,b'1'),
	(2,'1',30,NULL,0,b'1'),
	(3,'1',10,NULL,0,b'1'),
	(4,'1',6,NULL,0,b'1'),
	(9,'1',15,NULL,0,b'1'),
	(5,'1',2,NULL,0,b'1'),
	(6,'1',6,NULL,0,b'1'),
	(7,'1',3,NULL,0,b'1'),
	(8,'1',15,NULL,0,b'1');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table videos
# ------------------------------------------------------------

CREATE TABLE `videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `youtube_id` varchar(50) DEFAULT NULL,
  `correspondent_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `issues` bit(5) DEFAULT NULL COMMENT 'Bitfield - matches issues in user table',
  `posted_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posted` (`posted_date`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;

INSERT INTO `videos` (`id`, `youtube_id`, `correspondent_id`, `title`, `issues`, `posted_date`)
VALUES
	(1,'aaaa',NULL,NULL,NULL,'2012-03-20'),
	(2,'bbbb',NULL,NULL,NULL,'2012-03-21'),
	(3,'dddd',NULL,NULL,NULL,'2012-03-22'),
	(4,'jaypee',NULL,NULL,NULL,'2012-03-23');

/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
