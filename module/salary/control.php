<?php 

class salary extends control
{
	public function __construct()
	    {
	        parent::__construct();
	        $this->loadModel('user');
	        $this->loadModel('dept');
	        $this->loadModel('my')->setMenu();
	    }
	    
	public function monthly($typeID = 1,$finishedDate = 0, $orderBy = '')
	{
		/* set position and header  公共通用部分*/
		$month = date('Y-m-t',time());
		$startMonth = date('Y-m-01',time());
		if ($finishedDate!==0){
			$month=date('Y-m-t',strtotime($finishedDate));
			$startMonth = date('Y-m-01',strtotime($month));
		}
		$this->view->title = $this->lang->salary->common . $this->lang->colon . $this->lang->salary->salaryReport;
		$this->view->position[] 	= $this->lang->salary->common;
		/* Process the order by field. */
		if(!$orderBy) $orderBy = $this->cookie->salaryOrder ? $this->cookie->salaryOrder : 'allRank_desc';
		setcookie('salaryOrder', $orderBy, $this->config->cookieLife, $this->config->webRoot);
// 		if(!$orderBy) $orderBy = $this->session->salaryOrder ? $this->session->salaryOrder : 'count_desc';
// 		$this->session->set('salaryOrder',$orderBy);
		$this->view->orderBy = $orderBy;
		$orderBy = explode('_', $orderBy);
		//$salarys 	  = $this->salary->getSalaryDetail();
		$accountUser				= $this->user->getById($this->app->user->id);
		$standSalary				= $this->salary->getSalary($accountUser->account);
		$this->view->standSalary    = $standSalary;//标准薪资

		/*获取所有排名、科室排名、合署排名--经理*/
		$this->view->mAllRank		= $this->salary->managerRank($accountUser->account,1);
		$this->view->mDeptRank      = $this->salary->managerRank($accountUser->account,2);
		$this->view->mOfficeRank    = $this->salary->managerRank($accountUser->account,3);
		/*普通员工排名*/
		$this->view->staffallRank   = $this->salary->staffRank($accountUser->account,1);
		$this->view->staffDeptRank  = $this->salary->staffRank($accountUser->account,2);
		$this->view->staffOfficeRank = $this->salary->staffRank($accountUser->account,3);
		if (isset($_POST['typeID']))$typeID = $_POST['typeID'];
		if(!empty($_POST['finishedDate'])){
	    	if(empty($_POST['startDate'])){
				$_POST['startDate'] = date('Y-m-01',strtotime($_POST['finishedDate']));
	    	}
			$month = $_POST['finishedDate'];
			$startMonth = $_POST['startDate'];
	    	$startDate = $_POST['startDate'];
	    
		}
		$role = $this->dao->select('*')->from(TABLE_ICTUSER)->where('account')->eq($accountUser->account)->fetch();
		$finishedDate = date('Y-m-d',strtotime("$startMonth + 1 month -1 day"));
		//项目经理
		if ($role->role == 2){
			$this->view->mSingleCount	= $this->salary->mSingleDay($accountUser->account,$startMonth,$month);
			$this->view->managerCount	= $this->salary->getManagerCount($accountUser->account,$startMonth,$month);
// 			a($this->salary->mSingleDay($accountUser->account,$startMonth,$month));
		}
		else if ($role->role==3){
			$this->view->monthHours     = $this->salary->getPersonnelCount($accountUser->account,$startMonth,$month);
			$this->view->singleCount	= $this->salary->getSingleDay($accountUser->account,$startMonth,$finishedDate);
		}
		else if ($role->role == 1 || $role->role == 4){
		/*领导页面显示月薪酬详细*/
			if ($typeID==3){
				$this->view->allPerson		= $this->salary->getAllPerson($startMonth,$finishedDate,$orderBy);
				$this->view->allManager		= $this->salary->getAllManager($startMonth,$finishedDate,$orderBy);
			}else if ($typeID==1){
				$this->view->allStaffHours  = $this->salary->allStaffHours($month);
				$this->view->personNum	    = json_encode($this->salary->queryPersonNum($month));
				$this->view->salaryIncrease = $this->salary->salaryIncrease($month,$orderBy);
			}else if($typeID==2){
				$this->view->eachHours		= $this->salary->queryEachMonth($startMonth,$month);
				$this->view->eachAverage	= $this->salary->queryEachAverage($startMonth,$month);
				$this->view->salaryContrast = $this->salary->salaryContrast($startMonth,$month);
				$this->view->salaryPayAnalysis = $this->salary->salaryPayAnalysis($startMonth,$month, $orderBy);
			}
		}
		$this->view->accumulative	= $this->salary->getDayHours($accountUser->account,date('Y-m',strtotime($month)));
		$this->view->startDate		= $startMonth;
		$this->view->finishDate		= $month;
		$this->view->monthQuery     = date('Y年m',strtotime($month));
		/*区分显示当月和累计页面*/
		$this->view->type			= $typeID;
		$this->display();
	}

