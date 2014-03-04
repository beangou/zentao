<?php
/**
 * The control file of index module of ZenTaoPMS.
 *
 * When requests the root of a website, this index module will be called.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id: control.php 5036 2013-07-06 05:26:44Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
class indexcompare extends control
{
	
	/**
	 * 项目缺陷去除率
	 */
	public function defectRate()
	{
		$this->view->title = $this->lang->indexcompare->common;
		$this->view->position[] 	= $this->lang->indexcompare->common;
		$this->view->products		= $this->indexcompare->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
				// 				$defect = $this->defect->queryDefect($ids);
				$defect = $this->indexcompare->myQueryDefect($ids);
			}
		}
	
		$this->view->defectRate = $defect;
		$this->display();
	}
	
	/**
	 * 个人缺陷去除率
	 */
	public function personalRate()
	{
		$this->view->title = $this->lang->indexcompare->common;
		$this->view->position[] 	= $this->lang->indexcompare->common;
		$this->view->products		= $this->indexcompare->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
				// 				$defect = $this->defect->getPersonalRate($ids);
				$defect = $this->indexcompare->myQueryPerDefect($ids);
			}
		}
	
		$this->view->personalRate = $defect;
		$this->display();
	}
	
	//查看历史个人缺陷率
	public function perHisRate($account = '', $realname = '') 
	{
		$hisRateList = array();
		$hisRateList = $this->indexcompare->searchPerHisRate($account);
		$this->view->hisRateList = $hisRateList;
		 
		$this->view->developer = $realname;
		$this->display();
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
		array_push($sonArr, $rs[3]/($rs[3]+$rs[4]));
	
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
	
	
	
	/**
     * The index page of whole zentao system.
     * 
     * @access public
     * @return void
     */
    public function index($orderBy = '')
    {
        //if($this->app->getViewType() == 'mhtml') $this->locate($this->createLink($this->config->locate->module, $this->config->locate->method, $this->config->locate->params));
        //$this->locate($this->createLink('my', 'index'));
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    	
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    	
        $this->display();
    }
    
    //项目需求稳定度
    public function stability($orderBy = '') {
    	$productArr = array();
    	if(!empty($_POST)) {
    		$productArr = $this->post->ids;
    	}
    	 
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$this->view->products		= $this->indexcompare->getProduct();

    	$dbIds = $this->indexcompare->getInitStoryEndTime();
    	$viewIds = array();
    	$viewNames = array();
    	foreach ($dbIds as $id) {
    		array_push($viewIds, $id->id);
    		array_push($viewNames, $id->name);
    	}
    	$viewSelect = array_combine($viewIds, $viewNames);
    	
    	$this->view->ids = $viewSelect;
    	$this->view->proAndTimes = $this->indexcompare->selectProAndTime();
    	$this->view->stories = $this->indexcompare->selectStability($productArr);
    	$this->display();
    }
    
	//个人需求稳定度
    public function perStability($orderBy = '') {
    	$productArr = array();
    	if(!empty($_POST)) {
    		$productArr = $this->post->ids;
    	}
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$this->view->products		= $this->indexcompare->getProduct();

    	$dbIds = $this->indexcompare->getInitStoryEndTime();
    	$viewIds = array();
    	$viewNames = array();
    	foreach ($dbIds as $id) {
    		array_push($viewIds, $id->id);
    		array_push($viewNames, $id->name);
    	}
    	$viewSelect = array_combine($viewIds, $viewNames);
    	
    	$this->view->ids = $viewSelect;
    	$this->view->proAndTimes = $this->indexcompare->selectProAndTime();
    	$this->view->stories = $this->indexcompare->selectPerStability($productArr);
    	$this->display();
    }
    
    public function completed($orderBy = '') {
    	$productArr = array();
    	if(!empty($_POST)) {
    		$productArr = $this->post->ids;
    	}
    	 
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$this->view->products		= $this->indexcompare->getProduct();
    	$this->view->tasks = $this->indexcompare->selectCompleted($productArr);
    	
    	$this->display();
    }
    
    public function perCompleted($orderBy = '') {
  		$productArr = array();
    	if(!empty($_POST)) {
    		$productArr = $this->post->ids;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$this->view->products		= $this->indexcompare->getProduct();
    	$this->view->tasks = $this->indexcompare->selectPerCompleted($productArr);
    	 
    	$this->display();
    }
    
    public function productivity($orderBy = '') {
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$this->view->products		= $this->indexcompare->getProduct();
    	 
    	$this->display();

    	
//     	$productArr = array();
//     	if(!empty($_POST)) {
//     		$productArr = $this->post->ids;
//     	}
    	
//     	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
//     	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	
//     	$this->view->products		= $this->indexcompare->getProduct();
//     	$this->view->tasks = $this->indexcompare->selectPerCompleted($productArr);
    	
//     	$this->display();
    	
    }
    
    public function perProductivity($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->view->products		= $this->indexcompare->getProduct();
    
    	$this->display();
    }
    
    public function performance($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->view->products		= $this->indexcompare->getProduct();
    
    	$this->display();
    }
    
    public function perPerformance($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->view->products		= $this->indexcompare->getProduct();
    
    	$this->display();
    }
    
    //获取原始需求结束时间
    public function ajaxGetEndTime($id = '',$endTime = '') {
    	//将项目id和原始需求结束时间点插入到 TABLE_ICTINITSTORY_ENDTIME中
    	$this->indexcompare->insertTime($id, $endTime);
    	//找出没有记录到原始需求结束时间点表的项目 TABLE_ICTINITSTORY_ENDTIME
    	$dbIds = $this->indexcompare->getInitStoryEndTime();
    	
    	$viewIds = array();
    	$viewNames = array();
    	foreach ($dbIds as $id) {
    		array_push($viewIds, $id->id);
    		array_push($viewNames, $id->name);
    	}
    	$viewSelect = array_combine($viewIds, $viewNames);
    	 
    	die(indexcompare::select('project_id', $viewSelect, '', "class='select-1'"));
    } 
    
    //查询每个项目的原始需求结束时间，并以列表显示
    public function ajaxGetProjAndTime() {
    	$proAndTimes = $this->view->proAndTimes = $this->indexcompare->selectProAndTime();
    	die(indexcompare::tableTr($proAndTimes));
    }
    
    //执行插入数据操作
    public function ajaxInsertData() {
    	//$proAndTimes = $this->view->proAndTimes = $this->indexcompare->selectProAndTime();
    	die('<script>alert("success!")</script>');
    }
    
    /**
     * Create tags like "<select><option></option></select>
     *
     * @param  string $name          the name of the select tag.
     * @param  array  $options       the array to create select tag from.
     * @param  string $selectedItems the item(s) to be selected, can like item1,item2.
     * @param  string $attrib        other params such as multiple, size and style.
     * @param  string $append        adjust if add options[$selectedItems].
     * @return string
     */
    static public function select($name = '', $options = array(), $attrib = '')
    {
    	$options = (array)($options);
    	if(!is_array($options) or empty($options)) return "<select id='$id' $attrib><option>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option></select>";
    
    	$id = $name;
    	$string = "<select name='$name' id='$id' $attrib>\n";
    
    	foreach($options as $key => $value)
    	{
    		$string  .= "<option value='$key'>$value</option>\n";
    	}

//     	$string = 
//     	'[
//     	{"optionKey":"1", "optionValue":"Canon in D"},
//     	{"optionKey":"2", "optionValue":"Wind Song"},
//     	{"optionKey":"3", "optionValue":"Wings"}
//     	]';
//     	return $string;
    	return $string .= "</select>\n";
    }
    
    /**
     * Create tags like "<tr><td>...</td></tr>"
     *
     * @param  string $name          the name of the select tag.
     * @param  array  $options       the array to create select tag from.
     * @param  string $selectedItems the item(s) to be selected, can like item1,item2.
     * @param  string $attrib        other params such as multiple, size and style.
     * @param  string $append        adjust if add options[$selectedItems].
     * @return string
     */
    static public function tableTr($trs = array())
    {
    	/* The begin. */
    	foreach($trs as $mytr)
    	{
    		$string  .= "<tr><td>".$mytr->prodName.'</td><td>'.
    					     $mytr->projName.'</td><td>'.
    		 				 $mytr->initstory_endtime.'</td></tr>';
    	}
    	/* End. */
    	return $string;
    }
    
    /**
     * 处理数组成为字符串，以‘,’分隔，以适应model里面的in方法的参数
     *
     * @param  array $temp          the name of the select tag.
     */
    static public function dealForDbIn($temp = array())
    {
    	
    	$queryLowStaff = '';
    	for ($i=0;$i<count($temp);$i++){
	   		if ($queryLowStaff!== ''){
				$queryLowStaff = $queryLowStaff.','.$temp[$i]->project_id;
			}else{
				$queryLowStaff = $temp[$i]->project_id;
			}
    	}
    	/* End. */
    	return $queryLowStaff;
    }
    

    public function getProCodeLine($proId = '') 
    {
    	//	system('diffcount -c test/count');
    	//	passthru('diffcount -c test/count');
    	$command = 'diffcount -c test/teamtoy2';
    	
    	@exec($command, $output, $result);
    	echo count($output);
    	//echo $output[8];
    	
    	foreach($output as $key=>$value) {
    		echo $key. ':'. $value. '<br/>';
    	}
    	
    	//echo $output;
    	//$output_str = implode(' ', $output);
    	//echo $command;
    	//echo $result. '_________'. $output_str;
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
		
		//这有当数组有数据时，才给rowspanVal赋值，否则，没意义，多一条没用的数据
		if ($rowspanValue > 0) {
			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
		}
		
		/* End. */
		return $temp;
	}
	
	/**
	 * 处理有一定顺序的数组，是根据其中某个key设置rowspan以表格形式显示到页面上来,返回的数组中某些元素多了rowspanVal的值
	 * @param  array $temp          the name of the select tag.
	 */
	static public function staDealArrForRowspan($temp = array(), $key = '')
	{
		$rowspanIndex = 0;
		$rowspanValue = 0;
		$productInit = 0;
		$productAdd = 0;
		$productChange = 0;
		for ($i=0; $i<count($temp); $i++){
			if ($temp[$i]->initstory == 0) {
				$temp[$i]->stability = '不存在';
			} else {
				$temp[$i]->stability = round(($temp[$i]->addstory + $temp[$i]->changestory)/$temp[$i]->initstory, 2); 
			}
			if ($temp[$i]->$key == $temp[$rowspanIndex]->$key) {
				$productInit += $temp[$i]->initstory;
				$productAdd += $temp[$i]->addstory;
				$productChange += $temp[$i]->changestory;
				$rowspanValue++;
			} else {
				$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
				$rowspanValue = 1;
				$temp[$rowspanIndex]->productStability = round(($productAdd+$productChange) / $productInit, 2);
				
				$rowspanIndex = $i;
				$productInit = $temp[$i]->initstory;
				$productAdd = $temp[$i]->addstory;
				$productChange = $temp[$i]->changestory;
			}
		}
		if ($rowspanValue > 0) {
			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
			$temp[$rowspanIndex]->productStability = round(($productAdd+$productChange) / $productInit, 2);
		}
		/* End. */
		return $temp;
	}
	
	//执行插入需求稳定度数据操作
	public function ajaxInsertStabilityData() {
		global $config;
	
		$con = @mysql_connect($config->db->host, $config->db->user, $config->db->password)
		or die("数据库服务器连接失败");
		@mysql_select_db($config->db->name) //选择数据库mydb
		or die("数据库不存在或不可用");
	
		//需求稳定度
		$queProStability = @mysql_query("
		SELECT T1.product, T1.project, T1.openedBy, T1.initstory, T2.addstory, T3.changestory FROM
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
		WHERE T1.product IS NOT NULL
		")
			or die("queProStability SQL语句执行失败");
	
			$parentStaArr = array();
			while($rs= @mysql_fetch_array($queProStability)){
				if ($rs[4]+$rs[3] == 0) {
					continue;
				}
				$sonArr = array();
				$stabilityStr = '';
				array_push($sonArr, $rs[0]);
				array_push($sonArr, $rs[1]);
				array_push($sonArr, $rs[2]);
	
				if ($rs[4] == null) {
					$rs[4] = 0;
				}
				if ($rs[5] == null) {
					$rs[5] = 0;
				}
				if ($rs[3] == 0) {
					$stabilityStr = '不存在';
				} else {
					$stabilityStr = round(($rs[4] + $rs[5])/$rs[3], 2);
				}
					
				array_push($sonArr, $rs[3]);
				array_push($sonArr, $rs[4]);
				array_push($sonArr, $rs[5]);
				array_push($sonArr, $stabilityStr);
				array_push($parentStaArr, $sonArr);
			}
	
			$splitNum = 1;
			foreach(array_chunk($parentStaArr, $splitNum) as $values){
				@mysql_query($this->insertBatch(TABLE_ICTSTABILITY, array('product', 'project', 'openedBy', 'initstory', 'addstory', 'changestory', 'stability'), $values));
			}
			die('<script>alert("生成数据成功!")</script>');
			mysql_close($con);
	}
	
	public function ajaxInsertCompletedData() {
		global $config;
	
		$con = @mysql_connect($config->db->host, $config->db->user, $config->db->password)
		or die("数据库服务器连接失败");
		@mysql_select_db($config->db->name) //选择数据库mydb
		or die("数据库不存在或不可用");
	
		//任务完成率
		$queProCompleted = @mysql_query("
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
		")
		or die("queProCompleted SQL语句执行失败"); 
		
		$parentComArr = array();
		while($rs= @mysql_fetch_array($queProCompleted)){
			 if ($rs[4] == 0) {
				continue;
			 }
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

			$splitNum = 1;
			foreach(array_chunk($parentComArr, $splitNum) as $values){
				@mysql_query($this->insertBatch(TABLE_ICTCOMPLETED, array('product', 'project', 'assignedTo', 'closedtasks', 'alltasks'), $values));
			}
			
			die('<script>alert("生成数据成功!")</script>');
			mysql_close($con);
	}
	
	//实现批量插入数据
	function insertBatch($table, $keys, $values, $type = 'INSERT'){
		$tempArray = array();
		foreach($values as $value){
			$tempArray[] = implode('\', \'', $value);
		}
		//return $type.' INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES (\''.implode('), (', $tempArray).'\')';
		return $type.' INTO '.$table.' (`'.implode('`, `', $keys).'`) VALUES (\''.implode('\'), (\'', $tempArray).'\')';
	}
    
}
