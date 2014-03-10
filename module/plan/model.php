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
	 * 根据年份，月份，星期查询周计划
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryWeekPlan($account, $firstDayOfWeek)
	{
// 		$weekPlan = $this->dao->select('*,"" as auditorName,"" as chargeName')->from(TABLE_ICTWEEKPLAN)
// 		->where('week')->eq((int)$week)
// 		->andWhere('charge')->eq($account)->andWhere('isSubmit')->eq('1')
// 		->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
// 		->fetchAll();
// 			foreach ($weekPlan as $week){
// 				if (!empty($week->auditor))$week->auditorName =  $this->queryRealName($week->auditor);
// 				if (!empty($week->charge))$week->chargeName =  $this->queryRealName($week->charge);
// 			}
		
		$weekPlan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->where('T1.account')->eq($account)
		->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
		->orderBy('T1.type')
		->fetchAll();
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
	
	public function updateCheckPlan() 
	{
		$plans = fixer::input('post')->get();
		for ($i = 0; $i < count($_POST['ids']); $i++){
			$plan 				= new stdClass();
		
			//使计划的状态为未提交，这样才能在提交人的“自评”中出现
// 			$plan->submitOrNo = '0';
			//使计划状态为已经审核（值为1）
			$plan->confirmedOrNo 		= '是';
			//添加备注
			$plan->remark     = $plans->remark[$i];
			//是否通过
			$plan->confirmed     = $plans->confirmed[$i];
			
			$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$plans->ids[$i])->exec();
			if(dao::isError())
			{
				echo js::error(dao::getError());
				die(js::reload('parent'));
			}
		}
	}
	
	/**
	 * 待我审核页面--待我审核计划查询(审核条件必须得通过自评,现在改为可评价所有未评价的计划，包括下周周计划)
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryCheckPlan($account)
	{
		$myplan = array();
		//已审核周计划
		$checkWeekPlan = $this->dao->select('T1.*, T2.realname AS accountname')
				->from(TABLE_ICTWEEKPLAN)->alias('T1')
				->leftJoin(TABLE_USER)->alias('T2')->on('T1.account = T2.account')
				->where('T1.submitTo')->eq($account)
				->andWhere('T1.confirmedOrNo')->eq('是')
				->orderBy('T1.firstDayOfWeek desc, T1.type')
				->fetchAll();
		//未审核周计划
		$uncheckedWeekPlan = $this->dao->select('T1.*, T2.realname AS accountname')
				->from(TABLE_ICTWEEKPLAN)->alias('T1')
				->leftJoin(TABLE_USER)->alias('T2')->on('T1.account = T2.account')
				->where('T1.submitTo')->eq($account)
				->andWhere('T1.confirmedOrNo')->eq('否')
				->orderBy('T1.firstDayOfWeek desc, T1.type')
				->fetchAll();
		array_push($myplan, $checkWeekPlan);
		array_push($myplan, $uncheckedWeekPlan);
		return $myplan;
	}
	
	
	/**
	 * 获取周计划（本周未审核的或者评审未通过的计划）
	 * @param unknown_type $finishedDate
	 */
	public function queryPlanByTime($firstDayOfWeek)
	{
		$account = $this->app->user->account;
		$date_now=date("j"); //得到几号
		$cal_result=ceil($date_now/7);
		
		$myplan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->where('T1.account')->eq($account)
		->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.confirmedOrNo="否" OR T1.confirmed="不通过")')
// 		->andWhere('T1.confirmedOrNo="否" OR T1.confirmed="不通过"')
		
// 		$myplan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
// 		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
// 		->where('T1.account')->eq($account)
// 		->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
// 		->andWhere('T1.confirmedOrNo="否" OR T1.confirmed="不通过"')
// 		->andWhere('T1.submitOrNo')->eq('0')
// 		->andWhere('T1.confirmed')->eq('不通过')
		->orderBy('T1.firstDayOfWeek, T1.type')
		->fetchAll();
