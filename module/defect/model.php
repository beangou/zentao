<?php

class defectModel extends model
{
	/**
	 * 查询产品
	 */
	public function getProduct($mode = '')
	{
		$orderBy  = !empty($this->config->product->orderBy) ? $this->config->product->orderBy : 'isClosed';
		$mode    .= $this->cookie->productMode;
		$products = $this->dao->select('*,  IF(INSTR(" closed", status) < 2, 0, 1) AS isClosed')
		->from(TABLE_PRODUCT)
		->where('deleted')->eq(0)
		->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
		->orderBy($orderBy)
		->fetchAll();
		$pairs = array();
		foreach($products as $product)
		{
			if($this->loadModel('product')->checkPriv($product))
			{
				$pairs[$product->id] = $product->name;
			}
		}
		return $pairs;
	}
	
	public function queryDefect($ids = '')
	{
		/*查询*/
		$time=date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));
		$defect = $this->dao->select('t1.id,t1.name as project,t1.begin,t1.end,t3.id as productId,t3.name as product,0 as self,0 as total,0 as defect')
				->from(TABLE_PROJECT)
				->alias('t1')->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.project')
				->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t2.product = t3.id')->where('t1.deleted')->eq('0')
				->andWhere('date(t1.end)')->le(date('Y-m-d'))->andWhere('t3.id')->in($ids)
				->fetchAll();
		$total = $this->dao->select('project,count(*) as total')->from(TABLE_BUG)->groupBy('project')->fetchAll();
		$self = $this->dao->select('project,count(*) as self')->from(TABLE_BUG)->where('title')
				->like('%自测%')->orWhere('title')->like('%内测%')->groupBy('project')->fetchAll();
		foreach ($defect as $rate)
		{
			foreach ($total as $bugs){
				if (isset($bugs->total) && $bugs->project == $rate->id)$rate->total = $bugs->total; 
			}
			foreach ($self as $selfBug){
				if (isset($selfBug->self) && $selfBug->project == $rate->id)$rate->self = $selfBug->self;
			}
			if (!$rate->total == 0)$rate->defect = round($rate->self/$rate->total,4);
			$getDefect = $this->dao->select('*')->from(TABLE_ICTDEFECT)->where('product')->eq($rate->productId)
							->andWhere('project')->eq($rate->id)->fetch();
			if (!empty($getDefect) && $getDefect->end >= $time){
				$update->total  = $rate->total;
				$update->defect = $rate->defect;
				$update->selfBug= $rate->selfBug;
				$this->dao->update(TABLE_ICTDEFECT)->data($update)->where('product')->eq($rate->productId)
					->andWhere('project')->eq($rate->id)->autoCheck()->exec();
			}
			else if (empty($getDefect)){
				$data->product = $rate->productId;
				$data->project = $rate->id;
				$data->selfBug = $rate->self;
				$data->total   = $rate->total;
				$data->defect  = $rate->defect;
				$data->begin   = $rate->begin;
				$data->end     = $rate->end;
				$this->dao->insert(TABLE_ICTDEFECT)->data($data)->exec();
			}
		}
