
--一：需求稳定度
--需求表
SELECT * FROM zt_story;

--产品表
SELECT * FROM zt_product;

--项目表
SELECT * FROM zt_project;

--需求变更信息
SELECT * FROM zt_storyspec;

--项目和需求两表联系
SELECT * FROM zt_projectstory;

--主要记录每一次迭代的原始需求结束时间点
CREATE TABLE `ict_initstory_endtime` (
  `project_id` MEDIUMINT(8) UNSIGNED NOT NULL UNIQUE,   
  `initstory_endtime` DATE NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8

SELECT * FROM ict_initstory_endtime;

--找出没有记录到原始需求结束时间点表的项目
SELECT id FROM zt_project WHERE id NOT IN(SELECT project_id FROM ict_initstory_endtime);



SELECT * FROM ict_initstory_endtime;

DELETE FROM ict_initstory_endtime WHERE project_id=9;

SELECT id FROM zt_project WHERE id NOT IN(SELECT project_id FROM ict_initstory_endtime);

SELECT * FROM ict_initstory_endtime;

--2014/02/17

SELECT * FROM zt_projectproduct;
SELECT * FROM zt_product;
SELECT * FROM zt_project;

SELECT t2.`id`, t4.`name`, t2.`name`, t1.`initstory_endtime` FROM ict_initstory_endtime t1 
LEFT JOIN zt_project t2 ON (t1.`project_id` = t2.`id`)
LEFT JOIN zt_projectproduct t3 ON (t2.`id` = t3.`project`) 
LEFT JOIN zt_product t4 ON (t3.`product` = t4.id);

--新增需求数：initstory_endtime<=openedDate
--修改需求数：initstory_endtime>openedDate && initstory_endtime < lastEditedDate
--原始需求数：所有需求数-新增-修改（或者initstory_endtime>openedDate && lastEditedDate<initstory_endtime）
--详细的需求稳定度查询结果
SELECT t4.`name`, t2.`id`, t2.`name`, t1.`initstory_endtime`, t2.`begin`, t2.`end` FROM ict_initstory_endtime t1 
LEFT JOIN zt_project t2 ON (t1.`project_id` = t2.`id`)
LEFT JOIN zt_projectproduct t3 ON (t2.`id` = t3.`project`) 
LEFT JOIN zt_product t4 ON (t3.`product` = t4.`id`) ORDER BY t4.`name`
UNION 
SELECT COUNT(*) AS 新增需求数 FROM zt_story t1 LEFT JOIN zt_projectstory t2 ON (t1.id = t2.story)
LEFT JOIN zt_;

--现在：查出一个项目有几个新增需求，几个修改需求，几个原始需求
SELECT t4.name, COUNT(*) AS `新增需求` FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON (t1.story=t2.id)
LEFT JOIN ict_initstory_endtime t3 ON (t1.project=t3.project_id AND t3.initstory_endtime<=t2.openedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
UNION
SELECT t4.name, COUNT(*) AS `修改需求` FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON (t1.story=t2.id)
LEFT JOIN ict_initstory_endtime t3 ON 
(t1.project=t3.project_id AND t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
UNION
SELECT t4.name, COUNT(*) AS `原始需求`, t3.project_id FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON (t1.story=t2.id)
LEFT JOIN ict_initstory_endtime t3 ON 
(t1.project=t3.project_id AND t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
;


SELECT t4.name, COUNT(*) AS `原始需求`, t3.project_id, t3.initstory_endtime, t2.openedDate, t2.lastEditedDate FROM ict_initstory_endtime t3
LEFT JOIN  zt_projectstory t1 ON (t1.project=t3.project_id)
LEFT JOIN zt_story t2 ON (t1.story=t2.id AND t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
UNION
SELECT t4.name, COUNT(*) AS `新增需求`, t3.project_id FROM ict_initstory_endtime t3
LEFT JOIN  zt_projectstory t1 ON (t1.project=t3.project_id)
LEFT JOIN zt_story t2 ON (t1.story=t2.id AND t1.project=t3.project_id AND t3.initstory_endtime<=t2.openedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
UNION
SELECT t4.name, COUNT(*) AS `修改需求`, t3.project_id FROM ict_initstory_endtime t3
LEFT JOIN  zt_projectstory t1 ON (t1.project=t3.project_id)
LEFT JOIN zt_story t2 ON (t1.story=t2.id AND t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)


SELECT * FROM zt_story;
SELECT * FROM zt_project;
SELECT * FROM zt_projectstory;
SELECT * FROM ict_initstory_endtime;
DELETE FROM ict_initstory_endtime;


SELECT t4.name, t3.project_id FROM ict_initstory_endtime t3
LEFT JOIN  zt_projectstory t1 ON (t1.project=t3.project_id)
LEFT JOIN zt_story t2 ON (t1.story=t2.id AND t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)

SELECT t4.name, t3.project_id FROM zt_story t2 
LEFT JOIN ict_initstory_endtime t3 ON (t3.initstory_endtime>t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_projectstory t1 ON (t1.project=t3.project_id AND t1.story=t2.id)
LEFT JOIN zt_project t4 ON (t1.project=t4.id)