// 		foreach ($lastPlan as $last){
// 			$this->lang->plan->abcSort[$last->sort.'1'] = $last->sort.'1';
// 			$last->sort 		= $this->lang->plan->abcSort[$last->sort.'1'];
// 			$last->finishedDate = $finishedDate;
// 			$last->week	  		= date('W',strtotime($finishedDate));
// 		}
		return $myplan;
	}
	
	// 不允许自己审核自己的
	public function getSubmitToName() 
	{
		$submitToNames = array();
		$name1->account = 'dingbing';
		$name1->realname = '丁兵';
		$name2->account = 'liutongbin';
		$name2->realname = '刘同彬';
		$name3->account = 'zhoubenwen';
		$name3->realname = '周本文';
		$name4->account = 'liyuchen';
		$name4->realname = '李雨辰';
		$name5->account = 'yangtao';
		$name5->realname = '杨涛';
		$name6->account = 'chendaoming';
		$name6->realname = '吴道明';
		
		array_push($submitToNames, $name1);
		array_push($submitToNames, $name2);
		array_push($submitToNames, $name3);

		array_push($submitToNames, $name4);
		array_push($submitToNames, $name5);
		array_push($submitToNames, $name6);
		
		return $submitToNames; 	
	}
	
	
	/**
	 * 获取下周未通过的周计划(审核通过的就不用选了)，此方法和queryPlanByTime一样，暂时略去，不用
	 * @param unknown_type $finishedDate
	 */
	public function queryNextUnpassPlan($firstDayOfWeek)
	{
		$account = $this->app->user->account;
	
		$myplan = $this->dao->select('T1.*, T2.realname AS submitName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->where('T1.account')->eq($account)
		->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
		->andWhere('T1.')
		->orderBy('T1.type')
// 		->andWhere('confirmed')->eq('不通过')
// 		->andWhere('confirmedOrNo')->eq('是')
		->fetchAll();
		return $myplan;
	}
	
	/**
	 * 批量更新周计划（自评周计划）
	 * */
// 	public function batchUpdateWeekplan() {
		
// 	}
	
	
	/**
	 * 批量增加下周周计划
	 */
	public function myBatchCreate($firstDayOfWeek)
	{
		$plans = fixer::input('post')->get();
		$delIds = $plans->ids;
		//没有的id都删掉
		$this->dao->delete()->from(TABLE_ICTWEEKPLAN)
		->where('id')->notin($delIds)
		->andWhere('account')->eq($this->app->user->account)
		->andWhere('firstDayOfWeek')->eq($firstDayOfWeek)
		->exec();
		//批量插入周计划
		for ($i = 0; $i < count($_POST['type']); $i++){
			if ($plans->matter[$i] != ''){
				$plan 				= new stdClass();
				$plan->account   	= $this->app->user->account;
				$plan->type			= $plans->type[$i];
				$plan->matter		= $plans->matter[$i];
				$plan->plan			= $plans->plan[$i];
				$plan->deadtime     = $plans->deadtime[$i];
	
				//获取下个星期的月份和第几个星期
				$nextWeek = date('Y-m-d', time()+7*24*3600);
				$timeSplit = explode('-', $nextWeek);
	
				$plan->month 		= $timeSplit[1];
				$plan->weekno		= ceil($timeSplit[2]/7);
	
				//找出本周六的日期和上周五的日期
				$myDateArr = plan::getLastAndEndDayOfWeek();
				$plan->firstDayOfWeek	   = $myDateArr[0];
				$plan->lastDayOfWeek	   = $myDateArr[1];
	
				$plan->submitTo		= $plans->submitTo[$i];
				// 				$plan->submitOrNo   = '1';
				if (empty($plans->ids[$i])) {
					$this->dao->insert(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->exec();
				} else {
					//审核不通过，重新改，更新，此时，将计划状态设为“未审核”
					$plan->confirmedOrNo = '否';
					$plan->confirmed = '';
					$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq($plans->ids[$i])->exec();
				}
				// 				else $this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$null->id)->exec();
				if(dao::isError())
				{
					echo js::error(dao::getError());
					die(js::reload('parent'));
				}
			}
			else {
				unset($plans->type[$i]);
				unset($plans->matter[$i]);
				unset($plans->plan[$i]);
				unset($plans->deadtime[$i]);
				unset($plans->submitTo[$i]);
			}
		}
	
	}
	
	
	/**
	 * 批量增加周计划
	 */
	public function batchCreate($firstDayOfWeek)
	{
		$plans = fixer::input('post')->get();
		$delIds = $plans->ids;
		//没有的id都删掉
// 		$this->dao->delete()->from(TABLE_ICTWEEKPLAN)
// 		->where('id')->notin($delIds)
// 		->andWhere('account')->eq($this->app->user->account)
// 		->andWhere('firstDayOfWeek')->eq($firstDayOfWeek)
// 		->exec();
		//批量插入周计划
		for ($i = 0; $i < count($_POST['type']); $i++){
			if ($plans->matter[$i] != ''){
				$plan 				= new stdClass();
				$plan->account   	= $this->app->user->account;
				$plan->type			= $plans->type[$i];
				$plan->matter		= $plans->matter[$i];
				$plan->plan			= $plans->plan[$i];
				$plan->deadtime     = $plans->deadtime[$i];
				
				//获取下个星期的月份和第几个星期
				$nextWeek = date('Y-m-d', time()+7*24*3600);
				$timeSplit = explode('-', $nextWeek);
				
				$plan->month 		= $timeSplit[1];
				$plan->weekno		= ceil($timeSplit[2]/7);
				
				//找出本周六的日期和上周五的日期
				$myDateArr = plan::getLastAndEndDayOfWeek();
				$plan->firstDayOfWeek	   = $myDateArr[0];
				$plan->lastDayOfWeek	   = $myDateArr[1];								
				
				$plan->submitTo		= $plans->submitTo[$i];
// 				$plan->submitOrNo   = '1';
				if (empty($plans->ids[$i])) {
					$this->dao->insert(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->exec();
				} else {
					//审核不通过，重新改，更新，此时，将计划状态设为“未审核”
					$plan->confirmedOrNo = '否';
					$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq($plans->ids[$i])->exec();
				} 
// 				else $this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$null->id)->exec();
				if(dao::isError())
				{
					echo js::error(dao::getError());
					die(js::reload('parent'));
				}
			}
			else {
				unset($plans->type[$i]);
				unset($plans->matter[$i]);
				unset($plans->plan[$i]);
				unset($plans->deadtime[$i]);
				unset($plans->submitTo[$i]);
			}
		}
		
	}
	
	//自评周计划
	public function evaluateMyPlan() {
		$plans = fixer::input('post')->get();
		//批量自评周计划
		for ($i = 0; $i < count($_POST['status']); $i++){
			$plan 				= new stdClass();
			$plan->account   	= $this->app->user->account;
		
			//使提交状态为1
// 			$plan->submitOrNo   = '1';
			$plan->status 		= $plans->status[$i];
			$plan->evidence     = $plans->evidence[$i];
			$plan->courseAndSolution    = $plans->courseAndSolution[$i];
			//将审核状态改为否
			//将审核结果改为空
			$plan->confirmedOrNo = '否';
			$plan->confirmed	= '';
			$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq((int)$plans->ids[$i])->exec();
			if(dao::isError())
			{
				echo js::error(dao::getError());
				die(js::reload('parent'));
			}
		}
	} 
	
	//根据输入日期查询
	public function searchplan($account, $beginDate, $endDate) {
		$searchResult = $this->dao->select('T1.*, T2.realname as submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
						->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
						->where('T1.account')->eq($account)
						->andWhere('T1.firstDayOfWeek')->gt($beginDate)
						->andWhere('T1.lastDayOfWeek')->lt($endDate)
						->orderBy('T1.firstDayOfWeek')
						->fetchAll();
		return $searchResult; 
	}
	
	public function searchForDetail ($planId) {
		$planDetail = $this->dao->select('T1.*, T2.realname as submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
					  ->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
					  ->where('T1.id')->eq($planId)
					  ->fetchAll(); 
		return $planDetail[0];
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

// 		$checkWeekPlan = $this->dao->select('*')
// 		->from(TABLE_ICTWEEKPLAN)
// 		->where('submitTo')->eq($account)
// 		->andWhere('confirmed')->ne('不通过')
// 		->fetchAll();
		
// 		return $checkWeekPlan;
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