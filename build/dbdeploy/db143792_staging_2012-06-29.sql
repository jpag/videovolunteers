# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: external-db.s143792.gridserver.com (MySQL 5.1.55-rel12.6)
# Database: db143792_staging
# Generation Time: 2012-06-29 15:13:10 -0400
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
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

LOCK TABLES `correspondent` WRITE;
/*!40000 ALTER TABLE `correspondent` DISABLE KEYS */;

INSERT INTO `correspondent` (`id`, `fbhandle`, `twitterhandle`, `name`, `address`)
VALUES
	(1,'jpgary','jepiga','Admin','Riverside IL.'),
	(2,'','jemalexander','Jem Alex','Dunno'),
	(3,'','WesPhillips','Wes','USA'),
	(4,'pete.porch','','Peter','Sweden'),
	(5,'','JairamHansda','Jairam Hansda ','Jharkhand'),
	(6,'Sajadrasool','','Sajad Rasool','Jammnu and Kashmir'),
	(7,'','BirendraTirkey','Birendra Tirkey','Jharkhand,Gumla'),
	(8,'','ChunnuHansda','Chunnu Hansda ','Hazaribagh,Jharkhand'),
	(9,'','AmitaRahilTute','Amita Rahil Tute','Khunti,Jharkhand'),
	(10,'','RejanGudia','Rejan Gudia','Khunti, Jharkhand'),
	(11,'','Justinlakra1','Justin Lakra','Latehar,jharkhand'),
	(12,'','MohanBhuiyan','Mohan Bhuiyan','Ramgarh,Jharkhand');

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
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;

