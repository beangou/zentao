<?php 

class salaryModel extends model
{
	
	/**
	 * 
	 */
	public function getSalaryDetail(){
		return $this->dao->select('*')->from(TABLE_USER)
			   ->where('deleted')->eq(0);
	}
	
	
	/**********************************普通员工************************************************/
	public function getSingleDay($account,$startDate,$finishedDate)
	{
		$getPersonnelCount = $this->getPersonnelCount($account, $startDate, $finishedDate);
		$singleCount	= array();
		for($i=0;$i<count($getPersonnelCount);$i++){
			if($getPersonnelCount[$i]->year==date('Y-m',strtotime($finishedDate)))
				array_push($singleCount,$getPersonnelCount[$i]);
		}
		return $singleCount;
	}
	public function mSingleDay($account,$startDate,$finishedDate)
	{
		$getPersonnelCount = $this->getManagerCount($account, $startDate, $finishedDate);
		$singleCount	= array();
		for($i=0;$i<count($getPersonnelCount);$i++){
			if($getPersonnelCount[$i]->year==date('Y-m',strtotime($finishedDate))){
				array_push($singleCount,$getPersonnelCount[$i]);
// 				break;
			}
		}
			return $singleCount;
	}
	
	
	
	
	/*普通员工信息*/
	public function getPersonnelCount($account,$startDate,$finishedDate)
	{
		$dept	= $this->getDept($account)->dept;
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$countTime = $this->dao->select('SUM(estimate) as count,DATE_FORMAT(finishedDate,"%m") AS date,
					DATE_FORMAT(finishedDate,"%Y-%m") AS year,0 as bug,0 as personnelCoeff,0 as measure,
					0 as finalSalary,0 as deptCoeff,0 as bonus,0 as standsalary,"" as account,"" as realname, 
					"" as dept,"" as allRank,"" as deptRank,"" as officeRank,"" as deptName')
					 ->from(TABLE_TASK)->where('DATE_FORMAT(finishedDate,"%Y-%m")')
					 ->between($startDate,$finishedDate)->andWhere('finishedBy')->eq($account)->andWhere('deleted')->eq('0')
					 ->groupBy('DATE_FORMAT(finishedDate,"%Y-%m")')->fetchAll();
		for($i=0;$i<count($countTime);$i++){
			$countTime[$i]->account = $account;
			$realname = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($account)->fetch();
			$countTime[$i]->realname = $realname->realname;
			$countTime[$i]->dept = $dept;
				$deptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($dept)->fetch();
				$countTime[$i]->deptName = isset($deptName->name)?$deptName->name:"";
			if($countTime[$i]->count == '')$countTime[$i]->count=0;
		}
		$bugNum	= $this->getPersonnelBug($account, $startDate, $finishedDate);
		for($k=0;$k<count($bugNum);$k++){
			for($j=0;$j<count($countTime);$j++){
				if(isset($bugNum[$k]->date)&&$countTime[$j]->year==$bugNum[$k]->date){
					$countTime[$j]->bug = $bugNum[$k]->count;
				}
			}
		}
		/*个人兑现系数*/
		$deptCoeff = $this->getPersonelCount1($account,$startDate,$finishedDate);
		for($m=0;$m<count($countTime);$m++){
			for($n=0;$n<count($deptCoeff);$n++){
				if(isset($deptCoeff[$n]->date)&& $countTime[$m]->year==$deptCoeff[$n]->date)$countTime[$m]->personnelCoeff=$deptCoeff[$n]->personnelCoeff;
			}
		}
		/*部室月度产品系数*/
		$officeCoeff = $this->getDeptCoeff1($startDate, $finishedDate);
		for ($i=0;$i<count($countTime);$i++){
			for ($j=0;$j<count($officeCoeff);$j++){
				if (isset($officeCoeff[$j]->date)&& $countTime[$i]->year==$officeCoeff[$j]->date)$countTime[$i]->deptCoeff=$officeCoeff[$j]->deptCoeff;
			}
		}
		/*个人奖金和其他工资信息*/
		$bonus = $this->getIctUser($account, $startDate, $finishedDate);
		for ($i=0;$i<count($countTime);$i++){
			$countTime[$i]->standsalary = $this->queryStandSalary($account)->standsalary;
			for ($j=0;$j<count($bonus);$j++){
				if($countTime[$i]->year==$bonus[$j]->date){
					$countTime[$i]->bonus=$bonus[$j]->bonus;
				}
			}
		}
		$getStaffRank = $this->getStaffRank($account, $startDate, $finishedDate);
		for ($i=0;$i<count($countTime);$i++){
			for ($j=0;$j<count($getStaffRank);$j++){
				if (isset($getStaffRank[$j]->date) && $countTime[$i]->year==$getStaffRank[$j]->date){
					$countTime[$i]->deptRank = isset($getStaffRank[$j]->deptRank)?$getStaffRank[$j]->deptRank:1;
					$countTime[$i]->allRank = $getStaffRank[$j]->allRank;
					if ($countTime[$i]->dept==6)$countTime[$i]->officeRank = $getStaffRank[$j]->officeRank;
				}
			}
		}
		for($a=0;$a<count($countTime);$a++){
			$countTime[$a]->measure = $this->getSalary($account)->standsalary*0.6*$countTime[$a]->personnelCoeff*$countTime[$a]->deptCoeff;
			$countTime[$a]->finalSalary = $this->getSalary($account)->standsalary*0.4+$countTime[$a]->measure+$countTime[$a]->bug+$countTime[$a]->bonus;
		}
		return $countTime;
	}
	
	public function getPersonnelBug($account,$startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$bugNum = $this->dao->select('name,(rewards+delay+documentPunish) AS count,DATE_FORMAT(month,"%Y-%m") AS date')
				->from(TABLE_ICTREWARDS)->where('name')->eq($account)
				->andWhere('DATE_FORMAT(month,"%Y-%m")')->between($startDate,$finishedDate)
				->groupBy('DATE_FORMAT(month,"%Y-%m")')->fetchAll();
// 		$bugNum = $this->dao->select('COUNT(*) as count,DATE_FORMAT(resolvedDate,"%m") AS date')->from(TABLE_BUG)
// 				  ->where("DATE_FORMAT(resolvedDate,'%Y-%m-%d')")
// 				  ->between($startDate,$finishedDate)->andWhere('resolvedBy')->eq($account)
// 				  ->andWhere('deleted')->eq('0')->groupBy("DATE_FORMAT(resolvedDate,'%Y-%m')")->fetchAll();
// 		for($i=0;$i<count($bugNum);$i++){
// 				if ( 5<= $bugNum[$i]->count && $bugNum[$i]->count<=10)$bugNum[$i]->count=(-20)*$bugNum[$i]->count;
// 				else if ($bugNum[$i]->count>10)$bugNum[$i]->count=$bugNum[$i]->count*(-40);
// 				else $bugNum[$i]->count=0;
// 		}
		return $bugNum;
	}
	
	/*获取部室月度产品系数*/
	public function getDeptCoeff1($startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
// 		$deptCoeff = $this->dao->select('ROUND(SUM(coefficient* completrate)/SUM(completrate),2) AS deptCoeff,DATE_FORMAT(finishedDate,"%Y-%m") AS date')
// 		->from(TABLE_ICTPRODUCT)->where('date(finishedDate)')->between($startDate,$finishedDate)
// 		->groupBy("DATE_FORMAT(finishedDate,'%Y-%m')")
// 		->fetchAll();
		$deptCoeff = $this->dao->select('DISTINCT DATE_FORMAT(createDate,"%Y-%m") AS date,deptCoeff')->from(TABLE_ICTPRODUCT)
					->where('date_format(createDate,"%Y-%m")')->between($startDate,$finishedDate)->andWhere('deptCoeff')->ne('')
					->fetchAll();
		return $deptCoeff;
	}
	
	/**
	 * 获取ict_user表信息(奖金)
	 * @param unknown_type $account
	 */
	public function getIctUser($account,$startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$getRewards = $this->dao->select('name,rewards,bonus,DATE_FORMAT(month,"%Y-%m") AS date,0 as standsalary')->from(TABLE_ICTREWARDS)->where('name')
					->eq($account)->andWhere('DATE_FORMAT(month,"%Y-%m")')->between($startDate,$finishedDate)
					->groupBy('DATE_FORMAT(month,"%Y-%m")')->fetchAll();
		return $getRewards;
	}
	/*单独获取标准薪资*/
	public function queryStandSalary($account){
		$getSalary = $this->dao->select('account,standsalary')->from(TABLE_ICTUSER)
		->where('account')->eq($account)->fetch();
		return $getSalary;
	}
	
	/***************************************项目经理时间***********************************************/
	public function getManagerCount($account,$startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$dept	= $this->getDept($account)->dept;
		$mCountTime = $this->dao->select('SUM(estimate) as count,DATE_FORMAT(finishedDate,"%m") AS date,
					  DATE_FORMAT(finishedDate,"%Y-%m") AS year,0 as bug,0 as otherCoeff,1 as manageCoeff,
					  0 as measure,0 as finalSalary,0 as standsalary,0 as bonus,"" as account,"" as realname, 
					  "" as dept,"" as allRank,"" as deptRank,"" as officeRank,"" as deptName')
					  ->from(TABLE_TASK)->where('DATE_FORMAT(finishedDate,"%Y-%m")')
					 ->between($startDate,$finishedDate)->andWhere('finishedBy')->eq($account)->andWhere('deleted')->eq('0')
					 ->groupBy('DATE_FORMAT(finishedDate,"%Y-%m")')->fetchAll();
		$bugNum	= $this->getPersonnelBug($account, $startDate, $finishedDate);
		for($j=0;$j<count($mCountTime);$j++){
			$mCountTime[$j]->standsalary = $this->queryStandSalary($account)->standsalary;
			$deptName = $this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($dept)->fetch();
			$mCountTime[$j]->dept = $dept;
			$mCountTime[$j]->deptName = $deptName->name;
			$mCountTime[$j]->account = $account;
			$realname = $this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($account)->fetch();
			$mCountTime[$j]->realname = $realname->realname;
			for($k=0;$k<count($bugNum);$k++){
				if(isset($bugNum[$k]->date) && $mCountTime[$j]->year==$bugNum[$k]->date){
					$mCountTime[$j]->bug = $bugNum[$k]->count;
				}
			}
		}
// 		$getOtherCoeff	= $this->getOtherCoeff1($startDate, $finishedDate);
// 		for($m=0;$m<count($mCountTime);$m++){
// 			for ($n = 0; $n < count($getOtherCoeff); $n++) {
// 				if($mCountTime[$m]->year==$getOtherCoeff[$n]->date && $getOtherCoeff[$n]->DM==$account){
// 					$mCountTime[$m]->otherCoeff = $getOtherCoeff[$n]->otherCoeff;
// 				}
// 			}
// 		}
// 		$getAllProject = $this->getAllManagerCoeff1($startDate, $finishedDate);
// 		$coeffByMonth  = array();
// 		if(count($getAllProject)>1){
// 		for ($i=0;$i<count($mCountTime);$i++){
// 			for ($j=0;$j<count($getAllProject);$j++){
// 				if($mCountTime[$i]->year==$getAllProject[$j]->date)array_push($coeffByMonth,$getAllProject[$j]);
// 			}
// 			rsort($coeffByMonth);
// 			$if = 0;
// 			foreach($coeffByMonth as $search){
// 				$if++;
// 				if(strpos($search->DM, $account)!== false){
// 					break;
// 				}
// 			}
// 			$len = count($coeffByMonth);//长度
// 			$coeff = 0;//最终系数
// 			if($if<=round($len*0.2))$mCountTime[$i]->manageCoeff=1.2;
// 			else if($if>round($len*0.21) && $if<=round($len*0.8))$mCountTime[$i]->manageCoeff=1;
// 			else $mCountTime[$i]->manageCoeff=0.9;
			$coeffByMonth  = array();
// 		}
// 		}else if(count($getAllProject)==1){
// 			for($i=0;$i<count($mCountTime);$i++){
// 				if(isset($getAllProject->date) && $mCountTime[$i]->year==$getAllProject->date && $getAllProject->DM==$account)
// 					$mCountTime[$i]->manageCoeff = 1;
// 			}
// 		}
		$queryManagerCoeff = $this->queryManagerCoeff($account,$startDate, $finishedDate);
		for ($i=0;$i<count($mCountTime);$i++){
			for ($j=0;$j<count($queryManagerCoeff);$j++){
				if (isset($queryManagerCoeff[$j]->date) && $mCountTime[$i]->year==$queryManagerCoeff[$j]->date &&
						$mCountTime[$i]->account==$queryManagerCoeff[$j]->DM){
					$mCountTime[$i]->manageCoeff = $queryManagerCoeff[$j]->sum;
				}
			}
		}
		$queryAllOtherCoeff = $this->queryAllOtherCoeff($startDate, $finishedDate);
		for ($i=0;$i<count($mCountTime);$i++){
			for ($j=0;$j<count($queryAllOtherCoeff);$j++){
				if (isset($queryAllOtherCoeff[$j]->date) && $mCountTime[$i]->year==$queryAllOtherCoeff[$j]->date &&
						$mCountTime[$i]->account==$queryAllOtherCoeff[$j]->DM){
					$mCountTime[$i]->otherCoeff = $queryAllOtherCoeff[$j]->sum;
				}
			}
		}
		/*个人奖金和其他工资信息*/
		$bonus = $this->getIctUser($account, $startDate, $finishedDate);
		for ($i=0;$i<count($mCountTime);$i++){
			for ($j=0;$j<count($bonus);$j++){
				if($mCountTime[$i]->year==$bonus[$j]->date){
					$mCountTime[$i]->bonus=$bonus[$j]->bonus;
				}
			}
		}
		/************排名***********************/
		$allPerson = $this->queryRank($account, $startDate, $finishedDate);
		$mSingleDay = array();
		for ($i=0;$i<count($mCountTime);$i++){
			for ($j=0;$j<count($allPerson);$j++){
				if ($mCountTime[$i]->year==$allPerson[$j]->date){
					$mCountTime[$i]->allRank = $allPerson[$j]->allRank;
					$mCountTime[$i]->deptRank = $allPerson[$j]->deptRank;
					if ($mCountTime[$i]->dept==6)$mCountTime[$i]->officeRank = $allPerson[$j]->officeRank;
				}
			}
		}
		
		for ($a=0;$a<count($mCountTime);$a++){
			$mCountTime[$a]->measure = $this->getSalary($account)->standsalary*0.6*($mCountTime[$a]->manageCoeff+$mCountTime[$a]->otherCoeff);
			$mCountTime[$a]->finalSalary = $this->getSalary($account)->standsalary*0.4+$mCountTime[$a]->measure+$mCountTime[$a]->bug+$mCountTime[$a]->bonus;
		}
		/********/
		return $mCountTime;
	}
	
	/*获取产品i的工作量 2013/08/08*/
	public function queryTaskI($startDate,$finishedDate){
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
// 		$queryTask = $this->dao->select('i.project,i.DM,DATE_FORMAT(i.finishedDate,"%Y-%m") AS date,u.dept,
// 					 SUM(z.estimate) as sum,ROUND(SUM(z.estimate)*i.coefficient*i.completrate/i.partnum,2) 
// 					 AS averge FROM ict_product i LEFT JOIN zt_task z ON i.project = z.project AND 
// 					 DATE_FORMAT(z.finishedDate,"%Y-%m") = DATE_FORMAT(i.finishedDate,"%Y-%m") left join zt_user u on u.account=i.DM')
// 					 ->where('DATE_FORMAT(i.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
// 					->groupBy('i.project,i.DM,DATE_FORMAT(i.finishedDate,"%Y-%m"),z.project,DATE_FORMAT(z.finishedDate,"%Y-%m")')
// 					->fetchAll();
		$queryTask = $this->dao->select('p.product,i.DM,DATE_FORMAT(z.finishedDate,"%Y-%m") AS date,u.dept,
					SUM(z.estimate) as sum,ROUND(SUM(z.estimate)*i.coefficient*i.completrate/i.partnum,2)
					AS averge FROM zt_task z, zt_projectProduct p,ict_product i,zt_user u')
					->where('z.project = p.project')->andWhere('p.product=i.productId')
					->andWhere('DATE_FORMAT(z.finishedDate,"%Y-%m") = DATE_FORMAT(i.finishedDate,"%Y-%m")')
					->andWhere('u.account=i.DM')->andWhere('DATE_FORMAT(i.finishedDate,"%Y-%m")')
					->between($startDate,$finishedDate)
					->groupBy('p.product,DATE_FORMAT(z.finishedDate,"%Y-%m")')->fetchAll();
		for ($i=0;$i<count($queryTask)-1;$i++){
			for ($j=$i+1;$j<count($queryTask);$j++){
				if ($queryTask[$i]->date==$queryTask[$j]->date){
					if ($queryTask[$i]->DM==$queryTask[$j]->DM){
						$queryTask[$i]->averge = $queryTask[$i]->averge + $queryTask[$j]->averge;
						$queryTask[$i]->sum = $queryTask[$i]->sum + $queryTask[$j]->sum;
						unset($queryTask[$j]);
						$queryTask = array_values($queryTask);
					}
				}
			}
		}
		$data = array();
		$length = abs(date("Y",strtotime($finishedDate))-date("Y",strtotime($startDate)))*12+date("m",strtotime($finishedDate))-date("m",strtotime($startDate));
// 		$length = date('m',strtotime($finishedDate))-date('m',strtotime($startDate));
		for ($i=0;$i<$length+1;$i++){
			for ($j=0;$j<count($queryTask);$j++){
				if (isset($queryTask[$j]->date) && $queryTask[$j]->date==date('Y-m',strtotime(date('Y',strtotime($startDate)).'-'.(date('m',strtotime($startDate))+$i)))){
					$data[$i][$j] = $queryTask[$j];
					$data[$i] = array_values($data[$i]);
				}
			}
		}
		$data = array_values($data);
		for ($a=0;$a<count($data);$a++){
			if (count($data[$a])>1){
				for ($b=0;$b<count($data[$a])-1;$b++){
					for ($c=0;$c<count($data[$a])-$b-1;$c++){
						if ($data[$a][$c]->averge<$data[$a][$c+1]->averge){
							$temp = new stdClass();
							$temp = $data[$a][$c];
							$data[$a][$c] = $data[$a][$c+1];
							$data[$a][$c+1] = $temp;
						}
					}
				}
			}
		}
		for ($i=0;$i<count($data);$i++){
			$temp = 0;
			for ($j=0;$j<count($data[$i]);$j++){
				$temp ++;
				$count = round($temp/count($data[$i]),2);
				if ($count<=0.2)$data[$i][$j]->sum=1.2;
				else if ($count>=0.21 && $count<=0.8)$data[$i][$j]->sum=1;
				else if ($count>0.8)$data[$i][$j]->sum=0.9;
			}
		}	
		return $data;
	}
	/*获取经理项目管理系数值 sum*/
	public function queryManagerCoeff($account,$startDate,$finishedDate){
		$queryTask = $this->queryTaskI($startDate, $finishedDate);
		$ary = array();
		for ($i=0;$i<count($queryTask);$i++){
			for ($j=0;$j<count($queryTask[$i]);$j++){
				if ($queryTask[$i][$j]->DM==$account)array_push($ary, $queryTask[$i][$j]);
			}
		}
		return $ary;
	}
	
	/*根据月份条件获取项目经理其他工作绩效*/
	public function queryAllOtherCoeff($startDate,$finishedDate){
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
// 		$otherCof = $this->dao->select('DATE_FORMAT(z.finishedDate,"%Y-%m") AS date,i.DM,
// 					ROUND(SUM(z.estimate)*i.coefficient,2) AS sum,p.product,u.dept FROM zt_task z,zt_projectProduct p,ict_product i,zt_user u')
// 					->where('z.finishedBy=i.DM')->andWhere('i.productId=p.product')->andWhere('z.project=p.project')->andWhere('u.account=i.DM')
// 					->andWhere('DATE_FORMAT(z.finishedDate,"%Y-%m") = DATE_FORMAT(i.finishedDate,"%Y-%m")')
// 					->andWhere('DATE_FORMAT(z.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
// 					->groupBy('i.DM,DATE_FORMAT(z.finishedDate,"%Y-%m")')->fetchAll();
// 		$noProjectC = $this->dao->select('DATE_FORMAT(t.finishedDate,"%Y-%m") AS date,t.finishedBy as DM,SUM(t.estimate) AS sum,
// 						p.product,i.dept FROM zt_task t,zt_projectProduct p,ict_user u,zt_user i')
// 						->where('t.project=p.project')->andWhere('p.product=3')->andWhere('t.finishedBy=u.account')->andWhere('i.account=u.account')->andWhere('u.role=2')
// 						->andWhere('DATE_FORMAT(t.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
// 						->groupBy('t.finishedBy,DATE_FORMAT(t.finishedDate,"%Y-%m")')->fetchAll();
		$otherCof = $this->dao->select('DATE_FORMAT(z.finishedDate,"%Y-%m") AS date,SUM(z.estimate) AS sum,
					z.finishedBy AS DM,u.dept FROM zt_task z,ict_user i,zt_user u')
					->where('z.finishedBy=i.account')->andWhere('i.account=u.account')->andWhere('i.role=2')
					->andWhere('DATE_FORMAT(z.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
					->groupBy('z.finishedBy,DATE_FORMAT(z.finishedDate,"%Y-%m")')->fetchAll();
// 		for ($i=0;$i<count($noProjectC);$i++){
// 			array_push($otherCof, $noProjectC[$i]);
// 		}
// 		for ($i=0;$i<count($otherCof)-1;$i++){
// 			for ($j=$i+1;$j<count($otherCof);$j++){
// 				if ($otherCof[$i]->date==$otherCof[$j]->date){
// 					if ($otherCof[$i]->DM==$otherCof[$j]->DM){
// 						$otherCof[$i]->sum = $otherCof[$i]->sum + $otherCof[$j]->sum;
// 						unset($otherCof[$j]);
// 						$otherCof = array_values($otherCof);
// 					}
// 				}
// 			}
// 		}
		for ($i=0;$i<count($otherCof);$i++){
			if ($otherCof[$i]->sum/8 > 20)$otherCof[$i]->sum = 0.2;
			else if ($otherCof[$i]->sum/8 > 10 && $otherCof[$i]->sum/8 <= 20)$otherCof[$i]->sum = 0.1;
			else if ($otherCof[$i]->sum/8 >= 5 && $otherCof[$i]->sum/8 <= 10)$otherCof[$i]->sum = 0.05;
			else $otherCof[$i]->sum = 0;
		}
		return $otherCof;
	}
	/*推算排名经理 2013-08-19*/
	public function queryRank($account,$startDate,$finishedDate){
		$queryTaskI = $this->queryTaskI($startDate, $finishedDate);
		$queryAllOtherCoeff = $this->queryAllOtherCoeff($startDate, $finishedDate);
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($k=0;$k<count($queryAllOtherCoeff);$k++){
				if ($queryTaskI[$i][0]->date == $queryAllOtherCoeff[$k]->date){
					array_push($queryTaskI[$i], $queryAllOtherCoeff[$k]);
				}
			}
		}
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($j=0;$j<count($queryTaskI[$i])-1;$j++){
				for ($k=$j+1;$k<count($queryTaskI[$i]);$k++){
					if ($queryTaskI[$i][$j]->DM == $queryTaskI[$i][$k]->DM){
						$queryTaskI[$i][$j]->sum = $queryTaskI[$i][$j]->sum+$queryTaskI[$i][$k]->sum;
						unset($queryTaskI[$i][$k]);
						$queryTaskI[$i] = array_values($queryTaskI[$i]);
					}
				}
			}
		}
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($j=0;$j<count($queryTaskI[$i]);$j++){
				if ($queryTaskI[$i][$j]->sum<=0.2){
					$queryTaskI[$i][$j]->sum = $queryTaskI[$i][$j]->sum+1;
				}
			}
		}
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($j=0;$j<count($queryTaskI[$i])-1;$j++){
				for ($k=0;$k<count($queryTaskI[$i])-$i-1;$k++){
					if ($queryTaskI[$i][$k]->sum<$queryTaskI[$i][$k+1]->sum){
						$j1 = $queryTaskI[$i][$k];
						$queryTaskI[$i][$k] = $queryTaskI[$i][$k+1];
						$queryTaskI[$i][$k+1] = $j1;
						$queryTaskI[$i] = array_values($queryTaskI[$i]);
					}
				}
			}
		}
		/*全部排名*/
		$dept = $this->getDept($account)->dept;
		for ($i=0;$i<count($queryTaskI);$i++){
			$temp = 0;
			for ($j=0;$j<count($queryTaskI[$i]);$j++){
				$temp ++;
				$queryTaskI[$i][$j]->allRank = $temp;
				
			}
		}
		$arr = array();
		$flush = array();
		/*科室内部*/
		for ($i=0;$i<count($queryTaskI);$i++){
			$oTemp = 0;
			for ($j=0;$j<count($queryTaskI[$i]);$j++){
				if ($queryTaskI[$i][$j]->dept == $dept){
					$oTemp++;
					$arr[$i][$j] = $queryTaskI[$i][$j];
					$arr[$i][$j]->deptRank = $oTemp;
					$arr[$i] = array_values($arr[$i]);
				}
			}
		}
		for ($i=0;$i<count($arr);$i++){
			for ($j=0;$j<count($arr[$i]);$j++){
				if ($arr[$i][$j]->DM==$account)array_push($flush, $arr[$i][$j]);
			}
		}
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($j=0;$j<count($queryTaskI[$i]);$j++){
				for ($k=0;$k<count($flush);$k++){
					if ($queryTaskI[$i][$j]->date==$flush[$k]->date && $queryTaskI[$i][$j]->DM==$flush[$k]->DM){
						$queryTaskI[$i][$j]->deptRank = $flush[$k]->deptRank;
					}
				}
			}
		}
		/*中移合署*/
		$ary = array();
		$flushO = array();
		if ($dept == 6){
			for ($i=0;$i<count($queryTaskI);$i++){
				$oTemp = 0;
				for ($j=0;$j<count($queryTaskI[$i]);$j++){
					$oTemp ++;
					$ary[$i][$j] = $queryTaskI[$i][$j];
					$ary[$i][$j]->officeRank = $oTemp;
					$ary[$i] = array_values($ary[$i]);
				}
			}
			for ($i=0;$i<count($ary);$i++){
				for ($j=0;$j<count($ary[$i]);$j++){
					if ($ary[$i][$j]->DM==$account)array_push($flushO, $ary[$i][$j]);
				}
			}
			for ($i=0;$i<count($queryTaskI);$i++){
				for ($j=0;$j<count($queryTaskI[$i]);$j++){
					for ($k=0;$k<count($flushO);$k++){
						if ($queryTaskI[$i][$j]->date==$flushO[$k]->date && $queryTaskI[$i][$j]->DM==$flushO[$k]->DM){
							$queryTaskI[$i][$j]->officeRank = $flushO[$k]->officeRank;
						}
					}
				}
			}
		}
		$queryAllRank = array();
		for ($i=0;$i<count($queryTaskI);$i++){
			for ($j=0;$j<count($queryTaskI[$i]);$j++){
				if ($queryTaskI[$i][$j]->DM == $account){
					array_push($queryAllRank, $queryTaskI[$i][$j]);
				}
			}
		}
		return $queryAllRank;
	}
	/*推算普通员工排名情况 2013-08-19*/
	public function getStaffRank($account,$startDate,$finishedDate){
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$queryStaff = $this->dao->select('DATE_FORMAT(t.finishedDate,"%Y-%m") as date,SUM(t.estimate) as sum,t.finishedBy,u.dept FROM zt_task t,ict_user u')
		->where('t.finishedBy=u.account')->andWhere('u.role=3')
		->andWhere('DATE_FORMAT(t.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
		->andWhere('deleted')->eq('0')->groupBy('t.finishedBy,DATE_FORMAT(t.finishedDate,"%Y-%m")')->fetchAll();
		$data = array();
		$length = abs(date("Y",strtotime($finishedDate))-date("Y",strtotime($startDate)))*12+date("m",strtotime($finishedDate))-date("m",strtotime($startDate));
		for ($i=0;$i<$length+1;$i++){
			for ($j=0;$j<count($queryStaff);$j++){
				if (isset($queryStaff[$j]->date) && $queryStaff[$j]->date==date('Y-m',strtotime(date('Y',strtotime($startDate)).'-'.(date('m',strtotime($startDate))+$i)))){
					$data[$i][$j] = $queryStaff[$j];
					$data[$i] = array_values($data[$i]);
				}
			}
		}
		$data = array_values($data);
		for ($a=0;$a<count($data);$a++){
			if (count($data[$a])>1){
				for ($b=0;$b<count($data[$a])-1;$b++){
					for ($c=0;$c<count($data[$a])-$b-1;$c++){
						if ($data[$a][$c]->sum<$data[$a][$c+1]->sum){
							$temp = new stdClass();
							$temp = $data[$a][$c];
							$data[$a][$c] = $data[$a][$c+1];
							$data[$a][$c+1] = $temp;
						}
					}
				}
			}
		}
		/*全部排名*/
		$dept = $this->getDept($account)->dept;
		for ($i=0;$i<count($data);$i++){
			$temp = 0;
			for ($j=0;$j<count($data[$i]);$j++){
				$temp ++;
				$data[$i][$j]->allRank = $temp;
			}
		}
		$abc = $data;//临时存放
		/*科室内部*/
		for ($i=0;$i<count($data);$i++){
			$oTemp = 0;
			for ($j=0;$j<count($data[$i]);$j++){
				$oTemp ++;
				if ($data[$i][$j]->dept !== $dept){
					unset($data[$i][$j]);
					$data[$i] = array_values($data[$i]);
				}
					$data[$i][$j]->deptRank = $oTemp;
			}
		}
		/*中移合署*/
		$data = $abc;
		if ($dept == 6){
			for ($i=0;$i<count($data);$i++){
				$oTemp = 0;
				for ($j=0;$j<count($data[$i]);$j++){
					$oTemp ++;
					if ($data[$i][$j]->dept !== $dept){
						unset($data[$i][$j]);
						$data[$i] = array_values($data[$i]);
					}
						$data[$i][$j]->officeRank = $oTemp;
				}
			}
		}
		$queryAllRank = array();
		for ($i=0;$i<count($data);$i++){
			for ($j=0;$j<count($data[$i]);$j++){
				if (isset($data[$i][$j]->finishedBy) && $data[$i][$j]->finishedBy == $account)
				array_push($queryAllRank, $data[$i][$j]);
			}
		}
		return $queryAllRank;
	}
	/*获取当前项目经理其他工作绩效*/
	public function getOtherCoeff1($startDate,$finishedDate)
	{
		$otherSql		= $this->getAllOtherCoeff1($startDate,$finishedDate);
		for($i=0;$i<count($otherSql);$i++){
			if($otherSql[$i]->coeff>20)$otherSql[$i]->otherCoeff=0.2;
			else if($otherSql[$i]->coeff>=10 && $otherSql[$i]->coeff<=20)$otherSql[$i]->otherCoeff=0.1;
			else if($otherSql[$i]->coeff>=5 && $otherSql[$i]->coeff<10)$otherSql[$i]->otherCoeff=0.05;
		}
		return $otherSql;
	}
	
	/*获取当前经理项目管理系数值*/
	public function getManagerCoeff1($account,$startDate,$finishedDate)
	{
		$getAllProject	= $this->getAllManagerCoeff1($startDate,$finishedDate);
		for($i=0;$i<count($getAllProject);$i++){
			
		}
		$if = 0;
		foreach($getAllProject as $search){
			$if++;
			if(strpos($search->DM, $account)!== false){
				break;
			}
		}
		$len = count($getAllProject);//长度
		$coeff = 0;//最终系数
		if($if<=round($len*0.2))$coeff=1.2;
		else if($if>round($len*0.21) && $if<=round($len*0.8))$coeff=1;
		else $coeff=0.9;
		return $coeff;
	}
	
	/*获取所有项目经理其他工作绩效系数*/
	public function getAllOtherCoeff1($startDate,$finishedDate)
	{
		$otherSql = $this->dao->select('ROUND(SUM(t.estimate)*i.coefficient/8,2) AS coeff,t.project,i.DM,u.dept,DATE_FORMAT(t.finishedDate,"%Y-%m") AS date,0 as otherCoeff FROM zt_task t,ict_product i,zt_user u')
		->where('t.project=i.project')->andWhere('u.account=i.DM')
		->andWhere('DATE(t.finishedDate)')->between($startDate,$finishedDate)
		->groupBy("DATE_FORMAT(t.finishedDate,'%Y-%m')")->fetchAll();
		return $otherSql;
	}
	
	/*取得所有项目经理的工作量*/
	public function getAllManagerCoeff1($startDate,$finishedDate)
	{
		$getAllProject	= $this->dao->select('ROUND(i.coefficient*i.completrate*SUM(t.estimate)/i.partnum,2) AS coeff,t.project,i.DM,u.dept,DATE_FORMAT(t.finishedDate,"%Y-%m") as date FROM zt_task t,ict_product i,zt_user u')
		->where('t.project = i.project')->andWhere('u.account=i.DM')
		->andWhere('DATE(t.finishedDate)')->between($startDate,$finishedDate)
		->groupBy("DATE_FORMAT(t.finishedDate,'%Y-%m'),i.DM,u.dept ORDER BY  DATE_FORMAT(t.finishedDate,'%Y-%m') DESC,coeff DESC")
		->fetchAll();
		return $getAllProject;
	}
	
	/**********所有员工*************/
	public function queryAll()
	{
		return $this->dao->select(' DISTINCT account,dept,role')->from(TABLE_ICTUSER)
			   ->fetchAll();
	}
	/*******所有开发人员相关薪酬信息*******/
	public function getAllPerson($startDate,$finishedDate,$orderBy)
	{
		$allPerson = $this->queryAll();
		$singleDay = array();
		$m = 0;
		for ($i=0;$i<count($allPerson);$i++){
			if($allPerson[$i]->role==3){
				$personnel = $this->getPersonnelCount($allPerson[$i]->account, $startDate, $finishedDate);
				if (isset($personnel[0])){
					$singleDay[$m] = $personnel[0]; 
					$m++;
				}
			}
		}
		return $this->getSequence($singleDay, $orderBy);
	}
	/***每个项目经理的薪酬信息***/
	public function getAllManager($startDate, $finishedDate, $orderBy)
	{
		$allPerson = $this->queryAll();
		$mSingleDay = array();
		$m=0;
		for ($i=0;$i<count($allPerson);$i++){
			if($allPerson[$i]->role==2){
				$getManage = $this->getManagerCount($allPerson[$i]->account, $startDate, $finishedDate);
				if (isset($getManage[0])){
					$mSingleDay[$m] = $getManage[0];
					$m++;
				}
			}
		}
		return $this->getSequence($mSingleDay, $orderBy);
	}
	
	/*排序功能通用方法  
	 * $array
	 * return
	 * */
	public function getSequence($array,$orderBy){
		for ($i=0;$i<count($array)-1;$i++){
			for ($j=count($array)-1;$j > $i;$j--){
				if ($orderBy[1] == 'desc'){
					if ($array[$i]->$orderBy[0] < $array[$j]->$orderBy[0]){
						$temp = $array[$i];
						$array[$i] = $array[$j];
						$array[$j] = $temp;
					}
				}else {
					if ($array[$i]->$orderBy[0] > $array[$j]->$orderBy[0]){
						$temp = $array[$i];
						$array[$i] = $array[$j];
						$array[$j] = $temp;
					}
				}
			}
		}
		return $array;
	}
	
	
	
	
	
	
	/**
	 * 获取部门平均工作量1
	 * @param unknown_type $account
	 */
	public function getDeptCount1($startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$deptAccount = $this->dao->select('round(SUM(estimate)/count(distinct finishedBy),2) as sumEst,DATE_FORMAT(finishedDate,"%Y-%m") AS date')->from(TABLE_TASK)
		->where('finishedBy')->ne('')->andWhere('finishedBy')->ne('admin')
		->andWhere('DATE_FORMAT(finishedDate,"%Y-%m")')
		->between($startDate,$finishedDate)->andWhere('deleted')->eq('0')
		->groupBy('DATE_FORMAT(finishedDate,"%Y-%m")')
		->fetchAll();
		return $deptAccount;
	}
	/**
	 * 个人月度工作量1
	 * @param unknown_type $account
	 */
	public function getPersonelCount1($account,$startDate,$finishedDate)
	{
		$getDept = $this->getDeptCount1($startDate, $finishedDate);
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$personelCount = $this->dao->select('finishedBy,DATE_FORMAT(finishedDate,"%Y-%m") AS date,SUM(estimate) as count,0 as personnelCoeff')->from(TABLE_TASK)
						->where('finishedBy')->eq($account)
						->andWhere('DATE_FORMAT(finishedDate,"%Y-%m")')
						->between($startDate,$finishedDate)
						->andWhere('deleted')->eq('0')
						->groupBy('DATE_FORMAT(finishedDate,"%Y-%m")')->fetchAll();
		for ($j=0;$j<count($personelCount);$j++){
			for ($i=0;$i<count($getDept);$i++){
				if (isset($personelCount[$j]->date) && isset($getDept[$i]->date)){
					if ($personelCount[$j]->date == $getDept[$i]->date){
						$personelCount[$j]->personnelCoeff = round($personelCount[$j]->count/$getDept[$i]->sumEst,2);
					}
				}
			}
		}
		return $personelCount;
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 获取月度工时
	 * @param unknown_type $account
	 */
	public function getPersonalTime($account)
	{
		$countTime = $this->dao->select('SUM(estimate) as count')->from(TABLE_TASK)
			   ->where('DATE(finishedDate)')->ge(date('Y-m-01',time()))
			   ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
			   ->andWhere('finishedBy')->ne('')->andWhere('finishedBy')->ne('admin')
			   ->andWhere('deleted')->eq('0')->andWhere('finishedBy')->eq($account)
			   ->groupBy('finishedBy')
			   ->fetch();
		if($countTime->count =='')$countTime->count=0;
		return $countTime;
	}
	
	/**
	 * 获取ict_user表信息
	 * @param unknown_type $account
	 */
	public function getSalary($account)
	{
		$getSalary = $this->dao->select('*')->from(TABLE_ICTUSER)
			   ->where('account')->eq($account)
			   ->fetch();
		if (isset($getSalary->standsalary)){
			if($getSalary->standsalary=='')$getSalary->standsalary=0;
			if($getSalary->projectbonus=='')$getSalary->projectbonus=0;
		}
		return $getSalary;
	}
	
	/**
	 * 获取故障数
	 * @param unknown_type $account
	 */
	public function getBugNum($account)
	{
		$bugNum = $this->dao->select('COUNT(*) as bugNum')->from(TABLE_BUG)
				  ->where('DATE(resolvedDate)')->ge(date('Y-m-01',time()))
				  ->andWhere('DATE(resolvedDate)')->le(date('Y-m-t',time()))
				  ->andWhere('resolvedBy')->ne('')->andWhere('resolvedBy')->ne('admin')
				  ->andWhere('resolvedBy')->eq($account)
				  ->groupBy('resolvedBy')
				  ->fetch();
		if(5 <= $bugNum->bugNum  && $bugNum->bugNum<= 10 )return -20*$bugNum->bugNum;
		else if($bugNum->bugNum > 10)return -40*$bugNum->bugNum;
		else return 0;
	}
	
	/**
	 * 个人月度工作量
	 * @param unknown_type $account
	 */
	public function getPersonelCount($account)
	{
		$personelCount = $this->dao->select('SUM(estimate) as count')->from(TABLE_TASK)
			   ->where('finishedBy')->ne('')->andWhere('finishedBy')->ne('admin')
			   ->andWhere('finishedBy')->eq($account)
			   ->andwhere('DATE(finishedDate)')->ge(date('Y-m-01',time()))
			   ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
			   ->andWhere('deleted')->eq('0')
			   ->fetch();
		return $personelCount;
	}
	
	/**
	 * 获取部门平均工作量
	 * @param unknown_type $account
	 */
	public function getDeptCount()
	{
		$deptAccount = $this->dao->select('round(SUM(estimate)/30,2) as sumEst')->from(TABLE_TASK)
				 	   ->where('finishedBy')->ne('')->andWhere('finishedBy')->ne('admin')
				 	   ->andwhere('DATE(finishedDate)')->ge(date('Y-m-01',time()))
				 	   ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
				 	   ->andWhere('deleted')->eq('0')
					   ->fetch();
// 		$personNum = $this->dao->select('COUNT(*) as countAcc')->from(TABLE_USER)
// 					 ->where('company')->eq('1')
// 					 ->andWhere('deleted')->eq('0')
// 					 ->andWhere('account')->ne('admin')
// 					 ->fetchAll();
// 		return round($deptAccount->sumEst/$personNum[0]->countAcc,2);
		return round($deptAccount->sumEst/30,2);
	}
	
	/*获取部室月度产品系数*/
	public function getDeptCoeff()
	{
		$deptCoeff = $this->dao->select('SUM(coefficient* completrate) as coeffNum')
					 ->from(TABLE_ICTPRODUCT)
					 ->groupBy('name')
					 ->fetch();
		$deptNum   = $this->dao->select('SUM(completrate) as comNum')->from(TABLE_ICTPRODUCT)
					 ->groupBy('name')
					 ->fetch();
		return round($deptCoeff->coeffNum/$deptNum->comNum,2);
	}
	
	/*获取日工时*/
	public function getDayHours($account,$finishedDate)
	{
		return $this->dao->select('SUM(estimate) AS estimate,finishedBy,DATE(finishedDate) AS finishedDate')
			   ->from(TABLE_TASK)
			   ->where("DATE_FORMAT(finishedDate,'%Y-%m')")->eq($finishedDate)
			   ->andWhere('finishedBy')->eq($account)->andWhere('deleted')->eq('0')->groupBy('DATE(finishedDate)')
			   ->orderBy('finishedDate Desc')
			   ->fetchAll();
	}
	
	/************************************项目经理***********************************************/
	/*获取当前经理的所有项目以及信息*/
// 	public function getProjectInfo($account)
// 	{
// 		$getSum				= $this->dao->select('SUM(coefficient) AS coeff,SUM(partnum) AS partN,SUM(completrate) AS compl')
// 							->from(TABLE_ICTPRODUCT)->where('DM')->eq($account)->groupBy('project')
// 							->fetchAll();
// 		$getProjectEstimate = $this->dao->select('SUM(estimate)as sum,project')->from(TABLE_TASK)
// 							  ->where('DATE(finishedDate)')->ge(date('Y-m-01',time()))
// 					          ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
// 					          ->andWhere('deleted')->eq('0')
// 					          ->andWhere("project in (SELECT project FROM ict_product WHERE DATE(finishedDate) BETWEEN DATE_ADD(CURDATE(),INTERVAL -DAY(CURDATE())+1 DAY) AND LAST_DAY(CURDATE()) AND DM='".$account."')")
// 					          ->groupBy('project')
// 					          ->fetchAll();
// 		$projectArr		= 0;
// 		foreach ($getProjectEstimate as $projectID)
// 		{
// 			$projectArr  =  $projectID->sum + $projectArr;
// 		}
		
// 		return $projectArr*$getSum->coeff*$getSum->compl/$getSum->partN;
// 	} 
	
	
	/*获取当前项目经理其他工作绩效*/
	public function getOtherCoeff($account)
	{
		$otherSql		= $this->getAllOtherCoeff();
		$otherCoeff		= 0;
		foreach ($otherSql as $other){
			if(strpos($other->DM, $account)!== false){
				$otherCoeff	= $other->coeff;
				break;
			}
		}
		if($otherCoeff>20)return 0.2;
		else if (10<=$otherCoeff && $otherCoeff<=20)return 0.1;
		else if (5<=$otherCoeff && $otherCoeff<10)return 0.05;
		else return 0;
	}
	
	/*获取当前经理项目管理系数值*/
	public function getManagerCoeff($account)
	{
		$getAllProject	= $this->getAllManagerCoeff();
		$if = 0;
		foreach($getAllProject as $search){
			$if++;
			if(strpos($search->DM, $account)!== false){
				break;
			}
		}
		$len = count($getAllProject);//长度
		$coeff = 0;//最终系数
		if($if<=round($len*0.2))$coeff=1.2;
		else if($if>round($len*0.21) && $if<=round($len*0.8))$coeff=1;
		else $coeff=0.9;
		return $coeff;
	}
	/*所有员工排名 $condition参数区2分科室、3中移署、1全部*/
	public function staffRank($account,$condition)
	{
		$allStaffRank 	= $this->getAllStaff();
		rsort($allStaffRank);
		if($condition==1){
			$rank1 = 0;
			foreach ($allStaffRank as $staff1){
				$rank1++;
				if(strpos($staff1->finishedBy, $account))break;
			}
			return $rank1;
		}
		else if($condition==2){
			$dept = $this->getDept($account)->dept;
			$byDept = array();
			for($i=0;$i<count($allStaffRank);$i++){
				if($allStaffRank[$i]->dept==$dept)array_push($byDept,$allStaffRank[$i]);
			}
			rsort($byDept);
			$rank2 = 0;
			foreach ($byDept as $by){
				$rank2++;
				if(strpos($by->finishedBy, $account) !== false)break;
			}
			return $rank2;
		}else if($condition==3){
			$byDept2 = array();
			if($this->getDept($account)->dept ==6){
				for($n=0;$n<count($allStaffRank);$n++){
					if($allStaffRank[$n]->dept==6)array_push($byDept2, $allStaffRank[$n]);
				}
				rsort($byDept2);
				$rank3 = 0;
				foreach ($byDept2 as $by2){
					$rank3++;
					if(strpos($by2->finishedBy, $account)!== false)break;
				}
				return $rank3;
			}else return '';
		}
	}
	/*所有经理排名 $condition参数区2分科室、3中移署、1全部*/
	public function managerRank($account,$condition)
	{
		$projectRank	= $this->getAllManagerCoeff();
		$otherRank		= $this->getAllOtherCoeff();
		$allRank		= array_merge($projectRank,$otherRank);
		if(count($allRank)>1){
		for($i=0;$i<count($allRank);$i++){
			for($j=$i+1;$j<=count($allRank);$j++){
				if($allRank[$i]->DM==$allRank[$j]->DM){
					$allRank[$i]->coeff = $allRank[$j]->coeff+$allRank[$i]->coeff;
					unset($allRank[$j]);
				}
			}
			rsort($allRank);
		}
		}
		if($condition==1){
			$rank = 0;
			foreach ($allRank as $all){
				$rank++;
				if(strpos($all->DM, $account)!== false){
					break;
				}
			}
		return $rank;;
		}
		else if($condition==2){
			$dept = $this->getDept($account)->dept;
			$orderByDept = array();
			for($k=0;$k<count($allRank);$k++){
				if($allRank[$k]->dept==$dept)array_push($orderByDept,$allRank[$k]);
			}
			rsort($orderByDept);
			$rank2 = 0;
			foreach ($orderByDept as $orderDept){
				$rank2++;
				if(strpos($orderDept->DM, $account) !== false)break;
			}
			return $rank2;
		}
		else if($condition==3){
			$orderByDept3 = array();
			if($this->getDept($account)->dept ==6){
				for($n=0;$n<count($allRank);$n++){
					if($allRank[$n]->dept==6)array_push($orderByDept3, $allRank[$k]);
				}
				rsort($orderByDept3);
				$rank3 = 0;
				foreach ($orderByDept3 as $orderDept3){
					$rank3++;
					if(strpos($orderDept3->DM, $account)!== false)break;
				}
				return $rank3;
			}else{
				return '';
			}
		}
	}
	/*获取所有项目经理其他工作绩效系数*/
	public function getAllOtherCoeff()
	{
		$otherSql		  = $this->dao->select('ROUND(SUM(t.estimate)*i.coefficient/8,2) AS coeff,t.project,i.DM,u.dept FROM zt_task t,ict_product i,zt_user u')
						  ->where('t.project=i.project')->andWhere('u.account=i.DM')
						  ->andWhere('DATE(t.finishedDate)')->ge(date('Y-m-01',time()))
						  ->andWhere('DATE(t.finishedDate)')->le(date('Y-m-t',time()))
// 						  ->andWhere('DATE(t.finishedDate)')->between($startDate,$finishedDate)
						  ->groupBy('i.DM')->fetchAll();
		
		return $otherSql;
	}
	/*取得所有项目经理的工作量*/
	public function getAllManagerCoeff()
	{
// 		$getOther		= $this->dao->select('SUM(coefficient) as sumCoeff,SUM(partnum) as sumPart,SUM(completrate) as sumComp,project')
// 						  ->from(TABLE_ICTPRODUCT)
// 						  ->where('DATE(finishedDate)')->ge(date('Y-m-01',time()))
// 						  ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
// 						  ->groupBy('project')
// 						  ->fetchAll();
// 		$otherProject	= array();
// 		foreach($getOther as $other){
// 			$otherProject = $other->project;
// 		}
// 		$getSumEst		= $this->dao->select('SUM(estimate) as sumEst,project')->from(TABLE_TASK)
// 						  ->where('DATE(finishedDate)')->ge(date('Y-m-01',time()))
// 						  ->andWhere('DATE(finishedDate)')->le(date('Y-m-t',time()))
// 						  ->andWhere('deleted')->eq('0')->andWhere('project in(2,8)')
// 						  ->groupBy('project')->fetchAll();
		
		//
		$getAllProject	= $this->dao->select('ROUND(i.coefficient*i.completrate*SUM(t.estimate)/i.partnum,2) AS coeff,t.project,i.DM,u.dept FROM zt_task t,ict_product i,zt_user u')
						  ->where('t.project = i.project')->andWhere('u.account=i.DM')
						  ->andWhere('DATE(t.finishedDate)')->ge(date('Y-m-01',time()))
						  ->andWhere('DATE(t.finishedDate)')->le(date('Y-m-t',time()))
						  ->groupBy('i.project')
						  ->fetchAll();
		/*在所有结果大于1的情况下,求相同经理的工作量和*/
		if(count($getAllProject)>1){
		for($i=0;$i<count($getAllProject);$i++){
			for($j=$i+1;$j<count($getAllProject);$j++){
			if(isset($getAllProject[$i])&&$getAllProject[$i]->DM == $getAllProject[$j]->DM){
			$getAllProject[$i]->coeff = $getAllProject[$j]->coeff+$getAllProject[$i]->coeff;
			unset($getAllProject[$j]);
			}	
			}
		}
		rsort($getAllProject);
		}
		return $getAllProject;
	}
	/*获取所有普通员工的月度工时进行排名  
	 *根据ict_user表的role控制角色  1是管理员 2是项目经理 3是普通员工*/
	public function getAllStaff(){
		return 
		$this->dao->select('SUM(t.estimate) as sum,t.finishedBy,u.dept FROM zt_task t,ict_user u')
		->where('t.finishedBy=u.account')->andWhere('u.role=3')
		->andWhere('DATE(t.finishedDate)')->ge(date('Y-m-01',time()))
		->andWhere('DATE(t.finishedDate)')->le(date('Y-m-t',time()))
		->groupBy('t.finishedBy')->fetchAll();
	}
	/*找到所在部门*/
	public function getDept($account)
	{
		return $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)
				->andWhere('deleted')->eq('0')->fetch();
	}
	
	
	/**************科室领导******************/
	/*排除工时小于10的员工*/
	public function queryLowStaff($startDate,$finishedDate){
		$queryLowStaff = $this->dao->select('DATE_FORMAT(t.finisheddate,"%Y年%m月") as date,
							t.finishedby,SUM(t.estimate) as sum,u.dept from zt_task t,zt_user u')
				->where('t.finishedBy=u.account')->andWhere('DATE_FORMAT(t.finisheddate,"%Y-%m")')->between($startDate,$finishedDate)
				->andWhere('t.estimate<>0')->groupBy('t.finishedBy,DATE_FORMAT(t.finisheddate,"%Y-%m")')
				->having('sum(t.estimate)<=10')->fetchAll();
		return $queryLowStaff;
	}
	/*获取全体员工工时*/
	public function allStaffHours($finishedDate)
	{
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$temp = $this->queryLowStaff($finishedDate,$finishedDate);
		$queryLowStaff = '';
		for ($i=0;$i<count($temp);$i++){
			if ($queryLowStaff!== ''){
				$queryLowStaff = $queryLowStaff.','.$temp[$i]->finishedby;
			}else{
				$queryLowStaff = $temp[$i]->finishedby;
			}
		}
		$allStaffHours = $this->dao->select('sum(t.estimate) as sum,round(SUM(t.estimate)/COUNT(DISTINCT t.finishedBy),2) as average from zt_task t,zt_user u')
						->where('t.finishedby=u.account')->andWhere('date_format(t.finishedDate,"%Y-%m")')->eq($finishedDate)
						->andWhere('u.dept in(1,2,3,4,5,6,7)')->andWhere('t.finishedBy')->notIn($queryLowStaff)->fetch();
		$allICT	= $this->dao->select('SUM(t.estimate) AS sum,round(SUM(t.estimate)/COUNT(DISTINCT t.finishedby),2) as average FROM zt_task t,zt_user u')
					->where('t.finishedby=u.account')->andWhere('date_format(t.finishedDate,"%Y-%m")')->eq($finishedDate)
					->andWhere('u.dept in(1,2,3,4,5,6)')->andWhere('t.finishedBy')->notIn($queryLowStaff)->fetch();
		$allTeam = $this->dao->select('SUM(t.estimate) AS sum,round(SUM(t.estimate)/COUNT(DISTINCT t.finishedby),2) as average FROM zt_task t,zt_user u')
					->where('t.finishedby=u.account')->andWhere('date_format(t.finishedDate,"%Y-%m")')->eq($finishedDate)
					->andWhere('u.dept=7')->andWhere('t.finishedBy')->notIn($queryLowStaff)->fetch();
		$array = array();
		array_push($array, $allTeam);
		array_push($array, $allICT);
		array_push($array, $allStaffHours);
		return $array;
	}
	/*查询当月人数*/
	public function queryPersonNum($finishedDate)
	{
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$ictNum = $this->dao->select('count(p.num) as num from (select round(COUNT(DISTINCT t.finishedBy),2) as num 
					FROM zt_task t,zt_user u where t.finishedBy=u.account and u.dept=1 
					and date_format(t.finishedDate,"%Y-%m")='."'$finishedDate'".' group by t.finishedBy Having SUM(t.estimate)>10)p')
// 					->andWhere('u.dept=1')->andWhere('date_format(finishedDate,"%Y-%m")')->eq($finishedDate)
					->fetch();
		$officeNum = $this->dao->select('count(p.num) as num from (select round(COUNT(DISTINCT t.finishedBy),2) as num
				FROM zt_task t,zt_user u where t.finishedBy=u.account and u.dept=6
				and date_format(t.finishedDate,"%Y-%m")='."'$finishedDate'".' group by t.finishedBy Having SUM(t.estimate)>10)p')
				->fetch();
		$teamNum = $this->dao->select('count(p.num) as num from (select round(COUNT(DISTINCT t.finishedBy),2) as num
				FROM zt_task t,zt_user u where t.finishedBy=u.account and u.dept=7
				and date_format(t.finishedDate,"%Y-%m")='."'$finishedDate'".' group by finishedBy Having SUM(t.estimate)>10)p')
				->fetch();
		
// 		$officeNum = $this->dao->select('round(COUNT(DISTINCT t.finishedBy),2) as num FROM zt_task t,zt_user u')->where('t.finishedBy=u.account')
// 					->andWhere('u.dept=6')->andWhere('date_format(finishedDate,"%Y-%m")')->eq($finishedDate)->fetch();
// 		$teamNum = $this->dao->select('round(COUNT(DISTINCT t.finishedBy),2) as num FROM zt_task t,zt_user u')->where('t.finishedBy=u.account')
// 					->andWhere('u.dept not in(1,6)')->andWhere('date_format(finishedDate,"%Y-%m")')->eq($finishedDate)->fetch();
		$array = array();
		array_push($array, $ictNum);
		array_push($array, $officeNum);
		array_push($array, $teamNum);
		return $array;
	}
	
	/*获取薪酬增减*/
	public function salaryIncrease($finishedDate, $orderBy)
	{
		$startDate = date('Y-m-01',strtotime($finishedDate));
		$finishedDate = date('Y-m-t',strtotime($finishedDate));
// 		echo $startDate.'000'.$finishedDate;
		//普通员工
		$queryAllPerson = $this->getAllPerson($startDate, $finishedDate, $orderBy);
		//项目经理
		$getAllManager = $this->getAllManager($startDate, $finishedDate, $orderBy);
		$getAllUser = $this->dao->select('*')->from(TABLE_ICTUSER)->where('dept in(1,6)')->fetchAll();
		$temp = array();
		for ($i=0;$i<count($getAllUser);$i++){
			if ($getAllUser[$i]->role==3){
				$getPersonnelCount = $this->getPersonnelCount($getAllUser[$i]->account, $startDate, $finishedDate);
				if(isset($getPersonnelCount[0]))array_push($temp, $getPersonnelCount[0]);
			}
			else if ($getAllUser[$i]->role==2){
				$getManagerCount = $this->getManagerCount($getAllUser[$i]->account, $startDate, $finishedDate);
				if(isset($getManagerCount[0]))array_push($temp, $getManagerCount[0]);
			}
		}
// 		$time=strtotime($finishedDate);
// 		$firstday=date('Y-m-01',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($finishedDate))-1).'-01'));
// 		$lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
// 		$queryAllPersonLast	= $this->getAllPerson($firstday, $lastday);
// 		$queryAllManagerLast = $this->getAllManager($firstday, $lastday);
// 		$queryAllPerson = array_values(array_filter($queryAllPerson));
// 		$getAllManager = array_values(array_filter($getAllManager));
// 		$queryAllPersonLast = array_values(array_filter($queryAllPersonLast));
// 		$queryAllManagerLast = array_values(array_filter($queryAllManagerLast));
// 		$salaryIncrease = array();
// 		$n = 0;
// 		for ($i=0;$i<count($queryAllPerson);$i++){
// 			$n++;
// 			if ($queryAllPerson[$i][0]->dept==1){
// 				for ($j = 0; $j < count($queryAllPersonLast); $j++) {
// 						if ($queryAllPerson[$i][0]->account==$queryAllPersonLast[$j][0]->account){
// // 							$salaryIncrease->$n->increase = $queryAllPerson[$i][0]->finalSalary*1-$queryAllPersonLast[$j][0]->finalSalary*1;
// 							$queryAllPerson[$i][0]->finalSalary = $queryAllPerson[$i][0]->finalSalary*1-$queryAllPersonLast[$j][0]->finalSalary*1;
// 						}
// 				}
// 				array_push($salaryIncrease,$queryAllPerson[$i][0]);
// 				}
// 		}
// 		for ($i=0;$i<count($getAllManager);$i++){
// 			$n++;
// 			if ($getAllManager[$i][0]->dept==1){
// 				for ($j = 0; $j < count($queryAllManagerLast); $j++) {
// 						if ($getAllManager[$i][0]->account==$queryAllManagerLast[$j][0]->account){
// // 							$salaryIncrease->$n->increase = $getAllManager[$i][0]->finalSalary*1-$queryAllManagerLast[$j][0]->finalSalary*1;
// 							$getAllManager[$i][0]->finalSalary = $getAllManager[$i][0]->finalSalary*1-$queryAllManagerLast[$j][0]->finalSalary*1;
// 						}
// 				}
// 				array_push($salaryIncrease,$getAllManager[$i][0]);
// 			}
// 		}
		return $temp;
		
	}

/**************************查询各月(领导页面)************************************/
	/*总工时*/
	public function queryEachMonth($startDate,$finishedDate)
	{
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
// 		$system	= $this->dao->select('ROUND(SUM(t.estimate),2) AS sum,u.dept,DATE_FORMAT(t.finishedDate,"%Y年%m月") as date FROM zt_task t,zt_user u,zt_dept d ')
// 				 ->where('DATE_FORMAT(t.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
// 				 ->andWhere('t.finishedby = u.account')->andWhere('u.dept = 1')
// 				 ->groupBy(' DATE_FORMAT(t.finishedDate,"%Y-%m")')->fetchAll();
// 		$ustc	= $this->dao->select('ROUND(SUM(t.estimate),2) AS ustcSum,u.dept,DATE_FORMAT(t.finishedDate,"%Y年%m月") as date FROM zt_task t,zt_user u,zt_dept d ')
// 		->where('DATE_FORMAT(t.finishedDate,"%Y-%m")')->between($startDate,$finishedDate)
// 		->andWhere('t.finishedby = u.account')->andWhere('u.dept = 7')
// 		->groupBy(' DATE_FORMAT(t.finishedDate,"%Y-%m")')->fetchAll();
		$eachMonth = $this->dao->select("p.fDate as date,SUM(p.sum) as sum,SUM(p.ustSum) as ustSum 
					 FROM (SELECT ROUND(SUM(t.estimate),2) AS SUM,0 AS ustSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate 
					 FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m') 
					 BETWEEN '$startDate' and '$finishedDate' AND u.dept in(1,2,3,4,5,6) 
					 GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m') UNION ALL 
					 SELECT 0 AS SUM,ROUND(SUM(t.estimate),2) AS ustSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate 
					 FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m') 
					 BETWEEN '$startDate' and '$finishedDate' AND u.dept = 7 
					 GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m'))p" )->groupBy('p.fDate')->fetchAll();
		$queryLowStaff = $this->queryLowStaff($startDate, $finishedDate);
		for ($i=0;$i<count($queryLowStaff);$i++){
			for ($j=0;$j<count($eachMonth);$j++){
				if ($queryLowStaff[$i]->date==$eachMonth[$j]->date){
					if ($queryLowStaff[$i]->dept==1 ||$queryLowStaff[$i]->dept==2||
							$queryLowStaff[$i]->dept==3||$queryLowStaff[$i]->dept==4||
							$queryLowStaff[$i]->dept==5||$queryLowStaff[$i]->dept==6){
						$eachMonth[$j]->sum = $eachMonth[$j]->sum-$queryLowStaff[$i]->sum;
					}else if ($queryLowStaff[$i]->dept==7){
						$eachMonth[$j]->ustSum = $eachMonth[$j]->sum-$queryLowStaff[$i]->sum;
					}
				}
			}
		}
		return $eachMonth;
		
	}
	/*平均工时*/
	public function queryEachAverage($startDate,$finishedDate){
		$queryEachMonth = $this->queryEachMonth($startDate, $finishedDate);
		$startDate = date('Y-m',strtotime($startDate));
		$finishedDate = date('Y-m',strtotime($finishedDate));
		$queryLowStaff = $this->queryLowStaff($startDate, $finishedDate);
		$eachAverage = $this->dao->select("p.fDate as date,SUM(p.sum) as sum,SUM(p.ustSum) as allSum
				FROM (SELECT COUNT(DISTINCT t.finishedBy) AS SUM,0 AS ustSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate
				FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m')
				BETWEEN '$startDate' and '$finishedDate' AND u.dept in(1,2,3,4,5,6) AND t.estimate<>0
				GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m') UNION ALL
				SELECT 0 AS SUM,COUNT(DISTINCT t.finishedBy)AS allSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate
				FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m')
				BETWEEN '$startDate' and '$finishedDate' AND u.dept in(1,2,3,4,5,6,7) AND t.estimate<>0
				GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m'))p" )->groupBy('p.fDate')->fetchAll();
		for ($i=0;$i<count($queryLowStaff);$i++){
			for ($j=0;$j<count($eachAverage);$j++){
				if ($queryLowStaff[$i]->date==$eachAverage[$j]->date){
					if ($queryLowStaff[$i]->dept==1 ||$queryLowStaff[$i]->dept==2||
							$queryLowStaff[$i]->dept==3||$queryLowStaff[$i]->dept==4||
							$queryLowStaff[$i]->dept==5||$queryLowStaff[$i]->dept==6){
						$eachAverage[$j]->sum = $eachAverage[$j]->sum-1;
					}
					$eachAverage[$j]->allSum = $eachAverage[$j]->allSum-1;
				}
			}
		}
		for ($i=0;$i<count($eachAverage);$i++){
			for ($j=0;$j<count($queryEachMonth);$j++){
				if ($eachAverage[$i]->date==$queryEachMonth[$j]->date){
					$eachAverage[$i]->allSum = round(($queryEachMonth[$j]->sum+$queryEachMonth[$j]->ustSum)/$eachAverage[$i]->allSum,2);
					$eachAverage[$i]->sum = round($queryEachMonth[$j]->sum/$eachAverage[$i]->sum,2);
				}
			}
		}
// 		$eachAverage = $this->dao->select("p.fDate as date,SUM(p.sum) as sum,SUM(p.ustSum) as allSum
// 				FROM (SELECT ROUND(SUM(t.estimate)/COUNT(DISTINCT t.finishedBy),2) AS SUM,0 AS ustSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate
// 				FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m') 
// 				BETWEEN '$startDate' and '$finishedDate' AND u.dept in(1,2,3,4,5,6) AND t.estimate<>0
// 				GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m') UNION ALL
// 				SELECT 0 AS SUM,ROUND(SUM(t.estimate)/COUNT(DISTINCT t.finishedBy),2) AS allSum,DATE_FORMAT(t.finishedDate,'%Y年%m月') AS fDate
// 				FROM zt_task t,zt_user u WHERE t.finishedby = u.account and date_format(t.finishedDate,'%Y-%m') 
// 				BETWEEN '$startDate' and '$finishedDate' AND u.dept in(1,2,3,4,5,6,7) AND t.estimate<>0
// 				GROUP BY DATE_FORMAT(t.finishedDate,'%Y-%m'))p" )->groupBy('p.fDate')->fetchAll();
		return $eachAverage;
	}
	/*标准薪酬对比分析*/
	public function salaryContrast($startDate,$finishedDate){
		$ictUser = $this->queryICT();
		$singleDay = array();//普通员工
		$lastSingle = array();//普通员工上个月
		$mSingle   = array();//项目经理
		$mLastSingle = array();//项目经理上个月
		$array = array();
		for ($i=0;$i<count($ictUser);$i++){
			if($ictUser[$i]->role==3){
				$personnel = $this->getPersonnelCount($ictUser[$i]->account, $startDate, $finishedDate);
				array_push($singleDay, $personnel);
			}
		}
		for ($i=0;$i<count($ictUser);$i++){
			if($ictUser[$i]->role==2){
				$mCount = $this->getManagerCount($ictUser[$i]->account, $startDate, $finishedDate);
				array_push($singleDay, $mCount);
			}
		}
		$singleDay = array_filter($singleDay);
		$singleDay = array_values($singleDay);
// 		$length = date('m',strtotime($finishedDate))-date('m',strtotime($startDate));
		$length = abs(date("Y",strtotime($finishedDate))-date("Y",strtotime($startDate)))*12+date("m",strtotime($finishedDate))-date("m",strtotime($startDate))+1;
		$temp = array("upNum"=>"0","lowNum"=>"0","date"=>"");
		for ($m=0;$m<=$length;$m++){
			for ($i=0;$i<count($singleDay);$i++){
				if (isset($singleDay[$i])){
					for ($j=0;$j<count($singleDay[$i]);$j++){
						if ($singleDay[$i][$j]->year == date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)))
								&& $singleDay[$i][$j]->finalSalary >= $this->getSalary($singleDay[$i][$j]->account)->standsalary){
// 							$temp['upNum'] = $temp['upNum'] +1;
							$temp['date'] = date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)));
							if (isset($array[$m]['date'])){
								$array[$m]['upNum'] = $array[$m]['upNum']+1;
							}else {
								$temp['upNum'] = 1;
								$temp['date'] = date('Y年m月',strtotime($temp['date']));
								array_push($array, $temp);
							}
						}else if ($singleDay[$i][$j]->year == date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)))
								&& $singleDay[$i][$j]->finalSalary < $this->getSalary($singleDay[$i][$j]->account)->standsalary){
// 							echo $singleDay[$i][$j]->finalSalary . '&nbsp;&nbsp;';
// 							$temp['lowNum'] = $temp['lowNum'] +1;
							$temp['date'] = date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)));
							if (isset($array[$m]['date'])){
								$array[$m]['lowNum'] = $array[$m]['lowNum']+1;
							}else {
								$temp['lowNum'] = 1;
								$temp['date'] = date('Y年m月',strtotime($temp['date']));
								array_push($array, $temp);
							} 
						}
					}
				}
			}
		}
