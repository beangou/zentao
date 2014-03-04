<?php
/**
 * The model file of index module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     index
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 */
?>
<?php
class indexcompareModel extends model
{	
	/**
	 * Get tasks of a project.
	 *
	 * @param  int    $projectID
	 * @param  string $status       all|needConfirm|wait|doing|done|cancel
	 * @param  string $type
	 * @param  object $pager
	 * @access public
	 * @return array
	 */
	public function getIndex($proname='', $empname='')
	{
// 		$orderBy = str_replace('status', 'statusCustom', $orderBy);
// 		$orderBy = str_replace('left', '`left`', $orderBy);
// 		$type    = strtolower($type);
// 		$tasks = $this->dao->select('t1.*, t2.id AS storyID, t2.title AS storyTitle, t2.version AS latestStoryVersion, t2.status AS storyStatus, t3.realname AS assignedToRealName')
// 		->from(TABLE_TASK)->alias('t1')
// 		->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
// 		->leftJoin(TABLE_USER)->alias('t3')->on('t1.assignedTo = t3.account')
// 		->where('t1.project')->eq((int)$projectID)
// 		->andWhere('t1.deleted')->eq(0)
// 		->beginIF($type == 'undone')->andWhere("(t1.status = 'wait' or t1.status ='doing')")->fi()
// 		->beginIF($type == 'needconfirm')->andWhere('t2.version > t1.storyVersion')->andWhere("t2.status = 'active'")->fi()
// 		->beginIF($type == 'assignedtome')->andWhere('t1.assignedTo')->eq($this->app->user->account)->fi()
// 		->beginIF($type == 'finishedbyme')->andWhere('t1.finishedby')->eq($this->app->user->account)->fi()
// 		->beginIF($type == 'delayed')->andWhere('deadline')->between('1970-1-1', helper::now())->andWhere('t1.status')->in('wait,doing')->fi()
// 		->beginIF(strpos(',all,undone,needconfirm,assignedtome,delayed,finishedbyme,', ",$type,") === false)->andWhere('t1.status')->in($type)->fi()
// 		->orderBy($orderBy)
// 		->page($pager)
// 		->fetchAll();
// 		$this->loadModel('common')->saveQueryCondition($this->dao->get(), 'task', $type == 'needconfirm' ? false : true);
// 		if($tasks) return $this->processTasks($tasks);

		//获取评价指标
		//1.从任务完成率开始
		
		return $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq((int)$taskID)->fetchAll();
		
// 		return array();
	}
	
	public function getInitStoryEndTime() {
		$recordedIds = indexcompare::dealForDbIn($this->dao->select('project_id')->from(TABLE_ICTINITSTORY_ENDTIME)->fetchAll());
		$ids = $this->dao->select('t1.id, t1.name')->from(TABLE_PROJECT)->alias('t1')->where('t1.id')->notin($recordedIds)->fi()->fetchAll();
		return $ids;
	}
	
	public function insertTime($id='', $endTime='') {
		$data->project_id   = $id;
		$data->initstory_endtime     = $endTime;
		$this->dao->insert(TABLE_ICTINITSTORY_ENDTIME)->data($data)->exec();
	}
	
	public function selectProAndTime() {

		return $this->dao->select('t4.name as prodName, t2.name as projName, t1.initstory_endtime')->from(TABLE_ICTINITSTORY_ENDTIME)->alias('t1')
		->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project_id = t2.id')
		->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t2.id = t3.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t3.product = t4.id')
		->fetchAll();
	}
	
	/**
	 * 查询产品
	 */
	public function getProduct($mode = '')
	{
		$orderBy  = !empty($this->config->product->orderBy) ? $this->config->product->orderBy : 'isClosed';
		$mode    .= $this->cookie->productMode;
		$products = $this->dao->select('*,  IF(INSTR(" closed", status) < 2, 0, 1) AS isClosed')
		->from(TABLE_PRODUCT)
		->where('deleted')->eq(0)
		->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
		->orderBy($orderBy)
		->fetchAll();
		$pairs = array();
		foreach($products as $product)
		{
			if($this->loadModel('product')->checkPriv($product))
			{
				$pairs[$product->id] = $product->name;
			}
		}
		return $pairs;
	}	
	
