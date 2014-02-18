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
    
    public function stability($orderBy = '') {
    	 
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}

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
    	$this->view->stories = $this->indexcompare->selectStability();
    	$this->display();
    }
    
    public function perStability($orderBy = '') {
    	    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}

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
    
    	$this->display();
    }
    
    public function completed($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    	 
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    	 
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    	 
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    	
    	$this->display();
    }
    
    public function perCompleted($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    	 
    	$this->display();
    }
    
    public function productivity($orderBy = '') {
    	if(!empty($_POST)) {
    		$proname = $this->post->proname;
    		$empname = $this->post->empname;
    	}
    
    	if(!$orderBy) $orderBy = $this->cookie->projectTaskOrder ? $this->cookie->projectTaskOrder : 'status,id_desc';
    	setcookie('projectTaskOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
    
    	$selInfo = $this->indexcompare->getIndex($proname, $empname);
    	$this->view->selInfo = $selInfo;
    
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    	 
    	$this->display();
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
    
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    
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
    
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    
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
    
    	$this->view->products		= $this->loadModel('defect')->getProduct();
    	$defect 	= array();
    	if (!empty($_POST)){
    		foreach ($_POST as $ids){
    			$defect = $this->loadModel('defect')->queryDefect($ids);
    		}
    	}else {
    		$defect = $this->loadModel('defect')->queryDefect(1);
    	}
    
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
     * @param  string $name          the name of the select tag.
     * @param  array  $options       the array to create select tag from.
     * @param  string $selectedItems the item(s) to be selected, can like item1,item2.
     * @param  string $attrib        other params such as multiple, size and style.
     * @param  string $append        adjust if add options[$selectedItems].
     * @return string
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
    
}
