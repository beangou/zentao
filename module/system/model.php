<?php 

class systemModel extends model
{
	
	/***读取ict_user表的所有用户**/
	public function getUserInfo($account)
	{
		$userInfo = $this->dao->select('distinct u.*,i.realname from ict_user u left join zt_user i on u.account=i.account')
					->where('u.account')->like("%$account%")->fetchAll();
		$this->roleChange($userInfo);
		return $userInfo;
	}
	/**读取zt_user表的所有用户**/
	public function queryZtUser($account)
	{
		$userInfo = $this->dao->select("u.account,u.realname,u.dept,i.standsalary,i.role FROM zt_user u LEFT JOIN ict_user i ON u.account=i.account")
					->where('u.deleted')->eq('0')->andWhere('u.account')->ne('')->andWhere('u.account')->ne('admin')
					->andWhere('u.account NOT IN(SELECT account FROM ict_user)')->andWhere('u.account')->like("%$account%")->fetchAll();
		$this->roleChange($userInfo);
		return $userInfo;
	}
	/*删除人员*/
	public function delete($account){
		return $this->dao->delete()->from(TABLE_ICTUSER)->where('account')->eq($account)->limit(1)->exec();
	}
	/*根据角色显示*/
	public function roleChange($userInfo)
	{
		for ($i=0;$i<count($userInfo);$i++){
			if($userInfo[$i]->role==3)$userInfo[$i]->role='开发人员';
			if($userInfo[$i]->role==2)$userInfo[$i]->role='项目经理';
			if($userInfo[$i]->role==1)$userInfo[$i]->role='管理员';
			if($userInfo[$i]->role==4)$userInfo[$i]->role='科室领导';
		}
		return $userInfo;
	}
	/****根据列表页面传的account查询其它信息****/
	public function querySingleInfo($account)
	{
		if (empty($account)){
			die(js::reload());
		}
		$singleInfo = $this->dao->select("u.account,u.realname,i.standsalary,i.role FROM zt_user u LEFT JOIN ict_user i ON u.account=i.account")
					->where('u.deleted')->eq('0')->andWhere('u.account')->eq($account)->fetch();
		return $singleInfo;
	}
	
	/****保存修改页面信息**/
	public function setStandSalary()
	{
			$data = new stdClass();
			$data->account = $this->post->loginAccount;
			$data->standsalary = $this->post->standSalary;
			$data->role	= $this->post->role;
			$this->dao->update(TABLE_ICTUSER)->data($data)->autoCheck()->where('account')->eq($this->post->loginAccount)->exec();
			die(js::reload('parent'));
	}
	
