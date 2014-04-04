<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>

<head>     
<meta   http-equiv="Expires"   CONTENT="0">     
<meta   http-equiv="Cache-Control"   CONTENT="no-cache">     
<meta   http-equiv="Pragma"   CONTENT="no-cache">     
</head>  

<input id="evaluateId" type="hidden" value="<?php echo $evaluateResult;?>"/>
<input id="createId" type="hidden" value="<?php echo $createResult;?>"/>
<input id="changeId" type="hidden" value="<?php echo $changeResult;?>"/>

<script type="text/javascript">
	if ('true' == $('#evaluateId').val()) {
		alert('自评本周计划成功！');
	}
	
	if ('true' == $('#createId').val()) {
		alert('填写下周计划成功！');
	}

	if ('true' == $('#changeId').val()) {
		alert('修改本周计划成功！');
	}
	
</script>

  
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

  <form method='post' id='planform'>
  
  <table class='table-1 tablesorter colored datatable newBoxs' id="commentPlan"> 
    <caption><div align="center">自评本周计划完成情况(<?php echo $firstOfThisWeekDay. ' ~ '. $lastOfThisWeekDay;?>)</div></caption>
    <thead>
    	<tr class="colhead">
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
	      <th>备注</th>
	    </tr>  
    </thead>
    <?php 
    $stepID = 0;
    if (!empty($thisWeekPlan)):
    foreach ($thisWeekPlan as $plan):
    $stepID += 1;
    if(!empty($plan->status) && $plan->confirmedOrNo == '否') {$evaluateDisaAttr = ' disabled';}
    ?>
    <tr class='a-center'>
      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
      <td><?php echo $plan->type;?></td>
      <td style="text-align: left"><?php echo $plan->matter;?></td>
      <td style="text-align: left"><?php echo $plan->plan;?></td>
      <td><?php echo $plan->deadtime;?></td>
      <td><select name='status[]' <?php echo $evaluateDisaAttr;?>>
      		<option value='完成' <?php if('完成'==$plan->status){echo 'selected="selected"';}?>>完成</option>
      		<option value='延期完成' <?php if('延期完成'==$plan->status){echo 'selected="selected"';}?>>延期完成</option>
      		<option value='经领导允许延期完成' <?php if('经领导允许延期完成'==$plan->status){echo 'selected="selected"';}?>>经领导允许延期完成</option>
      		<option value='未完成' <?php if('未完成'==$plan->status){echo 'selected="selected"';}?>>未完成</option>	
          </select>
      </td>
      <td>
      	<?php echo html::input("evidence[]", $plan->evidence, "class=text-1". $evaluateDisaAttr);
      	?>
      </td>
      <td><?php 
	      echo html::input("courseAndSolution[]", $plan->courseAndSolution, "class=text-1". $evaluateDisaAttr);
      ?></td>
      <td><?php echo $plan->submitToName;?></td>
      <td><?php if(!empty($plan->confirmed)) {
      	echo $plan->confirmed;} else {
			echo '未确认';
      }?></td>
      <td><?php echo $plan->remark;?></td>
    </tr>
    
    <?php endforeach;?>
    <?php $link = $this->createLink('plan', 'myplan', "isSubmit=1")?>
    <tr><td colspan='11' class='a-center'>
    <input type="hidden" name="isSubmit" value="0">
    <?php 
