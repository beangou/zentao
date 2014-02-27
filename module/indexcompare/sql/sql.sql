
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

--修改需求
SELECT project, COUNT(story) FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime > t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
GROUP BY project;


SELECT t1.project, t2.openedDate, t3.project_id, t2.lastEditedDate
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime > t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
GROUP BY project;

SELECT * FROM ict_initstory_endtime;

--原始需求
SELECT t1.project, t2.openedDate, t2.lastEditedDate, t3.`initstory_endtime`
FROM zt_projectstory t1, zt_story t2, ict_initstory_endtime t3 
WHERE   t2.id=t1.story
	AND t3.`project_id` = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate
	
--原始需求	
SELECT t1.project, t2.openedDate, t3.`initstory_endtime`, t2.lastEditedDate
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate)
GROUP BY project;	


--原始需求
SELECT 	
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS '原始需求'
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project
UNION
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS '新增需求'
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project
UNION
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS '修改需求'
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.`project_id` = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project
;	


SELECT t2.product, t1.project, t4.name, t3.initstory_endtime AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate
	AND t3.initstory_endtime IS NOT NULL)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project


--项目需求稳定度
--where t3.initstory_endtime IS NOT NULL
SELECT T1.product, T1.project, T1.name, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)

GROUP BY project) T1,
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)

GROUP BY project) T2,
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)

GROUP BY project) T3
WHERE T1.project=T2.project
      AND T1.project=T3.project;
          
      
      
SELECT T1.product, T1.project, T1.name, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T1 LEFT JOIN
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T2 ON(T1.project=T2.project)
LEFT JOIN
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T3 ON(T1.project=T3.project)
WHERE T1.project=T2.project
      AND T1.project=T3.project;      
      



SELECT * FROM zt_story;

--
SELECT T1.product, T1.project, T1.name, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T1 LEFT JOIN
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T2 ON(T1.project=T2.project)
LEFT JOIN
(
SELECT t1.project, t4.name, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T3 ON(T1.project=T3.project)
WHERE T1.project=T2.project
      AND T1.project=T3.project;    




SELECT * FROM zt_story;  
SELECT * FROM zt_storyspec;  


SELECT t1.project, t4.name, t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime >= t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY t1.project, t2.openedBy;  


SELECT t1.project, t4.name,t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY t1.project, t2.openedBy;  


SELECT t1.project, t4.name,t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY t1.project, t2.openedBy;  


--select * FROM ict_initstory_endtime;


SELECT * FROM zt_story;









--***********************任务完成率************************
--任务完成率=状态为CLOSED的任务个数/所有任务个数
--1.项目任务完成率
SELECT * FROM zt_task;
SELECT project, COUNT(*) AS alltasks FROM zt_task GROUP BY project;
SELECT project, COUNT(*) AS completedtasks FROM zt_task WHERE STATUS='closed' GROUP BY project;
SELECT * FROM zt_taskestimate;
SELECT * FROM zt_user;
SELECT * FROM zt_projectproduct;

SELECT t4.name, t1.project, t2.name, COUNT(*) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectproduct t3 ON (t3.project = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t3.product)
WHERE t3.product IN (1)
GROUP BY t1.project;

--2.个人任务完成率
--查出产品名
SELECT t4.name, t1.project, t2.name, t1.assignedTo, COUNT(*) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectproduct t3 ON (t3.project = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t3.product)
WHERE t3.product IN (1)
GROUP BY t1.project, t1.assignedTo;

--不查出产品名
SELECT t1.project, t2.name, t1.assignedTo, COUNT(*) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
GROUP BY t1.project, t1.assignedTo;

SELECT t1.project, t1.assignedTo, COUNT(*) AS closedtasks FROM zt_task t1 
WHERE t1.status='closed'
GROUP BY t1.project, t1.assignedTo;


SELECT * FROM ict_defect;



--******************缺陷去除率************
SELECT * FROM zt_bug;

SELECT * FROM zt_user;

--项目缺陷去除率
--测试阶段：角色不是开发人员
SELECT t1.product, t4.name AS productname, t1.project, t3.name AS projectname, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_user t2 ON (t1.openedBy = t2.account)
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t1.product)
WHERE t2.role != 'dev'
GROUP BY t1.project
ORDER BY t1.product, t1.project;

--研发阶段：角色为开发人员
SELECT  t1.product, t4.name AS productname, t1.project, t3.name AS projectname, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_user t2 ON (t1.openedBy = t2.account)
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t1.product)
WHERE t2.role = 'dev'
GROUP BY t1.project
ORDER BY t1.product, t1.project;



