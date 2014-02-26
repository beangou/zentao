<?php
/*
$fp = fopen('D:\\beanGou\\testBat\\'. date('YmdHis'). '.txt', 'w+');
fwrite($fp, '现在的时间是' . date('Y-m-d H:i:s'));
fclose($fp);
*/
 
@mysql_connect("localhost", "root","root") //选择数据库之前需要先连接数据库服务器 
or die("数据库服务器连接失败"); 
@mysql_select_db("zentao") //选择数据库mydb 
or die("数据库不存在或不可用"); 
$query = @mysql_query("select * from ict_defect") //执行SQL语句 
or die("SQL语句执行失败"); 
//echo mysql_result($query, 0, 'product');

//缺陷去除率
$queProDefect = @mysql_query("
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
") //执行SQL语句 
or die("queProDefect SQL语句执行失败"); 

echo '<table>';
$parentArr = array();
while($rs= @mysql_fetch_array($queProDefect)){
	 echo '<tr><td>'. $rs[0]. '</td><td>'. $rs[1]. '</td><td>'. $rs[2]. '</td><td>'. $rs[3]. '</td></tr>';//输出为第2列数据。
	 $sonArr = array();
	 array_push($sonArr, $rs[0]);
	 array_push($sonArr, $rs[1]);
	 if ($rs[3] == null) {
		$rs[3] = 0;
	 }
	 if ($rs[4] == null) {
		$rs[4] = 0;
	 }
	 array_push($sonArr, $rs[4]);	 
	 array_push($sonArr, $rs[3]);
	 array_push($sonArr, $rs[3]+$rs[4]);
     array_push($sonArr, $rs[3]/($rs[4]+$rs[3]));
	 array_push($sonArr, $rs[2]);	
	 array_push($parentArr, $sonArr);
}
echo '</table>';


function insertBatch($table, $keys, $values, $type = 'INSERT'){
	$tempArray = array();
	foreach($values as $value){
		$tempArray[] = implode('\', \'', $value);
	}
	//return $type.' INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES (\''.implode('), (', $tempArray).'\')';
	return $type.' INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES (\''.implode('\'), (\'', $tempArray).'\')';
}

$splitNum = 5;

foreach(array_chunk($parentArr, $splitNum) as $values){
	@mysql_query(insertBatch('ict_defect', array('product', 'project', 'testBug', 'devBug', 'total', 'defect', 'developer'), $values));
	echo insertBatch('test_insert', array('product', 'project', 'testBug', 'devBug', 'total', 'defect', 'developer'), $values);
	echo '<br>';
}

//http://my.oschina.net/cart/
//exit();






/*
$fp = fopen('D:\\beanGou\\www\\testBat\\'. date('YmdHis'). '.txt', 'w+');
fwrite($fp, '搜到的结果是' . mysql_result($query, 0, 'product'));
fclose($fp);
*/

echo '<br/>*************需求稳定度***************<br/>';

//需求稳定度
$queProStability = @mysql_query("
SELECT T1.product, T1.project, T1.openedBy, T1.initstory, T2.addstory, T3.changestory FROM
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T1 LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime <= t2.openedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T2 ON(T1.product=T2.product AND T1.project=T2.project AND T1.openedBy=T2.openedBy)
LEFT JOIN
(
SELECT t2.product, t1.project, t4.name, t2.openedBy, COUNT(t3.initstory_endtime) AS changestory
FROM zt_projectstory t1
LEFT JOIN zt_story t2 ON(t2.id=t1.story)
LEFT JOIN ict_initstory_endtime t3 ON (
	t3.project_id = t1.project
	AND t3.initstory_endtime >= t2.openedDate 
	AND t3.initstory_endtime < t2.lastEditedDate)
LEFT JOIN zt_project t4 ON(t4.id = t1.project)
GROUP BY project) T3 ON(T1.product=T3.product AND T1.project=T3.project AND T1.openedBy=T3.openedBy)
")
or die("queProStability SQL语句执行失败"); 

echo '<table>';
$parentStaArr = array();
while($rs= @mysql_fetch_array($queProStability)){
	 echo '<tr><td>'. $rs[0]. '</td><td>'. $rs[1]. '</td><td>'. $rs[2]. '</td><td>'. $rs[3]. '</td></tr>';//输出为第2列数据。
	 $sonArr = array();
	 array_push($sonArr, $rs[0]);
	 array_push($sonArr, $rs[1]);
	 array_push($sonArr, $rs[2]);
	 if ($rs[3] == null) {
		$rs[3] = 0;
	 }
	 if ($rs[4] == null) {
		$rs[4] = 0;
	 }
	 if ($rs[5] == null) {
		$rs[5] = 0;
	 }
 
	 array_push($sonArr, $rs[3]);
	 array_push($sonArr, $rs[4]);
	 array_push($sonArr, $rs[5]);	
	 array_push($parentStaArr, $sonArr);
}
echo '</table>';

foreach(array_chunk($parentStaArr, $splitNum) as $values){
	@mysql_query(insertBatch('ict_stability', array('product', 'project', 'openedBy', 'initstory', 'addstory', 'changestory'), $values));
	echo insertBatch('ict_stability', array('product', 'project', 'openedBy', 'initstory', 'addstory', 'changestory'), $values);
	echo '<br>';
}

//任务完成率
echo '<br/>*************任务完成率***************<br/>';

//需求稳定度
$queProCompleted = @mysql_query("
SELECT T1.product, T1.project, T1.assignedTo, T2.closedtasks, T1.alltasks FROM (
SELECT t3.product, t1.project, t1.assignedTo, COUNT(*) AS alltasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectproduct t3 ON (t3.project = t1.project)
GROUP BY t3.product, t1.project, t1.assignedTo) T1 LEFT JOIN (
SELECT t3.product, t1.project, t1.assignedTo, COUNT(*) AS closedtasks FROM zt_task t1 
LEFT JOIN zt_project t2 ON (t2.id = t1.project)
LEFT JOIN zt_projectproduct t3 ON (t3.project = t1.project)
WHERE t1.status='closed'
GROUP BY t3.product, t1.project, t1.assignedTo
) T2 ON (
	T1.product = T2.product
	AND T1.project = T2.project	
)
")
or die("queProStability SQL语句执行失败"); 

echo '<table>';
$parentComArr = array();
while($rs= @mysql_fetch_array($queProCompleted)){
	 echo '<tr><td>'. $rs[0]. '</td><td>'. $rs[1]. '</td><td>'. $rs[2]. '</td><td>'. $rs[3]. '</td></tr>';//输出为第2列数据。
	 $sonArr = array();
	 array_push($sonArr, $rs[0]);
	 array_push($sonArr, $rs[1]);
	 array_push($sonArr, $rs[2]);
	 if ($rs[3] == null) {
		$rs[3] = 0;
	 }
	 if ($rs[4] == null) {
		$rs[4] = 0;
	 }
 
	 array_push($sonArr, $rs[3]);
	 array_push($sonArr, $rs[4]);

	 array_push($parentComArr, $sonArr);
}
echo '</table>';

foreach(array_chunk($parentComArr, $splitNum) as $values){
	@mysql_query(insertBatch('ict_completed', array('product', 'project', 'assignedTo', 'closedtasks', 'alltasks'), $values));
	echo insertBatch('ict_completed', array('product', 'project', 'assignedTo', 'closedtasks', 'alltasks'), $values);
	echo '<br>';
}

?>