//     	echo html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "") . "\", \"0\")'") ;
    	?>
   	<input type="submit" value=" 提交  " <?php echo $evaluateDisaAttr;?>>  
    </td></tr>
    <?php else :
    $stepID = 1;
    ?>
    <tr class='a-center'>
      <td class='stepID' colspan="11" style="text-align: right;">暂无数据！</td>
    </tr>
    <?php endif;?>
  </table>
  </form>
  
  <form method='post' id='addPlanform'>
  <table class='table-1 tablesorter colored datatable newBoxs' id="addPlan" style="margin-top: 5%"> 
    <caption>
    	<div align="center">
    		填写下周计划(<?php echo $firstOfNextWeekDay. ' ~ '. $lastOfNextWeekDay;?>)
    	</div>
    </caption>
    <thead>
    	<tr class="colhead">
	      <th width="3%">编号</th>
	      <th width="5%"><?php echo $lang->plan->sort;?></th>
	      <th width="25%"><?php echo $lang->plan->matter;?></th>
	      <th width="30%"><?php echo $lang->plan->plan;?></th>
	      <th width="15%">完成时限</th>
	      <th width="8%">确认人</th>
	      <th>操作</th>
	    </tr>  
    </thead>
    
    <?php 
    $stepAddID = 0;
 	if ($showFlag == '1'):
	    foreach ($nextWeekPlan as $plan):
	    $stepAddID += 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepAddID?>">
      <td class='stepAddID'>
      	<?php echo $stepAddID ; echo html::hidden("nextIds[]", $plan->id, "class=text-1").
      	html::hidden("auditIds[]", $plan->auditId, "class=text-1");?></td>
      <?php 
          $disabledAttr = ''; 
      	  if (empty($plan->result)) { $disabledAttr = 'disabled';}
      	  
	      echo '<td>'. html::input("type[]", $plan->type, "class=text-1 ". $disabledAttr). '</td>
	      <td style="text-align: left">'. html::textarea("matter[]", $plan->matter, 'rows="4" cols="50" '. $disabledAttr).'</td>
	      <td style="text-align: left">'. html::textarea("plan[]", $plan->plan, 'rows="4" cols="60" '. $disabledAttr). '</td>
	      <td>';
	       echo html::input('deadtime[]', date('Y-m-d',strtotime($plan->deadtime)), "class='select-2 date' ". $disabledAttr);
									       
	       echo '</td><td>'.html::select('submitTo[]', $users, $plan->submitTo, "class='select-1' ". $disabledAttr). '</td>';
		  
   	       echo '<td>'. html::commonButton($lang->plan->delete, "onclick='deleteRow($stepAddID, 0)' ". $disabledAttr).html::commonButton($lang->plan->add, "onclick='postInsert($stepAddID, 0)' ". $disabledAttr). '</td>';
      ?>
      
    </tr>
    <?php endforeach;?>
    <?php 
   // else :
    $stepAddID++;
    ?>
    <tr class='a-center' id="row<?php echo $stepAddID?>">
      <td class='stepAddID'><?php echo $stepAddID ;?></td>
      <td id="copyType1"><?php echo html::input("type[]", '', "class='text-1' onkeyup='this.value=this.value.toUpperCase()' ". $disabledAttr);?></td>
      <td id="copyMatter1" style="text-align: left"><?php echo html::textarea("matter[]", '', 'rows="4" cols="50" '. $disabledAttr);?></td>
      <td id="copyPlan1" style="text-align: left"><?php echo html::textarea("plan[]", '', 'rows="4" cols="60" '. $disabledAttr);?></td>
      <td id='copyDateTd'><?php 
      		echo html::input('deadtime[]', '', "class='select-2 date' ". $disabledAttr);
//       		html::input("deadtime[]", '', "class=text-1");
      		?>
      </td>
      <td id="selectName1">
      	 <?php 
			    echo html::select('submitTo[]', $users, '', "class='select-1' ". $disabledAttr);
		 ?>
      	 <?php 
//       		echo html::input("submitTo[]", '', "class=text-1");?>
      </td>
      <!-- <td>未审核</td> -->
      <td><?php echo html::commonButton($lang->plan->delete, "onclick='deleteRow($stepAddID, 0)' ". $disabledAttr).html::commonButton($lang->plan->add, "onclick='postInsert($stepAddID, 0) '". $disabledAttr)?></td>
      
    </tr>

    <tr><td colspan='7' class='a-center'>
    <input type="hidden" name="isSubmit" value="1">
    
   <input type="submit" value=" 提交  " <?php echo $disabledAttr;?>>
    </td></tr>
    
    <tr><td colspan="7" style="text-align:left">
    		<strong>审核结果:</strong>
    		<?php 
    		if(empty($plan->result)){
				echo '未审核<br/>';		  	
			  } else {
				echo '不同意<br/>';
			  }?>
    		<strong>审核意见:</strong><?php echo $nextWeekPlan[0]->auditComment;?></td></tr>
    	
   <?php 
	   else :
    ?>
 	<tr><td colspan="7" style="text-align:right">您的下周计划已经审核通过！</td></tr>   
   <?php 
 	  endif;
 	?>
  </table>
  
  <div style="display: none">
  	  <span id="copyType"><?php echo html::input("type[]", '', "class='text-1' onkeyup='this.value=this.value.toUpperCase()'");?></span>
      <span id="copyMatter" style="text-align: left"><?php echo html::textarea("matter[]", '', 'rows="4" cols="50"');?></span>
      <span id="copyPlan" style="text-align: left"><?php echo html::textarea("plan[]", '', 'rows="4" cols="60"');?></span>
      <span id="selectName">
      	 <?php 
			    echo html::select('submitTo[]', $users, '', "class='select-1'");
		 ?>
      	 <?php 
//       		echo html::input("submitTo[]", '', "class=text-1");?>
      </span>
  </div>
</form>


