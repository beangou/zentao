<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>

  
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
  
  <table class='table-1' id="commentPlan"> 
    <caption><div align="center">自评本周计划完成情况(<?php echo $firstOfThisWeekDay. ' ~ '. $lastOfThisWeekDay;?>)</div></caption>
    <thead>
      <th>编号</th>
      <th><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th><?php echo $lang->plan->plan;?></th>
      <th>完成时限</th>
      <th>完成情况</th>
      <th>见证性材料</th>
      <th>未完成原因说明及如何补救</th>
      <th>确认人</th>
      <th>确认结果</th>
    </thead>
    <?php 
    $stepID = 0;
    if (!empty($thisWeekPlan)):
    foreach ($thisWeekPlan as $plan):
    $stepID += 1;
    ?>
    <tr class='a-center'>
      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
      <td><?php echo $plan->type;?></td>
      <td><?php echo $plan->matter;?></td>
      <td><?php echo $plan->plan;?></td>
      <td><?php echo $plan->deadtime;?>
      <td><select name='status[]' <?php if($plan->confirmed == '通过'){echo 'disabled';}?>>
      		<option value='完成' <?php if('完成'==$plan->status){echo 'selected="selected"';}?>>完成</option>
      		<option value='延期完成' <?php if('延期完成'==$plan->status){echo 'selected="selected"';}?>>延期完成</option>
      		<option value='经领导允许延期完成' <?php if('经领导允许延期完成'==$plan->status){echo 'selected="selected"';}?>>经领导允许延期完成</option>
      		<option value='未完成' <?php if('未完成'==$plan->status){echo 'selected="selected"';}?>>未完成</option>	
          </select>
      </td>
      <td>
      	<?php if($plan->confirmed != '通过'){echo html::input("evidence[]", $plan->evidence, "class=text-1");}
      			else {echo $plan->evidence;}
      	?>
      </td>
      <td><?php 
	      if($plan->confirmed != '通过'){echo html::input("courseAndSolution[]", $plan->courseAndSolution, "class=text-1");}
	      else {echo $plan->courseAndSolution;}
      ?></td>
      <td><?php echo $plan->submitToName;?></td>
      <td><?php if(!empty($plan->confirmed)) {
      	echo $plan->confirmed;} else {
			echo '未审核';
      }?></td>
    </tr>
    <?php $link = $this->createLink('plan', 'myplan', "isSubmit=1")?>
    <tr><td colspan='10' class='a-center'><?php echo  
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=0") . "\")'") ;?>
    </td></tr>
    <?php endforeach;?>
    <?php else :
    $stepID = 1;
    ?>
    <tr class='a-center'>
      <td class='stepID' colspan="10">无数据</td>
    </tr>
    <?php endif;?>
  </table>
  </form>
  
  <form method='post'>
  <table class='table-1' id="addPlan" style="margin-top: 5%"> 
    <caption><div align="center">填写下周计划(<?php echo $firstOfNextWeekDay. ' ~ '. $lastOfNextWeekDay;?>)</div></caption>
    <thead>
      <th>编号</th>
      <th width="5%"><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th><?php echo $lang->plan->plan;?></th>
      <th width="15%">完成时限</th>
      <th width="8%">确认人</th>
      <th width="5%">审核结果</th>
      <th width="15%">审核意见</th>
    </thead>
    
    
    <?php 
    $stepAddID = 0;
//    if (!empty($nextWeekPlan)):
    foreach ($nextWeekPlan as $plan):
    $stepAddID += 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepAddID?>">
      <td class='stepAddID'>
      	<?php echo $stepAddID ; echo html::hidden("ids[]", $plan->id, "class=text-1").
      	html::hidden("auditIds[]", $plan->auditId, "class=text-1");?></td>
      <?php 
//       if($plan->confirmed != '通过'){
	      echo '<td>'. html::input("type[]", $plan->type, "class=text-1"). '</td>
	      <td>'. html::input("matter[]", $plan->matter, "class=text-1").'</td>
	      <td>'. html::input("plan[]", $plan->plan, "class=text-1"). '</td>
	      <td>';
	       echo html::input('deadtime[]', date('Y-m-d',strtotime($plan->deadtime)), "class='select-2 date'");
									       
	       echo '</td><td>'.html::select('submitTo[]', $users, $plan->submitTo, "class='select-1'"). '</td><td>';
	      if(empty($plan->result)){
			echo '未审核</td>';		  	
		  } else {
			echo '不同意</td>';
		  }

		  if($stepAddID == 1) {
		  	echo '<td rowspan="'. count($nextWeekPlan). '">'. $plan->auditComment. '</td>';
		  }
		  
	     echo '<td>'. html::commonButton($lang->plan->delete, "onclick='deleteRow($stepAddID)'").html::commonButton($lang->plan->add, "onclick='postInsert($stepAddID)'"). '</td>';
      ?>
      
    </tr>
    <?php endforeach;?>
    <?php 
   // else :
    $stepAddID++;
    ?>
    <tr class='a-center' id="row<?php echo $stepAddID?>">
      <td class='stepAddID'><?php echo $stepAddID ;?></td>
      <td><?php echo html::input("type[]", '', "class='select-1' onkeyup='this.value=this.value.toUpperCase()'");?></td>
      <td><?php echo html::input("matter[]", '', 'class="text-1"');?></td>
      <td><?php echo html::input("plan[]", '', "class=text-1");?></td>
      <td id='copyDateTd'><?php 
      		echo html::input('deadtime[]', '', "class='select-2 date'");
//       		html::input("deadtime[]", '', "class=text-1");
      		?>
      </td>
      <td id="selectName">
      	 <?php 
			    echo html::select('submitTo[]', $users, '', "class='select-1'");
		 ?>
      	 <?php 
//       		echo html::input("submitTo[]", '', "class=text-1");?>
      </td>
      <td>未审核</td>
      <td></td>
      <td><?php echo html::commonButton($lang->plan->delete, "onclick='deleteRow($stepAddID)'").html::commonButton($lang->plan->add, "onclick='postInsert($stepAddID)'")?></td>
      
    </tr>
    <?php 
 //   endif;?>
    <?php $link = $this->createLink('plan', 'myplan', "isSubmit=1");?>
    <tr><td colspan='9' class='a-center'>
    <?php echo  
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'");?>
    </td></tr>
  </table>
  
</form>
<?php include '../../common/view/footer.html.php';?>