<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<form method='post' id='planform'>
  <div id='featurebar'>
    <div class='f-left'>
      <?php 
      foreach($lang->plan->periods as $period => $label)
      {
          $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      echo "<span id='byDate'>" . html::input('date', $date,"class='w-date date' onchange='changeDate(this.value,members)'") . '</span>';

      ?>
    </div>
    <?php if (!empty($lead)):?>
    <div class='f-right'>
      <?php 
      common::printIcon('plan', 'export', "finish=$date&from=handle");
      ?>
    </div><?php endif;?>
  </div>
  
  <table class='table-1 tablesorter fixed colored datatable newBoxs'>
    <thead>
    <tr class='colhead'>
      <th style="width: 20px;">&nbsp;</th>
      <th style="width: 60px;"><?php echo $lang->plan->type;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->sort;?></th>
      <th style="width: 200px;"><?php echo $lang->plan->matter;?></th>
      <th><?php echo $lang->plan->plan;?></th>
      <th style="width: 70px;"><?php echo $lang->plan->limit;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->appraise;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->complete;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->charge;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->status;?></th>
      <th style="width: 80px;"><?php echo $lang->plan->remark;?></th>
      <th style="width: 60px;"><?php echo $lang->actions;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($membPlan)):?>
    <tr><td colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($membPlan)):?>
    <?php foreach ((array)$membPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>'/></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $lang->plan->abcSort[$week->sort];?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo $week->status !=3 ? html::a($this->createLink('plan', 'edit', "id=$week->id", '') , $week->matter, '', "class='colorbox'") : html::a($this->createLink('plan', 'copy', "id=$week->id", '') , $week->matter, '', "class='colorbox'");?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->chargeName;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
        <td>
          <?php
          	if ($week->status != 3) 
	          common::printIcon('plan', 'edit',   "id=$week->id&from=members", '', 'list');
          	  common::printIcon('plan', 'copy',   "id=$week->id", '', 'list');
          ?>
        </td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>