// 		$defectRate = $this->dao->select('t1.*,t2.name as projectName,t3.name as productName')
// 			->from(TABLE_ICTDEFECT)->alias('t1')
// 			->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')->leftJoin(TABLE_PRODUCT)->alias('t3')
// 			->on('t1.product = t3.id')->where('t1.product')->in($ids)->fetchAll();
		$defectRate = $this->dao->select('id, code, name, PO')->from(TABLE_PRODUCT)->where('deleted')->eq(0)
				->andWhere('id')->in($ids)->fetchAll('id');
		$childRate = $this->dao->select('t1.*,t2.name as projectName')
			->from(TABLE_ICTDEFECT)->alias('t1')
			->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
			->where('t1.product')->in(array_keys($defectRate))->orderBy('t1.end')->fetchAll('id');
		foreach($childRate as $child) $defectRate[$child->product]->details[$child->id] = $child;
		return $defectRate;
	}
	public function getPersonalRate($ids = '')
	{
		$details = array();
		$account = $this->app->user->account;
		$projects = $this->dao->select('t1.id,t1.name,t1.begin,t1.end')->from(TABLE_PROJECT)->alias('t1')
					->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.project')
					->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t2.product = t3.id')->where('t1.deleted')->eq('0')
					->andWhere('t1.id')->ne(0)->andWhere('t3.id')->in($ids)->fetchAll('id');
		$total = $this->dao->select('product,project,count(*) as total')->from(TABLE_BUG)
					->where('product')->in($ids)->groupBy('project')->fetchAll();
		$self = $this->dao->select('t1.id,t1.product,t1.project,t1.resolvedBy,count(t1.resolvedBy) as self,t2.realname')->from(TABLE_BUG)
					->alias('t1')->leftJoin(TABLE_USER)->alias('t2')->on('t1.resolvedBy = t2.account')
					->where('t1.product')->in($ids)->andWhere('t1.resolvedBy')->ne('')
					->groupBy('t1.project,t1.resolvedBy')->orderBy('t1.resolvedBy')->fetchAll();
// 		foreach ($total as $all){
// 			if(!isset($details[$all->project])) $details[$all->project] = new stdclass();
// 			$details[$all->project]->total = $all->total;
// 		}
		for ($i=0;$i<count($self);$i++){
			for ($j=0;$j<count($total);$j++){
				if ($self[$i]->project == $total[$j]->project)$self[$i]->rate = round($self[$i]->self/$total[$j]->total,4);
			}
		}
		foreach ($self as $honor){
			if ($honor->project != 0)$projects[$honor->project]->details[$honor->id] = $honor;
		}
		return $projects;
	}
	
	//更改：项目缺陷缺陷去除率
	public function myQueryDefect($ids = '') {
		//测试阶段发现bug
		$testBugs = $this->dao->select('t1.product, t4.name AS productname, t1.project, t3.name AS projectname, 0 AS devbugs, COUNT(*) AS testbugs, COUNT(*) AS allbugs, \'0%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_USER)->alias('t2')->on('t1.openedBy = t2.account')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t2.role')->ne('dev')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project')->orderBy('t1.product, t1.project')
		->fetchAll();
		$testBugLen = count($testBugs); 
		
		//研发阶段发现bug
		$devBugs = $this->dao->select('t1.project, COUNT(*) AS devbugs, COUNT(*) AS allbugs, \'100%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_USER)->alias('t2')->on('t1.openedBy = t2.account')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t2.role')->eq('dev')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project')->orderBy('t1.product, t1.project')
		->fetchAll();
		$devBugLen = count($devBugs);
		
		//组合两个阶段发现的bug
		for ($j=0; $j<$devBugLen; $j++) {
			//标志：用于判断测试阶段和研发阶段的记录是否可以合并，即显示在同一行内，否则，应该显示为两条记录
			$flag = 0;
			for ($i=0; $i<$testBugLen; $i++) {
				if ($testBugs[$i]->project == $devBugs[$j]->project) {
					$testBugs[$i]->devbugs = $devBugs[$i]->devbugs;
					$testBugs[$i]->allbugs = ($devBugs[$j]->devbugs + $testBugs[$i]->testbugs);
					$testBugs[$i]->defect = (100*round($testBugs[$i]->devbugs / ($testBugs[$i]->devbugs + $testBugs[$i]->testbugs), 4)). '%';
					$flag = 1;
					break;
				}	
			}
			//如果没有进行合并，需要将研发阶段的记录作为数组中一元素加到返回数组中
			if ($flag == 0) {
				array_push($testBugs, $devBugs[$j]);
			}
		}
		
		
// 		T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs, 
// 				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect
		
		$newResult = $this->dao->select('T1.product, T2.name AS productname, T3.name AS projectname, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs, 
				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect')
				->from(TABLE_ICTDEFECT)->alias('T1')
				->leftJoin(TABLE_PRODUCT)->alias('T2')->on('T2.id = T1.product')
				->leftJoin(TABLE_PROJECT)->alias('T3')->on('T3.id = T1.project')
				->where('T1.product')->in($ids)
				->groupBy('T1.product, T1.project')
				->orderBy('T1.product, T1.project')
				->fetchAll();
		
		return defect::dealArrForRowspan($newResult, 'product');
	}
	
	//更改：个人缺陷缺陷去除率
	public function myQueryPerDefect($ids = '') {
		//测试阶段发现bug
		$testBugs = $this->dao->select('t1.project, t3.name, t1.assignedTo, 0 AS devbugs, COUNT(*) AS testbugs, COUNT(*) AS allbugs, \'0%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t1.openedBy != t1.assignedTo')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project, t1.assignedTo')->orderBy('t1.product, t1.project')
		->fetchAll();
		$testBugLen = count($testBugs);
		//->ne('t1.assignedTo')
		
		//研发阶段发现bug
		$devBugs = $this->dao->select('t1.project, t3.name, t1.assignedTo, COUNT(*) AS devbugs, COUNT(*) AS allbugs, \'100%\' AS defect')->from(TABLE_BUG)->alias('t1')
		->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t1.project')
		->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t4.id = t1.product')
		->where('t1.openedBy = t1.assignedTo')
		->andWhere('t1.product')->in($ids)
		->groupBy('t1.project, t1.assignedTo')
		->orderBy('t1.project, t1.assignedTo')
		->fetchAll();
		$devBugLen = count($devBugs);
		
		for ($j=0; $j<$devBugLen; $j++) {
			//标志：用于判断测试阶段和研发阶段的记录是否可以合并，即显示在同一行内，否则，应该显示为两条记录
			$flag = 0;
			for ($i=0; $i<$testBugLen; $i++) {
				if (($testBugs[$i]->project == $devBugs[$j]->project) && ($testBugs[$i]->assignedTo == $devBugs[$j]->assignedTo)) {
					$testBugs[$i]->devbugs = $devBugs[$j]->devbugs;
					$testBugs[$i]->allbugs = ($testBugs[$i]->devbugs + $testBugs[$i]->testbugs);
					$testBugs[$i]->defect = (100*round($testBugs[$i]->devbugs / ($testBugs[$i]->devbugs + $testBugs[$i]->testbugs), 4)). '%';
					$flag = 1;
					break;
				}
			}
			//如果没有进行合并，需要将研发阶段的记录作为数组中一元素加到返回数组中
			if ($flag == 0) {
				array_push($testBugs, $devBugs[$j]);
			}
		}
		
		
		$newResult = $this->dao->select('T1.project, T2.name AS projectname, T1.developer, SUM(T1.devBug) AS devbugs, SUM(T1.testBug) AS testbugs,
				(SUM(T1.devBug)+SUM(T1.testBug)) AS allbugs, SUM(T1.devBug)/(SUM(T1.devBug)+SUM(T1.testBug)) AS defect')
						->from(TABLE_ICTDEFECT)->alias('T1')
						->leftJoin(TABLE_PROJECT)->alias('T2')->on('T2.id = T1.project')
						->where('T1.product')->in($ids)
						->groupBy('T1.project, T1.developer')
						->orderBy('T1.product, T1.project')
						->fetchAll();
	
		return defect::dealArrForRowspan($newResult, 'project');
	}
}