<form method='post' id='changeThisPlanform'>
  <table class='table-1 tablesorter colored datatable newBoxs' id="changePlan" style="margin-top: 5%"> 
    <caption><div align="center">修改本周计划(<?php echo $firstOfThisWeekDay. ' ~ '. $lastOfThisWeekDay;?>)</div></caption>
    <thead>
    	<tr class="colhead">
	      <th width="3%">编号</th>
	      <th width="5%"><?php echo $lang->plan->sort;?></th>
	      <th width="25%"><?php echo $lang->plan->matter;?></th>
	      <th width="30%"><?php echo $lang->plan->plan;?></th>
	      <th width="15%">完成时限</th>
	      <th width="8%">确认人</th>
	      <th>操作</th>
	    </tr>  
    </thead>
    
    <?php 
    $stepChangeID = 0;
 	if (!empty($thisWeekUnAuditPlan)):
 		// 如果本周计划审核不通过
	    foreach ($thisWeekUnAuditPlan as $plan):
	    $stepChangeID += 1;
 	
	 	$changeDisabledAttr = '';
	 	if (empty($plan->result)) { $changeDisabledAttr = ' disabled';}
    ?>
    <tr class='a-center' id="row_this<?php echo $stepChangeID?>">
      <td class='stepChangeID'>
      	<?php echo $stepChangeID ; echo html::hidden("nextIds[]", $plan->id, "class=text-1").
      	html::hidden("auditIds[]", $plan->auditId, "class=text-1");?></td>
      <?php 
//       if($plan->confirmed != '通过'){
	      echo '<td>'. html::input("type[]", $plan->type, "class=text-1". $changeDisabledAttr). '</td>
	      <td style="text-align: left">'. html::textarea("matter[]", $plan->matter, 'rows="4" cols="50"'. $changeDisabledAttr).'</td>
	      <td style="text-align: left">'. html::textarea("plan[]", $plan->plan, 'rows="4" cols="60"'. $changeDisabledAttr). '</td>
	      <td>';
	       echo html::input('deadtime[]', date('Y-m-d',strtotime($plan->deadtime)), "class='select-2 date'". $changeDisabledAttr);
									       
	       echo '</td><td>'.html::select('submitTo[]', $users, $plan->submitTo, "class='select-1'". $changeDisabledAttr). '</td>';
	     

// 		  if($stepAddID == 1) {
// 		  	echo '<td rowspan="'. count($nextWeekPlan). '">'. $plan->auditComment. '</td>';
// 		  }
		  
	     echo '<td>'. html::commonButton($lang->plan->delete, "onclick=\"deleteRow('_this$stepChangeID', 1)\". $changeDisabledAttr").html::commonButton($lang->plan->add, "onclick=\"postInsert('_this$stepChangeID', 1)\". $changeDisabledAttr"). '</td>';
      ?>
      
    </tr>
    <?php endforeach;?>
    <?php 
   // else :
    $stepChangeID++;
    ?>
    <tr class='a-center' id="row_this<?php echo $stepChangeID?>">
      <td class='stepChangeID'><?php echo $stepChangeID ;?></td>
      <td id="this_copyType1"><?php echo html::input("type[]", '', "class='text-1' onkeyup='this.value=this.value.toUpperCase()'". $changeDisabledAttr);?></td>
      <td id="this_copyMatter1" style="text-align: left"><?php echo html::textarea("matter[]", '', 'rows="4" cols="50"'. $changeDisabledAttr);?></td>
      <td id="this_copyPlan1" style="text-align: left"><?php echo html::textarea("plan[]", '', 'rows="4" cols="60"'. $changeDisabledAttr);?></td>
      <td id='this_copyDateTd'><?php 
      		echo html::input('deadtime[]', '', "class='select-2 date'". $changeDisabledAttr);
//       		html::input("deadtime[]", '', "class=text-1");
      		?>
      </td>
      <td id="this_selectName1">
      	 <?php 
			    echo html::select('submitTo[]', $users, '', "class='select-1'". $changeDisabledAttr);
		 ?>
      	 <?php 
//       		echo html::input("submitTo[]", '', "class=text-1");?>
      </td>
      <td><?php echo html::commonButton($lang->plan->delete, "onclick=\"deleteRow('_this$stepChangeID', 1)\". $changeDisabledAttr").html::commonButton($lang->plan->add, "onclick=\"postInsert('_this$stepChangeID', 1)\". $changeDisabledAttr")?></td>
      
    </tr>

    <tr><td colspan='7' class='a-center'>
    <input type="hidden" name="isSubmit" value="2">
    
   <input type="submit" value=" 提交  " <?php echo $changeDisabledAttr;?>>
    </td></tr>
    
   <tr><td colspan="7" style="text-align:left">
   			<strong>审核结果:</strong>
    		<?php 
    		if(empty($plan->result)){
				echo '未审核<br/>';		  	
			  } else {
				echo '不同意<br/>';
			  }?>
    		<strong>审核意见：</strong><?php echo $thisWeekUnAuditPlan[0]->auditComment;?></td></tr> 
   <?php 
	   else :
    ?>
 	<tr><td colspan="7" style="text-align:right">暂无数据！</td></tr>   
   <?php 
 	  endif;
 	?>
  </table>
  
</form>


<?php include '../../common/view/footer.html.php';?>