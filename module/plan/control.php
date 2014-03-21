<?php

require_once('class.phpmailer.php'); //载入PHPMailer类

class plan extends control{
	
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('user');
		$this->loadModel('dept');
		$this->loadModel('my')->setMenu();
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
		
		// $mail->AddEmbeddedImage("logo.jpg", "my-attach", "logo.jpg"); //设置邮件中的图片
// 		$mail->Body = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.helloweba.com"
// 		target="_blank">helloweba.com</a>的邮件！<br/>
// 		<img alt="helloweba" src="cid:my-attach">'; //邮件主体内容
		
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
		if (!empty($_POST) && !isset($_GET['submit'])){
			$getParam = $this->post->isSubmit;
			if ($getParam == '0') {
				//自评本周周计划
				$this->plan->evaluateMyPlan();
				$this->view->evaluateResult = 'true';
				$this->view->createResult = '';
			} else {
				//填写下周周计划
				$this->plan->myBatchCreate($myDateArr[0]);
				$this->view->createResult = 'true';
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
		//查出本周自评未审核或者评审未通过的计划
		$this->view->thisWeekPlan = $this->plan->queryPlanByTime($myDateArr[2]);
		//查出下周未评审或评审未通过的周计划（第一天为本周六）
		$this->view->nextWeekPlan = $this->plan->queryNextUnpassPlan($myDateArr[0]);
// 		$this->view->submitTos	  = $this->plan->getSubmitToName();
		
		$this->view->date           = $myDateArr[1];
		
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
		$this->view->mymenu = $mymenu;
		
		$this->view->users			= $this->plan->queryUser();
// 		$this->view->weekPlan		= $this->plan->queryWeekPlan($account, $week, 0, $pager);
// 		$this->view->date           = (int)$finish == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($finish));
// 		$this->view->team			= $this->plan->getTeaminfo();
// 		$this->view->currentPlan	= $this->plan->queryCurrentPlans($account);
// 		$this->view->lastPlan 		= $this->plan->queryLastPlan(date('Y-m-d', time()+7*24*3600));
// 		$this->view->users			= $this->plan->queryUser();
// 		$this->view->pager        	= $pager;
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
		//下周第一条为本周六
		$this->view->nextPlan 		= $this->plan->queryNextWeekPlan($account, $myDateArr[0]);
		
		$this->view->date           = (int)$date == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($date));
		
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	   = '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
		$this->view->mymenu = $mymenu;
		
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
		$this->dealArrForRowspan($myplan[0], 'firstDayOfWeek');
		$this->dealArrForRowspan($myplan[1], 'firstDayOfWeek');
		$this->view->checkPlan		= $myplan[0];
		$this->view->uncheckedPlan  = $myplan[1];
		
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
		$this->view->mymenu = $mymenu;
		
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
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
		$this->view->mymenu = $mymenu;
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
		// 		$teams		= array('' => '');
		// 		foreach ($member as $team)
			// 		{
			// 			$teams[$team->proteam] = $team->team;
			// 		}
		$this->view->users	= $this->plan->userNotSet();
		$this->view->members		= $member;
		// 		$this->view->teams			= $teams;
		$this->view->teams			= $this->plan->queryTeam();
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
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
	static public function select1($name = '', $options = array(), $attrib = '')
	{
		$options = (array)($options);
		if(!is_array($options) or empty($options)) return "<select id='$id' $attrib><option>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option></select>";
	
		$id = $name;
		$string = "<select name='$name' id='$id' $attrib>\n";
	
		foreach($options as $key => $value)
		{
			$string  .= "<option value='$key'>$value</option>\n";
		}
	
		return $string .= "</select>\n";
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
	 * 验证用户是否有权限进行“汇总计划”
	 */
	public function checkCollectPlan()
	{
		$result = $this->plan->checkCollectPlan();
		
		if(count($result)==0) {
			return true;
		}
		return false;
	}
	
	/**
	 * 汇总计划(汇总并发送计划到指定邮箱)
	 */
	public function collectPlan()
	{
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
		$this->view->mymenu = $mymenu;
		$daysArr = $this->getLastAndEndDayOfWeek();
		if (!empty($_POST) && $this->post->email != '') {
		$this->generateExcl($daysArr[0], $daysArr[2]);
		$this->view->info = $this->sendEmail($this->post->email);
		}
		$account = $this->app->user->account;
		$this->view->passedPlan = $this->plan->queryPassedPlan($account, $daysArr[2], $daysArr[0]);
		$this->display();
	}
	
	/**
	 * 生成excl文件
	 */
	public function generateExcl($nextWeekFirstDay, $thisWeekFirstDay) 
	{
		/**
		 * PHPExcel
		 *
		 * Copyright (C) 2006 - 2014 PHPExcel
		 *
		 * This library is free software; you can redistribute it and/or
		 * modify it under the terms of the GNU Lesser General Public
		 * License as published by the Free Software Foundation; either
		 * version 2.1 of the License, or (at your option) any later version.
		 *
		 * This library is distributed in the hope that it will be useful,
		 * but WITHOUT ANY WARRANTY; without even the implied warranty of
		 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
		 * Lesser General Public License for more details.
		 *
		 * You should have received a copy of the GNU Lesser General Public
		 * License along with this library; if not, write to the Free Software
		 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
		 *
		 * @category   PHPExcel
		 * @package    PHPExcel
		 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
		 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
		 * @version    1.8.0, 2014-03-02
		 */
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		/** Include PHPExcel */
		require_once '../Classes/PHPExcel.php';
		
		
		// Create new PHPExcel object
// 		echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
// 		echo date('H:i:s') , " Set document properties" , EOL;
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("PHPExcel Test Document")
		->setSubject("PHPExcel Test Document")
		->setDescription("Test document for PHPExcel, generated using PHP classes.")
		->setKeywords("office PHPExcel php")
		->setCategory("Test result file");
		
		
		// Add some data
// 		echo date('H:i:s') , " Add some data" , EOL;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '时间')
		->setCellValue('B1', '负责人')
		->setCellValue('C1', '按ABC分类')
		->setCellValue('D1', '本周事项')
		->setCellValue('E1', '行动计划')
		->setCellValue('F1', '完成时限')
		->setCellValue('G1', '完成情况')
		->setCellValue('H1', '见证性材料')
		->setCellValue('I1', '未完成原因说明及如何补救');
		
		
		$account = $this->app->user->account;
		$myplanList = $this->plan->queryPassedPlan($account, $thisWeekFirstDay, $nextWeekFirstDay);
// 		$this->dealArrForRowspan($myplan[0], 'firstDayOfWeek');
// 		$this->view->checkPlan		= $myplan[0];
// 		$this->view->uncheckedPlan  = $myplan[1];
		
		
		$i = 1;
		foreach ($myplanList as $myplan)
		{
			$i++;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'. $i, $myplan->firstDayOfWeek. ' ~ '. $myplan->lastDayOfWeek)
			->setCellValue('B'. $i, $myplan->accountname)
			->setCellValue('C'. $i, $myplan->type)
			->setCellValue('D'. $i, $myplan->matter)
			->setCellValue('E'. $i, $myplan->plan)
			->setCellValue('F'. $i, $myplan->deadtime)
			->setCellValue('G'. $i, $myplan->status)
			->setCellValue('H'. $i, $myplan->evidence)
			->setCellValue('I'. $i, $myplan->courseAndSolution);
		}
		// Miscellaneous glyphs, UTF-8
// 		$objPHPExcel->setActiveSheetIndex(0)
// 		->setCellValue('A4', 'Miscellaneous glyphs')
// 		->setCellValue('A5', '江山代有才人出，各领风骚数百年');
		
		
// 		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
// 		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
// 		$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
		
		
		// Rename worksheet
// 		echo date('H:i:s') , " Rename worksheet" , EOL;
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Save Excel 2007 file
// 		echo date('H:i:s') , " Write to Excel2007 format" , EOL;
// 		$callStartTime = microtime(true);
		
// 		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// 		$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
// 		$callEndTime = microtime(true);
// 		$callTime = $callEndTime - $callStartTime;
		
// 		echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// 		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// 		// Echo memory usage
// 		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
		
		
// 		// Save Excel 95 file
// 		echo date('H:i:s') , " Write to Excel5 format" , EOL;
		$callStartTime = microtime(true);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		
		return $myplanList;
		
// 		echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// 		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// 		// Echo memory usage
// 		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
		
		
// 		// Echo memory peak usage
// 		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
		
// 		// Echo done
// 		echo date('H:i:s') , " Done writing files" , EOL;
// 		echo 'Files have been created in ' , getcwd() , EOL;
	}
	
	/**
	 * 我的审核
	 */
	public function audit() {
		//提交意见
		if (!empty($_POST) && !empty($_POST['weekPlanId'])) {
			$this->plan->saveAudit();			
		}
		
		$mymembers = $this->plan->queryMyMember();
		$this->view->mymembers = $mymembers;  
		$mymenu['myplan']      = '我的计划';//主要是新增计划可以查看
		$mymenu['queryplan']   = '查询计划';//只可查询，不可操作
		$mymenu['handle']      = '我的确认';
		$mymenu['audit']	   = '我的审核';
		$mymenu['proteam'] 	= '项目组设定';
		$mymenu['membset']     = '成员设定';
		if (!$this->checkCollectPlan()) {
			$mymenu['collectplan']     = '汇总计划';
		}
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
							'<td>'.$plan->matter. '</td>'.
							'<td>'.$plan->plan. '</td>'.
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
			//下周第一条为本周六
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
							'<td>'.$plan->matter. '</td>'.
							'<td>'.$plan->plan. '</td>'.
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
		}
		
	}
	
}