<?php
class planModel extends model{
	
	/**
	 * 项目组设定页面审核人员从ICT_USER表里取出所有同步人员
	 */
	public function queryUser()
	{
		$users = $this->dao->select('t1.account,t2.realname')->from(TABLE_ICTUSER)->alias('t1')->leftJoin(TABLE_USER)
		->alias('t2')->on('t1.account = t2.account')->orderBy('t1.account')->fetchPairs();
		if(!$users) return array();
		foreach($users as $account => $realName)
		{
			$firstLetter = ucfirst(substr($account, 0, 1)) . ':';
			$users[$account] =  $firstLetter . ($realName ? $realName : $account);
		}
		return array('' => '') + $users;
	}
	
	/**
	 * 保存周计划
	 */
	public function saveWeekPlan()
	{
		/*计算是第几周*/
		$_POST['week'] = date('W',strtotime($_POST['finishedDate']));
		$_POST['charge'] = $this->app->user->account;
		$_POST['status'] = 1;
		$this->dao->insert(TABLE_ICTWEEKPLAN)->data($_POST)->autoCheck()
			->batchCheck($this->config->plan->create->weekrequiredFields,'notempty')->exec();
	}
	
	/**
	 * 查询周计划管理
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryWeekPlan($account, $week, $limit = 0, $pager = null)
	{
		$weekPlan = $this->dao->select('*,"" as auditorName,"" as chargeName')->from(TABLE_ICTWEEKPLAN)
		->where('week')->eq((int)$week)
// 		->andWhere('date_format(finishedDate,"%Y-%m")')->eq(date('Y-m',strtotime($finishedDate)))
		->andWhere('charge')->eq($account)->andWhere('isSubmit')->eq('1')
		->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
// 		->beginIF($limit > 0)->limit($limit)->fi()->page($pager)
		->fetchAll();
			foreach ($weekPlan as $week){
				if (!empty($week->auditor))$week->auditorName =  $this->queryRealName($week->auditor);
				if (!empty($week->charge))$week->chargeName =  $this->queryRealName($week->charge);
			}
		return $weekPlan;
	}
	/**
	 * 获取当前周周计划
	 * @param unknown_type $account
	 */
	public function queryCurrentPlans($account)
	{
		$week = floor(date('W', strtotime(date('Y-m-d', time()))));
		$weekPlan = $this->dao->select('*,"" as auditorName,"" as chargeName')->from(TABLE_ICTWEEKPLAN)
		->where('week')->eq((int)$week)
		->andWhere('charge')->eq($account)
		->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
		->fetchAll();
		foreach ($weekPlan as $week){
			if (!empty($week->auditor))$week->auditorName =  $this->queryRealName($week->auditor);
			if (!empty($week->charge))$week->chargeName =  $this->queryRealName($week->charge);
		}
		return $weekPlan;
	}
	public function getDealPlans($account,$finish)
	{
		$week = floor(date('W',strtotime($finish)));
		$dealPlans = $this->dao->select('*,"" as auditorName')->from(TABLE_ICTWEEKPLAN)->where('week')->eq((int)$week)
						->andWhere('charge')->eq($account)->andWhere('status=2 or complete=1')->fetchAll();
		if (empty($dealPlans))return;
		foreach ($dealPlans as $plans){
			if (!empty($plans->auditor))$plans->auditorName =  $this->queryRealName($plans->auditor);
		}
		return $dealPlans;
	}
	/**
	 * 编辑页面 查询周计划
	 * @param unknown_type $planID
	 * @return boolean|unknown
	 */
	public function queryPlanByID($planID = '')
	{
		$plan = $this->dao->findById((int)$planID)->from(TABLE_ICTWEEKPLAN)->fetch();
		if(!$plan) return false;
		return $plan;
	}
	/**
	 * 批量增加周计划
	 */
	public function batchCreate()
	{
		$plans = fixer::input('post')->get();
		for ($i = 0; $i < count($_POST['types']); $i++){
			$taskID = $plans->taskID[$i]!==''?$plans->taskID[$i]:time();
			$null = $this->dao->select('*')->from(TABLE_ICTWEEKPLAN)->where('charge')->eq($this->app->user->account)
				->andWhere('(id')->eq((int)$plans->taskID[$i])->orWhere("taskID = $taskID)")
				->andWhere('week')->eq(date('W', strtotime($this->post->limit)))->fetch();
			if ($plans->sorts[$i] != '' && $plans->matters[$i] != ''){
				$plan 				= new stdClass();
				$plan->startDate	= isset($this->post->startDate)?$this->post->startDate:$this->post->limit;
				$plan->finishedDate	= isset($this->post->finishedDate)?$this->post->finishedDate:$this->post->limit;				$plan->week			= date('W', strtotime($this->post->finishedDate));
				$plan->charge   	= $this->app->user->account;
				$plan->type			= $plans->types[$i];
				$plan->sort			= $plans->sorts[$i];
				$plan->matter		= $plans->matters[$i];
				$plan->plan			= $plans->plans[$i];
				if(isset($plan->appraises[$i]))$plan->appraise		= $plans->appraises[$i];
				$plan->auditor		= $plans->auditors[$i];
				$plan->limit		= $this->post->limit;
				$plan->week			= date('W', strtotime($this->post->limit));
				$plan->isSubmit		= !empty($_GET['isSubmit'])?$_GET['isSubmit']:'0';
				if(isset($plans->taskID[$i]))$plan->taskID		= $taskID;
				if (empty($null))$this->dao->insert(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->exec();
				else $this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$null->id)->exec();
				if(dao::isError())
				{
					echo js::error(dao::getError());
					die(js::reload('parent'));
				}
			}
			else {
				unset($plans->types[$i]);
				unset($plans->sorts[$i]);
				unset($plans->matters[$i]);
				unset($plans->plans[$i]);
				unset($plans->appraises[$i]);
				unset($plans->auditors[$i]);
				unset($plans->limits[$i]);
			} 
		}
	}
	/**
	 * 更新修改的单条周计划
	 * @param unknown_type $planID
	 */
	public function update($planID, $from)
	{
// 		if ($from == 'myplan' || $from == 'deal' || $from == 'members')$_POST['status'] = 1;$_POST['complete'] = 1;
		if (!empty($_POST['finishedDate']))$_POST['week'] = date('W',strtotime($this->post->finishedDate));
		if (!empty($_POST['status']))$_POST['auditor'] = $this->app->user->account;
		$this->dao->update(TABLE_ICTWEEKPLAN)->data($_POST)
			->autoCheck()->check('matter','notempty')->where('id')->eq((int)$planID)->exec();
	}
	/**
	 * 批量更新计划
	 */
	public function batchUpdate($from)
	{
		$plans						= array();
		$planIDList = $this->post->planIDList ? $this->post->planIDList : array();
		if (!empty($planIDList)){
			foreach ($planIDList as $planID){
				$oldPlan = $this->queryPlanByID($planID);
				$plan				= new stdClass();
				$plan->charge   	= $this->app->user->account;
				$plan->startDate 	= $oldPlan->startDate;
				$plan->finishedDate = $oldPlan->finishedDate;
				$plan->week		 	= $oldPlan->week;
				$plan->desc		 	= $oldPlan->desc;
				if ($from == 'planBatchEdit'){
					$plan->status 		= 1;
					$plan->remark 		= $oldPlan->remark;
					$plan->complete		= $oldPlan->complete;
					$plan->type     	= $this->post->types[$planID];
					$plan->sort     	= $this->post->sorts[$planID];
					$plan->matter   	= $this->post->matters[$planID];
					$plan->plan     	= $this->post->plans[$planID];
					$plan->appraise 	= $this->post->appraises[$planID];
					$plan->auditor  	= $this->post->auditors[$planID];
					$plan->limit    	= $this->post->limits[$planID];
				}
				else if ($from == 'handleBatchAction'){
					$plan->status 		= isset($this->post->status[$planID]) ? $this->post->status[$planID] : $oldPlan->status;
					$plan->type     	= $oldPlan->type;
					$plan->sort     	= $oldPlan->sort;
					$plan->matter   	= $oldPlan->matter;
					$plan->plan     	= $oldPlan->plan;
					$plan->appraise 	= $oldPlan->appraise;
					$plan->auditor  	= $oldPlan->auditor;
					$plan->limit    	= $oldPlan->limit;
					$plan->complete		= $this->post->completes[$planID];
					$plan->remark 		= $this->post->remarks[$planID];
				}
				$plans[$planID]		= $plan;
				unset($plan);
			}
			foreach($plans as $planID => $plan){
				$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()
					->check('matter','notempty')->where('id')->eq((int)$planID)->exec();
			}
		}
	}
	/**
	 * 获取所有周计划
	 */
	public function getAllPlans()
	{
		return $this->dao->select('*')->from(TABLE_ICTWEEKPLAN)->fetchAll('id');
	}
	/**
	 * 查询项目组设定信息
	 */
	public function queryProteam()
	{
		$proteam = $this->dao->select('t1.*,t2.realname,"" as rel1,"" as rel2')
		->from(TABLE_ICTPROTEAM)->alias('t1')->leftJoin(TABLE_USER)
		->alias('t2')->on('t1.leader = t2.account')->fetchAll();
		foreach ($proteam as $team){
			if (isset($team->auditor1))$team->rel1 = $this->queryRealName($team->auditor1);
			if (isset($team->auditor2))$team->rel2 = $this->queryRealName($team->auditor2);
		}
		return $proteam;
	}
	/**
	 *  保存项目组设定信息
	 */
	public function saveProteam()
	{
		if (isset($_POST['team']) && isset($_POST['leader']))
			$this->dao->insert(TABLE_ICTPROTEAM)->data($_POST)->autoCheck()
			->batchCheck($this->config->plan->create->requiredFields,'notempty')
			->check('team','unique')->check('leader','unique')->exec();
		if (dao::isError()){
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	/**
	 * 查询未设定的成员小组名单
	 */
	public function userNotSet()
	{
		return $this->dao->select('t1.account,t3.realname')->from(TABLE_ICTUSER)->alias('t1')->leftJoin(TABLE_USER)
		->alias('t3')->on('t1.account = t3.account')
		->where('not exists(select account from ict_membset t2 where t1.account=t2.account)')->fetchPairs();
	}
	/**
	 * 保存需要设定的成员小组名单到member group settings
	 */
	public function saveMembUser()
	{
		if ($this->post->members == false)return;
		foreach ($this->post->members as $account)
		{
			$data->account = $account;
			$proteam = $this->dao->select('*')->from(TABLE_ICTPROTEAM)->where('leader')->eq($account)->fetch();
			if (!empty($proteam->leader)){
				$data->proteam = $proteam->id;
				$data->leader = '1';
				$data->auditor1 = $proteam->auditor1;
				$data->auditor2 = $proteam->auditor2;
			}
			$this->dao->insert(TABLE_ICTMEMBSET)->data($data)->check('account','unique')->exec();
		}
	}
	/**
	 * 查询已保存的小组成员
	 */
	public function queryMembUser()
	{
		$memberUser = $this->dao->select('t1.*,t2.realname,t3.team,t4.name,"" as rel1,"" as rel2')
		->from(TABLE_ICTMEMBSET)->alias('t1')->leftJoin(TABLE_USER)
		->alias('t2')->on('t1.account = t2.account')->leftJoin(TABLE_ICTPROTEAM)->alias('t3')->on('t1.proteam = t3.id')
		->leftJoin(TABLE_DEPT)->alias('t4')->on('t2.dept = t4.id')->orderBy('t1.proteam desc')->fetchAll();
		foreach ($memberUser as $memb){
			if (isset($memb->auditor1))$memb->rel1 = $this->queryRealName($memb->auditor1);
			if (isset($memb->auditor2))$memb->rel2 = $this->queryRealName($memb->auditor2);
		}
		return $memberUser;
	}
	public function queryTeam()
	{
		$teams = $this->dao->select('id,team')->from(TABLE_ICTPROTEAM)->fetchPairs();
		return array('' => '')+$teams;
	}
	/**
	 * 成员计划查询
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryMemberPlan($account, $finishedDate)
	{
		$proteam = $this->judgeAuditor($account);
		$week = floor(date('W',strtotime($finishedDate)));
		if (!empty($proteam)){
			$weekPlan = $this->dao->select("*,''as chargeName,'' as auditorName from ict_weekplan")->where('week')->eq((int)$week)
						->andWhere('charge')->ne($account)->andWhere('isSubmit')->eq('1')
						->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
						->fetchAll();
		}
		else {
			$weekPlan = $this->dao->select("*,''as chargeName,'' as auditorName from ict_weekplan WHERE charge IN
						(SELECT account FROM ict_membset WHERE proteam = (SELECT proteam FROM ict_proteam WHERE leader = '$account') and leader ='0')")
						->andWhere('week')->eq((int)$week)->andWhere('isSubmit')->eq('1')
						->andWhere('date_format(finishedDate,"%Y-%m")')->eq(date('Y-m',strtotime($finishedDate)))->fetchAll();
		}
		foreach ($weekPlan as $plan){
			if (isset($plan->charge))$plan->chargeName = $this->queryRealName($plan->charge);
			if (isset($plan->auditor))$plan->auditorName = $this->queryRealName($plan->auditor);
		}
		return $weekPlan;
	}
	/**
	 * 待我审核页面--待我审核计划查询
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function getCheckPlan($account)
	{
		$proteam = $this->judgeAuditor($account);
		if (!empty($proteam)){
			$weekPlan = $this->dao->select("*,''as chargeName,'' as auditorName from ict_weekplan")->where('complete in(0,1)')
// 						->andWhere('appraise')->eq(2)
						->andWhere('isSubmit')->eq('1')->andWhere("(status =3 or auditor='$account')")->fetchAll();
		}
		else {
			$weekPlan = $this->dao->select("*,''as chargeName,'' as auditorName from ict_weekplan WHERE status = 1 and (charge IN
					(SELECT account FROM ict_membset WHERE proteam = (SELECT proteam FROM ict_proteam WHERE leader = '$account') and leader ='0')")
					->orWhere("auditor='$account')")->andWhere('status')->eq(1)->andWhere('complete')->in(0,1)
					->andWhere('isSubmit')->eq('1')->andWhere('appraise')->eq(2)->fetchAll();
		}
		foreach ($weekPlan as $plan){
			if (isset($plan->charge))$plan->chargeName = $this->queryRealName($plan->charge);
			if (isset($plan->auditor))$plan->auditorName = $this->queryRealName($plan->auditor);
		}
		return $weekPlan;
	}
/**
	 * 判断当前用户是否是科室领导
	 */
	public function judgeAuditor($account)
	{
		return $this->dao->select('*')->from(TABLE_ICTPROTEAM)->where('auditor1')->eq($account)
				->orWhere('auditor2')->eq($account)->fetchAll();
	} 
	/**
	 * 获取上周未完成的周计划
	 * @param unknown_type $account
	 * @param unknown_type $lastWeekDate
	 */
	public function getLastPlan($account, $lastWeekDate)
	{
		$week = date('W',strtotime($lastWeekDate));
		return $this->dao->select('*')->from(TABLE_ICTWEEKPLAN)->where('charge')->eq($account)->andWhere('week')
			->eq((int)$week)->andWhere('(complete')->in(0,1)->orWhere('appraise in(0,1))')->fetchAll();
	}
	/**
	 * 获取时间段内迭代中的任务
	 * @param unknown_type $account
	 * @param unknown_type $finish
	 */
	public function getLastTask($account, $finish)
	{
		return $this->dao->select('id,name,deadline,assignedTo')->from(TABLE_TASK)->where('date(openedDate)')->ge($finish)
			->andWhere('status')->in('wait','doing')->andWhere('assignedTo')->eq($account)->fetchAll('id');
	}
	/**
	 * 根据所选项目组查询组长
	 * @param unknown_type $id
	 */
	public function querySingleTeam($id)
	{
		$singleTeam = $this->dao->select('t1.*,t2.realname,"" as rel1,"" as rel2')
		->from(TABLE_ICTPROTEAM)->alias('t1')->leftJoin(TABLE_USER)->alias('t2')
		->on('t1.leader = t2.account')->where('t1.id')->eq((int)$id)->fetch();
		if (empty($singleTeam))return array();
		if (isset($singleTeam->auditor1))$singleTeam->rel1 = $this->queryRealName($singleTeam->auditor1);
		if (isset($singleTeam->auditor1))$singleTeam->rel2 = $this->queryRealName($singleTeam->auditor2);
		return $singleTeam;
	}
	/**
	 * 获取上周未完成周计划
	 * @param unknown_type $finishedDate
	 */
	public function queryLastPlan($finishedDate)
	{
		$account = $this->app->user->account;
		$strDate = strtotime($finishedDate);
		$beginLastweek=mktime(0,0,0,date('m',$strDate),date('d',$strDate)-date('w',$strDate)+1-7,date('Y',$strDate));
		$lastTask = $this->getLastTask($account, date('Y-m-d',$beginLastweek));
		$lastPlan = $this->getLastPlan($account, date('Y-m-d',$beginLastweek));
		$allPlan  = $this->dao->select('taskID')->from(TABLE_ICTWEEKPLAN)->fetchAll('taskID');
		$nextPlan = $this->dao->select('*')->from(TABLE_ICTWEEKPLAN)->where('charge')->eq($account)->andWhere('week')
					->eq(date('W', strtotime($finishedDate)))->andWhere('isSubmit')->ne('1')->fetchAll();
		foreach ($lastPlan as $last){
			$this->lang->plan->abcSort[$last->sort.'1'] = $last->sort.'1';
			$last->sort 		= $this->lang->plan->abcSort[$last->sort.'1'];
			$last->finishedDate = $finishedDate;
			$last->week	  		= date('W',strtotime($finishedDate));
		}
		return !empty($_GET['plan'])?array_merge($nextPlan, array_merge($lastPlan,$lastTask)):$nextPlan;
	}
	/**
	 * 查询月计划完成率
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function getPlanRate($startDate, $finishedDate)
	{
		$startDate = strtotime($startDate);
		$finishedDate = strtotime($finishedDate);
		$plan = $this->dao->select('t1.*,t2.realname,t3.name,t4.team')->from(TABLE_ICTWEEKPLAN)->alias('t1')->leftJoin(TABLE_USER)
				->alias('t2')->on('t1.charge = t2.account')->leftJoin(TABLE_DEPT)->alias('t3')->on('t2.dept = t3.id')
				->leftJoin(TABLE_ICTMEMBSET)->alias('t5')->on('t1.charge = t5.account')->leftJoin(TABLE_ICTPROTEAM)->alias('t4')
				->on('t4.id = t5.proteam')
				->where('date_format(t1.startDate, "%Y-%m")')->ge(date('Y-m',$startDate))
				->andWhere('date_format(t1.finishedDate, "%Y-%m")')->le(date('Y-m',$finishedDate))
				->andWhere('t1.complete')->eq(2)->fetchAll();
		foreach ($plan as $plans){
			switch ($plans->sort){
				case 'A1': $data->score = 4.5;break;
				case 'A2': $data->score = 4;break;
				case 'A3': $data->score = 3.5;break;
				case 'A4': $data->score = 3;break;
				case 'B1': $data->score = 2.5;break;
				case 'B2': $data->score = 2.25;break;
				case 'B3': $data->score = 2;break;
				case 'B4': $data->score = 1.75;break;
				case 'C1': $data->score = 0.75;break;
				case 'C2': $data->score = 0.5;break;
				case 'C3': $data->score = 0.25;break;
			}
			$this->dao->update(TABLE_ICTWEEKPLAN)->data($data)->where('id')->eq((int)$plans->id)->autoCheck()->exec();
			
		}
		$allPlan = $this->dao->select('sum(t1.score) as score,t1.startDate as start,t1.finishedDate,t1.charge,t2.realname,t3.name,t4.team')
					->from(TABLE_ICTWEEKPLAN)->alias('t1')->leftJoin(TABLE_USER)
					->alias('t2')->on('t1.charge = t2.account')->leftJoin(TABLE_DEPT)->alias('t3')->on('t2.dept = t3.id')
					->leftJoin(TABLE_ICTMEMBSET)->alias('t5')->on('t1.charge = t5.account')->leftJoin(TABLE_ICTPROTEAM)->alias('t4')
					->on('t4.id = t5.proteam')
					->where('date_format(t1.startDate, "%Y-%m")')->ge(date('Y-m',$startDate))
					->andWhere('date_format(t1.finishedDate, "%Y-%m")')->le(date('Y-m',$finishedDate))
					->andWhere('t1.complete')->eq(2)->groupBy('t1.charge,DATE_FORMAT(t1.finishedDate,"%Y-%m")')
					->orderBy('score desc ')->fetchAll();
		$data = array();
		$length = abs(date("Y",$finishedDate)-date("Y",$finishedDate))*12+date("m",$finishedDate)-date("m",$startDate)+1;
		for ($i = 0; $i<$length; $i++){
			for ($j = 0; $j<count($allPlan); $j++){
				if (date('Y-m', strtotime($allPlan[$j]->finishedDate)) == date('Y-m',strtotime(date('Y',$startDate).'-'.(date('m',$startDate)+$i)))){
					$data[$i][$j]	= $allPlan[$j];
					$data[$i] = array_values($data[$i]);
				}
			}
		}
		for ($i = 0; $i<count($data); $i++){
			for ($j = 0; $j<count($data[$i]); $j++){
				if (!isset($data[$i][$j]->rank))	$data[$i][$j]->rank	= new stdClass();
				$data[$i][$j]->rank	= $j+1;
			}
		}
		return $data;
	}
	/**
	 * 月计划完成率单个人员月计划列表页面
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function getPlanByAccount($account, $startDate, $finishedDate)
	{
		$startDate 	  = strtotime($startDate);
		$finishedDate = strtotime($finishedDate);
		$plan = $this->dao->select('t1.*,t2.realname')->from(TABLE_ICTWEEKPLAN)->alias('t1')->leftJoin(TABLE_USER)->alias('t2')
				->on('t1.charge = t2.account')
				->where('date_format(t1.startDate, "%Y-%m")')->ge(date('Y-m',$startDate))
				->andWhere('date_format(t1.finishedDate, "%Y-%m")')->le(date('Y-m',$finishedDate))
				->andWhere('t1.charge')->eq($account)
				->andWhere('t1.complete')->eq(2)->fetchAll();
		return $plan;
	}
	/**
	 * 查询所属项目名称
	 */
	public function getTeaminfo()
	{
		$account = $this->app->user->account;
		return $this->dao->select('t1.team,t3.realname')->from(TABLE_ICTPROTEAM)->alias('t1')->leftJoin(TABLE_ICTMEMBSET)
			->alias('t2')->on('t1.id = t2.proteam')->leftJoin(TABLE_USER)->alias('t3')->on('t1.leader = t3.account')
			->where('t2.account')->eq($account)->fetch();
	}
	/**
	 * 查询真实姓名(公用)
	 * @param unknown_type $account
	 */
	public function queryRealName($account)
	{
		return $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($account)->fetch()->realname;
	}
}