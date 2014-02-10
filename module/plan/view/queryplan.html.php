<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
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
      echo "<span id='byDate'>" . html::input('date', $date,"class='w-date date' onchange='changeDate(this.value)'") . '</span>';

      ?>
    </div>
  </div>
 
    <div align="center" style="font-size: 15px;margin: 18px 0px 6px 0px;">
	    <?php echo date('Y年', strtotime($date)).' 第'.date('W', strtotime($date)).''.$lang->plan->planTitle;
	    if (!empty($team)):
	    echo ''.$team->team.'';
	    endif;
	    $weekRange = getWeekDate(strtotime($date), date('W', strtotime($date)));
	    echo '('.$weekRange[0].'--'.$weekRange[1].')';
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
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
  </table>
  
     <div align="center" style="font-size: 15px;margin-bottom: 6px;margin-top: 20px;">
    <?php echo date('Y年', strtotime($date)-7*24*3600).' 第'.(date('W', strtotime($date)-7*24*3600)).''.$lang->plan->planTitle;
    if (!empty($team)):
    echo ''.$team->team.'';
    endif;
    $weekDate = getWeekDate(strtotime($date), date('W', strtotime($date))-1);
    echo '('.$weekDate[0].'--'.$weekDate[1].')';
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
    </tr>
    </thead>
    <tbody>
    <?php if (empty($lastPlan)):?>
    <tr><td colspan="11" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php elseif (!empty($lastPlan)):?>
    <?php foreach ((array)$lastPlan as $week):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='planIDList[<?php echo $week->id;?>]' value='<?php echo $week->id;?>' /></td>
		<td><?php echo $lang->plan->types[$week->type];?></td>
	    <td><?php echo $lang->plan->abcSort[$week->sort];?></td>
        <td class='a-left' title="<?php echo $week->matter?>">
        <?php echo html::a($this->createLink('plan', $week->status !=3 ?'edit':'copy', "id=$week->id", '', true) , $week->matter, '', "class='colorbox'");?></td>
        <td class='a-left' title="<?php echo $week->plan?>"><?php echo $week->plan;?></td>
        <td><?php echo $week->limit;?></td>
        <td><?php echo $lang->plan->completed[$week->appraise];?></td>
        <td class='<?php if ($week->complete==1)echo 'delayed'?>'><?php echo $lang->plan->completed[$week->complete];?></td>
        <td><?php echo $week->auditorName;?></td>
        <td><?php echo $lang->plan->handleStatus[$week->status];?></td>
        <td title="<?php echo $week->remark?>"><?php echo $week->remark;?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
    <tr class='a-center'></tr>
    </tbody>
  </table>
</form>
<?php 
function getWeekDate($year, $weeknum){
	$year = date('Y', $year);
	$firstdayofyear=mktime(0,0,0,1,1,$year);
	$firstweekday=date('N',$firstdayofyear);
	$firstweenum=date('W',$firstdayofyear);
	if($firstweenum==1){
		$day=(1-($firstweekday-1))+7*($weeknum-1);
		$startdate=date('Y/m/d',mktime(0,0,0,1,$day,$year));
		$enddate=date('Y/m/d',mktime(0,0,0,1,$day+6,$year));
	}else{
		$day=(9-$firstweekday)+7*($weeknum-1);
		$startdate=date('Y/m/d',mktime(0,0,0,1,$day,$year));
		$enddate=date('Y/m/d',mktime(0,0,0,1,$day+6,$year));
	}
	return array($startdate,$enddate);
}
?>
<?php include '../../common/view/footer.html.php';?>