// 		for ($m=0;$m<=$length;$m++){
// 			for ($i=0;$i<count($mSingle);$i++){
// 				for ($j=0;$j<count($mSingle[$i]);$j++){
// 					if ($mSingle[$i][$j]->year == date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)))
// 							&& $mSingle[$i][$j]->finalSalary >= $this->getSalary($mSingle[$i][$j]->account)->standsalary){
// 						$array[$m]->upNum = $array[$m]->upNum +1;
// 						$array[$m]->date = date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)));
// 					}else if ($mSingle[$i][$j]->year == date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)))
// 							&& $mSingle[$i][$j]->finalSalary < $this->getSalary($mSingle[$i][$j]->account)->standsalary){
// 						$array[$m]->lowNum = $array[$m]->lowNum +1;
// 						$array[$m]->date = date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$m)));
// 					}
// 				}
// 			}
// 		}
		return $array;
		
	}
	/*公司量化薪酬支出分析*/
	public function salaryPayAnalysis($startDate,$finishedDate, $orderBy){
		//普通员工
		$queryAllPerson = $this->getAllPerson($startDate, $finishedDate, $orderBy);
		//项目经理
		$getAllManager = $this->getAllManager($startDate, $finishedDate, $orderBy);
		$getAllUser = $this->dao->select('*')->from(TABLE_ICTUSER)->where('dept=1')->fetchAll();
		$singleDay = array();
		$data = array("upTotal"=>"0","lowTotal"=>"0","total"=>"0","date"=>"");
		$temp = array();
		for ($i=0;$i<count($getAllUser);$i++){
			if ($getAllUser[$i]->role==3){
				$getPersonnelCount = $this->getPersonnelCount($getAllUser[$i]->account, $startDate, $finishedDate);
				array_push($singleDay, $getPersonnelCount);
			}
			else if ($getAllUser[$i]->role==2){
				$getManagerCount = $this->getManagerCount($getAllUser[$i]->account, $startDate, $finishedDate);
				array_push($singleDay, $getManagerCount);
			}
		}
			$singleDay = array_filter($singleDay);
			$singleDay = array_values($singleDay);
			$length = abs(date("Y",strtotime($finishedDate))-date("Y",strtotime($startDate)))*12+date("m",strtotime($finishedDate))-date("m",strtotime($startDate))+1;
			$eachMonth = array();
			for ($j=0;$j<$length;$j++){
				for ($i=0;$i<count($singleDay);$i++){
// 					$singleDay[$i][$j]->increase = 	$singleDay[$i][$j]->finalSalary-$singleDay[$i][$j]->standsalary;
					if (isset($singleDay[$i][$j]) && 
							$singleDay[$i][$j]->year == date('Y-m',strtotime(date('Y',strtotime($finishedDate)).'-'.(date('m',strtotime($startDate))+$j))))
						$eachMonth[$j][$i]=$singleDay[$i][$j];
				}
						if (isset($eachMonth[$j]))$eachMonth[$j] = array_values($eachMonth[$j]);
			}
			for ($i=0;$i<$length;$i++){
				if (isset($eachMonth[$i])){
					for ($j=0;$j<count($eachMonth[$i]);$j++){
						$data['date'] = $eachMonth[$i][$j]->year;
						if (isset($eachMonth[$i][$j]))$eachMonth[$i][$j]->increase = $eachMonth[$i][$j]->finalSalary-$eachMonth[$i][$j]->standsalary;
						if (isset($eachMonth[$i][$j]) &&$eachMonth[$i][$j]->increase>=0){
							if (isset($temp[$i]['upTotal']))$temp[$i]['upTotal'] = $temp[$i]['upTotal']+$eachMonth[$i][$j]->increase;
							else {
								$data['upTotal'] = $eachMonth[$i][$j]->increase;
								$data['date'] = date('Y年m月',strtotime($data['date']));
								array_push($temp, $data);
							}
						}else if (isset($eachMonth[$i][$j]) &&$eachMonth[$i][$j]->increase<0){
							if (isset($temp[$i]['lowTotal']))$temp[$i]['lowTotal'] = $temp[$i]['lowTotal']+$eachMonth[$i][$j]->increase;
							else {
								$data['lowTotal'] = $eachMonth[$i][$j]->increase;
								$data['date'] = date('Y年m月',strtotime($data['date']));
								array_push($temp, $data);
							}
						}
					}
				}
			}
			for ($i=0;$i<count($temp);$i++){
				$temp[$i]['total'] = $temp[$i]['upTotal']+$temp[$i]['lowTotal'];
			}
			return $temp;
	}
	
	/*查询ICT人员名单*/
	public function queryICT(){
		return $this->dao->select('*')->from(TABLE_ICTUSER)->where('dept')->eq(1)->fetchAll();
	}
}

