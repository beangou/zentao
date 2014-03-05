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
  </div>
  
  <table class='table-1' id="addPlan"> 
    <caption><div align="center">自评本周计划完成情况(3月第1周        &nbsp;2014/03/01~2014/03/07)</div></caption>
    <thead>
      <th>编号</th>
      <th><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th><?php echo $lang->plan->plan;?></th>
      <th>完成时限</th>
      <th>完成情况</th>
      <th>见证性材料</th>
      <th>未完成原因说明及如何补救</th>
      <th>审核人</th>
    </thead>
    <?php 
    $stepID = 0;
    if (!empty($thisWeekPlan)):
    foreach ($thisWeekPlan as $plan):
    $stepID += 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepID?>">
      <td class='stepID'><?php echo $stepID ;?><?php echo html::hidden("ids[]", $plan->id, "class=text-1");?></td>
      <td><?php echo $plan->type;?></td>
      <td><?php echo $plan->matter;?></td>
      <td><?php echo $plan->plan;?></td>
      <td><?php echo $plan->deadtime;?>
      <td><select name='status[]'>
      		<option value='完成' <?php if('完成'==$plan->status){echo 'selected="selected"';}?>>完成</option>
      		<option value='延期完成' <?php if('延期完成'==$plan->status){echo 'selected="selected"';}?>>延期完成</option>
      		<option value='经领导允许延期完成' <?php if('经领导允许延期完成'==$plan->status){echo 'selected="selected"';}?>>经领导允许延期完成</option>
      		<option value='未完成' <?php if('未完成'==$plan->status){echo 'selected="selected"';}?>>未完成</option>	
          </select>
      </td>
      <td><?php echo html::input("evidence[]", $plan->evidence, "class=text-1");?></td>
      <td><?php echo html::input("courseAndSolution[]", $plan->courseAndSolution, "class=text-1");?></td>
      <td><?php echo $plan->submitTo;?></td>
    </tr>
    <?php endforeach;?>
    <?php else :
    $stepID = 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepID?>">
      <td class='stepID' colspan="9">无数据</td>
    </tr>
    <?php endif;?><?php $link = $this->createLink('plan', 'myplan', "isSubmit=1")?>
    <tr><td colspan='7' class='a-center'><?php echo  
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'") ;?>
    </td></tr>
  </table>
  
  
  <table class='table-1' id="addPlan" style="margin-top: 5%"> 
    <caption><div align="center">填写下周计划<?php echo '(3月第2周        &nbsp;2014/03/8~2014/03/14)'?></div></caption>
    <thead>
      <th>编号</th>
      <th><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th><?php echo $lang->plan->plan;?></th>
      <th>完成时限</th>
      <th>审核人</th>
    </thead>
    <?php 
    $stepID = 1;
    ?>
    <tr class='a-center' id="row<?php echo $stepID?>">
      <td class='stepID'><?php echo $stepID ;?></td>
      <td><?php echo html::input("type[]", '', "class='select-1' onkeyup='this.value=this.value.toUpperCase()'");?></td>
      <td><?php echo html::input("matter[]", '', 'class="f-left text-1"');?></td>
      <td><?php echo html::input("plan[]", '', "class=text-1");?></td>
      <td><?php echo  html::input('deadtime[]', date('Y-m-d',strtotime($date)+7*24*3600), "class='select-2 date'");?>
      <td><?php echo html::input("submitTo[]", '', "class=text-1");?></td>
      <td><?php echo html::commonButton($lang->plan->delete, "onclick='deleteRow($stepID)'").html::commonButton($lang->plan->add, "onclick='postInsert($stepID)'")?></td>
    </tr>
    <?php $link = $this->createLink('plan', 'myplan', "isSubmit=1")?>
    <tr><td colspan='7' class='a-center'>
    <?php echo  
    		html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'");?>
    </td></tr>
  </table>
  
  
  
  
  
  
  
  
</form>
<?php include '../../common/view/footer.html.php';?>