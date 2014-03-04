<?php

// $lang->searchObjects['indexcompare'] = 'IC:指标评比';
$lang->menu->indexcompare = '禅道度量|indexcompare|index';
// $lang->menuOrder[21] = 'repo';
/*
$lang->indexcompare->menu->list    = '%s';
$lang->indexcompare->menu->browse  = array('link' => '版本库|conn|browse|libID=%s', 'alias' => 'view,create,edit');
$lang->indexcompare->menu->edit    = '编辑|conn|editLib|libID=%s';
$lang->indexcompare->menu->module  = '分类|tree|browse|libID=%s&viewType=conn';
$lang->indexcompare->menu->delete  = array('link' => '删除|conn|deleteLib|libID=%s', 'target' => 'hiddenwin');
$lang->indexcompare->menu->create  = array('link' => '<span class="icon-add">&nbsp;</span>添加文档库|conn|createLib', 'float' => 'right');
*/

/**
 * The effort module zh-cn file of ZenTaoPMS.
 */

$lang->indexcompare->menu = $lang->indexcompare->menu;
/*添加导航条*/
$lang->menugroup->indexcompare   	= 'indexcompare';


$lang->indexcompare->menu->defectRate     = array('link' => '项目缺陷|indexcompare|defectrate|','alias' => 'personalrate');

$lang->indexcompare->menu->stability     = array('link' => '需求稳定度|indexcompare|stability', 'alias' => 'perstability');
$lang->indexcompare->menu->completed     = array('link' => '任务完成率|indexcompare|completed', 'alias' => 'percompleted');
$lang->indexcompare->menu->productivity     = array('link' => '生产率|indexcompare|productivity', 'alias' => 'perproductivity');
$lang->indexcompare->menu->performance     = array('link' => '绩效评审|indexcompare|performance', 'alias' => 'perperformance');

$lang->indexcompare->menu->generatedata     = array('link' => '生成数据|indexcompare|generatedata', 'alias' => 'generatedata');

/*
<th class='w-stability'>    <?php common::printOrderLink('stability',           $orderBy, $vars, $lang->indexcompare->stability);?></th>
	          <th class='w-completed'>   <?php common::printOrderLink('pri',          $orderBy, $vars, $lang->indexcompare->completed);?></th>
	          <th class='w-removed'>   <?php common::printOrderLink('name',         $orderBy, $vars, $lang->indexcompare->removed);?></th>
	          <th class='w-productivity'><?php common::printOrderLink('status',       $orderBy, $vars, $lang->indexcompare->productivity);?></th>
	          <th class='w-performance'>  
*/

?>