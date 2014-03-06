<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<form method='post'  id='planform'>
  	<div id='topmyplan'>
    <div class='f-left'>
      <?php 
      foreach($lang->plan->periods as $period => $label)
      {
          $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
  </div>
  <table>
  	<tr valign="top">
  		<td width="10%" style="padding-right: 10px;" class="side">
			<div class="box-title">查询方式</div>
			<div class="box-content">
				<ul id="report-list">
				  	<li><?php common::printLink('plan', 'queryplan', '', '查询近三周计划');?></li>
				  	<li><?php common::printLink('plan', 'searchplan', '', '按日期查询');?></li>
				</ul>
			</div>
		</td>
  		<td>
  			<table  class='table-1 tablesorter fixed colored datatable newBoxs'> 
			    <caption><div align="center">上周周计划(2014/02/22~2014/02/28)</div></caption>
			    <thead>
			      <tr class='colhead'>
				      <th>编号</th>
				      <th><?php echo $lang->plan->sort;?></th>
				      <th><?php echo $lang->plan->matter;?></th>
				      <th><?php echo $lang->plan->plan;?></th>
				      <th>完成时限</th>
				      <th>完成情况</th>
				      <th>见证性材料</th>
				      <th>未完成原因说明及如何补救</th>
				      <th>审核人</th>
				      <th>是否审核</th>
				      <th>审核结果</th>
				      <th>备注</th>
				  </tr>    
			    </thead>
			    <?php 
			    $stepID = 0;
			    if (!empty($lastPlan)):
			    foreach ($lastPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      <td><?php echo $plan->matter;?></td>
			      <td><?php echo $plan->plan;?></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      <td><?php echo $plan->evidence;?></td>
			      <td><?php echo $plan->courseAndSolution;?></td>
			      <td><?php echo $plan->submitTo;?></td>
			      <td><?php echo $plan->confirmedOrNo;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			      <td><?php echo $plan->remark;?></td>
			    </tr>
			    <?php endforeach;?>
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>
			  </table>
			  
			  
			  <table  class='table-1 tablesorter fixed colored datatable newBoxs' style="margin-top: 5%"> 
			    <caption><div align="center">本周周计划(2014/03/01~2014/03/07)</div></caption>
			    <thead>
			      <tr class='colhead'>
				      <th>编号</th>
				      <th><?php echo $lang->plan->sort;?></th>
				      <th><?php echo $lang->plan->matter;?></th>
				      <th><?php echo $lang->plan->plan;?></th>
				      <th>完成时限</th>
				      <th>完成情况</th>
				      <th>见证性材料</th>
				      <th>未完成原因说明及如何补救</th>
				      <th>审核人</th>
				      <th>是否审核</th>
				      <th>审核结果</th>
				      <th>备注</th>
				  </tr>    
			    </thead>
			    <?php 
			    $stepID = 0;
			    if (!empty($weekPlan)):
			    foreach ($weekPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      <td><?php echo $plan->matter;?></td>
			      <td><?php echo $plan->plan;?></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      <td><?php echo $plan->evidence;?></td>
			      <td><?php echo $plan->courseAndSolution;?></td>
			      <td><?php echo $plan->submitTo;?></td>
			      <td><?php echo $plan->confirmedOrNo;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			      <td><?php echo $plan->remark;?></td>
			    </tr>
			    <?php endforeach;?>
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>
			  </table>
			  
			  
			  <table  class='table-1 tablesorter fixed colored datatable newBoxs' style="margin-top: 5%"> 
			    <caption><div align="center">下周周计划(2014/03/08~2014/03/14)</div></caption>
			    <thead>
			      <tr class='colhead'>
				      <th>编号</th>
				      <th><?php echo $lang->plan->sort;?></th>
				      <th><?php echo $lang->plan->matter;?></th>
				      <th><?php echo $lang->plan->plan;?></th>
				      <th>完成时限</th>
				      <th>完成情况</th>
				      <th>见证性材料</th>
				      <th>未完成原因说明及如何补救</th>
				      <th>审核人</th>
				      <th>是否审核</th>
				      <th>审核结果</th>
				      <th>备注</th>
				  </tr>    
			    </thead>
			    <?php 
			    $stepID = 0;
			    if (!empty($nextPlan)):
			    foreach ($nextPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      <td><?php echo $plan->matter;?></td>
			      <td><?php echo $plan->plan;?></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      <td><?php echo $plan->evidence;?></td>
			      <td><?php echo $plan->courseAndSolution;?></td>
			      <td><?php echo $plan->submitTo;?></td>
			      <td><?php echo $plan->confirmedOrNo;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			      <td><?php echo $plan->remark;?></td>
			    </tr>
			    <?php endforeach;?>
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>
			  </table>
  		</td>
  	</tr>
  </table>
  
</form>

<?php include '../../common/view/footer.html.php';?>