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
}