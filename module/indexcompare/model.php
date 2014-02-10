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
}
