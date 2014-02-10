<!-- <form method="post"> -->
<table class='cont-lt1'>
  <tr valign='top'>
    <td class='side'>
      <div class='box-title'><?php echo $lang->salary->reportManger;?></div>
      <div class='box-content'>
      	<?php common::printLink('salary', 'monthly', 'typeID=1', $lang->salary->monthlySalary); ?>
      </div>
      <div class='box-content'>
        <?php common::printLink('salary', 'monthly', 'typeID=2', $lang->salary->monthTrend); ?>
      </div>
      <div class='box-content' style="height: 350px;"><?php echo ''?></div>
    </td>
    <td class='divider'></td>
      <?php
      	if($type==1 || $type==0)
      	   include 'leadermonthly.html.php';
      	else if($type==3)include 'leaderDetails.html.php';
      	else if($type==2)include 'leadertrend.html.php';
      ?>
  </tr>
 
</table>
