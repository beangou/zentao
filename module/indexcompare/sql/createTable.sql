
DROP TABLE ict_completed;

CREATE TABLE `ict_completed` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) DEFAULT NULL,
  `project` MEDIUMINT(8) DEFAULT NULL,
  `closedtasks` INT(10) DEFAULT NULL,
  `alltasks` INT(10) DEFAULT NULL,
  `assignedTo` VARCHAR(30) DEFAULT NULL,
  UNIQUE KEY `myunique` (`product`,`project`, `assignedTo`), 
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8


SELECT * FROM ict_completed;
DELETE FROM ict_completed;


DROP TABLE ict_defect;

SELECT * FROM ict_defect;
DELETE FROM ict_defect;

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
  UNIQUE KEY `account` (`product`, `project`, `developer`),
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;





SELECT * FROM ict_stability;
DELETE FROM ict_stability;
DROP TABLE ict_stability;


CREATE TABLE `ict_stability` (
  `id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `product` MEDIUMINT(8) DEFAULT NULL,
  `project` MEDIUMINT(8) DEFAULT NULL,
  `openedBy` VARCHAR(30) DEFAULT NULL,
  `initstory` INT(10) DEFAULT NULL,
  `addstory` INT(10) DEFAULT NULL,
  `changestory` INT(10) DEFAULT NULL,
  `stability` VARCHAR(10) DEFAULT NULL,
  UNIQUE KEY `account` (`product`, `project`, `openedBy`),
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8

