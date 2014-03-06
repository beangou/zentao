<?php
class plan extends control{
	
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('user');
		$this->loadModel('dept');
		$this->loadModel('my')->setMenu();
	}
	/**
	 * 我的计划
	 * @param unknown_type $finish
	 */
	public function myplan($finish = '', $recTotal = 0, $recPerPage = 20, $pageID = 1){
		$finish = date('Y-m-d',time());
		if (!empty($_POST) && !isset($_GET['submit'])){
			$this->plan->batchCreate();
			if (dao::isError())die(js::error(dao::getError()));
		}
		/* Load pager. */
		$this->app->loadClass('pager', $static = true);
		$pager = pager::init($recTotal, $recPerPage, $pageID);
		
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->myplan;
		$this->view->position[] 	= $this->lang->plan->myplan;
// 		if (empty($finish)) $finish = date('Y-m-d',time()+7*24*3600);
		$week = floor(date('W',strtotime($finish)));
		$account = $this->app->user->account;
		
		$myDateArr = $this->getLastAndEndDayOfWeek();
		$this->view->thisWeekPlan = $this->plan->queryPlanByTime($myDateArr[2]);

		//查出下周未通过的周计划（第一天为本周六）
		$this->view->nextWeekPlan = $this->plan->queryNextUnpassPlan($myDateArr[0]);
// 		$this->view->weekPlan		= $this->plan->queryWeekPlan($account, $week, 0, $pager);
// 		$this->view->date           = (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));
// 		$this->view->team			= $this->plan->getTeaminfo();
// 		$this->view->currentPlan	= $this->plan->queryCurrentPlans($account);
// 		$this->view->lastPlan 		= $this->plan->queryLastPlan(date('Y-m-d', time()+7*24*3600));
// 		$this->view->users			= $this->plan->queryUser();
// 		$this->view->pager        	= $pager;
		$this->display();
	}
	
	/**
	 * 按日期查询
	 * */
	public function searchplan() {
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->queryPlan;
		$this->view->position[] 	= $this->lang->plan->queryPlan;
		if (!empty($_POST)){
			$beginDate = $this->post->beginDate;
			$endDate   = $this->post->endDate;
			//在开始时间和结束时间之内
			$planArr = $this->plan->searchplan($this->app->user->account, $beginDate, $endDate);
			if (dao::isError())die(js::error(dao::getError()));
		}
		$this->view->searchPlans = $planArr;
		$this->display();
	}
	
	
