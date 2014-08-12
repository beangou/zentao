<?php

require_once('class.phpmailer.php'); //载入PHPMailer类

error_reporting(E_ALL);

class plan extends control{
	
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('user');
		$this->loadModel('dept');
		$this->loadModel('my')->setMenu();
	}
	
	/**
	 * 重写方法
	 */
	/**
	 * Print link icon.
	 *
	 * @param  string $module
	 * @param  string $method
	 * @param  string $vars
	 * @param  object $object
	 * @param  string $type button|list
	 * @param  string $icon
	 * @param  string $target
	 * @static
	 * @access public
	 * @return void
	 */
	public static function printIcon($event = '')
	{
		echo html::a('', '&nbsp;', 'empty', "class='icon-green-common-edit' title='编辑' target='_self' ". $event, false);
// 		html::linkButton();
// 		echo '<button class="icon-green-common-edit" '. $event. '>编辑</button>'; 
	}
	
	public function editproteaminfo() {
		if(!empty($_POST))
		{
			$this->plan->editProteamInfo();
			if(dao::isError()) die(js::error(dao::getError()));
			die('<script>parent.parent.location.reload();</script>');
		}
		$infoId = $_GET['infoId'];
		$proteamInfo = plan::dealArrForOneRow($this->plan->searchProteamInfo($infoId), 'id', 'managerName');
		$this->view->proteamInfo = $proteamInfo[0];
		$useNotSet = $this->plan->userNotSet();
		// 把原先的组长 加到 选择组长的select中
		
		// 把原先的技术经理 加到 选择技术经理的select中
		
		$this->view->users			= $useNotSet; 
		$this->display();
	}
	
	/**
	 * 查看成员计划(科长、周总可以查看各个组长、技术经理的计划，各个组长可以查看组内成员的计划，)
	 * 这样，周总可以查看组长、技术经理，以及其组内成员的计划
	 */
	public function querymemberplan()
	{
		$myDateArr = $this->getLastAndEndDayOfWeek();
		if (!empty($_GET['account'])) {
			$paraAccount = $_GET['account'];
			$lastWeekPlan = $this->plan->queryWeekPlan($paraAccount, $myDateArr[3]);
			$thisWeekPlan = $this->plan->queryWeekPlan($paraAccount, $myDateArr[2]);
			$nextWeekPlan = $this->plan->queryNextWeekPlan($paraAccount, $myDateArr[0]);
			
			$thisWeekAudits = $this->plan->queryNextUnpassPlan($myDateArr[2], $paraAccount);
			$nextWeekAudits = $this->plan->queryNextUnpassPlan($myDateArr[0], $paraAccount);
			$this->view->thisWeekAudits = $thisWeekAudits[1];
			$this->view->nextWeekAudits = $nextWeekAudits[1];
			
			$this->view->lastWeekPlan = $lastWeekPlan;
			$this->view->thisWeekPlan = $thisWeekPlan;
			$this->view->nextWeekPlan = $nextWeekPlan; 
			
			$this->view->memberVal    = $paraAccount; 
		}
		
		$this->view->firstOfThisWeekDay = $myDateArr[2];
		$this->view->lastOfThisWeekDay = $myDateArr[4];
		$this->view->firstOfNextWeekDay = $myDateArr[0];
		$this->view->lastOfNextWeekDay = $myDateArr[1];
		$this->view->firstOfLastWeekDay = $myDateArr[3];
		$this->view->lastOfLastWeekDay = $myDateArr[5];
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
		$this->view->mymember = $this->plan->queryMemberForQuery();
		$this->display();
	}
	
	/**
	 * 发送邮件
	 */
	public function sendEmail($email)	
	{
		$mail = new PHPMailer(); //实例化
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host = "smtp.163.com"; //SMTP服务器 以163邮箱为例子
		$mail->Port = 25;  //邮件发送端口
		$mail->SMTPAuth   = true;  //启用SMTP认证
		
		$mail->CharSet  = "UTF-8"; //字符集
		$mail->Encoding = "base64"; //编码方式
		
		$mail->Username = "15955552919@163.com";  //你的邮箱
		$mail->Password = "abcde12345";  //你的密码
		$mail->Subject = "ict周计划汇总"; //邮件标题
		
		$mail->From = "15955552919@163.com";  //发件人地址（也就是你的邮箱）
		$mail->FromName = "ict禅道系统";  //发件人姓名
		
// 		$address = "15955552919@163.com";//收件人email
		$address = $email;
		$mail->AddAddress($address, "ict周计划审核人");//添加收件人（地址，昵称）
		
		$mail->AddAttachment('control.xls','ict周计划汇总.xls'); // 添加附件,并指定名称
		$mail->IsHTML(true); //支持html格式内容
		
		$mail->Body = '您好! <br/>这是一封来自安徽移动ict禅道系统的邮件！<br/>'; //邮件主体内容
		
		//发送
		if(!$mail->Send()) {
// 			echo "Mailer Error: " . $mail->ErrorInfo;
			return $mail->ErrorInfo;
		} else {
// 			echo "Message sent!";
			return '<script>alert("发送成功!")</script>';
		}
    }
    
	/**
	 * 我的计划
	 * @param unknown_type $finish
	 */
	public function myplan($finish = '', $recTotal = 0, $recPerPage = 20, $pageID = 1){
		$finish = date('Y-m-d',time());
		$myDateArr = $this->getLastAndEndDayOfWeek();
		if (!empty($_POST) && !isset($_GET['submit'])) {
// 		if ($_POST &&  $_POST['submit']) {
			$getParam = $this->post->isSubmit;
			if ($getParam == '0') {
				//自评本周周计划
				$this->plan->evaluateMyPlan();
				$this->view->evaluateResult = 'true';
				$this->view->createResult = '';
				$this->view->changeResult = '';
			} else if ($getParam == '1') {
				//填写下周周计划
				$this->plan->myBatchCreate($myDateArr[0], $myDateArr[1]);
				$this->view->createResult = 'true';
				$this->view->evaluateResult = '';
				$this->view->changeResult = '';
			} else if ($getParam == '2') {
				//修改本周周计划
				$this->plan->myBatchCreate($myDateArr[2], $myDateArr[4]);
				$this->view->changeResult = 'true';
				$this->view->createResult = '';
				$this->view->evaluateResult = '';
			}
			
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
		
		$this->view->firstOfThisWeekDay = $myDateArr[2];
		$this->view->lastOfThisWeekDay = $myDateArr[4];
		$this->view->firstOfNextWeekDay = $myDateArr[0];
		$this->view->lastOfNextWeekDay = $myDateArr[1];
		
		$thisWeekPlan = $this->plan->queryPlanByTime($myDateArr[2]);
		//查出本周自评未确认或者确认未通过的计划
		$this->view->thisWeekPlan = $thisWeekPlan[0];
		//本周未审核或者审核未通过
		$this->view->thisWeekUnAuditPlan = $thisWeekPlan[1];
		
		//查出下周未评审或评审未通过的周计划（第一天为本周六）
		$nextAllPlan = $this->plan->queryNextWeekPlan($account, $myDateArr[0]);
		$nextUnpassPlan = $this->plan->queryNextUnpassPlan($myDateArr[0]);
		// 如果下周计划的数量和下周未通过的数量一样（包括都为0），说明所有的计划都没通过，需要显示在“我的计划”里面
		if (count($nextAllPlan) == count($nextUnpassPlan[0])) {
			$this->view->showFlag = '1';
			// 如果没有下周周计划， 则显示空行
			if (count($nextAllPlan) > 0) {
				$this->view->showBlankLine = 'none';
			}
		}
		$this->view->nextWeekPlan = $nextUnpassPlan[0];
		
		// 审核记录
		$this->view->auditList	  = $nextUnpassPlan[1];
		
		// 修改本周周计划中的审核记录
		$changeUnpassPlan = $this->plan->queryNextUnpassPlan($myDateArr[2]);
		$this->view->changeAuditList = $changeUnpassPlan[1];
		
		$this->view->date           = $myDateArr[1];
		
		// “填写下周计划”、“修改本周计划”是否可写(组长审核通过后，由于还要交给科长审核，此时该计划不可编辑)
		if (count($nextUnpassPlan[1]) > 0) {
			//填写下周计划
			if (count($this->plan->checkTechManager($nextUnpassPlan[1][0])) > 0) {
				$this->view->nextplanEditable = 'true';
			}
		} else {
			$this->view->nextplanEditable = 'true';
		}
		
		if (count($changeUnpassPlan[1]) > 0) {
			//修改本周计划
			//填写下周计划
			if (count($this->plan->checkTechManager($changeUnpassPlan[1][0])) > 0) {
				$this->view->thisplanEditable = 'true';
			}
		} else {
			$this->view->thisplanEditable = 'true';
		}
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
		$this->view->users	= $this->plan->queryUser();

		$this->display();
	}
	
	public function ajaxGetDate() {
		$myDateArr = $this->getLastAndEndDayOfWeek();
		die(html::input('deadtime[]', date('Y-m-d',strtotime($myDateArr[1])), "class='select-2 date'"));
	}
	/**
	 * 查询计划详情
	 * */
	public function searchfordetail() {
		$planId = $_GET['planId'];
		$myDetailPlan = $this->plan->searchForDetail($planId);
		$this->view->detailPlan = $myDetailPlan; 
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
			$this->dealArrForRowspan($planArr, 'firstDayOfWeek');
			if (dao::isError())die(js::error(dao::getError()));
		}
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
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
		
		$this->view->firstOfThisWeekDay = $myDateArr[2];
		$this->view->lastOfThisWeekDay = $myDateArr[4];
		$this->view->firstOfNextWeekDay = $myDateArr[0];
		$this->view->lastOfNextWeekDay = $myDateArr[1];
		$this->view->firstOfLastWeekDay = $myDateArr[3];
		$this->view->lastOfLastWeekDay = $myDateArr[5];
		
		//上周第一天为上上周六
		$this->view->lastPlan		= $this->plan->queryWeekPlan($account, $myDateArr[3]);
		//本周第一天为上周六
		$this->view->weekPlan		= $this->plan->queryWeekPlan($account, $myDateArr[2]);
		//下周第一天为本周六
		$this->view->nextPlan 		= $this->plan->queryNextWeekPlan($account, $myDateArr[0]);
		//查看下周的审核记录
		$nextUnpassPlan = $this->plan->queryNextUnpassPlan($myDateArr[0]);
		$this->view->auditList = $nextUnpassPlan[1]; 
		$this->view->date      = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
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
	 * 待我确认
	 */
	public function handle()
	{
		$this->view->title = $this->lang->my->common . $this->lang->colon . $this->lang->plan->handle;
		$this->view->position[] 	= $this->lang->plan->common;
		$account = $this->app->user->account;
		if (!empty($_POST))$this->plan->updateCheckPlan();
		$myplan 					= $this->plan->queryCheckPlan($account);
		$this->dealArrForRowspan($myplan[0], 'firstDayOfWeek');
		$this->dealArrForRowspan($myplan[1], 'firstDayOfWeek');
		$this->view->checkPlan		= $myplan[0];
		$this->view->uncheckedPlan  = $myplan[1];
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
		$this->display();
	}
	/**
	 * 项目组设定
	 */
	public function proteam(){
		$this->view->title 			= $this->lang->my->common . $this->lang->colon . $this->lang->plan->proteam;
		$this->view->position[] 	= $this->lang->plan->proteam;
		if (!empty($_POST))$this->plan->saveProteam();
// 		$this->view->contactLists   = $this->user->getContactLists($this->app->user->account, 'withnote');
		// 组长和技术经理都是从 未设定的成员里面 选择，设定成功后， 会自动执行 成员设定
		$this->view->users			= $this->plan->userNotSet();
		$this->view->proteam		= plan::dealArrForOneRow($this->plan->queryProteam(), 'id', 'managerName');
		$this->view->mymenu 		= plan::dealMenu($this->app->user->account);
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
// 				$data->leader = '1';
				$data->proteam = $value;
				$this->dao->update(TABLE_ICTMEMBSET)->data($data)->where('id')->eq((int)$key)->exec();
			}
		}
		if (!empty($_POST['members']))$this->plan->saveMembUser();
		$member	= $this->plan->queryMembUser();
		
		$this->view->users	= $this->plan->userNotSet();
		$this->view->members		= $member;
		// 		$this->view->teams			= $teams;
		$this->view->teams			= $this->plan->queryTeam();
		
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymenu = $mymenu;
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
		die("$leader->realname");
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
	 * 编辑项目组
	 */
	public function editproteam() {
		die(js::locate($this->createLink('plan','proteam'), 'parent'));
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
				// 删除项目组，不仅要 删除proteam， 还要删除memberset
				$this->dao->delete()->from(TABLE_ICTPROTEAM)->where('id')->eq((int)$id)->limit(1)->exec();
				$this->dao->delete()->from(TABLE_ICTMEMBSET)->where('proteam')->eq((int)$id)->exec();
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
		//如果今天是星期六，那么下个星期就是下个星期六，故
		if ($today == 6) {
			$today = -1;
		}

		//下周周计划第一天（本周六）
		$thisSaturday = date('Y-m-d', time()+(6-$today)*24*3600);
		//本周周计划第一天（上周六）
		$lastSaturday = date('Y-m-d', time()-(1+$today)*24*3600);
		//上周计划第一天（上上周六）
		$lastLastSaturday = date('Y-m-d', time()-(8+$today)*24*3600);
		//下周周计划最后一天（下周五）
		$nextFriday = date('Y-m-d', time()+(12-$today)*24*3600);
		//本周最后一天为本周五
		$thisFriday = date('Y-m-d', time()+(5-$today)*24*3600);
		//上周计划最后一天（上周五）
		$lastFriday = date('Y-m-d', time()-(1+$today)*24*3600);
		
		array_push($myDateArr, $thisSaturday);
		array_push($myDateArr, $nextFriday);
		array_push($myDateArr, $lastSaturday);
		array_push($myDateArr, $lastLastSaturday);
		array_push($myDateArr, $thisFriday);
		array_push($myDateArr, $lastFriday);
		return $myDateArr;
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
	
		foreach($options as $obj)
		{
			$string  .= "<option value='$obj->account'>$obj->realname</option>\n";
		}
	
		return $string .= "</select>\n";
	}
	
	/**
	 * 处理有一定顺序的数组，是根据其中某个key设置rowspan以表格形式显示到页面上来,返回的数组中某些元素多了rowspanVal的值
	 * @param  array $temp          the name of the select tag.
	 */
	static public function dealArrForRowspan($temp = array(), $key = '')
	{
		$rowspanIndex = 0;
		$rowspanValue = 0;
		for ($i=0; $i<count($temp); $i++){
			if ($temp[$i]->$key == $temp[$rowspanIndex]->$key) {
				$rowspanValue++;
			} else {
				$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
				$rowspanValue = 1;
				$rowspanIndex = $i;
			}
		}
		//这有当数组有数据时，才给rowspanVal赋值，否则，没意义，多一条没用的数据
		if ($rowspanValue > 0) {
			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
		}
		/* End. */
		return $temp;
	}
	
	/**
	 * 处理有一定顺序的数组,将多行 相同的字段 合并为一行， 不同的字段 的内容 合起来放在一行
	 * @param  array $temp          the name of the select tag.
	 */
	static public function dealArrForOneRow($temp = array(), $sameKey = '', $diffKey = '')
	{
		// 返回数组
		$resultArr = array();
		// 需要添加到数组 的元素 索引
		$colIndex = 0;
		// 值不同的字段 值拼接后的值  
		$colValue = '';
		$arrLen = count($temp);
		
		for ($i=0; $i<$arrLen; $i++) {
			if ($temp[$i]->$sameKey == $temp[$colIndex]->$sameKey) {
				// 如果多行字段的 值相同， 将 不同字段 的值 拼接起来
				$colValue .= $temp[$i]->$diffKey. '  ';
			} else {
				// 如果多行字段的 值 不相同， ， 先将 原先的不同的值 存入数组，   需要 将 该不同字段的值 记录下来赋值给 colValue
// 				$temp[$colIndex]->$diffKey = substr($colValue, 0, strlen($colValue)-1);
				$temp[$colIndex]->$diffKey = $colValue;
				array_push($resultArr, $temp[$colIndex]);
				$colIndex = $i;
				$colValue = $temp[$i]->$diffKey. '  ';
			}
		}
		//这有当数组有数据时，需要将最后一条数据 记录进去
		if ($arrLen > 0) {
// 			$temp[$colIndex]->$diffKey = substr($colValue, 0, strlen($colValue)-1);
			$temp[$colIndex]->$diffKey = $colValue;
			array_push($resultArr, $temp[$colIndex]);
		}
		/* End. */
		return $resultArr;
	}
	
	/**
	 * 我的审核
	 */
	public function audit() {
		//提交意见
		if (!empty($_POST) && !empty($_POST['weekPlanId'])) {
			$this->plan->saveAudit();
		} else if (!empty($_GET['account']) && !empty($_GET['firstDayOfWeek'])) {
			//根据传过来的参数查询计划
			$this->view->unAuditPlans   = $this->plan->queryNextWeekPlan($_GET['account'], $_GET['firstDayOfWeek']);
			$this->view->realname       = '(&nbsp;'. $_GET['realname']. ' ';
			$this->view->firstDayOfWeek = $_GET['firstDayOfWeek']. ' ~ ';
			$this->view->lastDayOfWeek  = $_GET['lastDayOfWeek']. ')';
			
			$auditList = $this->plan->queryNextUnpassPlan($_GET['firstDayOfWeek'], $_GET['account']);
			$this->view->auditList = $auditList[1];
		}
		$this->view->unAuditPlansAlink = $this->plan->queryUnauditForAlink($this->app->user->account); 
		$mymembers = $this->plan->queryMyMember();
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->mymembers = $mymembers;
		$this->view->mymenu = $mymenu;
		$this->display();
	}
	
	/**
	 * 我的审核获取本周和下周计划
	 */
	public function ajaxGetPlan($account='', $flag='') {
		$planArr = array();
		$myDateArr = $this->getLastAndEndDayOfWeek();
		if ($flag == 0) {
			//本周第一天为上周六
			$planArr		= $this->plan->queryWeekPlan($account, $myDateArr[2], 'passed');
			if (count($planArr)  == 0) {
				die('<tr><td class="stepID" colspan="10" style="text-align: right;">'. '暂时没有记录'. '</td></tr>');
			} else {
				foreach ($planArr as $plan) {
					$planStr.= '<tr class="a-center"><td>'.$plan->type. 
							'</td>'.
							'<td style="text-align:left">'.$plan->matter. '</td>'.
							'<td style="text-align:left">'.$plan->plan. '</td>'.
							'<td>'.$plan->deadtime. '</td>'.
							'<td>'.$plan->status. '</td>'.
							'<td>'.$plan->evidence. '</td>'.
							'<td>'.$plan->courseAndSolution. '</td>'.
							'<td>'.$plan->submitToName. '</td>'.
							'<td>'.$plan->confirmed. '</td>'.
							'<td>'.$plan->remark. '</td></tr>';
				}
				die($planStr);
			}
		} else if ($flag == 1) {
			//下周第一天为本周六
			$planArr		= $this->plan->queryNextWeekPlan($account, $myDateArr[0]);
			if (count($planArr)  == 0) {
				$planStr.= '<tr><td class="stepID" colspan="5" style="text-align: right;">'. '暂时没有记录'. '</td></tr><script>';
				$planStr.= '$("#resultYes").attr("checked", true);';
					$planStr.= '$("#auditComment").val("");';
				$planStr.= '</script>';
				die($planStr);
			} else {
				foreach ($planArr as $plan) {
					$planStr.= '<tr class="a-center"><td>'.$plan->type. 
							'<input type="hidden" name="weekPlanId[]" value="'. $plan->id. '">
							<input type="hidden" name="weekAuditId[]" value="'. $plan->auditId. '"></td>'.
							'<td style="text-align:left">'.$plan->matter. '</td>'.
							'<td style="text-align:left">'.$plan->plan. '</td>'.
							'<td>'.$plan->deadtime. '</td>'.
							'<td>'.$plan->submitToName. '</td></tr>';
					$planStr.= '<script>';
					if ($plan->result == '不同意') {
						$planStr.= '$("#resultNo").attr("checked", true);';
					} else {
						$planStr.= '$("#resultYes").attr("checked", true);';
					} 
					if (empty($plan->auditComment)) {
						$planStr.= '$("#auditComment").val("");';
					} else {
						$planStr.= '$("#auditComment").val("'. $plan->auditComment. '");';
					}
					$planStr.= '</script>';
				}
				die($planStr);
			} 
		} else if ($flag == 2) {
			//查出本周所有未审核的计划
			$planArr		= $this->plan->queryUnauditPlan($account, $myDateArr[2]);
			if (count($planArr)  == 0) {
				$planStr.= '<tr><td class="stepID" colspan="5" style="text-align: right;">'. '暂时没有记录'. '</td></tr><script>';
// 				$planStr.= '$("#resultYes").attr("checked", true);';
// 				$planStr.= '$("#auditComment").val("");';
				$planStr.= '</script>';
				die($planStr);
			} else {
				
				foreach ($planArr as $plan) {
					$planStr.= '<tr class="a-center"><td>'.$plan->type.
					'<input type="hidden" name="weekPlanId[]" value="'. $plan->id. '">
							<input type="hidden" name="weekAuditId[]" value="'. $plan->auditId. '"></td>'.
										'<td style="text-align:left">'.$plan->matter. '</td>'.
										'<td style="text-align:left">'.$plan->plan. '</td>'.
										'<td>'.$plan->deadtime. '</td>'.
										'<td>'.$plan->submitToName. '</td></tr>';
				}
				die($planStr);
			}
		}
		
	}
	
	// 请假登记
	public function leave() {
		$saveResult = '';
		if (!empty($_POST)) {
			$saveResult = $this->plan->saveLeave();
		}
		$mymenu = plan::dealMenu($this->app->user->account);
		$this->view->users	= $this->plan->queryUser();
		$this->view->mymenu = $mymenu;
		$this->view->saveResult = $saveResult;
		$this->display();
	}
	
	// 处理 “计划管理” 菜单
	static public function dealMenu($account) {
		$mymenu['myplan']      		= '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   		= '查询计划';//只可查询，不可操作
		$mymenu['handle']      		= '我的确认';
		$mymenu['audit']	   		= '我的审核';
		$mymenu['querymemberplan']  = '查看成员计划';
		$mymenu['proteam'] 			= '项目组设定';
		$mymenu['membset']     		= '成员设定';
		// 验证是否有 “登记请假”的权限， 由于只有“周本文”有权限，故只用判断用户名即可
		if ($account == 'zhoubenwen') {
			$mymenu['leave']     = '请假登记';
		}
		return $mymenu;
	}
	
	/**
	 * 请假登记  根据请假人姓名 获取其 所在部门
	 */
	public function ajaxGetDepatment($account='') {
		$department = $this->plan->getDepartment($account);
		if (empty($department))return;
		if (! isset($department->name))$leader->name = '';
		if (! isset($department->name))$leader->name = '';
		die("$department->name");
	}
	
}