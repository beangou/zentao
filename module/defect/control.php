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
		
		if ($rowspanValue > 0) {
			$temp[$rowspanIndex]->rowspanVal = $rowspanValue;
		}
		
		/* End. */
		return $temp;
	}
}