<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>

<style>
	pre {
		font-size: 18px;
	}		
	
	.matterStyle {
		font-size: 18px;
	}
</style>

  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
//       foreach($lang->plan->periods as $period => $label)
      foreach($mymenu as $period => $label)
      {
//           if ($period == 'collectplan') {continue;}
      	  $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
  </div>
  
  <input id="memberVal" type="hidden" value="<?php echo $memberVal;?>">
  
  <form name="form1" method='post'>
 选择成员：  
<?php 
  echo html::select('member', $mymember, '', 'onchange="queryPlan()"');?>
  <script type="text/javascript">
	$('#member').val($('#memberVal').val());
  </script>
 	
  <br/><br/>
	  <table class='table-1 tablesorter colored datatable newBoxs'> 
	    <caption><div align="center">上周周计划(&nbsp;<span class="accountName"></span>&nbsp;<?php echo $firstOfLastWeekDay. ' ~ '. $lastOfLastWeekDay;?>)</div></caption>
	    <thead>
	    	<tr class='colhead'>
	    	  <th width="3%">编号</th>	
		      <th width="5%"><?php echo $lang->plan->sort;?></th>
		      <th width="15%"><?php echo $lang->plan->matter;?></th>
		      <th width="20%"><?php echo $lang->plan->plan;?></th>
		      <th width="6%">完成时限</th>
		      <th width="6%">完成情况</th>
		      <th width="10%">见证性材料</th>
		      <th width="14%">未完成原因说明及如何补救</th>
		      <th width="5%">确认人</th>
		      <th width="6%">确认结果</th>
		    </tr>  
	    </thead>
	    <tbody>
	    	<?php 
			    $stepID = 0;
			    if (!empty($lastWeekPlan)):
			    foreach ($lastWeekPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      <td style="text-align: left" class="matterStyle"><?php echo $plan->matter;?></td>
			      <td style="text-align: left"><pre class='forShow'><?php echo $plan->plan;?></pre></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      <td><?php echo $plan->evidence;?></td>
			      <td><?php echo $plan->courseAndSolution;?></td>
			      <td><?php echo $plan->submitToName;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			    </tr>
			    <?php endforeach;?>
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="10" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>	
	    </tbody>
	  </table>
	  
	  <table class='table-1 tablesorter colored datatable newBoxs' style="margin-top:3%"> 
	    <caption><div align="center">本周周计划(&nbsp;<span class="accountName"></span>&nbsp;<?php echo $firstOfThisWeekDay. ' ~ '. $lastOfThisWeekDay;?>)</div></caption>
	    <thead>
	    	<tr class='colhead'>
	    	  <th width="3%">编号</th>	
		      <th width="5%"><?php echo $lang->plan->sort;?></th>
		      <th width="15%"><?php echo $lang->plan->matter;?></th>
		      <th width="20%"><?php echo $lang->plan->plan;?></th>
		      <th width="6%">完成时限</th>
		      <th width="6%">完成情况</th>
		      <th width="10%">见证性材料</th>
		      <th width="14%">未完成原因说明及如何补救</th>
		      <th width="5%">确认人</th>
		      <th width="6%">确认结果</th>
		   </tr>   
	    </thead>
	    <tbody>
	    	<?php 
			    $stepID = 0;
			    if (!empty($thisWeekPlan)):
			    foreach ($thisWeekPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      <td style="text-align: left" class="matterStyle"><?php echo $plan->matter;?></td>
			      <td style="text-align: left"><pre class='forShow'><?php echo $plan->plan;?></pre></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->status;?></td>
			      <td><?php echo $plan->evidence;?></td>
			      <td><?php echo $plan->courseAndSolution;?></td>
			      <td><?php echo $plan->submitToName;?></td>
			      <td><?php echo $plan->confirmed;?></td>
			    </tr>
			    <?php endforeach;?>
			       <tr>
				   		<td colspan="10" style="text-align:left">
				   			<strong>审核记录:</strong><br>
				    		<?php 
					    		if (empty($thisWeekAudits)) {
									echo '无记录';
								} else {
									$i = 1;
									foreach ($thisWeekAudits as $myaudit) {
										echo $i. '.&nbsp;'. $myaudit->auditTime. '&nbsp;&nbsp;审核人:&nbsp;'. $myaudit->realname. ' &nbsp;  审核结果:&nbsp;'. $myaudit->result;
										echo ',  &nbsp;&nbsp; 审核意见:&nbsp;'. $myaudit->auditComment. '<br/>';
										$i++;
									}					
								}
				    		?>
				    	</td>
				    </tr> 
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="10" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>	
	    </tbody>
	  </table>
	 
	 <?php
// 	  echo	html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'");
	  ?> 
	</form>  
	
	<table  class='table-1 tablesorter colored datatable newBoxs' style="margin-top: 5%"> 
			    <caption><div align="center">下周周计划(&nbsp;<span class="accountName"></span>&nbsp;<?php echo $firstOfNextWeekDay. ' ~ '. $lastOfNextWeekDay;?>)</div></caption>
			    <thead>
			      <tr class='colhead'>
				      <th>编号</th>
				      <th><?php echo $lang->plan->sort;?></th>
				       
				      <th width="25%"><?php echo $lang->plan->matter;?></th>
				      <th width="25%"><?php echo $lang->plan->plan;?></th>
				      <th>完成时限</th>
				      <th>确认人</th>
				  </tr>    
			    </thead>
			    <?php 
			    $stepID = 0;
			    if (!empty($nextWeekPlan)):
			    foreach ($nextWeekPlan as $plan):
			    $stepID += 1;
			    ?>
			    <tr class='a-center'>
			      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
			      <td><?php echo $plan->type;?></td>
			      
			      <td style="text-align: left" class="matterStyle"><?php echo $plan->matter;?></td>
			      <td style="text-align: left"><pre class='forShow'><?php echo $plan->plan;?></pre></td>
			      <td><?php echo $plan->deadtime;?>
			      <td><?php echo $plan->submitToName;?></td>
			    </tr>
			    <?php endforeach;?>
			    <tr>
				   		<td colspan="6" style="text-align:left">
				   			<strong>审核记录:</strong><br>
				    		<?php 
					    		if (empty($nextWeekAudits)) {
									echo '无记录';
								} else {
									$i = 1;
									foreach ($nextWeekAudits as $myaudit) {
										echo $i. '.&nbsp;'. $myaudit->auditTime. '&nbsp;&nbsp;审核人:&nbsp;'. $myaudit->realname. ' &nbsp;  审核结果:&nbsp;'. $myaudit->result;
										echo ',  &nbsp;&nbsp; 审核意见:&nbsp;'. $myaudit->auditComment. '<br/>';
										$i++;
									}					
								}
				    		?>
				    	</td>
				    </tr> 
			    <?php else :
			    $stepID = 1;
			    ?>
			    <tr class='a-center' id="row<?php echo $stepID?>">
			      <td class='stepID' colspan="9" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td>
			    </tr>
			    <?php endif;?>
		</table>
  <script type="text/javascript">
	$('.accountName').text($('#member').find("option:selected").text());
  </script>
<?php include '../../common/view/footer.html.php';?>