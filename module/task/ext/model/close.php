<?php
/**
 * Close a task.
 *
 * @param  int      $taskID
 * @access public
 * @return void
 */
public function close($taskID)
{
	$oldTask = $this->getById($taskID);
	$now     = helper::now();
	$task = fixer::input('post')
	->setDefault('status', 'closed')
	->setDefault('assignedDate', $now)
	->setDefault('closedBy, lastEditedBy', $this->app->user->account)
	->setDefault('closedDate, lastEditedDate', $now)
	->setIF($oldTask->status == 'done',   'closedReason', 'done')
	->setIF($oldTask->status == 'cancel', 'closedReason', 'cancel')
	->remove('_recPerPage')
	->remove('comment')->get();
	$this->setStatus($task);

	$this->dao->update(TABLE_TASK)->data($task)->autoCheck()->where('id')->eq((int)$taskID)->exec();

	if($oldTask->story) $this->loadModel('story')->setStage($oldTask->story);
	if(!dao::isError()) return common::createChanges($oldTask, $task);
}