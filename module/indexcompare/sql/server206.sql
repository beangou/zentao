
-- 缺陷去除率
SELECT T1.product, T1.project, T1.assignedTo, T2.devbugs, T1.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(t2.begin) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testTask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin < t1.assignedDate
	)
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 LEFT JOIN (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(t2.begin) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testTask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin >= t1.assignedDate
	)
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project	
) T2 ON (
	T2.product = T1.product 
	AND T2.project = T1.project 
	AND T1.assignedTo = T2.assignedTo	
)

SELECT * FROM zt_bug;
-- 查询每个产品下的每个项目的每个人的研发阶段bug数以及测试阶段bug数（发现使用左连接，不行，）
SELECT t1.product, t1.project, t2.product, t2.project, t1.assignedTo, COUNT(t2.begin) AS testbugs, t1.assignedDate, t2.begin FROM zt_bug t1
LEFT JOIN zt_testTask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.assignedDate
	)
WHERE t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project


-- 测试阶段
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project

-- 研发阶段
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project


-- 将测试和研发两个阶段联合起来

SELECT T1.product, T1.project, T1.assignedTo, T2.devbugs ,T1.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 LEFT JOIN (

SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
)

UNION

SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project 
 ) T1 LEFT JOIN (

SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project

) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
)


SELECT * FROM ict_defect;
DELETE FROM ict_defect;








SELECT *  FROM (
	SELECT T1.product, T1.project, T1.assignedTo, T2.devbugs ,T1.testbugs FROM (
	SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2
	WHERE   t2.product = t1.product
		AND t2.project = t1.project
		AND t2.begin <= t1.openedDate
		AND t1.assignedTo != 'closed'
	GROUP BY t1.product, t1.project, t1.assignedTo
		) T1 LEFT JOIN (
			
	SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2
	WHERE   t2.product = t1.product
		AND t2.project = t1.project
		AND t2.begin > t1.openedDate
		AND t1.assignedTo != 'closed'
	GROUP BY t1.product, t1.project, t1.assignedTo
		) T2 ON (
		T1.product = T2.product
		AND T1.project = T2.project
		AND T1.assignedTo = T2.assignedTo
	)
			
	UNION
			
	SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
	SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2
	WHERE   t2.product = t1.product
		AND t2.project = t1.project
		AND t2.begin > t1.openedDate
		AND t1.assignedTo != 'closed'
	GROUP BY t1.product, t1.project, t1.assignedTo
	 ) T1 LEFT JOIN (
			
	SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2
	WHERE   t2.product = t1.product
		AND t2.project = t1.project
		AND t2.begin <= t1.openedDate
		AND t1.assignedTo != 'closed'
	GROUP BY t1.product, t1.project, t1.assignedTo
	) T2 ON (
		T1.product = T2.product
		AND T1.project = T2.project
		AND T1.assignedTo = T2.assignedTo
		)
	) T
	ORDER BY T.product, T.project

SELECT * FROM ict_defect;

 DELETE FROM ict_defect;



SELECT T1.product, T2.product, T1.project, T2.project, T1.assignedTo, T2.assignedTo, T2.devbugs ,T1.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 LEFT JOIN (

SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
)



SELECT t1.product, t2.product, t1.project, t2.project, t1.assignedTo, t2.begin, COUNT(t2.begin) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testTask t2 
ON ( t2.product = t1.product 
	AND t2.project = t1.project 
	AND t2.begin IS NOT NULL	
	AND t2.begin <= t1.openedDate
   )
WHERE t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo



SELECT t1.product, t2.product, t1.project, t2.project, t1.assignedTo, t1.openedDate, t2.begin AS testbugs FROM zt_bug t1
LEFT JOIN zt_testTask t2 
ON ( t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
   )
 
WHERE t1.assignedTo = 'liyilong' AND t1.product=5 AND t1.project=100	
WHERE t1.product=11 AND t1.project=85 AND t1.assignedTo='liyilong';  
-- GROUP BY t1.product, t1.project, t1.assignedTo


SELECT * FROM zt_bug WHERE product=5 AND project=100 AND assignedTo='liyilong';


