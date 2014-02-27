
CREATE TABLE `zentao`.`test_unique`(  
  `firstname` VARBINARY(30),
  `lastname` VARCHAR(30),
  UNIQUE KEY `myunique` (`firstname`,`lastname`) 
);

SELECT * FROM test_unique;

INSERT INTO test_unique(firstname, lastname) VALUES('aa', 'ee'), ('cc', 'dd');

INSERT INTO test_unique VALUES('aa', 'bb');
INSERT INTO test_unique VALUES('aa', 'bb');
INSERT INTO test_unique VALUES('cc', 'dd');
INSERT INTO test_unique VALUES('gg', 'bb');