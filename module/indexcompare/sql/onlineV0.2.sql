SELECT *  FROM (
	SELECT T1.product, T1.project, T3.realname AS assignedTo, T2.devbugs ,T1.testbugs FROM (
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
	) LEFT JOIN zt_user T3 ON (
			T1.assignedTo = T3.account
		)
			
	UNION
			
	SELECT T1.product, T1.project, T3.realname AS assignedTo, T1.devbugs, T2.testbugs FROM (
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
		) LEFT JOIN zt_user T3 ON (
			T1.assignedTo = T3.account
		)
		
	) T
	WHERE assignedTo IS NOT NULL
	
	
	DELETE FROM ict_defect;
	SELECT * FROM ict_defect
	
	
	
	SELECT T1.product, T1.project, T4.realname AS openedBy, T1.initstory, T2.addstory, T3.changestory FROM
		(
		SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory
		FROM zt_projectStory t1
		LEFT JOIN zt_story t2 ON(t2.id=t1.story)
		LEFT JOIN ict_initstory_endtime t3 ON (
			t3.project_id = t1.project
			AND t3.initstory_endtime >= t2.openedDate)
		LEFT JOIN zt_project t4 ON(t4.id = t1.project)
		GROUP BY t2.product, t1.project, t2.openedBy) T1 LEFT JOIN
		(
		SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory
		FROM zt_projectStory t1
		LEFT JOIN zt_story t2 ON(t2.id=t1.story)
		LEFT JOIN ict_initstory_endtime t3 ON (
			t3.project_id = t1.project
			AND t3.initstory_endtime <= t2.openedDate)
		LEFT JOIN zt_project t4 ON(t4.id = t1.project)
		GROUP BY t2.product, t1.project, t2.openedBy) T2 ON(T1.product=T2.product AND T1.project=T2.project AND T1.openedBy=T2.openedBy)
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
		GROUP BY t2.product, t1.project, t2.openedBy) T3 ON(T1.product=T3.product AND T1.project=T3.project AND T1.openedBy=T3.openedBy)
		LEFT JOIN zt_user T4 ON (T1.openedBy = T4.account)
		WHERE T1.product IS NOT NULL
		ORDER BY t2.product, t1.project
		
		
		DELETE FROM ict_stability;
		
		
		
		
		
		
		
		
		
		
		SELECT T1.product, T1.project, T3.realname AS assignedTo, T2.closedtasks, T1.alltasks FROM (
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
		) LEFT JOIN zt_user T3 ON (T1.assignedTo = T3.account)
		ORDER BY T1.product, T1.project
		
		
		
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
		
		
		
		
		DELETE FROM ict_completed;
		SELECT * FROM ict_completed;
		