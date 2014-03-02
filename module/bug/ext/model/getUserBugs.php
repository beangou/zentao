<?php
/**
 * Get user bugs.
 * 扩展：增加检索条件 t1.status = closed
 * @param  string $account
 * @param  string $type
 * @param  string $orderBy
 * @param  int    $limit
 * @param  int    $pager
 * @access public
 * @return void
 */
public function getUserBugs($account, $type = 'assignedTo', $orderBy = 'id_desc', $limit = 0, $pager = null)
{
	$bugs = $this->dao->select('*')->from(TABLE_BUG)
	->where('deleted')->eq(0)
	->andWhere("$type")->eq($account)
	->andWhere("status")->ne('closed')
	->orderBy($orderBy)
	->beginIF($limit > 0)->limit($limit)->fi()
	->page($pager)
	->fetchAll();
	return $bugs ? $bugs : array();
}