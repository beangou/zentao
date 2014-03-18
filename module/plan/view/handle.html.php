<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php js::set('users', json_encode($users))?>

  <div id='featurebar'>
    <div class='f-left'>
      <?php 
      foreach($mymenu as $period => $label)
      {
          $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
  </div>

<form method='post' id='planform'>
  
  <table class='table-1 tablesorter colored datatable newBoxs'>
    <caption><div align="center">未确认计划</div></caption>
    <thead>
    <tr class='colhead'>
      <th width="7%">时间</th>
	  <th width="5%"><?php echo $lang->plan->account;?></th>
      <th width="3%"><?php echo $lang->plan->type;?></th>
      <th width="20%"><?php echo $lang->plan->matter;?></th>
      <th width="20%"><?php echo $lang->plan->plan;?></th>
      <th width="7%"><?php echo $lang->plan->deadtime;?></th>
      <th width="7%"><?php echo $lang->plan->status;?></th>
      <th width="7%"><?php echo $lang->plan->confirmed;?></th>
      <th width="15%"><?php echo $lang->plan->remark;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($uncheckedPlan)):?>
    <tr><td colspan="9" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($uncheckedPlan)):?>
    <?php foreach ((array)$uncheckedPlan as $week):?>
    <tr class='a-center'>
    	<?php if(!empty($week->rowspanVal)) {
			    echo '<td rowspan="'. $week->rowspanVal. '">('. $week->firstDayOfWeek. '<br/>~<br/>'. $week->lastDayOfWeek. ')</td>';
		}?>
    	<td><?php echo $week->accountname;?></td>
    	<td><?php echo $week->type;?><?php echo html::hidden("ids[]", $week->id, "");?></td>
    	<td><?php echo $week->matter;?></td>
    	<td><?php echo $week->plan;?></td>
    	<td><?php echo $week->deadtime;?></td>
    	<td><?php echo $week->status;?></td>
    	<td>
    		<select name='confirmed[]'>
    			<option value='不通过'>不通过</option>
    			<option value='通过'>通过</option>
    		</select>
    	</td>
    	<td><?php echo html::input("remark[]", $week->remark, "class=text-1");?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
    <?php if (!empty($uncheckedPlan)):?>
    <tr>
	    <td colspan='9' class='a-center'>
	    <?php echo  
	    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'handle', "isSubmit=1") . "\")'");?>
	    </td>
    </tr>
    <?php endif;?>
  </table>
  
</form>

<table class='table-1 tablesorter colored datatable newBoxs' style="margin-top: 5%">
  	<caption><div align="center">已确认计划</div></caption>
    <thead>
    <tr class='colhead'>
      <th width="7%">时间</th>
	  <th width="5%"><?php echo $lang->plan->account;?></th>
      <th width="3%"><?php echo $lang->plan->type;?></th>
      <th width="20%"><?php echo $lang->plan->matter;?></th>
      <th width="20%"><?php echo $lang->plan->plan;?></th>
      <th width="7%"><?php echo $lang->plan->deadtime;?></th>
      <th width="7%"><?php echo $lang->plan->status;?></th>
      <th width="7%"><?php echo $lang->plan->confirmed;?></th>
      <th width="15%"><?php echo $lang->plan->remark;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($checkPlan)):?>
    <tr><td colspan="9" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($checkPlan)):?>
    <?php foreach ((array)$checkPlan as $week):?>
    <tr class='a-center'>
    	<?php if(!empty($week->rowspanVal)) {
			    echo '<td rowspan="'. $week->rowspanVal. '">('. $week->firstDayOfWeek. '<br/>~<br/>'. $week->lastDayOfWeek. ')</td>';
		}?>
    	<td><?php echo $week->accountname;?></td>
    	<td><?php echo $week->type;?></td>
    	<td><?php echo $week->matter;?></td>
    	<td><?php echo $week->plan;?></td>
    	<td><?php echo $week->deadtime;?></td>
    	<td><?php echo $week->status;?></td>
    	<td><?php echo $week->confirmed;?></td>
    	<td><?php echo $week->remark;?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
  </table>
<?php include '../../common/view/footer.html.php';?>