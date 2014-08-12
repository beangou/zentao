<?php
error_reporting(E_ALL);
class planModel extends model{
	
	/**
	 * 根据id查询项目组信息
	 */
	public function searchProteamInfo($infoId = '') {
		$proteam = $this->dao->select('T1.id, T1.team, T1.leader, T3.realname AS leaderName, T4.realname AS managerName')
					->from(TABLE_ICTPROTEAM)->alias('T1')
					->leftJoin(TABLE_ICTMEMBSET)->alias('T2')->on('T2.proteam = T1.id AND T2.leader = "2"')
					->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T1.leader')
					->leftJoin(TABLE_USER)->alias('T4')->on('T4.account = T2.account')
					->where('T1.id')->eq($infoId)
					->orderBy('T1.team')
					->fetchAll();
		return $proteam;
	}
	
	/**
	 * 根据id查询项目组信息
	 */
	public function editProteamInfo() {
		// 更新项目组信息：改名或 改组长 或 改技术经理 需要
		$proteamInfo->id     		= $_POST['infoId'];
		$proteamInfo->team   		= $_POST['team'];
		$proteamInfo->leader 		= $_POST['leader'];
		
		$this->dao->update(TABLE_ICTPROTEAM)->data($proteamInfo)
					->autoCheck()
					->check('team','notempty')->check('leader','notempty')
					->check('team','unique')->check('leader','unique')
					->where('id')->eq((int)$_POST['infoId'])->exec();
		
		// 更新组长的leader值
		if (!empty($_POST['leader'])) {
			$leaderData->leader = '1';
			$this->dao->update(TABLE_ICTMEMBSET)->data($leaderData)
			->where('account')->eq($_POST['leader'])->exec();
		}
		
		// 更新技术经理的leader值
		if (!empty($_POST['techmanager'])) {
			$managers = $_POST['techmanager'];
			$techCount = count($managers);
			// 将技术经理 插入到 memberset表中
			for ($i = 0; $i < $techCount; $i++){
				$managerData->proteam = $proteamId->id;
				$managerData->leader  = '2';
				
				$this->dao->update(TABLE_ICTPROTEAM)->data($managerData)
				->where('account')->eq($_POST['techmanager'])->exec();
				$this->dao->insert(TABLE_ICTMEMBSET)->data($data)->autoCheck()
				->check('account','unique')->exec();
			}
		}
		
	}
	
