<?php

class defect extends control
{
	/**
	 * 项目缺陷去除率
	 */
	public function defectRate()
	{
		$this->view->title = $this->lang->defect->common;
		$this->view->position[] 	= $this->lang->defect->common;
		$this->view->products		= $this->defect->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
// 				$defect = $this->defect->queryDefect($ids);
				$defect = $this->defect->myQueryDefect($ids);
			}
		}
		
// 		else {
// 			$defect = $this->defect->queryDefect(1);
// 		}
		$this->view->defectRate = $defect;
		$this->display();
	}
	
	/**
	 * 个人缺陷去除率
	 */
	public function personalRate()
	{
		$this->view->title = $this->lang->defect->common;
		$this->view->position[] 	= $this->lang->defect->common;
		$this->view->products		= $this->defect->getProduct();
		$defect 	= array();
		if (!empty($_POST)){
			foreach ($_POST as $ids){
// 				$defect = $this->defect->getPersonalRate($ids);
				$defect = $this->defect->myQueryPerDefect($ids);
			}
		}
		
// 		else {
// 			$defect = $this->defect->getPersonalRate(1);
// 		}
		$this->view->personalRate = $defect;
		$this->display();
	}
}