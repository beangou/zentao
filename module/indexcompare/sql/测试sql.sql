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




SELECT * FROM zt_bug;
SELECT * FROM zt_testtask;





SELECT * FROM ict_defect;




-- 缺陷去除率
-- 个人缺陷去除率

DELETE FROM ict_defect;
SELECT * FROM ict_defect;
SELECT * FROM ict_stability;
SELECT * FROM ict_completed;

DELETE FROM ict_stability;

SELECT T1.product, T1.project, T1.assignedTo, T2.devbugs, T1.testbugs FROM (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(t2.begin) AS testbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
	t2.product = t1.product 
	AND t2.project = t1.project 	
	AND t2.begin < t1.assignedDate
	)
GROUP BY t1.product, t1.project, t1.assignedTo
ORDER BY t1.product, t1.project ) T1 LEFT JOIN (
SELECT t1.product, t1.project, t1.assignedTo, COUNT(t2.begin) AS devbugs FROM zt_bug t1
LEFT JOIN zt_testtask t2 ON (
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
);

SELECT * FROM zt_bug;