	//更改：项目缺陷缺陷去除率
	public function myQueryDefect($ids = '') {
		//测试阶段发现bug
		$testBugs = $this->dao->select('t1.product, t4.name AS productname, t1.project, t3.name AS projectname, 0 AS devbugs, COUNT(*) AS testbugs, COUNT(*) AS allbugs, \'0%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_USER)->alias('t2')->on('t1.openedBy = t2.account')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t2.role')->ne('dev')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project')->orderBy('t1.product, t1.project')
		->fetchAll();
		$testBugLen = count($testBugs);
	
		//研发阶段发现bug
		$devBugs = $this->dao->select('t1.project, COUNT(*) AS devbugs, COUNT(*) AS allbugs, \'100%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_USER)->alias('t2')->on('t1.openedBy = t2.account')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t2.role')->eq('dev')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project')->orderBy('t1.product, t1.project')
		->fetchAll();
		$devBugLen = count($devBugs);
	
		//组合两个阶段发现的bug
		for ($j=0; $j<$devBugLen; $j++) {
			//标志：用于判断测试阶段和研发阶段的记录是否可以合并，即显示在同一行内，否则，应该显示为两条记录
			$flag = 0;
			for ($i=0; $i<$testBugLen; $i++) {
				if ($testBugs[$i]->project == $devBugs[$j]->project) {
					$testBugs[$i]->devbugs = $devBugs[$i]->devbugs;
					$testBugs[$i]->allbugs = ($devBugs[$j]->devbugs + $testBugs[$i]->testbugs);
					$testBugs[$i]->defect = (100*round($testBugs[$i]->devbugs / ($testBugs[$i]->devbugs + $testBugs[$i]->testbugs), 4)). '%';
					$flag = 1;
					break;
				}
			}
			//如果没有进行合并，需要将研发阶段的记录作为数组中一元素加到返回数组中
			if ($flag == 0) {
				array_push($testBugs, $devBugs[$j]);
			}
		}
	
	
		// 		T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs,
		// 				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect
	
		$newResult = $this->dao->select('T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs,
				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect')
					->from(TABLE_ICTDEFECT)->alias('T1')
					->leftJoin(TABLE_PRODUCT)->alias('T2')->on('T2.id = T1.product')
					->leftJoin(TABLE_PROJECT)->alias('T3')->on('T3.id = T1.project')
					->where('T1.product')->in($ids)
					->andWhere('T1.developer')->ne('')
					->groupBy('T1.product, T1.project')
					->orderBy('T1.product, T1.project')
					->fetchAll();
	
		return indexcompare::dealArrForRowspan($newResult, 'product');
	}
	
	//更改：个人缺陷缺陷去除率
	public function myQueryPerDefect($ids = '') {
		$newResult = $this->dao->select('T1.project, T2.name AS projectname, T3.account, T3.realname AS developer, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs,
				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect')
					->from(TABLE_ICTDEFECT)->alias('T1')
					->leftJoin(TABLE_PROJECT)->alias('T2')->on('T2.id = T1.project')
					->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T1.developer')
					->where('T1.product')->in($ids)
					->andWhere('T1.developer')->ne('')
					->groupBy('T1.project, T1.developer')
					->orderBy('T1.product, T1.project')
					->fetchAll();
	
		return indexcompare::dealArrForRowspan($newResult, 'project');
	}
	
	public function searchPerHisRate($account = '') {
		$hisResult = $this->dao->select('t2.begin, t2.end, t2.name, t1.devBug, t1.total, t1.defect')
					 ->from(TABLE_ICTDEFECT)->alias('t1')
					 ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t2.id = t1.project')
					 ->where('t1.developer')->eq($account)
					 ->orderBy('t2.begin')
					 ->fetchAll();
		return $hisResult; 
	}
	
	
	//查询项目需求稳定度
	public function selectStability($productArr = array()) {
		//原始需求
		$initStory = $this->dao->select('t2.product, t5.name as productname, t1.project, t4.name as projectname, COUNT(t3.initstory_endtime) AS initstory, 0 AS addstory, 0 AS changestory, \'0%\' AS stability')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.initstory_endtime >= t2.openedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t5')->on('t5.id = t2.product')
		->where('t2.product')->in($productArr)
		->groupBy('t1.project')->orderBy('t2.product, t1.project')
		->fetchAll();
		$initLen = count($initStory);
		
		// 		->andWhere('t3.initstory_endtime IS NOT NULL')
		//新增需求
		$addStory = $this->dao->select('t2.product, t1.project, t4.name, 0 AS initstory, COUNT(t3.initstory_endtime) AS addstory, 0 AS changestory')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.project_id = t1.project AND t3.initstory_endtime <= t2.openedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->where('t2.product')->in($productArr)
// 		->andWhere('t3.initstory_endtime IS NOT NULL')
		->groupBy('t1.project')->orderBy('t2.product, t1.project')
		->fetchAll();
		$addLen = count($addStory);
		
		//修改需求
		$changeStory = $this->dao->select('t2.product, t1.project, t4.name, 0 AS initstory, 0 AS addstory, COUNT(t3.initstory_endtime) AS changestory')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.initstory_endtime >= t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->where('t2.product')->in($productArr)
// 		->andWhere('t3.initstory_endtime IS NOT NULL')
		->groupBy('t1.project')->orderBy('t2.product, t1.project')
		->fetchAll();
		$changeLen = count($changeStory);
		
		for ($i=0; $i<$initLen; $i++) {
// 			for ()
			$initStory[$i]->addstory = $addStory[$i]->addstory;
			$initStory[$i]->changestory = $changeStory[$i]->changestory;
			if ($initStory[$i]->initstory != 0) {
				$initStory[$i]->stability = 100*(round(($addStory[$i]->addstory + $changeStory[$i]->changestory) / $initStory[$i]->initstory, 4)). '%';
			} else if ($addStory[$i]->addstory + $changeStory[$i]->changestory > 0) {
				$initStory[$i]->stability = '无穷大';
			}
			
			//去除没用的记录（即原始需求数/新增需求数均为0）
			if ($initStory[$i]->initstory == 0 && $initStory[$i]->addstory == 0) {
				array_splice($initStory, $i, 1);
			}
		}
		
		$newResult = $this->dao->select('T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.initstory) AS initstory, SUM(T1.addstory) AS addstory,
				SUM(T1.changestory) AS changestory')
						->from(TABLE_ICTSTABILITY)->alias('T1')
						->leftJoin(TABLE_PRODUCT)->alias('T2')->on('T2.id = T1.product')
						->leftJoin(TABLE_PROJECT)->alias('T3')->on('T3.id = T1.project')
						->where('T1.product')->in($productArr)
						->andWhere('T1.openedBy')->ne('')
						->groupBy('T1.product, T1.project')
						->orderBy('T1.product, T1.project')
						->fetchAll();

		return indexcompare::staDealArrForRowspan($newResult, 'product');
	}
	
	//查询个人需求稳定度
	public function selectPerStability($productArr = array()) {
		//原始需求
		$initStory = $this->dao->select('t1.project, t4.name, t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS initstory')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.initstory_endtime >= t2.openedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t5')->on('t5.id = t2.product')
		->where('t2.product')->in($productArr)
		->groupBy('t1.project, t2.openedBy')->orderBy('t2.product, t1.project')
		->fetchAll();
		$initLen = count($initStory);
	
		//新增需求
		$addStory = $this->dao->select('t1.project, t4.name, t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS addstory')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.project_id = t1.project AND t3.initstory_endtime <= t2.openedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->where('t2.product')->in($productArr)
		->groupBy('t1.project, t2.openedBy')->orderBy('t2.product, t1.project')
		->fetchAll();
		$addLen = count($addStory);
	
		//修改需求
		$changeStory = $this->dao->select('t1.project, t4.name,t2.title, t2.openedBy, COUNT(t3.initstory_endtime) AS changestory')->from(TABLE_PROJECTSTORY)->alias('t1')
		->leftJoin(TABLE_STORY)->alias('t2')->on('t2.id=t1.story')
		->leftJoin(TABLE_ICTINITSTORY_ENDTIME)->alias('t3')
		->on('t3.project_id = t1.project AND t3.initstory_endtime >= t2.openedDate AND t3.initstory_endtime < t2.lastEditedDate')
		->leftJoin(TABLE_PROJECT)->alias('t4')->on('t4.id = t1.project')
		->where('t2.product')->in($productArr)
		->groupBy('t1.project, t2.openedBy')->orderBy('t2.product, t1.project')
		->fetchAll();
		$changeLen = count($changeStory);
	
		for ($i=0; $i<$initLen; $i++) {
			$initStory[$i]->addstory = $addStory[$i]->addstory;
			$initStory[$i]->changestory = $changeStory[$i]->changestory;
			if ($initStory[$i]->initstory != 0) {
				$initStory[$i]->stability = 100*round(($addStory[$i]->addstory + $changeStory[$i]->changestory) / $initStory[$i]->initstory, 4). '%';
			} else if ($addStory[$i]->addstory + $changeStory[$i]->changestory > 0) {
				$initStory[$i]->stability = '无穷大';
			}
			//去除没用的记录（即原始需求数/新增需求数均为0）
			if ($initStory[$i]->initstory == 0 && $initStory[$i]->addstory == 0) {
				array_splice($initStory, $i, 1);
			} 
		}
		
		
		$newResult = $this->dao->select('T1.project, T3.name AS projectname, T4.realname AS openedBy, T1.initstory, T1.addstory,
				T1.changestory, T1.stability')
						->from(TABLE_ICTSTABILITY)->alias('T1')
						->leftJoin(TABLE_PRODUCT)->alias('T2')->on('T2.id = T1.product')
						->leftJoin(TABLE_PROJECT)->alias('T3')->on('T3.id = T1.project')
						->leftJoin(TABLE_USER)->alias('T4')->on('T4.account = T1.openedBy')
						->where('T1.product')->in($productArr)
						->andWhere('T1.openedBy')->ne('')
						->groupBy('T1.product, T1.project, T1.openedBy')
						->orderBy('T1.product, T1.project')
						->fetchAll();
	
		return indexcompare::dealArrForRowspan($newResult, 'project');
	}
	
	//项目任务完成率
	public function selectCompleted($productArr = array()) {
		$allTasks = $this->dao->select('t3.product, t4.name as productname, t1.project, t2.name as projectname,  COUNT(*) AS alltasks')->from(TABLE_TASK)->alias('t1')
		->leftJoin(TABLE_PROJECT)->alias('t2')->on('t2.id = t1.project')
		->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t3.project = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t3.product')
		->where('t3.product')->in($productArr)
		->groupBy('t1.project')->fetchAll();
		$closedTasks = $this->dao->select('project, COUNT(*) AS closedtasks')->from(TABLE_TASK)
		->where('status')->eq('closed')
		->groupBy('project')
		->orderBy('project')
		->fetchAll();
		$allLen = count($allTasks);
		$closedLen = count($closedTasks);
		
		for ($i=0; $i<$allLen; $i++) {
			for ($j=0; $j<$closedLen; $j++) {
				if ($closedTasks[$j]->project == $allTasks[$i]->project && $closedTasks[$j]->closedtasks > 0) {
					$allTasks[$i]->closedtasks = $closedTasks[$j]->closedtasks;
					break;
				} else {
					$allTasks[$i]->closedtasks = 0;
				}
			}
			$allTasks[$i]->completed = 100*round(($allTasks[$i]->closedtasks / $allTasks[$i]->alltasks), 4). '%';
		}
		
		$newResult = $this->dao->select('T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.closedtasks) AS closedtasks, 
				SUM(T1.alltasks) AS alltasks, SUM(T1.closedtasks)/SUM(T1.alltasks) AS completed')
						->from(TABLE_ICTCOMPLETED)->alias('T1')
						->leftJoin(TABLE_PRODUCT)->alias('T2')->on('T2.id = T1.product')
						->leftJoin(TABLE_PROJECT)->alias('T3')->on('T3.id = T1.project')
						->where('T1.product')->in($productArr)
						->andWhere('T1.assignedTo')->ne('')
						->groupBy('T1.product, T1.project')
						->orderBy('T1.product, T1.project')
						->fetchAll();
		
		return indexcompare::dealArrForRowspan($newResult, 'product');
	}
	
	//个人任务完成率
	public function selectPerCompleted($productArr = array()) {
		$allTasks = $this->dao->select('t1.project, t2.name, t1.assignedTo, COUNT(*) AS alltasks')->from(TABLE_TASK)->alias('t1')
		->leftJoin(TABLE_PROJECT)->alias('t2')->on('t2.id = t1.project')
		->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t3.project = t2.id')
		->where('t3.product')->in($productArr)
		->groupBy('t1.project, t1.assignedTo')
		->orderBy('t3.product, t1.project')
		->fetchAll();
		$closedTasks = $this->dao->select('project, assignedTo, COUNT(*) AS closedtasks')->from(TABLE_TASK)
		->where('status')->eq('closed')->groupBy('project, assignedTo')->fetchAll();
		$allLen = count($allTasks);
		$closedLen = count($closedTasks);
	
		for ($i=0; $i<$allLen; $i++) {
			for ($j=0; $j<$closedLen; $j++) {
				if ($closedTasks[$j]->project == $allTasks[$i]->project &&
					$closedTasks[$j]->assignedTo == $allTasks[$i]->assignedTo &&
					$closedTasks[$j]->closedtasks > 0) {
					$allTasks[$i]->closedtasks = $closedTasks[$j]->closedtasks;
					break;
				} else {
					$allTasks[$i]->closedtasks = 0;
				}
			}
			$allTasks[$i]->completed = 100*round($allTasks[$i]->closedtasks / $allTasks[$i]->alltasks, 4). '%';
		}
		
		
		$newResult = $this->dao->select('T1.project, T2.name AS projectname, T3.realname AS assignedTo, T1.closedtasks, T1.alltasks, 
 						T1.closedtasks/ T1.alltasks AS completed')
						->from(TABLE_ICTCOMPLETED)->alias('T1')
						->leftJoin(TABLE_PROJECT)->alias('T2')->on('T2.id = T1.project')
						->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T1.assignedTo')
						->where('T1.product')->in($productArr)
						->andWhere('T1.assignedTo')->ne('')
						->groupBy('T1.product, T2.id, T1.assignedTo')
						->orderBy('T1.product, T2.id')
						->fetchAll();
	
		return indexcompare::dealArrForRowspan($newResult, 'project');
	}
	
}