	/**同步数据到ict_user**/
	public function sysnchronous()
	{
			for ($i=0;$i<count($_POST['accounts']);$i++){
				$data = new stdClass();
				$data->account	= $_POST['accounts'][$i];
				$data->dept	= $this->dao->select('*')->from(TABLE_USER)->where('account')->eq($_POST['accounts'][$i])->fetch()->dept;
				$this->dao->insert(TABLE_ICTUSER)->data($data)->autoCheck()->check('account','unique')->exec();
		}
				die(js::reload('parent'));
	}
	/*查询产品名称信息*/
	public function queryProduct()
	{
		return $this->dao->select('id,name')->from(TABLE_PRODUCT)->where('deleted')->eq('0')->fetchAll();
	}
	/*查询项目经理信息*/
	public function queryDM()
	{
		return $this->dao->select('u.realname,i.account from ict_user i LEFT JOIN zt_user u on i.account=u.account')->where('i.role=2')->fetchAll();
	}
	/*保存项目基本信息设置*/
	public function saveIctProduct()
	{
		if (!isset($_POST['finishedDate']))$_POST['finishedDate'] = $_POST['createDate'];
		$check	 = $this->dao->select('*')->from(TABLE_ICTPRODUCT)->where('productId')->eq($_POST['productId'])->andWhere('project')->eq($_POST['project'])
					->andWhere('DATE_FORMAT(createDate,"%Y-%m")')->eq(date('Y-m',strtotime($_POST['finishedDate'])))->fetchAll();
		if (count($check)>0){
			die(js::reload());
		}
		$this->dao->insert(TABLE_ICTPRODUCT)->data($_POST)->autoCheck()
			->batchCheck($this->config->system->create->requiredFields,'notempty')->exec();
		if(dao::isError())
		{
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	/*查询项目基本信息设置*/
	public function queryProductInfo($startDate){
		$startDate = date('Y-m',strtotime($startDate));
		$productInfo = $this->dao->select('t.name as productName,j.name as projectName,u.realname,p.* FROM ict_product p,
						zt_product t,zt_project j,zt_user u')->where('p.productId=t.id')
						->andWhere('p.project=j.id')->andWhere('p.DM=u.account')
						->andWhere('DATE_FORMAT(p.createDate,"%Y-%m")')->eq($startDate)->fetchAll();
		return $productInfo;
	}
	/*删除项目基本信息设置*/
	public function deleteProduct($productID){
		for ($i=0;$i<count($productID);$i++){
			return $this->dao->delete()->from(TABLE_ICTPRODUCT)->where('id')->eq((int)$productID[$i])->limit(1)->exec();
		}
	}
	public function deleteById($productID){
		return $this->dao->delete()->from(TABLE_ICTPRODUCT)->where('id')->eq((int)$productID)->limit(1)->exec();
	}
	/*更新项目基本信息设置*/
	public function updateProduct(){
		for ($i=0;$i<count($_POST['ids']);$i++){
			$data = new stdClass();
			$data->deptCoeff = $_POST['deptCoeff'][$i];
			$data->coefficient = $_POST['coefficient'][$i];
			$data->partnum = $_POST['partnum'][$i];
			$data->completrate = $_POST['completrate'][$i];
			$this->dao->update(TABLE_ICTPRODUCT)->data($data)->autoCheck()
				->where('id')->eq($_POST['ids'][$i])->exec();
		}
		if(dao::isError())
		{
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	/*查询普通员工故障数*/
	public function queryBug($resolvedDate)
	{
		$queryRewards = $this->dao->select('r.name,r.integratedBug,r.deliverBug,r.onlineBug,r.documentPunish,
							r.rewards,r.delay,r.bonus,r.month,r.total,z.realname,u.account,
							0 AS count FROM ict_user u LEFT JOIN ict_rewards r ON u.account=r.name and date_format(r.month,"%Y-%m")="'.date('Y-m',strtotime($resolvedDate)).'"
							LEFT JOIN zt_user z ON z.account=u.account')
						->where('u.role in (2,3)')->fetchAll();
		$queryBug = $this->dao->select('resolvedBy,COUNT(resolvedBy) AS count,DATE_FORMAT(openedDate,"%Y-%m") as month')->from(TABLE_BUG)
				->where('DATE_FORMAT(openedDate,"%Y-%m")')->eq(date('Y-m',strtotime($resolvedDate)))->andWhere('title')->notLike('%内测%')->groupBy('resolvedBy')->fetchAll();
		for ($j=0;$j<count($queryRewards);$j++){
				for ($i=0;$i<count($queryBug);$i++){
					if (isset($queryBug[$i]->resolvedBy)&&$queryBug[$i]->resolvedBy==$queryRewards[$j]->account){
						$queryRewards[$j]->integratedBug = $queryBug[$i]->count;
					}
					if($queryRewards[$j]->integratedBug=='')$queryRewards[$j]->integratedBug=0;
					if ($queryRewards[$j]->integratedBug<=5)$queryRewards[$j]->rewards=0;
					else if ($queryRewards[$j]->integratedBug>5 && $queryRewards[$j]->integratedBug<=10)$queryRewards[$j]->rewards=$queryRewards[$j]->integratedBug*(-20);
					else if ($queryRewards[$j]->integratedBug>10)$queryRewards[$j]->rewards=$queryRewards[$j]->integratedBug*(-40);
					$queryRewards[$j]->total = $queryRewards[$j]->rewards+$queryRewards[$j]->delay+$queryRewards[$j]->bonus;
				}
				$queryRewards[$j]->month = $resolvedDate;
		}
		return $queryRewards;
	}
	/****查询月工时增减项***/
	public function queryIncrease($resolvedDate)
	{
		$resolvedDate = date('Y-m',strtotime($resolvedDate));
		return $this->dao->select('u.account,z.realname,i.* FROM ict_user u LEFT JOIN ict_increase i ON u.account=i.name and DATE_FORMAT(i.month,"%Y-%m")='."'$resolvedDate'".' left join zt_user z on u.account=z.account')
				->where('u.role in (2,3)')->fetchAll();
	}
	
	/*保存月工时增减项*/
	public function saveIncrease($month)
	{
		for ($i=0;$i<count($_POST['increaseName']);$i++){
			$num = $this->dao->select('*')->from(TABLE_ICTINCREASE)->where('name')->eq($_POST['increaseName'][$i])->andWhere('date_format(month,"%Y-%m")')->eq(date('Y-m',strtotime($month)))->fetch();
			$data = new stdClass();
			$data->name = $_POST['increaseName'][$i];
			$data->master = $_POST['master'][$i];
			$data->creative = $_POST['creative'][$i];
			$data->patent = $_POST['patent'][$i];
			$data->report = $_POST['report'][$i];
			$data->codeQuality = $_POST['codeQuality'][$i];
			$data->total = $_POST['total'][$i];
			if (isset($num->name)){
				$this->dao->update(TABLE_ICTINCREASE)->data($data)->check('name','notempty')->where('name')->eq($_POST['increaseName'][$i])->andWhere('date_format(month,"%Y-%m")')->eq(date('Y-m',strtotime($month)))->exec();
			}else{
				$data->month = $month;
				$this->dao->insert(TABLE_ICTINCREASE)->data($data)->check('name','notempty')->check('month','notempty')->exec();
			}
		}
// 		die(js::reload('parent'));
		if(dao::isError())
		{
			echo js::error(dao::getError());
			die(js::reload('parent'));
		}
	}
	/*保存质量奖惩*/
	public function saveRewards($month)
	{
		for ($i=0;$i<count($_POST['name']);$i++){
			$num = $this->dao->select('*')->from(TABLE_ICTREWARDS)->where('name')->eq($_POST['name'][$i])->andWhere('date_format(month,"%Y-%m")')->eq(date('Y-m',strtotime($month)))->fetch();
			$data = new stdClass();
			$data->name = $_POST['name'][$i];
			$data->integratedBug = $_POST['integratedBug'][$i];
			$data->deliverBug = $_POST['deliverBug'][$i];
			$data->onlineBug = $_POST['onlineBug'][$i];
			$data->documentPunish = $_POST['documentPunish'][$i];
			$data->rewards = $_POST['rewards'][$i];
			$data->delay = $_POST['delay'][$i];
			$data->bonus = $_POST['bonus'][$i];
			$data->total = $_POST['total'][$i];
			if (isset($num->name)){
				$this->dao->update(TABLE_ICTREWARDS)->data($data)->where('name')->eq($_POST['name'][$i])->andWhere('date_format(month,"%Y-%m")')->eq(date('Y-m',strtotime($month)))->exec();
			}else{
				$data->month = $month;
				$this->dao->insert(TABLE_ICTREWARDS)->data($data)->exec();
			}
			
		}
// 		die(js::reload('parent'));
			if(dao::isError())
			{
				echo js::error(dao::getError());
				die(js::reload('parent'));
			}
	}
}