SELECT T1.product, T1.productname, T1.project, T1.projectname, T1.testbugs, T2.devbugs FROM (
SELECT t1.product, t4.name AS productname, t1.project, t3.name AS projectname, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_user t2 ON (t1.openedBy = t2.account)
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t1.product)
WHERE t2.role != 'dev'
GROUP BY t1.project
ORDER BY t1.product, t1.project) T1 LEFT JOIN (
SELECT  t1.product, t1.project, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_user t2 ON (t1.openedBy = t2.account)
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
LEFT JOIN zt_product t4 ON (t4.id = t1.product)
WHERE t2.role = 'dev'
GROUP BY t1.project
ORDER BY t1.product, t1.project) T2 ON (
    T1.product = T2.product
AND T1.project = T2.project
);

--对于缺陷去除率，更改了查询的条件， 即不是通过指派人员来区分是开发阶段还是测试阶段，
--而是通过发布测试版本之前发现的bug即为研发阶段发现的bug，
--发布测试版本之后发现的bug即为测试阶段发现的bug，
SELECT * FROM ict_defect;

SELECT * FROM zt_testtask;
SELECT * FROM zt_bug;


--研发阶段bug
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testtask t2 
	WHERE t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project;
--测试阶段
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testtask t2 
	WHERE t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project;



SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testtask t2 
	WHERE t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project) T1 ,(
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testtask t2 
	WHERE t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project) T2 
WHERE
T1.product = T2.product
AND T1.project = T2.project
AND T1.assignedTo = T2.assignedTo
;

--sql问题：先简单点，查出每个产品对应下的每个项目有多少研发bug，多少测试bug
SELECT t1.product, t1.project, COUNT(*) AS devbugs FROM zt_bug t1, zt_testtask t2 
	WHERE t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project


--对比使用左连接和未使用左连接的差别后，发现应该使用左连接
--研发阶段
SELECT t1.product, t2.product, t1.project, t2.project, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project

--测试阶段
SELECT t1.product, t1.project, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project



SELECT T1.product, T1.project, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 FULL JOIN (
SELECT t1.product, t1.project, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project
) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
)


SELECT * FROM zt_bug;


SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 LEFT JOIN (
SELECT t1.product, t1.project, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project
) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
) 
UNION
SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin > t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 RIGHT JOIN (
SELECT t1.product, t1.project, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	)
WHERE t2.begin <= t1.assignedDate
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project
) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
) 






SELECT t1.product, t2.product, t1.project, t2.project, t1.assignedTo, t2.begin, t1.assignedDate FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.begin IS NOT NULL
	AND t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin < t1.assignedDate
	)
GROUP BY t1.product, t1.project
ORDER BY t1.product, t1.project;



SELECT t1.product, t1.project, t1.assignedTo, t1.id, t2.id, COUNT(t2.id) FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (t2.product = t1.product AND t2.project = t1.project AND t2.begin >= t1.assignedDate)
GROUP BY t1.project
ORDER BY t1.product, t1.project;



SELECT T1.product, T1.project, T1.assignedTo, T1.testbugs, T2.devbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (t2.product = t1.product AND t2.project = t1.project AND t2.begin < t1.assignedDate)
GROUP BY t1.project
ORDER BY t1.product, t1.project) T1 LEFT JOIN (
SELECT  t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (t2.product = t1.product AND t2.project = t1.project AND t2.begin >= t1.assignedDate)
GROUP BY t1.project
ORDER BY t1.product, t1.project) T2 ON (
    T1.product = T2.product
AND T1.project = T2.project
AND T1.assignedTo = T2.assignedTo
);

DELETE FROM ict_defect;
SELECT * FROM ict_defect;

--个人缺陷去除率
--测试阶段
SELECT t1.project, t3.name, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
WHERE t1.openedBy != t1.assignedTo
GROUP BY t1.project, t1.assignedTo;


--研发阶段
SELECT t1.project, t3.name, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
WHERE t1.openedBy = t1.assignedTo
GROUP BY t1.project, t1.assignedTo;



SELECT T1.product, T1.project, T1.assignedTo, T1.testbugs, T2.devbugs FROM (
SELECT t1.product, t1.project, t3.name, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
WHERE t1.openedBy != t1.assignedTo
GROUP BY t1.project, t1.assignedTo) T1 LEFT JOIN (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1
LEFT JOIN zt_project t3 ON (t3.id = t1.project)
WHERE t1.openedBy = t1.assignedTo
GROUP BY t1.project, t1.assignedTo) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
);



SELECT * FROM zt_action;
SELECT * FROM zt_project;
SELECT * FROM ict_product;
SELECT * FROM zt_productplan;
SELECT * FROM zt_testtask;


SELECT t2.product, t1.project, t4.name, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project ORDER BY t2.product;

--产品稳定度
SELECT product, COUNT(*), SUM(initstory) FROM (
SELECT t2.product, t1.project, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project ORDER BY t2.product) T1
GROUP BY T1.product;