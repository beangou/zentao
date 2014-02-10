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
      ?>
    </div>
  </div>
  
  <table class='table-1 tablesorter fixed colored datatable newBoxs'>
    <thead>
    <tr class='colhead'>
      <th style="width: 20px;"></th>
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
    <?php if (empty($checkPlan)):?>
    <tr><td colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($checkPlan)):?>
    <?php foreach ((array)$checkPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>' /></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $week->sort;?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo html::a($this->createLink('plan', 'copy', "id=$week->id", '') , $week->matter, '', "class='colorbox'");?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td class='<?php if ($week->complete==1)echo 'delayed'?>'><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->chargeName;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
        <td>
          <?php
          		common::printIcon('plan', 'finish', "planID=$week->id", $week, 'list', '', '', 'iframe', true);
          	 if ($week->complete == 0 || $week->complete == 1){
          		common::printIcon('plan', 'edit', "planID=$week->id&from=handle", $week, 'list', '', '', '', '');
          	}
          ?>
        </td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
    <?php if (!empty($checkPlan)):?>
    <tfoot>
       <tr>
          <td colspan='12' class='a-right'>
              <div class='f-left'>
              <?php
                 echo html::selectAll() . html::selectReverse();
                 
//                   if(common::hasPriv('story', 'batchEdit'))
//                   {
                      $actionLink = $this->createLink('plan', 'batchAction', "from=handleBrowse");
                      echo html::commonButton($lang->actions, "onclick=\"changeAction('planform', 'batchAction', '$actionLink')\"");
//                   }
              ?>
              </div>
            </td>
        </tr>
    </tfoot>
    <?php endif;?>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>