<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<form method='post' id='planform'>
  
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
    </tr>
    </thead>
        <tbody>
    <?php if (empty($weekPlan)):?>
    <tr><td colspan="11" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($weekPlan)):?>
    <?php foreach ((array)$weekPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>' /></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $lang->plan->abcSort[$week->sort];?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo html::a($this->createLink('plan', 'copy', "id=$week->id", '') , $week->matter, '', "class='colorbox'");?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->realname;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
    <?php if (!empty($weekPlan)):?>
    <tfoot>
       <tr>
          <td colspan='11' class='a-right'>
              <div class='f-left'>
              <?php
              	echo html::backButton();
              ?>
              </div>
            </td>
        </tr>
    </tfoot>
    <?php endif;?>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>