	/**
	 * get data to export
	 *
	 * @param  int $projectID
	 * @param  string $orderBy
	 * @access public
	 * @return void
	 */
	public function export($startDate, $finishedDate, $param = 0, $orderBy = ''){
		if ($_POST){
			if(!$orderBy) $orderBy = $this->cookie->salaryOrder ? $this->cookie->salaryOrder : 'allRank_desc';
			$orderBy = explode('_', $orderBy);
			
			$salaryLang   = $this->lang->salary;
			$salaryConfig = $this->config->salary;
			$account = $this->user->getById($this->app->user->id)->account;
			$role = $this->dao->select('*')->from(TABLE_ICTUSER)->where('account')->eq($account)->fetch();
			/* Create field lists. */
			if ($role->role==2){
				$fields = explode(',', $salaryConfig->list->exportFields);
				foreach($fields as $key => $fieldName)
				{
					$fieldName = trim($fieldName);
					$fields[$fieldName] = isset($salaryLang->$fieldName) ? $salaryLang->$fieldName : $fieldName;
					unset($fields[$key]);
				}
				$file = $this->salary->getManagerCount($account,$startDate,$finishedDate);
				$this->post->set('fields', $fields);
				$this->post->set('rows', $file);
				$this->fetch('file', 'export2' . $this->post->fileType, $_POST);
			}else if ($role->role==1 || $role->role==4){
				if ($param == 'staff'){
					$fields = explode(',', $salaryConfig->list->leaderStaff);
					foreach($fields as $key => $fieldName)
					{
						$fieldName = trim($fieldName);
						$fields[$fieldName] = isset($salaryLang->$fieldName) ? $salaryLang->$fieldName : $fieldName;
						unset($fields[$key]);
					}
					$file = $this->salary->getAllPerson($startDate, $finishedDate, $orderBy);
					$this->post->set('fields', $fields);
					$this->post->set('rows', $file);
					$this->fetch('file', 'export2' . $this->post->fileType, $_POST);
				}else if ($param == 'manage'){
					$fields = explode(',', $salaryConfig->list->exportManage);
					foreach($fields as $key => $fieldName)
					{
						$fieldName = trim($fieldName);
						$fields[$fieldName] = isset($salaryLang->$fieldName) ? $salaryLang->$fieldName : $fieldName;
						unset($fields[$key]);
					}
					$file = $this->salary->getAllManager($startDate, $finishedDate, $orderBy);
					$this->post->set('fields', $fields);
					$this->post->set('rows', $file);
					$this->fetch('file', 'export2' . $this->post->fileType, $_POST);
				}
			}else if ($role->role == 3){
				$fields = explode(',', $salaryConfig->list->exportDetails);
				foreach($fields as $key => $fieldName)
				{
					$fieldName = trim($fieldName);
					$fields[$fieldName] = isset($salaryLang->$fieldName) ? $salaryLang->$fieldName : $fieldName;
					unset($fields[$key]);
				}
				$file = $this->salary->getPersonnelCount($account,$startDate,$finishedDate);
				$this->post->set('fields', $fields);
				$this->post->set('rows', $file);
				$this->fetch('file', 'export2' . $this->post->fileType, $_POST);
			}
		}
		$this->display();
	}
	
}