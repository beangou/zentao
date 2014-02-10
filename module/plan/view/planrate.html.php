<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<form method='post' id='planform'>
  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
      foreach($lang->plan->periods as $period => $label)
      {
          $vars = $period;
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
    <!--<div class='f-right'>
      <?php 
      common::printIcon('plan', 'export', "start=$start&finish=$finish&from=planrate");
      ?>
    </div> -->
  </div>
  <div style="margin-bottom: 5px;">
  	<?php echo "<span id='byDate'>".
  	$lang->plan->startDate.'：'.html::input('start', $start,"class='w-date date' ")."&nbsp;&nbsp;".
  	$lang->plan->finishedDate.'：'.html::input('finish', $finish,"class='w-date date'");
  	$actionLink = $this->createLink('plan', 'planRate', "");
  	echo html::commonButton($lang->plan->query, "onclick=\"changeAction('planform', 'planRate', '$actionLink')\"");
  	?>
  </div>
  <table class='table-1 tablesorter fixed colored datatable newBoxs'>
    <thead>
    <tr class='colhead'>
      <th style="width: 20px;"></th>
      <th><?php echo $lang->plan->realname;?></th>
      <th><?php echo $lang->plan->dept;?></th>
      <th><?php echo $lang->plan->team;?></th>
      <th><?php echo $lang->plan->score;?></th>
      <th><?php echo $lang->plan->month;?></th>
      <th><?php echo $lang->plan->rank;?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($planRate)):?>
    <tr><td colspan="7" style="text-align: right;"><?php echo $lang->pager->noRecord ;?></td></tr>
    <?php else :?>
    <?php foreach ($planRate as $plan) :?>
    <?php foreach ($plan as $rate):?>
    <tr class='a-center'>
    	<td><input type='checkbox' name='' value='' /></td>
    	<td><?php $start = str_replace('-', '', $rate->start);$finish = str_replace('-', '', $rate->finishedDate);?>
    	<?php echo html::a($this->createLink('plan', 'planlist', "account=$rate->charge&start=".$start."&finish=".$finish, ''), $rate->realname) ;?></td>
    	<td><?php echo $rate->name ;?></td>
    	<td><?php echo $rate->team ;?></td>
    	<td><?php echo html::a($this->createLink('plan', 'planlist', "account=$rate->charge&start=".$start."&finish=".$finish, ''), $rate->score) ;?></td>
    	<td><?php echo date('m', strtotime($rate->finishedDate)) ;?></td>
    	<td><?php echo $rate->rank ;?></td>
    </tr>
    <?php endforeach;?>
    <?php endforeach;?>
    <?php endif;?>
    </tbody>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>