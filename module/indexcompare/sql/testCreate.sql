USE `zentao`;

/*Table structure for table `ict_completed` */

DROP TABLE IF EXISTS `ict_completed`;

CREATE TABLE `ict_completed` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) DEFAULT NULL,
  `project` MEDIUMINT(8) DEFAULT NULL,
  `closedtasks` INT(10) DEFAULT NULL,
  `alltasks` INT(10) DEFAULT NULL,
  `assignedTo` VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `myunique` (`product`,`project`,`assignedTo`)
) ENGINE=MYISAM AUTO_INCREMENT=5154 DEFAULT CHARSET=utf8;

/*Table structure for table `ict_defect` */

DROP TABLE IF EXISTS `ict_defect`;

CREATE TABLE `ict_defect` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) DEFAULT NULL,
  `project` MEDIUMINT(8) DEFAULT NULL,
  `devBug` INT(10) DEFAULT NULL,
  `testBug` INT(10) DEFAULT NULL,
  `total` INT(10) DEFAULT NULL,
  `defect` DOUBLE DEFAULT NULL,
  `begin` DATE DEFAULT NULL,
  `end` DATE DEFAULT NULL,
  `company` MEDIUMINT(8) DEFAULT '1',
  `developer` CHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `account` (`product`,`project`,`developer`)
) ENGINE=MYISAM AUTO_INCREMENT=729 DEFAULT CHARSET=utf8;


/*Table structure for table `ict_initstory_endtime` */

DROP TABLE IF EXISTS `ict_initstory_endtime`;

CREATE TABLE `ict_initstory_endtime` (
  `project_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `initstory_endtime` DATE NOT NULL,
  UNIQUE KEY `project_id` (`project_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


/*Table structure for table `ict_stability` */

DROP TABLE IF EXISTS `ict_stability`;

CREATE TABLE `ict_stability` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) DEFAULT NULL,
  `project` MEDIUMINT(8) DEFAULT NULL,
  `openedBy` VARCHAR(30) DEFAULT NULL,
  `initstory` INT(10) DEFAULT NULL,
  `addstory` INT(10) DEFAULT NULL,
  `changestory` INT(10) DEFAULT NULL,
  `stability` VARCHAR(10) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `account` (`product`,`project`,`openedBy`)
) ENGINE=MYISAM AUTO_INCREMENT=300 DEFAULT CHARSET=utf8;


SELECT * FROM ict_stability;

SELECT * FROM ict_defect;
