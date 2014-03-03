<?php

class defect extends control
{
	/**
	 * 项目缺陷去除率
	 */
	public function defectRate()
	{
		$this->view->title = $this->lang->defect->common;
		$this->view->position[] 	= $this->lang->defect->common;
		$this->view->products		= $this->defect->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
// 				$defect = $this->defect->queryDefect($ids);
				$defect = $this->defect->myQueryDefect($ids);
			}
		}
		
// 		else {
// 			$defect = $this->defect->queryDefect(1);
// 		}
		$this->view->defectRate = $defect;
		$this->display();
	}
	
	/**
	 * 个人缺陷去除率
	 */
	public function personalRate()
	{
		$this->view->title = $this->lang->defect->common;
		$this->view->position[] 	= $this->lang->defect->common;
		$this->view->products		= $this->defect->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
// 				$defect = $this->defect->getPersonalRate($ids);
				$defect = $this->defect->myQueryPerDefect($ids);
			}
		}
		
// 		else {
// 			$defect = $this->defect->getPersonalRate(1);
// 		}
		$this->view->personalRate = $defect;
		$this->display();
	}
	
	/**
	 * 处理有一定顺序的数组，是根据其中某个key设置rowspan以表格形式显示到页面上来,返回的数组中某些元素多了rowspanVal的值
	 * @param  array $temp          the name of the select tag.
	 */
	static public function dealArrForRowspan($temp = array(), $key = '')
	{
		$rowspanIndex = 0;
		$rowspanValue = 0;
		for ($i=0; $i<count($temp); $i++){
			if ($temp[$i]->$key == $temp[$rowspanIndex]->$key) {
				$rowspanValue++;
			} else {
				$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
				$rowspanValue = 1;
				$rowspanIndex = $i;
			}
		}
		
		if ($rowspanValue > 0) {
			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
		}
		
		/* End. */
		return $temp;
	}
	
	function insertBatch($table, $keys, $values, $type = 'INSERT'){
		$tempArray = array();
		foreach($values as $value){
			$tempArray[] = implode('\', \'', $value);
		}
		//return $type.' INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES (\''.implode('), (', $tempArray).'\')';
		return $type.' INTO '.$table.' (`'.implode('`, `', $keys).'`) VALUES (\''.implode('\'), (\'', $tempArray).'\')';
	}
	
	//执行插入缺陷去除率操作
	public function ajaxInsertDefectData() {
		global $config;
		
		$con = @mysql_connect($config->db->host, $config->db->user, $config->db->password)
		or die("数据库服务器连接失败");
		@mysql_select_db($config->db->name) //选择数据库mydb
		or die("数据库不存在或不可用");
		
		//缺陷去除率
		$queProDefect = @mysql_query("
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
") //执行SQL语句
		or die("queProDefect SQL语句执行失败");
		
		$parentArr = array();
		while($rs= @mysql_fetch_array($queProDefect)){
			if ($rs[4]+$rs[3] == 0) {
				continue;
			}
		
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
			array_push($sonArr, $rs[4]/($rs[3]+$rs[4]));
		
			array_push($sonArr, $rs[2]);
			array_push($parentArr, $sonArr);
			
		}
		
		$splitNum = 1;
		
		foreach(array_chunk($parentArr, $splitNum) as $values){
			@mysql_query($this->insertBatch(TABLE_ICTDEFECT, array('product', 'project', 'testBug', 'devBug', 'total', 'defect', 'developer'), $values));
		}
		
		die('<script>alert("生成数据成功!")</script>');
		mysql_close($con);
	}
	
}