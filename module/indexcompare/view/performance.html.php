<?php
/**
 * The html template file of index method of install module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     ZenTaoPMS
 * @version     $Id: index.html.php 4129 2013-01-18 01:58:14Z wwccss $
 */
?>
 <?php include '../../common/view/header.html.php';?>
	指标评比:
	<form method='post' id='dataform'>
		<select name="proname">
			<option value="">选择项目</option>
			<option value="proa">项目A</option>
			<option value="prob">项目B</option>
			<option value="proc">项目C</option>
			<option value="prod">项目D</option>
		</select>
		
		<select name="empname">
			<option value="">选择职员</option>
			<option value="empa">职员A</option>
			<option value="empb">职员B</option>
			<option value="empc">职员C</option>
			<option value="empd">职员D</option>
		</select>
		
		<input type="submit" value="查询"/>
	</form>
	
	<table class='table-1 fixed colored tablesorter datatable' id='indexPriList'>
        <?php $vars = "projectID=$project->id&status=$status&parma=$param&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage"; ?>
        <thead>
	        <tr class='colhead'>
	          <th><?php common::printOrderLink('project',           $orderBy, $vars, $lang->indexcompare->project);?></th>
	          <th><?php common::printOrderLink('employee',           $orderBy, $vars, $lang->indexcompare->employee);?></th>
	          <th class='w-stability'>    <?php common::printOrderLink('stability',           $orderBy, $vars, $lang->indexcompare->stability);?></th>
	          <th class='w-completed'>   <?php common::printOrderLink('pri',          $orderBy, $vars, $lang->indexcompare->completed);?></th>
	          <th class='w-removed'>   <?php common::printOrderLink('name',         $orderBy, $vars, $lang->indexcompare->removed);?></th>
	          <th class='w-productivity'><?php common::printOrderLink('status',       $orderBy, $vars, $lang->indexcompare->productivity);?></th>
	          <th class='w-performance'>  <?php common::printOrderLink('deadline',     $orderBy, $vars, $lang->indexcompare->performance);?></th>
	        </tr>
        </thead>
        
        
    </table>
	
<?php include '../../common/view/footer.html.php';?>