SELECT * FROM zt_bug WHERE product=11 AND project=85 AND assignedTo='liyilong';





SELECT COUNT(*) FROM zt_bug t1
LEFT JOIN zt_testTask t2 
ON ( t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
   )
WHERE t1.assignedTo = 'liyilong' AND t1.product=5 AND t1.project=100	






-- 优化上述sql，比如不需提前order by

SELECT *  FROM (
SELECT T1.product, T1.project, T1.assignedTo, T2.devbugs ,T1.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
	) T1 LEFT JOIN (

SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo
	) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
)

UNION

SELECT T1.product, T1.project, T1.assignedTo, T1.devbugs, T2.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS devbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin > t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo

 ) T1 LEFT JOIN (

SELECT t1.product, t1.project, t1.assignedTo, COUNT(*) AS testbugs FROM zt_bug t1, zt_testTask t2 
WHERE   t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin <= t1.openedDate
	AND t1.assignedTo != 'closed'	
GROUP BY t1.product, t1.project, t1.assignedTo

) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo
	)
) T
ORDER BY T.product, T.project




SELECT * FROM zt_bug;

-- 需求稳定度
SELECT T1.product, T2.product, T3.product, T1.project, T2.project, T2.project, T1.openedBy, T2.openedBy, T3.openedBy, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T1 LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T2 ON(T1.product=T2.product AND T1.project=T2.project AND T1.openedBy=T2.openedBy)
LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T3 ON (
	T1.product=T3.product 
	AND T1.project=T3.project 
	AND T1.openedBy=T3.openedBy)
	
	


SELECT * FROM zt_projectStory;
	
	

SELECT T1.product, T2.product, T3.product, T1.project, T2.project, T2.project, T1.openedBy, T2.openedBy, T3.openedBy, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory, t3.initstory_endtime, t2.openedDate
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY t2.product, t1.project, t2.openedBy) T1 LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory, t3.initstory_endtime, t2.openedDate
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T2 ON(T1.product=T2.product AND T1.project=T2.project AND T1.openedBy=T2.openedBy)
LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectStory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T3 ON (
	T1.product=T3.product 
	AND T1.project=T3.project 
	AND T1.openedBy=T3.openedBy)	

DELETE FROM ict_stability;
SELECT * FROM ict_stability;


SELECT* FROM zt_task;

-- 任务完成率
SELECT T1.product, T1.project, T1.assignedTo, T2.closedtasks, T1.alltasks FROM (
SELECT t3.product, t1.project, t1.assignedTo, COUNT(t1.assignedTo) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectProduct t3 ON (t3.project = t1.project)
WHERE t3.product IS NOT NULL
AND t1.assignedTo != 'closed'
GROUP BY t3.product, t1.project, t1.assignedTo) T1 LEFT JOIN (
SELECT t3.product, t1.project, t1.assignedTo, COUNT(t1.assignedTo) AS closedtasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectProduct t3 ON (t3.project = t1.project)
WHERE t1.status='closed'
AND t3.product IS NOT NULL
AND t1.assignedTo != 'closed'
GROUP BY t3.product, t1.project, t1.assignedTo
) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project
	AND T1.assignedTo = T2.assignedTo	
) 
ORDER BY T1.product, T1.project



SELECT t3.product, t1.project, t1.assignedTo, COUNT(t1.assignedTo) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectProduct t3 ON (t3.project = t1.project)
WHERE t3.product IS NOT NULL
GROUP BY t3.product, t1.project, t1.assignedTo


SELECT t3.product, t1.project, t1.assignedTo, COUNT(t1.assignedTo) AS closedtasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectProduct t3 ON (t3.project = t1.project)
WHERE t1.status='closed'
AND t3.product IS NOT NULL
GROUP BY t3.product, t1.project, t1.assignedTo



SELECT * FROM ict_defect;
SELECT * FROM ict_stability;
SELECT * FROM ict_completed;

DELETE FROM  ict_defect;
DELETE FROM  ict_stability;
DELETE FROM  ict_completed;