	/**
	 * 我的审核
	 * 查出a链接
	 */
	public function queryUnauditForAlink($account)
	{
		$unauditLinks = array();
		//如果是科长：即查询各个组长和技术经理的相关信息
		if (count($this->checkCollectPlan()) > 0 && $this->app->user->account != 'zhoubenwen') {
			// 找出未审核的组长的周计划
			$leaderLinks = $this->dao->select('T1.account, T2.realname, T3.firstDayOfWeek, T3.lastDayOfWeek, T4.team')->from(TABLE_ICTWEEKPLAN)->alias('T3')
							->leftJoin(TABLE_ICTMEMBSET)->alias('T1')->on('T1.account = T3.account')
							->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.account')
							->leftJoin(TABLE_ICTPROTEAM)->alias('T4')->on('T4.leader = T3.account')
							->where('T1.leader')->eq('1')
							->andWhere('T3.auditPass')->eq('2')
							->groupBy('T1.account, T3.firstDayOfWeek')
							->orderBy('T4.team, T3.account, T3.firstDayOfWeek')
							->fetchAll();
			// 找出未审核的技术经理的周计划
			$techManagerLinks =	$this->dao->select('T4.team, T1.account, T5.realname, T1.firstDayOfWeek, T1.lastDayOfWeek from ict_my_weekplan T1 
								LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
		    		 			GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T2 
								on (T2.account = T1.account AND T2.firstDayOfWeek = T1.firstDayOfWeek)')
								->leftJoin(TABLE_ICTMEMBSET)->alias('T3')->on('T3.account = T1.account')
								->leftJoin(TABLE_ICTPROTEAM)->alias('T4')->on('T4.id = T3.proteam')
								->leftJoin(TABLE_USER)->alias('T5')->on('T5.account = T1.account')
								->where('T3.leader = "2" AND (T1.auditPass = "2" OR (T2.result = "同意" AND T2.auditor != "chenxiaobo"))')
								->groupBy('T1.account, T1.firstDayOfWeek')
								->orderBy('T4.team, T1.account, T1.firstDayOfWeek')
								->fetchAll();
			
			
			$unauditLinks = array_merge($leaderLinks, $techManagerLinks);
			
			$teamArr = array();
			$nameArr = array();
			$i = 0;
			foreach ($unauditLinks as $unauditLink) {
				$teamArr[$i] = $unauditLink->team;
				$nameArr[$i] = $unauditLink->realname;
				$i++;
			}
			
			array_multisort($teamArr, $nameArr, $unauditLinks); 
			
		} else {
			//如果是组长：即查询其组内成员的相关信息(如果自己审核通过了，肯定查不出结果，不能再审核。条件：该计划的最后审核人是该组长 )
			$unauditLinks = $this->dao->select('T1.account, T2.realname, T4.firstDayOfWeek, T4.lastDayOfWeek from ict_my_weekplan T4 
								LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
		    		 			GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T5
								on (T5.account = T4.account AND T5.`firstDayOfWeek` = T4.`firstDayOfWeek`)')
								->leftJoin(TABLE_ICTMEMBSET)->alias('T1')->on('T1.account = T4.account')
								->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T4.account')
								->leftJoin(TABLE_ICTPROTEAM)->alias('T3')->on('T3.id = T1.proteam')
								->where('T3.leader="'. $account. '" AND (T5.auditor IS NULL OR T5.auditor = "'. $account. '") 
										 AND T4.auditPass = "2"')
								->andWhere('T1.account')->ne($account)
								->groupBy('T4.account, T4.firstDayOfWeek')
								->orderBy('T3.team, T4.account, T4.firstDayOfWeek')
								->fetchAll();
		}
		return $unauditLinks;
	}
	
	/**
	 * 获取可查看的成员(科长、周总可以查看各个组长、技术经理的计划，各个组长可以查看组内成员的计划，)
	 */
	public function queryMemberForQuery()
	{
		$members = array();
		//组长查出组内成员计划
		$members1 = array();
		//科长或者周总查出组长、技术经理计划
		$members2 = array();
		// 		"我的审核"页面里面，如果是组长，取出小组普通成员的下拉列表
		if ($this->app->user->account == 'zhoubenwen' || count($this->checkCollectPlan()) == 0) {
			$members1 = $this->dao->select('T1.account, T2.realname')->from(TABLE_ICTMEMBSET)->alias('T1')
			->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.account')
			->leftJoin(TABLE_ICTPROTEAM)->alias('T3')->on('T3.id = T1.proteam')
			->where('T3.leader')->eq($this->app->user->account)
			->andWhere('T1.account')->ne($this->app->user->account)
			->orderBy('T1.account')
			->fetchPairs();
		} 

		if (count($this->checkCollectPlan()) > 0) {
			// 只要leader不是0，即1或2，对应的就是组长和技术经理了
			$members2 = $this->dao->select('T1.account, T2.realname')->from(TABLE_ICTMEMBSET)->alias('T1')
			->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.account')
			->where('T1.leader')->ne('0')
			->orderBy('T1.account')
			->fetchPairs();
		}
		// 		"我的审核"页面里面，如果是科长，取出小组组长的下拉列表
		if (count($members1) > 0 && count($members2) > 0) {
			$members = array_merge($members2, $members1);
		} else if (count($members1) > 0) {
			$members = $members1;
		} else if (count($members2) > 0) {
			$members = $members2;
		} 	
			
		if(!$members) return array();
		foreach($members as $account => $realName)
		{
			$firstLetter = ucfirst(substr($account, 0, 1)) . ':';
			$users[$account] =  $firstLetter . ($realName ? $realName : $account);
		}
		return array('' => '') + $members;
	}
	
	/**
	 * "我的审核" 增加本周未审核计划部分
	 */
	public function queryUnauditPlan($account, $firstDayOfThisWeek)
	{
		$weekPlan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->where('T1.auditId is NULL')
		->andWhere('T1.account')->eq($account)
		->andWhere('T1.firstDayOfWeek')->eq($firstDayOfThisWeek)
		->orderBy('T1.firstDayOfWeek desc, T1.type')
		->fetchAll();
		return $weekPlan;
	}
	
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
	 * “我的审核”页面里面取得我的成员下拉列表
	 */
	public function queryMyMember()
	{
		
		$members = array();
		// "我的审核"页面里面，如果是组长，取出小组普通成员的下拉列表
		if ($this->app->user->account == 'zhoubenwen' || count($this->checkCollectPlan()) == 0) {
			$members = $this->dao->select('T1.account, T2.realname')->from(TABLE_ICTMEMBSET)->alias('T1')
					->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.account')
					->leftJoin(TABLE_ICTPROTEAM)->alias('T3')->on('T3.id = T1.proteam')
					->where('T3.leader')->eq($this->app->user->account)
					->andWhere('T1.account')->ne($this->app->user->account)
					->orderBy('T1.account')
					->fetchPairs();
		} else {
			// 只要leader不是0，即1或2，对应的就是组长和技术经理了
			$members = $this->dao->select('T1.account, T2.realname')->from(TABLE_ICTMEMBSET)->alias('T1')
			->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.account')
			->where('T1.leader')->eq('0')
			->orderBy('T1.account')
			->fetchPairs();
		}
		// "我的审核"页面里面，如果是科长，取出小组组长的下拉列表
		if(!$members) return array();
		foreach($members as $account => $realName)
		{
			$firstLetter = ucfirst(substr($account, 0, 1)) . ':';
			$users[$account] =  $firstLetter . ($realName ? $realName : $account);
		}
		return array('' => '') + $members;
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
	public function queryWeekPlan($account, $firstDayOfWeek, $passed='')
	{
		
		$weekPlan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->where('T1.account')->eq($account)
		->beginIF($passed != '')->andWhere('T1.confirmed')->eq('通过')->fi()
		->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
		->orderBy('T1.type')
		->fetchAll();
		return $weekPlan;
	}
	
	/**
	 * 根据年份，月份，星期查询周计划(查询下周周计划，包括评审结果和评审意见)
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryNextWeekPlan($account, $firstDayOfWeek)
	{
		$weekPlan = $this->dao->select('T1.*, T2.realname AS submitToName, T3.result, T3.auditComment')->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
		->leftJoin(TABLE_ICTAUDIT)->alias('T3')->on('T3.id = T1.auditId')
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
	 * 待我确认页面--待我确认计划查询(确认条件必须得通过自评,现在改为可评价所有未评价的计划)
	 * 又做变更：改为我的确认页面，一般只确认本周的计划完成情况，所以和下周计划无关，那么plan的status即自评肯定不为空
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
				->andWhere('T1.status IS NOT NULL')
				->orderBy('T1.firstDayOfWeek desc, T1.account, T1.type')
				->fetchAll();
		//未审核周计划
		$uncheckedWeekPlan = $this->dao->select('T1.*, T2.realname AS accountname')
				->from(TABLE_ICTWEEKPLAN)->alias('T1')
				->leftJoin(TABLE_USER)->alias('T2')->on('T1.account = T2.account')
				->where('T1.submitTo')->eq($account)
				->andWhere('T1.confirmedOrNo')->eq('否')
				->andWhere('T1.status IS NOT NULL')
				->orderBy('T1.firstDayOfWeek desc, T1.account, T1.type')
				->fetchAll();
		array_push($myplan, $checkWeekPlan);
		array_push($myplan, $uncheckedWeekPlan);
		return $myplan;
	}
	
	
	/**
	 * 查出各项目组组长已经审核通过的下周计划以及确认通过的本周计划，准备汇总
	 * @param unknown_type $account
	 * @param unknown_type $startDate
	 * @param unknown_type $finishedDate
	 */
	public function queryPassedPlan($account, $thisWeekFirstOfDay, $nextWeekFirstOfDay)
	{
		//已通过周计划
// 		$passedPlan = $this->dao->select('T1.*, T2.realname AS accountname')
// 		->from(TABLE_ICTWEEKPLAN)->alias('T1')
// 		->leftJoin(TABLE_USER)->alias('T2')->on('T1.account = T2.account')
// 		->where('T1.submitTo')->eq($account)
// 		->andWhere('T1.confirmed')->eq('通过')
// 		->orderBy('T1.firstDayOfWeek desc, T1.account, T1.type')
// 		->fetchAll();

		$passedPlan = $this->dao->select('T1.*, T2.realname AS accountname')
		->from(TABLE_ICTWEEKPLAN)->alias('T1')
		->leftJoin(TABLE_USER)->alias('T2')->on('T1.account = T2.account')
		->leftJoin(TABLE_ICTAUDIT)->alias('T3')->on('T3.id = T1.auditId')
		->leftJoin(TABLE_ICTPROTEAM)->alias('T4')->on('T4.leader = T1.account')
		->where('(T1.`firstDayOfWeek` ="'. $thisWeekFirstOfDay. '" AND T1.`confirmed` = "通过")'. 
	 				' OR '. 
      		 '(T1.`firstDayOfWeek` ="'. $nextWeekFirstOfDay. '" AND T3.`result` = "同意")')
		->orderBy('T1.account, T1.firstDayOfWeek desc, T1.type')
		->fetchAll();
		return $passedPlan;
	}
	
	
	/**
	 * 获取周计划（
	 * 【1】本周未确认的或者确认未通过的计划
	 * 【2】本周未审核的或者评审未通过的计划）
	 * @param unknown_type $finishedDate
	 */
	public function queryPlanByTime($firstDayOfWeek)
	{
		$thisWeekPlan = array();
		$account = $this->app->user->account;
		//未确认或者确认未通过(前提是审核通过)
		$roleVal = $this->dao->select('T1.*')->from(TABLE_ICTMEMBSET)->alias('T1')
					->where('T1.account')->eq($account)->andWhere('T1.leader')->eq('2')
					->fetchAll();
		if (empty($roleVal)) {
			//如果不是技术经理
			$unHandleplan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
							->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
							->where('T1.account')->eq($account)
							->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.confirmedOrNo="否" OR T1.confirmed="不通过")')
							->andWhere('T1.auditPass')->eq('1')
							->orderBy('T1.firstDayOfWeek, T1.type')
							->fetchAll();
		} else {
			//如果是技术经理（必须科长审核通过才行）
			$unHandleplan = $this->dao->select('T1.*, T2.realname AS submitToName from ict_my_weekplan T1
									LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
									GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T3
								    on (T3.account = T1.account AND T3.`firstDayOfWeek` = T1.`firstDayOfWeek`)')
									->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
									->where('T1.account')->eq($account)
									->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.confirmedOrNo="否" OR T1.confirmed="不通过")')
									->andWhere('T1.auditPass')->eq('1')
									->andWhere('T3.auditor')->eq('chenxiaobo')
									->orderBy('T1.firstDayOfWeek, T1.type')
									->fetchAll();
		}
		
		//未审核或审核未通过
		if (empty($roleVal)) {
			//如果不是技术经理
			$unAuditplan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
									->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
									->where('T1.account')->eq($account)
									->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
									->andWhere('T1.auditPass')->ne('1')
									->orderBy('T1.firstDayOfWeek, T1.type')
									->fetchAll();
		} else {
			//如果是技术经理（组长审核通过还不够，还需要科长审核）检索条件：审核人不是科长或者审核不通过
			$unAuditplan = $this->dao->select('T1.*, T2.realname AS submitToName, T3.result from ict_my_weekplan T1
									LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
									GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T3
									on (T3.account = T1.account AND T3.`firstDayOfWeek` = T1.`firstDayOfWeek`)')
									->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
									->where('T1.account')->eq($account)
									->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.auditPass != "1" OR T3.auditor != "chenxiaobo")')
									->orderBy('T1.firstDayOfWeek, T1.type')
									->fetchAll();
		}
		
		array_push($thisWeekPlan, $unHandleplan);
		array_push($thisWeekPlan, $unAuditplan);
		return $thisWeekPlan;
	}
	
	// 不允许自己审核自己的
	public function getSubmitToName() 
	{
		$submitToNames = array();
		
		$result = $this->dao->select('*')->from(TABLE_ICTMEMBSET)
				  ->where('account')->eq($this->app->user->account)
				  ->andWhere('leader')->eq('1')
				  ->fetchAll();
		
		//如果是组长，需要选择审核人（二选一）
		if (count($result) > 0)		
		{
			$names = $this->dao->select('T1.auditor1, T2.realname as auditor1_name, T1.auditor2, T3.realname as auditor2_name')
			->from(TABLE_ICTMEMBSET)->alias('T1')
			->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.auditor1')
			->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T1.auditor2')
			->where('T1.account')->eq($this->app->user->account)
			->fetchAll();
			
			$name1->account = $names[0]->auditor1;
			$name1->realname = $names[0]->auditor1_name;
			$name2->account = $names[0]->auditor2;
			$name2->realname = $names[0]->auditor2_name;
			
			array_push($submitToNames, $name1);
			array_push($submitToNames, $name2);
		} else {
			//如果是普通成员，找出其组长
			$names = $this->dao->select('T2.leader, T3.realname')
			->from(TABLE_ICTMEMBSET)->alias('T1')
			->leftJoin(TABLE_ICTPROTEAM)->alias('T2')->on('T2.id = T1.proteam')
			->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T2.leader')
			->where('T1.account')->eq($this->app->user->account)
			->fetchAll();
				
			$name1->account = $names[0]->leader;
			$name1->realname = $names[0]->realname;
				
			array_push($submitToNames, $name1);
		}
		return $submitToNames; 	
	}
	
	
	/**
	 * 获取下周未通过的周计划(审核通过的就不用选了)，此方法和queryPlanByTime一样，暂时略去，不用
	 * 后来因为变更，还是使用
	 * @param unknown_type $finishedDate
	 */
	public function queryNextUnpassPlan($firstDayOfWeek, $account='')
	{
		$dataArr = array();
		if ($account == '') {
			$account = $this->app->user->account;
		}
		
		$roleVal = $this->dao->select('T1.*')->from(TABLE_ICTMEMBSET)->alias('T1')
					->where('T1.account')->eq($account)->andWhere('T1.leader')->eq('2')
					->fetchAll();
		
		if (empty($roleVal)) {
			//如果不是技术经理（找出审核未通过或者未审核的即可）
			$myplan = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
								->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
								->where('T1.account')->eq($account)
								->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
								->andWhere('T1.auditPass')->ne('1')
								->orderBy('T1.firstDayOfWeek, T1.type')
								->fetchAll();
		} else {
			//如果是技术经理（组长审核通过还不够，还需要科长审核）检索条件：审核人不是科长或者审核不通过
			$myplan = $this->dao->select('T1.*, T2.realname AS submitToName, T3.result from ict_my_weekplan T1
								LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
								GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T3
								on (T3.account = T1.account AND T3.`firstDayOfWeek` = T1.`firstDayOfWeek`)')
								->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
								->where('T1.account')->eq($account)
								->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.auditPass != "1" OR T3.auditor != "chenxiaobo")')
								->orderBy('T1.firstDayOfWeek, T1.type')
								->fetchAll();
		}
		
		// 查出审核结果
		$myAuditList = $this->dao->select('T1.*, T2.realname')->from(TABLE_ICTAUDIT)->alias('T1')
							->leftJoin(TABLE_USER)->alias('T2')->on('T2.account = T1.auditor')
							->where('T1.account')->eq($account)
							->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
							->orderBy('T1.auditTime desc')
							->fetchAll();
		array_push($dataArr, $myplan);
		array_push($dataArr, $myAuditList);
		return $dataArr;
	}
	
	/**
	 * "我的审核" “填写下周计划”
	 * @param unknown_type $finishedDate
	 */
	public function queryNextWeekPlans($firstDayOfWeek)
	{
		$account = $this->app->user->account;
		$roleVal = $this->dao->select('T1.*')->from(TABLE_ICTMEMBSET)->alias('T1')
					->where('T1.account')->eq($account)->andWhere('T1.leader')->eq('2')
					->fetchAll();
	//未审核或审核未通过
		if (empty($roleVal)) {
			//如果不是技术经理
			$nextplans = $this->dao->select('T1.*, T2.realname AS submitToName')->from(TABLE_ICTWEEKPLAN)->alias('T1')
									->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
									->where('T1.account')->eq($account)
									->andWhere('T1.firstDayOfWeek')->eq($firstDayOfWeek)
									->andWhere('T1.auditPass')->ne('1')
									->orderBy('T1.firstDayOfWeek, T1.type')
									->fetchAll();
		} else {
			//如果是技术经理（组长审核通过还不够，还需要科长审核）检索条件：审核人不是科长或者审核不通过
			$nextplans = $this->dao->select('T1.*, T2.realname AS submitToName, T3.result from ict_my_weekplan T1
									LEFT JOIN ( SELECT * FROM ict_audit WHERE auditTime IN (SELECT MAX(auditTime) FROM ict_audit
									GROUP BY account, firstDayOfWeek) ORDER BY account, firstDayOfWeek) T3
									on (T3.account = T1.account AND T3.`firstDayOfWeek` = T1.`firstDayOfWeek`)')
									->leftJoin(TABLE_USER)->alias('T2')->on('T1.submitTo = T2.account')
									->where('T1.account')->eq($account)
									->andWhere('T1.firstDayOfWeek="'. $firstDayOfWeek. '" AND (T1.auditPass != "1" OR T3.auditor != "chenxiaobo")')
									->orderBy('T1.firstDayOfWeek, T1.type')
									->fetchAll();
		}
	
		return $nextplans;
	}
	
	/**
	 * 批量更新周计划（自评周计划）
	 * */
// 	public function batchUpdateWeekplan() {
		
// 	}
	
	
	/**
	 * 批量增加下周周计划
	 * 或者本周周计划（未被审核或者审核未通过）
	 */
	public function myBatchCreate($firstDayOfWeek, $lastDayOfWeek)
	{
		$plans = fixer::input('post')->get();
		$delIds = $plans->nextIds;

		//没有的id都删掉，并且通过的不要删除
		$this->dao->delete()->from(TABLE_ICTWEEKPLAN)
		->where('id')->notin($delIds)
		->andWhere('account')->eq($this->app->user->account)
		->andWhere('firstDayOfWeek="'. $firstDayOfWeek. '" AND (confirmed IS NULL OR confirmed="不通过")')
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
	
				//找出本周六的日期和上周五的日期
				$plan->firstDayOfWeek	   = $firstDayOfWeek;
				$plan->lastDayOfWeek	   = $lastDayOfWeek;
	
				$plan->submitTo		= $plans->submitTo[$i];
				
				//$plan->submitOrNo   = '1';
				if (empty($plans->nextIds[$i])) {
					$this->dao->insert(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->exec();
				} else {
					//审核不通过，重新改，更新，此时，将计划状态设为“未审核”
					$plan->auditPass = '2';
// 					$plan->confirmedOrNo = '否';
// 					$plan->confirmed = NULL;
					$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()->where('id')->eq($plans->nextIds[$i])->exec();
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
			//将备注改为空
			$plan->confirmedOrNo = '否';
			$plan->confirmed	= '';
			$plan->remark = '';
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
						->orderBy('T1.firstDayOfWeek desc, T1.type')
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
		$proteam = $this->dao->select('T1.id, T1.team, T1.leader, T3.realname AS leaderName, T4.realname AS managerName')
					->from(TABLE_ICTPROTEAM)->alias('T1')
					->leftJoin(TABLE_ICTMEMBSET)->alias('T2')->on('T2.proteam = T1.id AND T2.leader = "2"')
					->leftJoin(TABLE_USER)->alias('T3')->on('T3.account = T1.leader')
					->leftJoin(TABLE_USER)->alias('T4')->on('T4.account = T2.account')
					->orderBy('T1.team')
					->fetchAll();
		return $proteam;
	}
	/**
	 *  保存项目组设定信息
	 */
	public function saveProteam()
	{
		if (isset($_POST['team']) && isset($_POST['leader'])) {
			$teamData->team   = $_POST['team'];
			$teamData->leader = $_POST['leader'];
			$this->dao->insert(TABLE_ICTPROTEAM)->data($teamData)->autoCheck()
			->check('team','unique')->check('leader','unique')->exec();
			
			// 将组长插到 memberset表中
			// 查出新建 项目组的 id
			$proteamId = $this->getNewTeamId($_POST['leader']);
			$data->account = $_POST['leader'];
			$data->proteam = $proteamId->id;
			$data->leader  = '1';
			$this->dao->insert(TABLE_ICTMEMBSET)->data($data)->autoCheck()
			->check('account','unique')->exec();
			
			if (isset($_POST['leader'])) {
				$managers = $_POST['techmanager'];
				$techCount = count($managers);
				// 将技术经理 插入到 memberset表中 
				for ($i = 0; $i < $techCount; $i++){
					$data->account = $managers[$i];
					$data->proteam = $proteamId->id;
					$data->leader  = '2';
					$this->dao->insert(TABLE_ICTMEMBSET)->data($data)->autoCheck()
					->check('account','unique')->exec();
				}				
			}
		}
		
		if (dao::isError()){
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	/**
	 * 看memberset表中 是否存在 用户account
	 */
	public function checkAccountInMemberset($account = '') {
		$result = true;
		$departmentName = $this->dao->select('T2.name AS name')->from(TABLE_ICTUSER)->alias('T1')
		->leftJoin(TABLE_DEPT)->alias('T2')->on('T2.id = T1.dept')
		->where('account')->eq($account)
		->fetch();
		if (empty($departmentName)) {
			$result = false;
		}
		return $result;
	}
	/**
	 * 查出新建 项目组的 id, 根据$account获取
	 */
	public function getNewTeamId($account = '') {
		$teamId = $this->dao->select('id')->from(TABLE_ICTPROTEAM)
		->where('leader')->eq($account)
		->fetch();
		return $teamId;
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
			$this->dao->insert(TABLE_ICTMEMBSET)->data($data)->check('account','unique')->exec();
		}
	}
	/**
	 * 查询已保存的小组成员
	 */
	public function queryMembUser()
	{
		$memberUser = $this->dao->select('t1.*,t2.realname,t3.team,t5.realname AS leadname,t4.name,"" as rel1,"" as rel2')
		->from(TABLE_ICTMEMBSET)->alias('t1')->leftJoin(TABLE_USER)
		->alias('t2')->on('t1.account = t2.account')->leftJoin(TABLE_ICTPROTEAM)->alias('t3')->on('t1.proteam = t3.id')
		->leftJoin(TABLE_USER)->alias('t5')->on('t5.account = t3.leader')
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
		$singleTeam = $this->dao->select('t1.*,t2.realname')
		->from(TABLE_ICTPROTEAM)->alias('t1')->leftJoin(TABLE_USER)->alias('t2')
		->on('t1.leader = t2.account')->where('t1.id')->eq((int)$id)->fetch();
		if (empty($singleTeam))return array();
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
	
	/**
	 * 判断用户是否有查看‘汇总计划’的权限，否则不予显示
	 * 只有科室经理即科长有权限查看以及操作,通过判断role为4来判断
	 */
	public function checkCollectPlan()
	{
		$account = $this->app->user->account;
		if($account == 'zhoubenwen' || $account == 'chenxiaobo') {
			return $this->dao->select('*')->from(TABLE_ICTUSER)
			->where('account')->eq($account)
			->andWhere('role')->eq('4')
			->fetchAll();
		} else {
			return array();
		}
	}
	
	/**
	 * 保存评审意见, 同时更新ict_my_weekplan表中的auditId字段，使之关联起来
	 */
	public function saveAudit()
	{
		//插入ict_audit数据
		$auditData->result = $_POST['result'];
		$auditData->auditComment = $_POST['auditComment'];
		$auditData->account = $_POST['account'];
		$auditData->firstDayOfWeek = $_POST['firstDayOfWeek'];
		$auditData->auditTime = helper::now();	
		$auditData->auditor	= $this->app->user->account;
		
		//如果没有审核，此时就是新增
		$this->dao->insert(TABLE_ICTAUDIT)->data($auditData)->autoCheck()->exec();
		
		//设置ict_my_weekplan的auditPass字段， 同意设为1， 不同意设为0
		$plan->auditPass = '';
		if ('同意' == $auditData->result) {
			$plan->auditPass = '1';
		} else {
			$plan->auditPass = '0';
		}
		
		$this->dao->update(TABLE_ICTWEEKPLAN)->data($plan)->autoCheck()
		->where('account')->eq($_POST['account'])
		->andWhere('firstDayOfWeek')->eq($_POST['firstDayOfWeek'])
		->exec();
		
		if(dao::isError())
		{
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	
	/**
	 * 判断某条计划的责任人是否是技术经理以及最后的审核人是否是其组长
	 */
	public function checkTechManager($lastAuditPlan) {
		if ($lastAuditPlan->result == '同意') {
// 			return $this->dao->select('*')->from(TABLE_ICTPROTEAM)
// 					->where('leader')->eq($lastAuditPlan->auditor)
// 					->andWhere('techmanager')->eq($lastAuditPlan->account)
// 					->fetchAll();
			return $this->dao->select('T1.*')->from(TABLE_ICTPROTEAM)->alias('T1')
					->leftJoin(TABLE_ICTMEMBSET)->alias('T2')->on('T2.proteam = T1.id')
					->where('T1.leader')->eq($lastAuditPlan->auditor)
					->andWhere('T2.account')->eq($lastAuditPlan->account)
					->andWhere('T2.leader')->eq('2')
					->fetchAll();
		} else {
			return array();
		}
	}
	
	/**
	 * 根据用户名 获取 所在科室
	 */
	public function getDepartment($account = '') {
		$departmentName = $this->dao->select('T2.name AS name')->from(TABLE_ICTUSER)->alias('T1')
		->leftJoin(TABLE_DEPT)->alias('T2')->on('T2.id = T1.dept')
		->where('account')->eq($account)
		->fetch();
		if (empty($departmentName))return array();
		return $departmentName;
	}
	
	/**
	 * 保存请假信息
	 */
	public function saveLeave() {
		$leaveData->account 	= $_POST['leaverName'];
		$leaveData->reason 		= $_POST['reason'];
		$leaveData->startTime 	= $_POST['startTime'];
		$leaveData->endTime 	= $_POST['endTime'];
		$leaveData->askTime 	= $_POST['askTime'];
		
		//如果没有审核，此时就是新增
		$this->dao->insert(TABLE_ICTLEAVE)->data($leaveData)->autoCheck()->exec();
		if(dao::isError())
		{
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
		return "success";
	}
	
	/**
	 * 根据项目组id查询组长、项目组姓名
	 * leader为1， 即查组长
	 * leader为2，即查技术经理
	 */
	public function queryNameByProteamId($proteamId = '', $leader)
	{
		return $this->dao->select('t1.account,t2.realname')->from(TABLE_ICTMEMBSET)->alias('t1')
				->leftJoin(TABLE_USER)->alias('t2')->on('t2.account=t1.account')
				->where('t1.proteam')->eq($proteamId)
				->andWhere('t1.leader')->eq($leader)
				->fetchPairs();
	}
	
}