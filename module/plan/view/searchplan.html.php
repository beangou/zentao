<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>
<form method='post'  id='planform'>
  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
//       foreach($lang->plan->periods as $period => $label)
//       {
//           $vars = $period;
// //           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
//           echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
//       }
		
      foreach($mymenu as $period => $label)
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
  			<table  class='table-1 tablesorter colored datatable newBoxs'> 
			    <caption>
			    	<div align="center">输入起始时间:
			    	<?php 
      		echo html::input('beginDate', '', "class='select-2 date'").' ~ '.html::input('endDate', '', "class='select-2 date'");
//       		html::input("deadtime[]", '', "class=text-1");
      		?>
  						<!-- <input type="text" name="beginDate">~<input type="text" name="endDate"> -->
  						  <?php echo  
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'searchplan', "isSubmit=1") . "\")'") ;?>
  					</div>
  				</caption>
			    <thead>
			      <tr class='colhead'>
			      	  <th width="11%">时间</th>
				      <th><?php echo $lang->plan->sort;?></th>
				       
				      <th width="20%"><?php echo $lang->plan->matter;?></th>
				      <th width="30%"><?php echo $lang->plan->plan;?></th>
				      <th width="10%">完成时限</th>
				      <th>完成情况</th>
				      <!-- 
				      <th>见证性材料</th>
				      <th>未完成原因说明及如何补救</th>
				       -->
				      <th width="5%">确认人</th>
				      <th>是否确认</th>
				      <th>确认结果</th>
				  </tr>    
			    </thead>
			    <?php 
			    $stepID = 0;
			    if (!empty($searchPlans)):
			    foreach ($searchPlans as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <!-- <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td> -->
			      <?php if(!empty($plan->rowspanVal)) {
			      		echo '<td rowspan="'. $plan->rowspanVal. '">('. $plan->firstDayOfWeek. '<br/>~<br/>'. $plan->lastDayOfWeek. ')</td>';
			      }?>
			      <td><?php echo $plan->type;?></td>
			      <td style="text-align: left"><?php echo $plan->matter;?></td>
			      <td style="text-align: left"><?php echo $plan->plan;?></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      
			      <td><?php echo $plan->submitToName;?></td>
			      <td><?php echo $plan->confirmedOrNo;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			    </tr>
			    <?php endforeach;?>
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="9" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>
			  </table>
  		</td>
  	</tr>
  </table>
  
  
  
  
  
</form>
<?php include '../../common/view/footer.html.php';?>