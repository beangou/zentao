SELECT T1.name, T1.age, T1.chi, T2.math FROM (


SELECT T1.name, T2.name, T1.age, T2.age, T1.chi, T2.math FROM chi T1
LEFT JOIN math T2 ON (
 T1.name = T2.name
AND T1.age = T2.age
)
UNION 
SELECT T1.name, T2.name, T1.age, T2.age, T2.chi, T1.math FROM math T1
LEFT JOIN chi T2 ON (
 T1.name = T2.name
AND T1.age = T2.age
)