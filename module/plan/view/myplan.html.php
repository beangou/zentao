<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>
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
    <div class='f-right'>
      <?php $finish = str_replace('-', '', date('Y-m-d', time()));
      common::printIcon('plan', 'export', "finish=$finish&from=myplan");
//       common::printIcon('plan', 'batchCreate', "date=" . str_replace('-', '', $date));
//       common::printIcon('plan', 'create', "date=" . str_replace('-', '', $date));
      ?>
    </div>
  </div>
 <table class='table-1' id="addPlan"> 
    <caption> 
	    <div class='f-left'><?php echo $lang->plan->limit. $lang->colon . html::input('limit', date('Y-m-d',strtotime($date)+7*24*3600), "class='select-2 date'");?></div>
    </caption>
    <tr>
      <th class='w-30px'>编号</th>
      <th class='w-80px'><?php echo $lang->plan->type;?></th>
      <th class='w-50px'><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th class='w-p50 red'><?php echo $lang->plan->plan;?></th>
      <th class='w-80px'><?php echo $lang->plan->auditor;?></th>
      <!-- <th class='w-82px'><?php echo $lang->plan->limit;?></th> -->
      <th class='w-130px'><?php echo $lang->actions;?></th>
    </tr>
    <?php 
    $stepID = 0;
    if (!empty($lastPlan)):
    foreach ($lastPlan as $plans):
    $stepID += 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepID?>">
      <td class='stepID'><?php echo $stepID ;?></td>
      <td><?php echo html::hidden("taskID[]", $plans->id, "class=text-1");?>
      <?php echo html::select("types[]", $lang->plan->types, isset($plans->type)?$plans->type:'', "class='select-1'");?></td>
      <td><?php echo html::input("sorts[]", isset($plans->sort)?$plans->sort:'', "class='select-1'onkeyup='this.value=this.value.toUpperCase()'");?></td>
      <td><?php echo html::input("matters[]", isset($plans->matter)?$plans->matter:$plans->name, 'class="f-left text-1"');?></td>
      <td><?php echo html::input("plans[]", isset($plans->plan)?$plans->plan:'', "class=text-1");?></td>
       <td><?php echo html::select("auditors[]", $users, isset($plans->auditor)?$plans->auditor:'', "class='select-1'");?></td>
     <!--  <td><input name="limits[]" class="text-1 date" value="<?php echo $plans->limit?>"/></td> -->
      <td><?php echo html::commonButton($lang->plan->delete, "onclick='deleteRow($stepID)'").html::commonButton($lang->plan->add, "onclick='postInsert($stepID)'")?></td>
    </tr>
    <?php endforeach;?>
    <?php else :
    $stepID = 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepID?>">
      <td class='stepID'><?php echo $stepID ;?></td>
      <td><?php echo html::hidden("taskID[]", '', "class=text-1");?><?php echo html::select("types[]", $lang->plan->types, '', "class='select-1'");?></td>
      <td><?php echo html::input("sorts[]", '', "class='select-1' onkeyup='this.value=this.value.toUpperCase()'");?></td>
      <td><?php echo html::input("matters[]", '', 'class="f-left text-1"');?></td>
      <td><?php echo html::input("plans[]", '', "class=text-1");?></td>
       <td><?php echo html::select("auditors[]", $users, '', "class='select-1'");?></td>
      <td><?php echo html::commonButton($lang->plan->delete, "onclick='deleteRow($stepID)'").html::commonButton($lang->plan->add, "onclick='postInsert($stepID)'")?></td>
    </tr>
    <?php endif;?><?php $link = $this->createLink('plan', 'myplan', "isSubmit=1")?>
    <tr><td colspan='7' class='a-center'><?php echo html::submitButton($lang->save, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=0") . "\")'") . 
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'").
    		html::submitButton($lang->plan->lastPlan, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "plan=last") . "\")'") ;?>
    </td></tr>
  </table>
 
    <!-- 
    <div align="center" style="font-size: 15px;margin: 18px 0px 6px 0px;">
	    <?php echo date('Y年m月d日', strtotime($date)).' 第'.date('W', strtotime($date)).''.$lang->plan->planTitle;
	    if (!empty($team)):
	    echo ''.$team->team.'';
	    endif;
	    ?>
    
    </div>
  <table class='table-1 tablesorter fixed colored datatable newBoxs'>
    <thead>
    <tr class='colhead'>
      <th style="width: 20px;"></th>
      <th style="width: 60px;"><?php echo $lang->plan->type;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->sort;?></th>
      <th style="width: 200px;"><?php echo $lang->plan->matter;?></th>
      <th ><?php echo $lang->plan->plan;?></th>
      <th style="width: 70px;"><?php echo $lang->plan->limit;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->appraise;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->complete;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->auditor;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->status;?></th>
      <th style="width: 80px;"><?php echo $lang->plan->remark;?></th>
      <th style="width: 60px;"><?php echo $lang->actions;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($weekPlan)):?>
    <tr><td colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($weekPlan)):?>
    <?php foreach ((array)$weekPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>' /></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $lang->plan->abcSort[$week->sort];?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo $week->status !=3 ? html::a($this->createLink('plan', 'edit', "id=$week->id", '') , $week->matter, '', "") : $week->matter;?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td class='<?php if ($week->complete==1)echo 'delayed'?>'><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->auditorName;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
        <td>
          <?php 
          	if ($week->complete == 0 || $week->complete == 1){
	          common::printIcon('plan', 'edit',   "id=$week->id&from=myplan", '', 'list');
	          common::printIcon('plan', 'delete', "id=$week->id&module=myplan&date=" . str_replace('-', '', $date), '', 'list', '', 'hiddenwin');
          	}
          ?>
        </td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
    <?php if (!empty($weekPlan)):?>
    <tfoot>
       <tr>
          <td colspan='12' class='a-right'>
              <div class='f-left'>
              <?php
                 echo html::selectAll() . html::selectReverse();
                 
                      $actionLink = $this->createLink('plan', 'batchEdit', "from=planBrowse&date=". str_replace('-', '', $date));
                      echo html::commonButton($lang->edit, "onclick=\"changeAction('planform', 'batchEdit', '$actionLink')\"");
              ?>
              </div>
            </td>
        </tr>
    </tfoot>
    <?php endif;?>
  </table>
  -->
  
     <div align="center" style="font-size: 15px;margin-bottom: 6px;margin-top: 20px;">
    <?php echo date('Y年m月d日', time()).' 第'.date('W', time()).''.$lang->plan->planTitle.$lang->plan->currentPlan;
    if (!empty($team)):
    echo ''.$team->team.'';
    endif;
    ?>
    
    </div>

    <table class='table-1 tablesorter fixed colored datatable newBoxs'>
    <thead>
    <tr class='colhead'>
      <th style="width: 20px;"></th>
      <th style="width: 60px;"><?php echo $lang->plan->type;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->sort;?></th>
      <th style="width: 200px;"><?php echo $lang->plan->matter;?></th>
      <th ><?php echo $lang->plan->plan;?></th>
      <th style="width: 70px;"><?php echo $lang->plan->limit;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->appraise;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->complete;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->auditor;?></th>
      <th style="width: 60px;"><?php echo $lang->plan->status;?></th>
      <th style="width: 80px;"><?php echo $lang->plan->remark;?></th>
      <th style="width: 60px;"><?php echo $lang->actions;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($weekPlan)):?>
    <tr><td colspan="12" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($weekPlan)):?>
    <?php foreach ((array)$weekPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>' /></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $week->sort;?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo html::a($this->createLink('plan', $week->status !=3 ?'edit':'copy', "id=$week->id", '', true) , $week->matter, '', "class='colorbox'");?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td class='<?php if ($week->complete==1)echo 'delayed'?>'><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->auditorName;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
        <td>
          <?php 
          	if ($week->complete == 0 || $week->complete == 1){
	          common::printIcon('plan', 'edit',   "id=$week->id&from=myplan", '', 'list');
// 	          common::printIcon('plan', 'delete', "id=$week->id&module=myplan", '', 'list', '', 'hiddenwin');
          	}
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