// 	public function addWeekPlan() {
// 		$this->plan->addWeekPlan();
// 	}
	
	
	//可以查询上周计划、本周计划、下周计划，以及根据输入时间查询计划
	public function queryplan($goSearch = ''){
		
// 		if (!empty($goSearch)){
// 			$this->locate($this->createLink('plan', 'searchplan'));
// 		}
		
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->queryPlan;
		$this->view->position[] 	= $this->lang->plan->queryPlan;
// 		if (empty($date)) $date = date('Y-m-d',time());
		
		//本周
		$lastWeek = date('Y-m-d', time()-7*24*3600);
		$lastTimeSplit = explode('-', $lastWeek);
		
		//本周
		$thisWeek = date('Y-m-d', time());
		$thisTimeSplit = explode('-', $thisWeek);
		
		//下周
		$nextWeek = date('Y-m-d', time()+7*24*3600);
		$nextTimeSplit = explode('-', $nextWeek);
		
// 		$week = floor(date('W',strtotime($date)));
		
		$account = $this->app->user->account;
		$myDateArr = $this->getLastAndEndDayOfWeek();
		//上周第一天为上上周六
		$this->view->lastPlan		= $this->plan->queryWeekPlan($account, $myDateArr[3]);
		//本周第一天为上周六
		$this->view->weekPlan		= $this->plan->queryWeekPlan($account, $myDateArr[2]);
		//下周第一条为本周六
		$this->view->nextPlan 		= $this->plan->queryWeekPlan($account, $myDateArr[0]);
		
		$this->view->date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
// 		$this->view->team			= $this->plan->getTeaminfo();
		$this->display();
	}
	/**
	 * 待我处理
	 * @param unknown_type $finish
	 */
	public function deal($finish = ''){
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->deal;
		$this->view->position[] 	= $this->lang->plan->deal;
		if (empty($finish)) $finish = date('Y-m-d',time());
		$account = $this->app->user->account;
		$this->view->dealPlan		= $this->plan->getDealPlans($account, $finish);
		$this->view->date           = (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));
		$this->display();
	}
	/**
	 * 成员计划
	 */
	public function members($finish = '')
	{
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->member;
		$this->view->position[] 	= $this->lang->plan->common;
		$startDate = date('Y-m-01',time());
		if (empty($finish)) $finish = date('Y-m-d',time());
		$week = floor(date('W',strtotime($finish)));
		$account = $this->app->user->account;
		if (!empty($_POST))$this->plan->updateCheck();
		$this->view->membPlan		= $this->plan->queryMemberPlan($account, $finish);
		$this->view->date           = (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));
		$this->view->lead			= $this->plan->judgeAuditor($account);
		$this->display();
	}
	/**
	 * 待我审核
	 */
	public function handle()
	{
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->handle;
		$this->view->position[] 	= $this->lang->plan->common;
		$account = $this->app->user->account;
		if (!empty($_POST))$this->plan->updateCheckPlan();
		$myplan 					= $this->plan->queryCheckPlan($account);
		$this->view->checkPlan		= $myplan[0];
		$this->view->uncheckedPlan  = $myplan[1];
// 		$this->view->lead			= $this->plan->judgeAuditor($account);
		$this->display();
	}
	/**
	 * 项目组设定
	 */
	public function proteam(){
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->proteam;
		$this->view->position[] 	= $this->lang->plan->proteam;
		$this->view->users			= $this->plan->queryUser();
		if (!empty($_POST))$this->plan->saveProteam();
		$this->view->proteam		= $this->plan->queryProteam();
		$this->display();
	}
	/**
	 * 成员小组设定
	 */
	public function membset(){
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->membset;
		$this->view->position[] 	= $this->lang->plan->membset;
// 		$this->view->role			= $this->plan->queryRole();
		if (!empty($_POST['proteam'])){
			foreach ($_POST['proteam'] as $key => $value){
				$data->proteam = $value;
				$this->dao->update(TABLE_ICTMEMBSET)->data($data)->where('id')->eq((int)$key)->exec();
			}
			foreach ($_POST['auditor1'] as $key => $value){
				$auditor1->auditor1 = $value;
				$this->dao->update(TABLE_ICTMEMBSET)->data($auditor1)->where('id')->eq((int)$key)->exec();
			}
			foreach ($_POST['auditor2'] as $key => $value){
				$auditor2->auditor2 = $value;
				$this->dao->update(TABLE_ICTMEMBSET)->data($auditor2)->where('id')->eq((int)$key)->exec();
			}
		}
		if (!empty($_POST['members']))$this->plan->saveMembUser();
		$member	= $this->plan->queryMembUser();
		// 		$teams		= array('' => '');
		// 		foreach ($member as $team)
			// 		{
			// 			$teams[$team->proteam] = $team->team;
			// 		}
		$this->view->users	= $this->plan->userNotSet();
		$this->view->members		= $member;
		// 		$this->view->teams			= $teams;
		$this->view->teams			= $this->plan->queryTeam();
		$this->display();
	}
	/**
	 * 月计划完成率
	 * @param unknown_type $start
	 * @param unknown_type $finish
	 */
	public function planRate($start = '', $finish = ''){
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->planrate;
		$this->view->position[] 	= $this->lang->plan->planrate;
		$start = !empty($_POST['start']) ? $_POST['start'] : date('Y-m-01',time());
		$finish = !empty($_POST['finish']) ? $_POST['finish'] : date('Y-m-d',time());
// 		$this->view->date           = (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));
		$this->view->planRate		= $this->plan->getPlanRate($start, $finish);
		$this->view->start			= (int)$start == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($start));;
		$this->view->finish			= (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));;
		$this->display();
	}
	public function planList($account = '', $start = '', $finish = ''){
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->planrate;
		$this->view->position[] 	= $this->lang->plan->planrate;
		$this->view->weekPlan		= $this->plan->getPlanByAccount($account, $start, $finish);
		$this->display();
	}
	/**
	 * 加载所选小组组长
	 * @param unknown_type $id
	 */
	public function ajaxGetLeader($id = '',$memb = ''){
		$leader = $this->plan->querySingleTeam($id);
		if (empty($leader))return;
		if (! isset($leader->leader))$leader->leader = '';
		if (! isset($leader->realname))$leader->realname = '';
		die("<input name='auditor1[$memb]' value='$leader->auditor1' type='hidden'/>
				<input name='auditor2[$memb]' value='$leader->leader' type='hidden'/>
				<input value='$leader->rel1' style='border:0;width: 60px;' readonly/>
				<input value='$leader->realname' style='border:0;width: 60px;' readonly/>");
	}
	/**
	 * 新增
	 */
	public function create($date){
		$date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
		if (!empty($_POST)){
			$this->plan->saveWeekPlan();
			if (dao::isError())die(js::error(dao::getError()));
			die(js::locate($this->createLink('plan', 'myplan', "finish=$date"), 'parent'));
		}
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->weekly;
		$this->view->position[] 	= $this->lang->plan->create;
		$this->view->users			= $this->plan->queryUser();
		$this->view->date			= $date;
		$this->display();
	}
	/**
	 * 批量添加
	 */
	public function batchCreate($date){
		$date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
		if (!empty($_POST)){
			$this->plan->batchCreate();
			if (dao::isError())die(js::error(dao::getError()));
			die(js::locate($this->createLink('plan', 'myplan', "finish=$date"), 'parent'));
		}
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->weekly;
		$this->view->position[] 	= $this->lang->plan->batchCreate;
		$this->view->users			= $this->plan->queryUser();
		$this->view->date			= $date;
		$this->view->lastPlan 		= $this->plan->queryLastPlan($date);
		$this->display();
	}
	/**
	 * 编辑
	 */
	public function edit($planID = '', $from = ''){
		if (!empty($_POST)){
			$this->plan->update($planID, $from);
			if (dao::isError())die(js::error(dao::getError()));
			if(isonlybody()) die(js::reload('parent.parent'));
			if ($from == 'myplan')die(js::locate($this->createLink('plan', 'myplan'), 'parent'));
			else if ($from == 'members')die(js::locate($this->createLink('plan', 'members'), 'parent'));
			else if ($from == 'handle')die(js::locate($this->createLink('plan', 'handle'), 'parent'));
			else if ($from == 'deal')die(js::locate($this->createLink('plan', 'deal'), 'parent'));
		}
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->weekly;
		$this->view->position[] 	= $this->lang->plan->edit;
		$this->view->users			= $this->plan->queryUser();
		$this->view->from			= $from;
		$this->view->plan			= $this->plan->queryPlanByID($planID);
		$this->display();
	}
	/**
	 * 批量编辑
	 * @param unknown_type $from
	 */
	public function batchEdit($from = '', $date = ''){
		$date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
		if ($from == 'planBrowse'){
			$editedPlans  	= array();
			$planIDList 	= $this->post->planIDList ? $this->post->planIDList : array();
			$allPlans		= $this->plan->getAllPlans();
			if (!$allPlans) $allPlans = array();
			foreach ($allPlans as $all){
				if (in_array($all->id, $planIDList)){
					$editedPlans[$all->id]	= $all;
				}
			}
		}
		elseif ($from == 'planBatchEdit'){
			if (!empty($_POST)){
				$this->plan->batchUpdate($from);
			}
			die(js::locate($this->createLink('plan','myplan', "finish=$date"), 'parent'));
		}
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->weekly;
		$this->view->position[] 	= $this->lang->plan->batchEdit;
		$this->view->users			= $this->plan->queryUser();
		$this->view->editedPlans    = $editedPlans;
		$this->view->date = $date;
		$this->display();
	}
	public function batchAction($from = ''){
		if ($from == 'handleBrowse'){
			$plans			= array();
			$planIDList 	= $this->post->planIDList ? $this->post->planIDList : array();
			$allPlans		= $this->plan->getAllPlans();
			if (!$allPlans) $allPlans = array();
			foreach ($allPlans as $all){
				if (in_array($all->id, $planIDList)){
					$plans[$all->id]	= $all;
				}
			}
		}
		else if ($from == 'handleBatchAction'){
			if (!empty($_POST)){
				$this->plan->batchUpdate($from);
			}
			die(js::locate($this->createLink('plan','handle'), 'parent'));
		}
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->batchAction;
		$this->view->position[] 	= $this->lang->plan->batchAction;
		$this->view->users			= $this->plan->queryUser();
		$this->view->actionPlans	= $plans;
		$this->view->lead			= $this->plan->judgeAuditor($this->app->user->account);
		$this->display();
	}
	/**
	 * 删除
	 * @param unknown_type $id
	 * @param unknown_type $module
	 */
	public function delete($id = '', $module = '',$date = '', $confirm = 'no'){
		//我的计划页面删除
		if ($module == 'myplan'){
			if ($confirm == 'no'){
				echo js::confirm($this->lang->plan->confirmDelete, $this->createLink('plan', 'delete', "id=$id&module=$module&date=$date&confirm=yes"));
				exit;
			}
			else {
				$date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
				$this->dao->delete()->from(TABLE_ICTWEEKPLAN)->where('id')->eq((int)$id)->limit(1)->exec();
				die(js::locate($this->createLink('plan','myplan', "finish=$date"), 'parent'));
			}
		}
		//成员设定页面删除
		else if ($module == 'memb'){
			if ($confirm == 'no'){
				echo js::confirm($this->lang->plan->memberDelete, $this->createLink('plan', 'delete', "id=$id&module=$module&date=$date&confirm=yes"));
				exit;
			}else {
				$this->dao->delete()->from(TABLE_ICTMEMBSET)->where('id')->eq((int)$id)->limit(1)->exec();
				die(js::locate($this->createLink('plan','membset'), 'parent'));
			}
		}
		//项目组页面删除小组
		else if ($module == 'proteam'){
			if ($confirm == 'no'){
				echo js::confirm($this->lang->plan->proteamDelete, $this->createLink('plan', 'delete', "id=$id&module=$module&date=$date&confirm=yes"));
				exit;
			}else {
				$this->dao->delete()->from(TABLE_ICTPROTEAM)->where('id')->eq((int)$id)->limit(1)->exec();
// 				$this->dao->delete()->from(TABLE_ICTMEMBSET)->where('proteam')->eq((int)$id)->limit(1)->exec();
				die(js::locate($this->createLink('plan','proteam'), 'parent'));
			}
		}
	}
	/**
	 * 待我审核页面处理周计划
	 * @param unknown_type $planID
	 */
	public function finish($planID){
		if (!empty($_POST)){
			$this->plan->update($planID);
			if(dao::isError()) die(js::error(dao::getError()));
			if(isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('plan', 'handle'), 'parent'));
		}
		$this->view->plan			= $this->plan->queryPlanByID($planID);
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->finish;
		$this->view->position[] 	= $this->lang->plan->finish;
		$this->view->lead			= $this->plan->judgeAuditor($this->app->user->account);
		$this->display();
	}
	/**
	 * 查看功能
	 * @param unknown_type $planID
	 */
	public function copy($planID){
		$this->view->title = $this->lang->plan->common . $this->lang->colon . $this->lang->plan->copy;
		$this->view->position[] 	= $this->lang->plan->copy;
		$this->view->users			= $this->plan->queryUser();
		$this->view->plan			= $this->plan->queryPlanByID($planID);
		$this->display();
	}
	/**
	 * 导出
	 * @param unknown_type $finish
	 */
	public function export($start = '', $finish= '', $from = 'myplan'){
// 		echo $finish.'----'.$from;
		if ($_POST){
			$account = $this->app->user->account;
			$planLang   = $this->lang->plan;
			$planConfig = $this->config->plan;
			if ($from == 'planrate')$fields = explode(',', $planConfig->list->exportRate);
			else $fields = explode(',', $planConfig->list->exportFields);
			foreach($fields as $key => $fieldName)
			{
				$fieldName = trim($fieldName);
				$fields[$fieldName] = isset($planLang->$fieldName) ? $planLang->$fieldName : $fieldName;
				unset($fields[$key]);
			}
			if ($from == 'planrate')$file = $this->plan->getPlanRate($start, $finish);
			else {
				$week = floor(date('W',strtotime($finish)));
				if ($from == 'myplan')$file = $this->plan->queryWeekPlan($account, $week, $finish);
				else if ($from == 'handle')$file = $this->plan->queryMemberPlan($account, $finish);
				foreach ($file as $plan){
					if (isset($plan->type)) $plan->type = $this->lang->plan->types[$plan->type];
					$plan->sort = isset($plan->sort)?$this->lang->plan->abcSort[$plan->sort]:'';
					if (isset($plan->appraise))$plan->appraise = $this->lang->plan->completed[$plan->appraise];
					if (isset($plan->complete))$plan->complete = $this->lang->plan->completed[$plan->complete];
					if (isset($plan->status))$plan->status = $this->lang->plan->handleStatus[$plan->status];
				}
			}
			$this->post->set('fields', $fields);
			$this->post->set('rows', $file);
			$this->fetch('file', 'export2' . $this->post->fileType, $_POST);
		}
		$this->display();
	}
	
	//获取本周六和下周五的日期
	static public function getLastAndEndDayOfWeek() {
		$myDateArr = array();
		//今天是星期几
		$today = date("w");

		//本周六日期
		$thisSaturday = date('Y-m-d', time()+(6-$today)*24*3600);
		//下周五
		$nextFriday = date('Y-m-d', time()+(12-$today)*24*3600);
		//上周六日期
		$lastSaturday = date('Y-m-d', time()-(1+$today)*24*3600);
		//上上周六
		$lastLastSaturday = date('Y-m-d', time()-(8+$today)*24*3600);
		array_push($myDateArr, $thisSaturday);
		array_push($myDateArr, $nextFriday);
		array_push($myDateArr, $lastSaturday);
		array_push($myDateArr, $lastLastSaturday);
		return $myDateArr;
	}
	
}