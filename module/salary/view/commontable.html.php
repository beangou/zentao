<form method="post">
<table class='cont-lt1'>
  <tr valign='top'>
    <td class='side'>
      <div class='box-title'><?php echo $lang->salary->reportManger;?></div>
      <div class='box-content'>
      	<?php common::printLink('salary', 'monthly', 'typeID=1', $lang->salary->personalSalary); ?>
      </div>
      <div class='box-content'>
        <?php common::printLink('salary', 'monthly', 'typeID=2', $lang->salary->monthCount); ?>
      	<!--<?php common::printOrderLink('', $orderBy, $vars, $lang->salary->monthCount,'salary','accumulative');?> -->
      </div>
      <div class='box-content' style="height: 350px;"><?php echo ''?></div>
    </td>
      <?php
      if($standSalary->role==2){
      	if($type==1 || $type==3)
      	   include 'managerSalary.html.php';
      	else if ($type==2) include 'managerAccumulative.html.php';
      }
      else if ($standSalary->role==3){
        if($type==1 || $type==3) 
      	    include 'personelSalary.html.php';
        else if ($type==2)
      	    include 'personelAccumulative.html.php';
      } 
      if($type==5)
      	include 'personnelHelp.html.php';
      ?>
  </tr>
 
</table>
</form>