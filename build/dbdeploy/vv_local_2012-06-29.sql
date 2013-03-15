# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: local.local (MySQL 5.1.44)
# Database: vv
# Generation Time: 2012-06-29 12:24:07 -0400
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
	(32,'Fancycat','schneidertobias','Tobias Schneider',''),
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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;

INSERT INTO `posts` (`id`, `video_id`, `user_id`, `date`, `friends`)
VALUES
	(1,24,1,'2012-06-11',591),
	(2,24,16,'2012-06-11',24),
	(3,1,1,'2012-04-11',591),
	(4,22,1,'2012-06-28',591),
	(5,24,18,'2012-06-28',24),
	(6,25,1,'2012-06-28',591),
	(7,21,1,'2012-06-28',591),
	(8,20,1,'2012-06-28',591),
	(9,19,1,'2012-06-28',591),
	(10,23,1,'2012-06-28',591),
	(11,95,1,'2012-06-28',591),
	(19,25,18,'2012-06-29',24),
	(18,89,1,'2012-06-28',591),
	(17,90,1,'2012-06-28',591),
	(16,93,1,'2012-06-28',591);

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fbid`, `frequency`, `issues_selected`, `totalfriends`, `active`, `access_token`, `access_token_expiration`)
VALUES
	(1,'64900125',30,b'10101',591,b'1','AAAEZClYif3qEBAKbbKpAOnIW5UHy7OrAT9gC5Iy5bNC3XKskU68dhrey7bqlHUgJ17srKsi9BRDmO6T0emXT8F6HZAlOlwRrKYZBBOZCQwZDZD','2012-08-27 22:39:40'),
	(2,'1107594382',30,b'00110',1025,b'1',NULL,NULL),
	(3,'1109649779',30,b'00001',756,b'1',NULL,NULL),
	(4,'1069145404',30,b'00010',157,b'1',NULL,NULL),
	(9,'169600046',30,b'00100',250,b'1',NULL,NULL),
	(5,'508885295',30,b'01000',300,b'1',NULL,NULL),
	(6,'500457979',30,b'10000',350,b'1',NULL,NULL),
	(7,'500043161',3,b'11111',458,b'1',NULL,NULL),
	(8,'500022770',15,b'10001',350,b'1',NULL,NULL),
	(10,'169600013',3,b'11110',200,b'1',NULL,NULL),
	(11,'64901577',30,b'11111',15,b'1',NULL,NULL),
	(12,'64901212',30,b'11111',200,b'1',NULL,NULL),
	(13,'64901127',30,b'11111',500,b'1',NULL,NULL),
	(14,'100002592880536',30,b'00110',315,b'1',NULL,NULL),
	(15,'1404965892',30,b'11111',250,b'1',NULL,NULL),
	(18,'100001743899134',15,b'11111',24,b'1','AAAEZClYif3qEBAHeTNGO37psxJFJUFXh7g7jUyjxZCTrJ9mEO4kVAZBYhjr3jL4RNJSdCe5QsPepuQaoRIz93ZB2SR2cfKhai9deBrUcXgZDZD','2012-08-28 16:20:44');

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
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=latin1;

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
	(29,'bx_LWm6_6tA',22,'Crisis Of Credit',b'11000','2011-08-08'),
	(129,'Soj9v3nDbI8',0,'Syria Massacre: UN Finds Bloody Scene',b'00000','2012-06-09'),
	(127,'2SssljwlaWU',0,'The Big Lie: The Cancer Bride',b'00000','2012-06-09'),
	(128,'Cd_GvptRLd0',0,'\'America\'s Got Talent\' Singer Accused of Lying',b'00000','2012-06-09'),
	(126,'qweqMXDNpTE',0,'Mysterious Yoga Retreat Death in Ariz.',b'00000','2012-06-09'),
	(125,'2NcenTjxWgw',0,'Reality Contestant Accused of Lying',b'00000','2012-06-09'),
	(123,'vz8HUx4Wwx4',0,'ASPCA: HOVERCAT',b'00000','2012-06-09'),
	(124,'vpAGUWUuhKc',0,'Giant Worm: Iceland\'s Loch Ness Monster?',b'00000','2012-06-09'),
	(122,'KshkG9X9EwY',0,'Anthony Sanchez, California Official, Caught On Camera Allegedly Beating Stepson',b'00000','2012-06-09'),
	(121,'pYCB2atuIxI',0,'NPR\'s \'Car Talk\' Radio Show With Hosts \'Click and Clack\' To End',b'00000','2012-06-09'),
	(120,'obakDwGKBIQ',0,'Spain\'s $125 Billion Bailout Package By European Union',b'00000','2012-06-10'),
	(119,'srrKX84LEf0',0,'David Axelrod Says Classified National Security Leaks Not From White House: Interview (2012)',b'00000','2012-06-10'),
	(118,'HcSv7pxTAEQ',0,'Mike Huckabee on His VP Chances: \'More Likely I\'ll Be Asked By Madonna To Go on Tour\'',b'00000','2012-06-10'),
	(117,'wwQIglq0IGs',0,'Auburn University Shooting in Montgomery Alabama',b'00000','2012-06-11'),
	(116,'OEyfgiGQkzA',0,'Class of 2012: 97 Year-old Graduate Ann Colagiovanni',b'00000','2012-06-11'),
	(114,'eEp50_QS5dk',0,'FACEBOOK WEBCAST 6 11 12',b'00000','2012-06-11'),
	(115,'tpeDuO2CLIk',0,'Pastor Creflo Dollar Preaches After Arrest For Allegedly Punching His Daughter',b'00000','2012-06-11'),
	(113,'sfKRKFeUF28',0,'Lady Gaga Hit In Head With Pole, Suffers Concussion During Concert: Caught on Tape',b'00000','2012-06-11'),
	(112,'QmVUGHBortA',0,'Bristol Palin Confronts Man Criticizing Mom Sarah Palin on Reality Show, Discusses in Interview',b'00000','2012-06-11'),
	(111,'kVbchWeM1tA',0,'Paris Jackson Oprah Winfrey Interview: \'People Try to Cyber-Bully Me\'',b'00000','2012-06-11'),
	(110,'ly0i3aQinCo',0,'UK Prime Minister Forgets Young Daughter in Pub: David Cameron Realizes 2 Miles Down Road',b'00000','2012-06-11'),
	(109,'Aopd7aXTQ2g',0,'\'Good Morning America\' Host Robin Roberts on MDS Diagnosis: \'I\'m Going to Beat This\'',b'00000','2012-06-11'),
	(108,'ICpqEtacuCU',0,'Snooki and Jionni Interview (2012): \'Jersey Shore\' Star On Breast-Feeding, Drinking, High Heels',b'00000','2012-06-11'),
	(107,'j1QHDGh4yy8',0,'Bike Stunt Gone Wrong: Caught on Tape',b'00000','2012-06-11'),
	(105,'5LADSHJjL8M',0,'White House Now Up for Grabs?',b'00000','2012-06-05'),
	(106,'XGAvyn13nPg',0,'Tomato Festival\'s Massive Food Fight: Caught on Tape',b'00000','2012-06-11'),
	(104,'WDBYXuCxkPM',0,'Trainer Regains Fit Body After Weight-Gain Experiment: Fit2Fat and Back: A Weight-Gain Journey',b'00000','2012-06-05'),
	(103,'VgFgeRMvthg',0,'WNN Webcast for Tuesday, June 5, 2012',b'00000','2012-06-05'),
	(102,'RSXITrRSPxA',29,'Man Applies Sunscreen, Catches on Fire: BBQ Grill-Sunscreen Combo to Blame for Serious Injuries?',b'11010','2012-06-05'),
	(101,'Y-cMO7GbZbM',0,'Bill O\'Reilly\'s Advice for Mitt Romney: \'Get Out of the Way\'',b'00000','2012-06-05'),
	(100,'o5YkdOGhvaE',0,'\'Bath Salts\' Drug Leads to Unspeakable Crimes, Violence and Zombie Apocalypse Rumors',b'00000','2012-06-05'),
	(99,'q-8EOcKLAL0',0,'Woman Wrestles Away Boy\'s Gift from Donald Driver, Caught on Tape',b'00000','2012-06-05'),
	(98,'bsYLelmN6BA',5,'1968: Robert F. Kennedy Assassinated',b'00101','2012-06-05'),
	(71,'9EFNHheZY7M',0,'Wisconsin Recall: Governor Scott Walker Wins Election, Governor Now a Rising GOP Star',b'00000','2012-06-06'),
	(72,'7SolwKGDibQ',0,'Donald Trump Says Miss USA Contestant Sheena Monnin Has \'Loser\'s Remorse\' After \'Rigged\' Allegations',b'00000','2012-06-06'),
	(73,'aWSZFaQh7Lo',0,'Miley Cyrus Engaged to Liam Hemsworth: \'Hunger Games\' Actor Proposes to Former \'Hanna Montana\' Star',b'00000','2012-06-06'),
	(74,'A3PzubM26z4',0,'Sheryl Crow Brain Tumor: Singer Learns of Benign Mass in Brain After Asking for MRI to Check Memory',b'00000','2012-06-06'),
	(75,'_djM_7W6oSc',0,'Vaccine Effectiveness at Risk From Improper Storage, Expired Doses Found by Recent Investigation',b'00000','2012-06-06'),
	(76,'F2KoApqMhYk',0,'WNN Webcast for Wednesday, June 6, 2012',b'00000','2012-06-06'),
	(77,'0rtiR2j43gg',5,'Shrinks to the Stars',b'00000','2012-06-06'),
	(96,'88cYpIW87Ck',0,'Aspirin Risk Greater Than Previously Thought',b'00000','2012-06-06'),
	(95,'x83GQGUAPE0',19,'Michelle Obama, Disney on Cutting Junk Food Ads',b'11010','2012-06-06'),
	(94,'jO3kQqKdGNg',0,'Dogs Causing Car Accidents? Dog Crash Dummies Video',b'00000','2012-06-06'),
	(93,'_PU1DUVeA1k',28,'Wisconsin Recall: Tea Party vs. Union Workers',b'10011','2012-06-06'),
	(92,'Dv4gjdlL0vk',0,'Mammoth Bones Discovered Behind House in Iowa',b'00000','2012-06-06'),
	(91,'fPPyim4t9jo',0,'Al Qaeda No. 2 Terrorist Killed by US Drone',b'00000','2012-06-06'),
	(90,'tsQpRxsyYhw',28,'Queen Elizabeth\'s Diamond Jubilee Speech',b'10000','2012-06-06'),
	(89,'IZQKUxOzt64',8,'Vaccines Stored in Dangerous Conditions',b'01010','2012-06-06'),
	(88,'T0nfKiXi7w4',0,'The Real Downton Abbey',b'00000','2012-06-06'),
	(97,'f5VtRZKFQUw',0,'Jerry Sandusky Trial: Jury Selection',b'00000','2012-06-06'),
	(130,'iZQQsibQ138',0,'Tornadoes, Thunderstorms Strike: Caught on Tape',b'00000','2012-06-09');

/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
