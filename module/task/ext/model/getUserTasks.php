<?php
/**
 * Get tasks of a user.
 *
 * @param  string $account
 * @param  string $type     the query type
 * @param  int    $limit
 * @param  object $pager
 * @access public
 * @return array
 */
public function getUserTasks($account, $type = 'assignedTo', $limit = 0, $pager = null, $orderBy="id_desc")
{
	$taskStatus = array();
	array_push($taskStatus, 'closed');
	array_push($taskStatus, 'cancel');
	$tasks = $this->dao->select('t1.*, t2.id as projectID, t2.name as projectName, t3.id as storyID, t3.title as storyTitle, t3.status AS storyStatus, t3.version AS latestStoryVersion')
	->from(TABLE_TASK)->alias('t1')
	->leftjoin(TABLE_PROJECT)->alias('t2')
	->on('t1.project = t2.id')
	->leftjoin(TABLE_STORY)->alias('t3')
	->on('t1.story = t3.id')
	->where('t1.deleted')->eq(0)
	->andWhere("t1.$type")->eq($account)
	->andWhere("t1.status")->notin($taskStatus)
	->orderBy($orderBy)
	->beginIF($limit > 0)->limit($limit)->fi()
	->page($pager)
	->fetchAll();

	$this->loadModel('common')->saveQueryCondition($this->dao->get(), 'task');

	if($tasks) return $this->processTasks($tasks);
	return array();
}