INSERT INTO `posts` (`id`, `video_id`, `user_id`, `date`, `friends`)
VALUES
	(1,1,1,'2012-06-11',1011),
	(2,1,10,'2012-06-13',542),
	(3,1,6,'2012-06-13',628),
	(4,1,7,'2012-06-13',263),
	(5,1,8,'2012-06-14',24),
	(6,1,4,'2012-06-15',485),
	(7,1,9,'2012-06-18',11),
	(8,1,14,'2012-06-25',1841),
	(9,1,13,'2012-06-25',464),
	(10,1,15,'2012-06-25',581),
	(11,1,16,'2012-06-26',455),
	(12,1,12,'2012-06-26',346),
	(16,38,1,'2012-06-27',1011),
	(15,38,17,'2012-06-27',866),
	(17,38,6,'2012-06-27',628),
	(18,38,4,'2012-06-27',485),
	(19,38,12,'2012-06-27',346),
	(20,38,7,'2012-06-27',263),
	(21,38,8,'2012-06-27',24),
	(22,38,9,'2012-06-27',11),
	(23,38,10,'2012-06-27',542),
	(24,38,13,'2012-06-27',464),
	(25,4,14,'2012-06-27',1841),
	(26,38,15,'2012-06-27',581),
	(27,38,16,'2012-06-27',455),
	(28,37,1,'2012-06-29',1011),
	(29,37,6,'2012-06-29',628),
	(30,37,8,'2012-06-29',24),
	(31,37,9,'2012-06-29',11),
	(32,37,15,'2012-06-29',581),
	(33,37,12,'2012-06-29',346),
	(34,37,10,'2012-06-29',542),
	(35,37,7,'2012-06-29',263),
	(36,37,4,'2012-06-29',485),
	(37,38,14,'2012-06-29',1841),
	(38,25,16,'2012-06-29',455),
	(39,25,1,'2012-06-29',1011),
	(40,25,6,'2012-06-29',628),
	(41,25,8,'2012-06-29',24),
	(42,25,9,'2012-06-29',11),
	(43,25,15,'2012-06-29',581),
	(44,25,12,'2012-06-29',346),
	(45,25,10,'2012-06-29',542),
	(46,25,7,'2012-06-29',263),
	(47,25,4,'2012-06-29',485),
	(48,25,14,'2012-06-29',1841),
	(49,24,16,'2012-06-29',455),
	(50,25,17,'2012-06-29',866);

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fbid`, `frequency`, `issues_selected`, `totalfriends`, `active`, `access_token`, `access_token_expiration`)
VALUES
	(1,'516040846',1,b'11111',1011,b'1','AAAFtJfAgAc0BAAZCK9B3Ktn0ZA55zViwTPITe7g0Q0nVknH4H6pZCdoZBuVZB1So72EJgjjUeGYBLMClpAZBZCwsyfGSD8asKyM8OEtj4KfOwZDZD','2012-08-20 20:31:50'),
	(6,'64900125',1,b'11111',628,b'1','AAAFtJfAgAc0BAApwth6X6P5N6vZAXAFZAQAeBO8f2OkY6yzL3on5ZBZCpvgfOFszMOUeVM5WHwhDLAJTGaA8bVIkLQ0z3TskcZA232vxYQwZDZD','2012-08-24 15:34:38'),
	(4,'503132',1,b'11111',485,b'1','AAAFtJfAgAc0BAGmN6Nrr69iRMQWeYfMBz5S6CPYxNiLHY4Ua5zUDw0cK7g36tSPd2ZArd8ay2iPERUOkP2XGvl6p3RfYZD','2012-08-24 19:23:55'),
	(12,'679896809',1,b'11111',346,b'1','AAAFtJfAgAc0BANNsC2wZBCZAEL2dIHGD37SRJZBTcynBtjhnqKF608Vz2IbMlTRcO1gB3IQEDAbHgZCiIZBvmZB7AVmbCYc3crT0jFVUSUegZDZD','2012-08-24 15:38:07'),
	(7,'517307056',1,b'11111',263,b'1','AAAFtJfAgAc0BAPYKFF4IiIZCGFiZA6xZBMAuSyibeZBdSvYrRe0NhsfHVqjlocIUZCHNfqFBDKLK8JeBkt1t1gDdYp9LgObcZCuYdJpvGgmgZDZD','2012-08-19 15:19:29'),
	(8,'100001743899134',1,b'11111',24,b'1','AAAFtJfAgAc0BAG9kbaQK64cLvuZBGhsklOIb4rsv6OEbIcZCIz2U9JU9zIk9GvEZCti3vl3gHwqbOicxu79wCOZA8LnEJ9gZB79CEFc5XUwZDZD','2012-08-27 23:07:49'),
	(9,'100002391092556',1,b'11111',11,b'1','AAAFtJfAgAc0BAD3FMylPotHa9JRPlluPM9waPRbaFhTXjePZAuMDWZCNeOPnh3YyJIGeDVLoNm6zk3ypjJALvKkpUJgZASYWOG8CfBd5QZDZD','2012-08-17 16:16:48'),
	(10,'603152945',1,b'01100',542,b'1','AAAFtJfAgAc0BABOxpFLd3KNATQZBsVXroLVxYWeW76U6iSdpFE667VSy0ySO9E4762IMK4iyZBeV0bW8bzeMnsNrhlZCnL0dZBiRkMJiXAZDZD','2012-08-11 21:57:43'),
	(11,'2001807',1,b'11101',546,b'1','0','2012-06-12 22:06:30'),
	(13,'820080674',15,b'11111',464,b'1','AAAFtJfAgAc0BAFNcHQfKXTjjigwWb27Clc7KZBj9YJZCbLCbZCXqBGqXXW0pTWeYw5vgSlWnDH2CAuMnziVocTXEzRM6V0SbZBIZCkFm06AZDZD','2012-08-24 15:54:00'),
	(14,'7714820',1,b'11010',1841,b'1','AAAFtJfAgAc0BABmBOOqYAHZAdlRkDZAS3zBt5060OzJZCMC7U9rZA1VESsrf0c0EQAijVZBEZBLZBPq6lJZCt7Se4qKdnS3YaDYZD','2012-08-28 16:40:49'),
	(15,'22700006',1,b'11111',581,b'1','AAAFtJfAgAc0BAPJZC7YQ90voJOfVeZC1CGTGuRLsXzesOlZAvrlSIhY5IdaEtT0SAZACp4mZCZBx8ZCQHHuIwNHhdin7L9qZBk3ZCoR3ySKrzuQZDZD','2012-08-24 15:56:53'),
	(16,'616735014',1,b'10000',455,b'1','AAAFtJfAgAc0BAH7VYQKshB7JX0FmnQ4xv1ihsxGtHNh0HVRkqwKNwNLuQLc04oooZCeTRz0m5AEX9EBbpPBhwmkRQ1NELcWULl7SQaQZDZD','2012-08-24 20:44:46'),
	(17,'594297960',1,b'11111',866,b'1','AAAFtJfAgAc0BAJm5Ka4Ay9CvAZC3TyUUQdBe9yGMFKZAHGyZByfKZBqopUgoh2ovOb7K2E93jbfE4Vn8hoCkAROLyeG9KZCR5ZB30HUyXrIQZDZD','2012-08-27 17:50:16');

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
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

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
	(8,'sgd0_qDvBE0',1,'UNTOUCHABILITY On The Ground promo',b'11001','2012-05-25'),
	(9,'l1uq2rSqdlw',1,'Corruption in MGNREGA,Jharkhand:100 Days of Labour Goes Unpaid',b'11110','2012-05-24'),
	(10,'5G7OR_exZ7U',1,'Exorcising The Ghosts of Blind Faith',b'11000','2012-05-23'),
	(11,'x31v_EjkbGY',1,'The Case of the Disappearing Anganwadi',b'11001','2012-05-22'),
	(12,'BeaHvdhuqIU',1,'Sansari Puja: Goddess of Sikkim Blesses Doctors Away',b'11001','2012-05-21'),
	(13,'7753L6uxEQU',1,'Dirty Water Makes Children Suffer',b'11111','2012-05-18'),
	(14,'bXAlLhNvHiA',1,'No Hygiene. No Electricity. No Teachers: The Worst School in Mumbai',b'11111','2012-05-17'),
	(15,'wWsZIKN85sA',1,'Jammu and Kashmir Dying for Health',b'11111','2012-05-16'),
	(16,'kDUPNK7Dywc',1,'Organic Farming Prospers in Karauli District',b'01100','2012-05-15'),
	(17,'HKYprKgNDKo',1,'Meter Down: A Journey With A Woman Taxi Driver in Rural Pune',b'00110','2012-05-14'),
	(18,'ucVuHapCVgY',1,'Jharkhand Govt. Has No Funds For Minority School',b'00011','2012-05-11'),
	(19,'aMAeUEDSooc',1,'The Chingari Women\'s Committee Vs. Alcohol',b'00001','2012-05-10'),
	(20,'pPMi4x_1uPA',1,'Corruption Makes The Poor Sleep Hungry',b'01110','2012-05-09'),
	(21,'UTdRksS-mAc',1,'IU Impact:Citizen Journalist Turns Symbol of Women\'s Hope',b'10001','2012-05-08'),
	(22,'b7CNNSDxd_Q',1,'All Work and No Pay for Jharkhand Teachers',b'10100','2012-05-08'),
	(23,'rPPj5NbKTYs',1,'Untouchability in Church | ARTICLE 17',b'00111','2012-05-04'),
	(24,'i6TMbE77gNs',1,'ARTICLE 17: Our First Call to the National Commission of Scheduled Castes',b'10111','2012-05-03'),
	(25,'zTKN4NjRDkg',1,'Untouchability in Housing | ARTICLE 17',b'11110','2012-04-17'),
	(26,'uzlqjA6UyWQ',0,'Untouchability in Pots | ARTICLE 17',b'00000','2012-04-13'),
	(27,'ni-8NsUgDyA',0,'Jharkhand PDS Cheats Empty Stomachs',b'00000','2012-06-15'),
	(28,'Uapfl6nLKCk',0,'Annus Horribilis: Nagaland\'s Year of the Entrepreneur',b'00000','2012-06-14'),
	(29,'7WwN2Cv-Wec',0,'Uttarakhand Village Gets Little Relief for Big Losses',b'00000','2012-06-18'),
	(30,'DLFocMMbMDA',0,'India\'s Wandering Tribe Loses Its Way',b'00000','2012-06-20'),
	(31,'s0Vct6hg0Fs',0,'IU Impact: Citizen Journalist Brings Change to Derelict Graveyard',b'00000','2012-06-19'),
	(32,'zsCyDSN9rFI',0,'Chhattisgarh Village Fights and Wins Right to Land',b'00000','2012-06-21'),
	(33,'Iowxs7sKzzk',0,'Teachers in Jharkhand Breathe a Sigh of Relief',b'00000','2012-06-23'),
	(34,'GYTvHZBcg3c',0,'The Dust Damned Villages of Jharkhand',b'00000','2012-06-22'),
	(35,'RM_jzq-qeOk',0,'Jharkhand Tribal Go Missing on PDS List',b'00000','2012-06-25'),
	(36,'1gtC0RhPydg',0,'Wham Bam! Traffic Jam!',b'00000','2012-06-26'),
	(37,'ucSk4VOrJPM',4,'The Sorrow of the Circus',b'00100','2012-06-27'),
	(38,'LNZC-1tRREs',8,'Teachers in Jharkhand Breathe a Sigh of Relief',b'10100','2012-06-29'),
	(39,'CX3vgHXb1uA',0,'Corruption in Rural Banking: Don\'t Say \'Bribe\', Say \'Commission\'',b'00000','2012-06-28');

/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
